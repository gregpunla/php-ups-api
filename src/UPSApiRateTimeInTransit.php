<?php

namespace Ups;

use DOMDocument;
use DOMElement;
use Exception;
use SimpleXMLElement;
use \Ups\Entity\RateRequest;
use \Ups\Entity\RateResponse;
use \Ups\Entity\Shipment;
use \Ups\UPSApiRate;

class UPSApiRateTimeInTransit extends UPSApiRate {

    /**
     * @param $rateRequest
     *
     * @throws Exception
     *
     * @return RateResponse
     */
    public function getRateTimeInTransit($rateRequest)
    {
        if ($rateRequest instanceof Shipment) {
            $shipment = $rateRequest;
            $rateRequest = new RateRequest();

            $pickuptype = new \Ups\Entity\PickupType;
            $pickuptype->setCode('03');
            $rateRequest->setPickupType($pickuptype);

            $customer_classification = new \Ups\Entity\CustomerClassification;
            $customer_classification->setCode('00');
            $rateRequest->setCustomerClassification($customer_classification);

            $rateRequest->setShipment($shipment);
        }

        $this->requestOption = 'Ratetimeintransit';

        return $this->sendRequest($rateRequest);
    }

    /**
     * @param $rateRequest
     *
     * @throws Exception
     *
     * @return RateResponse
     */
    public function shopRatesTimeInTransit($rateRequest)
    {
        if ($rateRequest instanceof Shipment) {
            $shipment = $rateRequest;
            $rateRequest = new RateRequest();
            
            $pickuptype = new \Ups\Entity\PickupType;
            $pickuptype->setCode('03');
            $rateRequest->setPickupType($pickuptype);

            $customer_classification = new \Ups\Entity\CustomerClassification;
            $customer_classification->setCode('00');
            $rateRequest->setCustomerClassification($customer_classification);

            $rateRequest->setShipment($shipment);
        }

        $this->requestOption = 'Shoptimeintransit';

        return $this->sendUpsRequest($rateRequest);
    }
}