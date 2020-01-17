<?php

namespace Ups;

use \Ups\Entity\Shipment;
use \Ups\Entity\ShipmentTotalWeight;
use \Ups\Entity\UPSItemizedPaymentInformation;

class UPSApiShipment extends Shipment {

    private $shipmentTotalWeight;
    private $ratingMethodRequestIndicator;
    private $taxInformationIndicator;
    private $OriginRecordTransactionTimestamp;
    private $itemizedPaymentInformation;
    private $itemizedPaymentInformationTransportation;

    public function setShipmentTotalWeight(ShipmentTotalWeight $shipmentTotalWeight) {
        $this->shipmentTotalWeight = $shipmentTotalWeight;
    }

    public function getShipmentTotalWeight()
    {
        return $this->shipmentTotalWeight;
    }

    public function setRatingMethodRequestIndicator($value)
    {
        $this->ratingMethodRequestIndicator = $value;

        return $this;
    }

    public function getRatingMethodRequestIndicator()
    {
        return $this->ratingMethodRequestIndicator; 
    }

    public function setTaxInformationIndicator($value)
    {
        $this->taxInformationIndicator = $value;

        return $this;
    }

    public function getTaxInformationIndicator()
    {
        return $this->taxInformationIndicator; 
    }

    public function setItemizedPaymentInformation(UPSItemizedPaymentInformation $itemizedPaymentInformation)
    {
        $this->itemizedPaymentInformation = $itemizedPaymentInformation;
        return $this;
    }

    public function getItemizedPaymentInformation()
    {
        return $this->itemizedPaymentInformation;
    }

    public function setItemizedPaymentInformationTransportation(UPSItemizedPaymentInformation $itemizedPaymentInformationTransportation)
    {
        $this->itemizedPaymentInformationTransportation = $itemizedPaymentInformationTransportation;
        return $this;
    }

    public function getItemizedPaymentInformationTransportation()
    {
        return $this->itemizedPaymentInformationTransportation;
    }

}