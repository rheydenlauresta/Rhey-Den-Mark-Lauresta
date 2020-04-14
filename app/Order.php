<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Order extends Model
{
    //
    protected $table = 'orders';

    protected $fillable = [
        'origin', 'destination', 'distance', 'status'
    ];

    public function scopeList($query, $data){

    	if(!is_numeric($data['limit']) || !is_numeric($data['page'])){
    		return response()->json([
    			'error' => 'Limit and page should be an integer.'
    		],422);
    	}

    	$res = Order::select('id','distance','status')->paginate($data['limit']);

    	return response()->json($res->toArray()['data'],200);
    }

    public function scopeOrders($query, $data){
    	$distance = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='.$data['origin'][0].','.$data['origin'][1].'&destinations='.$data['destination'][0].','.$data['destination'][1].'&key='.env('GMAP_API_KEY'));

    	$parse = json_decode($distance)->rows[0]->elements[0]->distance->value;

    	$id = Order::insertGetId([
    		'origin' => implode(',',$data['origin']),
    		'destination' => implode(',',$data['destination']),
    		'distance' => $parse,
    		'created_at' => date("Y-m-d H:i:s"),
    		'updated_at' => date("Y-m-d H:i:s")
    	]);

    	return response()->json([
    		'id' => $id,
    		'distance' => $parse,
    		'status' => "UNASSIGNED"
    	],200);
    }

    public function scopeTake($query, $id, $data){
    	$res = Order::where('id',$id)->where('status','UNASSIGNED')->update([
    		'status' => $data['status']
    	]);

    	if($res == 0){
    		return response()->json([
	    		'error' => "Order already taken."
	    	], 422);
    	}

    	return response()->json([
    		'status' => "SUCCESS"
    	], 200);
    }
}
