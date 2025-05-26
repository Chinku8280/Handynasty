<?php

namespace App\Observers;

use App\Voucher;
use App\Helper\SearchLog;
use Illuminate\Support\Facades\File;

class VoucherObserver
{

    public function updating(Voucher $voucher)
    {
        if($voucher->isDirty('image') && !is_null($voucher->getOriginal('image'))){
            $path = public_path('user-uploads/voucher/'.$voucher->getOriginal('image'));
            if($path){
                File::delete($path);
            }
        }
    }

    public function deleted(Voucher $voucher)
    {
        if(!is_null($voucher->getOriginal('image')))
        {
            $path = public_path('user-uploads/voucher/'.$voucher->getOriginal('image'));
            if($path) {
                File::delete($path);
            }
        }

    }

}
