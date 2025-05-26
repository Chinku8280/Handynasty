<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['branch'])->group(function () {  
    Route::get('/branch/{branchName}/dashboard', 'BranchAuth\DashboardController@index')->name('branch.dashboard');
});

Route::get('/{location}/login', 'BranchAuth\LoginController@showLoginForm');
Route::post('/branch-login', 'BranchAuth\LoginController@login')->name('branch.login');


Route::prefix('{locationSlug}')->group(function () {
    Route::get('login', 'BranchAuth\LoginController@showLoginForm')->name('branch.login');
    Route::post('login', 'BranchAuth\LoginController@login');
    Route::post('logout', 'BranchAuth@LoginController@logout');
});


Auth::routes();

// Admin routes

Route::group(['middleware' => 'auth'], function () {
   
    // Admin routes
    Route::group(
        ['namespace' => 'Admin', 'prefix' => 'account', 'as' => 'admin.'], function () {

        // categories start

        Route::get('/categories/get-outlets-by-single-categoryId', 'CategoryController@get_outlets_by_single_categoryId')->name('categories.get-outlets-by-single-categoryId');
        Route::post('/categories/sort-update', 'CategoryController@sort_update')->name('categories.sort-update');
        Route::post('/categories/send-notification', 'CategoryController@send_notification')->name('categories.send-notification');
        Route::get('/get-categories-by-single-outlet', 'CategoryController@get_categories_by_single_outlet')->name('get-categories-by-single-outlet');

        //  categories ned

        // service start

        Route::post('business-services/store-images', 'BusinessServiceController@storeImages')->name('business-services.storeImages');
        Route::post('business-services/update-images', 'BusinessServiceController@updateImages')->name('business-services.updateImages');
        Route::get('/get-services-by-category', 'BusinessServiceController@get_services_by_category')->name('get-services-by-category');
        Route::get('/get-total-hours-by-services', 'BusinessServiceController@get_total_hours_by_services')->name('get-total-hours-by-services');
        Route::get('/get-service-details', 'BusinessServiceController@get_service_details')->name('get-service-details');
        Route::post('/business-services/sort-update', 'BusinessServiceController@sort_update')->name('business-services.sort-update');
        Route::post('/business-services/send-notification', 'BusinessServiceController@send_notification')->name('business-services.send-notification');
        Route::get('/get-services-by-category-outlet', 'BusinessServiceController@get_services_by_category_outlet')->name('get-services-by-category-outlet');

        // service end

        Route::get('locations/edit/{id}', 'LocationController@edit')->name('locations.edit');
        
        // coupon start

        Route::get('coupons/data', 'CouponController@data')->name('coupons.data');
        Route::post('/coupons/send-notification', 'CouponController@send_notification')->name('coupons.send-notification');
        
        // coupon end

        Route::get('packages/data', 'PackageController@data')->name('packages.data');

        Route::post('assign-package', 'PackageController@assignPackage')->name('assign.package');

        Route::post('todo-items/update-todo-item', 'TodoItemController@updateTodoItem')->name('todo-items.updateTodoItem');

        Route::post('save-booking-times-field', 'SettingController@saveBookingTimesField')->name('save-booking-times-field');

        Route::put('update-privacy-policy', 'SettingController@updatePrivacyPolicy')->name('update-privacy-policy');

        Route::put('update-terms-condition', 'SettingController@updateTermCondition')->name('update-terms-condition');

        Route::put('update-who-we-are-content', 'SettingController@updateWhoWeAreContent')->name('who-we-are-content.update');

        // service banner
        Route::post('/service-banner/store', 'SettingController@storeBanner')->name('service-banner.store');
        Route::post('/service-banner/destroy/{id}', 'SettingController@deleteBanner')->name('service-banner.destroy');
        Route::post('/service-banner/update-status', 'SettingController@updateBannerStatus')->name('service-banner.update-status');

        Route::resources(
            [
                'branches' => 'LocationController',
                'categories' => 'CategoryController',
                'business-services' => 'BusinessServiceController',
                'pages' => 'PageController',
                'settings' => 'SettingController',
                'booking-times' => 'BookingTimeController',
                'tax-settings' => 'TaxSettingController',
                'currency-settings' => 'CurrencySettingController',
                'language-settings' => 'LanguageSettingController',
                'email-settings' => 'SmtpSettingController',
                'theme-settings' => 'ThemeSettingController',
                'front-theme-settings' => 'FrontThemeSettingController',
                'customers' => 'CustomerController',                         
                'credential' => 'PaymentCredentialSettingController',
                'sms-settings' => 'SmsSettingController',
                'coupons' => 'CouponController',
                // 'sms-settings' => 'SmsSettingController',
                'todo-items' => 'TodoItemController',
                'deals' => 'DealController',
                'vouchers' => 'VouchersController',
                'offers' => 'OfferController',
                'packages' => 'PackageController',
                'discover' => 'DiscoverController',
                'promotion' => 'PromotionController',
                'happening' => 'HappeningController',
                'faq' => 'FAQController',
                'outlet' => 'OutletController',
                'feedback' => 'FeedbackController',
            ]
        );

        // customer start

        // loyalty point
        Route::post('customers/{customer}/store-loyalty-points', 'CustomerController@storeLoyaltyPoints')->name('customers.storeLoyaltyPoints');
        // coupon
        Route::post('/customers/coupon-used-store', 'CustomerController@store_coupon_used')->name('customers.coupon-used-store');
        // voucher
        Route::post('/customers/voucher-used-store', 'CustomerController@store_voucher_used')->name('customers.voucher-used-store');
        // loyalty shop product
        Route::post('/customers/loyalty-shop-product-used-store', 'CustomerController@store_loyalty_shop_product_used')->name('customers.loyalty-shop-product-used-store');
        // health question
        Route::post('/customers/health-question/store', 'HealthQuestionController@store')->name('customers.health-question.store');      

        // cusatomer end


        // voucher start

        // Route::post('selectBranch', 'VouchersController@selectBranch')->name('vouchers.selectBranch');
        // Route::post('selectServices', 'VouchersController@selectServices')->name('vouchers.selectServices');
        // Route::get('resetSelection', 'VouchersController@resetSelection')->name('vouchers.resetSelection');
        // Route::post('makeVoucher', 'VouchersController@makeVoucher')->name('vouchers.makevoucher');

        Route::post('/vouchers/send-notification', 'VouchersController@send_notification')->name('vouchers.send-notification');
        
        // voucher end

        

        // Notifications start
        
        Route::post('sendVoucherNotification', 'NotificationController@sendVoucherNotification')->name('vouchers.sendNotification');
        Route::post('sendCouponNotification', 'NotificationController@sendCouponNotification')->name('coupons.sendNotification');
        // Route::post('mark-notification-read', ['uses' => 'NotificationController@markAllRead'])->name('mark-notification-read');
        
        // Notifications end

        // deal start
        Route::post('selectLocation', 'DealController@selectLocation')->name('deals.selectLocation');
        Route::post('selectServices', 'DealController@selectServices')->name('deals.selectServices');
        Route::get('resetSelection', 'DealController@resetSelection')->name('deals.resetSelection');
        Route::post('makeDealWithMultipleLocation', 'DealController@makeDealWithMultipleLocation')->name('deals.makeDealWithMultipleLocation');
        Route::post('makeDeal', 'DealController@makeDeal')->name('deals.makeDeal');
        Route::post('makeDealMultipleLocation', 'DealController@makeDealMultipleLocation')->name('deals.makeDealMultipleLocation');
        // deal end

        // promotion start
        Route::post('/promotion/selectLocation', 'PromotionController@selectLocation')->name('promotion.selectLocation');
        Route::post('/promotion/selectServices', 'PromotionController@selectServices')->name('promotion.selectServices');
        Route::get('/promotions/resetSelection', 'PromotionController@resetSelection')->name('promotion.resetSelection');
        Route::post('/promotion/makeDeal', 'PromotionController@makeDeal')->name('promotion.makeDeal');
        // promotion end

        Route::post('change-language/{code}', 'SettingController@changeLanguage')->name('changeLanguage');

        Route::post('change-language/{code}', 'SettingController@changeLanguage')->name('changeLanguage');
        Route::post('role-permission/add-role', 'RolePermissionSettingController@addRole')->name('role-permission.addRole');
        Route::post('role-permission/add-members/{role_id}', 'RolePermissionSettingController@addMembers')->name('role-permission.addMembers');
        Route::get('role-permission/get-members/{role_id}', 'RolePermissionSettingController@getMembers')->name('role-permission.getMembers');
        Route::get('role-permission/get-members-to-add/{id}', 'RolePermissionSettingController@getMembersToAdd')->name('role-permission.getMembersToAdd');
        Route::delete('role-permission/remove-member', 'RolePermissionSettingController@removeMember')->name('role-permission.removeMember');
        Route::get('role-permission/data', 'RolePermissionSettingController@data')->name('role-permission.data');
        Route::post('role-permission/toggleAllPermissions', 'RolePermissionSettingController@toggleAllPermissions')->name('role-permission.toggleAllPermissions');
        Route::resource('role-permission', 'RolePermissionSettingController');

        Route::put('change-language-status/{id}', 'LanguageSettingController@changeStatus')->name('language-settings.changeStatus');
        Route::get('smtp-settings/sent-test-email', ['uses' => 'SmtpSettingController@sendTestEmail'])->name('email-settings.sendTestEmail');
        Route::get('reports/earningTable', ['uses' => 'ReportController@earningTable'])->name('reports.earningTable');
        Route::post('reports/earningChart', ['uses' => 'ReportController@earningReportChart'])->name('reports.earningReportChart');
        Route::get('reports', ['uses' => 'ReportController@index'])->name('reports.index');

        Route::get('reports/salesTable', ['uses' => 'ReportController@salesTable'])->name('reports.salesTable');
        Route::get('reports/tabularTable', ['uses' => 'ReportController@tabularTable'])->name('reports.tabularTable');
        Route::post('reports/salesChart', ['uses' => 'ReportController@salesReportChart'])->name('reports.salesReportChart');

        /* Graphical reporting section  */
        Route::get('reports/userTypeChart', ['uses' => 'ReportController@userTypeChart'])->name('reports.userTypeChart');
        Route::get('reports/serviceTypeChart', ['uses' => 'ReportController@serviceTypeChart'])->name('reports.serviceTypeChart');
        Route::get('reports/bookingSourceChart', ['uses' => 'ReportController@bookingSourceChart'])->name('reports.bookingSourceChart');
        Route::post('reports/bookingPerDayChart', ['uses' => 'ReportController@bookingPerDayChart'])->name('reports.bookingPerDayChart');
        Route::post('reports/paymentPerDayChart', ['uses' => 'ReportController@paymentPerDayChart'])->name('reports.paymentPerDayChart');
        Route::post('reports/bookingPerMonthChart', ['uses' => 'ReportController@bookingPerMonthChart'])->name('reports.bookingPerMonthChart');
        Route::post('reports/paymentPerMonthChart', ['uses' => 'ReportController@paymentPerMonthChart'])->name('reports.paymentPerMonthChart');
        Route::post('reports/bookingPerYearChart', ['uses' => 'ReportController@bookingPerYearChart'])->name('reports.bookingPerYearChart');
        Route::post('reports/bookingPerYearChart', ['uses' => 'ReportController@bookingPerYearChart'])->name('reports.bookingPerYearChart');
        Route::post('reports/paymentPerYearChart', ['uses' => 'ReportController@paymentPerYearChart'])->name('reports.paymentPerYearChart');

        Route::get('reports/customer', ['uses' => 'ReportController@customer'])->name('reports.customer');
        Route::get('pos/select-customer', ['uses' => 'POSController@selectCustomer'])->name('pos.select-customer');
        Route::get('pos/search-customer', ['uses' => 'POSController@searchCustomer'])->name('pos.search-customer');
        Route::get('pos/filter-services', ['uses' => 'POSController@filterServices'])->name('pos.filter-services');
        Route::get('pos/addCart', ['uses' => 'POSController@addCart'])->name('pos.addCart');
        Route::post('pos/apply-coupon', ['uses' => 'POSController@applyCoupon'])->name('pos.apply-coupon');
        Route::post('pos/update-coupon', ['uses' => 'POSController@updateCoupon'])->name('pos.update-coupon');
        Route::resource('pos', 'POSController');

        Route::post('employee/changeRole', 'EmployeeController@changeRole')->name('employee.changeRole');
        Route::resource('employee', 'EmployeeController');
        Route::resource('employee-group', 'EmployeeGroupController');

        Route::resource('update-application', 'UpdateApplicationController');
        Route::resource('search', 'SearchController');

        // 
        Route::get('dashboard', 'ShowDashboard')->name('dashboard');
        Route::get('dashboard/get-dashboard-data', 'ShowDashboard@get_dashboard_data')->name('dashboard.get-dashboard-data');

        Route::post('bookings/update-coupon', ['uses' => 'BookingController@updateCoupon'])->name('bookings.update-coupon');
        Route::post('multiStatusUpdate', ['uses' => 'BookingController@multiStatusUpdate'])->name('bookings.multiStatusUpdate');
        Route::post('sendReminder', ['uses' => 'BookingController@sendReminder'])->name('bookings.sendReminder');
        Route::post('bookings/{status?}', ['uses' => 'BookingController@index'])->name('bookings.index');
        Route::post('bookings/requestCancel/{id}', ['uses' => 'BookingController@requestCancel'])->name('bookings.requestCancel');
        Route::get('bookings/download/{id}', ['uses' => 'BookingController@download'])->name('bookings.download');
        Route::resources([
            'bookings' => 'BookingController',
            'profile' => 'ProfileController'
        ]);
        
        
        // loyalty program start

        Route::post('/customer/loyalty-program/store', 'LoyaltyProgramController@store')->name('customer.loyalty-program.store');
        Route::get('/customer/loyalty-program/get-progress-tracker', 'LoyaltyProgramController@get_loyalty_program_progress_tracker')->name('customer.loyalty-program.get-progress-tracker');
        Route::get('/customer/get-loyalty-program-history-table-data', 'LoyaltyProgramController@get_loyalty_program_history_table_data')->name('customer.get-loyalty-program-history-table-data');
        Route::post('/customer/loyalty-program-history-table-data/delete', 'LoyaltyProgramController@delete_loyalty_program_history_table_data')->name('customer.loyalty-program-history-table-data.delete');

        // loyalty program end

        // loyalty program settings
        Route::post('/settings/loyalty-program-setting/update', 'LoyaltyProgramSettingController@update')->name('settings.loyalty-program-setting.update');

        // loyalty point settings
        Route::post('/settings/loyalty-point-setting/update', 'LoyaltyPointSettingController@update')->name('settings.loyalty-point-setting.update');

        // notification settings
        Route::post('/settings/notification-setting/update', 'NotificationSettingController@update_setting')->name('settings.notification-setting.update');
    
        // products start

        Route::resource('products', 'ProductController');
        Route::post('products/store-images', 'ProductController@store_images')->name('products.store-images');
        Route::post('products/update-images', 'ProductController@update_images')->name('products.update-images');

        // products end

        // loyalty shop start

        Route::resource('loyalty-shop', 'LoyaltyShopController');

        // loyalty shop end
    
    });

    Route::get('change-mobile', 'VerifyMobileController@changeMobile')->name('changeMobile');
    Route::post('/send-otp-code', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode');
    Route::post('/send-otp-code/account', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode.account');
    Route::post('/verify-otp-phone', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode');
    Route::post('/verify-otp-phone/account', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode.account');
    Route::get('/remove-session', 'VerifyMobileController@removeSession')->name('removeSession');

    Route::get('/clear-cache', 'Admin\AdminController@clearCache')->name('cache.clear');
});


Route::group(
    ['namespace' => 'Front', 'as' => 'front.'], function () {
    Route::get('/', ['uses' => 'FrontController@index'])->name('index');

    Route::group(['middleware' => 'cookieRedirect'], function () {
        Route::get('/booking', ['uses' => 'FrontController@bookingPage'])->name('bookingPage');
        Route::get('/checkout', ['uses' => 'FrontController@checkoutPage'])->name('checkoutPage');
    });
    Route::get('/cart', ['uses' => 'FrontController@cartPage'])->name('cartPage');
    Route::get('/apply-coupon', ['uses' => 'FrontController@applyCoupon'])->name('apply-coupon');
    Route::get('/update-coupon', ['uses' => 'FrontController@updateCoupon'])->name('update-coupon');
    Route::get('/remove-coupon', ['uses' => 'FrontController@removeCoupon'])->name('remove-coupon');
    Route::get('/search', ['uses' => 'FrontController@searchServices'])->name('searchServices');
    Route::post('/add-or-update-product', ['uses' => 'FrontController@addOrUpdateProduct'])->name('addOrUpdateProduct');
    Route::post('/add-booking-details', ['uses' => 'FrontController@addBookingDetails'])->name('addBookingDetails');
    Route::post('/delete-product/{id}', ['uses' => 'FrontController@deleteProduct'])->name('deleteProduct');
    Route::post('/delete-front-product/{id}', ['uses' => 'FrontController@deleteProduct'])->name('deleteFrontProduct');
    Route::post('/update-cart', ['uses' => 'FrontController@updateCart'])->name('updateCart');
    Route::post('/check-user-availability', ['uses' => 'FrontController@checkUserAvailability'])->name('checkUserAvailability');
    Route::post('/grabDeal', ['uses' => 'FrontController@grabDeal'])->name('grabDeal');

    Route::post('/save-booking', ['uses' => 'FrontController@saveBooking'])->name('saveBooking');
    Route::group(['middleware' => 'mobileVerifyRedirect'], function () {
        Route::get('payment-gateway', array('as' => 'payment-gateway','uses' => 'FrontController@paymentGateway',));
        Route::get('offline-payment/{bookingId?}', array('as' => 'offline-payment','uses' => 'FrontController@offlinePayment',));
        Route::get('/payment-success/{paymentID?}', ['uses' => 'FrontController@paymentSuccess'])->name('payment.success');
        Route::get('/payment-fail/{paymentID?}', ['uses' => 'FrontController@paymentFail'])->name('payment.fail');
    });
    Route::post('/booking-slots', ['uses' => 'FrontController@bookingSlots'])->name('bookingSlots');
    Route::post('contact', ['uses' => 'FrontController@contact'])->name('contact');

    Route::get('paypal-recurring', array('as' => 'paypal-recurring','uses' => 'PaypalController@payWithPaypalRecurrring',));

    // route for view/blade file
    Route::get('paywithpaypal', array('as' => 'paywithpaypal','uses' => 'PaypalController@payWithPaypal',));
    // route for post request
    Route::get('paypal/{bookingId?}', array('as' => 'paypal','uses' => 'PaypalController@paymentWithpaypal',));
    // route for check status responce
    Route::get('paypal-status/{status?}', array('as' => 'status','uses' => 'PaypalController@getPaymentStatus',));

    Route::post('stripe/{bookingId?}', array('as' => 'stripe','uses' => 'StripeController@paymentWithStripe',));

    Route::post('razorpay', 'RazorPayController@paymentWithRazorpay')->name('razorpay');

    Route::post('change-language/{code}', 'FrontController@changeLanguage')->name('changeLanguage');

    Route::get('/{categorySlug}/{serviceSlug}', ['uses' => 'FrontController@serviceDetail'])->name('serviceDetail');

    Route::get('/deal/{dealId}/{dealSlug}', ['uses' => 'FrontController@dealDetail'])->name('dealDetail');
    Route::get('/voucher/{voucherId}/{voucherSlug}', ['uses' => 'VouchersController@voucherDetail'])->name('voucherDetail');

    Route::get('/{slug}', ['uses' => 'FrontController@page'])->name('page');

    
    
});

// ************ Outlet routes start ************

Route::group(['prefix' => 'outlet/{outlet_slug}'], function () {

    // login
    Route::get('/login', 'Outlet\OutletLoginController@showLoginForm')->name('outlet.login');
    Route::post('/login-submit', 'Outlet\OutletLoginController@login')->name('outlet.login-submit');

    Route::group(['middleware' => 'OutletUser'], function () {

        // logout
        Route::post('/logout', 'Outlet\OutletLoginController@logout')->name('outlet.logout');

        // dashboard
        Route::get('/dashboard', 'Outlet\OutletDashboardController@index')->name('outlet.dashboard'); 

        // // outlet
        // Route::get('/outlet', 'Admin\OutletController@index')->name('outlet.outlet.index'); 
        // // categories
        // Route::get('/categories', 'Admin\OutletController@index')->name('outlet.categories.index'); 
        // // business-services
        // Route::get('/business-services', 'Admin\BusinessServiceController@index')->name('outlet.business-services.index'); 
        // // customers
        // Route::get('/customers', 'Admin\CustomerController@index')->name('outlet.customers.index'); 
        // // employee
        // Route::get('/employee', 'Admin\EmployeeController@index')->name('outlet.employee.index'); 
        // // bookings
        // Route::get('/bookings', 'Admin\BookingController@index')->name('outlet.bookings.index'); 

    });
});

// ************ Outlet routes end ************


// ************ pos routes start ************

Route::group(['prefix' => 'outlet/pos/{outlet_slug}'], function () {

    // login
    Route::get('/login', 'Pos\PosAuthController@showLoginForm')->name('pos.login');
    Route::post('/login-submit', 'Pos\PosAuthController@login')->name('pos.login-submit');

    Route::group(['middleware' => 'PosUser'], function () {
        // logout
        Route::post('/logout', 'Pos\PosAuthController@logout')->name('pos.logout');

        // dashboard
        Route::get('/dashboard', 'Pos\PosDashboardController@index')->name('pos.dashboard'); 

        // session
        Route::post('/open-session', 'Pos\PosSessionController@open_session')->name('pos.open-session');

        // pos
        Route::get('/pos', 'Pos\PosController@index')->name('pos.pos'); 

        // bookings
        Route::get('/bookings', 'Pos\BookingController@index')->name('pos.bookings'); 
    });

});

// ************ pos routes end ************