<?php

namespace Custom\Checkoutform\Block;


class Session extends \Magento\Framework\View\Element\Template {
    protected $_coreSession;

            public function __construct(
            
                \Magento\Framework\Session\SessionManagerInterface $coreSession
                ){
                $this->_coreSession = $coreSession;
               
            }

            public function setValue($value){
                $this->_coreSession->start();
                $this->_coreSession->setMessage($value);
            }

            public function getValue(){
                $this->_coreSession->start();
                return $this->_coreSession->getMessage();
            }

            public function unSetValue(){
                $this->_coreSession->start();
                return $this->_coreSession->unsMessage();
            }

            public function setGuest($value){
                $this->_coreSession->start();
                $this->_coreSession->setGuest($value);
            }

            public function getGuest(){
                $this->_coreSession->start();
                return $this->_coreSession->getGuest();
            }

            public function unSetGuest(){
                $this->_coreSession->start();
                return $this->_coreSession->unsGuest();
            }
}