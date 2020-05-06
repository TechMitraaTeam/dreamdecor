<?php


namespace SME\PdfOffer\Block\Index;

use Magento\Store\Model\ScopeInterface;
use SME\ProductConfigurator\Api\Connector;

/**
 * Class Generate
 *
 * @package SME\PdfOffer\Block\Index
 */
class Generatepdf extends \Staempfli\Pdf\Block\PdfTemplate
{
    protected $_request;
    protected $_customer = null;
    protected $_scopeConfig;
    protected $_storeManager;
    protected $_currency;
    protected $_taxCalculation;
    protected $_optionsBlock;
    protected $productRepository;
    protected $_addressRepository;
    protected $_customerSession;
    protected $_countryFactory;
    protected $_connector;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Staempfli\Pdf\Api\OptionsFactory $optionsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currency,
        \Magento\Customer\Model\Session $customerSession,
        \SME\ProductConfigurator\Block\Product\View\Options $optionsBlock,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Tax\Model\ResourceModel\Calculation $taxCalculation,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        array $data = []
    ) {
        $this->_request                 = $request;
        $this->_scopeConfig             = $scopeConfig;
        $this->_storeManager            = $storeManager;
        $this->_currency                = $currency;
        $this->_taxCalculation          = $taxCalculation;
        $this->productRepository        = $productRepository;
        $this->_addressRepository       = $addressRepository;
        $this->_customerSession         = $customerSession;
        $product = $this->productRepository->getById($this->_request->getParam('product'));
        $this->_optionsBlock            = $optionsBlock;
        $this->_optionsBlock->setProduct($product);
        $this->_countryFactory          = $countryFactory;
        $this->_connector = new Connector();
        
        parent::__construct($context, $optionsFactory, $data);
    }
    
    
    public function getParams(){       
        return $this->_request->getParams();
    }
    
    public function getOptions(){
        
        $params = $this->_request->getParams();
        
        $apiConfig = $this->_connector->getConfigData();
        
        if(is_array($params))
        {
            foreach($params as $key => $value)
            {
                if($key == '' || $value == '' || $key == 'form_key' || $key == 'qty' || $key == 'customPrice' || $key == 'product' || $key == 'item' || $key == 'total_price' || $key == 'numberofcopies' || $key == 'related_product' || $key == 'selected_configurable_option')
                {
                    continue;
                }
                
                if(strpos($key, 'service_option') !== false){
                    $additionalOptions[] = [
                        'label' => $apiConfig['result']['service_options'][$key],
                        'value' => $apiConfig['result'][$key][$value]
                    ];
                } elseif($key == 'delivery'){
                    $additionalOptions[] = [
                        'label' => __('Delivery'),
                        'value' => $value
                    ];
                } elseif(array_key_exists($value, $apiConfig['result'][$key]) == false){
                    $additionalOptions[] = [
                        'label' => $apiConfig['result']['attribute'][$key],
                        'value' => $value
                    ];
                } else {
                    $additionalOptions[] = [
                        'label' => $apiConfig['result']['attribute'][$key],
                        'value' => $apiConfig['result'][$key][$value]
                    ];
                }
                
            }
        }
        
        return $additionalOptions;
    }
    
    public function getCustomerData() {
        if($this->_customer == null) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_customer = $objectManager->create('Magento\Customer\Model\Session');
        }
        
        if($this->_customer->isLoggedIn()) {
            $defaultBillingAddress = $this->_addressRepository->getById($this->_customer->getCustomer()->getDefaultBilling());
            return $defaultBillingAddress;
        }
        return '';
    }
    
    public function getStoreInformation(){
        $storeInfo = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE).' | '
                    .$this->_scopeConfig->getValue('general/store_information/street_line1', ScopeInterface::SCOPE_STORE).' | '
                    .$this->_scopeConfig->getValue('general/store_information/postcode', ScopeInterface::SCOPE_STORE).' '
                    .$this->_scopeConfig->getValue('general/store_information/city', ScopeInterface::SCOPE_STORE);
        
        return $storeInfo;
    }
    
    public function getTaxAmountByPrice($price){
        return $this->_optionsBlock->getTaxAmountByPrice($price);
    }
    
    public function getPriceInclTax($price){
        return $this->_optionsBlock->getPriceInclTax($price);
    }
    
    public function getCountryname($countryCode){
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
    
    public function getCurrencySymbol(){
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode(); 
        $currency = $this->_currency->create()->load($currencyCode); 
        return $currency->getCurrencySymbol();
    }
    
    public function getTaxRate(){
        // Tax Calculation
        $productTaxClassId = $this->_optionsBlock->getProduct()->getTaxClassId();
        $defaultCustomerTaxClassId = $this->_scopeConfig->getValue('tax/classes/default_customer_tax_class');

        if($this->getCustomerData() != ''){
            $country_id = $this->getCustomerData()->getCountryId();
        } else {
            $country_id = $this->_taxCalculation::USA_COUNTRY_CODE;
        }
        
        
        $request = new \Magento\Framework\DataObject(
            [
                'country_id' => '',
                'region_id' => null,
                'postcode' => null,
                'customer_class_id' => $defaultCustomerTaxClassId,
                'product_class_id' => $productTaxClassId
            ]
        );
        
        // Calculate tax
        $taxInfo = $this->_taxCalculation->getRateInfo($request);
        
        // Classify different taxes
        if (count($taxInfo['process']) > 0) {
            $taxDetails = []; $i = 0;
            
            foreach ($taxInfo['process'][0]['rates'] as $key => $rate) {
                return $rate['title'];
            }
        }
        
        return __('Tax');
    }
}

