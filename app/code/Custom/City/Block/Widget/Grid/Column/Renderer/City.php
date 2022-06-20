<?php

namespace Custom\City\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Custom\City\Model\CityFactory;
use Magento\Framework\Registry;

class City extends AbstractRenderer
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StateFactory
     */
    protected $cityFactory;

    /**
     * @param Registry $registry
     * @param CityFactory $cityFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        CityFactory $cityFactory,
        Context $context,
        array $data = array()
    )
    {
        $this->cityFactory = $cityFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return mixed
     */
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        // Get default value:
        $value = parent::_getValue($row);
        $options = $this->cityFactory->create()->load($value);
        if($options){
            return $options['city'];
        }
        return $value;
    }
}