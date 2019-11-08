<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;

use \Ups\Entity\Product;

class UPSProduct extends Product {
  /**
   * @var string
   */
  private $description1;

  /**
   * @var string
   */
  private $description2;

  /**
   * @var string
   */
  private $description3;

  /**
   * @var string
   */
  private $commodityCode;

  /**
   * @var string
   */
  private $partNumber;

  /**
   * @var string
   */
  private $originCountryCode;

  /**
   * @var Unit
   */
  private $unit;
  
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

    $node = $document->createElement('Product');
    for ($i = 1; $i <= 3; $i++) {
        $desc = $this->{'getDescription' . $i}();
        if ($desc !== null) {
            $node->appendChild($document->createElement('Description', $desc));
        }
    }
    if ($this->getUnit() !== null) {
        $node->appendChild($this->getUnit()->toNode($document));
    }
    if ($this->getCommodityCode() !== null) {
        $node->appendChild($document->createElement('CommodityCode', $this->getCommodityCode()));
    }
    if ($this->getPartNumber() !== null) {
        $node->appendChild($document->createElement('PartNumber', $this->getPartNumber()));
    }
    if ($this->getOriginCountryCode() !== null) {
        $node->appendChild($document->createElement('OriginCountryCode', $this->getOriginCountryCode()));
    }

    $UnitOfMeasurement = $document->createElement('UnitOfMeasurement');
    $UnitOfMeasurement->appendChild($document->createElement('Code', 'LBS'));
    $UnitOfMeasurement->appendChild($document->createElement('Description', 'LBS'));

    $ProductWeight = $document->createElement('ProductWeight');
    $ProductWeight->appendChild($document->createElement('Weight', '10'));
    $ProductWeight->appendChild($UnitOfMeasurement);

    $node->appendChild($ProductWeight);

    return $node;
  }
}