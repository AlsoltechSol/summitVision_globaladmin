<?php

namespace App\Http\Controllers\Web_Api;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSection;
use App\Models\Plan;
use App\Models\Utility;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index(){

        $plans = Plan::get();
        $get_section = LandingPageSection::orderBy('section_order', 'ASC')->get();
        $settings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
        $logo = Utility::get_file('uploads/landing_page_image/');
        $sup_logo = Utility::get_file('uploads/logo');
        $adminSettings = Utility::settings();
        
        $getseo = \App\Models\Utility::getSeoSetting();
        $metatitle = isset($getseo['meta_title']) ? $getseo['meta_title'] : '';
        $metadesc = isset($getseo['meta_description']) ? $getseo['meta_description'] : '';
        $meta_image = \App\Models\Utility::get_file('uploads/meta/');
        $meta_logo = isset($getseo['meta_image']) ? $getseo['meta_image'] : '';
        $enable_cookie = \App\Models\Utility::getCookieSetting('enable_cookie');
        
        $setting = \App\Models\Utility::colorset();
        $SITE_RTL = Utility::getValByName('SITE_RTL');
        $color = !empty($setting['theme_color']) ? $setting['theme_color'] : 'theme-3';
        
        return response()->json([
            'status' => 200,
            'plans' => $plans , 
            'get_section' => $get_section,
            'settings' => $settings,
            'logo' => $logo,
            'sup_logo' => $sup_logo,
            'adminSettings' => $adminSettings,
            'metatitle' => $metatitle,
            'metadesc' => $metadesc,
            'meta_image' => $meta_image,
            'meta_logo' => $meta_logo,
            'enable_cookie' => $enable_cookie,
            'setting' => $setting,
            'SITE_RTL' => $SITE_RTL,
            'color' => $color,
        ]);
    }

    public function get_landing_page_data(){
       

        
    }
}
