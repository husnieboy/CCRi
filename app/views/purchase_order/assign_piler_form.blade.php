@if( $errors->all() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
    </div>
@endif

<div class="widget">
    <div class="widget-header"> <i class="icon-th-list"></i>
    	<h3>{{ $heading_title_assign_po }}</h3>
    </div>
    <!-- /widget-header -->

    <div class="widget-content">
    	{{ Form::open(array('url'=>'purchase_order/assign_to_piler', 'id'=>"form-assign", 'class'=>'form-horizontal', 'style' => 'margin: 0px;', 'role'=>'form')) }}
		<!-- <div class="span3">&nbsp;</div> -->
		<div class="span3">&nbsp;</div>
		<div class="span7 add-piler-wrapper">
			<div class="control-group">
				<label class="control-label">Division :</label>
				<div class="controls">
					{{ Form::text('po_no', $po_no, array('id' => 'po_no', 'readonly' => 'readonly')) }}
					{{ Form::hidden('receiver_num', Input::get('receiver_no'), array('id' => 'po_no', 'readonly' => 'readonly')) }}
				
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
		
			<div class="control-group">
				<label class="control-label">{{ $entry_stock_piler }}</label>
				<div class="controls">
					{{ Form::select('stock_piler[]', $stock_piler_list, '', array('id' => 'stock_piler_select') ) }}
				
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
		
        </div>
        <!-- <div class="span10">&nbsp;</div> -->
        <div class="span3">&nbsp;</div>
        <div class="span7">
        	<div class="control-group">
				<label class="control-label" for=""></label>
				<div class="controls">
					<a class="btn btn-info" id="btn-assign">{{ $button_assign }}</a>
					<a class="btn" href="{{ $url_back }}">{{ $button_cancel }}</a>
				</div> <!-- /controls -->
			</div> <!-- /control-group -->
		</div>
        {{ Form::hidden('po_no', $po_no) }}
        {{ Form::hidden('receiver_no', $receiver_no) }}
		{{ Form::hidden('module', $module) }}
		{{ Form::hidden('filter_po_no', $filter_po_no) }}
		{{ Form::hidden('filter_receiver_no', $filter_receiver_no) }}
		{{ Form::hidden('filter_shipment_reference_no', $filter_shipment_reference_no) }}
		{{ Form::hidden('filter_entry_date', $filter_entry_date) }}
		{{ Form::hidden('filter_back_order', $filter_back_order) }}
		{{ Form::hidden('filter_stock_piler', $filter_stock_piler) }}
		{{ Form::hidden('filter_status', $filter_status) }}
		{{ Form::hidden('filter_brand', $filter_brand) }}
		{{ Form::hidden('filter_division', $filter_division) }}
		{{ Form::hidden('page', $page) }}
		{{ Form::hidden('sort', $sort) }}
		{{ Form::hidden('order', $order) }}

		{{ Form::close() }}
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	function removeStockPiler() {
		$('.remove-piler-btn').click(function(e) {
			console.log('ee');
			$(this).parent().parent().remove();
		});
	}

	removeStockPiler();
    // Submit Form
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

		removeStockPiler()

	});

	// Submit Assign PO
    $('#btn-assign').click(function() {
    	// stockpiler = $('select[name=\'stock_piler\']').val();
    	var stockpiler = $('#stock_piler_select').val();
    	console.log(stockpiler);

    	if (stockpiler == '') {
    		alert('{{ $error_assign_po }}');
    		return false;
    	} else {
    		$('#form-assign').submit();
    	}
    });
});
</script>