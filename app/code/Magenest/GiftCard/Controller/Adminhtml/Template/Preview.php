<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 03/02/2016
 * Time: 09:13
 */
namespace Magenest\GiftCard\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magenest\GiftCard\Model\TemplateFactory;
use Magento\Framework\Registry;

class Preview extends Action
{
    protected $_logger;

    protected $_directory;

    protected $_templateFactory;

    protected $_coreRegistry;
    protected $_filesystem;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        \Magenest\GiftCard\Logger\Logger $logger,
        TemplateFactory $templateFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Registry $registry
    ) {
        $this->_logger = $logger;
        $this->_templateFactory = $templateFactory;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_coreRegistry = $registry;
        $this->_filesystem = $filesystem;
        $this->jsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = [
            'recipient_name' => 'Hau dz',
            'sender_name' => 'Magenest',
            'message' => 'Merry Christmas To You',
            'code' => 'GIFT - ABCD -XYZT',
            'balance' => '$6969102',
            'expiry_date' => '01/01/2017'
        ];

        /** @var \Magenest\GiftCard\Model\Template $template */
        $template = $this->saveTemplateDraftMode();

        $pdf = $template->generatePdf($params);

        $pdf->render();

        $path = $this->_directory->getAbsolutePath().'app/code/Magenest/GiftCard/template.pdf';
        @chmod($path, 0775);

        $pdf->save( $this->_directory->getAbsolutePath().'app/code/Magenest/GiftCard/template.pdf');

        $pdfFile = $this->_directory->getAbsolutePath() . 'app/code/Magenest/GiftCard/template.pdf';
        $quality = 100;
        $res = '176x176';

        $currentTime = time();
        $exportPath = $this->_directory->getAbsolutePath() . 'pub/media/giftcard/template'.$currentTime.'.jpg';
        $path = 'giftcard/template'.$currentTime.'.jpg';

        set_time_limit(100);

        exec("'gs' '-dNOPAUSE' '-sDEVICE=jpeg' '-dUseCIEColor' '-dTextAlphaBits=4' '-dGraphicsAlphaBits=4' '-o$exportPath' '-r$res' '-dJPEGQ=$quality' '$pdfFile'", $output);
        $template->delete();
        $result = $this->jsonFactory->create();
        return $result->setData([
            'code' => 'success',
            'path' => $path
        ]);
    }

    /**
     * save the pdf template in draft mode
     */
    public function saveTemplateDraftMode()
    {

        $requestData = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($requestData) {
            /** @var \Magenest\GiftCard\Model\Template $model */
            $model = $this->_templateFactory->create();
            $data = $requestData['template'];
            if (isset($data['id'])) {
                $model->load($data['id']);
                if ($data['id'] != $model->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to save template.'));
                }
            }

            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader*/
            $uploaded_images = [];
            $uploaded_background = [];
            $current_template = $model->getData();

            $i = 0;
            $j = 0;

            //save the uploaded images
            $main_image_params = $this->getRequest()->getParam('main_img');
            $uploaded_images =[];

            if ($main_image_params) {
                foreach ($main_image_params as $key=> $value) {
                    array_push($uploaded_images,$value);
                }
            }

            if ($data['delete_images_count'] != "") {
                $delete_image_count = explode(';', $data['delete_images_count']);
                foreach ($delete_image_count as $item) {
                    $explode = explode('main', $item);

                    foreach ($uploaded_images as $key => $uploaded_image) {
                        if ($explode[1] == $uploaded_image['file']) {
                            $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                ->delete('giftcard/template/main' . $uploaded_image['file']);
                            unset($uploaded_images[$key]);
                        }
                    }
                }
            }

            $data['main_image'] = serialize($uploaded_images);

            //save the background images
            $uploaded_background_params = $this->getRequest()->getParam('background_img');
            $uploaded_background =[];

            if ($uploaded_background_params) {
                foreach ($uploaded_background_params as $key=> $value) {
                    array_push($uploaded_background,$value);
                }
            }

            $data['background_image'] = serialize($uploaded_background);

            if ($data['delete_background_count'] != "") {
                $delete_background_count = explode(';', $data['delete_background_count']);
                foreach ($delete_background_count as $item) {

                    $explode = explode('background', $item);
                    foreach ($uploaded_background as $key => $background) {
                        if ($explode[1] == $background['file']) {
                            $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA)
                                ->delete('giftcard/template/background' . $background['file']);
                            unset($uploaded_background[$key]);
                        }
                    }
                }
            }

            $data['background_image'] = serialize($uploaded_background);


            // save coordinate information
            $data['extra_field'] = serialize(
                [
                    'main' => $data['main_image_0'].';'.$data['main_image_1'].';'.$data['main_image_2'].';'.$data['main_image_3'],
                    'box' => $data['box_image_0'].';'.$data['box_image_1'].';'.$data['box_image_2'].';'.$data['box_image_3'],
                    'barcode' => $data['barcode_image_0'].';'.$data['barcode_image_1'].';'.$data['barcode_image_2'].';'.$data['barcode_image_3'],
                    'title' => $data['custom_title_0'].';'.$data['custom_title_1'].';'.$data['custom_title_2'],
                    'from' => $data['custom_from_0'].';'.$data['custom_from_1'].';'.$data['custom_from_2'],
                    'to' => $data['custom_to_0'].';'.$data['custom_to_1'].';'.$data['custom_to_2'],
                    'giftkey' => $data['custom_giftkey_0'].';'.$data['custom_giftkey_1'].';'.$data['custom_giftkey_2'],
                    'value' => $data['custom_value_0'].';'.$data['custom_value_1'].';'.$data['custom_value_2'],
                    'display' => $data['custom_display_0'].';'.$data['custom_display_1'].';'.$data['custom_display_2'],
                    'note' => $data['custom_note_0'].';'.$data['custom_note_1'].';'.$data['custom_note_2'].';'.$data['custom_note_align']
                ]
            );

            $data['additional_data'] = serialize($requestData);
            $data['saved_image_selected'] = $data['saved_image_selected'];
            $data['saved_background_selected'] =  $data['saved_background_selected'];

            try {
                //save the draft template
                unset($data['id']);
                $data['mode'] = 'draft';
                $model->setData($data);

                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
               return   $model;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_logger ->addError($e->getMessage());
                $this->messageManager->addError($e, __('Something went wrong while previewing template.'));
            } catch (\Exception $e) {
                $this->_logger ->addError($e->getMessage());
                $this->messageManager->addError($e, __('Something went wrong while previewing template.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
            }
        }
    }
}
