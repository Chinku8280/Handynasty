<?php

namespace App\Observers;

use App\Promotion;
use App\Helper\SearchLog;
use Illuminate\Support\Facades\File;

class PromotionObserver
{

    public function updating(Promotion $promotion)
    {
        if($promotion->isDirty('image') && !is_null($promotion->getOriginal('image'))){
            $path = public_path('user-uploads/promotion/'.$promotion->getOriginal('image'));
            if($path){
                File::delete($path);
            }
        }
    }

    public function deleted(Promotion $promotion)
    {
        if(!is_null($promotion->getOriginal('image')))
        {
            $path = public_path('user-uploads/promotion/'.$promotion->getOriginal('image'));
            if($path) {
                File::delete($path);
            }
        }

    }

}
