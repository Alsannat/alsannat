<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 09/08/2017
 * Time: 16:13
 */

namespace Magenest\GiftCard\Controller\Adminhtml\Template;
use Magento\Framework\App\Filesystem\DirectoryList;


class BackgroundUploader extends \Magento\Backend\App\Action
{
    protected $uploader;

    protected $directory;

    protected $filesystem;

    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\UploaderFactory  $uploader,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->uploader = $uploader;
        parent::__construct($context);
    }//end __construct()

    public function execute7()
    {
        // TODO: Implement execute() method.
    }

    public function execute()
    {
        $out = [];
        $mainImg =[];
        $result =[];
        try {
            $filesArray = $this->getRequest()->getFiles()->getArrayCopy();
            foreach ($filesArray as $key => $value) {
                if ($value['tmp_name'] != null) {
                    $fileId = $key;
                    $uploader = $this->uploader->create(['fileId' => $fileId]);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $main_path = 'giftcard/template/background/';

                    $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($main_path));

                    $result['url']  = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config')->getMediaUrl($result['file']);
                    $imageUrl = str_replace('catalog/product' , 'giftcard/template/background' , $result['url']);

                    $result['url'] = $imageUrl;

                    $result['code'] = 'success';
                    $out[] = $result;

                    //the result
                }

                //build the html section
                if (!empty($out)) {
                    $result['code'] = 'success';
                    //$result['htmlSection'] = $this->buildHtmlSection($out);
                }
            }

        } catch (\Exception $e) {
            $result = [
                'error'     => $e->getMessage(),
                'errorcode' => $e->getCode(),
                'code'      =>'error',
                'errorMessage' =>  __('There is error occurred during uploading images. Please refer to log file for more information')
            ];
        }//end try

        /*
            * @var \Magento\Framework\Controller\Result\Raw $response
        */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }//end execute()

    /**
     * @param $input
    /**
     * <div class="saved_images_container_main" style="float: left" align="center">
    <img class="saved_image_main <?php if (isset($item['selected'])) { echo "selected"; } ?>" onclick="addBorder(event)" src="<?php echo $block->getMediaUrl().'giftcard/template/main'.$item['file']?>"><br>
    <button style="" onclick="deleteImage(event)">Delete</button>
    </div>
     */
    public function buildHtmlSection($input) {
        $html = '';
        $delete = __('Delete');
       foreach ($input as $item) {
           $src = $item['url'];
           $str = <<<EOF
    <div class="saved_images_container_main" style="float: left" align="center">
       <img class="saved_image_main" onclick="addBorder(event)" src="$src" />
       <button style="" onclick="deleteImage(event)">$delete</button>
    </div>
EOF;
           $html.= $str;
       }

    }
}