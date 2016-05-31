<?php

class ReverseLogisticDetails extends Eloquent {

	protected $table = 'store_return_detail';
	protected $fillable = array('sku', 'so_no');

	/********************Methods for CMS only**************************/


	public static function getSODetails($so_no,$data = array()){
		// print_r($data); die();
		$query =  StoreReturnDetail::join('product_lists', 'store_return_detail.sku', '=', 'product_lists.upc')
				
					->join('store_return', 'store_return.so_no', '=', 'store_return_detail.so_no', 'LEFT')
					->select(DB::raw('convert(wms_product_lists.sku, decimal) as sku, convert(wms_product_lists.upc, decimal(20,0)) as upc'),'product_lists.description','store_return_detail.received_qty','store_return_detail.delivered_qty','store_return.created_at')
					// ->join('dataset', 'store_return.so_status', '=', 'dataset.id');
					->where('store_return_detail.so_no', '=', $so_no);

		if( CommonHelper::hasValue($data['filter_so_no']) ) $query->where('store_return.so_no', 'LIKE', '%'.$data['filter_so_no'].'%');
	//	if( CommonHelper::hasValue($data['filter_store_name']) ) $query->where('store_name', 'LIKE', '%'.$data['filter_store_name'].'%');
		if( CommonHelper::hasValue($data['filter_created_at']) ) $query->where('store_return.created_at', 'LIKE', '%'.$data['filter_created_at'].'%');
		if( CommonHelper::hasValue($data['filter_status']) ) {
			$arrParams = array('data_code' => 'SR_STATUS_TYPE', 'data_value'=> $data['filter_status']);
			$sr_status = Dataset::getType($arrParams)->toArray();

			$query->where('so_status', 'LIKE', '%'.$sr_status['id'].'%');
		}

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'product_lists.sku';
			if ($data['sort']=='upc') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='store') $data['sort'] = 'product_lists.upc';
			if ($data['sort']=='short_name') $data['sort'] = 'product_lists.short_description';
			if ($data['sort']=='ordered_quantity') $data['sort'] = 'store_return_detail.received_qty';
			if ($data['sort']=='delivered_quantity') $data['sort'] = 'store_return_detail.delivered_qty';
			if ($data['sort']=='so_no') $data['sort'] = 'store_return.so_no';
			if ($data['sort']=='created_at') $data['sort'] = 'store_return.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$result = $query->get();
		DebugHelper::log(__METHOD__, $result);

		return $result;
	}

	public static function getCountSODetails($so_no) {
		$storeOrderDetail = StoreReturnDetail::select('store_return_detail.*', 'product_lists.description')
			->join('product_lists', 'store_return_detail.sku', '=', 'product_lists.upc')
			// ->join('store_return', 'store_return.so_no', '=', 'store_return_detail.so_no', 'LEFT')
			->where('so_no', '=', $so_no)
			->get();

		return $storeOrderDetail->count();
	}
}