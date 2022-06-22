<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 04/10/2018
 * Time: 10:52
 */

namespace Wyomind\ElasticsearchCore\Helper;


class Design extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config|null
     */
    protected $_configHelper = null;

    /**
     * Design constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper
    )
    {
        parent::__construct($context);
        $this->_configHelper = $configHelper;
    }

    public function getPrimaryColor($store = null)
    {
        return $this->_configHelper->getDesignPrimaryColor($store);
    }

    public function getSecondaryColor($store = null)
    {
        return $this->_configHelper->getDesignSecondaryColor($store);
    }

    public function getBackgroundPrimaryColor($store = null)
    {
        return $this->_configHelper->getDesignBackgroundPrimaryColor($store);
    }

    public function getBackgroundSecondaryColor($store = null)
    {
        return $this->_configHelper->getDesignBackgroundSecondaryColor($store);
    }

    public function getOverlayEnabled($store = null)
    {
        return $this->_configHelper->getDesignOverlayEnable($store);
    }
    public function getTransitionEnabled($store = null)
    {
        return $this->_configHelper->getDesignTransitionEnable($store);
    }
    public function getTransitionDuration($store = null)
    {
        return $this->_configHelper->getDesignTransitionDuration($store);
    }

    public function getBlurEnabled($store = null)
    {
        return $this->_configHelper->getDesignBlurEnable($store);
    }
    public function adjustBrightness($hex, $steps)
    {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }

        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color = hexdec($color);
            $color = max(0, min(255, $color + $steps));
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
        }

        return $return;
    }
}