<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 26/01/2016
 * Time: 21:06
 */
namespace Magenest\GiftCard\Model;

use Magenest\GiftCard\Model\ResourceModel\Template as ResourceTemplate;
use Magenest\GiftCard\Model\ResourceModel\Template\Collection as Collection;
use Magenest\GiftCard\Helper\Data as Helper;
use Magento\Framework\Registry;

class Template extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'template';

    protected $_helper;

    protected $_coreRegistry;

    protected $logger;

    protected $directory_list;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceTemplate $resource,
        Collection $resourceCollection,
        Helper $helperData,
        \Magenest\GiftCard\Logger\Logger $logger,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        array $data = []
    ) {
        $this->_helper = $helperData;
        $this->_coreRegistry = $registry;
        $this->logger = $logger;
        $this->directory_list = $directory_list;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\ResourceModel\Template');
    }

    /**
     * get main image path
     */
    public function getMainImagePath()
    {

      //  if (!$this->getData()) return;
        $path =  $this->getData('saved_image_selected');
        if ($path) {
            $pos = strpos($path, 'pub');
            if ($pos > 0)  {
                $filePath = substr($path, $pos -1);
                return  $this->directory_list->getRoot().$filePath;
            } else {
                return $this->directory_list->getRoot().'/pub/media/giftcard/template/main' .$path;
            }
        }
    }

    /**
     * get background image
     */
    public function getBackgroundImagePath()
    {
       // if (!$this->getData()) return;
        // $block->getMediaUrl().'giftcard/template/main'.$item['file']

        $path = $this->getData('saved_background_selected');
        if ($path) {
            $pos = strpos($path, 'pub');
            if ($pos > 0)  {
                $filePath = substr($path, $pos -1);
                return  $this->directory_list->getRoot().$filePath;
            } else {
                return $this->directory_list->getRoot().'/pub/media/giftcard/template/background' .$path;
            }
        }
    }

    public function generatePdf($params, $form_data = null)
    {
        if (isset($form_data)) {
            if (is_string($form_data)) {
                $form_data = json_decode($form_data, true);
            }
        }
        $pdf = new \Zend_Pdf();
        $page = new \Zend_Pdf_Page(660, 375);
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES_BOLD_ITALIC);
        $textFont = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);
        $textFontBold = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA_BOLD);

        $page->setFont($font, 32);
        $rootPath = $this->directory_list->getRoot().'/';


        $this->logger->addDebug($rootPath);
        $boxBlankFile = $rootPath.'app/code/Magenest/GiftCard/view/adminhtml/web/images/boxblank.png';
        $this->logger->addDebug($boxBlankFile);

        $boxImage = \Zend_Pdf_Image::imageWithPath($rootPath.'app/code/Magenest/GiftCard/view/adminhtml/web/images/boxblank.png');
        $barcode = \Zend_Pdf_Image::imageWithPath($rootPath.'app/code/Magenest/GiftCard/view/adminhtml/web/images/barcode.png');

        if ($this->getMainImagePath()) {
          $mainPath = $this->getMainImagePath();
            $leftImage = \Zend_Pdf_Image::imageWithPath($mainPath
            );

            $centerImage = \Zend_Pdf_Image::imageWithPath($mainPath
            );
        }
        elseif (!isset($form_data) || $form_data['template[saved_image_selected]'] == null) {
            $leftImage = \Zend_Pdf_Image::imageWithPath($rootPath.
                'app/code/Magenest/GiftCard/view/adminhtml/web/images/leftmain.png'
            );
            $centerImage = \Zend_Pdf_Image::imageWithPath($rootPath.
                'app/code/Magenest/GiftCard/view/adminhtml/web/images/centermain.png'
            );
        }  else {
            $explode = explode("pub", $form_data['template[saved_image_selected]']);
            $leftImage = \Zend_Pdf_Image::imageWithPath('pub' . $explode[1]);
            $centerImage = \Zend_Pdf_Image::imageWithPath('pub' . $explode[1]);
        }
        //
        if ($this->getBackgroundImagePath()) {
            $backgroundImagePath = $this->getBackgroundImagePath();
            $backgroundImage = \Zend_Pdf_Image::imageWithPath($backgroundImagePath);
        } elseif (!isset($form_data) || $form_data['template[saved_background_selected]'] == null) {
            $backgroundImage = \Zend_Pdf_Image::imageWithPath($rootPath.
                'app/code/Magenest/GiftCard/view/adminhtml/web/images/background.jpg'
            );
        }  else {
            $explodeBackground = explode("pub", $form_data['template[saved_background_selected]']);
            $backgroundImage = \Zend_Pdf_Image::imageWithPath($rootPath.'pub' . $explodeBackground[1]);
        }

        $data = [
            'title' => $this->getData('title'),
            'note' => $this->getData('note'),
            'style_color' => $this->getData('style_color'),
            'text_color' => $this->getData('text_color'),
            'additional_info' => $this->getData('additional_info')
        ];

        if ($this->getData('design_type') == 1) {
            $page = $this->_helper->drawLeftTemplate(
                $data,
                $params,
                $page,
                $textFont,
                $textFontBold,
                $boxImage,
                $barcode,
                $leftImage,
                $backgroundImage
            );
        }

        if ($this->getData('design_type') == 2) {
            $page = $this->_helper->drawRightTemplate(
                $data,
                $params,
                $page,
                $textFont,
                $textFontBold,
                $boxImage,
                $barcode,
                $leftImage,
                $backgroundImage
            );
        }

        if ($this->getData('design_type') == 3) {
            $page = $this->_helper->drawCenterTemplate(
                $data,
                $params,
                $page,
                $textFont,
                $textFontBold,
                $boxImage,
                $barcode,
                $centerImage,
                $backgroundImage
            );
        }

        if ($this->getData('design_type') == 4) {
            $page = $this->_helper->drawCustomTemplate(
                $data,
                $params,
                $page,
                $font,
                $textFont,
                $boxImage,
                $barcode,
                $leftImage,
                $backgroundImage
            );
        }

        $pdf->pages[] = $page;
        return $pdf;
    }
}
