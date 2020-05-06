<?php

namespace SME\PdfOffer\Controller\Index;

use Magento\Framework\View\Element\Template\File\Resolver as TemplateResolver;
use Staempfli\Pdf\Model\View\PdfResult;
use Staempfli\Pdf\Service\PdfOptions;

/**
 * Class Generate
 *
 * @package SME\PdfOffer\Controller\Index
 */
class Generatepdf extends \Magento\Framework\App\Action\Action
{
    private $templateResolver;
    private $pdf;
    
    public function __construct(
        TemplateResolver $templateResolver,
        \Staempfli\Pdf\Model\PdfFactory $pdfFactory,
        \Magento\Framework\App\Action\Context $context
        ) {
            parent::__construct($context);
            $this->templateResolver = $templateResolver;
            $this->pdf = $pdfFactory->create();
    }
    
    public function execute()
    {
        return $this->renderPdfResult();
    }
    
    protected function renderPdfResult()
    {
        /** @var PdfResult $result */
        $result = $this->resultFactory->create(PdfResult::TYPE);
        $result->addGlobalOptions(
            new PdfOptions(
                [
                    PdfOptions::KEY_GLOBAL_TITLE => __('Ihr Angebot'),
                    PdfOptions::KEY_PAGE_ENCODING => PdfOptions::ENCODING_UTF_8,
                    PdfOptions::KEY_GLOBAL_ORIENTATION => PdfOptions::ORIENTATION_PORTRAIT,
                    PdfOptions::FLAG_PAGE_PRINT_MEDIA_TYPE,
                    PdfOptions::KEY_PAGE_HEADER_SPACING => "10",
                    PdfOptions::KEY_GLOBAL_MARGIN_TOP => "20",
                    PdfOptions::KEY_GLOBAL_MARGIN_BOTTOM => "30",
                    PdfOptions::KEY_GLOBAL_MARGIN_LEFT => "0",
                    PdfOptions::KEY_GLOBAL_MARGIN_RIGHT => "0",
                ]
                )
            );
        $result->addPageOptions(
            new PdfOptions(
                [
                    PdfOptions::KEY_PAGE_COOKIES => ${'_COOKIE'},
                    PdfOptions::KEY_PAGE_HEADER_HTML_URL => $this->templateResolver
                        ->getTemplateFileName('SME_PdfOffer::pdf/header.html'),
                    PdfOptions::KEY_PAGE_FOOTER_HTML_URL => $this->templateResolver
                        ->getTemplateFileName('SME_PdfOffer::pdf/footer.html'),
                ]
            )
        );
        
        return $result;
    }
}

