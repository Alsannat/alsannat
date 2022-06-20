<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/07/2016
 * Time: 09:52
 */
namespace Magenest\GiftCard\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Field;

class GiftCard extends AbstractModifier
{

    const FIELD_GIFTCARD_PRICE_SELECTOR = 'giftcard_price_selector';
    const FIELD_PRICE = 'price';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    protected $meta = [];

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $model = $this->locator->getProduct();
        $modelId = $model->getId();
        $value = '';

        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->adjustPriceSection();
       // $this->addMandatoryClass();
        return $this->meta;
    }

    /***
     * adjust the price section
     */
    private function adjustPriceSection()
    {
        $groupCode = $this->getGroupCodeByField($this->meta, 'container_' . static::FIELD_GIFTCARD_PRICE_SELECTOR);

        if (!$groupCode) {
            return ;
        }

        //remove price field
        if (isset($this->meta['product-details']['children']['container_price']['children']['price']['arguments']['data']['config']['validation']['required-entry'])) {
            unset($this->meta['product-details']['children']['container_price']['children']['price']['arguments']['data']['config']['validation']['required-entry']);
        }

        $containerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_GIFTCARD_PRICE_SELECTOR,
            $this->meta,
            null,
            'children'
        );
        $fieldPath = $this->arrayManager->findPath(static::FIELD_GIFTCARD_PRICE_SELECTOR, $this->meta, null, 'children');
        $groupConfig = $this->arrayManager->get($containerPath, $this->meta);
        $fieldConfig = $this->arrayManager->get($fieldPath, $this->meta);

        $this->meta = $this->arrayManager->merge($containerPath, $this->meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/group',
                        'label' => $groupConfig['arguments']['data']['config']['label'],
                        'breakLine' => false,
                        'sortOrder' => $fieldConfig['arguments']['data']['config']['sortOrder'],
                        'dataScope' => '',
                    ],
                ],
            ],
        ]);
        $this->meta = $this->arrayManager->merge(
            $containerPath,
            $this->meta,
            [
                'children' => [
                    static::FIELD_GIFTCARD_PRICE_SELECTOR => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataScope' => static::FIELD_GIFTCARD_PRICE_SELECTOR,

                                    'additionalClasses' => 'admin__field-x-small',
                                    'component' => 'Magenest_GiftCard/js/components/giftcard-price',
                                    'elementTmpl' =>  'Magenest_GiftCard/components/price.html',


                                ],
                            ],
                        ],
                    ],

                ],
            ]
        );

        return $this->meta;
    }

    /**
     * add mandatory class into
     */
    public function addMandatoryClass()
    {
        $inputFields = [
            'giftcard_price_scheme',
            'gc_fixed_price',
            'giftcard_price_selector',
            'gc_min_price',
            'gc_max_price'
        ];

        foreach ($inputFields as $field) {
            $groupCode = $this->getGroupCodeByField($this->meta, 'container_' . $field);
            if (!$groupCode) continue;

            $containerPath = $this->arrayManager->findPath(
                'container_' .$field,
                $this->meta,
                null,
                'children'
            );


            $containerPath = $this->arrayManager->findPath(
                'container_' . $field,
                $this->meta,
                null,
                'children'
            );
            $fieldPath = $this->arrayManager->findPath($field, $this->meta, null, 'children');
            $groupConfig = $this->arrayManager->get($containerPath, $this->meta);
            $fieldConfig = $this->arrayManager->get($fieldPath, $this->meta);

            $this->meta = $this->arrayManager->merge($containerPath, $this->meta, [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magento_Ui/js/form/components/group',
                            'label' => $groupConfig['arguments']['data']['config']['label'],
                            'breakLine' => false,
                            'sortOrder' => $fieldConfig['arguments']['data']['config']['sortOrder'],
                            'dataScope' => '',
                        ],
                    ],
                ],
            ]);
            $this->meta = $this->arrayManager->merge(
                $containerPath,
                $this->meta,
                [
                    'children' => [
                        $field => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataScope' => $field,
                                        'additionalClasses' => 'mandatory',
                                    ],
                                ],
                            ],
                        ],

                    ],
                ]
            );
        }

        return $this->meta;
    }

    /**
     * @param $meta
     * @return array
     */
    public function removeRequiredPrice()
    {
        // TODO: Implement modifyMeta() method.
        $groupCode = $this->getGroupCodeByField($this->meta, 'container_' . static::FIELD_PRICE);

        if (!$groupCode) {
            return ;
        }

        $containerPath = $this->arrayManager->findPath(
            'container_' . static::FIELD_PRICE,
            $this->meta,
            null,
            'children'
        );
        $fieldPath = $this->arrayManager->findPath(static::FIELD_PRICE, $this->meta, null, 'children');
        $groupConfig = $this->arrayManager->get($containerPath, $this->meta);
        $fieldConfig = $this->arrayManager->get($fieldPath, $this->meta);

        $this->meta = $this->arrayManager->merge($containerPath, $this->meta, [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/group',
                        'label' => $groupConfig['arguments']['data']['config']['label'],
                        'breakLine' => false,
                        'sortOrder' => $fieldConfig['arguments']['data']['config']['sortOrder'],
                        'dataScope' => '',
                    ],
                ],
            ],
        ]);
        $this->meta = $this->arrayManager->merge(
            $containerPath,
            $this->meta,
            [
                'children' => [
                    static::FIELD_PRICE => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataScope' => static::FIELD_PRICE,

                                    'required' => 0,


                                ],
                            ],
                        ],
                    ],

                ],
            ]
        );

        return $this->meta;
    }
}
