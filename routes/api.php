<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::post('/register', 'Api\AuthController@register');
// Route::post('/login', 'Api\AuthController@login');

// Route::group(['middleware' => 'auth.api'], function () {
//     Route::get('logout', 'API\AuthController@logout');
//     Route::get('getUser', 'API\AuthController@getUser');
// });


// *
Route::post('/register', 'Api\AuthController@register');
// *
Route::post('/login', 'Api\AuthController@login');

Route::group(['middleware' => 'auth:api'], function(){

   // *
   Route::post('/logout', 'Api\AuthController@logout');
   // *
   Route::post('/change-password', 'Api\AuthController@updatePassword');
   // *
   Route::post('/delete-customer', 'Api\AuthController@delete_customer');

   // *
   Route::get('/profile', 'Api\ProfileController@get_profile');
   // *
   Route::post('/update-profile', 'Api\ProfileController@update_profile');

   // *
   Route::get('/my-coupons', 'Api\CouponController@my_coupons');
   // *
   Route::get('/coupon-details', 'Api\CouponController@coupon_details');
   // *
   Route::get('/search-coupons', 'Api\CouponController@search_coupons');
   // *
   Route::post('/redeem-coupons', 'Api\CouponController@redeem_coupons');

   Route::get('/store-locations', 'Api\LocationController@store_locations');
   Route::get('/store-location-get-direction', 'Api\LocationController@store_location_get_direction');
   
   // *
   Route::get('/get-services', 'Api\ServiceController@get_services');
   // *
   Route::get('/all-services', 'Api\ServiceController@all_services');

   Route::get('/get-available-booking-times', 'Api\BookingController@get_booking_times');

   Route::get('/my-loyality-points', 'Api\AuthController@my_loyality_points');   

   // *
   Route::get('/redeemable-vouchers', 'Api\VouchersController@redeemable_vouchers');
   // *
   Route::post('/redeem-voucher', 'Api\VouchersController@redeem_vouchers');
   // *
   Route::get('/my-vouchers', 'Api\VouchersController@my_vouchers');
   // *
   Route::get('/welcome-voucher', 'Api\VouchersController@welcome_vouchers');
   // *
   Route::get('/voucher-details', 'Api\VouchersController@voucher_details');
   // *
   Route::get('/post-voucher', 'Api\VouchersController@past_voucher');
   


   Route::get('/get-all-offers', 'Api\OfferController@getAllOffers');

   Route::get('/get-all-available-deals', 'Api\DealController@getAllAvailableDeals');

   // *
   Route::post('/send-feedback', 'Api\FeedbackController@send_feedback');
   // *
   Route::get('/get-all-faqs', 'Api\FAQController@get_all_faqs');
   // *
   Route::get('/get-privacy-policy', 'Api\SettingsController@get_privacy_policy');
   // *
   Route::get('/get-terms-and-condition', 'Api\SettingsController@get_terms_and_condition');
   // *
   Route::get('/who-are-we', 'Api\SettingsController@who_we_are');

   // *
   Route::get('/outlet-services', 'Api\OutletController@outlet_services');
   // *
   Route::get('/choose-preference-therapist', 'Api\BookingController@choose_preference_therapist');
   // *
   Route::post('/book-appointment', 'Api\BookingController@book_appointment');
   // *
   Route::get('/booking-list', 'Api\BookingController@booking_list');
   // *
   Route::get('/booking-details/{booking_id}', 'Api\BookingController@booking_details');
   // *
   Route::post('/cancel-booking', 'Api\BookingController@cancel_booking');

   // *
   Route::get('/loyalty-program-progress-tracker', 'Api\LoyaltyProgramController@loyalty_program_progress_tracker');
   // *
   Route::post('/loyalty-program-session-details', 'Api\LoyaltyProgramController@loyalty_program_session_details');
   // *
   Route::get('/loyalty-program-recent-visits', 'Api\LoyaltyProgramController@loyalty_program_recent_visits');
   // *
   Route::get('/loyalty-program-recent-visit/details', 'Api\LoyaltyProgramController@loyalty_program_recent_visit_details');
   // *
   Route::get('/loyalty-program-reward-voucher', 'Api\LoyaltyProgramController@loyalty_program_reward_voucher');


   // *
   Route::get('/loyalty-point', 'Api\LoyaltyPointController@loyalty_point');

   // *
   Route::get('/health-question/display-form', 'Api\HealthQuestionController@display_form');
   // *
   Route::post('/health-question/store', 'Api\HealthQuestionController@store');
   // *
   Route::get('/health-question/get-data', 'Api\HealthQuestionController@get_data');

   // *
   Route::get('/loyalty-shop/redeemable-products', 'Api\LoyaltyShopProductController@redeemable_product_list');
   // *
   Route::get('/loyalty-shop/product-details', 'Api\LoyaltyShopProductController@product_details');
   // *
   Route::post('/loyalty-shop/redeem-products', 'Api\LoyaltyShopProductController@redeem_products');
   // *
   Route::get('/loyalty-shop/after-redeem-products', 'Api\LoyaltyShopProductController@after_redeem_products');
   // *
   Route::get('/loyalty-shop/past-product', 'Api\LoyaltyShopProductController@past_product');
   // *
   Route::get('/loyalty-shop/welcome-product', 'Api\LoyaltyShopProductController@welcome_product');


   // *
   Route::get('/loyalty-shop/redeemable-services', 'Api\LoyaltyShopServiceController@redeemable_service_list');
   // *
   Route::get('/loyalty-shop/service-details', 'Api\LoyaltyShopServiceController@service_details');

   // *
   Route::post('/get-consumer-player-id', 'Api\AuthController@get_consumer_player_id');

   // get all onesignal notification
   Route::get('/get-all-notification', 'Api\NotificationController@get_all_notification');

});


