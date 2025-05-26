<?php

namespace App\Http\Controllers\Admin;

use App\BookingTime;
use App\CompanySetting;
use App\Currency;
use App\Helper\Formats;
use App\Helper\Files;
use App\Helper\Reply;
use App\Language;
use App\Media;
use App\PaymentGatewayCredentials;
use App\SmtpSetting;
use App\TaxSetting;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSetting;
use App\Module;
use App\Permission;
use App\Role;
use App\SmsSetting;
use Carbon\Carbon;
use App\PrivacyPolicy;
use App\TermsAndCondition;
use App\WhoWeArePage;
use App\ServiceBanner;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        view()->share('pageTitle', __('menu.settings'));

    }

    public function index(){
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $bookingTimes = BookingTime::all();
        $images = Media::select('id', 'file_name')->latest()->get();
        $tax = TaxSetting::first();
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        $dateFormats = Formats::dateFormats();
        $timeFormats = Formats::timeFormats();
        $dateObject = Carbon::now($this->settings->timezone);
        $currencies = Currency::all();
        $enabledLanguages = Language::where('status', 'enabled')->orderBy('language_name')->get();
        $smtpSetting = SmtpSetting::first();
        $credentialSetting = PaymentGatewayCredentials::first();
        $smsSetting = SmsSetting::first();
        $privacypolicy = PrivacyPolicy::first();
        $terms = TermsAndCondition::first();
        $whowearecontent = WhoWeArePage::first();
        $bannerImage_1 = ServiceBanner::where('banner_type', 1)->get();
        $bannerImage_2 = ServiceBanner::where('banner_type', 2)->get();
        $roles = Role::where('name', '<>', 'administrator')->get();
        $totalPermissions = Permission::count();
        $modules = Module::all();

        $client = new Client();
        $res = $client->request('GET', config('froiden_envato.updater_file_path'), ['verify' => false]);
        $lastVersion = $res->getBody();
        $lastVersion = json_decode($lastVersion, true);
        $currentVersion = File::get('version.txt');

        $description = $lastVersion['description'];

        $newUpdate = 0;
        if (version_compare($lastVersion['version'], $currentVersion) > 0)
        {
            $newUpdate = 1;
        }
        $updateInfo = $description;
        $lastVersion = $lastVersion['version'];

        $appVersion = File::get('version.txt');
        $laravel = app();
        $laravelVersion = $laravel::VERSION;

        $loyalty_program_settings = DB::table('loyalty_program_settings')->first();
        $loyalty_point_settings = DB::table('loyalty_point_settings')->first();
        $loyalty_program_stamp_text_settings = DB::table('loyalty_program_stamp_text_settings')->get();

        $service_banner_settings = DB::table('service_banner_settings')->first();
        $notification_settings = DB::table('notification_settings')->first();

        return view('admin.settings.index', compact('bookingTimes', 'images', 'tax', 'timezones', 'dateFormats', 'timeFormats', 'dateObject', 'currencies', 'enabledLanguages', 'smtpSetting', 'lastVersion', 'updateInfo', 'appVersion', 'laravelVersion', 'newUpdate', 'credentialSetting', 'smsSetting', 'roles', 'totalPermissions', 'modules', 'privacypolicy', 'whowearecontent', 'bannerImage_1', 'bannerImage_2', 'terms', 'loyalty_program_settings', 'loyalty_point_settings', 'loyalty_program_stamp_text_settings', 'service_banner_settings', 'notification_settings'));
    }

    public function update(UpdateSetting $request, $id){
        abort_if(!$this->user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'), 403);

        $setting = CompanySetting::first();
        $setting->company_name = $request->company_name;
        // $setting->multi_task_user = $request->multi_task_user;
        $setting->company_email = $request->company_email;
        $setting->company_phone = $request->company_phone;
        $setting->address = $request->address;
        $setting->date_format = $request->date_format;
        $setting->time_format = $request->time_format;
        $setting->website = $request->website;
        $setting->timezone = $request->timezone;
        $setting->locale = $request->input('locale');
        $setting->currency_id = $request->currency_id;
        if ($request->hasFile('logo')) {
            $setting->logo = Files::upload($request->logo,'logo');
        }
        $setting->save();

        if ($setting->currency->currency_code !== 'INR') {
            $credential = PaymentGatewayCredentials::first();

            if ($credential->razorpay_status == 'active') {
                $credential->razorpay_status = 'deactive';

                $credential->save();
            }
        }
        return Reply::redirect(route('admin.settings.index'), __('messages.updatedSuccessfully'));
    }

    public function changeLanguage($code)
    {
        $language = Language::where('language_code', $code)->first();

        if ($language) {
            $this->settings->locale = $code;
        }
        else if ($code == 'en') {
            $this->settings->locale = 'en';
        }

        $this->settings->save();

        return Reply::success(__('messages.languageChangedSuccessfully'));
    }

    public function saveBookingTimesField(Request $request)
    {

        $booking_per_day = is_null($request->no_of_booking_per_customer) ? 0 : $request->no_of_booking_per_customer;

        $setting = CompanySetting::first();

        $setting->booking_per_day       = $booking_per_day;
        $setting->multi_task_user       = $request->multi_task_user;
        $setting->employee_selection    = $request->employee_selection;
        $setting->disable_slot          = $request->disable_slot;
        $setting->booking_time_type     = $request->booking_time_type;
        $setting->save();

        if($request->disable_slot=='enabled'){
            DB::table('payment_gateway_credentials')->where('id', 1)->update(['show_payment_options' => 'hide', 'offline_payment' => 1]);
        }

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function updatePrivacyPolicy(Request $request)
    {
        $request->validate([
            'privacy_policy' => 'required|string',
        ]);

        $privacyPolicyContent = $request->input('privacy_policy');
    
        $privacyPolicy = PrivacyPolicy::find(1);
    
        if (!$privacyPolicy) {
            return response()->json(['message' => 'Privacy Policy not found'], 404);
        }
    
        $privacyPolicy->update([
            'privacy_policy' => $privacyPolicyContent,
        ]);
        
        return Reply::success(__('messages.updatedSuccessfully'));
    }    

    public function updateWhoWeAreContent(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable',
        ]);

        $whoWeArePage = WhoWeArePage::find(1);

        if(!$whoWeArePage)
        {
            $whoWeArePage = new WhoWeArePage();
        }
    
        $whoWeArePage->title = $request->input('title');
        $whoWeArePage->description = $request->input('description');
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('user-uploads/who-we-are'), $imageName);
        
            $whoWeArePage->image = $imageName;
        }
    
        $whoWeArePage->save();
    
        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function storeBanner(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'service_banner_image' => 'required',
            ],
            [],
            [
                'service_banner_image' => 'Service Banner Image',
            ]
        );

        if ( $validator->fails() ) {
            return response()->json( ['status'=>'error', 'errors' => $validator->errors() ] );
        }
        else
        {
            $service_banner = new ServiceBanner();

            if ($request->hasFile('service_banner_image')) 
            {      
                $service_banner_image = $request->file('service_banner_image');
                $imageName = time() . '.' . $service_banner_image->getClientOriginalExtension();

                if($request->banner_type == 1)
                {
                    $service_banner_image->move(public_path('user-uploads/service-banner-1'), $imageName);
                }
                else
                {
                    $service_banner_image->move(public_path('user-uploads/service-banner-2'), $imageName);
                }
            
                $service_banner->image = $imageName;
                $service_banner->banner_type = $request->banner_type;
            }
        
            $service_banner->save();

            return Reply::success(__('messages.updatedSuccessfully'));
        }
    }

    public function deleteBanner($id)
    {
        $service_banner = ServiceBanner::find($id);

        if (!$service_banner) 
        {
            return response()->json(['status'=>'failed', 'message' => 'Service Banner Image not found!'], 404);
        }
        else
        {
            if($service_banner->banner_type == 1)
            {
                $oldImagePath = public_path("user-uploads/service-banner-1/".$service_banner->image);
            }
            else
            {
                $oldImagePath = public_path("user-uploads/service-banner-2/".$service_banner->image);
            }

            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $service_banner->delete();

            return Reply::success(__('messages.deletedSuccessfully'));
        }
    }

    public function updateBannerStatus(Request $request)
    {
        $service_banner_settings = DB::table('service_banner_settings')->first();

        if (!$service_banner_settings) 
        {
            if($request->banner_type == 2)
            {
                DB::table('service_banner_settings')->insert([
                    'service_banner_two_status' => $request->status,
                ]);
            }
        }
        else
        {
            if($request->banner_type == 2)
            {
                DB::table('service_banner_settings')
                    ->where('id', $service_banner_settings->id)
                    ->update([
                        'service_banner_two_status' => $request->status,
                    ]);
            }
        }

        return response()->json(['status'=>'success', 'message' => 'Service Banner Image status updated successfully!']);
    }
    
    // public function updateBanner(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'nullable',
    //     ]);

    //     $banner = ServiceBanner::findOrFail(1);

    //     if (!$banner) {
    //         return response()->json(['message' => 'Service Banner Image not found!'], 404);
    //     }

    //     if ($request->hasFile('image')) {
    //         $oldImagePath = public_path($banner->image);
    //         if (File::exists($oldImagePath)) {
    //             File::delete($oldImagePath);
    //         }
    
    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('user-uploads'), $imageName);
    
    //         $banner->image = 'user-uploads/' . $imageName;
    //     }
    
    //     $banner->save();
    
    //     return Reply::success(__('messages.updatedSuccessfully'));
    // }

    public function updateTermCondition(Request $request)
    {
        $request->validate([
            'terms_condition' => 'required',
        ]);

        $termsandcondition = $request->input('terms_condition');
    
        $terms = TermsAndCondition::find(1);
    
        if (!$terms) {
            return response()->json(['message' => 'Term and Condition not found'], 404);
        }
    
        $terms->update([
            'terms_condition' => $termsandcondition,
        ]);
        
        return Reply::success(__('messages.updatedSuccessfully'));
    }
}
