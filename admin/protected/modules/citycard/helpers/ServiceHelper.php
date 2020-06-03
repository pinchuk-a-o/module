<?php

/**
 * Class ServiceHelper
 */
class ServiceHelper
{
    /**
     * Возвращает запрос
     * @return null|Request
     * @throws Exception
     */
    public static function getRequest(): ?Request
    {
        return Registry::get('request');
    }

    /**
     * Возвращает ответ
     * @return null|Response
     * @throws Exception
     */
    public static function getResponse(): ?Response
    {
        return Registry::get('response');
    }
}