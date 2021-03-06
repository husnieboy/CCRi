<?php

class Unlisted extends Eloquent {

    protected $table = 'unlisted';
    protected $fillable = array('sku', 'reference_no');

    public static function createUpdate($data = array()) {
        $query = Unlisted::where('sku', '=', $data['sku'])
            ->where('reference_no', '=', $data['po_order_no'])->first();

        if(! is_null($query) ) {
            $unlisted     = $query->first();
            $qty_received = $unlisted['quantity_received'] + $data['quantity_delivered'];

            Unlisted::where('sku', '=', $data['sku'])
                ->where('reference_no', '=', $data['po_order_no'])
                ->update(array('quantity_received' => $qty_received));

        } else {

            $unlisted                    = new Unlisted;
            $unlisted->sku               = $data['sku'];
            $unlisted->reference_no      = $data['po_order_no'];
            $unlisted->quantity_received = $data['quantity_delivered'];
            $unlisted->save();
        }
    }

    public static function getList($data = array(), $getCount = false)
    {
        /*$query = Load::select(DB::raw("wms_load.id, wms_load.load_code, wms_load.is_shipped, group_concat(wms_pallet.store_code SEPARATOR ',') stores"))
            ->join('load_details', 'load_details.load_code', '=', 'load.load_code')
            ->join('pallet', 'pallet.pallet_code', '=', 'load_details.pallet_code')
            ->groupBy('load.load_code');*/

        $query = Unlisted::select('unlisted.*','users.firstname','users.lastname','purchase_order_lists.shipment_reference_no','purchase_order_lists.destination','purchase_order_lists.delivery_date',DB::raw('convert(wms_unlisted.sku, decimal(20,0)) as sku'))
                ->join('purchase_order_lists', 'unlisted.reference_no', '=', 'purchase_order_lists.purchase_order_no')
                ->join('users', 'unlisted.scanned_by', '=', 'users.id', 'RIGHT')
                ->where('unlisted.deleted_at', '=', '0000-00-00 00:00:00');

        if( CommonHelper::hasValue($data['filter_reference_no']) ) $query->where('reference_no', 'LIKE', '%'. $data['filter_reference_no'] . '%');
        if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('sku', 'LIKE', '%'. $data['filter_sku'] . '%');
        if( CommonHelper::hasValue($data['filter_shipment_reference_no']) ) $query->where('shipment_reference_no', 'LIKE', '%'. $data['filter_shipment_reference_no'] . '%');

        if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
            if ($data['sort'] == 'reference_no') $data['sort'] = 'reference_no';
            if ($data['sort'] == 'sku') $data['sort'] = 'sku';

            $query->orderBy($data['sort'], $data['order']);
        }


        if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']) && !$getCount)  {
            $query->skip($data['limit'] * ($data['page'] - 1))
                  ->take($data['limit']);
        }

        if($getCount) {
            return $result = $query->count();
        }
        $result['result'] = $query->get()->toArray();
        $result['ship_ref_count'] = $query->groupBy('shipment_reference_no')->get()->count();
        DebugHelper::log(__METHOD__, $result);
        return $result;

    }

    public static function deleteByReference($reference_no) {
        return Unlisted::where('reference_no', '=', $reference_no)->delete();
    }

}