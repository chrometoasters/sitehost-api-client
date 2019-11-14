<?php

namespace Chrometoaster\SiteHost\API;

class Response
{
    /**
     * @var bool
     */
    private $status = false;

    /**
     * @var string
     */
    private $msg = '';

    /**
     * @var array
     */
    private $data = [];


    /**
     * Response constructor.
     *
     * @param null $res
     */
    public function __construct($res = null)
    {
        if ($res) {
            $this->status = (bool) ($res->status ?? null);
            $this->msg    = (string) ($res->msg ?? null);
            $this->data   = (array) ($res->return ?? null);
        }
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }


    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->msg;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * @param string $item
     * @return mixed|null
     */
    public function getDataItem(string $item = '')
    {
        if ($this->data && mb_strlen(trim($item)) && array_key_exists($item, $this->data)) {
            return $this->data[$item];
        }

        return null;
    }


    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->status && $this->data;
    }
}
