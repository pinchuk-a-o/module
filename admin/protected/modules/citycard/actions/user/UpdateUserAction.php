<?php

class UpdateUserAction extends BaseAction
{
    /**
     * Обновление каналов коммуникации пользователя
     * @return array|bool|mixed|null
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
        $email      = $customerData['email'] ?? '';
        $name       = $customerData['name'] ?? '';
        $customerId = $customerData['customer_id'] ?? '';

        if ($customerId) {
            $userCityCard = UserCityCard::modelFind()->byCustomer($customerId)->with('_user')->find();

            if ($userCityCard) {
                $user        = $userCityCard->_user;
                $user->name  = $name;
                $user->email = $email;
                if ($phone) {
                    $user->phone = $phone;
                }

                if ($user->update()) {
                    UserCityCard::createOrUpdate($customerId, $user);
                    echo json_encode(['event_id' => $params['event_id'], 'status' => 1]);
                } else {
                    hSlack::notifyPinchuk(serialize($user->getErrors()));
                }
            }
        }

        return false;
    }
}