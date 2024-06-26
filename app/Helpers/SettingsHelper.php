<?php
namespace App\Helpers;

use App\Models\DistributorSetting;

class SettingsHelper
{
    /* 
    * list of default distributor settings key 
    */
    public static function getDistributorKeys()
    {
        return [
            [
                'label'=>'Toyyibpay Secret',
                'type'=>'text',
                'key'=>'TOYYIBPAY_SECRET',
            ],
            [
                'label'=>'Toyyibpay Category',
                'type'=>'text',
                'key'=>'TOYYIBPAY_CATEGORY',
            ],
            [
                'label'=>'Delivery Price',
                'type'=>'text',
                'key'=>'DELIVERY_PRICE'
            ],
            [
                'label'=>'Business Address',
                'type'=>'textarea',
                'key'=>'DELIVERY_ADDRESS',
                'helpertext'=>'Used for delivery and invoicing'
            ]
            // [
            //     'label'=>'Enable Order',
            //     'type'=>'select',
            //     'key'=>'ENABLE_ORDER',
            //     'options'=>[
            //         'YES',
            //         'NO'
            //     ]
            // ],
        ];
    }

    /*
    * list of default admin settings key
    */
    public static function getAdminKeys()
    {
        return [
            // [
            //     'label'=>'Toyyibpay Secret',
            //     'type'=>'text',
            //     'key'=>'TOYYIBPAY_SECRET',
            // ],
            // [
            //     'label'=>'Toyyibpay Category',
            //     'type'=>'text',
            //     'key'=>'TOYYIBPAY_CATEGORY_ID',
            // ],
            [
                'label'=>'Distributor Delivery Price',
                'type'=>'text',
                'key'=>'DELIVERY_PRICE',
                'helpertext'=>'Delivery price when stock request'
            ],
        ];
    }

    public static function getDistributorSettings($distributorID)
    {
        return DistributorSetting::where('distributor_id', $distributorID)->pluck('sval','skey');
    }
}