<?php

class ApiPurchaseOrder extends BaseController {


	public function __construct() {
		date_default_timezone_set('Asia/Manila');
	}

	/**
	* Display a prodcut listing of the resource
	*
	* @example  www.example.com/api/{version}/purchase_order/{piler_id}
	*
	* @param  piler_id  int    Stock piler id
	* @throws Some_Exception_Class If something interesting cannot happen
	* @return json encoded paginated purchase order list
	*/
	  public function RPolist($piler_id)
    {
    	try {
			$polist = PurchaseOrder::GetApiRPoList($piler_id);

			return Response::json(array('result' => $polist),200);
			//return $polist;
		}
		catch(Exception $e) 
	{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }
    public function RPoListDetailUpdate($receiver_no,$division,$quantity,$sku,$quantity_delivered)
    {
    	try {
		$polistdetails = PurchaseOrderDetail::updateqty($receiver_no,$division,$quantity,$sku,$quantity_delivered);

			return Response::json(array('result' => $polistdetails),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }
     public function RPolistDetail($receiver_no,$division_id)
    {
    	try {
			$polistdetail = PurchaseOrder::GetApiRPoListDetail($receiver_no,$division_id);

			return Response::json(array('result' => $polistdetail),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

   }



    public function UpdateApiRPoSlot($receiver_no,$division_id)
    {
    	try {
			$poupdatastatus = PurchaseOrder::UpdateApiRPoSlot($receiver_no,$division_id);

			return Response::json(array('result'),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }

	public function index($piler_id) {

		try {

			if(! CommonHelper::hasValue($piler_id) ) throw new Exception( 'Missing stockpiler id parameter.');

			$arrParams = array('stock_piler_id' => $piler_id);
			$purchaseOrder = PurchaseOrder::getAPIPoLists($arrParams);

			DebugHelper::log(__METHOD__, $purchaseOrder);

			// $items 		= $purchaseOrder;
			// $totalItems = PurchaseOrder::getAPICount($arrParams);
			// $perPage 	= 10;

			// $purchaseOrder = Paginator::make($items, count($items), $perPage);

			return CommonHelper::return_success_message($purchaseOrder);
		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Get Purchase Order Details
	*
	* @example  www.example.com/api/{version}/purchase_order/details/{receiver_no}
	*
	* @param  receiver_no    int    Receiver number
	* @return json encoded array of purchase order details
	*/
	public function getDetails($receiver_no) {
		try {
			if(! CommonHelper::hasValue($receiver_no) ) throw new Exception( 'Missing receiver number parameter.');

			$arrParams = array('receiver_no' => $receiver_no);
			$po_details = PurchaseOrderDetail::getAPIPoDetail($arrParams);

			DebugHelper::log(__METHOD__, $po_details);
			return CommonHelper::return_success_message($po_details->toArray());
		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Change purchase order status to closed and changes purchase order quantities
	*
	* @example  www.example.com/api/{version}/purchase_order/{po_order_no}
	*
	* @param  po_order_no    int             Purchase order number
	* @param  data           json string     json encoded array of skus and correspoinding moved quantity
	* @param  user_id        int             Stock Piler Id
	* @param  datetime_done  datetime        Date and time the purchase order was done (YYYY-MM-DD HH:MM:SS) (note: this is the time when the PO was marked done in the app, which is not the same with current time when the api call was done)
	* @param  receiver_no          int       Receiver number
	* @return Status
	*/
	public function savedReceivedPO($po_order_no) {
		try {
			if(! CommonHelper::hasValue($po_order_no) ) throw new Exception( 'Missing purchase order number parameter.');
			// echo "<pre>"; print_r(json_decode(Request::get('data'), true)); die();
			CommonHelper::setRequiredFields(array('data', 'user_id', 'datetime_done','receiver_no', 'slot_code'));

			$data 		= json_decode(Request::get('data'), true);

			/*if(empty($data)) {
				throw new Exception("Empty data parameter");
			}*/

			DebugHelper::log(__METHOD__, $data);
			$po_status   = "done";
			$date_done   = Request::get('datetime_done');
			$user_id     = Request::get('user_id');
			$receiver_no = Request::get('receiver_no');
			$slot_code = Request::get('slot_code');
			DB::beginTransaction();
			//check if user has the right to this PO
			PurchaseOrder::isPOAssignedToThisUser($user_id, $receiver_no);


			//save purcase order detail
			if (CommonHelper::arrayHasValue($data)) {
				foreach($data as $row) {
					$row['po_order_no'] = $po_order_no;
					PurchaseOrderDetail::updateSKUs($row, $receiver_no); //update po_detail table for the received qty
				}
				self::validatePassedPODetails($receiver_no, $po_order_no);
			}

			//update po status
			PurchaseOrder::updatePO($po_order_no, $po_status, $date_done, $slot_code); //update po_list status to done

			//add audit trail
			self::savedReceivedPOAuditTrail($po_order_no, $user_id);

			//add transaction for jda syncing
			JdaTransaction::insert(array(
				'module' 		=> Config::get('transactions.module_purchase_order'),
				'jda_action'	=> Config::get('transactions.jda_action_po_receiving'),
				'reference'		=> $po_order_no
			));

			DB::commit();
			return CommonHelper::return_success();
		}catch(Exception $e) {
			DB::rollback();
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Add audit trail for saving received PO
	*
	* @param  receiver_no    integer  Receiver number
	* @param  user_id  integer  stock piler assigned to the PO
	* @return true
	*/
	private static function savedReceivedPOAuditTrail($receiver_no, $user_id)
	{
		$data_after = 'Receiver #' . $receiver_no . ' was received by Stock Piler #' . $user_id  . '.';
		$arrParams = array(
			'module'		=> Config::get("audit_trail_modules.purchaseorder"),
			'action'		=> Config::get("audit_trail.save_po"),
			'reference'		=> 'Receiver #' . $receiver_no,
			'data_before'	=> '',
			'data_after'	=> $data_after,
			'user_id'		=> Authorizer::getResourceOwnerId(),// ResourceServer::getOwnerId(),
			'created_at'	=> date('Y-m-d H:i:s'),
			'updated_at'	=> date('Y-m-d H:i:s')
		);
		AuditTrail::addAuditTrail($arrParams);
	}

	private static function validatePassedPODetails($poId, $poNumber)
	{
		$poInfo = PurchaseOrder::getPOInfo($poId);
		if(empty($poInfo)) throw new Exception("Purchase order does not exist");
		if((int)$poInfo->purchase_order_no !== (int)$poNumber) throw new Exception("Passed purchase order number does not match purchase order id.");

	}


	/*********************Unused functions remove******************************/
	//TODO::Why 5? Is this function used? Ask Mobile
	/**
	* Assign Purchase order to admin
	*
	* @example  www.example.com/api/{version}/purchase_order/assign_to_stockpiler/{id}/{clerk_id}/{piler_id}
	*
	* @param  id     	int    Purchase order id
	* @param  clerk_id  int    Document Clerk Id
	* @param  piler_id  int    Stock Piler Id
	* @return Status if fail or successfull
	*/
	/*public function assignToPiler($id, $clerk_id, $piler_id) {
		try {
			if(! CommonHelper::hasValue($id) ) throw new Exception( 'Missing id parameter.');
			if(! CommonHelper::hasValue($clerk_id) ) throw new Exception( 'Missing clerk_id parameter.');
			if(! CommonHelper::hasValue($piler_id) ) throw new Exception( 'Missing stockpiler_id parameter.');

			DB::beginTransaction();
			$params = array(
				"assigned_by"			=> $clerk_id,
				"assigned_to_user_id"	=> $piler_id,
				"updated_at"			=> date('Y-m-d H:i:s'),
				"po_status"				=> 5
			);
			$po = PurchaseOrder::find($id);
			PurchaseOrder::assignToStockPiler($po->purchase_order_no, $params);
			DB::commit();

			DebugHelper::log(__METHOD__, $po->toArray());

			return Response::json(array(
				'error' => false,
				'message' => 'Successfully assigned to piler '. $piler_id),
				200
			);

		}catch(Exception $e) {
			DB::rollback();
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"message" => $e->getMessage()),
				400
			);
		}
	}
*/

		//TODO::Ask mobile if this is used
	/**
	* Change purchase order status
	*
	* @example  www.example.com/api/{version}/purchase_order/update_status/{po_order_no}
	*
	* @param  po_order_no    int    Purchase order number
	* @param  po_status      int    Purchase order status
	* @return Status
	*/
	public function updateStatus($po_order_no) {
		try {

			if(! CommonHelper::hasValue($po_order_no) ) throw new Exception( 'Missing purchase order number parameter.');
			if(! CommonHelper::hasValue(Request::get('po_status')) ) throw new Exception( 'Missing status parameter.');
			$status_value = Request::get('po_status');
			$status_options = Dataset::where("data_code", "=", "PO_STATUS_TYPE")->get()->lists("id", "data_value");

			if(! CommonHelper::arrayHasValue($status_options[$status_value]) ) throw new Exception( 'Invalid status value.');

			$loggedInUserId = Authorizer::getResourceOwnerId();

			$validateUser = PurchaseOrder::where('purchase_order_no', '=', $po_order_no)
					// ->where('assigned_to_user_id', '=', $loggedInUserId)->first();
					->whereRaw('find_in_set('. $loggedInUserId . ',assigned_to_user_id) > 0')->first();

			if ( empty($validateUser) ) throw new Exception( 'User does not have the rights to access this po.');

			$po = PurchaseOrder::where('purchase_order_no', '=', $po_order_no)
					->where('assigned_to_user_id', '=', $loggedInUserId)
					->update(array(
						"po_status" => $status_options[$status_value],
						"updated_at" => date('Y-m-d H:i:s')
					));

			//Audit trail
			$user_id = $loggedInUserId;
			$date_before = 'PO No #' . $po_order_no . ' status was Assigned.';
			$data_after = 'PO No #' . $po_order_no . ' status is now ' .$status_options[$status_value]. ' and was changed by Stock Piler #' . $user_id  . '.';
			$arrParams = array(
				'module'		=> Config::get("audit_trail_modules.purchaseorder"),
				'action'		=> Config::get("audit_trail.modify_po_status"),
				'reference'		=> 'Purchase Order #' . $po_order_no,
				'data_before'	=> $date_before,
				'data_after'	=> $data_after,
				'user_id'		=> Authorizer::getResourceOwnerId(),// ResourceServer::getOwnerId(),
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			);
			AuditTrail::addAuditTrail($arrParams);

			DebugHelper::log(__METHOD__, $po);
			return Response::json(array(
				'error' => false,
				'message' => 'Successfully changed status to '.$status_value,
				'result' => $po),
				200
			);

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}

	/**
	 * Items in master file but not in PO will be inserted to PO
	 * @param  int		 $po_order_no 	Purchase order no
	 * @return boolean
	 */
	public function notInPo($po_order_no) {
		try {
			DB::beginTransaction();
			CommonHelper::setRequiredFields(array('data'));
			if(! CommonHelper::hasValue($po_order_no) ) throw new Exception( 'Missing purchase order number parameter.');

			$data = json_decode(Request::get('data'), true);

			foreach ($data as $value) {
				$po = PurchaseOrderDetail::firstOrNew(array('sku'=>$value['upc'], 'receiver_no'=>$value['receiver_no']));
				$po->sku                = $value['upc'];
				$po->receiver_no        = $value['receiver_no'];
				$po->quantity_delivered = $value['quantity_delivered']; //($po->exists) ? ($po->quantity_delivered) :
				$po->expiry_date		= $value['expiry_date'];
				$po->save();
			}
			DebugHelper::log(__METHOD__, $po);


			//Audit trail
			$user_id              = Authorizer::getResourceOwnerId();
			$arr                  = array_map(function($el){ return $el['upc']; }, $data);
			$comma_separated_skus = implode(',', $arr);
			$data_after           = 'PO No #' . $po_order_no . ' skus ' .$comma_separated_skus. ' with quantity 1 has been added by Stock Piler #' . $user_id  . '.';

			$arrParams = array(
				'module'		=> Config::get("audit_trail_modules.purchaseorder"),
				'action'		=> Config::get("audit_trail.save_not_in_po"),
				'reference'		=> 'Purchase Order #' . $po_order_no,
				'data_before'	=> '',
				'data_after'	=> $data_after,
				'user_id'		=> $user_id,
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			);
			AuditTrail::addAuditTrail($arrParams);

			DB::commit();
			return CommonHelper::return_success();

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	 * Items not in master file and not in PO will be consider as unlisted
	 * @param  int		 $po_order_no 	Purchase order no
	 * @return boolean
	 */
	public function unlisted($po_order_no) {
		try {
			DB::beginTransaction();
			CommonHelper::setRequiredFields(array('data'));
			if(! CommonHelper::hasValue($po_order_no) ) throw new Exception( 'Missing purchase order number parameter.');

			$data = json_decode(Request::get('data'), true);

			foreach ($data as $value)
			{
				$unlisted = Unlisted::firstOrNew(array('sku'=>$value['upc'], 'reference_no'=>$po_order_no));

				if (array_key_exists('brand', $value)) $unlisted->brand = $value['brand'];
				if (array_key_exists('division', $value)) $unlisted->division = $value['division'];
				if (array_key_exists('description', $value)) $unlisted->description = $value['description'];
				if (array_key_exists('style_no', $value)) $unlisted->style_no = $value['style_no'];

				$unlisted->sku               = $value['upc'];
				$unlisted->reference_no      = $po_order_no;
				$unlisted->quantity_received = $value['quantity_received']; //($unlisted->exists) ? ($unlisted->quantity_received + 1) : $value['quantity_received'];
				$unlisted->updated_at        = date('Y-m-d H:i:s');
				$unlisted->scanned_by		 = Authorizer::getResourceOwnerId();
				$unlisted->save();
			}

			DebugHelper::log(__METHOD__, $unlisted);

			//Audit trail
			$user_id              = Authorizer::getResourceOwnerId();
			$arr                  = array_map(function($el){ return $el['upc']; }, $data);
			$comma_separated_skus = implode(',', $arr);
			$data_after           = 'PO No #' . $po_order_no . ' skus ' .$comma_separated_skus. ' with quantity 1 has been added by Stock Piler #' . $user_id  . '.';

			$arrParams = array(
				'module'		=> Config::get("audit_trail_modules.purchaseorder"),
				'action'		=> Config::get("audit_trail.unlisted"),
				'reference'		=> 'Purchase Order #' . $po_order_no,
				'data_before'	=> '',
				'data_after'	=> $data_after,
				'user_id'		=> $user_id,
				'created_at'	=> date('Y-m-d H:i:s'),
				'updated_at'	=> date('Y-m-d H:i:s')
			);
			AuditTrail::addAuditTrail($arrParams);

			DB::commit();
			return CommonHelper::return_success();

		}catch(Exception $e) {
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			DB::rollback();
			return CommonHelper::return_fail($e->getMessage());
		}
	}

	/**
	* Update a particular PO Detail
	*
	* @example  www.example.com/sample url
	*
	* @param  po_id    int    Purchase order id
	* @param  sku      string SKU/UPC of product
	* @return
	*/
	/*public function updateDetail($po_id, $sku) {
		try {
			if(! CommonHelper::hasValue($po_id) ) throw new Exception( 'Missing id parameter.');
			if(! CommonHelper::hasValue($sku) ) throw new Exception( 'Missing sku/upc parameter.');
			if(! CommonHelper::numericHasValue(Request::get("quantity_delivered")) ) throw new Exception( 'Missing quantity.');

			$quantity_delivered = Request::get("quantity_delivered");
			DB::beginTransaction();
			$po_detail = DB::table('purchase_order_details')
					->where('po_id', '=', $po_id)
					->where('sku', '=', $sku)
					->update(array(
						"quantity_delivered"=>$quantity_delivered,
						"updated_at" => date('Y-m-d H:i:s')
					));

			DebugHelper::log(__METHOD__, $po_detail->toArray());
			DB::commit();
			return Response::json(array(
				'error' => false,
				'message' => 'Successfull updated sku #'.$sku,
				'result' => $po_detail),
				200
			);

		}catch(Exception $e) {
			DB::rollback();
			Log::error(__METHOD__ .' Something went wrong: '.print_r($e->getMessage(),true));
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()),
				400
			);
		}
	}*/

}