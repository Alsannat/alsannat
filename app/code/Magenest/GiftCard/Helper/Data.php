<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/11/2015
 * Time: 16:57
 */
namespace Magenest\GiftCard\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface as Logger;
use Symfony\Component\Config\Definition\Exception\Exception;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EMAIL_TEMPLATE = "giftcard/general/email_template";
    const EMAIL_IDENTITY = "giftcard/general/email_identity";
    const ENABLE_DEBUG = 1;

    protected $_templateFactory;
    protected $_giftCardFactory;
    protected $_giftCardPurchasedFactory;
    protected $_transportBuilder;
    protected $_directory;

    protected $mediaDir;

    protected $_var;

    protected $_file;

    protected $logger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magenest\GiftCard\Model\TemplateFactory $templateFactory,
        \Magenest\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magenest\GiftCard\Model\GiftCardPurchasedFactory $giftCardPurchasedFactory,
        \Magenest\GiftCard\Model\Mail\TransportBuilder $transportBuilder,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magenest\GiftCard\Logger\Logger $logger,
        \Magento\Framework\Filesystem $filesystem
    ) {

        parent::__construct($context);
        $this->_logger = $context->getLogger();
        $this->_file = $file;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->mediaDir = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_templateFactory = $templateFactory;
        $this->_giftCardFactory = $giftCardFactory;

        $this->_giftCardPurchasedFactory = $giftCardPurchasedFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    public function getDesignMode()
    {
        return $this->scopeConfig->getValue(
            'giftcard/design/mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getVars()
    {
        return $this->_var;
    }

    public function addVar($key, $input)
    {
        $this->_var[$key] = $input;
    }
    /**
     * @param null $giftcard
     * @param null $params
     * @return \Zend_Pdf
     */
    public function generatePdf($giftcard = null, $params = null,$saveInMediaGiftCard = false)
    {
        $output = array();

        $templateName = $giftcard->getData('template');
        if (!$templateName) {
            return ;
        }

        /** @var  $template \Magenest\GiftCard\Model\Template */
        $template = $this->_templateFactory->create()->getCollection()->addFieldToFilter('name', $templateName)->getFirstItem();
        $params = [
            'recipient_name' => $giftcard->getData('recipient_name'),
            'sender_name' =>  $giftcard->getData('sender_name'),
            'message' => $giftcard->getData('message'),
            'code' => $giftcard->getData('code'),
            'balance' =>  $giftcard->getData('balance'),
            'expiry_date' => $giftcard->getData('date_expired')
        ];
        try {
            $pdf = $template->generatePdf($params,null);

            if (!$saveInMediaGiftCard) {
                $pdf->save($this->_directory->getAbsolutePath() . '/' . $giftcard->getId() . '.pdf');
                $output['file_path'] = $this->_directory->getAbsolutePath() . '/' . $giftcard->getId().'.pdf';
            } else {
                $pdf->save($this->mediaDir->getAbsolutePath('/giftcard/template/'. $giftcard->getId() . '.pdf'));
                $output['file_path'] = $this->mediaDir->getAbsolutePath('/giftcard/template/'. $giftcard->getId() . '.pdf');
            }

        } catch (\Exception  $e) {
            $this->logger->addError($e->getMessage());
            $this->logger->addError($e->getTraceAsString());
            $this->_logger->critical($e->getMessage());
            return ;
        }

        return $output;
    }

    /**
     * @param  $giftcard  \Magenest\GiftCard\Model\GiftCard
     * @param null $params
     * @return $this
     */
    public function sendEmail($giftcard = null, $params = null)
    {
        if ($giftcard) {
            $templateId = $this->scopeConfig->getValue(self::EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            if (!$templateId) {
                $templateId='giftcard_email_template';
            }
            $storeId = $giftcard->getData('store_id');
            $recipientEmail = $giftcard->getData('recipient_email');
            $recipientName = $giftcard->getData('recipient_name');

            $from =  $this->scopeConfig->getValue(
                self::EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );

            if (!$from) {
                $from = 'general' ;
            }

            $pdfFilePath = $giftcard->getPDF();
            if ($pdfFilePath) {
                $body = $this->_file->read($pdfFilePath);
                $attachments[] = ['body' =>$body , 'name'=> $giftcard->getName().'.pdf', 'cat' => 'pdf'] ;
            }

            //the personal design
            $designFile = $giftcard->getPersonalDesign();
            if ($designFile) {
                $body = $this->_file->read($designFile);
                $attachments[] = ['body' =>$body , 'name'=> $giftcard->getName() .'.png', 'cat' => 'png'] ;
            }


            $this->addVar('giftcard', $giftcard);

            //check the from condition
            if ($this->_transportBuilder->getMessage()->getFrom()) {
                $this->_transportBuilder->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(
                        ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                    )->setTemplateVars(
                        $this->getVars()
                    )
                    ->setFrom(
                        $from
                    )
                    ->addTo(
                        $recipientEmail,
                        $recipientName
                    );
            } else {
                $this->_transportBuilder->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(
                        ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                    )->setTemplateVars(
                        $this->getVars()
                    )
                    ->addTo(
                        $recipientEmail,
                        $recipientName
                    );
            }


            if ($attachments) {
                foreach ($attachments as $attachment) {
                    if ($attachment) {
                        $this->_transportBuilder->createAttachment($attachment);
                    }
                }
            }

            /** @var  $transport \Magento\Framework\Mail\Template\TransportBuilder */
            $transport = $this->_transportBuilder->getTransport();


            try {
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $e) {
                $this->_logger->critical($e->getMessage());
            };
        }
        return $this;
    }

    public function drawLeftTemplate(
        $data,
        $params,
        $page,
        $textFont,
        $textFontBold,
        $boxImage,
        $barcode,
        $leftImage,
        $backgroundImage
    ) {
        /** set some default param */
        if ($data['title'] == '') $data['title'] = "Title";
        if ($data['note'] == '') $data['note'] = "note";
        if ($data['style_color'] == '') $data['style_color'] = "#000000";
        if ($data['text_color'] == '') $data['text_color'] = "#000000";

        try {
            /** @var \Zend_Pdf_Page $page */
            $page->drawImage($backgroundImage, 0, 0, 660, 375);
            $page->drawImage($leftImage, 0, 0, 275, 375);

            $text_width = $this->getTextWidth(
                $data['title'],
                \Zend_Pdf_Font::FONT_TIMES_BOLD_ITALIC,
                $page->getFontSize()
            );
            $left_border = 276 + (385 - $text_width) / 2;
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['style_color']))
                ->drawText($data['title'], $left_border, 315, 'UTF-8');

            $page->setFont($textFont, 13);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('From:  ' . $params['sender_name'], 288, 282, 'UTF-8');
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('To:  ' . $params['recipient_name'], 288, 261, 'UTF-8');
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText($params['code'], 290, 120, 'UTF-8');
            $page->drawImage($boxImage, 288, 144, 642, 247);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('Value', 605, 120, 'UTF-8');

            $page->setFont($textFontBold, 26);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText($params['balance'], 640 - $this->getTextWidth($params['balance'], \Zend_Pdf_Font::FONT_HELVETICA_BOLD, 26), 87, 'UTF-8');

            $page->drawImage($barcode, 288, 69, 480, 110);

            $text_tokens = explode(PHP_EOL, $data['note']);
            $page->setFont($textFont, 9);
            $base_line = 60;

            for ($i = 0; $i < sizeof($text_tokens); $i++) {
                $width = $this->getTextWidth($text_tokens[$i], \Zend_Pdf_Font::FONT_HELVETICA, 9);
                $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                    ->drawText($text_tokens[$i], 640 - $width, $base_line, 'UTF-8');
                $base_line -= 10;
            }

            $page->setFont($textFont, 10);
            $message_token = explode(PHP_EOL, $params['message']);
            $top_line = 230;

            for ($i = 0; $i < sizeof($message_token); $i++) {
                $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                    ->drawText($message_token[$i], 300, $top_line, 'UTF-8');
                $base_line -= 10;
            }

            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('Expiry Date: ' . $params['expiry_date'], 290, 55, 'UTF-8');

            return $page;
        } catch (\Zend_Pdf_Exception $e) {
            throw new \Zend_Pdf_Exception(
                __('Cannot generate left style template.')
            );
        }
    }


    public function drawRightTemplate(
        $data,
        $params,
        $page,
        $textFont,
        $textFontBold,
        $boxImage,
        $barcode,
        $leftImage,
        $backgroundImage
    ) {
        try {
            /** @var \Zend_Pdf_Page $page */
            $page->drawImage($backgroundImage, 0, 0, 660, 375);
            $page->drawImage($leftImage, 385, 0, 660, 375);

            $text_width = $this->getTextWidth(
                $data['title'],
                \Zend_Pdf_Font::FONT_TIMES_BOLD_ITALIC,
                $page->getFontSize()
            );

            $left_border = (385 - $text_width) / 2;
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['style_color']))
                ->drawText($data['title'], $left_border, 315, 'UTF-8');

            $page->setFont($textFont, 13);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('From:  ' . $params['sender_name'], 15, 282, 'UTF-8');
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('To:  ' . $params['recipient_name'], 15, 261, 'UTF-8');
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText($params['code'], 17, 120, 'UTF-8');
            $page->drawImage($boxImage, 15, 144, 372, 247);

            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('Value', 335, 120, 'UTF-8');

            $page->setFont($textFontBold, 26);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['style_color']))
                ->drawText($params['balance'], 370 - $this->getTextWidth($params['balance'], \Zend_Pdf_Font::FONT_HELVETICA_BOLD, 26), 87, 'UTF-8');


            $page->drawImage($barcode, 15, 69, 210, 110);

            $text_tokens = explode(PHP_EOL, $data['note']);
            $page->setFont($textFont, 9);
            $base_line = 55;

            for ($i = 0; $i < sizeof($text_tokens); $i++) {
                $width = $this->getTextWidth($text_tokens[$i], \Zend_Pdf_Font::FONT_HELVETICA, 9);
                $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                    ->drawText($text_tokens[$i], 370 - $width, $base_line, 'UTF-8');
                $base_line -= 10;
            }

            $page->setFont($textFont, 10);
            $message_token = explode(PHP_EOL, $params['message']);
            $top_line = 228;

            for ($i = 0; $i < sizeof($message_token); $i++) {
                $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                    ->drawText($message_token[$i], 30, $top_line, 'UTF-8');
                $base_line -= 10;
            }

            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('Expiry Date: ' . $params['expiry_date'], 17, 57, 'UTF-8');

            return $page;
        } catch (\Zend_Pdf_Exception $e) {
            throw new \Zend_Pdf_Exception(
                __('Cannot generate right style template.')
            );
        }
    }

    public function drawCenterTemplate(
        $data,
        $params,
        $page,
        $textFont,
        $textFontBold,
        $boxImage,
        $barcode,
        $centerImage,
        $backgroundImage
    ) {
        try {
            /** @var \Zend_Pdf_Page $page */
            $page->drawImage($backgroundImage, 0, 0, 660, 375);
            $page->drawImage($centerImage, 0, 210, 660, 375);

            $text_width = $this->getTextWidth(
                $data['title'],
                \Zend_Pdf_Font::FONT_TIMES_BOLD_ITALIC,
                $page->getFontSize()
            );

            $left_border = (660 - $text_width) / 2;
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['style_color']))
                ->drawText($data['title'], $left_border, 175, 'UTF-8');

            $page->setFont($textFont, 13);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('From:  ' . $params['sender_name'], 15, 140, 'UTF-8');
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('To:  ' . $params['recipient_name'], 15, 119, 'UTF-8');

            $page->drawImage($boxImage, 15, 15, 260, 100);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('Value', 612, 140, 'UTF-8');
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText($params['code'], 280, 140, 'UTF-8');

            $page->setFont($textFontBold, 26);
            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['style_color']))
                ->drawText($params['balance'], 645 - $this->getTextWidth($params['balance'], \Zend_Pdf_Font::FONT_HELVETICA_BOLD, 26), 110, 'UTF-8');

            $page->drawImage($barcode, 278, 88, 480, 130);

            $text_tokens = explode(PHP_EOL, $data['note']);
            $page->setFont($textFont, 9);
            $base_line = 85;

            for ($i = 0; $i < sizeof($text_tokens); $i++) {
                $width = $this->getTextWidth($text_tokens[$i], \Zend_Pdf_Font::FONT_HELVETICA, 9);
                $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                    ->drawText($text_tokens[$i], 645 - $width, $base_line, 'UTF-8');
                $base_line -= 10;
            }

            $page->setFont($textFont, 10);
            $message_token = explode(PHP_EOL, $params['message']);
            $top_line = 80;

            for ($i = 0; $i < sizeof($message_token); $i++) {
                $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                    ->drawText($message_token[$i], 27, $top_line, 'UTF-8');
                $top_line -= 10;
            }

            $page->setFillColor(\Zend_Pdf_Color_Html::color($data['text_color']))
                ->drawText('Expiry Date: ' . $params['expiry_date'], 280, 75, 'UTF-8');

            return $page;
        } catch (\Zend_Pdf_Exception $e) {
            throw new \Zend_Pdf_Exception(
                __('Cannot generate center style template.')
            );
        }
    }

    public function drawCustomTemplate(
        $data,
        $params,
        $page,
        $font,
        $textFont,
        $boxImage,
        $barcode,
        $leftImage,
        $backgroundImage
    ) {
        try {
            $form_data = json_decode($data['additional_info'], true);
            /** @var \Zend_Pdf_Page $page */
            $page->drawImage($backgroundImage, 0, 0, 660, 375);
            $this->drawCustomImage($page, $form_data, $leftImage, 'main_image');
            $this->drawMessage($page, $params['message'], $textFont, $form_data, $boxImage, 'box_image');
            $this->drawCustomImage($page, $form_data, $barcode, 'barcode_image');

            $this->drawCustomText(
                $page,
                $form_data,
                $form_data['template[title]'],
                'custom_title',
                'style_color',
                $font
            );

            $this->drawCustomText($page, $form_data, 'From:  ' . $params['sender_name'], 'custom_from', 'text_color', $textFont);
            $this->drawCustomText($page, $form_data, 'To:  ' . $params['recipient_name'], 'custom_to', 'text_color', $textFont);
            $this->drawCustomText($page, $form_data, $params['code'], 'custom_giftkey', 'text_color', $textFont);
            $this->drawCustomText($page, $form_data, 'Value ', 'custom_value', 'text_color', $textFont);
            $this->drawCustomText($page, $form_data, $params['balance'], 'custom_display', 'style_color', $textFont);
            $this->drawCustomText($page, $form_data, 'Expiry Date: ' . $params['expiry_date'], 'custom_expiry', 'style_color', $textFont);

            $text_tokens = explode(PHP_EOL, $form_data['template[note]']);
            $page->setFont($textFont, $form_data['template[custom_note_2]']);
            $base_line = $form_data['template[custom_note_1]'];

            for ($i = 0; $i < sizeof($text_tokens); $i++) {
                $width = $this->getTextWidth($text_tokens[$i], \Zend_Pdf_Font::FONT_HELVETICA, 9);
                if ($form_data['template[custom_note_align]'] == 0) {
                    $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                        ->drawText($text_tokens[$i], $form_data['template[custom_note_0]'], $base_line, 'UTF-8');
                }
                if ($form_data['template[custom_note_align]'] == 1) {
                    $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                        ->drawText(
                            $text_tokens[$i],
                            $form_data['template[custom_note_0]'] - $width,
                            $base_line,
                            'UTF-8'
                        );
                }
                $base_line -= 11;
            }

            return $page;
        } catch (\Zend_Pdf_Exception $e) {
            throw new \Zend_Pdf_Exception(
                __('Cannot generate custom style template.')
            );
        }
    }

    public function getTextWidth($text, $fontName, $font_size)
    {
        $drawing_text = iconv('UTF-8', 'UTF-16BE//IGNORE', $text);
        $characters = array();
        $font = \Zend_Pdf_Font::fontWithName($fontName);

        for ($i = 0; $i < strlen($drawing_text); $i++) {
            $characters[] = (ord($drawing_text[$i++]) << 8) | ord($drawing_text[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $text_width = (array_sum($widths) / $font->getUnitsPerEm()) * $font_size;
        return $text_width;
    }

    public function drawCustomImage($page, $form_data, $image, $imageType)
    {
        /** @var \Zend_Pdf_Page $page */
        $page->drawImage(
            $image,
            intval($form_data['template[' . $imageType . '_0]']),
            intval($form_data['template[' . $imageType . '_1]']),
            intval($form_data['template[' . $imageType . '_2]']),
            intval($form_data['template[' . $imageType . '_3]'])
        );
    }

    public function drawMessage($page, $message, $textFont, $form_data, $image, $imageType)
    {
        /** @var \Zend_Pdf_Page $page */
        $page->drawImage(
            $image,
            intval($form_data['template[' . $imageType . '_0]']),
            intval($form_data['template[' . $imageType . '_1]']),
            intval($form_data['template[' . $imageType . '_2]']),
            intval($form_data['template[' . $imageType . '_3]'])
        );

        $page->setFont($textFont, 10);
        $message_token = explode(PHP_EOL, $message);
        $top_line = intval($form_data['template[' . $imageType . '_3]']) - 20;

        for ($i = 0; $i < sizeof($message_token); $i++) {
            $page->setFillColor(\Zend_Pdf_Color_Html::color('#777777'))
                ->drawText($message_token[$i], intval($form_data['template[' . $imageType . '_0]']) + 11, $top_line, 'UTF-8');
            $top_line -= 10;
        }
    }

    public function drawCustomText($page, $form_data, $text, $textType, $colorType, $font)
    {
        /** @var \Zend_Pdf_Page $page */
        $page->setFont($font, intval($form_data['template[' . $textType . '_2]']));

        $page->setFillColor(\Zend_Pdf_Color_Html::color($form_data['template[' . $colorType . ']']))
            ->drawText(
                $text,
                intval($form_data['template[' . $textType . '_0]']),
                intval($form_data['template[' . $textType . '_1]']),
                'UTF-8'
            );
    }

    public function getAmountGeneratedGiftCard($order_item_id)
    {
        $collection = $this->_giftCardFactory->create() ->getCollection()->addFieldToFilter('order_item_id',$order_item_id);
        return $collection->getSize();
    }

    public function getAmountGeneratedGiftCardPurchased($order_item_id)
    {
        $collection = $this->_giftCardPurchasedFactory->create() ->getCollection()->addFieldToFilter('order_item_id',$order_item_id);
        return $collection->getSize();
    }
}
