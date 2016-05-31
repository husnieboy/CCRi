@if( CommonHelper::arrayHasValue($error) )
    <div class="alert alert-danger">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ $error }}
    </div>
@endif

@if( CommonHelper::arrayHasValue($success) )
    <div class="alert alert-success">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ $success }}
    </div>
@endif

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
            {{ Form::open(array('url'=>'purchase_order', 'class'=>'form-signin', 'id'=>'form-purchase-order', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_purchase_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_po_no', $filter_po_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_po_no")) }}
				        	</span>
				        </div>
				<!--
				        <div>
				        	<span class="search-po-left-pane">{{ $label_receiver_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_receiver_no', $filter_receiver_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_receiver_no")) }}
				        	</span>
				        </div>
				-->
				        <div>
				        	<span class="search-po-left-pane">{{ $label_shipment_reference_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_shipment_reference_no', $filter_shipment_reference_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_shipment_reference_no")) }}
				        	</span>
				        </div>
			      	</div>
			      	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_entry_date }}</span>
				        	<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_entry_date', $filter_entry_date, array('class'=>'span2', 'id'=>"filter_entry_date", 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>
				        </div>
				<!--
				        <div>
				        	<span class="search-po-left-pane">{{ $label_back_order }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_back_order', $filter_back_order, array('class'=>'back-order', 'placeholder'=>'', 'id'=>"filter_back_order")) }}
				        	</span>
				        </div>
				-->
			      	</div>
			    
			      	<div class="span3">
			      	<!--
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_receiver }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_stock_piler', array('' => $text_select) + $stock_piler_list, $filter_stock_piler, array('class'=>'select-width', 'id'=>"filter_stock_piler")) }}
				        	</span>
				        </div>
				        -->
				
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_status }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_status', array('default' => $text_select) + $po_status_type, $filter_status, array('class'=>'select-width', 'id'=>"filter_status")) }}
				        	</span>
				        </div>
				       
				        <!--
				        <div>
				        	<span class="search-po-left-pane">{{ $label_brand }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_brand', array('' => $text_select) + $brands_list, $filter_brand, array('class'=>'select-width', 'id'=>"filter_brand")) }}
				        	</span>
				        </div>
				
				        <div>
				        	<span class="search-po-left-pane">{{ $label_division }}</span>
				        	<span class="search-po-right-pane">
				        		{{-- Form::select('filter_division', array('' => $text_select) + $divisions_list, $filter_division, array('class'=>'select-width', 'id'=>"filter_division")) --}}
				        		<select class="select-width" id="filter_division" name="filter_division">
				        			<option value="" selected="selected">Please Select</option>
				        		</select>
				        	</span>
				        </div>
				        -->
			      	</div>
			      	<div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
            </div>
            {{ Form::hidden('sort', $sort) }}
		    {{ Form::hidden('order', $order) }}

            {{ Form::close() }}
          </div>
      	</div>

	</div> <!-- /controls -->
</div> <!-- /control-group -->

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
	<div class="div-buttons">
		<table>
			<tr>
				<th>
					<div class="div-buttons">
						<!--@if ( CommonHelper::valueInArray('CanAssignPurchaseOrders', $permissions) )
						
							<a role="button" class="btn btn-info btn-darkblue assignPO" title="{{ $button_assign_to_stock_piler }}" data-toggle="modal">{{ $button_assign_to_stock_piler }}</a>
						@endif
						-->
						@if ( CommonHelper::valueInArray('CanExportPurchaseOrders', $permissions) )
					<!--<a href= {{ $url_export_backorder }} class="btn btn-info btn-darkblue">{{ $button_generate_backorder }}</a> -->
						<a class="btn btn-info btn-darkblue" href={{URL::to('purchase_order/sync_to_mobile')}}>Sync To Mobile </a>
						@endif 
					</div>
				</th>
				<th>
					<div class="div-buttons btn-group ">
				        <button type="button" class="btn btn-info btn-darkblue " data-toggle="dropdown">Report <span class="caret"></span>
				        </button>
				        <ul class="dropdown-menu">
				          <li><a href={{URL::to('purchase_order/discrepansy')}}>Overage/Shortage Report</a></li>
					          <li><a href={{URL::to('purchase_order/unlisted')}}>Unlisted Report</a></li>
				        </ul>
			      	</div>
			     </th>
			     <th>
					<div class="div-buttons">
						@if ( CommonHelper::valueInArray('CanSyncPurchaseOrders', $permissions) )
						<a class="btn btn-info btn-darkblue" href={{URL::to('purchase_order/pulljda')}}>{{ $button_jda }}</a>
						@endif
					</div>
				</th>
			</tr>
		</table>
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $purchase_orders_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
					<!--
						@if ( CommonHelper::valueInArray('CanAssignPurchaseOrders', $permissions) )
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
						@endif
					-->
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_po_no }}" class="@if( $sort=='po_no' ) {{ $order }} @endif">{{ $col_po_no }}</a></th>
						<th>{{ $col_shipment_ref }}</th>
						<th>{{ $col_total_qty }}</th>
				<!--	<th>{{ $col_carton_id }}</th>     -->
				<!--	<th><a href="{{ $sort_receiver_no }}" class="@if( $sort=='receiver_no' ) {{ $order }} @endif">{{ $col_receiver_no }}</a></th>    
						<th>{{ $col_receiving_stock_piler }}</th> -->
						<th>{{ $col_slot }}</th>
						<th><a href="{{ $sort_entry_date }}" class="@if( $sort=='entry_date' ) {{ $order }} @endif">{{ $col_entry_date }}</a></th>
						<!--
						<th>{{ $col_status }}</th>
						-->
						<th class="align-center">{{ $col_action }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($purchase_orders) )
				<tr class="font-size-13">
					<td colspan="13" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $purchase_orders as $po )
					<tr class="font-size-13 tblrow" data-id="{{ $po->purchase_order_no }}">
						@if ( CommonHelper::valueInArray('CanAssignPurchaseOrders', $permissions) )
						<!--
						<td class="align-center">
							@if($po->data_display == 'Open' || $po->data_display == 'Assigned')
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $po->purchase_order_no }}" value="{{ $po->purchase_order_no }}" />
							@endif
						</td>
						-->
						@endif
						<td>{{ $counter++ }}</td>
						<td><a href="{{ $url_detail . '&receiver_no=' . $po->receiver_no }}">{{ $po->purchase_order_no }}</a></td>
						<td>{{ $po->shipment_reference_no }}</td>
						<td>{{ $po->total_qty }}</td>
				<!--	<td>{{ $po->carton_id }}</td>
						<td><a href="{{ $url_detail . '&receiver_no=' . $po->receiver_no }}">{{$po->receiver_no}}</a></td>  
						<td>{{ $po->fullname }}</td>-->
						<td>{{ $po->slot_code }}</td>
						<td>{{ date("M d, Y", strtotime($po->created_at)) }}</td>
						<!--
						<td>{{ $po->data_display }}</td>
						-->
						<td class="align-center">
							@if ( CommonHelper::valueInArray('CanClosePurchaseOrders', $permissions) )
								@if($po->data_display === 'Posted')
									<a style="width: 70px;" disabled="disabled" class="btn btn-danger">{{ $text_posted_po }}</a>
								@elseif ($po->data_display === 'Done')
									<a style="width: 70px;" class="btn btn-success closePO" data-id="{{ $po->purchase_order_no }}">{{ $button_close_po }}</a>
									<!-- <a style="width: 70px;" id="reopen" data-id="{{ $po->purchase_order_no }}" class="btn btn-primary">Reopen</a> -->
								@else
									<a style="width: 70px;" disabled="disabled" class="btn">{{ $button_close_po }}</a>
								@endif

								{{ Form::open(array('url'=>'purchase_order/close_po', 'id' => 'closePO_' . $po->
								purchase_order_no, 'style' => 'margin: 0px;')) }}
									{{ Form::hidden('po_no', $po->purchase_order_no) }}
									{{ Form::hidden('invoice_no') }}
									{{ Form::hidden('invoice_amount') }}
									{{ Form::hidden('filter_po_no', $filter_po_no) }}
									{{ Form::hidden('filter_receiver_no', $filter_receiver_no) }}
									{{-- Form::hidden('filter_supplier', $filter_supplier) --}}
									{{ Form::hidden('filter_entry_date', $filter_entry_date) }}
									{{ Form::hidden('filter_stock_piler', $filter_stock_piler) }}
									{{ Form::hidden('filter_status', $filter_status) }}
									{{ Form::hidden('filter_shipment_reference_no', $filter_shipment_reference_no) }}
							  		{{ Form::hidden('sort', $sort) }}
									{{ Form::hidden('order', $order) }}
									{{ Form::hidden('page', $page) }}
									{{ Form::hidden('module', 'purchase_order') }}
									{{ Form::hidden('receiver_no', $po->receiver_no) }}

						  		{{ Form::close() }}

						  		{{ Form::open(array('url'=>'purchase_order/reopen', 'id' => 'reopenForm', 'style' => 'margin: 0px;')) }}
									{{ Form::hidden('purchase_order_no', '', array('id' => 'reopenPoNo')) }}
						  		{{ Form::close() }}

					  		@endif

						</td>
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
	@if( CommonHelper::arrayHasValue($purchase_orders) )
	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		{{ Form::open(array('url'=>'purchase_order/assign_to_piler', 'id'=>"form-assign", 'class'=>'form-horizontal', 'style' => 'margin: 0px;', 'role'=>'form')) }}
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">{{ $heading_title_assign_po }}</h3>
  		</div>
  		<div class="modal-body add-piler-wrapper">
			<div class="control-group">
				<label class="control-label" for="po_no">{{ $entry_purchase_no }}</label>
				<div class="controls">
					{{ Form::text('po_no', '', array('id' => 'po_no', 'readonly' => 'readonly')) }}
				</div> <!-- /controls -->
			</div> <!-- /control-group -->

			<div class="control-group piler-block">
				<label class="control-label" for="stock_piler">{{ $entry_stock_piler }}</label>
				<div class="controls">
					{{ Form::select('stock_piler[]', $stock_piler_list, $po->assigned_to_user_id) }}
					<a class="add-piler-btn"><i class="icon-plus-sign" style="font-size: 1.5em;"></i></a>
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
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
  		{{ Form::hidden('sort', $sort) }}
		{{ Form::hidden('order', $order) }}
		{{ Form::hidden('page', $page) }}
		{{ Form::hidden('module', 'purchase_order') }}

  		{{ Form::close() }}
	</div>
	@endif
	<!-- /widget-content -->
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
        	{{ Form::label('invoice_no',$label_invoice_number, array("style" => "margin-right:10px", "class" => "col-sm-2 control-label"))}}
        	<div class="col-sm-10">
        		{{ Form::text('invoice_no', '', array('required', 'id'=>'invoiceNoInput','class'=> "form-control"))}}
        	</div>
		</div>
		</br>
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

<!-- endi of modal for close po-->

<script type="text/javascript">
$(document).ready(function() {
	$('.add-piler-btn').unbind('click').click(function(e) {
		// $('.piler-block').clone().appendTo(".add-piler-wrapper");
		var html = '';
		html += '<div class="control-group piler-block">'
					+ '<label class="control-label" for="stock_piler">{{ $entry_stock_piler }}</label>'
						+ '<div class="controls">'
							+ '{{ Form::select('stock_piler[]', $stock_piler_list, '') }}'
							+ '<a class="remove-piler-btn" style="margin-left: 3px;"><i class="icon-minus-sign" style="font-size: 1.5em; color:#CB1212;"></i></a>'
						+ '</div>'
				+ '</div>';
		$(".add-piler-wrapper").append(html);

		$('.remove-piler-btn').click(function(e) {
			console.log('ee');
			$(this).parent().parent().remove();
		});

	});



    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });

    // Assign PO
    $('.assignPO').click(function() {
    	var count = $("[name='selected[]']:checked").length;

		if (count>0) {
			var answer = confirm('{{ $text_confirm_assign }}')

			if (answer) {
				var purchase_no = new Array();
				$.each($("input[name='selected[]']:checked"), function() {
					purchase_no.push($(this).val());
				});

    			$('#po_no').val(purchase_no.join(','));

    			// http://local.ccri.com/purchase_order/assign
    			location = "{{ $url_assign }}" + '&po_no=' + encodeURIComponent(purchase_no.join(','));
			} else {
				return false;
			}
		} else {
			alert('{{ $error_assign }}');
			return false;
		}
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

    //clear data id of closePOModal
    $('#closePoModal .close').click(function(){
    	$("#closePoModal").attr('data-id', '');
    });


    // Submit Assign PO
    $('#btn-assign').click(function() {
    	stockpiler = $('select[name=\'stock_piler\']').val();

    	if (stockpiler == undefined) {
    		alert('{{ $error_assign_po }}');
    		return false;
    	} else {
    		$('#form-assign').submit();
    	}
    });

    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-purchase-order').submit();
    });

    $('#form-purchase-order input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-purchase-order').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_po_no, #filter_receiver_no, #filter_supplier, #filter_shipment_reference_no').val('');
		$('#filter_entry_date, #filter_back_order').val('');

		$('select').val('');
		$('#form-purchase-order').submit();
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

		var filter_po_no = $('#filter_po_no').val();
		url += '?filter_po_no=' + encodeURIComponent(filter_po_no);

		var filter_receiver_no = $('#filter_receiver_no').val();
		url += '&filter_receiver_no=' + encodeURIComponent(filter_receiver_no);

		// var filter_supplier = $('#filter_supplier').val();
		// url += '&filter_supplier=' + encodeURIComponent(filter_supplier);

		var filter_entry_date = $('#filter_entry_date').val();
		url += '&filter_entry_date=' + encodeURIComponent(filter_entry_date);

		var filter_stock_piler = $('select[name=\'filter_stock_piler\']').val();
		url += '&filter_stock_piler=' + encodeURIComponent(filter_stock_piler);

		var filter_status = $('select[name=\'filter_status\']').val();
		url += '&filter_status=' + encodeURIComponent(filter_status);

		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_export }}" + url;
    });

    // Select
    $('.tblrow').click(function() {
    	var rowid = $(this).data('id');

    	if ($('#selected-' + rowid).length>0) {
	    	if ($('#selected-' + rowid).is(':checked')) {
	    		$('#selected-' + rowid).prop('checked', false);
	    		$(this).children('td').removeClass('tblrow-active');
	    	} else {
	    		$('#selected-' + rowid).prop('checked', true);
	    		$(this).children('td').addClass('tblrow-active');
	    	}
    	} else {
    		$(this).children('td').removeClass('tblrow-active');
    	}
    });

    $('.item-selected').click(function() {
    	var rowid = $(this).data('id');

    	if ($(this).is(':checked')) {
    		$(this).prop('checked', false);
    		$(this).children('td').removeClass('tblrow-active');
    	} else {
    		$(this).prop('checked', true);
    		$(this).children('td').addClass('tblrow-active');
    	}
    });

    $('#main-selected').click(function() {
    	if ($('#main-selected').is(':checked')) {
    		$('input[name*=\'selected\']').prop('checked', true);
    		$('.table tbody tr > td').addClass('tblrow-active');
    	} else {
    		$('input[name*=\'selected\']').prop('checked', false);
    		$('.table tbody tr > td').removeClass('tblrow-active');
    	}
   	});


   	$('#reopen').click(function() {
   		var answer = confirm('{{ $text_confirm_reopen }}')
   		if (answer) {
	    	var docNo = $(this).data('id');
   			// alert(docNo);
	    	$("#reopenPoNo").val(docNo);
	    	$('#reopenForm').submit();
    	} else {
			return false;
		}
    	/*url = '';

		var filter_po_no = $('#filter_po_no').val();
		url += '?filter_po_no=' + encodeURIComponent(filter_po_no);

		var filter_receiver_no = $('#filter_receiver_no').val();
		url += '&filter_receiver_no=' + encodeURIComponent(filter_receiver_no);

		var filter_supplier = $('#filter_supplier').val();
		url += '&filter_supplier=' + encodeURIComponent(filter_supplier);

		var filter_entry_date = $('#filter_entry_date').val();
		url += '&filter_entry_date=' + encodeURIComponent(filter_entry_date);

		var filter_stock_piler = $('select[name=\'filter_stock_piler\']').val();
		url += '&filter_stock_piler=' + encodeURIComponent(filter_stock_piler);

		var filter_status = $('select[name=\'filter_status\']').val();
		url += '&filter_status=' + encodeURIComponent(filter_status);

		url += '&sort=' + encodeURIComponent('{{ $sort }}');
		url += '&order=' + encodeURIComponent('{{ $order }}');

      	location = "{{ $url_reopen }}" + url;*/
    });
	dataVal = { brand : $('#filter_brand').val()};
	getDivision(dataVal);

	$('#filter_brand').click(function() {
		var value = $(this).val();
		var dataVal = { brand : value };

		getDivision(dataVal);

        return false;
	});

	function getDivision(dataVal) {
		var select = $("#filter_division");
		select.html('');
		if (dataVal.brand == '') {
			select.append('<option value="" selected="selected">Please Select</option>');
		} else {
			select.append('<option value="" selected="selected">Please Select</option>');
			$.ajax({
	            url: 'purchase_order/get_division',
	            type: 'GET',
	            cache: false,
	            data: dataVal,
	            dataType: 'json',
	            success: function(result) {
	            	// select.html('');
	            	$.each(result, function(key, val) {
	            		select.append('<option value="' + key + '">' + val + '</option>');
	            	})
	            },
	            error: function(xhr, textstatus, errorthrown){
	            	return false;
	            },
	            complete: function() {
	            	return false;
	            }

	        });
		}
	}
});
</script>