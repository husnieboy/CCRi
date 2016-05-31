<?php
ini_set('max_execution_time', 0);
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@showIndex');
Route::controller('users', 'UsersController');

Route::group(array('prefix'=>'api/v1'), function()
{
	//Route::controller('user', 'ApiUsers');
	Route::post('user', 'Api\UserController@validateUser');
});
//stores api
Route::group(array('prefix'=>'api/v1'), function()
{
	Route::controller('store_user', 'ApiStoreUsers');
	Route::get('store_receive', 'ApiStoreSO@getSO');
	Route::get('store_receive/detail/{so_no}', 'ApiStoreSO@getSoDetails');
	Route::post('store_receive/accept/{so_no}', 'ApiStoreSO@postAcceptSo');

});
//end stores api

Route::group(array("before"=>"auth.basic"), function()
{
	Route::get('shipping/list', 'shippingController@index');

    Route::get('shipping/assigned', 'shippingController@assignPilerForm');
    Route::post('shipping/assigned_to', 'shippingController@assignToPiler');
    Route::get('load/load_details','BoxController@index');
    Route::get('load/box_content', 'BoxController@getBoxDetails');
  //  Route::get('box_list_details/{id}', array('as' => 'box_list_details', 'uses' => 'BoxController@getListBox'));

	Route::get('purchase_order', 'PurchaseOrderController@showIndex');
	Route::post('purchase_order/assign_to_piler', 'PurchaseOrderController@assignToStockPiler');
	Route::post('purchase_order/close_po', 'PurchaseOrderController@closePO');
	Route::get('purchase_order/export', 'PurchaseOrderController@exportCSV');
	Route::get('purchase_order/export_detail', 'PurchaseOrderController@exportDetailsCSV');
	Route::get('purchase_order/export_backorder', 'PurchaseOrderController@exportBackorder');
	Route::post('purchase_order/reopen', 'PurchaseOrderController@reopen');
	Route::get('purchase_order/assign', 'PurchaseOrderController@assignPilerForm');
	Route::get('purchase_order/pulljda', 'PurchaseOrderController@pullJDA');

	Route::get('purchase_order/get_division', 'PurchaseOrderController@getDivisionv2');

	

	Route::get('purchase_order/division', 'PurchaseOrderController@showdivision');
	Route::get('purchase_order/discrepansy', 'PurchaseorderController@discrepansy');
	Route::get('purchase_order/updateqty', 'PurchaseOrderController@updateqty');

	Route::get('purchase_order/detail', 'PurchaseOrderController@getPODetails');
	Route::get('purchase_order/sync_to_mobile', 'PurchaseOrderController@synctomobile');


	Route::get('store_order', 'StoreOrderController@showIndex');
	Route::get('store_order/detail', 'StoreOrderController@getSODetails');
	Route::get('store_order/export', 'StoreOrderController@exportCSV');
	Route::get('store_order/export_detail', 'StoreOrderController@exportDetailsCSV');
	Route::get('store_order/export_mts', 'StoreOrderController@exportMTSCSV');
	Route::post('store_order/generate_picklist', 'StoreOrderController@generatePicklist');
	Route::get('store_order/mts_detail', 'StoreOrderController@getMtsDetails');


	Route::get('store_return/detail', 'StoreReturnController@getSODetails');
	Route::get('store_return/assign', 'StoreReturnController@assignPilerForm');
	Route::post('store_return/assign_to_piler', 'StoreReturnController@assignToStockPiler');
	Route::get('store_return/export', 'StoreReturnController@exportCSV');
	Route::get('store_return/export_detail', 'StoreReturnController@exportDetailsCSV');
	Route::post('store_return/close', 'StoreReturnController@closeStoreReturn');
	Route::get('store_return/stocktransfer','StoreReturnController@showIndex');


Route::get('reverse_logistic/list', [
	'as' => 'reverse_logistic/list',
	'uses' => 'ReverseLogisticController@index'
	]);
Route::get('reverse_logistic/assign', [
	'as' => 'reverse_logistic/assign',
	'uses'=> 'ReverseLogisticController@assignPilerForm'
	]);
Route::get('reverse_logistic/detail', [
	'as' => 'reverse_logistic/detail',
	'uses' => 'ReverseLogisticController@getSODetails'
	]);
Route::get('reverse_logistic/export',[
	'as' => 'reverse_logistic/export',
	'uses'=> 'ReverseLogisticController@exportCSV'
	]);
Route::get('reverse_logistic/export_detail',[
	'as' => 'reverse_logistic/export_detail',
	'uses'=> 'ReverseLogisticController@exportDetailsCSV'
	]);


	Route::get('box/list', 'BoxController@index');
	Route::get('box/detail', 'BoxController@getBoxDetails');
	Route::get('box/create', 'BoxController@createBox');
	Route::post('box/create', 'BoxController@postCreateBox');
	Route::get('box/update', 'BoxController@updateBox');
	Route::post('box/update', 'BoxController@postUpdateBox');
	Route::get('box/export', 'BoxController@exportBoxes');
	Route::get('box/export_detail', 'BoxController@exportDetailsCSV');
	Route::get('box/delete', 'BoxController@deleteBoxes');
	Route::post('box/load', 'BoxController@loadBoxes');
	Route::post('box/new/load', 'BoxController@generateLoadCode');
    Route::get('box/assign', 'BoxController@assignPilerForm');
    Route::post('box/assign_to_piler', 'BoxController@assignToStockPiler');

	Route::get('letdown', 'LetDownController@showIndex');
	Route::get('letdown/detail', 'LetDownController@getLetDownDetails');
	Route::get('letdown/export', 'LetDownController@exportCSV');
	Route::get('letdown/export_detail', 'LetDownController@exportDetailsCSV');
	Route::post('letdown/close_letdown', 'LetDownController@closeLetdown');
	Route::get('letdown/locktags', 'LetDownController@getLockTagList');
	Route::get('letdown/locktags_detail', 'LetDownController@getLockTagDetail');
	Route::post('letdown/unlock', 'LetDownController@unlockLetdownTag');

	Route::get('picking/list', 'PicklistController@showIndex');
	Route::get('picking/detail', 'PicklistController@getPicklistDetails');
	Route::get('picking/export', 'PicklistController@exportCSV');
	Route::get('picking/export_detail', 'PicklistController@exportDetailCSV');
	Route::get('picking/update', 'PicklistController@updatePicklist');
	Route::get('picking/locktags', 'PicklistController@getLockTagList');
	Route::get('picking/locktags_detail', 'PicklistController@getLockTagDetail');
	Route::post('picking/unlock', 'PicklistController@unlockPicklistTag');
	Route::post('picking/change_to_store', 'PicklistController@changeToStore');
	Route::post('picking/new/load', 'PicklistController@generateLoadCode');
	Route::post('picking/load', 'PicklistController@loadPicklistDocuments');
	Route::get('picking/assign', 'PicklistController@assignPilerForm');
	Route::post('picking/assign_to_piler', 'PicklistController@assignToStockPiler');
	Route::post('picking/close', 'PicklistController@closePicklist');
    Route::get('picking/printboxlabel/{doc_num}', 'PicklistController@printBoxLabel');

	Route::get('inventory', 'InventoryController@showIndex');
	Route::get('inventory/export', 'InventoryController@exportCSV');
	Route::get('inventory/detail', 'InventoryController@getDetails');
	Route::get('inventory/export_detail', 'InventoryController@exportDetailsCSV');

	Route::get('products', 'ProductListController@showIndex');
	Route::get('products/export', 'ProductListController@exportCSV');
	Route::get('products/department', 'ProductListController@getSubDepartments');

	Route::get('slots', 'SlotListController@showIndex');
	Route::get('slots/export', 'SlotListController@exportCSV');

	Route::get('stores', 'StoreController@showIndex');
	Route::get('stores/export', 'StoreController@exportCSV');

	Route::get('vendors', 'VendorController@showIndex');
	Route::get('vendors/export', 'VendorController@exportCSV');

	Route::get('user', 'UsersController@showIndex');
	Route::get('user/insert', 'UsersController@insertDataForm');
	Route::post('user/insertData', 'UsersController@insertData');
	Route::get('user/update', 'UsersController@updateDataForm');
	Route::post('user/updateData', 'UsersController@updateData');
	Route::get('user/password', 'UsersController@updatePasswordForm');
	Route::post('user/updatePassword', 'UsersController@updatePassword');
	Route::post('user/delete', 'UsersController@deleteData');
	Route::get('user/export', 'UsersController@exportCSV');

	Route::get('user/profile', 'UsersController@updateProfileForm');
	Route::get('user/change_password', 'UsersController@updateProfilePasswordForm');

	Route::get('user_roles', 'UserRolesController@showIndex');
	Route::get('user_roles/insert', 'UserRolesController@insertDataForm');
	Route::post('user_roles/insertData', 'UserRolesController@insertData');
	Route::get('user_roles/update', 'UserRolesController@updateDataForm');
	Route::post('user_roles/updateData', 'UserRolesController@updateData');
	Route::post('user_roles/delete', 'UserRolesController@deleteData');

	Route::get('audit_trail', 'AuditTrailController@showIndex');
	Route::get('audit_trail/insert', 'AuditTrailController@insertData');
	Route::get('audit_trail/export', 'AuditTrailController@exportCSV');
	Route::get('audit_trail/archive_logs', 'AuditTrailController@archive');

	Route::get('settings', 'SettingsController@showIndex');
	Route::get('settings/insert', 'SettingsController@insertDataForm');
	Route::post('settings/insertData', 'SettingsController@insertData');
	Route::get('settings/update', 'SettingsController@updateDataForm');
	Route::post('settings/updateData', 'SettingsController@updateData');
	Route::post('settings/delete', 'SettingsController@deleteData');

	Route::get('load/list', 'LoadController@showIndex');
	Route::get('load/export', 'LoadController@exportCSV');
	Route::post('load/ship', 'LoadController@shipLoad');
	Route::get('load/print/{loadCode}', 'LoadController@printLoad');
	Route::get('load/print/update/{loadCode}', 'LoadController@updatePrintLoad');
    Route::get('load/printpacklist/{loadCode}', 'LoadController@printPackingList');
    Route::get('load/printpacklist/update/{loadCode}', 'LoadController@updatePrintPackingList');
    Route::get('load/printloadingsheet/{loadCode}', 'LoadController@printLoadingSheet');

	Route::get('purchase_order/unlisted', 'UnlistedController@showIndex');
	Route::get('unlisted/export', 'UnlistedController@exportCSV');

	Route::get('expiry_items', 'ExpiryItemsController@showIndex');
	Route::get('expiry_items/export', 'ExpiryItemsController@exportCSV');
});
Route::group(array('prefix'=>'api/v1'), function()
{
	Route::post('oauth/access_token', function()
	{
	    // return AuthorizationServer::performAccessTokenFlow();
	    return Response::json(Authorizer::issueAccessToken());
	});
});
//Route::group(array('prefix'=>'api/v1', 'before'=>'oauth|auth.piler'), function()
Route::group(array('prefix'=>'api'), function()
{
	Route::get('RPoList/{piler_id}','ApiPurchaseOrder@RPolist');
	Route::get('RPoListDetailUpdate/{receiver_no}/{division}/{upc}/{quantity_delivered}','ApiPurchaseOrder@RPoListDetailUpdate');
	Route::get('RPoListDetail/{receiver_no}/{division_id}','ApiPurchaseOrder@RPolistDetail');
	Route::get('RPoUpdatestatus/{receiver_no}/{division_id}','ApiPurchaseOrder@UpdateApiRPoSlot');

	Route::get('products', 'ApiProductList@index');
	Route::get('products/upc_exist', 'ApiProductList@checkUpc');
	//purchase order apis
	Route::get('purchase_order/{piler_id}', 'ApiPurchaseOrder@index');
	Route::post('purchase_order/{po_order_no}', 'ApiPurchaseOrder@savedReceivedPO');
	Route::get('purchase_order/details/{receiver_no}', 'ApiPurchaseOrder@getDetails');
	Route::post('purchase_order/change_status/{po_order_no}', 'ApiPurchaseOrder@updateStatus');
	Route::post('purchase_order/not_in_po/{po_order_no}', 'ApiPurchaseOrder@notInPo');
	Route::post('purchase_order/unlisted/{po_order_no}', 'ApiPurchaseOrder@unlisted');

	//reserved zone
	Route::get('upc', 'ApiReserveZone@index');
	Route::post('upc/reserve_zone/{slot_id}', 'ApiReserveZone@putToReserve');
	//slot master
	Route::get('slots/list', 'ApiSlotMasterList@index');

	//letdown api
	Route::get('letdown/list', 'ApiLetdown@getLetDownLists');
	Route::get('letdown/list/detail/{sku}', 'ApiLetdown@getLetdownDetail');
	Route::post('letdown/detail', 'ApiLetdown@postLetdownDetail');

	//picklist
	Route::get('picking/list', 'ApiPicklist@getPickingLists');
	Route::get('picking/detail/{sku_or_store}', 'ApiPicklist@getPickingDetail');
	Route::post('picking/detail', 'ApiBox@postToPicklistToBox');
	Route::post('picking/done/{sku_or_store}', 'ApiPicklist@postDone');


	//boxing
	Route::get('boxes/{store_code}', 'ApiBox@getBoxesByStore');
	Route::post('boxes/create', 'ApiBox@postCreateBox');
	Route::post('boxes/load', 'ApiLoads@loadBoxes');
	Route::get('boxes', 'ApiBox@getAllBoxes');

	//load
	Route::post('loads/create', 'ApiLoads@generateLoadCode');
	Route::get('loads/list', 'ApiLoads@getList');

	//get status types
	Route::get('status/values', 'HomeController@getStatusValues');

	//department
	Route::get('department/brands', 'ApiDepartment@getBrands');
	Route::get('department/divisions', 'ApiDepartment@getDivisions');

	//audit trail
	Route::post('audittrail/insert', 'ApiAuditTrail@insertRecord');

	//slots
	Route::get('slot/is_exist', 'ApiSlots@getIsSlotExist');

	//store return
	Route::get('store_return/list', 'ApiStoreReturn@getStoreReturnList');
	Route::get('store_return/details/{soNo}', 'ApiStoreReturn@getStoreReturnDetail');
	Route::post('store_return/save', 'ApiStoreReturn@postSaveDetail');
	Route::post('store_return/change_status/{soNo}', 'ApiStoreReturn@updateStatus');
	Route::post('store_return/not_in_transfer/{soNo}', 'ApiStoreReturn@notInTransfer');

	Route::post('inter_transfer/add', 'ApiInterTransfer@insertRecord');

	//manual move
	Route::get('manual_move/get_info', 'ApiManualMove@getInfo');
	Route::post('manual_move/save', 'ApiManualMove@insertRecord');
});

