<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\SocialMedia;
use Illuminate\Support\Facades\DB;

if (!function_exists('formatCurrency')) {
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


// function formatNumber($number)
// {
//     return number_format($number, 2);
// }

function imageDir()
{
    return 'https://admin.dzeera.com/';
}

function getSocialMedia()
{
    return SocialMedia::where('brand_id', session('active-brand'))->get();
}

function getNewArrivals()
{
    $data_obj = DB::select("
            select p.*, pi2.file_name from products p
            join product_tags pt on pt.product_id = p.id
            left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
            where pt.tag_id = 1 and p.product_availability = 'y'
            and p.brand_id = ?
            limit 10
        ", [session('active-brand')]);

    foreach ($data_obj as $key => $value) {
        $data_obj[$key]->base_price = 0;
        $data_obj[$key]->disc = 0;
        $data_obj[$key]->price = 0;

        $data_opt = DB::select("
            select base_price, disc, price from product_options po where product_id = ?
            and option_availability = 'y' order by base_price asc
            limit 1
            ", [$value->id]);

        if ($data_opt) {
            $data_obj[$key]->base_price = $data_opt[0]->base_price;
            $data_obj[$key]->disc       = $data_opt[0]->disc;
            $data_obj[$key]->price      = $data_opt[0]->price;
        }
    }
    return $data_obj;
}

if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        $number = str_replace(',', '.', $number);
        return number_format($number, 0, ',', '.');
    }
}

function getHighlightedProduct(){
    $data = DB::select("select p.*, pi2.is_thumbnail, pi2.file_name   from products p
    join product_images pi2 on p.id = pi2.product_id
    where highlight = 1 and pi2.is_thumbnail = 1
    and p.brand_id = ?
    order by updated_at desc  limit 3", [session('active-brand')]);
    return $data;
}

function seeMore($content, $maxLength = 100){
    $content_decoded = htmlspecialchars_decode($content);
    if (strlen($content_decoded) > $maxLength) {
        $shortContent = substr($content_decoded, 0, $maxLength);
            // $remainingContent = substr($content_decoded, $maxLength);
        $result = $shortContent . '<span id="seeMoreDots">...</span>';
            // $result .= '<span id="seeMoreContent" style="display: none;">' . $remainingContent . '</span>';
            $result .= '<a href="#" id="seeMoreLink" onclick="openSeeMore()">See More</a>';
            $result .= '<script>
                function openSeeMore() {
                    var content = document.getElementById("seeMoreContent").innerHTML;
                    var win = window.open("", "_blank");
                    win.document.write(content);
                    win.document.close();
                }
            </script>';
    } else {
        $result = $content_decoded;
    }

    return $result;
}

function getHighlightedCategories(){
    $data = DB::select("select * from categories c where highlight = 1
    and brand_id = ?
    order by category_name asc limit 3", [session('active-brand')]);
    return $data;
}

function getProductByCategoryIndex($category_id){
    $data_obj = DB::select("
        select p.*, pi2.file_name from products p
        join product_tags pt on pt.product_id = p.id
        left join product_images pi2 on pi2.product_id = p.id
        where pt.tag_id = 1 and p.product_availability = 'y'
        and p.brand_id = ? and p.category_id = ?
        limit 10
    ", [session('active-brand'), $category_id]);

    foreach ($data_obj as $key => $value) {
    $data_obj[$key]->base_price = 0;
    $data_obj[$key]->disc = 0;
    $data_obj[$key]->price = 0;

    $data_opt = DB::select("
        select base_price, disc, price from product_options po where product_id = ?
        and option_availability = 'y' order by base_price asc
        limit 1
        ", [$value->id]);

    if ($data_opt) {
        $data_obj[$key]->base_price = $data_opt[0]->base_price;
        $data_obj[$key]->disc       = $data_opt[0]->disc;
        $data_obj[$key]->price      = $data_opt[0]->price;
    }
    }
    return $data_obj;
}


function getVids(){
    $data = DB::select("
    select* from video_embeds ve where is_active = 1
    and brand_id  = ?
    ", [session('active-brand')]);
    return $data;
}
