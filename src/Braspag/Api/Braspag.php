<?php
namespace Braspag\Api;

use Braspag\Merchant;
use Braspag\Api\Request\CreateSaleRequest;
use Braspag\ApiRequest\QuerySaleRequest;
use Braspag\Api\Request\UpdateSaleRequest;

/**
 * The Braspag Ecommerce SDK front-end;
 */
class Braspag
{

    private $merchant;

    private $environment;

    /**
     * Create an instance of Braspag choosing the environment where the
     * requests will be send
     *
     * @param
     *            \Braspag\Merchant merchant
     *            The merchant credentials
     * @param
     *            \Braspag\Environment environment
     *            The environment: {@link Environment::production()} or
     *            {@link Environment::sandbox()}
     */
    public function __construct(Merchant $merchant, Environment $environment = null)
    {
        if ($environment == null) {
            $environment = Environment::production();
        }

        $this->merchant = $merchant;
        $this->environment = $environment;
    }

    /**
     * Send the Sale to be created and return the Sale with payment id and the status
     * returned by Braspag.
     *
     * @param \Braspag\Api\Sale $sale
     *            The preconfigured Sale
     * @return \Braspag\Api\Sale The Sale with authorization, tid, etc. returned by Braspag.
     * @throws BraspagRequestException if anything gets wrong.
     */
    public function createSale(Sale $sale)
    {
        $createSaleRequest = new CreateSaleRequest($this->merchant, $this->environment);

        return $createSaleRequest->execute($sale);
    }

    /**
     * Query a Sale on Braspag by paymentId
     *
     * @param string $paymentId
     *            The paymentId to be queried
     * @return \Braspag\Api\Sale The Sale with authorization, tid, etc. returned by Braspag.
     * @throws BraspagRequestException if anything gets wrong.
     */
    public function getSale($paymentId)
    {
        $querySaleRequest = new QuerySaleRequest($this->merchant, $this->environment);

        return $querySaleRequest->execute($paymentId);
    }

    /**
     * Cancel a Sale on Braspag by paymentId and speficying the amount
     *
     * @param string $paymentId
     *            The paymentId to be queried
     * @param integer $amount
     *            Order value in cents
     * @return \Braspag\Api\Sale The Sale with authorization, tid, etc. returned by Braspag.
     * @throws BraspagRequestException if anything gets wrong.
     */
    public function cancelSale($paymentId, $amount = null)
    {
        $updateSaleRequest = new UpdateSaleRequest('void', $this->merchant, $this->environment);

        $updateSaleRequest->setAmount($amount);

        return $updateSaleRequest->execute($paymentId);
    }

    /**
     * Capture a Sale on Braspag by paymentId and specifying the amount and the
     * serviceTaxAmount
     *
     * @param string $paymentId
     *            The paymentId to be captured
     * @param integer $amount
     *            Amount of the authorization to be captured
     * @param integer $serviceTaxAmount
     *            Amount of the authorization should be destined for the service
     *            charge
     * @return \Braspag\Api\Payment The captured Payment.
     *
     * @throws BraspagRequestException if anything gets wrong.
     */
    public function captureSale($paymentId, $amount = null, $serviceTaxAmount = null)
    {
        $updateSaleRequest = new UpdateSaleRequest('capture', $this->merchant, $this->environment);

        $updateSaleRequest->setAmount($amount);
        $updateSaleRequest->setServiceTaxAmount($serviceTaxAmount);

        return $updateSaleRequest->execute($paymentId);
    }
}