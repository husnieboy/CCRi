<?php

class StoreReturn extends Eloquent {

	protected $table = 'store_return';


	public static function getSOList($data = array())
	{
		// print_r($data); die();
		$query = StoreReturn::select('store_return.*','stores.store_name','dataset.data_code', 'dataset.data_value', 'dataset.data_display')
		
			->join('stores', 'store_return.store_code', '=', 'stores.store_code', 'LEFT')

			->join('dataset', 'so_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('stores.store_name', 'LIKE', $data['filter_store_name']);
		if( CommonHelper::hasValue($data['filter_created_at']) ) $query->where('store_return.created_at', 'LIKE', '%'.$data['filter_created_at'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', 'LIKE', '%'.$data['filter_status'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='so_no') $data['sort'] = 'so_no';
			if ($data['sort']=='store') $data['sort'] = 'store_name';
			if ($data['sort']=='created_at') $data['sort'] = 'store_return.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		// get the multiple stock piler fullname
		foreach ($result as $key => $picklist) {
			$assignedToUserId       = explode(',', $picklist->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		DebugHelper::log(__METHOD__, $result);
		return $result;
	}

	public static function getCount($data = array()){
		$query = StoreReturn::join('stores', 'store_return.store_code', '=', 'stores.store_code', 'LEFT')
			->join('dataset', 'so_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('stores.store_name', 'LIKE', $data['filter_store_name']);
		if( CommonHelper::hasValue($data['filter_created_at']) ) $query->where('store_return.created_at', 'LIKE', '%'.$data['filter_created_at'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', 'LIKE', '%'.$data['filter_status'].'%');

		return $query->count();
	}

	public static function getSOInfo($so_id = NULL) {
		$query = StoreReturn::join('dataset', 'store_return.so_status', '=', 'dataset.id', 'LEFT')
					->where('store_return.id', '=', $so_id);

		$result = $query->get(array(
									'store_return.*',
									'dataset.data_display',
								)
							);
		return $result[0];
	}

	public static function getInfoBySoNo($data)
	{
		return StoreReturn::whereIn('so_no', $data)->get()->toArray();
	}

	public static function assignToStockPiler($soNo = '', $data = array())
	{
		$query = StoreReturn::where('so_no', '=', $soNo)->update($data);
	}

	public static function getStoreList(){
		$storeList = StoreReturn::lists('store_code');
			
		return $storeList;
	}

	/***********************00000*Methods for API only*00000*******************************/
	public static function getListByPiler($pilerId)
	{
		return StoreReturn::whereRaw('find_in_set('. $pilerId . ',assigned_to_user_id) > 0')
			->where('data_code', '=', 'SR_STATUS_TYPE')
			->where('data_value', '<>', 'closed')
		->join('store_return_detail', 'store_return.so_no', '=','store_return_detail.so_no')
			->join('stores', 'stores.store_code', '=', 'store_return.store_code')
			->join('dataset', 'store_return.so_status', '=', 'dataset.id')
			->groupBy('store_return.so_no')
			->get(array('store_return.so_no','stores.store_name', 'store_return.store_code', 'data_value as status', 'slot_code'))
			->toArray();
	}

	public static function updateStatus($soNo, $soStatus, $slot_code = '')
	{
		$params = array();
		$params = array( "so_status" => $soStatus, "updated_at" => date('Y-m-d H:i:s'));
		if ( !empty($slot_code) ) $params['slot_code'] = $slot_code;

		return StoreReturn::where('so_no', '=', $soNo)->update($params);
	}
}