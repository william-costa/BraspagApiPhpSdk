<?php
namespace Braspag\API;

class Item implements \JsonSerializable
{

    private $giftCategory;

    private $hostHedge;

    private $nonSensicalHedge;

    private $obscenitiesHedge;

    private $phoneHedge;

    private $name;

    private $quantity;

    private $sku;

    private $unitPrice;

    private $risk;

    private $timeHedge;

    private $type;

    private $velocityHedge;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
       $this->giftCategory = isset($data->GiftCategory)? $data->GiftCategory: null;
       $this->hostHedge = isset($data->HostHedge)? $data->HostHedge: null;
       $this->nonSensicalHedge = isset($data->NonSensicalHedge)? $data->NonSensicalHedge: null;
       $this->obscenitiesHedge = isset($data->ObscenitiesHedge)? $data->ObscenitiesHedge: null;
       $this->phoneHedge = isset($data->PhoneHedge)? $data->PhoneHedge: null;
       $this->name = isset($data->Name)? $data->Name: null;
       $this->quantity = isset($data->Quantity)? $data->Quantity: null;
       $this->sku = isset($data->Sku)? $data->Sku: null;
       $this->unitPrice = isset($data->UnitPrice)? $data->UnitPrice: null;
       $this->risk = isset($data->Risk)? $data->Risk: null;
       $this->timeHedge = isset($data->TimeHedge)? $data->TimeHedge: null;
       $this->type = isset($data->Type)? $data->Type: null;
       $this->velocityHedge = isset($data->VelocityHedge)? $data->VelocityHedge: null;
    }

    public function getGiftCategory()
    {
        return $this->giftCategory;
    }

    public function setGiftCategory($giftCategory)
    {
        $this->giftCategory = $giftCategory;
        return $this;
    }

    public function getHostHedge()
    {
        return $this->hostHedge;
    }

    public function setHostHedge($hostHedge)
    {
        $this->hostHedge = $hostHedge;
        return $this;
    }

    public function getNonSensicalHedge()
    {
        return $this->nonSensicalHedge;
    }

    public function setNonSensicalHedge($nonSensicalHedge)
    {
        $this->nonSensicalHedge = $nonSensicalHedge;
        return $this;
    }

    public function getObscenitiesHedge()
    {
        return $this->obscenitiesHedge;
    }

    public function setObscenitiesHedge($obscenitiesHedge)
    {
        $this->obscenitiesHedge = $obscenitiesHedge;
        return $this;
    }

    public function getPhoneHedge()
    {
        return $this->phoneHedge;
    }

    public function setPhoneHedge($phoneHedge)
    {
        $this->phoneHedge = $phoneHedge;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getRisk()
    {
        return $this->risk;
    }

    public function setRisk($risk)
    {
        $this->risk = $risk;
        return $this;
    }

    public function getTimeHedge()
    {
        return $this->timeHedge;
    }

    public function setTimeHedge($timeHedge)
    {
        $this->timeHedge = $timeHedge;
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

    public function getVelocityHedge()
    {
        return $this->velocityHedge;
    }

    public function setVelocityHedge($velocityHedge)
    {
        $this->velocityHedge = $velocityHedge;
        return $this;
    }

}
