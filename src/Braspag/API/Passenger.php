<?php
namespace Braspag\API;

class Passenger implements \JsonSerializable
{

    private $name;

    private $identity;

    private $status;

    private $rating;

    private $email;

    private $phone;

    private $travelLegs = [];

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
       $this->name = isset($data->Name)? $data->Name: null;
       $this->identity = isset($data->Identity)? $data->Identity: null;
       $this->status = isset($data->Status)? $data->Status: null;
       $this->rating = isset($data->Rating)? $data->Rating: null;
       $this->email = isset($data->Email)? $data->Email: null;
       $this->phone = isset($data->Phone)? $data->Phone: null;

        if (isset($data->TravelLegs) and is_array($data->TravelLegs)) {
           foreach($data->TravelLegs as $value){
             $travelLeg = $this->travelLeg();
             $travelLeg->populate($value);
           }
        }
    }

    public function getJourneyType()
    {
        return $this->journeyType;
    }

    public function setJourneyType($journeyType)
    {
        $this->journeyType = $journeyType;
        return $this;
    }

    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;
        return $this;
    }

    public function getPassengers()
    {
        return $this->passengers;
    }

    public function setPassengers($passengers)
    {
        $this->passengers = $passengers;
        return $this;
    }

    public function travelLeg()
    {
        $travelLeg = new TravelLeg();

        $this->travelLegs[] = $travelLeg;

        return $travelLeg;
    }

}
