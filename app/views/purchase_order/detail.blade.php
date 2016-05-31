<div class="control-group">

	<a href="{{ $url_back }}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>

	
	<!--
	@if ( CommonHelper::valueInArray('CanExportPurchaseOrders', $permissions) )
	<a class="btn btn-info btn-darkblue" id="exportList">{{ $button_export }}</a>
	@endif

	@if ( CommonHelper::valueInArray('CanAccessPurchaseOrders', $permissions) )
		@if($po_info->data_display !== 'Open' && $po_info->data_display !== 'Assigned')
			<a style="width: 145px;" class="btn" title="{{ $text_assigned }}" disabled="disabled">{{ $text_assigned }}</a>
		@else
			<!-- <a style="width: 145px;" href="#myModal" role="button" class="btn btn-success assignPO" title="{{ $button_assign_to_stock_piler }}" data-toggle="modal" data-id="{{ $po_info->purchase_order_no }}">{{ $button_assign_to_stock_piler }}</a> -->
	<!--
			<a style="width: 145px;" role="button" class="btn btn-info btn-darkblue assignPO" title="{{ $button_assign_to_stock_piler }}" data-id="{{ $po_info->purchase_order_no }}">{{ $button_assign_to_stock_piler }}</a>
		@endif
	@endif

	@if ( CommonHelper::valueInArray('CanAccessPurchaseOrders', $permissions) )
		@if($po_info->data_display === 'Posted')
			<a style="width: 70px;" disabled="disabled" class="btn btn-danger">{{ $text_posted_po }}</a>
		@elseif ($po_info->data_display === 'Done')
			<a style="width: 70px;" class="btn btn-success closePO" data-id="{{ $po_info->purchase_order_no }}">{{ $button_close_po }}</a>
		@else
			<a style="width: 70px;" disabled="disabled" class="btn">{{ $button_close_po }}</a>

		@endif
	-->

<!--
		{{ Form::open(array('url'=>'purchase_order/close_po', 'id' => 'closePO_', 'style' => 'margin: 0px;')) }}
			{{ Form::hidden('po_no', '') }}
			{{ Form::hidden('invoice_no') }}
			{{ Form::hidden('invoice_amount') }}
			{{ Form::hidden('filter_po_no', $filter_po_no) }}
			{{ Form::hidden('filter_receiver_no', $filter_receiver_no) }}
			{{-- Form::hidden('filter_supplier', $filter_supplier) --}}
			{{ Form::hidden('filter_entry_date', $filter_entry_date) }}
			{{ Form::hidden('filter_stock_piler', $filter_stock_piler) }}
			{{ Form::hidden('filter_status', $filter_status) }}
	  		{{ Form::hidden('sort_back', $sort_back) }}
			{{ Form::hidden('order_back', $order_back) }}
			{{ Form::hidden('page_back', $page_back) }}
			{{ Form::hidden('sort', $sort_detail) }}
			{{ Form::hidden('order', $order_detail) }}
			{{ Form::hidden('page', $page_detail) }}
			{{ Form::hidden('module', 'purchase_order_detail') }}
			{{ Form::hidden('receiver_no', $po_info->receiver_no) }}
  		{{ Form::close() }}

	@endif
	-->
</div>

