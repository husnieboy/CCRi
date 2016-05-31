<!--<div class="control-group">
	<a href="{{ $url_back }}" class="btn btn-info btn-darkblue"> <i class="icon-chevron-left"></i> {{ $button_back }}</a>

	@if ( CommonHelper::valueInArray('CanExportStoreReturn', $permissions) )
	<a class="btn btn-info btn-darkblue" id="exportList">{{ $button_export }}</a>
	@endif

</div>-->

<!-- PO Detail -->
<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_so_details }}</h3>
    </div>
    <!-- /widget-header -->
	<div class="widget-content">
	   <div class="row-fluid stats-box">
	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">TL Number :</span>
		        	<span class="left-pane"><input type="textfield" disabled="" value ="{{$so_info->so_no}}"> </input></span>
		        </div>

		        <div>
		        	<span class="left-pane"> From :</span>
		        	<span class="left-pane">{{ Form::text('filter_store', $filter_store, array('readonly' => 'readonly')) }}	
		        	 </input></span>
		        </div>
	      	</div>

	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">Stockpiler :</span>
		        	<span class="left-pane">{{ Form::text('filter_fullname', $filter_fullname, array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        	<span class="left-pane"> To :</span>
		        	<span class="left-pane"><input type="text" disabled="" value=""></input></span>
		        </div>
		        
	      </div>

	      <div class="span4">
		        <div>
		          <div>
		        	<span class="left-pane">{{ $label_status }}</span>
		        	<span class="right-pane">{{ Form::text('data_display', $so_info->data_display, array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        	<span class="left-pane">Receive Date : </span>
		        	<span class="right-pane"><input type="text" disabled="" value="{{
		         date("M d, Y",strtotime($so_info->created_at)) }}"></input></span>
		        </div>
		        </div>
	      </div>
	   </div>
	 </div>
</div>

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
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title_so_contents }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $store_return_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>{{ $col_id }}</th>
					<!--	<th><a href="{{ $sort_sku }}" class="@if( $sort=='sku' ) {{ $order }} @endif">{{ $col_sku }}</a></th> -->
						<th>
						{{ $col_upc }}</th>
						<th>{{ $col_short_name }}</th>
					<!--	<th><a href="{{ $sort_delivered_quantity }}" class="@if( $sort=='delivered_quantity' ) {{ $order }} @endif">{{ $col_delivered_quantity }}</a></th> -->
						<th>order quantity </th>
						<th> RECEIVED Quantity </th>
						<th> VARIANCE Quantity </th>
					</tr>
				</thead>
				@if( !CommonHelper::arrayHasValue($store_return) )
				<tr class="font-size-13">
					<td colspan="12" class="align-center">{{ $text_empty_results }}</td>
				</tr>
				@else
					@foreach( $store_return as $so )
					<tr class="font-size-13">
				<!--	@if ( $so['received_qty'] !== $so['delivered_qty'] )
					 		 style="background-color:#F29F9F" 
					@endif-->
					
						<td>{{ $counter++ }}</td>
						<!--<td>{{ $so['sku'] }}</td> -->
						<td>{{ $so['upc'] }}</td>
						<td>{{ $so['description'] }}</td>
					<!--	<td>{{ $so['delivered_qty'] }}</td> -->
					<td></td>
						<td>{{ $so['received_qty'] }}</td>
						<td>{{ $so['received_qty'] - $so['delivered_qty']  }}</td>
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

    // Close SO
    $('.closeSO').click(function() {
    	var answer = confirm('{{ $text_warning }}');

		if (answer) {
			var store_code = $(this).data('id');
	    	$('#closeSO_' + store_code).submit();
		}
    });

	// Export List
    $('#exportList').click(function() {
    	url = '';

    	url += '?id=' + encodeURIComponent('{{ $so_info->id }}');
		url += '&sort_detail=' + encodeURIComponent('{{ $sort_detail }}');
		url += '&order_detail=' + encodeURIComponent('{{ $order_detail }}');

      	location = "{{ $url_export }}" + url;
    });
});
</script>