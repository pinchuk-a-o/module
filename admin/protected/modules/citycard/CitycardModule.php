<?php

class CitycardModule extends CWebModule
{
    public function init()
    {
        $this->setImport([
            'citycard.models.*',
            'citycard.models.middleware.*',
            'citycard.helpers.*',
            'citycard.actions.card.*',
            'citycard.actions.*',
            'citycard.interfaces.*',
            'citycard.controllers.*',
        ]);
        $this->_addServices();
    }

    protected function _addServices()
    {
        Registry::set('request', new Request());
        Registry::set('response', new Response());
    }
}