<!-- PO Detail -->
<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_po_details }}</h3>
    </div>
    <!-- /widget-header -->
	<div class="widget-content">
	   <div class="row-fluid stats-box">
	      	<div class="span4">
	        	<div>
		        	<span class="left-pane">{{ $label_purchase_no }}</span>
		        	<span class="right-pane">{{ Form::text('purchase_order_no', $po_info->purchase_order_no, array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        	<span class="left-pane">{{ $label_stock_piler }}</span>
		        	<span class="right-pane">{{ Form::text('name', $po_info->firstname .' '.$po_info->lastname, array('readonly' => 'readonly')) }}</span>
		        </div>
	      	</div>

	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">{{ $label_entry_date.' :' }}</span>
		        	<span class="right-pane">{{ Form::text('entry_date', date('M d, Y', strtotime($po_info->created_at)), array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        	<span class="left-pane">{{ $label_status }}</span>
		        	<span class="right-pane">{{ Form::text('data_display', $po_info->data_display, array('readonly' => 'readonly')) }}</span>
		        </div>
		        
	      </div>

	      <div class="span4">
				
		       <div>
		        	<span class="left-pane">Division :</span>
		        	<span class="right-pane">{{ Form::text('Division_Name', $po_info->Division_Name, array('readonly' => 'readonly')) }}</span>
		        </div>
	      </div>
	   </div>
	 </div>
</div>

<div class="clear">
	<div class="div-paginate">
		@if(CommonHelper::arrayHasValue($purchase_orders) )
		    <h6 class="paginate">
				<span>{{ $purchase_orders->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_po_contents }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $purchase_orders_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive" onkeypress="return isNumber(event)">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_sku }}" class="@if( $sort_detail=='sku' ) {{ $order_detail }} @endif">{{ $col_sku }}</a></th>
						<th><a href="{{ $sort_upc }}" class="@if( $sort_detail=='upc' ) {{ $order_detail }} @endif">{{ $col_upc }}</a></th>
						<th><a href="{{ $sort_short_name }}" class="@if( $sort_detail=='short_name' ) {{ $order_detail }} @endif">{{ $col_short_name }}</a></th>
						<!--<th>{{ $col_expiry_date }}</th> -->
						<th><a href="{{ $sort_expected_quantity }}" class="@if( $sort_detail=='expected_quantity' ) {{ $order_detail }} @endif">{{ $col_expected_quantity }}</a></th>
						<th><a href="{{ $sort_received_quantity }}" class="@if( $sort_detail=='received_quantity' ) {{ $order_detail }} @endif">{{ $col_received_quantity }}</a></th>
						<th> VARIANCE </th>
						<th> NOT IN PO </th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($purchase_orders) )
				<tr class="font-size-13">
					<td colspan="7" class="align-center">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $purchase_orders as $po )
					<tr class="font-size-13"
					@if ( $po->quantity_ordered !== $po->quantity_delivered )
						style="background-color:#F29F9F"
					@endif
					>
						<td>{{ $counter++ }}</td>
						<td>{{ $po->sku }}</td>
						<td>{{ $po->upc }}</td>
						<td>{{ $po->short_description }}</td>
						<td>{{ $po->quantity_ordered }}</td>
						@if ( $po_info->data_display <> 'Posted')
							<td class="align-center" style="padding-top: 20px;" style="padding-top: 20px;">
								{{ Form::open(array('url'=>'purchase_order/updateqty', 'class'=>'form-signin', 'id'=>'form-purchase-order', 'role'=>'form', 'method' => 'get')) }}
									{{ Form::hidden('sku', $po->upc) }}
									{{ Form::hidden('receiver_no',  Input::get('receiver_no', NULL)) }}
									{{ Form::hidden('division',  Input::get('division', NULL)) }}
									{{ Form::text('quantity', $po->quantity_delivered, array('class'=>'form-signin', 'placeholder'=>'', 'id'=>"$po_info->sku")) }}
								{{ Form::close() }}
							</td>
						@else
							<td>{{ $po->quantity_delivered }}</td>
						@endif

						
						<td>{{ $po->quantity_delivered - $po->quantity_ordered  }}</td>
						<td>No</td>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($purchase_orders) )
    <h6 class="paginate">
		<span>{{ $purchase_orders->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

	<!-- Button to trigger modal -->
	<!-- Modal -->
	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		{{ Form::open(array('url'=>'purchase_order/assign_to_piler', 'id'=>'form-assign', 'class'=>'form-horizontal', 'style' => 'margin: 0px;', 'role'=>'form')) }}
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">{{ $heading_title_assign_po }}</h3>
  		</div>
  		<div class="modal-body">
  			<fieldset>
				<div class="control-group">
					<label class="control-label" for="stock_piler">{{ $entry_stock_piler }}</label>
					<div class="controls">
						{{ Form::select('stock_piler', $stock_piler_list, $po_info->assigned_to_user_id) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

				<div class="control-group">
					<label class="control-label" for="po_no">{{ $entry_purchase_no }}</label>
					<div class="controls">
						{{ Form::text('po_no', '', array('id' => 'po_no', 'readonly' => 'readonly')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
			</fieldset>
  		</div>
  		<div class="modal-footer">
  			<button class="btn btn-primary" id="btn-assign">{{ $button_assign }}</button>
			<button class="btn" data-dismiss="modal" aria-hidden="true">{{ $button_cancel }}</button>
  		</div>
  		{{ Form::hidden('filter_po_no', $filter_po_no) }}
		{{ Form::hidden('filter_receiver_no', $filter_receiver_no) }}
		{{-- Form::hidden('filter_supplier', $filter_supplier) --}}
		{{ Form::hidden('filter_entry_date', $filter_entry_date) }}
		{{ Form::hidden('filter_stock_piler', $filter_stock_piler) }}
		{{ Form::hidden('filter_status', $filter_status) }}
  		{{ Form::hidden('sort_back', $sort_back) }}
		{{ Form::hidden('order_back', $order_back) }}
		{{ Form::hidden('page_back', $page_back) }}

		{{ Form::hidden('sort', $sort_detail) }}
		{{ Form::hidden('order', $order_detail) }}
		{{ Form::hidden('page', $page_detail) }}
		{{ Form::hidden('module', 'purchase_order_detail') }}
		{{ Form::hidden('receiver_no', $po_info->receiver_no) }}

  		{{ Form::close() }}
	</div>

	<!--modal for close po-->
	<div id="closePoModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="closePoModalLabel" aria-hidden="true">
		 <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	        <h4 class="modal-title">{{$entry_invoice}}</h4>
	      </div>
	      <div class="modal-body">
	        {{ Form::open(array('role'=> 'form', "class"=> "form-horizontal"))}}
	        <div class="form-group">
	        	{{ Form::label('invoice_no',$label_invoice_number, array("style" => "margin-right:10px","class" => "col-sm-2 control-label"))}}
	        	<div class="col-sm-10">
	        		{{ Form::text('invoice_no', '', array('id'=>'invoiceNoInput','class'=> "form-control"))}}
	        	</div>
			</div>
			<br/>
			<div class="form-group">
	        	{{ Form::label('invoice_amount',$label_invoice_amount, array("style" => "margin-right:10px","class" => "col-sm-2 control-label"))}}
	        	<div class="col-sm-10">
	        		{{ Form::text('invoice_amount', '', array('required', 'id'=>'invoiceAmountInput','class'=> "form-control"))}}
	        	</div>
			</div>
	        {{ Form::close()}}
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-primary" id="closePOModalButton">Close PO</button>
	      </div>
	    </div><!-- /.modal-content -->
	</div>

	<!-- end of modal for close po-->

	<!-- /widget-content -->
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Assign PO
    /*$('.assignPO').click(function() {
    	var purchase_no = $(this).data('id');

    	$('#po_no').val(purchase_no);
    });*/





function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
return true;
}
										  

	$('.assignPO').click(function() {

		var po_no = $(this).data('id');
		// http://local.ccri.com/purchase_order/assign
		location = "{{ $url_assign }}" + '&module=purchase_order_detail&receiver_no={{ $receiver_no }}&po_no=' + po_no;
    });

    // Close PO
    /*$('.closePO').click(function() {
    	var purchase_no = $(this).data('id');

    	$("#closePoModal").modal('show');
    	$("#closePoModal").attr('data-id', purchase_no);
    });

    $('#closePOModalButton').click(function(){
    	var purchase_no = $("#closePoModal").data('id');
    	if(($('#invoiceNoInput').val() == '') || ($('#invoiceAmountInput').val() == '')) {
    		alert('Please input required fields.');
    	} else {
    		if(!isNaN($('#invoiceAmountInput').val())) {
    			if(purchase_no == '') {
		    		alert('Error, you have not chosen a PO to close.');
		    	} else {
		    		$('#closePO_' + purchase_no + ' input[name="invoice_no"]').val($('#invoiceNoInput').val());
		    		$('#closePO_' + purchase_no + ' input[name="invoice_amount"]').val($('#invoiceAmountInput').val());
		    		$('#closePO_' + purchase_no).submit();
		    	}
    		} else {
    			alert('Invoice amount should be a valid number.');
    		}
    	}
    });*/

	$('.closePO').click(function() {
    	var purchase_no = $(this).data('id');

    	var answer = confirm('Are you sure you want to close this PO?');
   		if (answer) {
	    	$('#closePO_' + purchase_no).submit();
    	} else {
			return false;
		}

    });


    //clear data id of closePOModal on close of closePoModal
    $('#closePoModal .close').click(function(){
    	$("#closePoModal").attr('data-id', '');
    });

    // Submit Assign PO
    $('#btn-assign').click(function() {
    	stockpiler = $('select[name=\'stock_piler\']').val();

    	if (stockpiler == '') {
    		alert('{{ $error_assign_po }}');
    		return false;
    	} else {
    		$('#form-assign').submit();
    	}
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

    	url += '?receiver_no=' + encodeURIComponent('{{ $po_info->receiver_no }}');
		url += '&sort=' + encodeURIComponent('{{ $sort_detail }}');
		url += '&order=' + encodeURIComponent('{{ $order_detail }}');

      	location = "{{ $url_export }}" + url;
    });
});
</script>