<?php

namespace App\Http\Controllers\Api;

use App\FAQ;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FAQController extends Controller
{
    // public function get_all_faqs() 
    // {        
    //     try {
    //         $faqs = FAQ::where('status', 'visible')->get();

    //         if ($faqs->isEmpty()) {
    //             return response()->json(['message' => 'No FAQs are found.'], 404);
    //         }

    //         foreach ($faqs as $faq) {
    //             $faq->answer = strip_tags($faq->answer);
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'success',
    //             'data' => $faqs
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Error fetching FAQs.', 
    //             'error' => $e->getMessage(                    
    //         )], 500);
    //     }
    // }

    public function get_all_faqs() 
    {        
        $faqs = FAQ::where('status', 'visible')->get();

        if ($faqs->isEmpty()) 
        {
            return response()->json([
                'status' => false,
                'message' => 'No FAQs are found.',
                'data' => $faqs
            ]);
        }
        else
        {
            foreach ($faqs as $faq) 
            {
                $faq->answer_filter = strip_tags($faq->answer);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $faqs
            ]);
        }
    }
}
