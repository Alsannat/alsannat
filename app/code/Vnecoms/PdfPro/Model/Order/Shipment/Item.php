<?php

namespace Vnecoms\PdfPro\Model\Order\Shipment;

/**
 * Class Item.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class Item extends \Magento\Framework\DataObject
{
    /**
     * Item constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Get Options of items.
     *
     * @param \Magento\Sales\Model\Order\Item $item
     *
     * @return array:
     */
    public function getItemOptions($item)
    {
        $result = array();
        if (!($item instanceof \Magento\Sales\Model\Order\Item)) {
            $item = $item->getOrderItem();
        }

        if ($options = $item->getProductOptions()) {
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
        }

        return $result;
    }

    /**
     * Getting all available childs for Invoice, Shipmen or Creditmemo item.
     *
     * @param Varien_Object $item
     *
     * @return array
     */
    public function getChilds($item)
    {
        $_itemsArray = array();

        if ($item instanceof \Magento\Sales\Model\Order\Invoice\Item) {
            $_items = $item->getInvoice()->getAllItems();
        } elseif ($item instanceof \Magento\Sales\Model\Order\Shipment\Item) {
            $_items = $item->getShipment()->getAllItems();
        } elseif ($item instanceof \Magento\Sales\Model\Order\Creditmemo\Item) {
            $_items = $item->getCreditmemo()->getAllItems();
        }

        if ($_items) {
            foreach ($_items as $_item) {
                $parentItem = $_item->getOrderItem()->getParentItem();
                if ($parentItem) {
                    $_itemsArray[$parentItem->getId()][$_item->getOrderItemId()] = $_item;
                } else {
                    $_itemsArray[$_item->getOrderItem()->getId()][$_item->getOrderItemId()] = $_item;
                }
            }
        }

        if (isset($_itemsArray[$item->getOrderItem()->getId()])) {
            return $_itemsArray[$item->getOrderItem()->getId()];
        } else {
            return;
        }
    }

    /**
     * Retrieve is Shipment Separately flag for Item.
     *
     * @return bool
     */
    public function isShipmentSeparately($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }

            $parentItem = $item->getParentItem();
            if ($parentItem) {
                $options = $parentItem->getProductOptions();
                if ($options) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == \Magento\Catalog\Model\Product\Type\AbstractType::SHIPMENT_SEPARATELY) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
                    if (isset($options['shipment_type'])
                        && $options['shipment_type'] == \Magento\Catalog\Model\Product\Type\AbstractType::SHIPMENT_SEPARATELY) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['shipment_type'])
                && $options['shipment_type'] == \Magento\Catalog\Model\Product\Type\AbstractType::SHIPMENT_SEPARATELY) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve is Child Calculated.
     *
     * @return bool
     */
    public function isChildCalculated($item = null)
    {
        if ($item) {
            if ($item->getOrderItem()) {
                $item = $item->getOrderItem();
            }

            $parentItem = $item->getParentItem();
            if ($parentItem) {
                $options = $parentItem->getProductOptions();
                if ($options) {
                    if (isset($options['product_calculations']) &&
                        $options['product_calculations'] == \Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_CHILD
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                $options = $item->getProductOptions();
                if ($options) {
                    if (isset($options['product_calculations']) &&
                        $options['product_calculations'] == \Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_CHILD
                    ) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }

        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['product_calculations'])
                && $options['product_calculations'] == \Magento\Catalog\Model\Product\Type\AbstractType::CALCULATE_CHILD) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve Bundle Options.
     *
     * @return array
     */
    public function getBundleOptions($item = null)
    {
        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['bundle_options'])) {
                return $options['bundle_options'];
            }
        }

        return array();
    }

    /**
     * Retrieve Selection attributes.
     *
     * @return mixed
     */
    public function getSelectionAttributes($item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }
        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }

        return;
    }

    /**
     * Retrieve Order options.
     *
     * @return array
     */
    public function getOrderOptions($item = null)
    {
        $result = array();

        $options = $this->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }

        return $result;
    }

    /**
     * Retrieve Order Item.
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getOrderItem()
    {
        if ($this->getItem() instanceof \Magento\Sales\Model\Order\Item) {
            return $this->getItem();
        } else {
            return $this->getItem()->getOrderItem();
        }
    }

    /**
     * Retrieve Value HTML.
     *
     * @return string
     */
    public function getValueHtml($item)
    {
        $result = strip_tags($item->getName());
        if (!$this->isShipmentSeparately($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result = sprintf('%d', $attributes['qty']).' x '.$result;
            }
        }
        if (!$this->isChildCalculated($item)) {
            $attributes = $this->getSelectionAttributes($item);
            if ($attributes) {
                $result .= ' '.strip_tags($this->getOrderItem()->getOrder()->formatPrice($attributes['price']));
            }
        }

        return $result;
    }

    /**
     * Can show price info for item.
     *
     * @return bool
     */
    public function canShowPriceInfo($item)
    {
        if (($item->getOrderItem()->getParentItem() && $this->isChildCalculated())
            || (!$item->getOrderItem()->getParentItem() && !$this->isChildCalculated())) {
            return true;
        }

        return false;
    }
}
