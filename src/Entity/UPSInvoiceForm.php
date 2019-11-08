<?php

namespace Ups\Entity;

use DOMDocument;
use DOMElement;

class UPSInvoiceForm
{
  const TYPE_INVOICE = '01';
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
      $node->appendChild($document->createElement('FormType', '01'));
      if ($data->return_service != 'true') {
        $node->appendChild($document->createElement('FormType', '06'));
        if ($form['nafta_form'] == '1' || $form['nafta_form'] == 'true') {
          $node->appendChild($document->createElement('FormType', '04'));
        }
      }

      $contact = $document->createElement('Contacts');

      $soldTo = $document->createElement('SoldTo');

      $soldTo->appendChild($document->createElement('Name', $data->sender_company_name));
      $soldTo->appendChild($document->createElement('AttentionName', $data->sender_name));

      $phone = $document->createElement('Phone');
      $phone->appendChild($document->createElement('Number', $data->sender_phone_number));

      $soldTo->appendChild($phone);
      $soldTo->appendChild($document->createElement('Option', '01'));

      $soldtoAddress = $document->createElement('Address');
      $soldtoAddress->appendChild($document->createElement('AddressLine', $data->sender_street_line_1));
      $soldtoAddress->appendChild($document->createElement('City', $data->sender_city));
      $soldtoAddress->appendChild($document->createElement('PostalCode', $data->sender_zip));
      $soldtoAddress->appendChild($document->createElement('CountryCode', $data->sender_country));

      $soldTo->appendChild($soldtoAddress);

      $contact->appendChild($soldTo);

      if ($form['nafta_form'] == '1' || $form['nafta_form'] == 'true') {
        $producer = $document->createElement('Producer');
        $producer->appendChild($document->createElement('Option', '01'));
        $contact->appendChild($producer);
      }

      $node->appendChild($contact);

      $packageWeight = 0;
      $packageWeightUnit = null;
      $packageCurrency = '';

      foreach($data->packages as $package) {
        $packageWeight += (double)$package['weight'];
        $packageWeightUnit = $package['weight_unit'];
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

        $products->appendChild($document->createElement('CommodityCode', $product['tariff_code']));

        $products->appendChild($document->createElement('OriginCountryCode', $product['country_of_origin']));
        $product_weight = $document->createElement('ProductWeight');

        $product_weight_measurement = $document->createElement('UnitOfMeasurement');
        $product_weight_measurement->appendChild($document->createElement('Code', $packageWeightUnit));
        $product_weight_measurement->appendChild($document->createElement('Description', $packageWeightUnit));

        $product_weight->appendChild($product_weight_measurement);
        $product_weight->appendChild($document->createElement('Weight', $packageWeight));

        $products->appendChild($product_weight);

        $packing_list_info = $document->createElement('PackingListInfo');
        $packing_associated = $document->createElement('PackageAssociated');

        $package_number = ((int)$product['package_number'] + 1);
        $packing_associated->appendChild($document->createElement('PackageNumber', $package_number));

        $packing_associated->appendChild($document->createElement('ProductAmount', $product['quantity']));
        $packing_list_info->appendChild($packing_associated);

        $products->appendChild($packing_list_info);

        if ($form['nafta_form'] == '1' || $form['nafta_form'] == 'true') {
          $netcostcode = ($product['net_code'] == '1' || $product['net_code'] == 'true') ? 'NC' : 'NO';
          $products->appendChild($document->createElement('NetCostCode', $netcostcode));

          if ($product['net_code'] == '1' || $product['net_code'] == 'true') {
            $netcostcode_daterange = $document->createElement('NetCostDateRange');

            $netcostcode_daterange->appendChild($document->createElement('BeginDate', str_replace("-", "", $product['net_code_start_date'])));
            $netcostcode_daterange->appendChild($document->createElement('EndDate', str_replace("-", "", $product['net_code_start_date'])));
            $products->appendChild($netcostcode_daterange);
          }

          $products->appendChild($document->createElement('PreferenceCriteria', $product['preference_citeria']));
          $products->appendChild($document->createElement('ProducerInfo', $product['producer']));
        }

        $node->appendChild($products);

        $packageCurrency = $product['currency'];
      }

      $node->appendChild($document->createElement('InvoiceNumber', $form['invoice_number']));
      $node->appendChild($document->createElement('InvoiceDate', date('Ymd')));

      $node->appendChild($document->createElement('PurchaseOrderNumber', $form['purchase_order_number']));

      $node->appendChild($document->createElement('TermsOfShipment', $form['terms_of_sale']));

      $node->appendChild($document->createElement('ReasonForExport', $data->reason_for_export));

      $node->appendChild($document->createElement('Comments', $form['additional_comments']));

      $node->appendChild($document->createElement('DeclarationStatement', $form['declaration_statement']));

      $blanket_period = $document->createElement('BlanketPeriod');
      $blanket_period->appendChild($document->createElement('BeginDate', '20180201'));
      $blanket_period->appendChild($document->createElement('EndDate', '20180215'));

      $node->appendChild($blanket_period);

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