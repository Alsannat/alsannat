<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 05/09/2016
 * Time: 09:06
 */
namespace Magenest\GiftCard\Page;

class Page extends \Magento\Framework\View\Result\Page
{
    /**
     * @description a wrapper function for assign function
     * @param $key
     * @param null $value
     * @return $this
     */
    public function assignPublic($key, $value = null)
    {
        return $this->assign($key, $value);
    }
}
