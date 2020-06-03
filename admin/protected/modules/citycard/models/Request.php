<?php

/**
 * Class Request
 */
class Request extends CHttpRequest
{
    protected $_headers = [];

    /**
     * Получить передаваемые параметры post
     * @param string|null $key
     * @return mixed|null
     */
    public function post(string $key = null)
    {
        $post = $_POST;
        if (empty($post)) {
            $post = json_decode($this->getRawBody(), true);
        }

        return $this->_getKey($key, $post);
    }

    /**
     * Получает передаваемые параметры get
     * @param string|null $key
     * @return mixed|null
     */
    public function get(string $key = null)
    {
        $get = $_GET;

        return $this->_getKey($key, $get);
    }

    /**
     * Получает заголовки запроса
     * @param string|null $key
     * @return array|mixed|null
     */
    public function headers(string $key = null)
    {
        if (empty($this->_headers)) {
            $this->_headers = getallheaders();
        }

        return $this->_getKey($key, $this->_headers);
    }

    /**
     * Получает ключ из массива
     * @param null|string $key
     * @param null|array $array
     * @return array|mixed|null
     */
    protected function _getKey(?string $key = null, array $array = [])
    {
        if (!is_null($key) && isset($array[$key])) {
            return $array[$key];
        } elseif (!is_null($key) && !isset($array[$key])) {
            return null;
        }

        return $array;
    }
}