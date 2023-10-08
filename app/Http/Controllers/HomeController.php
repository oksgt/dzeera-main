<?php

namespace App\Http\Controllers;
// use App\Helper\Helpers;

use App\Models\BannerImage;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        session(['active-brand-name' => strtolower($brand->brand_name)]);

        //get banner
        $banner = BannerImage::All();

        //get new arrival
        $newArrivals = getNewArrivals();

        // dd($newArrivals);

        return view('home', compact('brand_id', 'banner'));
    }

    public function search(Request $request)
    {
        $input_search = $request->get('input_search', '');

        if(!$input_search){
            return redirect()->back();
        }

        // Check if the 'page' query parameter is missing
        if (!$request->has('page')) {
            $currentUrl = url()->current();
            // Build the modified URL with the 'page' query parameter
            // $modifiedUrl = route('newArrivals', ['brandslug' => strtolower($brand->slug), 'page' => 1]);
            $modifiedUrl = url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => 1]));

            // Redirect to the modified URL
            return redirect($modifiedUrl);
        }

        $perPage = 10;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        $filtered_ = [
            'use_filter'    => "",
            'category'      => "",
            'from'          => "",
            'to'            => "",
            'search'        => "",
            'sort'          => "",
            'page'          => $page,
        ];

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
        ) as product_view
        ";

        $sql_2 = "
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
        ) as product_view
        ";

        $sql .= " where product_view.product_availability = 'y' and product_view.product_name like '%".$input_search."%'";
        $sql_2 .= " where product_view.product_availability = 'y' and product_view.product_name like '%".$input_search."%'";

        if (!empty($request->get('use_filter'))) {
            $filtered_['use_filter'] = 1;

        }

        if (!empty($request->get('search'))) {
            $query_ = $request->input('search', '');
            $sql    .= " and product_view.product_name LIKE '%$query_%'";
            $sql_2  .= " and product_view.product_name LIKE '%$query_%'";
            $filtered_['search'] = 'Keyword: ' . $query_;
        }

        if (!empty($request->get('input_category'))) {
            $query_ = $request->input('input_category');
            $sql    .= " and product_view.category_id = " . $query_;
            $sql_2  .= " and product_view.category_id = " . $query_;

            $categories = DB::select("select category_name from categories where id = ?", [$query_]);

            $filtered_['category'] = 'Category: ' . $categories[0]->category_name;
        }

        if (!empty($request->get('fromInput'))) {
            $from   = $request->input('fromInput');
            $to     = $request->input('toInput');

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('fromInput2'))) {
            $from   = $request->input('fromInput2');
            $to     = $request->input('toInput2');

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('sort'))) {
            if ($request->get('sort') == 'newest') {
                // $newArrivals = $this->sortByUpdated($newArrivals);
                $sql    .= " order by updated_at asc";
                $filtered_['sort'] = 'newest';
            } else if ($request->get('sort') == 'oldest') {
                $sql    .= " order by updated_at desc";
                // $newArrivals = $this->sortByUpdated($newArrivals, 'desc');
                $filtered_['sort'] = 'oldest';
            } else if ($request->get('sort') == 'priceHigh') {
                $sql    .= " order by product_view.price desc";
                // $newArrivals = $this->sortByPrice($newArrivals, 'desc');
                $filtered_['sort'] = 'priceHigh';
            } else if ($request->get('sort') == 'priceLow') {
                $sql    .= " order by product_view.price asc";
                // $newArrivals = $this->sortByPrice($newArrivals);
                $filtered_['sort'] = 'priceLow';
            } else if ($request->get('sort') == 'nameAsc') {
                // $newArrivals = $this->sortByName($newArrivals);
                $sql    .= " order by product_view.product_name asc";
                $filtered_['sort'] = 'nameAsc';
            } else if ($request->get('sort') == 'nameDesc') {
                // $newArrivals = $this->sortByName($newArrivals, 'desc');
                $sql    .= " order by product_view.product_name desc";
                $filtered_['sort'] = 'nameDesc';
            }
        } else {
            // $newArrivals = $this->sortByName($newArrivals);
            $sql    .= " order by product_view.product_name asc";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $data_obj = DB::select($sql, [session('active-brand')]);

        $data_obj_2 = DB::select($sql_2, [session('active-brand')]);

        foreach ($data_obj as $key => $value) {
            $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
            $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
            $data_obj[$key]->price      = (int) $data_obj[$key]->price;
        }

        $newArrivals = $data_obj;

        $data = $newArrivals;

        $totalItems = count($data_obj_2);

        $totalPages = ceil($totalItems / $perPage);

        // $view = View::make('search_result', compact('data', 'filtered_', 'page', 'totalPages'))->render();
        // return response()->json(['view' => $view]);

        return view('search_result', compact('data', 'filtered_', 'page', 'totalPages'));
    }

    public function newArrivals(Request $request, $brandslug = null)
    {
        if ($brandslug) {
            $brand = Brand::where('slug', $brandslug)->first();
        } else {
            $brand = Brand::where('main_brand', 1)->first();
        }
        $brand_id = $brand->id;
        session(['active-brand' => $brand->id]);
        session(['active-brand-name' => strtolower($brand->brand_name)]);

        // Check if the 'page' query parameter is missing
        if (!$request->has('page')) {
            $currentUrl = url()->current();
            // Build the modified URL with the 'page' query parameter
            // $modifiedUrl = route('newArrivals', ['brandslug' => strtolower($brand->slug), 'page' => 1]);
            $modifiedUrl = url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => 1]));

            // Redirect to the modified URL
            return redirect($modifiedUrl);
        }

        $perPage = 10;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        // $total = DB::select("select count(1) as total from products p
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ?", [session('active-brand')]);
        // $totalItems = $total[0]->total;

        $filtered_ = [
            'use_filter'    => "",
            'category'      => "",
            'from'          => "",
            'to'            => "",
            'search'        => "",
            'sort'          => "",
            'page'          => $page,
        ];

        // $sql = "
        // select DISTINCT  p.*, pi2.file_name, po.id as product_opt_id, po.base_price, po.disc, po.price from products p
        // JOIN (
        //     SELECT product_id, MIN(price) AS min_price
        //     FROM product_options
        //     GROUP BY product_id
        // ) AS min_prices ON p.id = product_view.product_id
        // JOIN product_options po
        // ON p.id = po.product_id AND po.price = min_prices.min_price
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ?";

        // $sql_2 = "
        // select DISTINCT  p.*, pi2.file_name,    po.base_price, po.disc, po.price from products p
        // JOIN (
        //     SELECT product_id, MIN(price) AS min_price
        //     FROM product_options
        //     GROUP BY product_id
        // ) AS min_prices ON p.id = min_prices.product_id
        // JOIN product_options po
        // ON p.id = po.product_id AND po.price = min_prices.min_price
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ? ";

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
        ) as product_view
        ";

        $sql_2 = "
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
        ) as product_view
        ";


        if (!empty($request->get('use_filter'))) {
            $filtered_['use_filter'] = 1;
            $sql .= " where product_view.product_availability = 'y' ";
            $sql_2 .= " where product_view.product_availability = 'y' ";
        }


        if (!empty($request->get('search'))) {
            $query_ = $request->input('search', '');
            $sql    .= " and product_view.product_name LIKE '%$query_%'";
            $sql_2  .= " and product_view.product_name LIKE '%$query_%'";
            $filtered_['search'] = 'Keyword: ' . $query_;
        }

        if (!empty($request->get('input_category'))) {
            $query_ = $request->input('input_category');
            $sql    .= " and product_view.category_id = " . $query_;
            $sql_2  .= " and product_view.category_id = " . $query_;

            $categories = DB::select("select category_name from categories where id = ?", [$query_]);

            $filtered_['category'] = 'Category: ' . $categories[0]->category_name;
        }

        if (!empty($request->get('fromInput'))) {
            $from   = $request->input('fromInput');
            $to     = $request->input('toInput');

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";
            // $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
            //     return ($obj->price >= $from && $obj->price <= $to);
            // });

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('fromInput2'))) {
            $from   = $request->input('fromInput2');
            $to     = $request->input('toInput2');

            // $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
            //     return ($obj->price >= $from && $obj->price <= $to);
            // });

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('sort'))) {
            if ($request->get('sort') == 'newest') {
                // $newArrivals = $this->sortByUpdated($newArrivals);
                $sql    .= " order by updated_at asc";
                $filtered_['sort'] = 'newest';
            } else if ($request->get('sort') == 'oldest') {
                $sql    .= " order by updated_at desc";
                // $newArrivals = $this->sortByUpdated($newArrivals, 'desc');
                $filtered_['sort'] = 'oldest';
            } else if ($request->get('sort') == 'priceHigh') {
                $sql    .= " order by product_view.price desc";
                // $newArrivals = $this->sortByPrice($newArrivals, 'desc');
                $filtered_['sort'] = 'priceHigh';
            } else if ($request->get('sort') == 'priceLow') {
                $sql    .= " order by product_view.price asc";
                // $newArrivals = $this->sortByPrice($newArrivals);
                $filtered_['sort'] = 'priceLow';
            } else if ($request->get('sort') == 'nameAsc') {
                // $newArrivals = $this->sortByName($newArrivals);
                $sql    .= " order by product_view.product_name asc";
                $filtered_['sort'] = 'nameAsc';
            } else if ($request->get('sort') == 'nameDesc') {
                // $newArrivals = $this->sortByName($newArrivals, 'desc');
                $sql    .= " order by product_view.product_name desc";
                $filtered_['sort'] = 'nameDesc';
            }
        } else {
            // $newArrivals = $this->sortByName($newArrivals);
            $sql    .= " order by product_view.product_name asc";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $data_obj = DB::select($sql, [session('active-brand')]);

        $data_obj_2 = DB::select($sql_2, [session('active-brand')]);

        foreach ($data_obj as $key => $value) {
            $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
            $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
            $data_obj[$key]->price      = (int) $data_obj[$key]->price;
        }

        $newArrivals = $data_obj;

        $data = $newArrivals;

        $totalItems = count($data_obj_2);

        // dd($data);

        $totalPages = ceil($totalItems / $perPage);
        return view('new_arrivals', compact('brand_id', 'data', 'filtered_', 'page', 'totalPages'));
    }

    public function allProducts(Request $request, $brandslug = null)
    {
        if ($brandslug) {
            $brand = Brand::where('slug', $brandslug)->first();
        } else {
            $brand = Brand::where('main_brand', 1)->first();
        }
        $brand_id = $brand->id;
        session(['active-brand' => $brand->id]);
        session(['active-brand-name' => strtolower($brand->brand_name)]);

        // Check if the 'page' query parameter is missing
        if (!$request->has('page')) {
            $currentUrl = url()->current();
            // Build the modified URL with the 'page' query parameter
            // $modifiedUrl = route('newArrivals', ['brandslug' => strtolower($brand->slug), 'page' => 1]);
            $modifiedUrl = url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => 1]));

            // Redirect to the modified URL
            return redirect($modifiedUrl);
        }

        $perPage = 10;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        // $total = DB::select("select count(1) as total from products p
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ?", [session('active-brand')]);
        // $totalItems = $total[0]->total;

        $filtered_ = [
            'use_filter'    => "",
            'category'      => "",
            'from'          => "",
            'to'            => "",
            'search'        => "",
            'sort'          => "",
            'page'          => $page,
        ];

        // $sql = "
        // select DISTINCT  p.*, pi2.file_name, po.id as product_opt_id, po.base_price, po.disc, po.price from products p
        // JOIN (
        //     SELECT product_id, MIN(price) AS min_price
        //     FROM product_options
        //     GROUP BY product_id
        // ) AS min_prices ON p.id = product_view.product_id
        // JOIN product_options po
        // ON p.id = po.product_id AND po.price = min_prices.min_price
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ?";

        // $sql_2 = "
        // select DISTINCT  p.*, pi2.file_name,    po.base_price, po.disc, po.price from products p
        // JOIN (
        //     SELECT product_id, MIN(price) AS min_price
        //     FROM product_options
        //     GROUP BY product_id
        // ) AS min_prices ON p.id = min_prices.product_id
        // JOIN product_options po
        // ON p.id = po.product_id AND po.price = min_prices.min_price
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ? ";

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
        WHERE p.product_availability = 'y'
            AND p.brand_id = ?
        GROUP BY
            item_id
        ) as product_view
        ";

        $sql_2 = "
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
        WHERE p.product_availability = 'y'
            AND p.brand_id = ?
        GROUP BY
            item_id
        ) as product_view
        ";


        if (!empty($request->get('use_filter'))) {
            $filtered_['use_filter'] = 1;
            $sql .= " where product_view.product_availability = 'y' ";
            $sql_2 .= " where product_view.product_availability = 'y' ";
        }


        if (!empty($request->get('search'))) {
            $query_ = $request->input('search', '');
            $sql    .= " and product_view.product_name LIKE '%$query_%'";
            $sql_2  .= " and product_view.product_name LIKE '%$query_%'";
            $filtered_['search'] = 'Keyword: ' . $query_;
        }

        if (!empty($request->get('input_category'))) {
            $query_ = $request->input('input_category');
            $sql    .= " and product_view.category_id = " . $query_;
            $sql_2  .= " and product_view.category_id = " . $query_;

            $categories = DB::select("select category_name from categories where id = ?", [$query_]);

            $filtered_['category'] = 'Category: ' . $categories[0]->category_name;
        }

        if (!empty($request->get('fromInput'))) {
            $from   = $request->input('fromInput');
            $to     = $request->input('toInput');

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";
            // $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
            //     return ($obj->price >= $from && $obj->price <= $to);
            // });

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('fromInput2'))) {
            $from   = $request->input('fromInput2');
            $to     = $request->input('toInput2');

            // $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
            //     return ($obj->price >= $from && $obj->price <= $to);
            // });

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('sort'))) {
            if ($request->get('sort') == 'newest') {
                // $newArrivals = $this->sortByUpdated($newArrivals);
                $sql    .= " order by updated_at asc";
                $filtered_['sort'] = 'newest';
            } else if ($request->get('sort') == 'oldest') {
                $sql    .= " order by updated_at desc";
                // $newArrivals = $this->sortByUpdated($newArrivals, 'desc');
                $filtered_['sort'] = 'oldest';
            } else if ($request->get('sort') == 'priceHigh') {
                $sql    .= " order by product_view.price desc";
                // $newArrivals = $this->sortByPrice($newArrivals, 'desc');
                $filtered_['sort'] = 'priceHigh';
            } else if ($request->get('sort') == 'priceLow') {
                $sql    .= " order by product_view.price asc";
                // $newArrivals = $this->sortByPrice($newArrivals);
                $filtered_['sort'] = 'priceLow';
            } else if ($request->get('sort') == 'nameAsc') {
                // $newArrivals = $this->sortByName($newArrivals);
                $sql    .= " order by product_view.product_name asc";
                $filtered_['sort'] = 'nameAsc';
            } else if ($request->get('sort') == 'nameDesc') {
                // $newArrivals = $this->sortByName($newArrivals, 'desc');
                $sql    .= " order by product_view.product_name desc";
                $filtered_['sort'] = 'nameDesc';
            }
        } else {
            // $newArrivals = $this->sortByName($newArrivals);
            $sql    .= " order by product_view.product_name asc";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $data_obj = DB::select($sql, [session('active-brand')]);

        $data_obj_2 = DB::select($sql_2, [session('active-brand')]);

        foreach ($data_obj as $key => $value) {
            $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
            $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
            $data_obj[$key]->price      = (int) $data_obj[$key]->price;
        }

        $newArrivals = $data_obj;

        $data = $newArrivals;

        $totalItems = count($data_obj_2);

        $totalPages = ceil($totalItems / $perPage);
        $pageTitle = 'All Products';
        return view('productList', compact('brand_id', 'data', 'filtered_', 'page', 'totalPages', 'pageTitle'));
    }

    public function ProductByCategory(Request $request, $brandslug = null, $categoryslug = null)
    {
        if ($brandslug) {
            $brand = Brand::where('slug', $brandslug)->first();
        } else {
            $brand = Brand::where('main_brand', 1)->first();
        }
        $brand_id = $brand->id;
        session(['active-brand' => $brand->id]);
        session(['active-brand-name' => strtolower($brand->brand_name)]);

        // Check if the 'page' query parameter is missing
        if (!$request->has('page')) {
            $currentUrl = url()->current();
            // Build the modified URL with the 'page' query parameter
            // $modifiedUrl = route('newArrivals', ['brandslug' => strtolower($brand->slug), 'page' => 1]);
            $modifiedUrl = url()->current() . '?' . http_build_query(array_merge(request()->query(), ['page' => 1]));

            // Redirect to the modified URL
            return redirect($modifiedUrl);
        }

        $perPage = 10;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        // $total = DB::select("select count(1) as total from products p
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ?", [session('active-brand')]);
        // $totalItems = $total[0]->total;

        $filtered_ = [
            'use_filter'    => "",
            'category'      => "",
            'from'          => "",
            'to'            => "",
            'search'        => "",
            'sort'          => "",
            'page'          => $page,
        ];

        // $sql = "
        // select DISTINCT  p.*, pi2.file_name, po.id as product_opt_id, po.base_price, po.disc, po.price from products p
        // JOIN (
        //     SELECT product_id, MIN(price) AS min_price
        //     FROM product_options
        //     GROUP BY product_id
        // ) AS min_prices ON p.id = product_view.product_id
        // JOIN product_options po
        // ON p.id = po.product_id AND po.price = min_prices.min_price
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ?";

        // $sql_2 = "
        // select DISTINCT  p.*, pi2.file_name,    po.base_price, po.disc, po.price from products p
        // JOIN (
        //     SELECT product_id, MIN(price) AS min_price
        //     FROM product_options
        //     GROUP BY product_id
        // ) AS min_prices ON p.id = min_prices.product_id
        // JOIN product_options po
        // ON p.id = po.product_id AND po.price = min_prices.min_price
        // join product_tags pt on pt.product_id = p.id
        // left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        // where pt.tag_id = 1 and p.product_availability = 'y'
        // and p.brand_id = ? ";

        $sql = "
        select * from
        (
        SELECT
            CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
            CONCAT(p.id, '-', pco.id) AS item_id,
            c.category_name, c.slug as category_slug,
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
        join categories c on p.category_id  = c.id and p.brand_id = c.brand_id
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
        WHERE p.product_availability = 'y'
        and c.slug = ?
            AND p.brand_id = ?
        GROUP BY
            item_id
        ) as product_view
        ";

        $sql_2 = "
        select * from
        (
        SELECT
            CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
            CONCAT(p.id, '-', pco.id) AS item_id,
            c.category_name, c.slug as category_slug,
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
            join categories c on p.category_id  = c.id and p.brand_id = c.brand_id
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
        WHERE p.product_availability = 'y'
            and c.slug = ?
            AND p.brand_id = ?
        GROUP BY
            item_id
        ) as product_view
        ";


        if (!empty($request->get('use_filter'))) {
            $filtered_['use_filter'] = 1;
            $sql .= " where product_view.product_availability = 'y' ";
            $sql_2 .= " where product_view.product_availability = 'y' ";
        }


        if (!empty($request->get('search'))) {
            $query_ = $request->input('search', '');
            $sql    .= " and product_view.product_name LIKE '%$query_%'";
            $sql_2  .= " and product_view.product_name LIKE '%$query_%'";
            $filtered_['search'] = 'Keyword: ' . $query_;
        }

        if (!empty($request->get('input_category'))) {
            $query_ = $request->input('input_category');
            $sql    .= " and product_view.category_id = " . $query_;
            $sql_2  .= " and product_view.category_id = " . $query_;

            $categories = DB::select("select category_name from categories where id = ?", [$query_]);

            $filtered_['category'] = 'Category: ' . $categories[0]->category_name;
        }

        if (!empty($request->get('fromInput'))) {
            $from   = $request->input('fromInput');
            $to     = $request->input('toInput');

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";
            // $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
            //     return ($obj->price >= $from && $obj->price <= $to);
            // });

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('fromInput2'))) {
            $from   = $request->input('fromInput2');
            $to     = $request->input('toInput2');

            // $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
            //     return ($obj->price >= $from && $obj->price <= $to);
            // });

            $sql    .= " and product_view.price BETWEEN $from AND $to ";
            $sql_2  .= " and product_view.price BETWEEN $from AND $to ";

            $filtered_['from'] = __('general.minPrice') . ': ' . formatnumber($to);
            $filtered_['to'] = __('general.maxPrice') . ': ' . formatnumber($from);
        }

        if (!empty($request->get('sort'))) {
            if ($request->get('sort') == 'newest') {
                // $newArrivals = $this->sortByUpdated($newArrivals);
                $sql    .= " order by updated_at asc";
                $filtered_['sort'] = 'newest';
            } else if ($request->get('sort') == 'oldest') {
                $sql    .= " order by updated_at desc";
                // $newArrivals = $this->sortByUpdated($newArrivals, 'desc');
                $filtered_['sort'] = 'oldest';
            } else if ($request->get('sort') == 'priceHigh') {
                $sql    .= " order by product_view.price desc";
                // $newArrivals = $this->sortByPrice($newArrivals, 'desc');
                $filtered_['sort'] = 'priceHigh';
            } else if ($request->get('sort') == 'priceLow') {
                $sql    .= " order by product_view.price asc";
                // $newArrivals = $this->sortByPrice($newArrivals);
                $filtered_['sort'] = 'priceLow';
            } else if ($request->get('sort') == 'nameAsc') {
                // $newArrivals = $this->sortByName($newArrivals);
                $sql    .= " order by product_view.product_name asc";
                $filtered_['sort'] = 'nameAsc';
            } else if ($request->get('sort') == 'nameDesc') {
                // $newArrivals = $this->sortByName($newArrivals, 'desc');
                $sql    .= " order by product_view.product_name desc";
                $filtered_['sort'] = 'nameDesc';
            }
        } else {
            // $newArrivals = $this->sortByName($newArrivals);
            $sql    .= " order by product_view.product_name asc";
        }

        $sql .= " LIMIT $perPage OFFSET $offset";

        $data_obj = DB::select($sql, [$categoryslug, session('active-brand')]);

        $data_obj_2 = DB::select($sql_2, [$categoryslug, session('active-brand')]);

        foreach ($data_obj as $key => $value) {
            $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
            $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
            $data_obj[$key]->price      = (int) $data_obj[$key]->price;
        }

        $newArrivals = $data_obj;

        $data = $newArrivals;

        $totalItems = count($data_obj_2);

        $totalPages = ceil($totalItems / $perPage);

        $category = DB::select("select category_name from categories where slug = ?", [$categoryslug]);

        $pageTitle = strtoupper($category[0]->category_name);
        return view('productList', compact('brand_id', 'data', 'filtered_', 'page', 'totalPages', 'pageTitle'));
    }

    public function product(Request $request){
        $slug = $request->productslug;

        // Separate the string using the delimiter "__"
        $parts = explode("__", $slug);
        $product_slug = $parts[0];
        $color_slug = str_replace("_", " ", $parts[1]);

        $sql = "
        select * from
        (
        SELECT
            CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
            CONCAT(p.id, '-', pco.id) AS item_id,
            p.product_desc,
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
        p.slug = ? and
        pco.color_name = ?
            AND p.product_availability = 'y'
        GROUP BY
            item_id
        ) as product_view
        ";

        $product_detail_obj = DB::select($sql, [$product_slug, $color_slug]);

        $product_detail = $product_detail_obj[0];

        $product_id = $product_detail_obj[0]->product_id;
        $color_id   = $product_detail_obj[0]->color_id;

        $options = DB::select("
            select po.id as option_id, po.product_id, po.color, po.size_opt_id, pso.`size`, po.stock,
            po.base_price, po.disc, po.price, po.option_availability
            from product_options po
            join product_size_options pso on pso.id = po.size_opt_id
            where po.color = ?
            and po.option_availability = 'y'
            and po.product_id = ?
        ", [$color_id, $product_id]);

        $images = DB::select("
        select * from product_images pi2 where product_id = ? order by is_thumbnail desc
        ", [$product_id]);

        // dd($images);

        return view('product_detail', compact('product_detail', 'options', 'images'));

    }

    private function sortByName(array $data, string $order = 'asc'): array
    {
        usort($data, function ($a, $b) use ($order) {
            if ($order === 'asc') {
                return strcmp($a->product_name, $b->product_name);
            } else {
                return strcmp($b->product_name, $a->product_name);
            }
        });

        return $data;
    }

    private function sortByPrice(array $data, string $order = 'asc'): array
    {
        usort($data, function ($a, $b) use ($order) {
            if ($order === 'asc') {
                return strcmp($a->price, $b->price);
            } else {
                return strcmp($b->price, $a->price);
            }
        });

        return $data;
    }

    private function sortByUpdated(array $data, string $order = 'asc'): array
    {
        usort($data, function ($a, $b) use ($order) {
            if ($order === 'asc') {
                return strcmp($a->updated_at, $b->updated_at);
            } else {
                return strcmp($b->updated_at, $a->updated_at);
            }
        });

        return $data;
    }

    public function myOrder(){
        if(!auth()->check()){
            return redirect()->route('home');
        }
        $transactions = Transaction::where('user_id', Auth::user()->id)->get();
            // dd( $transactions);
        return view('my_order', compact('transactions'));
    }


    public function detailOrder(Request $req)
    {
        if(!auth()->check()){
            return redirect()->route('home');
        }

        $code = $req->code;
        if (empty($code)) {
            return redirect()->route('home');
        }

        $transaction = Transaction::where('trans_number', $code)
            ->where('user_id',Auth::user()->id)
            ->first();

        // dd($transaction)''


        if (!$transaction) {
            return redirect()->route('home');
        }

        $trans_detail = DB::select("
        select
            po.id as cart_id,
            po.id as opt_id,
            p.id as product_id, p.product_name, pco.id as color_opt_id, pco.color_name , pso.id as size_opt_id, pso.`size`, po.price,
            po.qty,
            po.qty * po.price as total_price,
            pi2.file_name
            from transaction_details po
            join products p on p.id = po.product_id
            join product_color_options pco on pco.id = po.color_opt_id
            join product_size_options pso on pso.id = po.size_opt_id
            LEFT JOIN
                product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
                where po.trans_number = ?
            ", [$code]);

        if($transaction->voucher_id !== "-"){
            $voucher = Voucher::where('code', $transaction->voucher_id)->first();
        } else {
            $voucher = [];
        }

        return view('my-order-details', ['transaction' => $transaction, 'trans_detail' => $trans_detail, 'voucher' => $voucher]);
    }
}
