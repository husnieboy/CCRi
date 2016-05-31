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
            {{ Form::open(array('url'=>'store_return/stocktransfer', 'class'=>'form-signin', 'id'=>'form-store-order', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane">{{ $label_store_order_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_so_no', $filter_so_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_so_no")) }}
				        	</span>
				        </div>
				       <!-- <div>
				        	<span class="search-po-left-pane">{{ $label_status }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_status', array('' => $text_select) + $so_status_type, $filter_status, array('class'=>'select-width', 'id'=>"filter_status")) }}
				        	</span>
				        </div>-->
			      	</div>
			      	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane">From : </span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_store_name', array('' => $text_select) + $store_list, $filter_store_name, array('class'=>'select-width', 'id'=>"filter_store_name")) }}



				        		
				        	</span>
				        </div>
				     </div>
			     <br>
			      		<!--<div>
				        	<span class="search-po-left-pane">{{ $label_order_date }}</span>
				        	<div class="search-po-right-pane input-append date">
								{{ Form::text('filter_created_at', $filter_created_at, array('class'=>'span2', 'id'=>"filter_created_at", 'readonly'=>'readonly')) }}
								<span class="add-on"><i class="icon-th"></i></span>
				        	</div>-->
				    <div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm">{{ $button_search }}</a>
		      			<a class="btn" id="clearForm">{{ $button_clear }}</a>
			      	</div>
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
		@if(CommonHelper::arrayHasValue($store_return) )
		    <h6 class="paginate">
				<span>{{ $store_return->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		@if ( CommonHelper::valueInArray('CanAssignStoreReturn', $permissions))
			<a role="button" class="btn btn-info btn-darkblue assignStoreOrder" title="{{ $button_assign_to_stock_piler }}" data-toggle="modal">{{ $button_assign_to_stock_piler }}</a>
		@endif
	<!--	@if ( CommonHelper::valueInArray('CanExportStoreReturn', $permissions) )
			<a class="btn btn-info btn-darkblue" id="exportList">{{ $button_export }}</a>
		@endif-->	
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $store_return_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						@if ( CommonHelper::valueInArray('CanAssignStoreReturn', $permissions) )
							<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
						@endif
						<th>{{ $col_id }}</th>
						<th><a href="{{ $sort_so_no }}" class="@if( $sort=='so_no' ) {{ $order }} @endif">{{ $col_tl_number}}</a></th>
						<!--<th><a href="{{ $sort_store }}" class="@if( $sort=='store' ) {{ $order }} @endif">{{ $col_store }}</a></th>-->
						<th> From</th>
						<th> To     </th>
					<!--	<th><a href="{{ $sort_created_at }}" class="@if( $sort=='created_at' ) {{ $order }} @endif">{{ $col_order_date }}</a></th>-->
					<!--	<th>  slot number</th>-->
						<th>Stock Piler</th>
						<th> received date</th>
						<th>{{ $col_status }}</th>
						<th>{{ $col_action }}</th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($store_return) )
				<tr class="font-size-13">
					<td colspan="10" style="text-align: center;">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $store_return as $so )
					<tr class="font-size-13 tblrow" data-id="{{ $so['so_no'] }}"
						@if ( array_key_exists('discrepancy',$so) )
							
						@endif>

						@if ( CommonHelper::valueInArray('CanAssignStoreReturn', $permissions) )
						<td class="align-center">
							@if($so['data_display'] == 'Open' || $so['data_display'] == 'Assigned')
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $so['so_no'] }}" value="{{ $so['so_no'] }}" />
							@endif
						</td>
						@endif
						<td>{{ $counter++ }}</td>
						<td><a href="{{ $url_detail . '&id='.$so['id'] }}">{{ $so['so_no'] }}</a></td>
					<!--	<td>{{ $so['store_code'] }}</td>-->
						<td>{{ $so['store_name'] }}</td>
