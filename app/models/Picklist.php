<?php

class Picklist extends Eloquent {

	protected $table = 'picklist';

	/*****CMS Functions*****/
	public static function getPickList($picklistDocNo)
	{
		$picklist = Picklist::where('picklist.move_doc_number', '=', $picklistDocNo)
		->join('picklist_details', 'picklist.move_doc_number','=', 'picklist.move_doc_number')
		->where('picklist_details.move_doc_number', '=', $picklistDocNo)
			->first()->toArray();
		return $picklist;
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
public static function getPickingListv2($data= array(), $getCount=false)
	{
		// $query = Picklist::select(DB::raw('wms_picklist.*, sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned, store_code' ))

		$query = Picklist::select('picklist.*','division.*','picklist_details.*','dataset.*','picklist_details.updated_at as action_date')
            ->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
          	->join('division','picklist_details.division','=','division.id', 'LEFT')
			->join('dataset', 'picklist.pl_status', '=', 'dataset.id');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('data_value', '=', $data['filter_status'])->where('data_code', '=', 'PICKLIST_STATUS_TYPE');
		if( CommonHelper::hasValue($data['filter_store']) ) $query->where('store_code', '=',  $data['filter_store']);
		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->whereRaw('find_in_set('. $data['filter_stock_piler'] . ',assigned_to_user_id) > 0');
        if( CommonHelper::hasValue($data['filter_transfer_no']) ) $query->where('picklist_details.so_no', 'LIKE', '%'. $data['filter_transfer_no'] . '%');
        if( CommonHelper::hasValue($data['filter_action_date']) ) $query->whereBetween('picklist_details.updated_at', array($data['filter_action_date'] . ' 00:00:00', $data['filter_action_date'] . ' 23:59:59'));
		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no') $data['sort'] = 'picklist.move_doc_number';
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$query->groupBy('picklist.move_doc_number');
		$result = $query->get();
        DebugHelper::log(__METHOD__, $result);

		// get the multiple stock piler fullname
		foreach ($result as $key => $picklist) {
			$assignedToUserId       = explode(',', $picklist->assigned_to_user_id);
			$getUsers               = User::getUsersFullname($assignedToUserId);
			$result[$key]->fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $getUsers));
		}

