<?php

use Flowwow\Components\Common\LoyaltyPrograms\CityCardProgramLoyalty;

class InitAction extends BaseAction
{
    /**
     * Начальная настройка модуля
     */
    public function run()
    {
        $login    = mSettings::getValue(CityCardProgramLoyalty::ATTRIBUTE_LOGIN);
        $password = mSettings::getValue(CityCardProgramLoyalty::ATTRIBUTE_PASSWORD);
        $hash     = mSettings::getValue(CityCardProgramLoyalty::ATTRIBUTE_HASH);

        $token = CityCardApiHelper::getAuthToken($login, $password, $hash);

        if ($token) {
            CityCardApiHelper::initEvents($token);
        }

        return '';
    }
}