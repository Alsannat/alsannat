<?php

namespace Vnecoms\Sms\Block\Customer;


class Session extends \Magento\Framework\View\Element\Template {
        protected $_coreSession;

            public function __construct(
            
                \Magento\Framework\Session\SessionManagerInterface $coreSession
                ){
                $this->_coreSession = $coreSession;
               
            }

            public function setValue($value){
                $this->_coreSession->start();
                $this->_coreSession->setCode($value);
            }

            public function getValue(){
                $this->_coreSession->start();
                return $this->_coreSession->getCode();
            }

            public function unSetValue(){
                $this->_coreSession->start();
                return $this->_coreSession->unsCode();
            }
}