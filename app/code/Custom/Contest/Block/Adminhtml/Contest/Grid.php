<?php
namespace Custom\Contest\Block\Adminhtml\Contest;

/**
 * Adminhtml Contest grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Custom\Contest\Model\ResourceModel\Contest\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Custom\Contest\Model\Contest
     */
    protected $_contest;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Custom\Contest\Model\Contest $contestPage
     * @param \Custom\Contest\Model\ResourceModel\Contest\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Custom\Contest\Model\Contest $contest,
        \Custom\Contest\Model\ResourceModel\Contest\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_contest = $contest;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('contestGrid');
        $this->setDefaultSort('contest_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Custom\Contest\Model\ResourceModel\Contest\Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('contest_id', [
            'header'    => __('ID'),
            'index'     => 'contest_id',
        ]);
        
        $this->addColumn('first_name', ['header' => __('First Name'), 'index' => 'first_name']);
        $this->addColumn('father_name', ['header' => __('Father Name'), 'index' => 'father_name']);
        $this->addColumn('last_name', ['header' => __('Last Name'), 'index' => 'last_name']);
        $this->addColumn('phone_no', ['header' => __('Phone Number'), 'index' => 'phone_no']);
        $this->addColumn('email', ['header' => __('Email'), 'index' => 'email']);
        $this->addColumn('city', ['header' => __('City'), 'index' => 'city']);
        $this->addColumn('gender', ['header' => __('Gender'), 'index' => 'gender']);
        $this->addColumn('nationality', ['header' => __('Nationality'), 'index' => 'nationality']);
        $this->addColumn('age_group', ['header' => __('Age Group'), 'index' => 'age_group']);
        $this->addColumn('about_us', ['header' => __('How did you know about us ?'), 'index' => 'about_us']);
        
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created'),
                'index' => 'created_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );
        
        

        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));
        $this->addExportType('*/*/exportExcel', __('Excel'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('contest_id');
        $this->getMassactionBlock()->setFormFieldName('contest_id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?')
            )
        );

        return $this;
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    //public function getRowUrl($row)
    //{
      //  return $this->getUrl('*/*/edit', ['contest_id' => $row->getId()]);
    //}

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
