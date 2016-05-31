<?php


class ReverseLogistic extends Eloquent {

	protected $table = 'store_return';


	public static function getSOList($data = array())
	{
		// print_r($data); die();
		$query = ReverseLogistic::select('store_return.*','stores.store_name','dataset.data_code', 'dataset.data_value', 'dataset.data_display')
		
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
		$query = ReverseLogistic::join('stores', 'store_return.store_code', '=', 'stores.store_code', 'LEFT')
			->join('dataset', 'so_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
		if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('stores.store_name', 'LIKE', $data['filter_store_name']);
		if( CommonHelper::hasValue($data['filter_created_at']) ) $query->where('store_return.created_at', 'LIKE', '%'.$data['filter_created_at'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', 'LIKE', '%'.$data['filter_status'].'%');

		return $query->count();
	}
		public static function getSOInfo($so_id) {
		$query = ReverseLogistic::join('dataset', 'store_return.so_status', '=', 'dataset.id', 'LEFT')
					->where('store_return.id', '=', $so_id)
					->first();
					return $query;
				}
	
	public static function getInfoBySoNo($data)
	{
		return StoreReturn::whereIn('so_no', $data)->get()->toArray();
	}
}


	
