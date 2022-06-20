<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/08/2016
 * Time: 09:35
 */

namespace Magenest\GiftCard\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Field;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form;

class Design extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    protected $dataProvider;

    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        \Magenest\GiftCard\Ui\DataProvider\Product\Modifier\Data\Design $designDataFeed
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;

        $this->dataProvider = $designDataFeed;
    }
    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        if ($this->locator->getProduct()->getTypeId() === \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD) {

            $backgroundData = $this->dataProvider->getBackgroundForGiftCard($productId);
            $artData = $this->dataProvider->getArtForGiftCard($productId);

            $data[$productId]['product']['giftcard']['link'] = $backgroundData;

            $data[$productId]['product']['giftcard']['art'] = $artData;

            $data[$productId]['product']['gift-card']['link'] = $backgroundData;
            $data[$productId]['product']['gift-card']['art'] = $artData;
        }
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() === \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD) {
            // $designPath = "downloadable/children/container_links";
            $path = "gift-card/children/container_design";

            $container['arguments']['data']['config'] = [
                'componentType' => Form\Fieldset::NAME,
                'additionalClasses' => 'admin__fieldset-section',
                'label' => __('Gift Card Design'),
                'dataScope' => '',
                'visible' => $this->locator->getProduct()->getTypeId() === \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD,
                'sortOrder' => 91,
            ];

            $container = $this->arrayManager->set(
                'children',
                $container,
                [
                    'link' => $this->getDynamicRows(),
                    'art'  => $this->getDynamicRowForArt()
                ]
            );

            $meta = $this->arrayManager->set($path, $meta, $container);
        }
        return $meta;
        // TODO: Implement modifyMeta() method.
    }

    /**
     * @return array
     */
    protected function getDynamicRows()
    {
        $dynamicRows['arguments']['data']['config'] = [
            'addButtonLabel' => __('Add'),
            'componentType' => DynamicRows::NAME,
            'itemTemplate' => 'record',
            'renderDefaultRecord' => false,
            'columnsHeader' => true,
            'additionalClasses' => 'admin__field-wide',
            'dataScope' => 'giftcard',
            'deleteProperty' => 'is_delete',
            'deleteValue' => '1',
        ];

        return $this->arrayManager->set('children/record', $dynamicRows, $this->getRecord());
    }

    /**
     * @return array
     */
    protected function getDynamicRowForArt()
    {
        $dynamicRows['arguments']['data']['config'] = [
            'addButtonLabel' => __('Add art'),
            'componentType' => DynamicRows::NAME,
            'itemTemplate' => 'record',
            'renderDefaultRecord' => false,
            'columnsHeader' => true,
            'additionalClasses' => 'admin__field-wide',
            'dataScope' => 'giftcard',
            'deleteProperty' => 'is_delete',
            'deleteValue' => '1',
        ];

        return $this->arrayManager->set('children/record', $dynamicRows, $this->getRecordForArt());
    }

    /**
     *
     */
    protected function getRecordForArt()
    {
        $record['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'isTemplate' => true,
            'is_collection' => true,
            'component' => 'Magento_Ui/js/dynamic-rows/record',
            'dataScope' => '',
        ];
        $recordPosition['arguments']['data']['config'] = [
            'componentType' => Form\Field::NAME,
            'formElement' => Form\Element\Input::NAME,
            'dataType' => Form\Element\DataType\Number::NAME,
            'dataScope' => 'sort_order',
            'visible' => false,
        ];
        $recordActionDelete['arguments']['data']['config'] = [
            'label' => null,
            'componentType' => 'actionDelete',
            'fit' => true,
        ];

        return $this->arrayManager->set(
            'children',
            $record,
            [
                //'title' => $this->getTitle(),

                'container_file' => $this->getFileColumnArt(),

                'action_delete' => $recordActionDelete,
            ]
        );
    }

    /**
     * @return array
     */
    protected function getRecord()
    {
        $record['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'isTemplate' => true,
            'is_collection' => true,
            'component' => 'Magento_Ui/js/dynamic-rows/record',
            'dataScope' => '',
        ];
        $recordPosition['arguments']['data']['config'] = [
            'componentType' => Form\Field::NAME,
            'formElement' => Form\Element\Input::NAME,
            'dataType' => Form\Element\DataType\Number::NAME,
            'dataScope' => 'sort_order',
            'visible' => false,
        ];
        $recordActionDelete['arguments']['data']['config'] = [
            'label' => null,
            'componentType' => 'actionDelete',
            'fit' => true,
        ];

        return $this->arrayManager->set(
            'children',
            $record,
            [
                //'title' => $this->getTitle(),

                'container_file' => $this->getFileColumn(),

                'action_delete' => $recordActionDelete,
            ]
        );
    }

    /**
     * @return array
     */
    protected function getFileColumn()
    {
        $fileContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Background'),
            'dataScope' => '',
        ];

        $fileLinkUrl['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'title',
            'validation' => [
                'required-entry' => true,
            ],
        ];
        $fileUploader['arguments']['data']['config'] = [
            'formElement' => 'fileUploader',
            'componentType' => 'fileUploader',
            'component' => 'Magento_Downloadable/js/components/file-uploader',
            'elementTmpl' => 'Magento_Downloadable/components/file-uploader',
            'fileInputName' => 'link',
            'uploaderConfig' => [
                'url' => $this->urlBuilder->addSessionParam()->getUrl(
                    'giftcard/design/upload',
                    ['type' => 'link', '_secure' => true]
                ),
            ],
            'dataScope' => 'file',
            'validation' => [
                'required-entry' => true,
            ],
        ];

        return $this->arrayManager->set(
            'children',
            $fileContainer,
            [
                'title'=>$fileLinkUrl,
                'links_file' => $fileUploader
            ]
        );
    }

    /**
     *
     */
    protected function getFileColumnArt()
    {
        $fileContainer['arguments']['data']['config'] = [
            'componentType' => Container::NAME,
            'formElement' => Container::NAME,
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => __('Art'),
            'dataScope' => '',
        ];

        $fileLinkUrl['arguments']['data']['config'] = [
            'formElement' => Form\Element\Input::NAME,
            'componentType' => Form\Field::NAME,
            'dataType' => Form\Element\DataType\Text::NAME,
            'dataScope' => 'title',
            'validation' => [
                'required-entry' => true,
            ],
        ];
        $fileUploader['arguments']['data']['config'] = [
            'formElement' => 'fileUploader',
            'componentType' => 'fileUploader',
            'component' => 'Magento_Downloadable/js/components/file-uploader',
            'elementTmpl' => 'Magento_Downloadable/components/file-uploader',
            'fileInputName' => 'art',
            'uploaderConfig' => [
                'url' => $this->urlBuilder->addSessionParam()->getUrl(
                    'giftcard/design/art',
                    ['type' => 'art', '_secure' => true]
                ),
            ],
            'dataScope' => 'file',
            'validation' => [
                'required-entry' => true,
            ],
        ];

        return $this->arrayManager->set(
            'children',
            $fileContainer,
            [
                'title'=>$fileLinkUrl,
                'links_file' => $fileUploader
            ]
        );
    }
}