Route::group(array('prefix'=>'api/v1', 'before'=>'oauth'), function()
{
	//store order api
	Route::get('store_order/loads/{store_code}', 'ApiStoreOrder@getLoads');
	Route::get('store_order/product/list/{store_code}', 'ApiStoreOrder@getProductList');
	Route::post('store_order/receive', 'ApiStoreOrder@postReceive');
	Route::post('store_order/close', 'ApiStoreOrder@closeStoreOrders');
});

Route::group(array('prefix'=>'api/v2', 'before'=>'oauth|auth.piler'), function()
{
	Route::get('letdown/list', 'ApiLetdown@getLetDownListsv2');
	Route::get('letdown/details/{move_doc_number}', 'ApiLetdown@getLetdownDetailv2');
	Route::post('letdown/save', 'ApiLetdown@postLetdownDetailv2');

	Route::get('picking/list', 'ApiPicklist@getPickingListsv2');
	Route::get('picking/details/{move_doc_number}', 'ApiPicklist@getPickingDetailv2');
	Route::post('picking/save', 'ApiPicklist@postPickingDetailv2');
	Route::post('picking/change_status/{docNo}', 'ApiPicklist@updateStatus');

    Route::get('boxes/{store_code}/{userid}', 'ApiBox@getBoxesByStoreUserId'); // with username
    Route::post('boxes/createbyuserid', 'ApiBox@postCreateBoxByUserId'); // with username
});
