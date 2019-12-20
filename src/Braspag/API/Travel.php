<?php
namespace Braspag\API;

class Travel implements \JsonSerializable
{

    private $journeyType;

    private $departureTime;

    private $passengers = [];

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
        $this->journeyType = isset($data->JourneyType)? $data->JourneyType: null;
        $this->departureTime = isset($data->DepartureTime)? $data->DepartureTime: null;

        if (isset($data->Passengers) and is_array($data->Passengers)) {
           foreach($data->Passengers as $passenger){
             $travelPassenger = $this->passenger();
             $travelPassenger->populate($passenger);
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

    public function passenger()
    {
        $passenger = new Passenger();

        $this->passengers[] = $passenger;

        return $passenger;
    }

}
