<?php
namespace Ups;

use Symfony\Component\DomCrawler\Crawler;

use App\Models\Order;

class UPSCrawler {

	CONST UPS_CTC_URL = 'https://wwwapps.ups.com/ctc/results';
	CONST USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';

	public function getLetterZones(Order $order, $request) {
		$fromAddress = ($order->exists) ? $order->FromAddress : null;
		$toAddress = ($order->exists) ? $order->ToAddress : null;

		$form_params = array(
			"timeOnlyRts" => "false",
			"ctcModPkgType" => "null",
			"ivrPkgType" => "null",
			"ctcModAccountFlag" => "show",
			"ctcModLoginStatus" => "null",
			"ctcModuleWeight" => "null",
			"ctcModuleWeightType" => "null",
			"importFlag" => "",
			"assume" => "",
			"rtsFlag" => "",
			"destCtyCurrency" => "",
			"destCtyDimUnit" => "",
			"destCtyUom" => "",
			"destCtyUomKey" => "",
			"afcity" => "null",
			"afpostal" => "null",
			"afcountry" => "null",
			"prefCity" => "null",
			"prefPostal" => "null",
			"prefcountry" => "null",
			"addressCountry" => "null",
			"userId" => "",
			"A_Resi" => "null",
			"isResidential" => "null",
			"addressDiffFromBook" => "NO",
			"addressBookCompanyOrName" => "",
			"addresseName" => "",
			"addressLine1" => "",
			"addressLine2" => "",
			"addressLine3" => "",
			"addressCity" => "null",
			"addressZip" => "null",
			"resComDet" => "null",
			"addressBookState" => "null",
			"requestor" => "",
			"taxIndicator" => "null",
			"DeSurInd" => "null",
			"AccNum" => "null",
			"returnURL" => "null",
			"page" => "accessorialModule",
			"loc" => "en_CA",
			"lanCancelURL" => "",
			"packageLetter" => "null",
			"selectedAccountNumber" => "",
			"selectedAccountClassification" => "null",
			"isSelectedAccountABREnabled" => "",
			"isSelectedAccountGBPalletEnabled" => "null",
			"accImsFlag" => "false",
			"accType" => "null",
			"accSelectedCountry" => "null",
			"jsDisabled" => "null",
			"isAccountSelected" => "null",
			"modDestResidetail" => "null",
			"destRes" => "null",
			"modWeightUnit" => "null",
			"modDimUnit" => "null",
			"modContainer" => "null",
			"modWeight" => "null",
			"modLength" => "null",
			"modWidth" => "null",
			"modHeight" => "null",
			"modDeclValue" => "null",
			"modDropOfChoice" => "null",
			"modPickUpMethod" => "null",
			"modDailyPickUp" => "null",
			"modValueAdded" => "null",
			"modPickUpMethod1" => "null",
			"modPickupAdded" => "null",
			"modRequestor" => "null",
			"modCustomValue" => "null",
			"modSameValue" => "null",
			"isModifyClicked" => "null",
			"modOrigCity" => "null",
			"modOrigZip" => "null",
			"modOrigCountry" => "null",
			"modDestCity" => "null",
			"modDestZip" => "null",
			"modDestCountry" => "null",
			"selectpackaging" => "null",
			"mypacking" => "My Packaging",
			"upsletter" => "UPS Envelope",
			"expressbox" => "UPS Express Box",
			"smallbox" => "UPS Express Box - Small",
			"mediumbox" => "UPS Express Box - Medium",
			"largebox" => "UPS Express Box - Large",
			"tube" => "UPS Tube",
			"pack" => "UPS Pak",
			"tenkg" => "UPS Worldwide Express 10KG Box",
			"twentyfivekg" => "UPS Worldwide Express 25KG Box",
			"palletPkgType" => "Pallet",
			"timeOnlyCountries" => "AS,AD,AI,AG,AM,AW,BB,BY,BZ,BJ,BT,BW,VG,BN,BF,KH,CV,CF,TD,CG,CK,DM,GQ,ER,FO,FJ,GF,PF,GA,GM,GE,GL,GD,GP,GU,GN,GW,GY,HT,IS,JM,KI,LA,LB,LS,LR,LI,MK,MG,MV,ML,MH,MQ,MR,FM,MC,MN,MS,MP,ME,NA,NP,AN,NC,NE,NF,PW,PG,RE,SM,SN,SC,SL,SB,KN,LC,VC,SR,SZ,SY,TJ,TG,TO,TT,TC,TV,UZ,VU,WF,WS,YE",
			"isOrigDestDutiable" => "true",
			"promoDiscountEligible" => "",
			"billableWeightIndicator" => "",
			"customerClassificationCode" => "",
			"abrEligible" => "",
			"useAcc" => "null",
			"modAccNumIn" => "null",
			"ctcModuleLogin" => "null",
			"quoteTypeQcc" => "estimateTimeCost.x",
			"origtype" => "",
			"datevalue" => "",
			"noofpackages" => "1",
			"quoteselected" => "estimateTimeCost.x",
			"nextclicked" => "next",
			"fromaddORcountry" => "",
			"itsQuickquote" => "no",
			"onChangeAccValue" => "",
			"quickQuoteTypePackageLetter" => "",
			"transitTimeSelected" => "",
			"shipmentTypeFreight" => "smallORPallet",
			"origCurrency" => "",
			"usPR" => ($request->sender_country == 'US' || $request->sender_country == 'PR') ? 'true' : '',
			"dismissLink" => "",
			"metricUnit" => isset($request->packages[0]['dimensions_unit']) ? $request->packages[0]['dimensions_unit'] : '',
			"containerSelected" => '',
			"fromCountryChange" => "false",
			"toCountryChange" => "false",
			"ratingQuoteTypeTime" => "null",
			"ratingQuoteTypeDetail" => "null",
			"ratingQuoteTypePackage" => "null",
			"ratingQuoteTypeLetter" => "null",
			"ratingHowWillRetail" => "null",
			"ratingHowWillDriver" => "null",
			"ratingHowWillDotCom" => "null",
			"ratingHowWillOneEight" => "null",
			"ratingDailyPick" => "null",
			"ratingPackType" => "null",
			"ratingDestTypeRes" => "null",
			"ratingOrigTypeRes" => "",
			"ratingDestTypeComm" => "null",
			"preferenceaddresskey" => "000",
			"palletselected" => "0",
			"refreshmod1" => "",
			"shipDate" => date('Y-m-d', strtotime($request->shipment_date)),
			"accountPrefpickup" => "null",
			"accountPrefgiveDriver" => "null",
			"palletEligable" => "null",
			"imsStatus" => "null",
			"ipaParameter" => "",
			"DAF" => "",
			"HFP" => "",
			"ddoPref" => "false",
			"pickupSupportIndicator" => "true",
			"countriesToCheckDropOffLocations" => "false",
			"countriesSupportingPickupsDomestic" => "US,PR",
			"shipmenttype" => "smallPkg",
			"inTranslation" => "inches",
			"cmTranslation" => "cm",
			"lbsTranslation" => "lb",
			"kgsTranslation" => "kg",
			"weightTranslation" => "Weight",
			"widthTranslation" => "Width",
			"heightTranslation" => "Height",
			"quoteType" => "estimateTimeCost.x",
			"pageRenderName" => "summaryResults",
			"origCountry" => !is_null($fromAddress) ? $fromAddress->country : $request->sender_country,
			"origCity" => !is_null($fromAddress) ? $fromAddress->city : $request->sender_city,
			"origStates" => !is_null($fromAddress) ? $fromAddress->state : $request->sender_state,
			"origPostalCode" => !is_null($fromAddress) ? $fromAddress->zip : $request->sender_zip,
			"casuiStreetSearch" => "",
			"casuiCitySearch" => "",
			"casuiStateSearch" => "emptyState",
			"casuiPostalCodeSearch" => "",
			"casuiPrefix" => "",
			"casuiId" => "",
			"shipmentType" => "smallPkg",
			"destCountry" => !is_null($toAddress) ? $toAddress->country : $request->recipient_country,
			"destCity" => !is_null($toAddress) ? $toAddress->city : $request->recipient_city,
			"destStates" => !is_null($toAddress) ? $toAddress->state : $request->recipient_state,
			"destPostalCode" => !is_null($toAddress) ? $toAddress->zip : $request->recipient_zip,
			"uapDetails_locationId" => "",
			"uapDetails_CompanyName" => "",
			"uapDetails_AddressLine1" => "",
			"uapDetails_AddressLine2" => "",
			"uapDetails_City" => "",
			"uapDetails_State" => "",
			"uapDetails_Zip" => "",
			"uapDetails_Phone" => "",
			"uapDetails_Country" => "",
			"pickerDate" => date('m/d/Y', time()),
			"shipmentDocsPallet" => "02",
			"currencyScalar" => isset($request->customs_value) ? $request->customs_value : "",
			"currencyUnits" => "CAD",
			"weight" => "1",
			"weightType" => $request->packages[0]['weight_unit'],
			"packagesMod1" => "1",
			"pickupPrefSel" => "0",
			"deliveryPrefSel" => "0",
			"destPostal" => !is_null($toAddress) ? $toAddress->zip : $request->recipient_zip,
			"origPostal" => !is_null($fromAddress) ? $fromAddress->zip : $request->sender_zip,
			"recalculateAccessorials" => "",
			"container" => $request->package_type,
			"length1" => "",
			"width1" => "",
			"height1" => "",
			"diUnit" => "IN",
			"weight1" => $request->packages[0]['weight'],
			"shipWeightUnit" => $request->packages[0]['weight_unit'],
			"packages" => "1",
			"sameValues" => "YES",
			"currency1" => "",
			"declaredValCurrencyUnit" => "CAD",
			"currency" => "null",
			"destinationCountryRequiresCODAmount" => "false",
			"defaultCODCurrencyForDestination" => "CAD",
			"signRequiredALL" => "DCR",
			"return_label" => "ERL",
			"return_label_coc" => "ERL",
			"pickupMethod1" => "1",
			"WWEFMT" => "",
			"WWEFIT" => "",
			"ctc_second_submit" => "10",
		);
		$results = self::_curl(self::UPS_CTC_URL, $form_params);
		$results = trim(preg_replace('/\s+/', ' ', $results));

			
		$crawler = new Crawler($results);

		$crawler = $crawler->filter(".ctcDetailView");
		$nodeValues = $crawler->each(
			function (Crawler $node, $i) {
				// find service code
				$button = $node->filter('button[name="shipAction"]')->attr('onclick');
				$service_code = explode(',', str_replace("'","", urldecode($button)));

				$first = $node->filter('.secBody p')->first()->text();
				$re = '/(?<=zone).*/';
				$str = $first;
				preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);

				$special_chars = array('%C2%A0', '%C2%AE', '%C2%A9', '%E2%84', '++', '+++');
				$first = str_replace($special_chars,'+', urlencode($first));
				$first = urldecode($first);
				@$zone_number = str_replace("\xc2\xa0", "", trim($matches[0][0]));

				$service_level = $this->findServiceLevel(trim($first), $zone_number, $service_code[3]);

				return array(
					'service_level' => $service_level,
					'zone_number' => @$zone_number
				);
			}
		);

