<?php
/**
 * Created by PhpStorm.
 * User: thomasnordkvist
 * Date: 17-01-30
 * Time: 08:15
 */
 
namespace Netrix\ConfigurableSkuSwitch\Plugin\Magento\ConfigurableProduct\Block\Product\View\Type;

class Configurable
{
    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $result) 
    {

        $jsonResult = json_decode($result, true);

        $jsonResult['skus'] = [];
        $jsonResult['names'] = [];
        $jsonResult['sizes'] = [];
        $jsonResult['dimensions'] = [];
        $jsonResult['materials'] = [];
        $jsonResult['bag_weights'] = [];
        $jsonResult['descriptions'] = [];
        $jsonResult['pocket_sizes'] = [];
        $jsonResult['product_types'] = [];
        $jsonResult['lbs_weights'] = [];
        $jsonResult['kg_weights'] = [];
        $jsonResult['set_dimensions'] = [];
        $jsonResult['metric_weights'] = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
        foreach ($subject->getAllowProducts() as $simpleProduct) {
            $product = $objectManager->get('Magento\Catalog\Model\Product')->load($simpleProduct->getId());
            $jsonResult['skus'][$simpleProduct->getId()] = $simpleProduct->getSku();
            $jsonResult['names'][$simpleProduct->getId()] = $simpleProduct->getName();
            $jsonResult['sizes'][$simpleProduct->getId()] = $product->getAttributeText('size');
            $jsonResult['dimensions'][$simpleProduct->getId()] = $product->getDimensions();
            $jsonResult['materials'][$simpleProduct->getId()] = $product->getAttributeText('material');
            $jsonResult['bag_weights'][$simpleProduct->getId()] = $product->getBagWeight();
            $jsonResult['descriptions'][$simpleProduct->getId()] = $product->getDescription();
            $jsonResult['pocket_sizes'][$simpleProduct->getId()] = $product->getAttributeText('laptop_pocket_size');
            $jsonResult['product_types'][$simpleProduct->getId()] = $this->getCategoryIds($simpleProduct->getId()) == false ? $this->getCategoryIds($subject->getProduct()->getId()) : $this->getCategoryIds($simpleProduct->getId());
            $jsonResult['lbs_weights'][$simpleProduct->getId()] = $this->getMultipleLbsWeightHtml($product,(bool)$product->getHasMultipleWeights()); 
            $jsonResult['kg_weights'][$simpleProduct->getId()] = $this->getMultipleKgWeightHtml($product,(bool)$product->getHasMultipleWeights());
            $jsonResult['set_dimensions'][$simpleProduct->getId()] = $this->getSetDimensionHtml($product,(bool)$product->getHasMultipleDimensions());
            $jsonResult['metric_weights'][$simpleProduct->getId()] = $this->getMetricWeightHtml($product,$product->getHasMultipleDimensions());

        }

        $result = json_encode($jsonResult);

        return $result;
    }

    public function getCategoryIds(int $productId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->productCategory = $objectManager->get('Magento\Catalog\Model\ProductCategoryList');
        $categoryIds = $this->productCategory->getCategoryIds($productId);
        $category = [];
        $categoryNames = [];
        if ($categoryIds) {
            $category = array_unique($categoryIds);
        }

        if(count($category)>0)
        {
            $iterator = 1;
            foreach ($category as $categoryId)
            {
                if(count($category)==$iterator)
                {
                    $cat = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
                    $categoryNames[] = $cat->getName();
                }

                $iterator++;
                
                
            }
        }
        if(count($categoryNames)>0)
        {
            return $categoryNames;
        }
       
        return false;
       
    }

    public function getMultipleKgWeightHtml($_product,$usesMultiple)
    {
        $weightHtml = "";
        if(!is_null($_product))
        {
            if($usesMultiple)
            {
                if(!is_null($_product->getWeightKg1())&&!is_null($_product->getAttributeText('dimension_title_1')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_1').' : '.$_product->getWeightKg1().'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg2())&&!is_null($_product->getAttributeText('dimension_title_2')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_2').' : '.$_product->getWeightKg2().'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg3())&&!is_null($_product->getAttributeText('dimension_title_3')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_3').' : '.$_product->getWeightKg3().'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg4())&&!is_null($_product->getAttributeText('dimension_title_4')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_4').' : '.$_product->getWeightKg4().'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg5())&&!is_null($_product->getAttributeText('dimension_title_5')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_5').' : '.$_product->getWeightKg5().'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg6())&&!is_null($_product->getAttributeText('dimension_title_6')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_6').' : '.$_product->getWeightKg6().'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg7())&&!is_null($_product->getAttributeText('dimension_title_7')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_7').' : '.$_product->getWeightKg7().'</strong><br/>';
                }
            }
            else
            {
                if(!is_null($_product->getWeightKg1())&&!is_null($_product->getAttributeText('dimension_title_1')))
                {
                    $weightHtml.='<strong>'.$_product->getAttributeText('dimension_title_1').' : '.$_product->getWeightKg1().'</strong><br/>';
                }
            }
        }

        return $weightHtml;

    }

    public function getMultipleLbsWeightHtml($_product,$usesMultiple)
    {
        $weightLBSHtml = "";
        if(!is_null($_product))
        {
            if($usesMultiple)
            {
                if(!is_null($_product->getWeightKg1())&&!is_null($_product->getAttributeText('dimension_title_1')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_1').' : '.number_format((float)$_product->getWeightKg1()*2.205, 3, '.', '').'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg2())&&!is_null($_product->getAttributeText('dimension_title_2')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_2').' : '.number_format((float)$_product->getWeightKg2()*2.205, 3, '.', '').'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg3())&&!is_null($_product->getAttributeText('dimension_title_3')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_3').' : '.number_format((float)$_product->getWeightKg3()*2.205, 3, '.', '').'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg4())&&!is_null($_product->getAttributeText('dimension_title_4')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_4').' : '.number_format((float)$_product->getWeightKg4()*2.205, 3, '.', '').'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg5())&&!is_null($_product->getAttributeText('dimension_title_5')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_5').' : '.number_format((float)$_product->getWeightKg5()*2.205, 3, '.', '').'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg6())&&!is_null($_product->getAttributeText('dimension_title_6')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_6').' : '.number_format((float)$_product->getWeightKg6()*2.205, 3, '.', '').'</strong><br/>';
                }

                if(!is_null($_product->getWeightKg7())&&!is_null($_product->getAttributeText('dimension_title_7')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_7').' : '.number_format((float)$_product->getWeightKg7()*2.205, 3, '.', '').'</strong><br/>';
                }
            }
            else
            {
                if(!is_null($_product->getWeightKg1())&&!is_null($_product->getAttributeText('dimension_title_1')))
                {
                    $weightLBSHtml.= '<strong>'.$_product->getAttributeText('dimension_title_1').' : '.number_format((float)$_product->getWeightKg1()*2.205, 3, '.', '').'</strong><br/>';
                }
            }
        }

        return $weightLBSHtml;

    }

    public function getMetricWeightHtml($product,$usesMultiple)
    {
        $html = "";
        if(!is_null($product))
        {
            if($usesMultiple)
            {
                $html.="<p>";
                if(!is_null($product->getAttributeText('dimension_title_1'))&&!is_null($product->getData('dimension_height_1'))&&!is_null($product->getData('dimension_width_1'))&&!is_null($product->getData('dimension_length_1')))
                {
                    $metricWeight1 = ((float)$product->getData('dimension_height_1')*
                                      (float)$product->getData('dimension_width_1')*
                                      (float)$product->getData('dimension_length_1')
                                    )/5000;
                    $html.="<strong>".$product->getAttributeText('dimension_title_1')." : ".$metricWeight1." Kg </strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_2'))&&!is_null($product->getData('dimension_height_2'))&&!is_null($product->getData('dimension_width_2'))&&!is_null($product->getData('dimension_length_2')))
                {
                    $metricWeight2 = ((float)$product->getData('dimension_height_2')*
                                      (float)$product->getData('dimension_width_2')*
                                      (float)$product->getData('dimension_length_2')
                                    )/5000;
                    $html.="<strong>".$product->getAttributeText('dimension_title_2')." : ".$metricWeight2." Kg </strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_3'))&&!is_null($product->getData('dimension_height_3'))&&!is_null($product->getData('dimension_width_3'))&&!is_null($product->getData('dimension_length_3')))
                {
                    $metricWeight3 = ((float)$product->getData('dimension_height_3')*
                                      (float)$product->getData('dimension_width_3')*
                                      (float)$product->getData('dimension_length_3')
                                    )/5000;
                    $html.="<strong>".$product->getAttributeText('dimension_title_3')." : ".$metricWeight3." Kg </strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_4'))&&!is_null($product->getData('dimension_height_4'))&&!is_null($product->getData('dimension_width_4'))&&!is_null($product->getData('dimension_length_4')))
                {
                    $metricWeight4 = ((float)$product->getData('dimension_height_4')*
                                      (float)$product->getData('dimension_width_4')*
                                      (float)$product->getData('dimension_length_4')
                                    )/5000;
                    $html.="<strong>".$product->getAttributeText('dimension_title_4')." : ".$metricWeight4." Kg </strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_5'))&&!is_null($product->getData('dimension_height_5'))&&!is_null($product->getData('dimension_width_5'))&&!is_null($product->getData('dimension_length_5')))
                {
                    $metricWeight5 = ((float)$product->getData('dimension_height_5')*
                                      (float)$product->getData('dimension_width_5')*
                                      (float)$product->getData('dimension_length_5')
                                    )/5000;
                    $html.="<strong>".$product->getAttributeText('dimension_title_5')." : ".$metricWeight5." Kg </strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_6'))&&!is_null($product->getData('dimension_height_6'))&&!is_null($product->getData('dimension_width_6'))&&!is_null($product->getData('dimension_length_6')))
                {
                    $metricWeight6 = ((float)$product->getData('dimension_height_6')*
                                      (float)$product->getData('dimension_width_6')*
                                      (float)$product->getData('dimension_length_6')
                                    )/5000;
                    $html.="<strong>".$product->getAttributeText('dimension_title_6')." : ".$metricWeight6." Kg </strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_7'))&&!is_null($product->getData('dimension_height_7'))&&!is_null($product->getData('dimension_width_7'))&&!is_null($product->getData('dimension_length_7')))
                {
                    $metricWeight7 = ((float)$product->getData('dimension_height_7')*
                                      (float)$product->getData('dimension_width_7')*
                                      (float)$product->getData('dimension_length_7')
                                    )/5000;
                    $html.="<strong>".$product->getAttributeText('dimension_title_7')." : ".$metricWeight7." Kg </strong><br/>";
                }

                $html.="</p>";
            }
            else
            {
                if(!is_null($product->getData('dimension_height_1'))&&!is_null($product->getData('dimension_width_1'))&&!is_null($product->getData('dimension_length_1')))
                {
                    $metricWeight1 = ((float)$product->getData('dimension_height_1')*
                                      (float)$product->getData('dimension_width_1')*
                                      (float)$product->getData('dimension_length_1')
                                    )/5000;
                    $html.="<p><strong>".$metricWeight1."Kg</strong></p>";
                }
            }
            
        }

        return $html;
    }

    public function getSetDimensionHtml($product,$usesMultiple)
    {
        
        $html = "";
        if(!is_null($product))
        {
            if($usesMultiple)
            {
                $html.="<p>";
                if(!is_null($product->getAttributeText('dimension_title_1'))&&!is_null($product->getData('dimension_height_1'))&&!is_null($product->getData('dimension_width_1'))&&!is_null($product->getData('dimension_length_1')))
                {
                    $html.="<strong>".$product->getAttributeText('dimension_title_1')." : H: ".$product->getData('dimension_height_1')." cm, D: ".$product->getData('dimension_length_1')." cm, W: ".$product->getData('dimension_width_1')." cm</strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_2'))&&!is_null($product->getData('dimension_height_2'))&&!is_null($product->getData('dimension_width_2'))&&!is_null($product->getData('dimension_length_2')))
                {
                    $html.="<strong>".$product->getAttributeText('dimension_title_2')." : H: ".$product->getData('dimension_height_2')." cm, D: ".$product->getData('dimension_length_2')." cm, W: ".$product->getData('dimension_width_2')." cm</strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_3'))&&!is_null($product->getData('dimension_height_3'))&&!is_null($product->getData('dimension_width_3'))&&!is_null($product->getData('dimension_length_3')))
                {
                    $html.="<strong>".$product->getAttributeText('dimension_title_3')." : H: ".$product->getData('dimension_height_3')." cm, D: ".$product->getData('dimension_length_3')." cm, W: ".$product->getData('dimension_width_3')." cm</strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_4'))&&!is_null($product->getData('dimension_height_4'))&&!is_null($product->getData('dimension_width_4'))&&!is_null($product->getData('dimension_length_4')))
                {
                    $html.="<strong>".$product->getAttributeText('dimension_title_4')." : H: ".$product->getData('dimension_height_4')." cm, D: ".$product->getData('dimension_length_4')." cm, W: ".$product->getData('dimension_width_4')." cm</strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_5'))&&!is_null($product->getData('dimension_height_5'))&&!is_null($product->getData('dimension_width_5'))&&!is_null($product->getData('dimension_length_5')))
                {
                    $html.="<strong>".$product->getAttributeText('dimension_title_5')." : H: ".$product->getData('dimension_height_5')." cm, D: ".$product->getData('dimension_length_5')." cm, W: ".$product->getData('dimension_width_5')." cm</strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_6'))&&!is_null($product->getData('dimension_height_6'))&&!is_null($product->getData('dimension_width_6'))&&!is_null($product->getData('dimension_length_6')))
                {
                    $html.="<strong>".$product->getAttributeText('dimension_title_6')." : H: ".$product->getData('dimension_height_6')." cm, D: ".$product->getData('dimension_length_6')." cm, W: ".$product->getData('dimension_width_6')." cm</strong><br/>";
                }

                if(!is_null($product->getAttributeText('dimension_title_7'))&&!is_null($product->getData('dimension_height_7'))&&!is_null($product->getData('dimension_width_7'))&&!is_null($product->getData('dimension_length_7')))
                {
                    $html.="<strong>".$product->getAttributeText('dimension_title_7')." : H: ".$product->getData('dimension_height_7')." cm, D: ".$product->getData('dimension_length_7')." cm, W: ".$product->getData('dimension_width_7')." cm</strong>";
                }

                $html.="</p>";
            }
            else
            {
                if(!is_null($product->getData('dimension_height_1'))&&!is_null($product->getData('dimension_width_1'))&&!is_null($product->getData('dimension_length_1')))
                {
                    $html.="<p><strong>"."H: ".$product->getData('dimension_height_1')." cm <br/> D: ".$product->getData('dimension_length_1')." cm <br/> W: ".$product->getData('dimension_width_1')." cm</strong></p>";
                }
            }
            
        }

        return $html;
    }
}