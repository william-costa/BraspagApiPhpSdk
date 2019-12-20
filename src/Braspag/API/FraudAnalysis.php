<?php
namespace Braspag\API;

class FraudAnalysis implements \JsonSerializable
{

    private $sequence;

    private $sequenceCriteria;

    private $provider;

    private $captureOnLowRisk;

    private $voidOnHighRisk;

    private $totalOrderAmount;

    private $fingerPrintId;

    private $browser;

    private $cart;

    private $merchantDefinedFields = [];

    private $shipping;

    private $travel;

    private $id;

    private $status;

    private $fraudAnalysisReasonCode;

    private $replyData;

    public function __construct($provider = null)
    {
        $this->setProvider($provider);
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function populate(\stdClass $data)
    {
        $this->sequence = isset($data->Sequence)? $data->Sequence: null;
        $this->sequenceCriteria = isset($data->SequenceCriteria)? $data->SequenceCriteria: null;
        $this->provider = isset($data->Provider)? $data->Provider: null;
        $this->captureOnLowRisk = isset($data->CaptureOnLowRisk)? $data->CaptureOnLowRisk: null;
        $this->voidOnHighRisk = isset($data->VoidOnHighRisk)? $data->VoidOnHighRisk: null;
        $this->totalOrderAmount = isset($data->TotalOrderAmount)? $data->TotalOrderAmount: null;
        $this->fingerPrintId = isset($data->FingerPrintId)? $data->FingerPrintId: null;

        if (isset($data->Browser)) {
            $this->browser = new Browser();
            $this->browser->populate($data->Browser);
        }

        if (isset($data->Cart)) {
            $this->cart = new Cart();
            $this->cart->populate($data->Cart);
        }

        if (isset($data->Shipping)) {
            $this->shipping = new Shipping();
            $this->shipping->populate($data->Shipping);
        }

        if (isset($data->Travel)) {
            $this->travel = new Travel();
            $this->travel->populate($data->Travel);
        }

        if (isset($data->MerchantDefinedFields) and is_array($data->MerchantDefinedFields)) {
           foreach($data->MerchantDefinedFields as $field){
             $merchantDefinedField = $this->merchantDefinedField();
             $merchantDefinedField->populate($field);
           }
        }

        $this->id = isset($data->Id)? $data->Id: null;
        $this->status = isset($data->Status)? $data->Status: null;
        $this->fraudAnalysisReasonCode = isset($data->FraudAnalysisReasonCode)? $data->FraudAnalysisReasonCode: null;

        if (isset($data->ReplyData)) {
            $this->replyData = new ReplyData();
            $this->replyData->populate($data->ReplyData);
        }
    }

    public function getSequence()
    {
        return $this->sequence;
    }

    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
        return $this;
    }

    public function getSequenceCriteria()
    {
        return $this->sequenceCriteria;
    }

    public function setSequenceCriteria($sequenceCriteria)
    {
        $this->sequenceCriteria = $sequenceCriteria;
        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    public function getCaptureOnLowRisk()
    {
        return $this->captureOnLowRisk;
    }

    public function setCaptureOnLowRisk($captureOnLowRisk)
    {
        $this->captureOnLowRisk = $captureOnLowRisk;
        return $this;
    }

    public function getVoidOnHighRisk()
    {
        return $this->voidOnHighRisk;
    }

    public function setVoidOnHighRisk($voidOnHighRisk)
    {
        $this->voidOnHighRisk = $voidOnHighRisk;
        return $this;
    }

    public function getTotalOrderAmount()
    {
        return $this->totalOrderAmount;
    }

    public function setTotalOrderAmount($totalOrderAmount)
    {
        $this->totalOrderAmount = $totalOrderAmount;
        return $this;
    }

    public function getFingerPrintId()
    {
        return $this->fingerPrintId;
    }

    public function setFingerPrintId($fingerPrintId)
    {
        $this->fingerPrintId = $fingerPrintId;
        return $this;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function setBrowser($browser)
    {
        $this->browser = $browser;
        return $this;
    }

    public function browser()
    {
        $browser = new Browser();

        $this->setBrowser($browser);

        return $browser;
    }

    public function getCart()
    {
        return $this->cart;
    }

    public function setCart($cart)
    {
        $this->cart = $cart;
        return $this;
    }

    public function cart()
    {
        $cart = new Cart();

        $this->setCart($cart);

        return $cart;
    }

    public function getMerchantDefinedFields()
    {
        return $this->merchantDefinedFields;
    }

    public function setMerchantDefinedFields($merchantDefinedFields)
    {
        $this->merchantDefinedFields = $merchantDefinedFields;
        return $this;
    }

    public function merchantDefinedField()
    {
        $merchantDefinedField = new MerchantDefinedField();

        $this->merchantDefinedField[] = $merchantDefinedField;

        return $merchantDefinedField;
    }

    public function getShipping()
    {
        return $this->shipping;
    }

    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
        return $this;
    }

    public function shipping()
    {
        $shipping = new Shipping();

        $this->setShipping($shipping);

        return $shipping;
    }

    public function getTravel()
    {
        return $this->travel;
    }

    public function setTravel($travel)
    {
        $this->travel = $travel;
        return $this;
    }

    public function travel()
    {
        $travel = new Travel();

        $this->setTravel($travel);

        return $travel;
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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getFraudAnalysisReasonCode()
    {
        return $this->fraudAnalysisReasonCode;
    }

    public function setFraudAnalysisReasonCode($fraudAnalysisReasonCode)
    {
        $this->fraudAnalysisReasonCode = $fraudAnalysisReasonCode;
        return $this;
    }

    public function getReplyData()
    {
        return $this->replyData;
    }

    public function setReplyData($replyData)
    {
        $this->replyData = $replyData;
        return $this;
    }

    public function replyData()
    {
        $replyData = new ReplyData();

        $this->setReplyData($replyData);

        return $replyData;
    }
}
