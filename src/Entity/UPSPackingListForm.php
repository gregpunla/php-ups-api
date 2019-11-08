<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;

class UPSPackingListForm
{
  const TYPE_PACKINGLIST = '06';
  private $data = [];

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

    $data = $this->getData();

    foreach ($data->international_invoice_forms as $form) {
      $node = $document->createElement('InternationalForms');
      $node->appendChild($document->createElement('FormType', self::TYPE_PACKINGLIST));

      $contact = $document->createElement('Contacts');

      $soldTo = $document->createElement('SoldTo');

      $soldTo->appendChild($document->createElement('Name', $data->sender_company_name));
      $soldTo->appendChild($document->createElement('AttentionName', $data->sender_name));

      $phone = $document->createElement('Phone');
      $phone->appendChild($document->createElement('Number', $data->sender_phone_number));
      $phone->appendChild($document->createElement('Extension', '1234')); // test

      $soldTo->appendChild($phone);
      $soldTo->appendChild($document->createElement('Option', '01'));

      $soldtoAddress = $document->createElement('Address');
      $soldtoAddress->appendChild($document->createElement('AddressLine', $data->sender_street_line_1));
      $soldtoAddress->appendChild($document->createElement('City', $data->sender_city));
      $soldtoAddress->appendChild($document->createElement('PostalCode', $data->sender_zip));
      $soldtoAddress->appendChild($document->createElement('CountryCode', $data->sender_country));

      $soldTo->appendChild($soldtoAddress);

      $contact->appendChild($soldTo);

      //$node->appendChild($contact);

      $packageWeight = 0;
      $packageWeightUnit = null;
      $packageCurrency = '';
      $product_count = count($form['products']);
      $package_count = 0;

      foreach($data->packages as $package) {
        $packageWeight += (double)$package['weight'];
        $packageWeightUnit = $package['weight_unit'];
        $package_count += 1;
      }

      foreach ($form['products'] as $product) {
        $products = $document->createElement('Product');

        $products->appendChild($document->createElement('Description', $product['description']));

        $product_unit = $document->createElement('Unit');
        $product_unit->appendChild($document->createElement('Number', $product['quantity']));

        $product_unit_measurement = $document->createElement('UnitOfMeasurement');
        $product_unit_measurement->appendChild($document->createElement('Code', $product['unit_of_measure']));
        $product_unit_measurement->appendChild($document->createElement('Description', $product['unit_of_measure']));

        $product_unit->appendChild($product_unit_measurement);
        $product_unit->appendChild($document->createElement('Value', $product['price_per_unit']));

        $products->appendChild($product_unit);

        if (strlen($product['tariff_code']) > 0) {
          $products->appendChild($document->createElement('CommodityCode', $product['tariff_code']));
        }

        $products->appendChild($document->createElement('OriginCountryCode', $product['country_of_origin']));
        $product_weight = $document->createElement('ProductWeight');

        $product_weight_measurement = $document->createElement('UnitOfMeasurement');
        $product_weight_measurement->appendChild($document->createElement('Code', $packageWeightUnit));
        $product_weight_measurement->appendChild($document->createElement('Description', $packageWeightUnit));

        $product_weight->appendChild($product_weight_measurement);
        $product_weight->appendChild($document->createElement('Weight', $packageWeight));

        $products->appendChild($product_weight);

        // $packing_list_info = $document->createElement('PackingListInfo');
        // $packing_associated = $document->createElement('PackageAssociated');
        // $packing_associated->appendChild($document->createElement('PackageNumber', 1));
        // $packing_associated->appendChild($document->createElement('ProductAmount', $product['quantity']));
        // $packing_list_info->appendChild($packing_associated);

        // $products->appendChild($packing_list_info);

        $node->appendChild($products);

        $packageCurrency = $product['currency'];
        //$product_count += 1;
      }

      $node->appendChild($document->createElement('InvoiceNumber', $form['invoice_number']));
      $node->appendChild($document->createElement('InvoiceDate', date('Ymd')));

      $node->appendChild($document->createElement('PurchaseOrderNumber', $form['purchase_order_number']));

      $node->appendChild($document->createElement('TermsOfShipment', $form['terms_of_sale']));

      $node->appendChild($document->createElement('ReasonForExport', $data->reason_for_export));

      $node->appendChild($document->createElement('Comments', $form['additional_comments']));

      $node->appendChild($document->createElement('DeclarationStatement', $form['declaration_statement']));

      $discount = $document->createElement('Discount');
      $discount->appendChild($document->createElement('MonetaryValue', ($form['discount']) ? $form['discount'] : '0'));

      $node->appendChild($discount);

      $freight = $document->createElement('FreightCharges');
      $freight->appendChild($document->createElement('MonetaryValue', ($form['freight_charges']) ? $form['freight_charges'] : '0'));

      $node->appendChild($freight);

      $insurance = $document->createElement('InsuranceCharges');
      $insurance->appendChild($document->createElement('MonetaryValue', ($form['insurance']) ? $form['insurance'] : '0'));

      $node->appendChild($insurance);

      $other_charges = $document->createElement('OtherCharges');
      $other_charges->appendChild($document->createElement('MonetaryValue', ($form['other_charges']) ? $form['other_charges'] : '0'));
      $other_charges->appendChild($document->createElement('Description', 'Misc'));

      $node->appendChild($other_charges);



      $node->appendChild($document->createElement('CurrencyCode', $packageCurrency));
    }

    return $node;
  }

  public function setData($data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }
}