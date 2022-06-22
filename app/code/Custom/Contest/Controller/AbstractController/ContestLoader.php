<?php

namespace Custom\Contest\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class ContestLoader implements ContestLoaderInterface
{
    /**
     * @var \Custom\Contest\Model\ContestFactory
     */
    protected $contestFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Custom\Contest\Model\ContestFactory $contestFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Custom\Contest\Model\ContestFactory $contestFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->contestFactory = $contestFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $contest = $this->contestFactory->create()->load($id);
        $this->registry->register('current_contest', $contest);
        return true;
    }
}
