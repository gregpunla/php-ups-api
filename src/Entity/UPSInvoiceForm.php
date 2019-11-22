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

      if (!isset($data->nafta_form)) {
          $data->nafta_form = false;
      }

      if (!isset($data->return_service)) {
          $data->return_service = false;
      }

      $node = $document->createElement('InternationalForms');
      $node->appendChild($document->createElement('FormType', '01'));
      if (!$data->return_service) { //TODO: if return is needed
          $node->appendChild($document->createElement('FormType', '06'));
          if ($data->nafta_form) { //TODO: if nafta form is needed
              $node->appendChild($document->createElement('FormType', '04'));
          }
      }

      $contact = $document->createElement('Contacts');

      $soldTo = $document->createElement('SoldTo');

      $soldTo->appendChild($document->createElement('Name', $data->fromAddress->contact_name));
      $soldTo->appendChild($document->createElement('AttentionName', $data->fromAddress->company_name));

      $phone = $document->createElement('Phone');
      $phone->appendChild($document->createElement('Number', $data->fromAddress->phone));

      $soldTo->appendChild($phone);
      $soldTo->appendChild($document->createElement('Option', '01'));

      $soldtoAddress = $document->createElement('Address');
      $soldtoAddress->appendChild($document->createElement('AddressLine', $data->fromAddress->stree_line_1));
      $soldtoAddress->appendChild($document->createElement('City', $data->fromAddress->city));
      $soldtoAddress->appendChild($document->createElement('PostalCode', $data->fromAddress->zip));
      $soldtoAddress->appendChild($document->createElement('CountryCode', $data->fromAddress->country));

      $soldTo->appendChild($soldtoAddress);

      $contact->appendChild($soldTo);

      if ($data->nafta_form) {
          $producer = $document->createElement('Producer');
          $producer->appendChild($document->createElement('Option', '01'));
          $contact->appendChild($producer);
      }

      $node->appendChild($contact);
      
      foreach ($data->packages as $key => $package) {
        $packageNumber = $key + 1;
      
        if($package->products->isEmpty()){
          throw \App\Exceptions\ErrorException::withCode('create.order.failed', 422, 'Each package should have at least one product');
        }

        foreach ($package->products as $product) {
            $products = $document->createElement('Product');

            $products->appendChild($document->createElement('Description', $product->description));

            $product_unit = $document->createElement('Unit');
            $product_unit->appendChild($document->createElement('Number', $product->quantity));

            $product_unit_measurement = $document->createElement('UnitOfMeasurement');
            $product_unit_measurement->appendChild($document->createElement('Code', $product->unit_of_measure));
            $product_unit_measurement->appendChild($document->createElement('Description', $product->unit_of_measure));

            $product_unit->appendChild($product_unit_measurement);
            $product_unit->appendChild($document->createElement('Value', $product->price_unit ?? 0));

            $products->appendChild($product_unit);

            $products->appendChild($document->createElement('CommodityCode', $product->tariff_code ?? null));
            $products->appendChild($document->createElement('OriginCountryCode', $product->country_of_origin));
            $product_weight = $document->createElement('ProductWeight');

            $product_weight_measurement = $document->createElement('UnitOfMeasurement');
            $product_weight_measurement->appendChild($document->createElement('Code', $package->weight_unit));
            $product_weight_measurement->appendChild($document->createElement('Description', $package->weight_unit));

            $product_weight->appendChild($product_weight_measurement);
            $product_weight->appendChild($document->createElement('Weight', $package->weight));

            $products->appendChild($product_weight);

            $packing_list_info = $document->createElement('PackingListInfo');
            $packing_associated = $document->createElement('PackageAssociated');

            $packing_associated->appendChild($document->createElement('PackageNumber', $packageNumber));

            $packing_associated->appendChild($document->createElement('ProductAmount', $product->quantity));
            $packing_list_info->appendChild($packing_associated);

            $products->appendChild($packing_list_info);

            if ($data->nafta_form) {
                $netcostcode = ('1' == $product->net_code || 'true' == $product->net_code) ? 'NC' : 'NO';
                $products->appendChild($document->createElement('NetCostCode', $netcostcode));

                if ('1' == $product->net_code || 'true' == $product->net_code) {
                    $netcostcode_daterange = $document->createElement('NetCostDateRange');

                    $netcostcode_daterange->appendChild($document->createElement('BeginDate', str_replace('-', '', $product->net_code_start_date)));
                    $netcostcode_daterange->appendChild($document->createElement('EndDate', str_replace('-', '', $product->net_code_start_date)));
                    $products->appendChild($netcostcode_daterange);
                }

                $products->appendChild($document->createElement('PreferenceCriteria', $product->preference_citeria));
                $products->appendChild($document->createElement('ProducerInfo', $product->producer));
            }

            $node->appendChild($products);

            $packageCurrency = $product->currency;
        }
      }

      $node->appendChild($document->createElement('InvoiceNumber', $data->invoice_number));
      $node->appendChild($document->createElement('InvoiceDate', date('Ymd')));
      $node->appendChild($document->createElement('PurchaseOrderNumber', $data->purchase_order_number));
      $node->appendChild($document->createElement('TermsOfShipment', $data->terms_of_sale));
      $node->appendChild($document->createElement('ReasonForExport', $data->shipment_purpose ?? 'sale'));
      $node->appendChild($document->createElement('Comments', $data->additional_comments));
      $node->appendChild($document->createElement('DeclarationStatement', $data->declaration_statement));

      $blanket_period = $document->createElement('BlanketPeriod');
      $blanket_period->appendChild($document->createElement('BeginDate', '20180201'));
      $blanket_period->appendChild($document->createElement('EndDate', '20180215'));

      $node->appendChild($blanket_period);
     
      $discount = $document->createElement('Discount');
      $discount->appendChild($document->createElement('MonetaryValue', ($data->discount_or_rebate) ? $data->discount_or_rebate : '0'));
     
      $node->appendChild($discount);
     
      $freight = $document->createElement('FreightCharges');
      $freight->appendChild($document->createElement('MonetaryValue', ($data->freight_charges) ? $data->freight_charges : '0'));
     
      $node->appendChild($freight);
     
      $insurance = $document->createElement('InsuranceCharges');
      $insurance->appendChild($document->createElement('MonetaryValue', ($data->insurance) ? $data->insurance : '0'));
     
      $node->appendChild($insurance);

      $other_charges = $document->createElement('OtherCharges');
      $other_charges->appendChild($document->createElement('MonetaryValue', ($data->other_charges) ? $data->other_charges : '0'));
      $other_charges->appendChild($document->createElement('Description', 'Misc'));

      $node->appendChild($other_charges);

      $node->appendChild($document->createElement('CurrencyCode', $packageCurrency));
      
      unset($data->nafta_form);
      unset($data->return_service);

      return $node;
  }

  public function setData($data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }
}