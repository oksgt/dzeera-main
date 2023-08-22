<?php

namespace App\Http\Controllers;
// use App\Helper\Helpers;

use App\Models\BannerImage;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        session(['active-brand-name' => strtolower($brand->brand_name)]);

        //get banner
        $banner = BannerImage::All();

        //get new arrival
        $newArrivals = getNewArrivals();

        // dd($newArrivals);

        return view('home', compact('brand_id', 'banner'));
    }

    public function newArrivals(Request $request, $brandslug = null){
        $perPage = 10;

        $filtered_ = [
            'use_filter'    => "",
            'category'      => "",
            'from'          => "",
            'to'            => "",
            'search'        => "",
            'sort'          => ""
        ];

        $query = 'SELECT * FROM your_table';
        if ($brandslug) {
            $brand = Brand::where('slug', $brandslug)->first();
        } else {
            $brand = Brand::where('main_brand', 1)->first();
        }
        $brand_id = $brand->id;
        session(['active-brand' => $brand->id]);
        session(['active-brand-name' => strtolower($brand->brand_name)]);

        $sql = "select p.*, pi2.file_name from products p
        join product_tags pt on pt.product_id = p.id
        left join product_images pi2 on pi2.product_id = p.id and pi2.is_thumbnail = 1
        where pt.tag_id = 1 and p.product_availability = 'y'
        and p.brand_id = ? ";

        if(!empty($request->get('use_filter'))){
            $filtered_['use_filter'] = 1;
        }

        if(!empty($request->get('search'))){
            $query_ = $request->input('search', '');
            $sql    .= " and p.product_name LIKE '%$query_%'";
            $filtered_['search'] = 'Keyword: '.$query_;
        }

        if(!empty($request->get('input_category'))){
            $query_ = $request->input('input_category');
            $sql    .= " and p.category_id = ".$query_;

            $categories = DB::select("select category_name from categories where id = ?", [$query_]);

            $filtered_['category'] = 'Category: '.$categories[0]->category_name;
        }

        $data_obj = DB::select($sql, [session('active-brand')]);

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
                $data_obj[$key]->base_price = (int) $data_opt[0]->base_price;
                $data_obj[$key]->disc       = (int) $data_opt[0]->disc;
                $data_obj[$key]->price      = (int) $data_opt[0]->price;
            }
        }
        $newArrivals = $data_obj;

        if(!empty($request->get('fromInput'))){
            $from   = $request->input('fromInput');
            $to     = $request->input('toInput');

            $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
                return ($obj->price >= $from && $obj->price <= $to);
            });

            $filtered_['from'] = __('general.minPrice').': '.formatnumber($to);
            $filtered_['to'] = __('general.mixPrice').': '.formatnumber($from);
        }

        if(!empty($request->get('fromInput2'))){
            $from   = $request->input('fromInput2');
            $to     = $request->input('toInput2');

            $newArrivals = array_filter($newArrivals, function ($obj) use ($from, $to) {
                return ($obj->price >= $from && $obj->price <= $to);
            });

            $filtered_['from'] = __('general.minPrice').': '.formatnumber($to);
            $filtered_['to'] = __('general.maxPrice').': '.formatnumber($from);
        }

        if(!empty($request->get('sort'))){
            if($request->get('sort') == 'newest'){
                $newArrivals = $this->sortByUpdated($newArrivals);
                $filtered_['sort'] = 'newest';
            } else if($request->get('sort') == 'oldest'){
                $newArrivals = $this->sortByUpdated($newArrivals, 'desc');
                $filtered_['sort'] = 'oldest';
            } else if($request->get('sort') == 'priceHigh'){
                $newArrivals = $this->sortByPrice($newArrivals, 'desc');
                $filtered_['sort'] = 'priceHigh';
            } else if($request->get('sort') == 'priceLow'){
                $newArrivals = $this->sortByPrice($newArrivals);
                $filtered_['sort'] = 'priceLow';
            } else if($request->get('sort') == 'nameAsc'){
                $newArrivals = $this->sortByName($newArrivals);
                $filtered_['sort'] = 'nameAsc';
            } else if($request->get('sort') == 'nameDesc'){
                $newArrivals = $this->sortByName($newArrivals, 'desc');
                $filtered_['sort'] = 'nameDesc';
            }
        } else {
            $newArrivals = $this->sortByName($newArrivals);
        }
        // dd($newArrivals);
        return view('new_arrivals', compact('brand_id', 'newArrivals', 'filtered_'));
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