		if($getCount) return count($result);
		return $result;
	}

	public static function getPickingList($data= array(), $getCount=false)
	{
		$query = Picklist::select(DB::raw('wms_picklist.*, sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned, store_code' ))
			->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number');

		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('picklist.move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('pl_status', '=', $data['filter_status']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if($data['sort'] == 'doc_no') $data['sort'] = 'picklist.move_doc_number';
			$query->orderBy($data['sort'], $data['order']);
		}


		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$query->groupBy('picklist.move_doc_number');
		$result = $query->get();

		return $result;
	}

	public static function getPickingListCount($data)
	{
		$query = Picklist::select('*');
		if( CommonHelper::hasValue($data['filter_doc_no']) ) $query->where('move_doc_number', 'LIKE', '%'. $data['filter_doc_no'] . '%');
		if( CommonHelper::hasValue($data['filter_type']) ) $query->where('type', '=',  $data['filter_type']);
		if( CommonHelper::hasValue($data['filter_status']) ) $query->where('pl_status', '=', $data['filter_status']);

		$result = $query->count();

		return $result;

	}

	

	public static function changeToStore($docNo)
	{
		$picklistDetails = Picklist::select(DB::raw('sum(wms_picklist_details.move_to_shipping_area) as sum_moved, sum(wms_picklist_details.assigned_user_id) as sum_assigned'))
			->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
			// ->whereI('picklist.move_doc_number', '=',$docNo)
			->whereIn('picklist.move_doc_number', $docNo)
			->groupBy('picklist.move_doc_number')
			->first();
		DebugHelper::log(__METHOD__, $picklistDetails);
		if(count($picklistDetails) === 0) throw new Exception("Document number does not exists");
		if($picklistDetails['sum_moved'] > 0 || $picklistDetails['sum_assigned'] > 0) {
			throw new Exception("This picklist cannot be changed to type store");
		}
		// Picklist::where('move_doc_number', '=', $docNo)
		Picklist::whereIn('move_doc_number', $docNo)
			->update(array(
				'type'		=> 'store',
				'updated_at'=>	date('Y-m-d H:i:s')));
		return;
	}

	public static function getInfoByDocNos($data)
	{
		return Picklist::whereIn('move_doc_number', $data)->get()->toArray();
	}

	public static function assignToStockPiler($docNo = '', $data = array())
	{
		$query = Picklist::where('move_doc_number', '=', $docNo)->update($data);
	}

	/***************************Methods for API only*********************************/
	public static function getListByPiler($pilerId)
	{
		return Picklist::whereRaw('find_in_set('. $pilerId . ',assigned_to_user_id) > 0')
			->where('data_code', '=', 'PICKLIST_STATUS_TYPE')
			->where('data_value', '<>', 'closed')
			->join('picklist_details', 'picklist.move_doc_number', '=', 'picklist_details.move_doc_number')
			->join('stores', 'stores.store_code', '=', 'picklist_details.store_code')
			->join('dataset', 'picklist.pl_status', '=', 'dataset.id')
			->groupBy('picklist.move_doc_number')
			->get(array('picklist_details.so_no as transfer_no','picklist.move_doc_number','stores.store_name', 'picklist_details.store_code', 'data_value as status'))
			->toArray();
	}

	public static function updateStatusToMoved($docNo)
	{
		return Picklist::where('move_doc_number','=', $docNo)
					->update(array('pl_status' =>  Config::get('picking_statuses.moved')));
	}

	public static function updateStatus($docNo, $plStatus)
	{
		return Picklist::where('move_doc_number', '=', $docNo)
					->update(array(
						"pl_status" => $plStatus,
						"updated_at" => date('Y-m-d H:i:s')
					));
	}

	public static function getPicklistWithoutDiscrepancies()
	{
		$status_options = Dataset::where("data_code", "=", "PICKLIST_STATUS_TYPE")->get()->lists("id", "data_value");

		$query = Picklist::join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
			->where('pl_status', '=', $status_options['done'])
			// ->where('quantity_to_pick', '=', 'moved_qty')
			// ->groupBy('picklist.move_doc_number')
			->get()->toArray();


		echo "<pre>"; print_r($query); die();
	}

	public static function getPicklistBoxes($doc_num){
		$box = Picklist::select('box_details.box_code','box_details.moved_qty',
                            'picklist_details.sku as upc','picklist_details.store_code','picklist_details.so_no','picklist_details.store_code',
                            'product_lists.description','product_lists.dept_code','product_lists.sub_dept','product_lists.class','product_lists.sub_class')
            ->join('picklist_details', 'picklist_details.move_doc_number', '=', 'picklist.move_doc_number')
			->join('box_details', 'picklist_details.id', '=', 'box_details.picklist_detail_id')
            ->join('product_lists','product_lists.upc','=','picklist_details.sku','LEFT')
			->where('picklist.move_doc_number','=', $doc_num)
			->orderBy('box_details.box_code')
			->get();

		if(!empty($box)){
                $counter=count($box);
                for($i=0;$i<$counter;$i++){
                	$ship_date=StoreOrder::where('so_no',$box[$i]->so_no)->select(DB::raw("date_format(order_date,'%m/%d/%y') as order_date"))->get();
	                if(!$ship_date->isEmpty())
	                	$data[$box[$i]->box_code]['ship_date'] = $ship_date[0]->order_date;
	                else
	                	$data[$box[$i]->box_code]['ship_date'] = '';
	                $res= Department::getBrand($box[$i]->dept_code,$box[$i]->sub_dept,0,0);
	                try{
		                $data[$box[$i]->box_code]['brand'] = $res[0];
		            }
		            catch(Exception $e){
		            	continue;
		            }
		            $loadCodes = DB::table('pallet_details')
		                        ->select('load_code')
		                        ->join('box_details','box_details.box_code','=','pallet_details.box_code')
		                        ->join('load_details','load_details.pallet_code','=','pallet_details.pallet_code')
		                        ->where('box_details.box_code', '=', $box[$i]->box_code)
		                        ->get();
			        foreach($loadCodes as $loadCode){
			        	$rs=DB::table('load')
                    ->select('is_shipped')
                    ->where('load_code', '=', $loadCode->load_code)
                    ->first();
				        $data[$box[$i]->box_code]['is_shipped'] = $rs->is_shipped;
			        }
			        if($loadCodes==null)
				        $data[$box[$i]->box_code]['is_shipped'] = 2;

	                $store = Store::select('store_name')
	                    ->where('store_code','=',$box[$i]->store_code)
	                    ->first();

                    $data[$box[$i]->box_code]['store_name'] = $store['store_name'];
                    $data[$box[$i]->box_code]['store_code'] = $box[$i]->store_code;
                    $data[$box[$i]->box_code]['items'] = $box;
                }
            }
		return $data;
	}
}
