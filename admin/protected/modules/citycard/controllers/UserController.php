<?php

class UserController extends CController
{
    /**
     * @return array
     */
    public function actions()
    {
        $actions = [];

        $actions['create'] = [
            'class' => 'application.modules.citycard.actions.user.CreateUserAction',
        ];

        $actions['update'] = [
            'class' => 'application.modules.citycard.actions.user.UpdateUserAction',
        ];

        $actions['init'] = [
            'class' => 'application.modules.citycard.actions.user.InitAction',
        ];

        return array_merge(parent::actions(), $actions);
    }
}