<?php

class Store extends Eloquent {

	protected $table = 'stores';

	public static function getStoreList($data = array()) {
		$query = DB::table('stores');


		//if( CommonHelper::hasValue($data['filter_store_code']) ) $query->where('store_code', 'LIKE', '%'.$data['filter_store_code'].'%');
		//if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_name', 'LIKE', '%'.$data['filter_store_name'].'%');
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		CommonHelper::filternator($query,$data,2);
		//CommonHelper::pagenator($query,$data['page']);

		$result = $query->get();
				
		return $result;
	}
	public static function StorReturnlists($data = array()) {
		$query = DB::table('stores');


		//if( CommonHelper::hasValue($data['filter_store_code']) ) $query->where('store_code', 'LIKE', '%'.$data['filter_store_code'].'%');
		//if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_name', 'LIKE', '%'.$data['filter_store_name'].'%');
		
		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		
		CommonHelper::filternator($query,$data,2);
		//CommonHelper::pagenator($query,$data['page']);

		$result = $query->get();
				
		return $result;
	}

	public static function getCountstoreLists($data = array())
	{
		$query = DB::table('stores');

		if( CommonHelper::hasValue($data['filter_store_code']) ) $query->where('store_code', 'LIKE', '%'.$data['filter_store_code'].'%');
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_name', 'LIKE', '%'.$data['filter_store_name'].'%');
		$result = $query->count();

		return $result;
	}

	public static function getStoreName($storeCode)
	{
		$storeName = Store::where('store_code', '=', $storeCode)
			->pluck('store_name');
			
		return $storeName;
	}
	
	public static function getStoreList1(){
		$storeList = Store::lists('store_name');
			
		return $storeList;
	}	
		public static function getStoreList2(){
		$storeList = Store::lists('store_name');
			
		return $storeList;
	}

	public static function getPickStoreName($storeCode)
	{
		$storeName = Store::where('store_code', '=', $storeCode)
			->pluck('store_name');
			
		return $storeName;
	}


	
}

	