		$results = array();
		foreach($nodeValues as $service)
		{
			$results[$service['service_level']['name']] = $service;
		}

		return $results;
	}

	public function getZones(Order $order, $request)
	{
		$fromAddress = ($order->exists) ? $order->FromAddress : null;
		$toAddress = ($order->exists) ? $order->ToAddress : null;

		$form_params = array(
			"timeOnlyRts" => "true",
			"ctcModPkgType" => "null",
			"ivrPkgType" => "null",
			"ctcModAccountFlag" => "show",
			"ctcModLoginStatus" => "null",
			"ctcModuleWeight" => "null",
			"ctcModuleWeightType" => "null",
			"importFlag" => 'false',
			"assume" => "",
			"rtsFlag" => "",
			"destCtyCurrency" => "",
			"destCtyDimUnit" => "",
			"destCtyUom" => "",
			"destCtyUomKey" => "",
			"afcity" => "null",
			"afpostal" => "null",
			"afcountry" => "null",
			"prefCity" => "null",
			"prefPostal" => "null",
			"prefcountry" => "null",
			"addressCountry" => "null",
			"userId" => "",
			"A_Resi" => "null",
			"isResidential" => "null",
			"addressDiffFromBook" => "NO",
			"addressBookCompanyOrName" => "",
			"addresseName" => "",
			"addressLine1" => "",
			"addressLine2" => "",
			"addressLine3" => "",
			"addressCity" => "null",
			"addressZip" => "null",
			"resComDet" => "null",
			"addressBookState" => "null",
			"requestor" => "",
			"taxIndicator" => "null",
			"DeSurInd" => "null",
			"AccNum" => "null",
			"returnURL" => "null",
			"page" => "accessorialModule",
			"loc" => "en_CA",
			"lanCancelURL" => "",
			"packageLetter" => "null",
			"selectedAccountNumber" => "",
			"selectedAccountClassification" => "null",
			"isSelectedAccountABREnabled" => "",
			"isSelectedAccountGBPalletEnabled" => "null",
			"accImsFlag" => "false",
			"accType" => "null",
			"accSelectedCountry" => "null",
			"jsDisabled" => "null",
			"isAccountSelected" => "null",
			"modDestResidetail" => "null",
			"destRes" => "null",
			"modWeightUnit" => "null",
			"modDimUnit" => "null",
			"modContainer" => "null",
			"modWeight" => "null",
			"modLength" => "null",
			"modWidth" => "null",
			"modHeight" => "null",
			"modDeclValue" => "null",
			"modDropOfChoice" => "null",
			"modPickUpMethod" => "null",
			"modDailyPickUp" => "null",
			"modValueAdded" => "null",
			"modPickUpMethod1" => "null",
			"modPickupAdded" => "null",
			"modRequestor" => "null",
			"modCustomValue" => "null",
			"modSameValue" => "null",
			"isModifyClicked" => "null",
			"modOrigCity" => "null",
			"modOrigZip" => "null",
			"modOrigCountry" => "null",
			"modDestCity" => "null",
			"modDestZip" => "null",
			"modDestCountry" => "null",
			"selectpackaging" => "null",
			"mypacking" => "My Packaging",
			"upsletter" => "UPS Envelope",
			"expressbox" => "UPS Express Box",
			"smallbox" => "UPS Express Box - Small",
			"mediumbox" => "UPS Express Box - Medium",
			"largebox" => "UPS Express Box - Large",
			"tube" => "UPS Tube",
			"pack" => "UPS Pak",
			"tenkg" => "UPS Worldwide Express 10KG Box",
			"twentyfivekg" => "UPS Worldwide Express 25KG Box",
			"palletPkgType" => "Pallet",
			"timeOnlyCountries" => "AS,AD,AI,AG,AM,AW,BB,BY,BZ,BJ,BT,BW,VG,BN,BF,KH,CV,CF,TD,CG,CK,DM,GQ,ER,FO,FJ,GF,PF,GA,GM,GE,GL,GD,GP,GU,GN,GW,GY,HT,IS,JM,KI,LA,LB,LS,LR,LI,MK,MG,MV,ML,MH,MQ,MR,FM,MC,MN,MS,MP,ME,NA,NP,AN,NC,NE,NF,PW,PG,RE,SM,SN,SC,SL,SB,KN,LC,VC,SR,SZ,SY,TJ,TG,TO,TT,TC,TV,UZ,VU,WF,WS,YE",
			"isOrigDestDutiable" => "true",
			"promoDiscountEligible" => "",
			"billableWeightIndicator" => "",
			"customerClassificationCode" => "",
			"abrEligible" => "",
			"useAcc" => "null",
			"modAccNumIn" => "null",
			"ctcModuleLogin" => "null",
			"quoteTypeQcc" => "estimateTimeCost.x",
			"origtype" => "",
			"datevalue" => "",
			"noofpackages" => "1",
			"quoteselected" => "estimateTimeCost.x",
			"nextclicked" => "next",
			"fromaddORcountry" => "",
			"itsQuickquote" => "no",
			"onChangeAccValue" => "",
			"quickQuoteTypePackageLetter" => "",
			"transitTimeSelected" => "",
			"shipmentTypeFreight" => "smallORPallet",
			"origCurrency" => "",
			"usPR" => (in_array($request->sender_country, ['US', 'PR'])) ? 'true' : '',
			"dismissLink" => "",
			"metricUnit" => (isset($request->packages[0]['dimensions_unit'])) ? $request->packages[0]['dimensions_unit'] : '',
			"containerSelected" => '',
			"fromCountryChange" => "false",
			"toCountryChange" => "false",
			"ratingQuoteTypeTime" => "null",
			"ratingQuoteTypeDetail" => "null",
			"ratingQuoteTypePackage" => "null",
			"ratingQuoteTypeLetter" => "null",
			"ratingHowWillRetail" => "null",
			"ratingHowWillDriver" => "null",
			"ratingHowWillDotCom" => "null",
			"ratingHowWillOneEight" => "null",
			"ratingDailyPick" => "null",
			"ratingPackType" => "null",
			"ratingDestTypeRes" => "null",
			"ratingOrigTypeRes" => "",
			"ratingDestTypeComm" => "null",
			"preferenceaddresskey" => "000",
			"palletselected" => "0",
			"refreshmod1" => "",
			"shipDate" => date('Y-m-d', strtotime($request->shipment_date)),
			"accountPrefpickup" => "null",
			"accountPrefgiveDriver" => "null",
			"palletEligable" => "null",
			"imsStatus" => "null",
			"ipaParameter" => "",
			"DAF" => "",
			"HFP" => "",
			"ddoPref" => "false",
			"pickupSupportIndicator" => "true",
			"countriesToCheckDropOffLocations" => "false",
			"countriesSupportingPickupsDomestic" => "US,PR",
			"shipmenttype" => "smallPkg",
			"inTranslation" => "inches",
			"cmTranslation" => "cm",
			"lbsTranslation" => "lb",
			"kgsTranslation" => "kg",
			"weightTranslation" => "Weight",
			"widthTranslation" => "Width",
			"heightTranslation" => "Height",
			"quoteType" => "estimateTimeCost.x",
			"pageRenderName" => "summaryResults",
			"origCountry" => !is_null($fromAddress) ? $fromAddress->country : $request->sender_country,
			"origCity" => !is_null($fromAddress) ? $fromAddress->city : $request->sender_city,
			"origStates" => !is_null($fromAddress) ? $fromAddress->state : $request->sender_state,
			"origPostalCode" => !is_null($fromAddress) ? $fromAddress->zip : $request->sender_zip,
			"casuiStreetSearch" => "",
			"casuiCitySearch" => "",
			"casuiStateSearch" => "emptyState",
			"casuiPostalCodeSearch" => "",
			"casuiPrefix" => "",
			"casuiId" => "",
			"shipmentType" => "smallPkg",
			"destCountry" => !is_null($toAddress) ? $toAddress->country : $request->recipient_country,
			"destCity" => !is_null($toAddress) ? $toAddress->city : $request->recipient_city,
			"destStates" => !is_null($toAddress) ? $toAddress->state : $request->recipient_state,
			"destPostalCode" => !is_null($toAddress) ? $toAddress->zip : $request->recipient_zip,
			"uapDetails_locationId" => "",
			"uapDetails_CompanyName" => "",
			"uapDetails_AddressLine1" => "",
			"uapDetails_AddressLine2" => "",
			"uapDetails_City" => "",
			"uapDetails_State" => "",
			"uapDetails_Zip" => "",
			"uapDetails_Phone" => "",
			"uapDetails_Country" => "",
			"pickerDate" => date('m/d/Y', time()),
			"currencyScalar" => isset($request->customs_value) ? $request->customs_value : "",
			"currencyUnits" => "CAD",
			"weight" => "1",
			"weightType" => "LBS",
			"packagesMod1" => "1",
			"pickupPrefSel" => "0",
			"deliveryPrefSel" => "0",
			"destPostal" => !is_null($toAddress) ? $toAddress->zip : $request->recipient_zip,
			"origPostal" => !is_null($fromAddress) ? $fromAddress->zip : $request->sender_zip,
			"recalculateAccessorials" => "",
			"container" => $request->package_type,
			"shipWeightUnit" => $request->packages[0]['weight_unit'],
			"currency1" => "",
			"declaredValCurrencyUnit" => "CAD",
			"currency" => "null",
			"destinationCountryRequiresCODAmount" => "false",
			"defaultCODCurrencyForDestination" => "CAD",
			"signRequiredALL" => "DCR",
			"return_label" => "ERL",
			//"return_label_coc" => "ERL",
			"pickupMethod1" => "1",
			"WWEFMT" => "",
			"WWEFIT" => "",
			"ctc_second_submit" => "10",
		);

		if (isset($request->packages)) {
			if (count($request->packages) > 1) {
				###$form_params['sameValues'] = "NO";
				$form_params['packages'] = count($request->packages);
				$form_params['total_pkg_num'] = count($request->packages);
				$form_params['length1'] = null;
				$form_params['width1'] = null;
				$form_params['height1'] = null;
				$form_params['diUnit'] = (isset($request->packages[0]['dimensions_unit'])) ? $request->packages[0]['dimensions_unit'] : '';
				$form_params['weight1'] = null;
				foreach($request->packages as $key => $value) {
					$pkg_cnt = $key + 1;
					$l = (isset($value['length'])) ? $value['length'] : '';
					$wd = (isset($value['width'])) ? $value['width'] : '';
					$w = (isset($value['weight'])) ? $value['weight'] : '';
					$wu = (isset($value['weight_unit'])) ? $value['weight_unit'] : '';
					$h = (isset($value['height'])) ? $value['height'] : '';
					$du = (isset($value['dimensions_unit'])) ? $value['dimensions_unit'] : '';

					$form_params['session_pkg_row_' . $pkg_cnt] = "CTCPkgDetailBean__".$pkg_cnt."__".$w."__".$wu."____ __".$l."__".$wd."__".$h."__".$du;
				}
			} else {
				###$form_params['sameValues'] = "YES";
				$form_params['length1'] = $request->packages[0]['length'];
				$form_params['width1'] = $request->packages[0]['width'];
				$form_params['height1'] = $request->packages[0]['height'];
				$form_params['diUnit'] = $request->packages[0]['dimensions_unit'];
				$form_params['weight1'] = $request->packages[0]['weight'];
			}
		} else {
			###$form_params['sameValues'] = "YES";
			$form_params['length1'] = null;
			$form_params['width1'] = null;
			$form_params['height1'] = null;
			$form_params['diUnit'] = (isset($request->packages[0]['dimensions_unit'])) ? $request->packages[0]['dimensions_unit'] : '';
			$form_params['weight1'] = $request->packages[0]['weight'];

			if ($request->package_type == '01') {
				$form_params['shipmentDocsPallet'] = '02';
			}
		}

		if ($request->sender_country != $request->recipient_country) {
			if ($request->sender_country != 'CA') {
				$form_params['signRequiredALL'] = 'DSS';
				$form_params['IMPALL'] = 'IMP';
				$form_params['import_label'] = 'LDE';
			}
		}
		// echo '<pre>';
		// var_export($form_params);die;

		$results = self::_curl(self::UPS_CTC_URL, $form_params);
		$results = trim(preg_replace('/\s+/', ' ', $results));

		$crawler = new Crawler($results);

		$crawler = $crawler->filter(".ctcDetailView");
		$nodeValues = $crawler->each(
			function (Crawler $node, $i) {
				// find service code
				$button = $node->filter('button[name="shipAction"]')->attr('onclick');
				$service_code = explode(',', str_replace("'","", urldecode($button)));

				$first = $node->filter('.secBody p')->first()->text();
				$re = '/(?<=zone).*/';
				$str = $first;
				preg_match($re, $str, $matches, PREG_OFFSET_CAPTURE, 0);

				$special_chars = array('%C2%A0', '%C2%AE', '%C2%A9', '%E2%84', '++', '+++');
				$first = str_replace($special_chars,'+', urlencode($first));
				$first = urldecode($first);
				@$zone_number = str_replace("\xc2\xa0", "", trim($matches[0][0]));

				$service_level = $this->findServiceLevel(trim($first), $zone_number, $service_code[3]);

				return array(
					'service_level' => $service_level,
					'zone_number' => @$zone_number
				);
			}
		);

		$results = array();
		foreach($nodeValues as $service)
		{
			$results[$service['service_level']['name']] = $service;
		}

		return $results;
	}

	private function findServiceLevel($text, $zone_number, $service_code)
	{
		$text = str_ireplace('SM', '', $text);
		$text = str_replace('This will open a new window', '', $text);
		$text = str_replace('zone', '', $text);
		$text = str_replace('-', '', $text);
		$text = str_replace(@$zone_number, '', $text);
		$text = trim($text);

		return array('name' => $text, 'code' => substr($service_code, 1));
	}

	private function _curl($url, $form_params)
	{
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_COOKIESESSION, true );
			curl_setopt($ch, CURLOPT_COOKIEJAR, storage_path('logs/my_cookies.txt'));
			curl_setopt($ch, CURLOPT_COOKIEFILE, storage_path('logs/my_cookies.txt'));
			curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($form_params));
			curl_setopt($ch, CURLOPT_USERAGENT, SELF::USER_AGENT);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$results = curl_exec($ch);
			curl_close ($ch);

			return $results;
		} catch (Exception $e) {
			report($e);

			return false;
		}
	}

}