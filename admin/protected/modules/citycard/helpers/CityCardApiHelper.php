<?php

class CityCardApiHelper
{
    const TEST_DOMAIN = 'http://api.dev.mfid.ru';
    const LIVE_DOMAIN = 'https://api.mfid.ru';

    /**
     * Получить домен
     * @return string
     */
    private static function getDomain()
    {
        return hServer::isLive() ? self::LIVE_DOMAIN : self::TEST_DOMAIN;
    }

    /**
     * Возвращает токен
     * @param $login
     * @param $password
     * @param $hash
     * @return false|string
     */
    public static function getAuthToken($login, $password, $hash)
    {
        $url = self::getDomain() . "/user.auth?login=$login&password=$password&app_hash=$hash";

        $data = file_get_contents($url);
        if (!empty($data)) {
            $data = json_decode($data, true);

            return $data['response']['token'] ?? false;
        }

        return false;
    }

    /**
     * Подвиска на события
     * @param $token
     * @return bool
     */
    public static function initEvents($token)
    {
        $addCallback    = urlencode(hServer::baseHostWithScheme() . '/admin/citycard/user/create');
        $updateCallback = urlencode(hServer::baseHostWithScheme() . '/admin/citycard/user/update');

        $addUrl          = "http://" . self::getDomain() . "/user.eventStart?token=$token&name=customer.connect&callback=$addCallback";
        $addUrlDelete    = "http://" . self::getDomain() . "/user.eventStop?token=$token&name=customer.connect";
        $updateUrl       = "http://" . self::getDomain() . "/user.eventStart?token=$token&name=customer.update&callback=$updateCallback";
        $updateUrlDelete = "http://" . self::getDomain() . "/user.eventStop?token=$token&name=customer.update";

        file_get_contents($addUrlDelete);
        file_get_contents($updateUrlDelete);

        $data1 = file_get_contents($addUrl);
        $data2 = file_get_contents($updateUrl);

        if (empty($data1) || empty($data2)) {
            return false;
        }

        return true;
    }
}
