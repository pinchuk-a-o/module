<?php

namespace Flowwow\Components\Common\LoyaltyPrograms;

use CDbException;
use Order;
use ProgramLoyaltyCodes;
use ProgramLoyaltyCodesLog;
use PromoCode;
use Yii;

class CityCardProgramLoyalty extends LoyaltyProgram implements LoyaltyProgramInterface
{
    const ATTRIBUTE_LOGIN    = 'citycardUser';
    const ATTRIBUTE_PASSWORD = 'citycardPassword';
    const ATTRIBUTE_HASH     = 'citycardHash';

    /**
     * Максимальная длина промокода
     * @return int
     */
    public function getPromoCodeMaxLength()
    {
        return self::CODE_LENGTH_5;
    }

    /**
     * Минимальная длина промокода
     * @return int
     */
    public function getPromoCodeMinLength()
    {
        return self::CODE_LENGTH_5;
    }

    /**
     * Маска промокода
     * @return string
     */
    public function getPromoCodeMask()
    {
        return "/^kgfw[a-zA-Z0-9]{" . $this->getPromoCodeMinLength() . "," . $this->getPromoCodeMaxLength() . "}$/u";
    }

    /**
     * Определение промокода
     * @return bool
     */
    public function determine()
    {
        $result = false;
        if (preg_match($this->getPromoCodeMask(), $this->promoCode)) {
            //поищем промокод с таким типом в базе промокодов
            $this->promoCodeModel = PromoCode::modelFind()
                ->byCodeName($this->promoCode)
                ->byReleaseType(PromoCode::RELEASE_TYPE_CITY_CARD)
                ->find();
            if (empty($this->promoCodeModel)) {
                //поищем код в базе программ лояльности
                $this->programLoyaltyCode =
                    ProgramLoyaltyCodes::modelFind()->byCode($this->promoCode)->byType($this->getType())->find();
                if ($this->programLoyaltyCode) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Валидируем промокод
     * @return bool
     */
    public function validate()
    {
        //не валидируем дополнительно
        return true;
    }

    /**
     * Возвращает тип программы лояльности
     * @return string
     */
    public function getType()
    {
        return self::PROGRAM_CITY_CARD;
    }

    /**
     * Перенос кода программы лояльности в таблицу промокодов
     * @return bool
     * @throws CDbException
     */
    public function transferToPromocode()
    {
        $result    = true;
        $promoCode = PromoCode::modelFind()
            ->byCodeName($this->promoCode)
            ->byReleaseType(PromoCode::RELEASE_TYPE_CITY_CARD)
            ->find();
        if (!$promoCode) {
            $userId    = Yii::app()->user->getId() ?? 0;
            $promoCode =
                LoyaltyProgramManager::createPromocode(
                    $this->promoCode,
                    LoyaltyProgram::DISCOUNT_SUM_350,
                    $userId,
                    PromoCode::RELEASE_TYPE_CITY_CARD,
                    PromoCode::DISCOUNT_TYPE_SUM
                );
            if (!$promoCode) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Действия которые нужно совершить, после совершения покупки
     * @param Order $order
     * @return bool
     */
    public function afterPurchase(Order $order)
    {
        $result = false;
        $log    = ProgramLoyaltyCodesLog::create($order->id, $order->promocode, $this->getType());
        if ($log) {
            $result = true;
        }

        return $result;
    }

    /**
     * Действия которые нужно совершить, в случае отмены заказа
     * @param Order $order
     * @return bool
     * @throws CDbException
     */
    public function afterCancelOrder(Order $order)
    {
        $result = false;

        $log = ProgramLoyaltyCodesLog::modelFind()
            ->byCode($this->promoCode)
            ->byOrder($order->id)
            ->byType($this->getType())
            ->notDeleted()
            ->find();
        if ($log) {
            $log->deleted = ProgramLoyaltyCodesLog::DELETED_YES;
            $result       = $log->update(['deleted']);
        }

        return $result;
    }
}
