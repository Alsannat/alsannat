<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

/**
 * Theme utilities
 */
class Theme extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Code of the theme used on the frontend
     * @var string
     */
    private $_themeModel = '';
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Theme\Model\View\Design $design
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Theme\Model\View\Design $design
    )
    {
        $this->_themeModel = $design->getDesignParams()['themeModel']->getCode();
        parent::__construct($context);
    }

    /**
     * Get the code of the theme used on the frontend
     * @return string
     */
    public function getThemeModel()
    {
        return $this->_themeModel;
    }
}