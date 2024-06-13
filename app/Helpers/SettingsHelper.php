<?php
namespace App\Helpers;

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
}