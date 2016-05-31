<!--
    <div class="alert alert-danger">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>

    </div>

    <div class="alert alert-success">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	
    </div>
-->

<div class="control-group">
	<div class="controls">
		<div class="accordion" id="accordion2">
          <div class="accordion-group" style="background-color: #FFFFFF;">
 {{ Form::open(array('url'=>'reverse_logistic/list', 'class'=>'form-signin', 'id'=>'form-store-order', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">
	                <div class="span4">
			        	<div>
				        	<span class="search-po-left-pane"> TL Number : </span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_so_no', '', array('class'=>'login', 'placeholder'=>'', 'id'=>"")) }}
				        	</span>
				        </div>
				    
			      	</div>
			      	<div class="span4">
			      		<div>
				        	<span class="search-po-left-pane">To :</span>
				        	<span class="search-po-right-pane"> 		{{ Form::select('filter_store_name', array('' => $text_select) + $store_list, $filter_store_name, array('class'=>'select-width', 'id'=>"filter_store_name")) }}
				        	</span>
				        </div>
				     </div>
			     <br>
			      		
			    <div class="span11 control-group collapse-border-top">
			      		<a class="btn btn-success btn-darkblue" id="submitForm"> Search Now</a>
		      			<a class="btn" id="clearForm"> Clear</a>
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
		
			<a role="button" class="btn btn-info btn-darkblue assignReverseLogistic" title="" data-toggle="modal"> Assign to StockPiler</a>
	
	
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Reverse Logistic </h3>
      <span class="pagination-totalItems">{{ $text_total }}{{ $store_return_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
					<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
					
						<th>No.</th>
						<th><a href=""> TL Number</a></th>
						<th> From</th>
						<th> To     </th>
						<th>Stock Piler</th>
						<th> received date</th>
						<th>status</th>
						<th>action</th>
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
							
						@endif
					>
						@if ( CommonHelper::valueInArray('CanAssignStoreReturn', $permissions) )
						<td class="align-center">
							@if($so['data_display'] == 'Open' || $so['data_display'] == 'Assigned')
							<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $so['so_no'] }}" value="{{ $so['so_no'] }}" />
							@endif
						</td>
						@endif

					
						<td>{{ $counter++ }}</td>
						<td><a href="detail?so_no={{$so['so_no'].'&fromStore='.$so['store_name'].'&ToStore='.''.'&fullname='.$so['fullname'].'&CreatedAt='.date('M d, Y',strtotime($so['created_at'])).'&filter_status='.$so['data_display']}}">{{ $so['so_no'] }}</a></td>
					<!--	<td>{{ $so['store_code'] }}</td>-->
						<td>{{ $so['store_name'] }}</td>
<!-- "TO" store--> 		<td> </td> 
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

							{{ Form::open(array('url'=>'reverse_logistic/close', 'id' => 'closeSO_' . $so['so_no'], 'style' => 'margin: 0px;')) }}
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
    
   
});
</script>
	
  
