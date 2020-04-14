<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Order;

class OrderController extends Controller
{
    //
    public function list(){
    	return Order::list(request()->all());
    }

    public function orders(Request $request){
    	$origin = array_filter(request('origin'), function($x) { return !empty($x); });
    	$destination = array_filter(request('destination'), function($x) { return !empty($x); });

		if(count($origin) != 2 || count($destination) != 2){
			return response()->json([
    			'error' => 'Invalid coordinates.'
    		],422);
		}

    	return Order::orders(request()->all());
    }

    public function take($id){
    	return Order::take($id,request()->all());
    }
}
