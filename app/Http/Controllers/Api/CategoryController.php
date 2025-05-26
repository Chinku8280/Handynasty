<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    // category_list

    public function category_list()
    {
        $category = Category::where('status', 'active')->orderBy('order_level', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Category List',
            'data' => $category,
        ]);
    }

    // category_list_loyalty_program

    public function category_list_loyalty_program()
    {
        $category = Category::where('status', 'active')
                            ->where('is_loyalty_program', 1)
                            ->orderBy('order_level', 'desc')
                            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Category List for Loyalty Program',
            'data' => $category,
        ]);
    }
}
