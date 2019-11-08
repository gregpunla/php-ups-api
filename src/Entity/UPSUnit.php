<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;

use \Ups\Entity\Unit;

class UPSUnit extends Unit {

  /**
   * @var string
   */
  private $number = 1;

  /**
   * @var string
   */
  private $value;

  /**
   * @var UnitOfMeasurement
   */
  private $unitOfMeasurement;


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

    $node = $document->createElement('Unit');
    $node->appendChild($document->createElement('Number', $this->getNumber()));
    $node->appendChild($document->createElement('Value', $this->getValue()));
    if ($this->getUnitOfMeasurement() !== null) {
      $node->appendChild($this->getUnitOfMeasurement()->toNode($document));
    }

    return $node;
  }

  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
  * @param $value
  *
  * @throws \Exception
  *
  * @return $this
  */
  public function setValue($value)
  {
    $this->value = number_format($value, 0, '.', '');

    if (strlen((string)$this->value) > 19) {
        throw new \Exception('Value too long');
    }

    return $this;
  }
}