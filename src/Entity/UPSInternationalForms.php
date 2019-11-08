<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;

use \Ups\Entity\InternationalForms;

class UPSInternationalForms extends InternationalForms
{
  /**
   * @var array
   */
  private $types = [
      self::TYPE_INVOICE
  ];

  /**
   * Form Types.
   */
  const TYPE_INVOICE = '01';
  const TYPE_CO = '03';
  const TYPE_NAFTA_CO = '04';
  const TYPE_PARTIAL_INVOICE = '05';
  const TYPE_PACKINGLIST = '06';
  const TYPE_CUSTOMER_GENERATED_FORMS = '07';
  const TYPE_AIR_FREIGHT_PACKING_LIST = '08';
  const TYPE_CN22_FORMS = '09';
  const TYPE_UPS_PREMIUM_CARE = '10';
  const TYPE_EEI_SHIPMENT_WITH_RETURN_SERVICE = '11';

  private static $typeNames = [
      '01' => 'Invoice',
      '03' => 'CO',
      '04' => 'NAFTA CO',
      '05' => 'Partial Invoice',
      '06' => 'Packinglist',
      '07' => 'Customer Generated Forms',
      '08' => 'Air Freight Packing List',
      '09' => 'CN22 Forms',
      '10' => 'UPS Premium Care',
      '11' => 'EEI. For shipment with return service',
  ];

  /**
   * @var string
   */
  private $termsOfShipment;

  /**
   * Terms of Shipment.
   */
  const TOS_COST_AND_FREIGHT = 'CFR';
  const TOS_COST_INSURANCE_AND_FREIGHT = 'CIF';
  const TOS_CARRIAGE_AND_INSURANCE_PAID = 'CIP';
  const TOS_CARRIAGE_PAID_TO = 'CPT';
  const TOS_DELIVERED_AT_FRONTIER = 'DAF';
  const TOS_DELIVERY_DUTY_PAID = 'DDP';
  const TOS_DELIVERY_DUTY_UNPAID = 'DDU';
  const TOS_DELIVERED_EX_QUAY = 'DEQ';
  const TOS_DELIVERED_EX_SHIP = 'DES';
  const TOS_EX_WORKS = 'EXW';
  const TOS_FREE_ALONGSIDE_SHIP = 'FAS';
  const TOS_FREE_CARRIER = 'FCA';
  const TOS_FREE_ON_BOARD = 'FOB';

  private static $termsOfShipmentNames = [
      'CFR' => 'Cost and Freight',
      'CIF' => 'Cost, Insurance and Freight',
      'CIP' => 'Carriage and Insurance Paid',
      'CPT' => 'Carriage Paid To',
      'DAF' => 'Delivered at Frontier',
      'DDP' => 'Delivery Duty Paid',
      'DDU' => 'Delivery Duty Unpaid',
      'DEQ' => 'Delivered Ex Quay',
      'DES' => 'Delivered Ex Ship',
      'EXW' => 'Ex Works',
      'FAS' => 'Free Alongside Ship',
      'FCA' => 'Free Carrier',
      'FOB' => 'Free On Board',
  ];

  /**
   * @var string
   */
  private $reasonForExport;

  /**
   * Reasons for export.
   */
  const RFE_SALE = 'SALE';
  const RFE_GIFT = 'GIFT';
  const RFE_SAMPLE = 'SAMPLE';
  const RFE_RETURN = 'RETURN';
  const RFE_REPAIR = 'REPAIR';
  const RFE_INTERCOMPANYDATA = 'INTERCOMPANYDATA';

  /**
   * @var string
   */
  private $comments;

  /**
   * @var string
   */
  private $declarationStatement;

  /**
   * @var string
   */
  private $currencyCode;

  /**
   * @var string
   */
  private $invoiceNumber;

  /**
   * @var DateTime
   */
  private $invoiceDate;

  /**
   * @var string
   */
  private $purchaseOrderNumber;

  /**
   * @var array
   */
  private $products = [];

  /**
   * @var Discount
   */
  private $discount;

  /**
   * @var FreightCharges
   */
  private $freightCharges;

  /**
   * @var bool
   */
  private $additionalDocumentIndicator;

  /**
   * @var EEIFilingOption
   */
  private $eeiFilingOption;

  private $hasUpsPremium;

  private $shipDate;

  /**
   * @return array
   */
  public static function getFormTypes()
  {
      return self::$typeNames;
  }

  /**
   * @param string $type
   *
   * @return string
   */
  public function getFormTypeName($type)
  {
      return self::$typeNames[$type];
  }

