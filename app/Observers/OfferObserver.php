<?php

namespace App\Observers;


use App\Helper\SearchLog;
use App\offer;
use Illuminate\Support\Facades\File;

class OfferObserver
{

    public function updating(Offer $offer)
    {
        if($offer->isDirty('image') && !is_null($offer->getOriginal('image'))){
            $path = public_path('user-uploads/offer/'.$offer->getOriginal('image'));
            if($path){
                File::delete($path);
            }
        }
    }

    public function deleted(Offer $offer)
    {
        if(!is_null($offer->getOriginal('image')))
        {
            $path = public_path('user-uploads/offer/'.$offer->getOriginal('image'));
            if($path) {
                File::delete($path);
            }
        }

    }

}
