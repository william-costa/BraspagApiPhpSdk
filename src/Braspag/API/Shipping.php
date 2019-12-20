<?php
namespace Braspag\API;

class Shipping implements \JsonSerializable
{

    private $addressee;

    private $method;

    private $phone;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
       $this->addressee = isset($data->Addressee)? $data->Addressee: null;
       $this->method = isset($data->Method)? $data->Method: null;
       $this->phone = isset($data->Phone)? $data->Phone: null;
    }

    public function getAddressee()
    {
        return $this->addressee;
    }

    public function setAddressee($addressee)
    {
        $this->addressee = $addressee;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

}
