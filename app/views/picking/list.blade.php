@if( $errors->first() )
    <div class="alert alert-error">
    	<button class="close" data-dismiss="alert" type="button">&times;</button>
    	{{ HTML::ul($errors->all()) }}
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
            {{ Form::open(array('url'=>'picking/list', 'class'=>'form-signin', 'id'=>'form-pick-list', 'role'=>'form', 'method' => 'get')) }}
            <div id="collapseOne" class="accordion-body collapse in" style="padding-top: 20px;">

			      	<div class="span5">
			      		<div>
				        	<span class="search-po-left-pane">{{ $label_doc_no }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::text('filter_doc_no', $filter_doc_no, array('class'=>'login', 'placeholder'=>'', 'id'=>"filter_doc_no")) }}
				        	</span>
				        </div>

				        <div>
				        	<span class="search-po-left-pane">{{ $label_status }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_status', array('' => $text_select) + $pl_status_type, $filter_status, array('class'=>'select-width', 'id'=>"filter_status")) }}
				        	</span>
				        </div>

				    </div>

				    <div class="span5">
				        <!-- <div>
				        	<span class="search-po-left-pane">{{ $label_type }}</span>
				        	<span class="search-po-right-pane">
				        		{{ Form::select('filter_type', array('' => $text_select) + $pl_type, array('class'=>'select-width', 'id'=>"filter_type")) }}
				        	</span>
				        </div> -->

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
		@if(CommonHelper::arrayHasValue($picklist) )
		    <h6 class="paginate">
				<span>{{ $picklist->appends($arrFilters)->links() }}&nbsp;</span>
			</h6>
		@else
			&nbsp;
		@endif
	</div>
	<div class="div-buttons">
		<!-- @if ( CommonHelper::valueInArray('CanLoadPicking', $permissions) ) -->
			<!-- <a role="button" class="btn btn-warning" id="load-picklist" title="{{ $button_load }}" data-toggle="modal">{{ $button_load }}</a> -->
		<!-- @endif -->
		<!-- @if ( CommonHelper::valueInArray('CanAddLoad', $permissions) ) -->
		<!-- <a  class="btn btn-info" id="generate-load">{{ $button_add_store }}</a> -->
		<!-- @endif -->
		<!-- <a role="button" class="btn btn-info multi-change-to-store" title="{{ $button_change_to_store }}" data-toggle="modal">{{ $button_change_to_store }}</a> -->
		@if ( CommonHelper::valueInArray('CanExportPickingDocuments', $permissions) )
		<a role="button" class="btn btn-info btn-darkblue assignPicklist" title="{{ $button_assign_to_stock_piler }}" data-toggle="modal">{{ $button_assign_to_stock_piler }}</a>
		<a href="{{$url_export}}" class="btn btn-info btn-darkblue">{{ $button_export }}</a>
		@endif

		@if ( CommonHelper::valueInArray('CanViewPickingLockTags', $permissions) )
		<a href="{{$url_lock_tags}}" class="btn btn-info btn-darkblue">{{ $button_to_lock_tags }}</a>
		@endif
	</div>
</div>


<div class="widget widget-table action-table">
    <div class="widget-header"> <i class="icon-th-list"></i>
      <h3>{{ $heading_title }}</h3>
      <span class="pagination-totalItems">{{ $text_total }} {{ $picklist_count }}</span>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">
    	<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					@if ( CommonHelper::valueInArray('CanLoadPicking', $permissions) || CommonHelper::valueInArray('CanEditPicklist', $permissions))
			  		{{ Form::open(array('url'=>$url_change_to_store,'id' => 'form-picking-change', 'style' => 'margin: 0px;', 'method'=> 'post')) }}
						{{ Form::hidden('filter_type', $filter_type) }}
						{{ Form::hidden('filter_doc_no', $filter_doc_no) }}
						{{ Form::hidden('filter_status', $filter_status) }}
				  		{{ Form::hidden('sort', $sort) }}
						{{ Form::hidden('order', $order) }}
						{{ Form::hidden('page', $page) }}
						{{ Form::hidden('module', 'picklist') }}
						{{ Form::hidden('picklist_doc_no', '', array('id'=>'picklist-docno-change' )) }}
			  		{{ Form::close() }}
						<th style="width: 20px;" class="align-center"><input type="checkbox" id="main-selected" /></th>
					@endif
					<th>{{ $col_no }}</th>
					<!-- <th>{{ $col_type }}</th> -->
					<th><a href="{{ $sort_doc_no }}" class="@if( $sort=='doc_no' ) {{ $order }} @endif">{{ $col_doc_no }}</a></th>
					<th>STORE</th>
					<th>{{ $col_status }}</th>
					<th>{{ $col_action }}</th>
				</thead>
				@if( !CommonHelper::arrayHasValue($picklist) )
					<tr class="font-size-13">
						<td colspan="10" style="text-align: center;">{{ $text_empty_results }}</td>
					</tr>
				@else
					@foreach( $picklist as $value )
						<tr class="font-size-13 tblrow" data-id="{{ $value['move_doc_number'] }}">
							@if ( CommonHelper::valueInArray('CanLoadPicking', $permissions) )
							<td class="align-center">
								@if($value['type'] == 'upc')
								<input type="checkbox" class="checkbox item-selected" name="selected[]" id="selected-{{ $value['move_doc_number'] }}" value="{{ $value['move_doc_number'] }}" />
								@endif
							</td>
							@endif
							<td>{{ $counter++ }}</td>
							<!-- <td>{{ $value['type'] }}</td> -->
							<td>
      							@if( CommonHelper::valueInArray('CanAccessPickingDetails', $permissions))
								<a href="{{$url_detail}}&picklist_doc={{$value['move_doc_number']}}">{{ $value['move_doc_number'] }}
								@else
								{{ $value['move_doc_number'] }}
								@endif
							</td>
							<td>{{ Store::getStoreName($value['store_code']) }}</td>
							<td>{{ $value['data_display'] }}</td>
							@if ( CommonHelper::valueInArray('CanLoadPicking', $permissions)  || CommonHelper::valueInArray('CanEditPicklist', $permissions))
								<td class="align-center">
								@if ( CommonHelper::valueInArray('CanEditPicklist', $permissions) )

								@endif
							@endif <!--End of checking if either of the permissions are present-->
							</td>
						</tr>
					@endforeach
				@endif
			</table>
		</div>
	</div>

	@if( CommonHelper::arrayHasValue($picklist) )
    <h6 class="paginate">
		<span>{{ $picklist->appends($arrFilters)->links() }}</span>
	</h6>
	@endif


</div>



<script type="text/javascript">
$(document).ready(function() {

    /*$('#load-picklist-main-button').click(function(){
    	if ($('select[name="load_codes"]').val()== '') {
    		alert('{{ $error_load_no_load_code }}');
    		return false;
    	}

    	var answer = confirm('{{ $text_confirm_load }}');
    	if (answer ) {
    		$('#form-picklist-load').submit();
    	} else {
    			alert('{{ $error_load }}');
			return false;
		}

    });*/

    /*$('.load-picklist-single').click(function(){
    	var picklist_doc_no= $(this).attr('data-id');

    	$('.picklist-ids-load').val(picklist_doc_no);
    	$('#load-picklist-modal').modal('show');
    });*/


    $('.edit-picklist-single').click(function() {
    	var answer = confirm('{{ $text_confirm_change }}')
    	if (answer) {
	    	docNo = $(this).attr('data-id');
	    	$("#picklist-docno-change").val(docNo);
	    	$('#form-picking-change').submit();
    	} else {
			alert('{{ $error_change }}');
			return false;
		}
    });

    /*$('.multi-change-to-store').click(function() {
    	var count = $("[name='selected[]']:checked").length;
		console.log(count);
		if (count>0) {
			var picklist = new Array();
			$.each($("input[name='selected[]']:checked"), function() {
				picklist.push($(this).val());
			});
			//form-picking-load

			$('#picklist-docnos-change').val(picklist.join(','));
			console.log($('#picklist-docnos-change').val(picklist.join(',')));
		} else {
			alert('{{ $error_load }}');
			return false;
		}
    });*/

    $('.multi-change-to-store').click(function(e){
    	var count = $("[name='selected[]']:checked").length;
    	if (count>0) {
			var answer = confirm('Are you sure you want to change type?')

			if (answer) {
				var picklist = new Array();
				$.each($("input[name='selected[]']:checked"), function() {
					picklist.push($(this).val());
				});

    			$('#picklist-docno-change').val(picklist.join(','));
    			$('#form-picking-change').submit();

			} else {
				return false;
			}
		} else {
			alert('{{ $error_load }}');
			return false;
		}
    });



    /*// add load
    $('#generate-load').click(function() {
    	var token = $('#add-load-form input[name="_token"]').val();
    	  $.ajax({
    	      url: "{{$url_generate_load_code}}",
    	      type: "POST",
    	      data: {'_token': token},
    	      success: function(response){
    	      	response = JSON.parse(response);
    	      	$('#load-code-created').html('You have generated ' + response.load_code);
    	        $('#add-load-modal').modal('show');
    	      }
    	   });
    });
 	$('#close-add-load').click(function() {
   		window.location.reload();
    });*/


	// Submit Form
    $('#submitForm').click(function() {
    	$('#form-pick-list').submit();
    });

    $('#form-pick-list').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#form-pick-list').submit();
		}
	});
	// Clear Form
    $('#clearForm').click(function() {
    	$('#filter_doc_no, #filter_status, #filter_type').val('');

		$('select').val('');
		$('#form-pick-list').submit();
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
});
</script>
