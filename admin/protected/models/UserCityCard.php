<?php

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $customer_id
 *
 * Relations
 * @property User $_user
 */
class UserCityCard extends FwActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{user_city_card}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['user_id, customer_id', 'required'],
            ['user_id, customer_id', 'numerical', 'integerOnly' => true],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            '_user' => [self::BELONGS_TO, 'User', 'user_id', 'joinType' => 'LEFT JOIN'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'user_id'     => 'User',
            'customer_id' => 'Customer',
        ];
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CardsMobile the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return UserCityCardSearch
     */
    public static function modelFind()
    {
        return UserCityCardSearch::model(get_called_class());
    }

    /**
     * Создаем/обновляем запись
     * @param int $customerId
     * @param User|mUser $user
     * @return UserCityCard
     */
    public static function createOrUpdate(int $customerId, $user)
    {
        $userCityCard = self::modelFind()->byCustomer($customerId)->find();
        if (empty($userCityCard)) {
            $userCityCard              = new UserCityCard();
            $userCityCard->customer_id = $customerId;
        }

        $userCityCard->user_id = (int)$user->id;

        $userCityCard->save();

        return $userCityCard;
    }
}