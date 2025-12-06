<?php

namespace JamesGifford\LaravelArchitecture\Support\Controllers;

use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;

abstract class PageControllerAbstract extends ControllerAbstract implements PageControllerInterface
{
    /**
     * Set the page view property with a Response Transfer.
     */
    protected function page(ResponseTransferInterface $response)
    {
        return ['page' => $response];
    }
}
