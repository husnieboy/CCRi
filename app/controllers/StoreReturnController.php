<?php

class StoreReturnController extends BaseController {
	private $data = array();
	protected $layout = "layouts.main";

	public function __construct() {
    	date_default_timezone_set('Asia/Manila');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
	}

	public function showIndex() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessStoreReturn', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->getList();
	}


	public function exportCSV() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportStoreReturn', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_return' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}
		$this->data = Lang::get('store_return');
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SR_STATUS_TYPE");
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$arrParams = array(
							'filter_so_no' 			=> Input::get('filter_so_no', NULL),
							'filter_store_name' 			=> Input::get('filter_store_name', NULL),
							'filter_created_at' 	=> Input::get('filter_created_at',NULL),
							'filter_status' 		=> Input::get('filter_status', NULL),
							'sort'					=> Input::get('sort', 'so_no'),
							'order'					=> Input::get('order', 'ASC'),
							'page'					=> NULL,
							'limit'					=> NULL
						);

		$results = StoreReturn::getSOList($arrParams);

		$this->data['results'] = $results;

		$pdf = App::make('dompdf');
		$pdf->loadView('store_return.report_list', $this->data)->setPaper('a4')->setOrientation('landscape');
		// return $pdf->stream();
		return $pdf->download('store_return_' . date('Ymd') . '.pdf');
	}

	public function exportDetailsCSV() {
		///Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanExportStoreReturn', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_return' . $this->setURL());
			}
    	} else {
			return Redirect::to('users/logout');
		}

		if (StoreReturn::find(Input::get('id', NULL))!=NULL) {
			$filter_so_no = Input::get('filter_so_no', NULL);
			$filter_store_name = Input::get('filter_store_name', NULL);
			$filter_created_at = Input::get('filter_created_at', NULL);
			$filter_status = Input::get('filter_status', NULL);

			//for back
			$sort_back  = Input::get('sort_back', 'so_no');
			$order_back = Input::get('order_back', 'ASC');
			$page_back  = Input::get('page_back', 1);

			// Details
			$sort_detail  = Input::get('sort', 'sku');
			$order_detail = Input::get('order', 'ASC');
			$page_detail  = Input::get('page', 1);

			//Data
			$so_id = Input::get('id', NULL);
			$so_no = Input::get('so_no', NULL);


			$this->data = Lang::get('store_return');
			$this->data['so_status_type'] = Dataset::getTypeWithValue("SR_STATUS_TYPE");
			$this->data['text_empty_results'] = Lang::get('general.text_empty_results');

			$arrParams = array(
					'id'             	=> $so_id,
					'sort'              => $sort_detail,
					'order'             => $order_detail,
					'page'              => $page_detail,
					'so_no'             => $so_no,
					'filter_so_no'      => $filter_so_no,
					'filter_store'      => $filter_store,
					'filter_created_at' => $filter_created_at,
					'filter_status'     => $filter_status,
					'limit' => NULL
				);

			$so_info = StoreReturn::getSOInfo($so_id);
			$results = StoreReturnDetail::getSODetails($so_info->so_no, $arrParams)->toArray();
			$this->data['results'] = $results;

			$pdf = App::make('dompdf');
			$pdf->loadView('store_return.report_detail', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('store_return_detail_' . date('Ymd') . '.pdf');
		}
	}

	public function getSODetails() {
		// Check Permissions
		if (Session::has('permissions')) {
	    	if (!in_array('CanAccessStoreReturn', unserialize(Session::get('permissions'))))  {
				return Redirect::to('store_return');
			} elseif (StoreReturn::find(Input::get('id', NULL))==NULL) {
				return Redirect::to('store_return')->with('error', Lang::get('store_return.error_so_details'));
			}
    	} else {
			return Redirect::to('users/logout');
		}

		$this->data                       = Lang::get('store_return');
		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total']         = Lang::get('general.text_total');
		$this->data['text_select']        = Lang::get('general.text_select');
		$this->data['button_back']        = Lang::get('general.button_back');
		$this->data['button_export']      = Lang::get('general.button_export');

		// URL
		$this->data['url_export']         = URL::to('store_return/export_detail');
		$this->data['url_back']           = URL::to('store_return' . $this->setURL(false, true));
		$this->data['url_assign']         = URL::to('store_return/assign');

		// Message
		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Options
		// Search Options
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SR_STATUS_TYPE");

		$store_list 	  			  = StoreReturn::getStoreList();

		if(CommonHelper::arrayHasValue($store_list)) {
			foreach($store_list as $store){
				$this->data['store_list'][$store] = $store;
			}
		}
		else {
			$this->data['store_list'][] = NULL;
		}

		// Search Filters
		$filter_so_no = Input::get('filter_so_no', NULL);
		$filter_store = Input::get('filter_store', NULL);
		$filter_created_at = Input::get('filter_created_at', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$filter_fullname=Input::get('filter_fullname', NULL);
		$filter_fromStore = Input::get('filter_fromStore', NULL);

		//for sort table column
		$sort               = Input::get('sort', 'so_no');
		$order              = Input::get('order', 'DESC');
		$page               = Input::get('page', 1);

		//for back
		$sort_back  = Input::get('sort_back', 'so_no');
		$order_back = Input::get('order_back', 'ASC');
		$page_back  = Input::get('page_back', 1);

		// Details
		$sort_detail  = Input::get('sort', 'sku');
		$order_detail = Input::get('order', 'ASC');
		$page_detail  = Input::get('page', 1);

		//Data
		$so_id = Input::get('id', NULL);
		$so_no = Input::get('so_no', NULL);
		$fullname = Input::get('fullname', null);
		$created_at = Input::get('created_at', null);
		$fromStore=Input::get('fromStore', Null);


		$this->data['so_info'] = StoreReturn::getSOInfo($so_id);

		$arrParams = array(
						'id'             	=> $so_id,
						'sort'              => $sort_detail,
						'order'             => $order_detail,
						'page'              => $page_detail,
						'so_no'             => $so_no,
						'filter_so_no'      => $filter_so_no,
						'filter_store' => $filter_store,
						'filter_created_at' => $filter_created_at,
						'filter_status'     => $filter_status,
						'filter_fullname'	=> $filter_fullname,
						'filter_fromStore'	=> $filter_fromStore,
						'limit'             => 30
					);


		$results 		= StoreReturnDetail::getSODetails($so_no, $arrParams)->toArray();
		$results_total 	= StoreReturnDetail::getCountSODetails($so_no, $arrParams);
		

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_so_no'      => $filter_so_no,
									'filter_store' => $filter_store,
									'filter_created_at' => $filter_created_at,
									'filter_status'     => $filter_status,
									'sort_back'         => $sort_back,
									'order_back'        => $order_back,
									'page_back'         => $page_back,
									'id'             	=> $so_id,
									'so_no'             => $so_no,
									'sort'              => $sort_detail,
									'order'             => $order_detail,
									'fullname'			=> $fullname,
									'created_at'		=> $created_at,
									'fromStore'			=> $fromStore
								);

		$this->data['store_return'] = Paginator::make($results, $results_total, 30);
		$this->data['store_return_count'] = $results_total;

		$this->data['counter'] 	= $this->data['store_return']->getFrom();

		// Main
		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store'] = $filter_store;
		$this->data['filter_created_at'] = $filter_created_at;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_fullname'] =$filter_fullname;
		$this->data['filter_fromStore']=$filter_fromStore;
		

		//data  na pgkuha sa ibng page
		$this->data['fullname'] = $fullname;
		$this->data['created_at'] =$created_at;
		$this->data['fromStore'] =$fromStore;

		$this->data['sort'] = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page'] = $page_detail;

		// Details
		$this->data['sort_detail']  			= $sort_detail;
		$this->data['order_detail'] 			= $order_detail;
		$this->data['sort_back']    = $sort_back;
		$this->data['order_back']   = $order_back;
		$this->data['page_back']    = $page_back;


		$url = '?filter_so_no=' . $filter_so_no . '&filter_store' . $filter_store;
		$url .= '&filter_created_at=' . $filter_created_at;
		$url .='&filter_fullname='.$filter_fullname;
		$url .='&filter_fromStore='.$filter_fromStore;
		$url .= '&&filter_status=' . $filter_status;
		$url .= '&sort_back=' . $sort_back . '&order_back=' . $order_back . '&page_back=' . $page_back;
		$url .= '&page_detail=' . $page_detail . '&id=' . $so_id . '&so_no=' . $so_no;


		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort_detail=='upc' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort_detail=='short_name' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_delivered_quantity = ($sort_detail=='delivered_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_allocated_quantity = ($sort_detail=='allocated_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_dispatched_quantity = ($sort_detail=='dispatched_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';



		//header table sort order
		$this->data['sort_sku'] = URL::to('store_return/detail' . $url . '&sort=sku&order=' . $order_sku, NULL, FALSE);
		$this->data['sort_upc'] = URL::to('store_return/detail' . $url . '&sort=upc&order=' . $order_upc, NULL, FALSE);
	
		$this->data['sort_short_name'] = URL::to('store_return/detail' . $url . '&sort=short_name&order=' . $order_short_name, NULL, FALSE);

		$this->data['sort_delivered_quantity'] = URL::to('store_return/detail' . $url . '&sort=delivered_quantity&order=' . $order_delivered_quantity, NULL, FALSE);
		$this->data['sort_allocated_quantity'] = URL::to('store_return/detail' . $url . '&sort=allocated_quantity&order=' . $order_allocated_quantity, NULL, FALSE);
		$this->data['sort_dispatched_quantity'] = URL::to('store_return/detail' . $url . '&sort=dispatched_quantity&order=' . $order_dispatched_quantity, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('store_return.detail', $this->data);
	}

	protected function getList() {


		$this->data = Lang::get('store_return');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
		// URL
		$this->data['url_assign'] = URL::to('store_return/assign'. $this->setURL());
		$this->data['url_export'] = URL::to('store_return/export');
		$this->data['url_detail'] = URL::to('store_return/detail' . $this->setURL(true));
		//$this->data['stores']            = Store::lists( 'store_name', 'store_code');
		// Message

		$this->data['error'] = '';
		if (Session::has('error')) {
			$this->data['error'] = Session::get('error');
		}

		$this->data['success'] = '';
		if (Session::has('success')) {
			$this->data['success'] = Session::get('success');
		}

		// Search Options
		$store_list 	  			  = Store::getStoreList1();

		if(CommonHelper::arrayHasValue($store_list)) {
			foreach($store_list as $store_name){
				$this->data['store_list'][$store_name] = $store_name;
			}
		}
		else {
			$this->data['store_list'][] = NULL;
		}


		
		// Search Filters
		$filter_so_no = Input::get('filter_so_no', NULL);
		$filter_store_name = Input::get('filter_store_name', NULL);
		$filter_created_at = Input::get('filter_created_at', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'so_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		//Data link TL number to other page
		$arrParams = array(
						'filter_so_no' 			=> $filter_so_no,
						'filter_store_name' 	=> $filter_store_name,
						'filter_created_at' 	=> $filter_created_at,
						'filter_status' 		=> $filter_status,
						'sort'					=> $sort,
						'order'					=> $order,
						'page'					=> $page,
						'limit'					=> 30
					);



		$results 		= StoreReturn::getSOList($arrParams);


		foreach ($results as $result) {
			$arrParams = array(
							'filter_so_no' 			=> $filter_so_no,
							'filter_store_name' 	=> $filter_store_name,
							'filter_created_at' 	=> $filter_created_at,
							'filter_status' 		=> $filter_status,
							'sort'					=> $sort,
							'order'					=> $order,
							'page'					=> $page,
							'limit'					=> 0
						);
		$details= StoreReturnDetail::getSODetails($result['so_no'], $arrParams)->toArray();
			foreach($details as $detail){
				if($detail['received_qty'] != $detail['delivered_qty'] ){
					$result->discrepancy=1;
					break;	
				}
			}
		}
		$results = $results->toArray();
		$results_total 	= StoreReturn::getCount($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_so_no' 			=> $filter_so_no,
									'filter_store_name' 	=> $filter_store_name,
									'filter_created_at' 	=> $filter_created_at,
									'filter_status' 		=> $filter_status,
									'sort'					=> $sort,
									'order'					=> $order
								);



		$this->data['store_return'] = Paginator::make($results, $results_total, 30);
		$this->data['store_return_count'] = $results_total;

		$this->data['counter'] 	= $this->data['store_return']->getFrom();
		$this->data['so_status_type'] = Dataset::getTypeWithValue("SR_STATUS_TYPE");
	
		//automatic data to givin to other page
		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store_name'] = $filter_store_name;
		$this->data['filter_created_at'] = $filter_created_at;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		

		$url = '?filter_so_no=' . $filter_so_no . '&filter_store_name=' . $filter_store_name;
		$url .= '&filter_created_at=' . $filter_created_at;
		$url .= '&filter_status=' . $filter_status;
		$url .= '&page=' . $page;

		//header ng table sort order (descending or ascending)
		$order_so_no = ($sort=='so_no' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_store = ($sort=='store' && $order=='ASC') ? 'DESC' : 'ASC';
		$order_created_at = ($sort=='created_at' && $order=='ASC') ? 'DESC' : 'ASC';


		$this->data['sort_so_no'] = URL::to('store_return/stocktransfer' . $url . '&sort=so_no&order=' . $order_so_no, NULL, FALSE);
		$this->data['sort_store'] = URL::to('store_return/stocktransfer' . $url . '&sort=store&order=' . $order_store, NULL, FALSE);
		$this->data['sort_created_at'] = URL::to('store_return/stocktransfer' . $url . '&sort=created_at&order=' . $order_created_at, NULL, FALSE);

		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('store_return.stocktransfer', $this->data);
	}

	protected function setURL($forDetail = false, $forBackToList = false) {
		
		// Search Filters
// http://local.ccri.com/picking/list?filter_doc_no=&filter_status=&filter_store=26&sort=doc_no&order=ASC
		$url = '?filter_so_no=' . Input::get('filter_so_no', NULL);
		$url .= '&filter_store_name=' . Input::get('filter_store_name', NULL);
		$url .= '&filter_created_at=' . Input::get('filter_created_at', NULL);
		$url .= '&filter_status=' . Input::get('filter_status', NULL);
		if($forDetail) {
			$url .= '&sort_back=' . Input::get('sort', 'so_no');
			$url .= '&order_back=' . Input::get('order', 'ASC');
			$url .= '&page_back=' . Input::get('page', 1);
		} else {
			if($forBackToList == true) {
				$url .= '&sort=' . Input::get('sort_back', 'so_no');
				$url .= '&order=' . Input::get('order_back', 'ASC');
				$url .= '&page=' . Input::get('page_back', 1);
			} else {
				$url .= '&sort=' . Input::get('sort', 'so_no');
				$url .= '&order=' . Input::get('order', 'ASC');
				$url .= '&page=' . Input::get('page', 1);
			}
		}
		return $url;
	}

	/*protected function setURL() {
		// Search Filters
		$url = '?filter_so_no=' . Input::get('filter_so_no', NULL);
		$url .= '&filter_store=' . Input::get('filter_store', NULL);
		$url .= '&filter_created_at=' . Input::get('filter_created_at', NULL);
		$url .= '&filter_status=' . Input::get('filter_status', NULL);
		$url .= '&sort=' . Input::get('sort', 'so_no');
		$url .= '&order=' . Input::get('order', 'ASC');
		$url .= '&page=' . Input::get('page', 1);

		return $url;
	}*/

	public function assignPilerForm() {
		if (Session::has('permissions')) {
	    	if (!in_array('CanAssignStoreReturn', unserialize(Session::get('permissions'))))  {
	    		return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}
		// Search Filters
		$filter_so_no = Input::get('filter_so_no', NULL);
		$filter_store_name = Input::get('filter_store_name', NULL);
		$filter_created_at = Input::get('filter_created_at', NULL);
		$filter_status = Input::get('filter_status', NULL);

		$sort = Input::get('sort', 'so_no');
		$order = Input::get('order', 'ASC');
		$page = Input::get('page', 1);

		$this->data                    = Lang::get('store_return');
		$this->data['so_no']           = Input::get('so_no');

		$this->data['filter_so_no'] = $filter_so_no;
		$this->data['filter_store_name'] = $filter_store_name;
		$this->data['filter_created_at'] = $filter_created_at;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['page'] = $page;

		$this->data['stock_piler_list'] = $this->getStockPilers();
		$this->data['button_assign']    = Lang::get('general.button_assign');
		$this->data['button_cancel']    = Lang::get('general.button_cancel');
		$this->data['url_back']         = URL::to('store_return'). $this->setURL();
		$this->data['params']           = explode(',', Input::get('so_no'));
		$this->data['info']             = StoreReturn::getInfoBySoNo($this->data['params']);

		$this->layout->content    = View::make('store_return.assign_piler_form', $this->data);
	}

	/**
	* Assign stock piler to purchase order
	*
	* @example  www.example.com/purchase_order/assign_to_piler
	*
	* @param  po_no         int    Purchase order number
	* @param  stock_piler   int    Stock piler id
	* @return Status
	*/
	public function assignToStockPiler() {
		// Check Permissions
		$pilers = implode(',' , Input::get('stock_piler'));


		//get moved_to_reserve id
		$arrParams = array('data_code' => 'SR_STATUS_TYPE', 'data_value'=> 'assigned');
		$storeReturnStatus = Dataset::getType($arrParams)->toArray();

		$arrSoNo = explode(',', Input::get("so_no"));

		foreach ($arrSoNo as $soNo) {
			$arrParams = array(
								'assigned_by' 			=> Auth::user()->id,
								'assigned_to_user_id' 	=> $pilers, //Input::get('stock_piler'),
								'so_status' 			=> $storeReturnStatus['id'], //assigned
								'updated_at' 			=> date('Y-m-d H:i:s')
							);
			StoreReturn::assignToStockPiler($soNo, $arrParams);

			// AuditTrail
			$users = User::getUsersFullname(Input::get('stock_piler'));

			$fullname = implode(', ', array_map(function ($entry) { return $entry['name']; }, $users));

			$data_before = '';
			$data_after = 'Store Return No: ' . $soNo . ' assigned to ' . $fullname;

			$arrParams = array(
							'module'		=> Config::get('audit_trail_modules.store_return'),
							'action'		=> Config::get('audit_trail.assign_store_return'),
							'reference'		=> $soNo,
							'data_before'	=> $data_before,
							'data_after'	=> $data_after,
							'user_id'		=> Auth::user()->id,
							'created_at'	=> date('Y-m-d H:i:s'),
							'updated_at'	=> date('Y-m-d H:i:s')
							);
			AuditTrail::addAuditTrail($arrParams);
			// AuditTrail
		}


		return Redirect::to('store_return/stocktransfer' . $this->setURL())->with('message', Lang::get('store_return.text_success_assign'));

	}

	/**
	* Gets stock piler for drop down
	*
	* @example  $this->getStockPilers();
	*
	* @return array of stock piler and drop down initial text;
	*/
	private function getStockPilers()
	{
		$stock_pilers = array();
		foreach (User::getStockPilerOptions() as $item) {
			$stock_pilers[$item->id] = $item->firstname . ' ' . $item->lastname;
		}
		return array('' => Lang::get('general.text_select')) + $stock_pilers;
	}


	public function closeStoreReturn()
	{
		// Check Permissions
		/*if (Session::has('permissions')) {
	    	if (!in_array('CanClosePurchaseOrders', unserialize(Session::get('permissions'))) || !in_array('CanClosePurchaseOrderDetails', unserialize(Session::get('permissions'))))  {
				return Redirect::to('user/profile');
			}
    	} else {
			return Redirect::to('users/logout');
		}*/

		$soNo        = Input::get("so_no");
		$status       = 'posted'; // closed
		$date_updated = date('Y-m-d H:i:s');

		$status_options = Dataset::where("data_code", "=", "SR_STATUS_TYPE")->get()->lists("id", "data_value");
		$store = StoreReturn::updateStatus($soNo, $status_options['closed']);


		// AuditTrail
		$user = User::find(Auth::user()->id);

		$data_before = '';
		$data_after = 'Store Return No: ' . $soNo . ' posted by ' . $user->username;

		$arrParams = array(
						'module'		=> Config::get("audit_trail_modules.store_return"),
						'action'		=> Config::get("audit_trail.modify_store_return_status"),
						'reference'		=> $soNo,
						'data_before'	=> $data_before,
						'data_after'	=> $data_after,
						'user_id'		=> Auth::user()->id,
						'created_at'	=> date('Y-m-d H:i:s'),
						'updated_at'	=> date('Y-m-d H:i:s')
						);
		AuditTrail::addAuditTrail($arrParams);
		// AuditTrail

		// Add transaction for jda syncing
		$isSuccess = JdaTransaction::insert(array(
			'module' 		=> Config::get('transactions.module_store_return'),
			'jda_action'	=> Config::get('transactions.jda_action_sr_closing'),
			'reference'		=> $soNo
		));
		Log::info(__METHOD__ .' jda transaction dump: '.print_r($isSuccess,true));
		// run daemon command: php app/cron/jda/classes/receive_po.php
		if( $isSuccess )
		{
			$daemon = "classes/store_return.php {$soNo}";
			CommonHelper::execInBackground($daemon,'store_return');
		}

		return Redirect::to('store_return' . $this->setURL())->with('message', Lang::get('store_return.text_success_posted'));
	}
}
