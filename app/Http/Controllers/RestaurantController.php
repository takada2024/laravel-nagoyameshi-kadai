<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     *  index
     */
    public function index(Request $request, Restaurant $restaurant)
    {
        $categories = Category::all();

        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $price = $request->input('price');

        $sort_query = [];
        $sorted = "created_at desc";

        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc'
        ];

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        $query = Restaurant::query();

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('address', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('categories', function ($q) use ($keyword) {
                        $q->where('categories.name', 'LIKE', "%{$keyword}%");
                    });
            });
        }

        if (!empty($category_id)) {
            $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }

        if (!empty($price)) {
            $query->where('lowest_price', '<=', "$price");
        }

        $restaurants = $query->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        $total = $restaurants->total();

        return view('restaurants.index', compact('keyword', 'category_id', 'price', 'sorts', 'sorted', 'restaurants', 'categories', 'total'));
    }

    /**
     *  show
     */
    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', compact('restaurant'));
    }
}
