<?php
namespace MGS\Popup\Controller\Adminhtml\PopupGrid;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Magento\Backend\App\Action
{
	/**
     * File Uploader factory
     *
     * @var \Magento\Framework\Filesystem\Driver\File
     */
	protected $_file;
	
	
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Filesystem\Driver\File $file
	)     
	{
		parent::__construct($context);
		
		$this->_file = $file;
	}
	
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute()
    {
		
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('MGS\Popup\Model\Popup');
		
			if (isset($_FILES['background_image']['name']) && $_FILES['background_image']['name'] != '') {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'background_image']
				);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
				$uploader->setAllowRenameFiles(true);
				$uploader->setAllowCreateFolders(true);
				$uploader->setFilesDispersion(true);
				$ext = pathinfo($_FILES['background_image']['name'], PATHINFO_EXTENSION);
				if ($uploader->checkAllowedExtension($ext)) {
					$path = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA)
						->getAbsolutePath('mgs_popup/');
					$uploader->save($path);
					$fileName = $uploader->getUploadedFileName();
					if ($fileName) {
						$data['background_image'] = 'mgs_popup' . $fileName;
					}
				} else {
					$this->messageManager->addError(__('Disallowed file type.'));
					return $this->redirectToEdit($model, $data);
				}
			} else {
				if (isset($data['background_image']['delete']) && $data['background_image']['delete'] == 1) {
					$fileName = substr($data['background_image']['value'],10);
					$path = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs_popup/');
					$fullPath = $path . $fileName;
					
					if ($this->_file->isExists($fullPath)) {
						$this->_file->deleteFile($fullPath);
					}
					$data['background_image'] = '';
					
				} else {
					unset($data['background_image']);
				}
			}
			
			if($this->getRequest()->getParam('customer_group')) {
				$customerGroups = $this->getRequest()->getParam('customer_group');
				$data['customer_group'] = serialize($customerGroups);
			}else {
				$data['customer_group'] = '';
			}
			
			$id = $this->getRequest()->getParam('popup_id');
            if ($id) {
                $model->load($id);
            }
			
            $model->setData($data);
			
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The popup has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('popup_id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, $e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('popup_id' => $this->getRequest()->getParam('popup_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}
