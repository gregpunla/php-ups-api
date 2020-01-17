<?php

namespace Ups;

use DOMDocument;
use DOMElement;
use Exception;
use SimpleXMLElement;
use \Ups\Entity\RateRequest;
use \Ups\Entity\RateResponse;
use \Ups\Entity\Shipment;
use \Ups\Rate;

class UPSApiRate extends Rate {

    private function createUpsRequest(RateRequest $rateRequest)
    {
        $shipment = $rateRequest->getShipment();

        $document = $xml = new DOMDocument();
        $xml->formatOutput = true;

        /** @var DOMElement $trackRequest */
        $trackRequest = $xml->appendChild($xml->createElement('RatingServiceSelectionRequest'));
        $trackRequest->setAttribute('xml:lang', 'en-US');

        $request = $trackRequest->appendChild($xml->createElement('Request'));

        $node = $xml->importNode($this->createTransactionNode(), true);
        $request->appendChild($node);

        $request->appendChild($xml->createElement('RequestAction', 'Rate'));
        $request->appendChild($xml->createElement('RequestOption', $this->requestOption));
        $request->appendChild($xml->createElement('SubVersion', '1707'));

        $trackRequest->appendChild($rateRequest->getPickupType()->toNode($document));

        $customerClassification = $rateRequest->getCustomerClassification();
        if (isset($customerClassification)) {
            $trackRequest->appendChild($customerClassification->toNode($document));
        }

        $shipmentNode = $trackRequest->appendChild($xml->createElement('Shipment'));

        $requestDate = new \DateTime('America/New_York');
        $shipmentNode->appendChild($xml->createElement('OriginRecordTransactionTimestamp', $requestDate->format('Y-m-d') . $requestDate->format('H:i:s:v')));

        // Support specifying an individual service
        // $service = $shipment->getService();
        // if (isset($service)) {
        //     $shipmentNode->appendChild($service->toNode($document));
        // }

        $shipper = $shipment->getShipper();
        if (isset($shipper)) {
            $shipmentNode->appendChild($shipper->toNode($document));
        }

        $shipFrom = $shipment->getShipFrom();
        if (isset($shipFrom)) {
            $shipmentNode->appendChild($shipFrom->toNode($document));
        }

        $shipTo = $shipment->getShipTo();
        if (isset($shipTo)) {
            $shipmentNode->appendChild($shipTo->toNode($document));
        }

        $alternateDeliveryAddress = $shipment->getAlternateDeliveryAddress();
        if (isset($alternateDeliveryAddress)) {
            $shipmentNode->appendChild($alternateDeliveryAddress->toNode($document));
        }

        $rateInformation = $shipment->getRateInformation();
        if ($rateInformation !== null) {
            $shipmentNode->appendChild($rateInformation->toNode($document));
        }

        $shipmentIndicationType = $shipment->getShipmentIndicationType();
        if (isset($shipmentIndicationType)) {
            $shipmentNode->appendChild($shipmentIndicationType->toNode($document));
        }

        foreach ($shipment->getPackages() as $package) {
            $shipmentNode->appendChild($package->toNode($document));
        }

        $shipmentServiceOptions = $shipment->getShipmentServiceOptions();
        if (isset($shipmentServiceOptions)) {
            $shipmentNode->appendChild($shipmentServiceOptions->toNode($xml));
        }

        $deliveryTimeInformation = $shipment->getDeliveryTimeInformation();
        if (isset($deliveryTimeInformation)) {
            $shipmentNode->appendChild($deliveryTimeInformation->toNode($xml));
        }

        $shipmentTotalWeight = $shipment->getShipmentTotalWeight();
        if (isset($shipmentTotalWeight)) {
        	$shipmentNode->appendChild($shipmentTotalWeight->toNode($xml));
        }

        $ratingMethodRequestIndicator = $shipment->getRatingMethodRequestIndicator();
        if (isset($ratingMethodRequestIndicator)) {
            $shipmentNode->appendChild($document->createElement('RatingMethodRequestIndicator'));
        }

        $taxtInformationIndicator = $shipment->getTaxInformationIndicator();
        if (isset($taxtInformationIndicator)) {
            $shipmentNode->appendChild($document->createElement('TaxInformationIndicator'));
        }

        $invoiceLineTotal = $shipment->getInvoiceLineTotal();
        if (isset($invoiceLineTotal)) {
            $shipmentNode->appendChild($invoiceLineTotal->toNode($xml));
        }

        return $xml->saveXML();
    }

    /*
     * Creates and sends a request for the given shipment. This handles checking for
     * errors in the response back from UPS.
     *
     * @param RateRequest $rateRequest
     *
     * @throws Exception
     *
     * @return RateResponse
     */
    protected function sendUpsRequest(RateRequest $rateRequest)
    {
        $request = $this->createUpsRequest($rateRequest);

        $this->response = $this->getRequest()->request($this->createAccess(), $request, $this->compileEndpointUrl(self::ENDPOINT));
        $response = $this->response->getResponse();

        if (null === $response) {
            throw new Exception('Failure (0): Unknown error', 0);
        }

        if ($response->Response->ResponseStatusCode == 0) {
            throw new Exception(
                "Failure ({$response->Response->Error->ErrorSeverity}): {$response->Response->Error->ErrorDescription}",
                (int)$response->Response->Error->ErrorCode
            );
        } else {
            return $this->formatResponse($response);
        }
    }

    /**
     * Format the response.
     *
     * @param SimpleXMLElement $response
     *
     * @return RateResponse
     */
    private function formatResponse(SimpleXMLElement $response)
    {
        // We don't need to return data regarding the response to the user
        unset($response->Response);

        $result = $this->convertXmlObject($response);

        return new RateResponse($result);
    }

}