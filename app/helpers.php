<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\SocialMedia;

if (! function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}



function getAllActiveBrand()
{
    $brand = Brand::orderBy('main_brand', 'desc')->get();
    return $brand;
}

function getAllCategoriesByBrand()
{
    $categories = Category::where('brand_id', session('active-brand'))->get();
    return $categories;
}


function formatNumber($number)
{
    return number_format($number, 2);
}

function imageDir(){
    return 'https://admin.dzeera.com/';
}

function getSocialMedia(){
    return SocialMedia::where('brand_id', session('active-brand'))->get();
}
