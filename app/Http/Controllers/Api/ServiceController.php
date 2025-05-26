<?php

namespace App\Http\Controllers\Api;

use App\BusinessService;
use App\Category;
use App\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\CreateService;
use App\Outlet;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    // public function get_services()
    // {
    //     try {
    //         $services = BusinessService::with(['users', 'location', 'category'])
    //             ->get();
    //         $formattedServices = $services->map(function ($service) {
    //             $imageUrl = str_replace('http://127.0.0.1:8000', env('APP_URL'), $service->service_image_url);
                
    //             return [
    //                 'id' => $service->id,
    //                 'name' => ucfirst($service->name),
    //                 'description' => strip_tags($service->description),
    //                 'price' => $service->price,
    //                 'time' => $service->time,
    //                 'time_type' => $service->time_type,
    //                 'discount' => $service->discount,
    //                 'discount_type' => $service->discount_type,
    //                 'status' => $service->status,
    //                 'slug' => $service->slug,
    //                 'location' => ucfirst($service->location->name),
    //                 'category' => ucfirst($service->category->name),
    //                 'users' => $service->users->map(function ($user) {
    //                     return [
    //                         'id' => $user->id,
    //                         'name' => $user->name,
    //                     ];
    //                 }),
    //                 'service_image_url' => $imageUrl,
    //             ];
    //         });

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'success',
    //             'data' => $formattedServices,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Error retrieving services',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function get_services()
    {
        try {
            $services = BusinessService::with(['users', 'category'])->get();

            $formattedServices = $services->map(function ($service) {
                // $imageUrl = str_replace('http://127.0.0.1:8000', env('APP_URL'), $service->service_image_url);
                
                $business_services_outlets = DB::table('business_services_outlets')->where('business_service_id', $service->id)->pluck('outlet_id')->toArray();
                $outlet = Outlet::whereIn('id', $business_services_outlets)->pluck('outlet_name')->toArray();

                return [
                    'id' => $service->id,
                    'name' => ucfirst($service->name),
                    'description' => strip_tags($service->description),
                    'price' => $service->price,
                    'time' => $service->time,
                    'time_type' => $service->time_type,
                    'discount' => $service->discount,
                    'discount_type' => $service->discount_type,
                    'status' => $service->status,
                    'slug' => $service->slug,
                    'location' => '',
                    'outlet' => implode(', ', $outlet),
                    'category' => ucfirst($service->category->name),
                    'users' => $service->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                        ];
                    }),
                    'service_image_url' => $service->service_image_url,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $formattedServices,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving services',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function all_services()
    // {
    //     try {
    //         $servicesByCategory = BusinessService::with(['location', 'category'])
    //             ->get()
    //             ->groupBy('category.name'); // Grouping all services by category name
    
    //         $formattedServicesByCategory = [];
    
    //         foreach ($servicesByCategory as $categoryName => $services) 
    //         {
    //             $formattedServices = $services->map(function ($service) {
    //                 $imageUrl = str_replace('http://127.0.0.1:8000', env('APP_URL'), $service->service_image_url);
    
    //                 return [
    //                     'id' => $service->id,
    //                     'name' => ucfirst($service->name),
    //                     'description' => strip_tags($service->description),
    //                     'price' => $service->price,
    //                     'time' => $service->time,
    //                     'time_type' => $service->time_type,
    //                     'discount' => $service->discount,
    //                     'discount_type' => $service->discount_type,
    //                     'status' => $service->status,
    //                     'slug' => $service->slug,
    //                     'location' => ucfirst($service->location->name),
    //                     'service_image_url' => $imageUrl,
    //                 ];
    //             });
    
    //             $formattedServicesByCategory[] = [
    //                 'service_category_name' => ucfirst($categoryName),
    //                 'services' => $formattedServices,
    //             ];
    //         }
    
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'success',
    //             'data' => $formattedServicesByCategory,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Error retrieving services by category',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function all_services()
    {
        try {
            $servicesByCategory = BusinessService::with(['category'])
                ->get()
                ->groupBy('category.name'); // Grouping all services by category name
    
            $formattedServicesByCategory = [];
    
            foreach ($servicesByCategory as $categoryName => $services) 
            {
                $formattedServices = $services->map(function ($service) {
                    // $imageUrl = str_replace('http://127.0.0.1:8000', env('APP_URL'), $service->service_image_url);
    
                    $business_services_outlets = DB::table('business_services_outlets')->where('business_service_id', $service->id)->pluck('outlet_id')->toArray();
                    $outlet = Outlet::whereIn('id', $business_services_outlets)->pluck('outlet_name')->toArray();

                    return [
                        'id' => $service->id,
                        'name' => ucfirst($service->name),
                        'description' => strip_tags($service->description),
                        'price' => $service->price,
                        'time' => $service->time,
                        'time_type' => $service->time_type,
                        'discount' => $service->discount,
                        'discount_type' => $service->discount_type,
                        'status' => $service->status,
                        'slug' => $service->slug,
                        'location' => '',
                        'outlet' => implode(', ', $outlet),
                        'service_image_url' => $service->service_image_url,
                    ];
                });
    
                $formattedServicesByCategory[] = [
                    'service_category_name' => ucfirst($categoryName),
                    'services' => $formattedServices,
                ];
            }
    
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $formattedServicesByCategory,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error retrieving services by category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function services_by_category($category_id)
    {
        $category = Category::find($category_id);

        if($category)
        {
            $BusinessService = BusinessService::where('category_id', $category_id)
                                                ->where('status', 'active')
                                                ->orderBy('order_level', 'desc')
                                                ->get();
        
            $data = [
                'category_id' => $category_id,
                'catgeory_name' => $category->name,
                'services' => $BusinessService
            ];

            return response()->json([
                'status' => true,
                'message' => 'Services List By Category',
                'data' => $data
            ]);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Category Not Found'
            ]);
        }
    }
}
