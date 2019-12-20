<?php
namespace Braspag\API;

class ReplyData implements \JsonSerializable
{
    private $addressInfoCode;

    private $factorCode;

    private $score;

    private $binCountry;

    private $cardIssuer;

    private $cardScheme;

    private $hostSeverity;

    private $internetInfoCode;

    private $ipRoutingMethod;

    private $scoreModelUsed;

    private $casePriority;

    private $providerTransactionId;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
        $this->addressInfoCode = isset($data->AddressInfoCode)? $data->AddressInfoCode: null;
        $this->factorCode = isset($data->FactorCode)? $data->FactorCode: null;
        $this->score = isset($data->Score)? $data->Score: null;
        $this->binCountry = isset($data->BinCountry)? $data->BinCountry: null;
        $this->cardIssuer = isset($data->CardIssuer)? $data->CardIssuer: null;
        $this->cardScheme = isset($data->CardScheme)? $data->CardScheme: null;
        $this->hostSeverity = isset($data->HostSeverity)? $data->HostSeverity: null;
        $this->internetInfoCode = isset($data->InternetInfoCode)? $data->InternetInfoCode: null;
        $this->ipRoutingMethod = isset($data->IpRoutingMethod)? $data->IpRoutingMethod: null;
        $this->scoreModelUsed = isset($data->ScoreModelUsed)? $data->ScoreModelUsed: null;
        $this->casePriority = isset($data->CasePriority)? $data->CasePriority: null;
        $this->providerTransactionId = isset($data->ProviderTransactionId)? $data->ProviderTransactionId: null;
    }

    public function getAddressInfoCode()
    {
        return $this->addressInfoCode;
    }

    public function setAddressInfoCode($addressInfoCode)
    {
        $this->addressInfoCode = $addressInfoCode;
        return $this;
    }

    public function getFactorCode()
    {
        return $this->factorCode;
    }

    public function setFactorCode($factorCode)
    {
        $this->factorCode = $factorCode;
        return $this;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    public function getBinCountry()
    {
        return $this->binCountry;
    }

    public function setBinCountry($binCountry)
    {
        $this->binCountry = $binCountry;
        return $this;
    }

    public function getCardIssuer()
    {
        return $this->cardIssuer;
    }

    public function setCardIssuer($cardIssuer)
    {
        $this->cardIssuer = $cardIssuer;
        return $this;
    }

    public function getCardScheme()
    {
        return $this->cardScheme;
    }

    public function setCardScheme($cardScheme)
    {
        $this->cardScheme = $cardScheme;
        return $this;
    }

    public function getHostSeverity()
    {
        return $this->hostSeverity;
    }

    public function setHostSeverity($hostSeverity)
    {
        $this->hostSeverity = $hostSeverity;
        return $this;
    }

    public function getInternetInfoCode()
    {
        return $this->internetInfoCode;
    }

    public function setInternetInfoCode($internetInfoCode)
    {
        $this->internetInfoCode = $internetInfoCode;
        return $this;
    }

    public function getIpRoutingMethod()
    {
        return $this->ipRoutingMethod;
    }

    public function setIpRoutingMethod($ipRoutingMethod)
    {
        $this->ipRoutingMethod = $ipRoutingMethod;
        return $this;
    }

    public function getScoreModelUsed()
    {
        return $this->scoreModelUsed;
    }

    public function setScoreModelUsed($scoreModelUsed)
    {
        $this->scoreModelUsed = $scoreModelUsed;
        return $this;
    }

    public function getCasePriority()
    {
        return $this->casePriority;
    }

    public function setCasePriority($casePriority)
    {
        $this->casePriority = $casePriority;
        return $this;
    }

    public function getProviderTransactionId()
    {
        return $this->providerTransactionId;
    }

    public function setProviderTransactionId($providerTransactionId)
    {
        $this->providerTransactionId = $providerTransactionId;
        return $this;
    }

}
