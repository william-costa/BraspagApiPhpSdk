<?php
namespace Braspag\API;

class Browser implements \JsonSerializable
{

    private $cookiesAccepted;

    private $email;

    private $hostName;

    private $ipAddress;

    private $type;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
      $this->cookiesAccepted = isset($data->CookiesAccepted)? $data->CookiesAccepted: null;
      $this->email = isset($data->Email)? $data->Email: null;
      $this->hostName = isset($data->HostName)? $data->HostName: null;
      $this->ipAddress = isset($data->IpAddress)? $data->IpAddress: null;
      $this->type = isset($data->Type)? $data->Type: null;
    }

    public function getCookiesAccepted()
    {
        return $this->cookiesAccepted;
    }

    public function setCookiesAccepted($cookiesAccepted)
    {
        $this->cookiesAccepted = $cookiesAccepted;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getHostName()
    {
        return $this->hostName;
    }

    public function setHostName($hostName)
    {
        $this->hostName = $hostName;
        return $this;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
