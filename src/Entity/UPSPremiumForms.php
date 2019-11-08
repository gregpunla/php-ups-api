<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;

class UPSPremiumForms
{
  const TYPE_UPS_PREMIUM_CARE = '10';
  private $data = [];
  private $hasUpsPremium;
  private $shipDate;

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
    $node->appendChild($document->createElement('FormType', UPSPremiumForms::TYPE_UPS_PREMIUM_CARE));

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