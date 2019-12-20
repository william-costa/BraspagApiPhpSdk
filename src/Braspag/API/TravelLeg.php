<?php
namespace Braspag\API;

class TravelLeg implements \JsonSerializable
{

    private $origin;

    private $destination;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
        $this->origin = isset($data->Origin)? $data->Origin: null;
        $this->destination = isset($data->Destination)? $data->Destination: null;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function setOrigin($origin)
    {
        $this->origin = $origin;
        return $this;
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

}
