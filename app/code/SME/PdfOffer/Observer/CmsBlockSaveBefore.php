<?php
namespace SME\PdfOffer\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CmsBlockSaveBefore implements ObserverInterface
{
    protected $_path = '/code/SME/PdfOffer/view/frontend/templates/pdf/footer.html';
    protected $_dir;
    
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $dir
        )
    {
        $this->_dir = $dir;
    }
    
    /**
     * Save block content into a static html subpart
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $modelObject = $observer->getEvent()->getObject();
        
        if($modelObject->getIdentifier() == 'pdf-text-footer') {
            
            $content = $modelObject->getContent();
            
            file_put_contents($this->_dir->getPath('app').$this->_path, $content);
        }
    }
}