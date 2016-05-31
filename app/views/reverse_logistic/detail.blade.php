
<!-- PO Detail -->
<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Reverse Logistic Details</h3>
    </div>
    <!-- /widget-header -->
	<div class="widget-content">
	   <div class="row-fluid stats-box">
	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">TL Number :</span>
		        	<span class="left-pane">{{ Form::text('so_no', $so_no, array('readonly' => 'readonly')) }}	</span>
		        </div>

		        <div>
		        	<span class="left-pane"> From :</span>
		        	<span class="left-pane">{{ Form::text('fromStore', $fromStore, array('readonly' => 'readonly')) }}	
		        	 </span>
		        </div>
	      	</div>

	      	<div class="span4">
	      		<div>
		        	<span class="left-pane">Stockpiler :</span>
		        	<span class="right-pane">{{ Form::text('fullname', $fullname, array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        	<span class="left-pane"> To :</span>
		        	<span class="right-pane"><input type="text" disabled="" value=""></input></span>
		        </div>
		        
	      </div>

	      <div class="span4">
		        <div>
		          <div>
		        	<span class="left-pane">Status</span>
		        	<span class="right-pane">{{ Form::text('filter_status', $filter_status, array('readonly' => 'readonly')) }}</span>
		        </div>
		        <div>
		        	<span class="left-pane">Receive Date : </span>
		        	<span class="right-pane"><input type="text" disabled="" value="{{
		         date("M d, Y",strtotime('$created_at')) }}"></input></span>
		        </div>
		        </div>
	      </div>
	   </div>
	 </div>
</div>

<div class="clear">
	<div class="div-paginate">
		
		    <h6 class="paginate">
				<span> </span>
			</h6>
		
			
		
	</div>
</div>

<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3> Reverse Logistic Content</h3>
      <span class="pagination-totalItems">Total {{ $store_return_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>No.</th>
				
						<th><a href=""> UPC</a></th>
						<th><a href="">  Short Name</a></th>
			
						<th>order quantity </th>
						<th> RECEIVED Quantity </th>
						<th> VARIANCE Quantity </th>
					</tr>
				</thead>

				
				@if( !CommonHelper::arrayHasValue($store_return) )
				<tr class="font-size-13">
					<td colspan="12" class="align-center" style="background-color:#f6f6f6">{{ $text_empty_results }}</td>
				</tr>
				@else

					@foreach( $store_return as $so )
					<tr class="font-size-13" style="background-color:#F29F9F">
								
						<td>{{ $counter++ }}</td>
				
						<td>{{ $so['upc'] }}</td>
						<td>{{ $so['description'] }}</td>
						<td></td>
						<td>{{ $so['received_qty'] }}</td>
						<td>{{ $so['received_qty'] - $so['delivered_qty']  }}</td>
					</tr>
					@endforeach
				@endif
			
			</table>
		</div>
	</div>


    <h6 class="paginate">
		<span></span>
	</h6>


</div>

