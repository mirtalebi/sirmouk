<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    const APP_NAME = "APP_NAME";
    const INVOICE_PAYMENT_CATEGORY_ID = "INVOICE_PAYMENT_CATEGORY_ID";


    const defualtValues = [
        self::APP_NAME => "قاطی پلو",
        self::INVOICE_PAYMENT_CATEGORY_ID => null
    ];

    public static function getValue($name)
    {
        $ss = SiteSetting::where('name', $name)->first();
        if ($ss) {
            return $ss->value;
        }

        return self::defualtValues[$name];
    }

    public static function setValue($name, $value)
    {
        $ss = SiteSetting::where('name', $name)->first();
        if ($ss) {
            $ss->value = $value;
            $ss->save();
        }

        return $ss;
    }
}
