<?php

namespace Ups;

use Illuminate\Database\Eloquent\Model;
use DB;

class UPSMatrixExchange extends Model
{
    public static function getPublishedRate( $ups_request, $post_data, $vll_account_id )
    {
        $request = $ups_request->request->all();  

        $user_matrix_id = DB::connection('pricematrix')
            ->table('vll_account_numbers as vll')
            ->where('vll.account_id', '=', $vll_account_id) 
            ->join('customer_details as cd', 'cd.id', '=', 'vll.customer_detail_id')
            ->first();

        $rate_table = (String) ('rates_'.$user_matrix_id->user_id.' as usr');        

        $result = DB::connection('pricematrix')
            ->table($rate_table)
            ->where('usr.id', '=', $request['selectedRates']['user_rate_id'])
            ->join('ups_published_rates as upr', 'upr.id', '=', 'usr.ups_published_rate_id')
            ->first();      
            
        if(!$result){
            $result = DB::connection('pricematrix')
            ->table('ups_published_rates')
            ->select("ups_published_rates.id as rate_id", "ups_published_rates.*")
            ->where('id', $request['selectedRates']['user_rate_id'])
            ->first();     

            $zone_discounts = DB::connection('pricematrix')
            ->table('zone_discounts')
            ->where('package_type_id', $result->package_type_id)
            ->where('service_level_id', $result->service_level_id)
            ->where('zone_id', $result->zone_id)
            ->where('user_id', $user_matrix_id->user_id)
            ->where('year', '2019')
            ->first();

            if ($zone_discounts) {
                $pub_rate = $result->value;
                if ($zone_discounts->discount_type == 'percentage') {
                    $zone_discount = $pub_rate * $zone_discounts->value;
                } else {
                    $zone_discount = $zone_discounts->value;
                }
                $value = $pub_rate - $zone_discount;
                $result->value = number_format((float) $value, 2, '.', '');
            }
        }

    	return $result;
    }
}