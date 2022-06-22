<?php 
namespace Evincemage\ProductAttributes\Ui\DataProvider\Product\Form\Modifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class Image extends AbstractModifier
{
	 /**
     * @var Magento\Framework\Stdlib\ArrayManager
     */
    protected $arrayManager;

	public function __construct
	(
        ArrayManager $arrayManager
    )
    {
        $this->arrayManager = $arrayManager;
    }

    public function modifyMeta(array $meta)
    {
    	$imageCode1 = 'image1';
    	$imageCode2 = 'image2';
    	$elementPath1 = $this->arrayManager->findPath($imageCode1, $meta, null, 'children');
    	$elementPath2 = $this->arrayManager->findPath($imageCode2, $meta, null, 'children');
        $containerPath1 = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $imageCode1, $meta, null, 'children');
        $containerPath2 = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $imageCode2, $meta, null, 'children');

        if (!$elementPath1||!$elementPath2) 
        {
            return $meta;
        }

        $meta = $this->arrayManager->merge(
            $containerPath1,
            $meta,
            [
                'children'  => [
                    $imageCode1 => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'elementTmpl'   => 'Evincemage_ProductAttributes/elements/image',
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );

        $meta = $this->arrayManager->merge(
            $containerPath2,
            $meta,
            [
                'children'  => [
                    $imageCode2 => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'elementTmpl'   => 'Evincemage_ProductAttributes/elements/image',
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}