<?php
namespace Braspag\Api\Request;

class BraspagRequestException extends \Exception
{

    private $braspagError;

    public function __construct($message, $code, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getBraspagError()
    {
        return $this->braspagError;
    }

    public function setBraspagError(BraspagError $braspagError)
    {
        $this->braspagError = $braspagError;
        return $this;
    }
}