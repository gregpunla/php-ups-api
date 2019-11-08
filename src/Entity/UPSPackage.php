<?php

namespace Ups\Entity;

use \Ups\Entity\Package;
use \Ups\Entity\PackageWeight;
use \Ups\Entity\PackagingType;
use \Ups\Entity\ReferenceNumber;
use \Ups\Entity\PackageServiceOptions;
use \Ups\Entity\Activity;
use \Ups\Entity\Dimensions;

use DOMDocument;
use DOMElement;
use \Ups\NodeInterface;

class UPSPackage extends Package {
  const PKG_OVERSIZE1 = '1';
  const PKG_OVERSIZE2 = '2';
  const PKG_LARGE = '4';

  /**
   * @var PackagingType
   */
  private $packagingType;

  /**
   * @var PackageWeight
   */
  private $packageWeight;

  /**
   * @var string
   */
  private $description;

  /**
   * @var PackageServiceOptions
   */
  private $packageServiceOptions;

  /**
   * @var string
   */
  private $upsPremiumCareIndicator;

  /**
   * @var ReferenceNumber
   */
  private $referenceNumber;
  
  /**
   * @var ReferenceNumber
   */
  private $referenceNumber2;

  /**
   * @var string
   */
  private $trackingNumber;

  /**
   * @var bool
   */
  private $isLargePackage;

  /**
   * @var bool
   */
  private $additionalHandling;

  /**
   * @var Dimensions|null
   */
  private $dimensions;

  /**
   * @var Activity[]
   */
  private $activities = [];

  private $dimWeight;

  private $dimWeightUnit;

  /**
   * @param null|object $attributes
   */
  public function __construct($attributes = null)
  {
      $this->setPackagingType(new PackagingType(
          isset($attributes->PackagingType) ? $attributes->PackagingType : null)
      );
      $this->setReferenceNumber(new ReferenceNumber());
      $this->setReferenceNumber2(new ReferenceNumber());
      $this->setPackageWeight(new PackageWeight());
      $this->setPackageServiceOptions(new PackageServiceOptions());

      if (null !== $attributes) {
          if (isset($attributes->PackageWeight)) {
              $this->setPackageWeight(new PackageWeight($attributes->PackageWeight));
          }
          if (isset($attributes->Description)) {
              $this->setDescription($attributes->Description);
          }
          if (isset($attributes->PackageServiceOptions)) {
              $this->setPackageServiceOptions(new PackageServiceOptions($attributes->PackageServiceOptions));
          }
          if (isset($attributes->UPSPremiumCareIndicator)) {
              $this->setUpsPremiumCareIndicator($attributes->UPSPremiumCareIndicator);
          }
          if (isset($attributes->ReferenceNumber)) {
              $this->setReferenceNumber(new ReferenceNumber($attributes->ReferenceNumber));
          }
          if (isset($attributes->ReferenceNumber2)) {
              $this->setReferenceNumber2(new ReferenceNumber($attributes->ReferenceNumber2));
          }
          if (isset($attributes->TrackingNumber)) {
              $this->setTrackingNumber($attributes->TrackingNumber);
          }
          if (isset($attributes->LargePackage)) {
              $this->setLargePackage($attributes->LargePackage);
          }
          if (isset($attributes->Dimensions)) {
              $this->setDimensions(new Dimensions($attributes->Dimensions));
          }
          if (isset($attributes->Activity)) {
              $activities = $this->getActivities();
              if (is_array($attributes->Activity)) {
                  foreach ($attributes->Activity as $Activity) {
                      $activities[] = new Activity($Activity);
                  }
              } else {
                  $activities[] = new Activity($attributes->Activity);
              }
              $this->setActivities($activities);
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

      $packageNode = $document->createElement('Package');

      if ($this->getDescription()) {
          $packageNode->appendChild($document->createElement('Description', $this->getDescription()));
      }
      $packageNode->appendChild($this->getPackagingType()->toNode($document));
      $packageNode->appendChild($this->getPackageWeight()->toNode($document));

      if (null !== $this->getDimWEight()) {
        $dimWeight = $document->createElement('DimWeight');
        $dimWeight_unit_measure = $document->createElement('UnitOfMeasurement');
        $dimWeight_unit_measure->appendChild($document->createElement('Code', $this->getDimWeightUnit()));

        $dimWeight->appendChild($dimWeight_unit_measure);
        $dimWeight->appendChild($document->createElement('Weight', $this->getDimWEight()));

        $packageNode->appendChild($dimWeight);
      }


      if (null !== $this->getDimensions()) {
          $packageNode->appendChild($this->getDimensions()->toNode($document));
      }

      if ($this->isLargePackage()) {
          $packageNode->appendChild($document->createElement('LargePackageIndicator'));
      }

      if ($this->getAdditionalHandling()) {
          $packageNode->appendChild($document->createElement('AdditionalHandling'));
      }

      if ($this->getPackageServiceOptions()) {
          $packageNode->appendChild($this->getPackageServiceOptions()->toNode($document));
      }

      if ($this->getReferenceNumber()
          && !is_null($this->getReferenceNumber()->getCode())
          && !is_null($this->getReferenceNumber()->getValue())
      ) {
          $packageNode->appendChild($this->getReferenceNumber()->toNode($document));
      }
      
      if ($this->getReferenceNumber2()
          && !is_null($this->getReferenceNumber2()->getCode())
          && !is_null($this->getReferenceNumber2()->getValue())
      ) {
          $packageNode->appendChild($this->getReferenceNumber2()->toNode($document));
      }

      return $packageNode;
  }

  public function setDimWeight($dimWeight) {
    $this->dimWeight = $dimWeight;
    return $this;
  }

  public function getDimWEight() {
    return $this->dimWeight;
  }

  public function setDimWeightUnit($dimWeightUnit) {
    $this->dimWeightUnit = $dimWeightUnit;
    return $this;
  }

  public function getDimWeightUnit() {
    return $this->dimWeightUnit;
  }
}