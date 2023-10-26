<?php

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\SocialMedia;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session as FacadesSession;

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

    $sql = "
    select * from
    (
    SELECT
        CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
        CONCAT(p.id, '-', pco.id) AS item_id,
        pco.id AS color_id,
        pco.color_name,
        p.id AS product_id,
        p.brand_id,
        p.category_id,
        p.product_sku,
        p.product_name,
        p.slug,
        p.product_status,
        p.product_availability,
        p.rating,
        pi2.file_name,
        min_prices.base_price,
        min_prices.disc,
        min_prices.price
    FROM
        product_color_options pco
    JOIN
        products p ON p.id = pco.product_id
    JOIN
        product_tags pt ON pt.product_id = p.id
    LEFT JOIN
        product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
    JOIN
        (
            SELECT
                product_id,
                MIN(price) AS price,
                disc,
                base_price
            FROM
                product_options
            GROUP BY
                product_id
        ) AS min_prices ON p.id = min_prices.product_id
    WHERE
        pt.tag_id = 1
        AND p.product_availability = 'y'
        AND p.brand_id = ?
    GROUP BY
        item_id
    ) as product_view limit 10
        ";

    $data_obj = DB::select($sql, [session('active-brand')]);

    foreach ($data_obj as $key => $value) {
        $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
        $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
        $data_obj[$key]->price      = (int) $data_obj[$key]->price;
    }

    return $data_obj;
}


function getYouMightLike($pid)
{

    $sql = "
    select * from
    (
    SELECT
        CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
        CONCAT(p.id, '-', pco.id) AS item_id,
        pco.id AS color_id,
        pco.color_name,
        p.id AS product_id,
        p.brand_id,
        p.category_id,
        p.product_sku,
        p.product_name,
        p.slug,
        p.product_status,
        p.product_availability,
        p.rating,
        pi2.file_name,
        min_prices.base_price,
        min_prices.disc,
        min_prices.price
    FROM
        product_color_options pco
    JOIN
        products p ON p.id = pco.product_id
    JOIN
        product_tags pt ON pt.product_id = p.id
    LEFT JOIN
        product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
    JOIN
        (
            SELECT
                product_id,
                MIN(price) AS price,
                disc,
                base_price
            FROM
                product_options
            GROUP BY
                product_id
        ) AS min_prices ON p.id = min_prices.product_id
    WHERE
        pt.tag_id = 1
        AND p.product_availability = 'y'
        AND p.id  = ?
    GROUP BY
        item_id
    ) as product_view limit 10
        ";

    $data_obj = DB::select($sql, [$pid]);

    foreach ($data_obj as $key => $value) {
        $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
        $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
        $data_obj[$key]->price      = (int) $data_obj[$key]->price;
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

function getHighlightedProduct()
{
    $data = DB::select("select p.*, pi2.is_thumbnail, pi2.file_name   from products p
    join product_images pi2 on p.id = pi2.product_id
    where highlight = 1 and pi2.is_thumbnail = 1
    and p.brand_id = ?
    order by updated_at desc  limit 3", [session('active-brand')]);
    return $data;
}

function getModalHomePopUp(){
    $data = DB::select("select * from app_modal_popup amp where name = 'home'");
    return $data;
}

function seeMore($content, $maxLength = 100)
{
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

function getHighlightedCategories()
{
    $data = DB::select("select * from categories c where highlight = 1
    and brand_id = ?
    order by category_name asc limit 3", [session('active-brand')]);
    return $data;
}

function getProductByCategoryIndex($category_id)
{

    $sql = "
    select * from
    (
    SELECT
        CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
        CONCAT(p.id, '-', pco.id) AS item_id,
        pco.id AS color_id,
        pco.color_name,
        p.id AS product_id,
        p.brand_id,
        p.category_id,
        p.product_sku,
        p.product_name,
        p.slug,
        p.product_status,
        p.product_availability,
        p.rating,
        pi2.file_name,
        min_prices.base_price,
        min_prices.disc,
        min_prices.price
    FROM
        product_color_options pco
    JOIN
        products p ON p.id = pco.product_id
    JOIN
        product_tags pt ON pt.product_id = p.id
    LEFT JOIN
        product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
    JOIN
        (
            SELECT
                product_id,
                MIN(price) AS price,
                disc,
                base_price
            FROM
                product_options
            GROUP BY
                product_id
        ) AS min_prices ON p.id = min_prices.product_id
    WHERE
        pt.tag_id = 1
        AND p.product_availability = 'y'
        AND p.brand_id = ? and p.category_id = ?
    GROUP BY
        item_id
    ) as product_view limit 10
        ";

    $data_obj = DB::select($sql, [session('active-brand'), $category_id]);

    // $data_obj = DB::select("
    //     select p.*, pi2.file_name
    //     from products p
    //     join product_tags pt on pt.product_id = p.id
    //     left join product_images pi2 on pi2.product_id = p.id
    //     where pt.tag_id = 1 and p.product_availability = 'y'
    //     and p.brand_id = ? and p.category_id = ?
    //     limit 10
    // ", [session('active-brand'), $category_id]);

    // foreach ($data_obj as $key => $value) {
    //     $data_obj[$key]->base_price = 0;
    //     $data_obj[$key]->disc = 0;
    //     $data_obj[$key]->price = 0;

    //     $data_opt = DB::select("
    //     select base_price, disc, price from product_options po where product_id = ?
    //     and option_availability = 'y' order by base_price asc
    //     limit 1
    //     ", [$value->id]);

    //     if ($data_opt) {
    //         $data_obj[$key]->base_price = $data_opt[0]->base_price;
    //         $data_obj[$key]->disc       = $data_opt[0]->disc;
    //         $data_obj[$key]->price      = $data_opt[0]->price;
    //     }
    // }
    return $data_obj;
}


function getVids()
{
    $data = DB::select("
    select* from video_embeds ve where is_active = 1
    and brand_id  = ?
    ", [session('active-brand')]);
    return $data;
}

function checkArrayValuesNotEmpty($array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            if (empty(array_filter($value))) {
                return false;
            }
        } else {
            if (empty($value)) {
                return false;
            }
        }
    }
    return true;
}

function checkAllKeysNotEmpty($array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            if (checkAllKeysNotEmpty($value) === false) {
                return false;
            }
        } else {
            if (empty($value)) {
                return false;
            }
        }
    }
    return true;
}


function getAppliedVoucher()
{
    $sessions = FacadesSession::all();

    if (isset($sessions['voucher'])) {
        return $sessions['voucher'];
    } else {
        return null;
    }
}


function getCartCount(){
    $count_cart = 0;
    if(auth()->check()){
        $count_cart = Cart::where('user_id', auth()->id())->count();
    } else {
        $cart = json_decode(request()->cookie('cart'), true) ?? [];
        $count_cart = count($cart);
    }
    return $count_cart;
}
