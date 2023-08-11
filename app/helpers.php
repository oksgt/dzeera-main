<?php

use App\Models\Brand;
use App\Models\Category;

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

