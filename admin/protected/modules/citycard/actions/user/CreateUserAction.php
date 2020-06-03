<?php

class CreateUserAction extends BaseAction
{
    /**
     * Создаем пользователя
     * @return array|mixed
     * @throws CDbException
     * @throws Exception
     */
    public function run()
    {
        parse_str(ServiceHelper::getRequest()->getRawBody(), $params);

        $data = [];
        if (isset($params['data'])) {
            $data = json_decode($params['data'], true);
        }

        $customerData = $data['customer'] ?? null;
        if (!$customerData) {
            hSlack::notifyPinchuk(serialize($params));

            return false;
        }

        $phone      = $customerData['phone'] ?? '';
        $customerId = $customerData['customer_id'] ?? '';

        $userCityCard = UserCityCard::modelFind()->byCustomer($customerId)->find();
        if ($userCityCard) {
            hSlack::notifyPinchuk('User city card id ' . $customerId . ' повторная попытка создания');

            return false;
        }

        if ($phone) {
            $mUser  = sUser::createCustomerAuto($customerData);

            if ($mUser) {
                UserCityCard::createOrUpdate($customerId, $mUser);
            }

            echo json_encode(['event_id' => $params['event_id'], 'status' => 1]);
        }

        return false;
    }
}