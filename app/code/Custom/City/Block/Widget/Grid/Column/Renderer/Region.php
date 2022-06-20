<?php

namespace Custom\City\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Custom\City\Model\StateFactory;
use Magento\Framework\Registry;

class Region extends AbstractRenderer
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var StateFactory
     */
    protected $stateFactory;

    /**
     * state constructor.
     * @param StateFactory $stateFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        StateFactory $stateFactory,
        Context $context,
        array $data = array()
    )
    {
        $this->stateFactory = $stateFactory;
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
        $options = $this->stateFactory->create()->load($value);
        if($options){
            return $options['default_name'] ? $options['default_name'] : 'No State';
        }

        return $value;
    }
}