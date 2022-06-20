<?php

namespace Vnecoms\PdfPro\Observer;

use Vnecoms\PdfPro\Model\Api\AttachmentContainerInterface as ContainerInterface;

/**
 * Class AbstractObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
abstract class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Vnecoms\PdfPro\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Vnecoms\PdfPro\Model\Api\PdfRendererInterface
     */
    protected $pdfRenderer;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * AbstractObserver constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data                        $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface     $pdfRenderer
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer

    ) {
        $this->scopeConfig = $scopeConfig;
        $this->pdfRenderer = $pdfRenderer;
        $this->helper = $helper;
    }

    /**
     * @param $config
     * @param $content
     * @param $pdfFilename
     * @param $mimeType
     * @param ContainerInterface $attachmentContainer
     */
    public function attachContent($config, $content, $pdfFilename, $mimeType, ContainerInterface $attachmentContainer)
    {
        $attachment = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Framework\DataObject')
            ->setData(
            [
                'config' => $config,
                'content' => $content,
                'mimeType' => $mimeType,
                'fileName' => $pdfFilename,
            ]
        );
        $attachmentContainer->addAttachment($attachment);
    }

    /**
     * @param $config
     * @param $pdfString
     * @param $pdfFilename
     * @param ContainerInterface $attachmentContainer
     */
    public function attachPdf($config, $pdfString, $pdfFilename, ContainerInterface $attachmentContainer)
    {
        $type = $config;
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Vnecoms\PdfPro\Helper\Data');

        $attached = $helper->getConfig('pdfpro/general/'.$type.'_email_attach');
        //if $config == 0
        if ($attached != \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            $this->attachContent($config, $pdfString, $pdfFilename, 'application/pdf', $attachmentContainer);
        }
    }

    public function isLoadedLib()
    {
        return true;
        $helper = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Vnecoms\PdfPro\Helper\Data');
        if (is_dir($helper->getPdfLibDir()) and file_exists($helper->getPdfLibDir().'/vendor/autoload.php')) {
            return true;
        }

        return false;
    }
}
