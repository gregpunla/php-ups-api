<?php
namespace Ups\Entity;

use DOMDocument;
use DOMElement;
use \Ups\Entity\BillShipper;
use \Ups\Entity\BillThirdParty;
use \Ups\Entity\FreightCollect;

class UPSItemizedPaymentInformation {
    const TYPE_TRANSPORTATION = '01';
    const TYPE_DUTIES_AND_TAXES = '02';
    const CHARGE_BILLSHIPPER = 'BillShipper';
    const CHARGE_THIRDPARTY = 'BillThirdParty';
    const CHARGE_DUTIES_AND_TAXES = 'BillReceiver';

    private $type;
    private $billShipper;
    private $billThirdParty;
    private $billReceiver;

    public function __construct($type = self::CHARGE_BILLSHIPPER, $attributes = null)
    {
        switch ($type) {
            case self::CHARGE_BILLSHIPPER:
                $this->billShipper = new BillShipper($attributes);
                break;
            case self::CHARGE_THIRDPARTY:
                $this->billThirdParty = new BillThirdParty($attributes);
                break;
            case self::CHARGE_DUTIES_AND_TAXES:
                $this->billReceiver = new FreightCollect($attributes);                
                break;
            default:
                throw new LogicException(sprintf('Unknown Itemized Payment Information type requested: "%s"', $type));
        }

        if ($attributes) {
            $this->type = $attributes->ShipmentChargeType;
        }
    }

    public function toNode(DOMDocument $document = null)
    {
        if (null === $document) {
            $document = new DOMDocument();
        }

        $shipmentCharge = $document->createElement('ShipmentCharge');
        $shipmentCharge->appendChild($document->createElement('Type', $this->getType()));
    
        if ($this->billShipper) 
        {
            $billShipper = $document->createElement('BillShipper');
            $billShipper->appendChild($document->createElement('AccountNumber', $this->billShipper->getAccountNumber()));   
            //$billShipper->appendChild($document->appendChild($this->billShipper->getBillShipperAddress()->toNode($document)));         
            $shipmentCharge->appendChild($billShipper);            
        }

        if ($this->billThirdParty) 
        {
            $billThirdParty = $document->createElement('BillThirdParty');
            if ($this->getType() == '01') {
                $billThirdPartyShipper = $document->createElement('BillThirdPartyShipper'); // BillThirdPartyConsignee
            } else {
                $billThirdPartyShipper = $document->createElement('BillThirdPartyConsignee'); // BillThirdPartyConsignee
            }
            
            $billThirdPartyShipper->appendChild($document->createElement('AccountNumber', $this->billThirdParty->getAccountNumber()));
            $thirdParty = $document->createElement('ThirdParty');
            $thirdParty->appendChild($document->appendChild($this->billThirdParty->getThirdPartyAddress()->toNode($document)));
            $billThirdPartyShipper->appendChild($thirdParty);
            $billThirdParty->appendChild($billThirdPartyShipper);
            $shipmentCharge->appendChild($billThirdParty);
        }

        if ($this->billReceiver) 
        {
            $billReceiver = $document->createElement('BillReceiver');
            $billReceiver->appendChild($document->createElement('AccountNumber', $this->billReceiver->getAccountNumber()));            
            $billReceiver->appendChild($document->appendChild($this->billReceiver->getBillReceiverAddress()->toNode($document)));
            $shipmentCharge->appendChild($billReceiver);
        }

        return $shipmentCharge;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

}