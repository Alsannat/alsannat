<?php
namespace Custom\Sorter\Plugin\Catalog\Block;

class Toolbar
{
    public function aroundSetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, 
    \Closure $proceed, $collection) 
    {
        $currentOrder = $subject->getCurrentOrder();
        $dir = $subject->getRequest()->getParam("product_list_dir");
        $result = $proceed($collection);

        if($currentOrder)
        {
            if($currentOrder == 'high_to_low' && (empty($dir) || $dir == "asc"))
            {
                $subject->getCollection()->setOrder('price', 'desc');
            }
            elseif($currentOrder == 'high_to_low' && ($dir == "desc"))
            {
                $subject->getCollection()->setOrder('price', 'asc');
            }
            elseif ($currentOrder == 'low_to_high' && (empty($dir) || $dir == "asc"))
            {
                $subject->getCollection()->setOrder('price', 'asc');
            }
            elseif ($currentOrder == 'low_to_high' && ($dir == "desc" ))
            {
                $subject->getCollection()->setOrder('price', 'desc');
            }
            elseif ($currentOrder == 'new' && (empty($dir) || $dir == "asc"))
            {
                $subject->getCollection()->setOrder('entity_id','desc');
            }
            elseif ($currentOrder == 'new' && (empty($dir) || $dir == "desc"))
            {
                $subject->getCollection()->setOrder('entity_id','asc');
            }
            elseif ($currentOrder == 'discount' && (empty($dir) || $dir == "asc"))
            {
                $biggestsaving = "discount_percent_value_".rand(1,1000);
                $subject->getCollection()
                    ->addExpressionAttributeToSelect($biggestsaving , '(({{price}} - final_price) / {{price}})',array('price'))
                    ->getselect()->Order($biggestsaving,'desc');
            }
            elseif ($currentOrder == 'discount' && (empty($dir) || $dir == "desc"))
            {
                $biggestsaving = "discount_percent_value_".rand(1,1000);
                $subject->getCollection()
                    ->addExpressionAttributeToSelect($biggestsaving , '(({{price}} - final_price) / {{price}})',array('price'))
                    ->getselect()->Order($biggestsaving,'asc');
            }
        }
        
        return $result;
    }
}