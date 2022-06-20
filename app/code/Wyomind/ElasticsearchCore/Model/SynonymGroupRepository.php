<?php

namespace Wyomind\ElasticsearchCore\Model;

class SynonymGroupRepository extends \Magento\Search\Model\SynonymGroupRepository
{

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Synonyms
     */
    protected $synonymsHelper;

    /**
     * @var \Magento\Search\Model\ResourceModel\SynonymGroup
     */
    protected $resourceModel;

    /**
     * @var StoreManagerInterface|\Magento\Store\Model\StoreManagerInterface\Proxy|null
     */
    protected $storeManager = null;

    /**
     * SynonymGroupRepository constructor.
     * @param \Magento\Search\Model\SynonymGroupFactory $synonymGroupFactory
     * @param \Magento\Search\Model\ResourceModel\SynonymGroup $resourceModel
     * @param \Wyomind\ElasticsearchCore\Helper\Synonyms $synonymsHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Search\Model\SynonymGroupFactory $synonymGroupFactory,
        \Magento\Search\Model\ResourceModel\SynonymGroup $resourceModel,
        \Wyomind\ElasticsearchCore\Helper\Synonyms $synonymsHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($synonymGroupFactory, $resourceModel);
        $this->synonymsHelper = $synonymsHelper;
        $this->resourceModel = $resourceModel;
        $this->storeManager = $storeManager;
    }

    /**
     * Save a synonym group
     * @param \Magento\Search\Api\Data\SynonymGroupInterface $synonymGroup
     * @param bool $errorOnMergeConflict
     * @return \Magento\Search\Api\Data\SynonymGroupInterface
     * @throws \Exception
     */
    public function save(\Magento\Search\Api\Data\SynonymGroupInterface $synonymGroup, $errorOnMergeConflict = false)
    {
        $result = parent::save($synonymGroup, $errorOnMergeConflict);
        $this->generateSynonymsFile();
        return $result;
    }

    /**
     * Deletes a synonym group
     * @param \Magento\Search\Api\Data\SynonymGroupInterface $synonymGroup
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Magento\Search\Api\Data\SynonymGroupInterface $synonymGroup)
    {
        $result = parent::delete($synonymGroup);
        $this->generateSynonymsFile();
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function generateSynonymsFile()
    {
        foreach ($this->storeManager->getStores() as $store) {
            $results = [];
            $synonymsGroupsAll = $this->resourceModel->getByScope(0, 0);
            $synonymsGroups = array_merge($synonymsGroupsAll, $this->resourceModel->getByScope($store->getWebsite()->getId(), $store->getId()));
            foreach ($synonymsGroups as $synonymGroup) {
                $rows = explode("\r\n", $synonymGroup['synonyms']);
                foreach ($rows as $row) {
                    $synonyms = explode(',', $row);
                    foreach ($synonyms as $word) {
                        $results[$word] = array_values(array_diff($synonyms, [$word]));
                    }
                }
            }
            $this->synonymsHelper->generateSynonymsFiles($store->getCode(), $results);
        }
    }

}