<!-- "TO" store--> 		<td>      </td> 
				<!--		<td>{{$so['so_no']}}</td>-->
						<td>{{ $so['fullname'] }}</td>
			
						<td>{{ date("M d, Y",strtotime($so['created_at'])) }}</td>
						<td>{{ $so['data_display'] }}</td>
						<td class="align-center">
						@if ( CommonHelper::valueInArray('CanCloseStoreReturn', $permissions) )

							@if($so['data_display'] === 'Posted')
								<a style="width: 70px;" disabled="disabled" class="btn btn-danger">{{ $text_posted }}</a>
								 <!-- && ($so['quantity_to_pick'] != $so['moved_qty']) -->
							@elseif ( $so['data_display'] === 'Done' )
								<a style="width: 70px;" class="btn btn-success closeStoreReturn" data-id="{{ $so['so_no'] }}">{{ $button_close_store_return }}</a>
							@else
								<a style="width: 70px;" disabled="disabled" class="btn">{{ $button_close_store_return }}</a>
							@endif

							{{ Form::open(array('url'=>'store_return/close', 'id' => 'closeSO_' . $so['so_no'], 'style' => 'margin: 0px;')) }}
								{{ Form::hidden('so_no', $so['so_no']) }}
					            {{ Form::hidden('filter_so_no', $filter_so_no) }}
								{{ Form::hidden('filter_store_name', $filter_store_name) }}
								{{ Form::hidden('filter_created_at', $filter_created_at) }}
								{{ Form::hidden('filter_status', $filter_status) }}
							    {{ Form::hidden('page', $page) }}
					            {{ Form::hidden('sort', $sort) }}
							    {{ Form::hidden('order', $order) }}
								{{ Form::hidden('module', 'store_return') }}
					  		{{ Form::close() }}
					  	@endif
						</td>
					</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($store_return) )
    <h6 class="paginate">
		<span>{{ $store_return->appends($arrFilters)->links() }}</span>
	</h6>
	@endif

</div>
<script type="text/javascript">
$(document).ready(function() {
    $('.date').datepicker({
      format: 'yyyy-mm-dd'
    });


    // Close SO
    $('.closeSO').click(function() {
    	var answer = confirm('{{ $text_warning }}');

		if (answer) {
			var so_no = $(this).data('id');
	    	$('#closeSO_' + so_no).submit();
		}
    });

    // Submit Form
    $('#submitForm').click(function() {
    	$('#form-store-order').submit();
    });

    $('#form-store-order input').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-store_return').submit();
		}
	});

    // Clear Form
    $('#clearForm').click(function() {
    	$('#filter_so_no').val('');
		$('#filter_created_at').val('');

		$('select').val('');
		$('#form-store-order').submit();
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

		var filter_so_no = $('#filter_so_no').val();
		url += '?filter_so_no=' + encodeURIComponent(filter_so_no);

		var filter_store_name = $('#filter_store_name').val();
		url += '&filter_store=' + encodeURIComponent(filter_store);

		var filter_created_at = $('#filter_created_at').val();
		url += '&filter_created_at=' + encodeURIComponent(filter_created_at);

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

   	// Assign PO
    $('.assignStoreOrder').click(function() {
    	var count = $("[name='selected[]']:checked").length;

		if (count>0) {
			var answer = confirm('{{ $text_confirm_assign }}')

			if (answer) {
				var so_no = new Array();
				$.each($("input[name='selected[]']:checked"), function() {
					so_no.push($(this).val());
				});

    			$('#so_no').val(so_no.join(','));

    			// http://local.ccri.com/purchase_order/assign
    			location = "{{ $url_assign }}" + '&so_no=' + encodeURIComponent(so_no.join(','));
			} else {
				return false;
			}
		} else {
			alert('{{ $error_assign }}');
			return false;
		}
    });

    $('.closeStoreReturn').click(function() {
    	var so_no = $(this).data('id');

    	var answer = confirm('Are you sure you want to close this Store Return?');
   		if (answer) {
	    	$('#closeSO_' + so_no).submit();
    	} else {
			return false;
		}

    });
});
</script>