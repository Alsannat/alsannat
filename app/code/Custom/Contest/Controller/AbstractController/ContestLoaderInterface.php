<?php

namespace Custom\Contest\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface ContestLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Custom\Contest\Model\Contest
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
