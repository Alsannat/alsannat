<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\ZeroDowntimeDeploy\Controller\Deploy;

use Magento\Framework\App\Action\Action;
use Magefan\ZeroDowntimeDeploy\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Phrase;

class Schedule extends Action implements CsrfAwareActionInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var File
     */
    private $file;

    /**
     * Schedule constructor.
     * @param Context $context
     * @param Config $config
     * @param File $file
     */
    public function __construct(
        Context $context,
        Config $config,
        File $file
    ) {
        $this->config = $config;
        $this->file = $file;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->config->isEnabled()) {
            exit(__(strrev('sdnammoC yolpeD nuR oT emitnwoD oreZ >- 
            snoisnetxE nafegaM >- noitarugifnoC >- serotS nI noisnetxE elbanE esaelP')));
        }

        if (!$this->config->isWebhooksEnabled()) {
            exit(__(strrev('skoohbeW tiG esU oT emitnwoD oreZ >- 
            snoisnetxE nafegaM >- noitarugifnoC >- serotS nI skoohbeW elbanE esaelP')));
        }

        if (empty($this->config->getWebhooksSecret())) {
            exit(__('Webhooks secret is empty.'));
        }

        if (!$this->validateSignature()) {
            exit(__('Webhooks secret is not valid.'));
        }

        if ($this->file->write(Config::SCHEDULED_FLAG_FILE, ' ')) {
            exit(__('Deployment is scheduled.'));
        } else {
            exit(__('Something went wrong.'));
        }
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');

        return new InvalidRequestException(
            $resultRedirect,
            [new Phrase('Invalid Form Key. Please refresh the page.')]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @return bool
     */
    private function validateSignature()
    {
        $userAgent = explode('/', $this->getRequest()->getHeader('User-Agent'))[0];
        switch ($userAgent) {
            case 'GitLab':
                if ($this->config->getWebhooksSecret() === $this->getRequest()->getHeader('X-Gitlab-Token')) {
                    return true;
                }
                break;
            case 'GitHub-Hookshot':
                $postData = file_get_contents('php://input');
                $signature = 'sha256=' . hash_hmac('sha256', $postData, $this->config->getWebhooksSecret());
                if (hash_equals($signature, $this->getRequest()->getHeader('X-Hub-Signature-256'))) {
                    return true;
                }
                break;
            case 'Bitbucket-Webhooks':
                return false;
        }

        return false;
    }
}
