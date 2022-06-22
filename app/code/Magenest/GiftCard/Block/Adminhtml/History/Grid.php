<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/03/2016
 * Time: 15:34
 */
namespace Magenest\GiftCard\Block\Adminhtml\History;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_historyFactory;

    protected $_coreRegistry;

    protected $_template = 'Magenest_GiftCard::widget/grid/extended.phtml';

    /***
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magenest\GiftCard\Model\HistoryFactory $historyFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magenest\GiftCard\Model\HistoryFactory $historyFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_historyFactory = $historyFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $giftCardId = $this->_coreRegistry->registry('id');
        $collection = $this->_historyFactory->create()->getCollection()->addFieldToFilter('giftcard_id', $giftCardId->getId());
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'history_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'history_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'date_created',
            [
                'header' => __('Created At'),
                'index' => 'date_created',
                'class' => 'datetime'
            ]
        );
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'options',
                'index' => 'action',
                'options'=>[
                   '1'=> 'Paid'
                ],
                'class' => 'column'
            ]
        );

        $this->addColumn(
            'amount',
            [
                'header' => __('Amount'),
                'index' => 'amount',
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
}
