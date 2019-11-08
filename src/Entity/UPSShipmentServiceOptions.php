<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;

use \Ups\Entity\ShipmentServiceOptions;

class UPSShipmentServiceOptions extends ShipmentServiceOptions
{
  /**
   * @var
   */
  public $SaturdayPickup;

  /**
   * @var
   */
  public $SaturdayDelivery;

  /**
   * @var
   */
  public $COD;

  /**
   * @var CallTagARS
   */
  public $CallTagARS;

  /**
   * @var
   */
  public $NegotiatedRatesIndicator;

  /**
   * @var
   */
  public $DirectDeliveryOnlyIndicator;

  /**
   * @var
   */
  public $DeliverToAddresseeOnlyIndicator;

  /**
   * @var
   */
  private $internationalForms;

  /**
   * @var null|LabelMethod
   */
  private $labelMethod;

  /**
   * @var array
   */
  private $notifications = [];

  /**
   * @var AccessPointCOD
   */
  private $accessPointCOD;

  /**
   * @var boolean
   */
  private $importControlIndicator;

  /**
   * @var DeliveryConfirmation
   */
  private $deliveryConfirmation;

  private $UPScarbonneutralIndicator;

  private $invoiceForm;

  private $packingListForm;

  private $premiumForms;

  private $returnService;

  /**
   * @param null $response
   */
  public function __construct($response = null)
  {
      $this->CallTagARS = new \Ups\Entity\CallTagARS();

      if (null !== $response) {
          if (isset($response->SaturdayPickup)) {
              $this->SaturdayPickup = $response->SaturdayPickup;
          }
          if (isset($response->SaturdayDelivery)) {
              $this->SaturdayDelivery = $response->SaturdayDelivery;
          }
          if (isset($response->COD)) {
              $this->COD = $response->COD;
          }
          if (isset($response->AccessPointCOD)) {
              $this->setAccessPointCOD(new \Ups\Entity\AccessPointCOD($response->AccessPointCOD));
          }
          if (isset($response->CallTagARS)) {
              $this->CallTagARS = new \Ups\Entity\CallTagARS($response->CallTagARS);
          }
          if (isset($response->NegotiatedRatesIndicator)) {
              $this->NegotiatedRatesIndicator = $response->NegotiatedRatesIndicator;
          }
          if (isset($response->DirectDeliveryOnlyIndicator)) {
              $this->DirectDeliveryOnlyIndicator = $response->DirectDeliveryOnlyIndicator;
          }
          if (isset($response->DeliverToAddresseeOnlyIndicator)) {
              $this->DeliverToAddresseeOnlyIndicator = $response->DeliverToAddresseeOnlyIndicator;
          }
          if (isset($response->InternationalForms)) {
              $this->setInternationalForms($response->InternationalForms);
          }
          if (isset($response->ImportControlIndicator)) {
              $this->setImportControlIndicator($response->ImportControlIndicator);
          }
          if (isset($response->DeliveryConfirmation)) {
              $this->setDeliveryConfirmation($response->DeliveryConfirmation);
          }
          if (isset($response->LabelMethod)) {
              $this->setLabelMethod(new LabelMethod($response->LabelMethod));
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

      $node = $document->createElement('ShipmentServiceOptions');

      if (isset($this->DirectDeliveryOnlyIndicator)) {
          $node->appendChild($document->createElement('DirectDeliveryOnlyIndicator'));
      }

      if (isset($this->DeliverToAddresseeOnlyIndicator)) {
          $node->appendChild($document->createElement('DeliverToAddresseeOnlyIndicator'));
      }

      if (isset($this->SaturdayPickup)) {
          $node->appendChild($document->createElement('SaturdayPickup'));
      }

      if (isset($this->SaturdayDelivery)) {
          $node->appendChild($document->createElement('SaturdayDelivery'));
      }

      if ($this->getCOD()) {
          $node->appendChild($this->getCOD()->toNode($document));
      }

      if ($this->getAccessPointCOD()) {
          $node->appendChild($this->getAccessPointCOD()->toNode($document));
      }

      if (isset($this->internationalForms)) {
          $node->appendChild($this->internationalForms->toNode($document));
      }

      if (isset($this->premiumForms)) {
        $node->appendChild($this->premiumForms->toNode($document));
      }

      if (isset($this->invoiceForm)) {
        $node->appendChild($this->invoiceForm->toNode($document));
      }

      if (isset($this->packingListForm)) {
        $node->appendChild($this->packingListForm->toNode($document));
      }

      if (isset($this->deliveryConfirmation)) {
          $node->appendChild($this->deliveryConfirmation->toNode($document));
      }

      if (isset($this->importControlIndicator)) {
          $node->appendChild($document->createElement('ImportControlIndicator'));
      }

      if (isset($this->labelMethod)) {
          $node->appendChild($this->labelMethod->toNode($document));
      }

      if (!empty($this->notifications)) {
          foreach ($this->notifications as $notification) {
              $node->appendChild($notification->toNode($document));
          }
      }

      if ($this->getUPScarbonneutralIndicator()) {
        $node->appendChild($document->createElement('UPScarbonneutralIndicator'));
      }

      if ($this->getReturnService()) {
        $node->appendChild($this->getReturnService()->toNode($document));
      }

      return $node;
  }

  public function setUPScarbonneutralIndicator($UPScarbonneutralIndicator)
  {
    $this->UPScarbonneutralIndicator = $UPScarbonneutralIndicator;
    return $this;
  }

  public function getUPScarbonneutralIndicator()
  {
    return $this->UPScarbonneutralIndicator;
  }

  public function setInvoiceForms(\Ups\Entity\UPSInvoiceForm $invoiceForm)
  {
    $this->invoiceForm = $invoiceForm;
    return $this;
  }

  public function setPackingListForms(\Ups\Entity\UPSPackingListForm $packingListForm)
  {
    $this->packingListForm = $packingListForm;
    return $this;
  }

  public function getPackingListForm()
  {
    return $this->packingListForm;
  }

  public function getInvoiceForm()
  {
    return $this->invoiceForm;
  }

  public function setPremiumForms(\Ups\Entity\UPSPremiumForms $premiumForms)
  {
    $this->premiumForms = $premiumForms;
    return $this;
  }

  public function getPremiumForms()
  {
    return $this->premiumForms;
  }

  public function setReturnService(\Ups\Entity\ReturnService $returnService)
  {
    $this->returnService = $returnService;
    return $this;
  }

  public function getReturnService()
  {
    return $this->returnService;
  }
}