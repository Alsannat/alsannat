<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\ZeroDowntimeDeploy\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class Config
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Extension enabled config path
     */
    const XML_PATH_EXTENSION_ENABLED           = 'mfzerodwt/general/enabled';
    const XML_PATH_THEME_DEPLOY_MODE           = 'mfzerodwt/general/static_content_deploy';
    const XML_PATH_ENABLE_CACHES               = 'mfzerodwt/general/enable_caches';
    const XML_PATH_STORE_LOCALE                = 'general/locale/code';
    const XML_PATH_MAGENTO_COMMAND             = 'mfzerodwt/general/magento_cli_command';

    /**
     * Composer config section
     */
    const XML_PATH_COMPOSER_PULL_ENABLED       = 'mfzerodwt/composer/pull_from_composer';
    const XML_PATH_COMPOSER_COMMANDS           = 'mfzerodwt/composer/composer_pull_command';

    /**
     * Git config section
     */
    const XML_PATH_GIT_PULL_ENABLED            = 'mfzerodwt/git/pull_from_git';
    const XML_PATH_GIT_BRANCH                  = 'mfzerodwt/git/pull_from_git_branch';
    const XML_PATH_WEBHOOKS_ENABLED            = 'mfzerodwt/git/webhooks_enabled';
    const XML_PATH_WEBHOOKS_SECRET             = 'mfzerodwt/git/secret';

    /**
     * Path to scheduled flag file
     */
    const SCHEDULED_FLAG_FILE                  = 'var/mfzdd-scheduled.flag';

    /**
     * Path to running flag file
     */
    const RUNNING_FLAG_FILE                    = 'var/mfzdd-running.flag';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
    }

    public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)$this->getConfig(self::XML_PATH_EXTENSION_ENABLED, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isPullFromGit($storeId = null)
    {
        return (bool)$this->getConfig(self::XML_PATH_GIT_PULL_ENABLED, $storeId);
    }

    /**
     * @param null $storeId
     * @return false|string[]
     */
    public function getGitCommand($storeId = null)
    {
        $branchName = $this->getConfig(self::XML_PATH_GIT_BRANCH, $storeId);
        $cleanBranchName = preg_replace('/[^A-Za-z0-9\-\_]+/', '', $branchName);

        return 'git pull origin ' . $cleanBranchName;
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getMagentoCommand($storeId = null)
    {
        return (string)$this->getConfig(self::XML_PATH_MAGENTO_COMMAND, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isPullFromComposer($storeId = null)
    {
        return (bool)$this->getConfig(self::XML_PATH_COMPOSER_PULL_ENABLED, $storeId);
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnableAllCaches($storeId = null)
    {
        return (bool)$this->getConfig(self::XML_PATH_ENABLE_CACHES, $storeId);
    }

    /**
     * @param null $storeId
     * @return false|string[]
     */
    public function getComposerCommand($storeId = null)
    {
        $command = (string)$this->getConfig(self::XML_PATH_COMPOSER_COMMANDS, $storeId);
        if (false !== strpos($command, '&')
            || false !== strpos($command, ';')
            || false === strpos($command, 'composer')
        ) {
            $command = 'composer';
        }
        
        if (false === strpos($command, '--no-interaction')) {
            $command .= ' --no-interaction ';
        }

        return $command  . ' install';
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function getThemeDeployMode($storeId = null)
    {
        return (int)$this->getConfig(self::XML_PATH_THEME_DEPLOY_MODE, $storeId);
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getStoreLocaleByStoreId($storeId)
    {
        $locale =  $this->getConfig(self::XML_PATH_STORE_LOCALE, $storeId);
        return ($locale && is_string($locale) && strlen($locale) > 2) ? $locale : 'en_US';
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getThemeDataByStoreId($storeId)
    {

        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $theme = $this->themeProvider->getThemeById($themeId);

        return $theme->getData();
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isWebhooksEnabled($storeId = null)
    {
        return (bool)$this->getConfig(self::XML_PATH_WEBHOOKS_ENABLED, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getWebhooksSecret($storeId = null)
    {
        $value = $this->getConfig(self::XML_PATH_WEBHOOKS_SECRET, $storeId);
        return $value ? $this->getEncryptor()->decrypt($value) : $value;
    }

    /**
     * @return \Magento\Framework\Encryption\EncryptorInterface|mixed
     */
    private function getEncryptor()
    {
        if (null === $this->encryptor) {
            $this->encryptor = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Encryption\EncryptorInterface::class);
        }
        return $this->encryptor;
    }
}