  /**
   * @param null|object $attributes
   */
  public function __construct($attributes = null)
  {
      if (null !== $attributes) {
          if (isset($attributes->FormType)) {
              $this->setType($attributes->FormType);
          }
          if (isset($attributes->InvoiceNumber)) {
              $this->setInvoiceNumber($attributes->InvoiceNumber);
          }
          if (isset($attributes->InvoiceDate)) {
              $this->setInvoiceDate(new DateTime($attributes->InvoiceDate));
          }
          if (isset($attributes->PurchaseOrderNumber)) {
              $this->setPurchaseOrderNumber($attributes->PurchaseOrderNumber);
          }
          if (isset($attributes->TermsOfShipment)) {
              $this->setTermsOfShipment($attributes->TermsOfShipment);
          }
          if (isset($attributes->Comments)) {
              $this->setComments($attributes->Comments);
          }
          if (isset($attributes->DeclarationStatement)) {
              $this->setDeclarationStatement($attributes->DeclarationStatement);
          }
          if (isset($attributes->CurrencyCode)) {
              $this->setCurrencyCode($attributes->CurrencyCode);
          }
          if (isset($attributes->EEIFilingOption)) {
              $this->setEEIFilingOption(new EEIFilingOption($attributes->EEIFilingOption));
          }
      }
  }

  /**
   * @param null|DOMDocument $document
   *
   * @return DOMElement
   */
  public function toNode(DOMDocument $document = null)
  {
      if (null === $document) {
          $document = new DOMDocument();
      }

      $node = $document->createElement('InternationalForms');

      foreach ($this->getTypes() as $type) {
          $node->appendChild($document->createElement('FormType', $type));
      }
      if ($this->getInvoiceNumber() !== null) {
          $node->appendChild($document->createElement('InvoiceNumber', $this->getInvoiceNumber()));
      }
      if ($this->getInvoiceDate() !== null) {
          $node->appendChild($document->createElement('InvoiceDate', $this->getInvoiceDate()->format('Ymd')));
      }
      if ($this->getPurchaseOrderNumber() !== null) {
          $node->appendChild($document->createElement('PurchaseOrderNumber', $this->getPurchaseOrderNumber()));
      }
      if ($this->getTermsOfShipment() !== null) {
          $node->appendChild($document->createElement('TermsOfShipment', $this->getTermsOfShipment()));
      }
      if ($this->getReasonForExport() !== null) {
          $node->appendChild($document->createElement('ReasonForExport', $this->getReasonForExport()));
      }
      if ($this->getComments() !== null) {
          $node->appendChild($document->createElement('Comments', $this->getComments()));
      }
      if ($this->getDeclarationStatement() !== null) {
          $node->appendChild($document->createElement('DeclarationStatement', $this->getDeclarationStatement()));
      }
      if ($this->getCurrencyCode() !== null) {
          $node->appendChild($document->createElement('CurrencyCode', $this->getCurrencyCode()));
      }
      if ($this->getDiscount() !== null) {
          $node->appendChild($this->getDiscount()->toNode($document));
      }
      if ($this->getFreightCharges() !== null) {
          $node->appendChild($this->getFreightCharges()->toNode($document));
      }
      if ($this->getAdditionalDocumentIndicator() !== null) {
          $node->appendChild($document->createElement('AdditionalDocumentIndicator'));
      }
      if ($this->getEEIFilingOption() !== null) {
          $node->appendChild($this->getEEIFilingOption()->toNode($document));
      }
      foreach ($this->products as $product) {
          $node->appendChild($product->toNode($document));
      }

      if ($this->getHasUpsPremium()) {
        $upsPremium = $document->createElement('UPSPremiumCareForm');
        $upsPremium->appendChild($document->createElement('ShipmentDate', $this->getShipmentDate()));
        $upsPremium->appendChild($document->createElement('PageSize', '01'));
        $upsPremium->appendChild($document->createElement('PrintType', '02'));
        $upsPremium->appendChild($document->createElement('NumOfCopies', '02'));
        
        $LanguageForUPSPremiumCare = $document->createElement('LanguageForUPSPremiumCare');
        $LanguageForUPSPremiumCare->appendChild($document->createElement('Language', 'eng'));
        $LanguageForUPSPremiumCare->appendChild($document->createElement('Language', 'fra'));
        $upsPremium->appendChild($LanguageForUPSPremiumCare);
        $node->appendChild($upsPremium);
      }

      return $node;
  }

  public function setHasUpsPremium($hasUpsPremium)
  {
    $this->hasUpsPremium = $hasUpsPremium;
    return $this;
  }

  public function getHasUpsPremium()
  {
    return $this->hasUpsPremium;
  }

  public function setShipmentDate($shipDate)
  {
    $this->shipDate = $shipDate;
    return $this;
  }

  public function getShipmentDate()
  {
    return $this->shipDate;
  }
}