<?php

use Flowwow\Traits\ConfigurableTrait;
use Klein\AbstractResponse;
use Klein\DataCollection\HeaderDataCollection;
use Klein\Exceptions\ResponseAlreadySentException;

class Response extends AbstractResponse
{
    use ConfigurableTrait;

    const SUCCESS_CODE         = 1;
    const FAILED_CODE          = 0;
    const STATUS_FIELD         = 'status';
    const RESULT_FIELD         = 'result';
    const MESSAGE_FIELD        = 'description';
    const LINE_FIELD           = 'line';
    const TRACE_FIELD          = 'trace';
    const EXTRA_FIELD          = 'extra';
    const DEFAULT_SUCCESS_CODE = 200;
    const DEFAULT_FAILED_CODE  = 422;

    /** @var array|stdClass|CModel|null $data */
    protected $data = [];
    /** @var int|null $encode_options */
    protected $encode_options = JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK;

    /** @var string $protocol_version */

    public function init()
    {
        $this->setHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Формирует ответ с ошибкой
     * @param Throwable $e
     */
    public function failed(Throwable $e)
    {
        $response = [
            self::STATUS_FIELD  => self::FAILED_CODE,
            self::MESSAGE_FIELD => $e->getMessage()
        ];
        if (!hServer::isLive()) {
            $traces = [];
            foreach ($e->getTrace() as $trace) {
                $traces[] = [
                    "{$trace['file']} ({$trace['line']})",
                    "{$trace['class']}{$trace['type']}{$trace['function']}()"
                ];
            }
            $response = array_merge($response, [
                self::LINE_FIELD  => "{$e->getFile()} ({$e->getLine()})",
                self::TRACE_FIELD => $traces
            ]);
        }
        if (method_exists($e, 'getExtra') && !empty($e->getExtra())) {
            $response[self::EXTRA_FIELD] = $e->getExtra();
        }
        $code       = $e->getCode() != 0 ? $e->getCode() : self::DEFAULT_FAILED_CODE;
        $this->data = $response;
        $this->setStatus($code);
        $this->send();
    }

    /**
     * Формирует успешный ответ
     */
    public function success()
    {
        $this->send();
    }

    /**
     * Отправляет ответ
     * @param bool $override
     * @return AbstractResponse
     * @throws ResponseAlreadySentException
     */
    public function send($override = false)
    {
        if ($this->sent && !$override) {
            throw new ResponseAlreadySentException('Response has already been sent');
        }
        // Send our response data
        $this->sendHeaders();

        //cardsmobile так попросили
        if ($this->getData() !== null) {
            $this->body = json_encode($this->getData(), $this->getEncodeOptions());
            $this->sendBody();
        }

        // Lock the response from further modification
        $this->lock();

        // Mark as sent
        $this->sent = true;

        // If there running FPM, tell the process manager to finish the server request/response handling
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        return $this;
    }

    /**
     * @return HeaderDataCollection
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array|null $headers
     * @return Response $this;
     */
    public function setHeaders(?array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return array|CModel|null|stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|CModel|null|stdClass $data
     * @return Response $this;
     */
    public function setData($data = null): self
    {
        if ($data instanceof CModel) {
            $data = $data->getAttributes();
        } elseif ($data instanceof stdClass) {
            $data = get_object_vars($data);
        }
        $this->data = $data;

        return $this;
    }

    /**
     * @return int
     */
    public function getEncodeOptions(): int
    {
        return $this->encode_options;
    }

    /**
     * @param int|null $encode_options
     * @return Response $this;
     */
    public function setEncodeOptions(int $encode_options = null): self
    {
        $this->encode_options = $encode_options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param mixed $cookies
     * @return Response $this;
     */
    public function setCookies($cookies): self
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol_version;
    }

    /**
     * @param string $protocol_version
     * @return Response $this;
     */
    public function setProtocolVersion(string $protocol_version): self
    {
        $this->protocol_version = $protocol_version;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Response $this;
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}