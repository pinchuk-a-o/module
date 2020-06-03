<?php

/**
 * Class CardsMobileSearch *
 * AR function
 * @method UserCityCard find()
 * @method UserCityCard[] findAll()
 * @method UserCityCard[] batch(int $batchSize = 100, bool $asArray = false)
 */
class UserCityCardSearch extends SearchCriteria
{
    /**
     * by user field
     * @param int ...$ids
     * @return static
     */
    public function byUser(int...$ids)
    {
        $this->criteria()->addInCondition('t.user_id', $ids);

        return $this;
    }

    /**
     * by user field
     * @param int ...$ids
     * @return static
     */
    public function byCustomer(int...$ids)
    {
        $this->criteria()->addInCondition('t.customer_id', $ids);

        return $this;
    }
}