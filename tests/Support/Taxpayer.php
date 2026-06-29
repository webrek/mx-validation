<?php

namespace Webrek\MxValidation\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Webrek\MxValidation\Laravel\Casts\ClabeCast;
use Webrek\MxValidation\Laravel\Casts\CurpCast;
use Webrek\MxValidation\Laravel\Casts\NssCast;
use Webrek\MxValidation\Laravel\Casts\RfcCast;

class Taxpayer extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'rfc' => RfcCast::class,
        'curp' => CurpCast::class,
        'clabe' => ClabeCast::class,
        'nss' => NssCast::class,
    ];
}
