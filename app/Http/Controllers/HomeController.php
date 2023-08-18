<?php

namespace App\Http\Controllers;
// use App\Helper\Helpers;

use App\Models\BannerImage;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Show the application's home page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($brandslug = null)
    {
        if ($brandslug) {
            $brand = Brand::where('slug', $brandslug)->first();
        } else {
            $brand = Brand::where('main_brand', 1)->first();
        }
        $brand_id = $brand->id;
        session(['active-brand' => $brand->id]);

        //get banner
        $banner = BannerImage::All();

        //get new arrival
        $newArrivals = getNewArrivals();

        // dd($newArrivals);

        return view('home', compact('brand_id', 'banner', 'newArrivals'));
    }
}
