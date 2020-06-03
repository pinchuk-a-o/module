<?php

/**
 * Класс обертка для того чтобы пробросить ответ в контроллер
 * Class BaseAction
 */
abstract class BaseAction extends CAction
{
    public $middleware = [];

    /**
     * Вызываем с параметрами
     * @param array $params
     * @return bool|void
     * @throws Exception
     * @throws \Flowwow\Exceptions\InvalidConfigException
     */
    public function runWithParams($params)
    {
        $this->beforeMiddleware();
        /* @phan-suppress-next-line PhanTypeMismatchReturn, PhanTypeVoidAssignment */
        $data = $this->run();
        /* @phan-suppress-next-line PhanTypeMismatchArgument Argument */
        ServiceHelper::getResponse()->setData($data);
        $this->afterMiddleware();
    }

    /**
     * Выполняются посредники до экшена
     * @throws \Flowwow\Exceptions\InvalidConfigException
     */
    protected function beforeMiddleware()
    {
        /** @var MiddlewareInterface $middleware */
        foreach ($this->middleware as $middleware) {
            if (!is_subclass_of($middleware, MiddlewareInterface::class)) {
                throw new \Flowwow\Exceptions\InvalidConfigException('Посредник должен реализовывать MiddlewareInterface');
            }
            if ($middleware::direction() == MiddlewareInterface::BEFORE) {
                $middleware::run();
            }
        }
    }

    /**
     * Выполняются посредники после экшена
     * @throws \Flowwow\Exceptions\InvalidConfigException
     */
    protected function afterMiddleware()
    {
        /** @var MiddlewareInterface $middleware */
        foreach ($this->middleware as $middleware) {
            if (!is_subclass_of($middleware, MiddlewareInterface::class)) {
                throw new \Flowwow\Exceptions\InvalidConfigException('Посредник должен реализовывать MiddlewareInterface');
            }
            if ($middleware::direction() == MiddlewareInterface::AFTER) {
                $middleware::run();
            }
        }
    }

    /**
     * Исполняемый контектс
     * @return mixed
     */
    abstract public function run();
}