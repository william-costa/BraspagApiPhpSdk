<?php
namespace Braspag\API;

class MerchantDefinedField implements \JsonSerializable
{

    private $id;

    private $value;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
        $this->id = isset($data->Id)? $data->Id: null;
        $this->value = isset($data->Value)? $data->Value: null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

}