// forget password start *
Route::post('/forget-password/send-otp', 'Api\PasswordResetController@send_otp');
Route::post('/forget-password/verify-otp', 'Api\PasswordResetController@verify_otp');
Route::post('/forget-password/reset-password', 'Api\PasswordResetController@reset_password');
// forget password end


// *
Route::get('/category-list', 'Api\CategoryController@category_list');

// *
Route::get('/services-by-category/{id}', 'Api\ServiceController@services_by_category');

// *
Route::get('/all-promotions', 'Api\PromotionController@all_promotions');
// *
Route::get('/promotion-details', 'Api\PromotionController@promotion_details');

// *
Route::get('/all-happenings', 'Api\HappeningController@all_happenings');
// *
Route::get('/happening-details', 'Api\HappeningController@happening_details');

// *
Route::get('/category-for-loyalty-program', 'Api\CategoryController@category_list_loyalty_program');

// *
Route::get('/no-login/loyalty-program-progress-tracker', 'Api\LoyaltyProgramController@loyalty_program_progress_tracker_without_login');

// *
Route::get('/get-service-banner-1', 'Api\SettingsController@getServiceBanner_1');
// *
Route::get('/get-service-banner-2', 'Api\SettingsController@getServiceBanner_2');
// *
Route::get('/get-service-banner-settings', 'Api\SettingsController@get_ServiceBanner_settings');

// *
Route::get('/get-outlets', 'Api\OutletController@get_outlets');
// *
Route::get('/outlets-get-direction', 'Api\OutletController@outlets_get_directions');
// *
Route::get('/outlet-details/{outlet_id}', 'Api\OutletController@outlet_details');

// *
Route::get('/no-login/redeemable-vouchers', 'Api\VouchersController@redeemable_vouchers_without_login');
// *
Route::get('/no-login/loyalty-shop/redeemable-products', 'Api\LoyaltyShopProductController@redeemable_product_list_without_login');
// *
Route::get('/no-login/loyalty-shop/product-details', 'Api\LoyaltyShopProductController@product_details_without_login');
// *
Route::get('/no-login/voucher-details', 'Api\VouchersController@voucher_details_without_login');


// Route::get('/service', 'Api\AuthController@serviceHomepage');
