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

        $total = DB::select("select count(1) as total from products p
        join product_tags pt on pt.product_id = p.id
        left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        where pt.tag_id = 1 and p.product_availability = 'y'
        and p.brand_id = ?", [session('active-brand')]);
        $totalItems = $total[0]->total;

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
        select DISTINCT  p.*, pi2.file_name, po.base_price, po.disc, po.price from products p
        JOIN (
            SELECT product_id, MIN(price) AS min_price
            FROM product_options
            GROUP BY product_id
        ) AS min_prices ON p.id = min_prices.product_id
        JOIN product_options po
        ON p.id = po.product_id AND po.price = min_prices.min_price
        join product_tags pt on pt.product_id = p.id
        left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        where pt.tag_id = 1 and p.product_availability = 'y'
        and p.brand_id = ?";

        $sql_2 = "
        select DISTINCT  p.*, pi2.file_name,    po.base_price, po.disc, po.price from products p
        JOIN (
            SELECT product_id, MIN(price) AS min_price
            FROM product_options
            GROUP BY product_id
        ) AS min_prices ON p.id = min_prices.product_id
        JOIN product_options po
        ON p.id = po.product_id AND po.price = min_prices.min_price
        join product_tags pt on pt.product_id = p.id
        left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        where pt.tag_id = 1 and p.product_availability = 'y'
        and p.brand_id = ? ";


        if (!empty($request->get('use_filter'))) {
            $filtered_['use_filter'] = 1;
        }

        if (!empty($request->get('search'))) {
            $query_ = $request->input('search', '');
            $sql    .= " and p.product_name LIKE '%$query_%'";
            $sql_2  .= " and p.product_name LIKE '%$query_%'";
            $filtered_['search'] = 'Keyword: ' . $query_;
        }

        if (!empty($request->get('input_category'))) {
            $query_ = $request->input('input_category');
            $sql    .= " and p.category_id = " . $query_;
            $sql_2  .= " and p.category_id = " . $query_;

            $categories = DB::select("select category_name from categories where id = ?", [$query_]);

            $filtered_['category'] = 'Category: ' . $categories[0]->category_name;
        }

        if (!empty($request->get('fromInput'))) {
            $from   = $request->input('fromInput');
            $to     = $request->input('toInput');

            $sql    .= " and po.price BETWEEN $from AND $to ";
            $sql_2  .= " and po.price BETWEEN $from AND $to ";
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

            $sql    .= " and po.price BETWEEN $from AND $to ";
            $sql_2  .= " and po.price BETWEEN $from AND $to ";

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
                $sql    .= " order by po.price desc";
                // $newArrivals = $this->sortByPrice($newArrivals, 'desc');
                $filtered_['sort'] = 'priceHigh';
            } else if ($request->get('sort') == 'priceLow') {
                $sql    .= " order by po.price asc";
                // $newArrivals = $this->sortByPrice($newArrivals);
                $filtered_['sort'] = 'priceLow';
            } else if ($request->get('sort') == 'nameAsc') {
                // $newArrivals = $this->sortByName($newArrivals);
                $sql    .= " order by p.product_name asc";
                $filtered_['sort'] = 'nameAsc';
            } else if ($request->get('sort') == 'nameDesc') {
                // $newArrivals = $this->sortByName($newArrivals, 'desc');
                $sql    .= " order by p.product_name desc";
                $filtered_['sort'] = 'nameDesc';
            }
        } else {
            // $newArrivals = $this->sortByName($newArrivals);
            $sql    .= " order by p.product_name asc";
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
        return view('new_arrivals', compact('brand_id', 'data', 'filtered_', 'page', 'totalPages'));
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
}
