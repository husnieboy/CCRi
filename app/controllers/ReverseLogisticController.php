<?php

class ReverseLogisticController extends BaseController {
private $data = array();
protected $layout = "layouts.main";



	public function __construct() {
    	date_default_timezone_set('Asia/Manila');
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('auth', array('only'=> array('Dashboard')));
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
public function exportDetailsCSV() {
		///Check Permissions
	

		if (Reverselogistic::find(Input::get('id', NULL))!=NULL) {
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
			$pdf->loadView('reverse_logistic.report_detail', $this->data)->setPaper('a4')->setOrientation('landscape');
			// return $pdf->stream();
			return $pdf->download('reverse_logistic_detail_' . date('Ymd') . '.pdf');
		}
	}
	public function exportCSV() {
		// Check Permissions
	
		$this->data = Lang::get('reverse_logistic');
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
	
	}

	public function index() {
		// Check Permissions
		
		$this->getList();
	}


	public function getList()
	{
	

		$this->data = Lang::get('reverselogistic');

		$this->data['text_empty_results'] = Lang::get('general.text_empty_results');
		$this->data['text_total'] = Lang::get('general.text_total');
		$this->data['text_select'] = Lang::get('general.text_select');
		$this->data['button_search'] = Lang::get('general.button_search');
		$this->data['button_clear'] = Lang::get('general.button_clear');
		$this->data['button_export'] = Lang::get('general.button_export');
	
		//$this->data['url_assign'] = URL::to('reverse_logistic/assign'. $this->setURL());
		//$this->data['url_export'] = URL::to('reverse_logistic/export');
		//$this->data['url_detail'] = URL::to('reverse_logistic/detail' . $this->setURL(true));

		

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
		$store_list 	  			  = Store::getStoreList2();

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


		$results 		= reverselogistic::getSOList($arrParams);


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
		$details= ReverselogisticDetails::getSODetails($result['so_no'], $arrParams)->toArray();
		foreach($details as $detail){
				if($detail['received_qty'] != $detail['delivered_qty'] ){
					$result->discrepancy=1;
					break;	
				}
			}
		}
		$results = $results->toArray();
		$results_total 	= reverselogistic::getCount($arrParams);

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_so_no' 			=> $filter_so_no,
									'filter_store_name' 		=> $filter_store_name,
									'filter_created_at' 	=> $filter_created_at,
									'filter_status' 		=> $filter_status,
									'sort'					=> $sort,
									'order'					=> $order
								);

		$this->data['store_return'] = Paginator::make($results, $results_total, 30);
		$this->data['store_return_count'] = $results_total;

		$this->data['counter'] 	= $this->data['store_return']->getFrom();
		
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


		// Permissions
		$this->data['permissions'] = unserialize(Session::get('permissions'));

		$this->layout->content = View::make('reverse_logistic.list', $this->data);
	}


	public function getSODetails() {
		// Check Permissions
	
		// URL

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
		$filter_store_name = Input::get('filter_store_name', NULL);
		$filter_created_at = Input::get('filter_created_at', NULL);
		$filter_status = Input::get('filter_status', NULL);
		$filter_fullname=Input::get('filter_fullname', NULL);
		$filter_fromStore = Input::get('filter_fromStore', NULL);

		//for sort table column
		$sort               = Input::get('sort', 'so_no');
		$order              = Input::get('order', 'DESC');
		$page               = Input::get('page', 1);

		//for back table  column
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
		$DataDisplay=Input::get('DataDisplay', Null);

		//$this->data['so_info'] = reverselogistic::getSOInfo($so_id);

		$arrParams = array(
						'id'             	=> $so_id,
						'sort'              => $sort_detail,
						'order'             => $order_detail,
						'page'              => $page_detail,
						'so_no'             => $so_no,
						'filter_so_no'      => $filter_so_no,
						'filter_store_name' => $filter_store_name,
						'filter_created_at' => $filter_created_at,
						'filter_status'     => $filter_status,
						'filter_fullname'	=> $filter_fullname,
						'filter_fromStore'	=> $filter_fromStore,
						'limit'             => 30
					);


		$results 		= ReverselogisticDetails::getSODetails($so_no, $arrParams)->toArray();
		$results_total 	= ReverselogisticDetails::getCountSODetails($so_no, $arrParams);
		

		// Pagination
		$this->data['arrFilters'] = array(
									'filter_so_no'      => $filter_so_no,
									'filter_store_name' => $filter_store_name,
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
		$this->data['filter_store_name'] = $filter_store_name;
		$this->data['filter_created_at'] = $filter_created_at;
		$this->data['filter_status'] = $filter_status;
		$this->data['filter_fullname'] =$filter_fullname;
		$this->data['filter_fromStore']=$filter_fromStore;


		//data  na pgkuha sa ibng page
		$this->data['so_no'] = $so_no;
		$this->data['fullname'] = $fullname;
		$this->data['created_at'] =$created_at;
		$this->data['fromStore'] =$fromStore;
		
		$this->data['filter_status'] = $filter_status;
		$this->data['sort'] = $sort_detail;
		$this->data['order'] = $order_detail;
		$this->data['page'] = $page_detail;

		// Details
		$this->data['sort_detail']  			= $sort_detail;
		$this->data['order_detail'] 			= $order_detail;
		$this->data['sort_back']    = $sort_back;
		$this->data['order_back']   = $order_back;
		$this->data['page_back']    = $page_back;


		$url = '?filter_so_no=' . $filter_so_no . '&filter_store_name' . $filter_store_name;
		$url .= '&filter_created_at=' . $filter_created_at;
		$url .='&filter_fullname='.$filter_fullname;
		$url .='&filter_fromStore='.$filter_fromStore;
		$url .= '&filter_status=' . $filter_status;
		$url .= '&sort_back=' . $sort_back . '&order_back=' . $order_back . '&page_back=' . $page_back;
		$url .= '&page_detail=' . $page_detail . '&id=' . $so_id . '&so_no=' . $so_no;


		$order_sku = ($sort_detail=='sku' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_upc = ($sort_detail=='upc' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_short_name = ($sort_detail=='short_name' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_delivered_quantity = ($sort_detail=='delivered_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_allocated_quantity = ($sort_detail=='allocated_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';
		$order_dispatched_quantity = ($sort_detail=='dispatched_quantity' && $order_detail=='ASC') ? 'DESC' : 'ASC';



		//header table sort order

		// Permissions

		

		$this->layout->content = View::make('reverse_logistic.detail', $this->data);
	}
	public function assignPilerForm() {
	
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
		$this->data['info']             = Reverselogistic::getInfoBySoNo($this->data['params']);

	$this->layout->content    = View::make('reverse_logistic.assign_piler_form', $this->data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
