<?php




	public static function getPoListsdivision($data = array(), $getCount = FALSE) {
		
		$query = Division::getPOQuerydivision($data);
		// echo "<pre>"; print_r($data); die();


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get(array(
									'purchase_order_lists.*',
									'vendors.vendor_name',
									'purchase_order_details.*',
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		DebugHelper::log(__METHOD__, $result);
		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		if($getCount) return count($result);

		return $result;
	}

	public static function getPOQuerydivision($data = array())
	{
		$query = DB::table('purchase_order_lists')
					
			->select('*',DB::raw('sum(quantity_ordered) as quantity_ordered1'),DB::raw('sum(quantity_delivered) as quantity_delivered1'))
		 	 	// ->join('users', 'purchase_order_lists.assigned_to_user_id', 'IN', 'users.id', 'LEFT')
		
			->join('purchase_order_details', 'purchase_order_lists.receiver_no', '=', 'purchase_order_details.receiver_no', 'LEFT')
			->join('product_lists', 'purchase_order_details.sku', '=', 'product_lists.upc', 'LEFT')
			->join('dataset', 'purchase_order_details.po_status', '=', 'dataset.id', 'LEFT')
			->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT')
			->where('purchase_order_details.receiver_no','=', $data['receiver_no'])
			->groupBy('purchase_order_details.division');

		

		if( CommonHelper::hasValue($data['filter_po_no']) ) $query->where('purchase_order_no', 'LIKE', '%'.$data['filter_po_no'].'%');
		if( CommonHelper::hasValue($data['filter_receiver_no']) ) $query->where('purchase_order_lists.receiver_no', '=', $data['filter_receiver_no']);
		if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('purchase_order_lists.created_at', 'LIKE', '%'.$data['filter_entry_date'].'%');
		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0');
		if( CommonHelper::hasValue($data['filter_status']) && $data['filter_status'] !== 'default' ) $query->where('purchase_order_lists.po_status', '=', $data['filter_status']);
		if( CommonHelper::hasValue($data['filter_back_order']) ) $query->where('back_order', '=', $data['filter_back_order']);
		if( CommonHelper::hasValue($data['filter_brand']) ) $query->where('dept_code', '=', $data['filter_brand']);
		if( CommonHelper::hasValue($data['filter_division']) ) $query->where('division', '=', $data['filter_division']);
		if( CommonHelper::hasValue($data['filter_shipment_reference_no']) ) $query->where('shipment_reference_no', '=', $data['filter_shipment_reference_no']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='po_no') $data['sort'] = 'division';
			if ($data['sort']=='receiver_no') $data['sort'] = 'purchase_order_lists.receiver_no';
			if ($data['sort']=='entry_date') $data['sort'] = 'purchase_order_lists.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		//if( !empty($data['filter_back_order_only']) ) $query->where('back_order', '<>', 0);

		DebugHelper::log(__METHOD__, $query);
		return $query;
	}

	public static function getPoListsdiv($data = array(), $getCount = FALSE) {

		
		$query = Division::getPOQuerydivision($data);
		// echo "<pre>"; print_r($data); die();

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get(array(
									'purchase_order_lists.*',
									'vendors.vendor_name',
									'purchase_order_details.Division_Name',
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		DebugHelper::log(__METHOD__, $result);
		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		if($getCount) return count($result);

		return $result;
	}

	public static function getPOInfodiv($receiver_no = NULL) {
		$query = DB::table('purchase_order_lists')
					// ->join('users', 'purchase_order_lists.assigned_to_user_id', '=', 'users.id', 'LEFT')
					->join('dataset', 'purchase_order_lists.po_status', '=', 'dataset.id', 'LEFT')
					->join('vendors', 'purchase_order_lists.vendor_id', '=', 'vendors.id', 'LEFT')
					->where('purchase_order_lists.receiver_no', '=', $receiver_no);

		$result = $query->get(array(
									'purchase_order_lists.*',
									'vendors.vendor_name',
									'dataset.data_display'
									// 'users.firstname',
									// 'users.lastname'
								)
							);

		// get the multiple stock piler fullname
		foreach ($result as $key => $po) {
			$assignedToUserId       = explode(',', $po->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		return $result[0];
	}

}