<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
{{ HTML::style('resources/css/bootstrap.min.css') }}
{{ HTML::style('resources/css/bootstrap-responsive.min.css') }}
{{ HTML::style('resources/css/style.css') }}
</head>
<body>
<div class="table-responsive">
			<div style="text-align: center">
				<h1>Casual Clothing Retailers Inc.<br/>PURCHASE ORDER DETAIL REPORT</h1>
				Print Date: {{ date('m/d/y h:i A')}}
			</div>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>{{ $col_sku }}</th>
				<th>{{ $col_upc }}</th>
				<th>{{ $col_short_name }}</th>
				<th>{{ $col_expiry_date }}</th>
				<th>{{ $col_expected_quantity }}</th>
				<th>{{ $col_received_quantity }}</th>
				<th> VARIANCE </th>
			</tr>
		</thead>
		@if( !CommonHelper::arrayHasValue($results) )
		<tr class="font-size-13">
			<td colspan="7" class="align-center">{{ $text_empty_results }}</td>
		</tr>
		@else
			@foreach( $results as $po )
			<tr class="font-size-13"
			@if ( ($po->quantity_ordered - $po->quantity_delivered) > 0 )
				style="background-color:#F29F9F"
			@endif
			>
				<td>{{ $po->sku }}</td>
				<td>{{ $po->upc }}</td>
				<td>{{ $po->short_description }}</td>
				<td>
					@if ($po->expiry_date == '0000-00-00 00:00:00' )
						N/A
					@else
						{{ date('M d, Y', strtotime($po->expiry_date)) }}
					@endif
				</td>
				<td>{{ $po->quantity_ordered }}</td>
				<td>{{ $po->quantity_delivered }}</td>
				<td>{{ $po->quantity_ordered- $po->quantity_delivered }}</td>
			</tr>
			@endforeach
		@endif
	</table>
</div>

</body>
</html>