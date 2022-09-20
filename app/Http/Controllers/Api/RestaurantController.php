<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Plate;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RestaurantController extends Controller
{

    // api che restituisce le categorie da visualizzare
    public function categories() {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'categories' => $categories,
        ]);
    }

    public function fixImageUrl($imgPath) {
        return $imgPath ? asset('/storage/' . $imgPath) : null;
    }

    // api che restituisce le i ristoranti da visualizzare (con anche i piatti) nel json
    public function restaurants(Request $request){


        $category = $request->get('category');

        $restaurants= User::all();
        foreach( $restaurants as $restaurant){
            $restaurant->img = $this->fixImageUrl($restaurant->img);
        }


        $arrRestaurants = User::join('category_user', 'users.id', '=', 'category_user.user_id')
                            ->join('categories', 'category_user.category_id', '=', 'categories.id')
                            ->select('users.*','categories.name as categoryName', 'categories.id as categoryId')
        ->where('category_id', $category)->get();
        // ->where('category_id', $category)->with(['plates'])->get();


        return response()->json([
            'success' => true,
            'arrRestaurants' => $arrRestaurants
        ]);
    }


    public function show($id){


        $infoRestaurant = User::where('id', $id)->first();
        $plate = Plate::where('user_id', $id)->get();


        return response()->json([
            'success' => true,
            'infoRestaurant' => $infoRestaurant,
            'plates' => $plate
        ]);

    }
    public function random() {
        $randomRest = User::inRandomOrder()->limit(8)->get();

        return response()->json([
           'success' => true,
            'randomRest' => $randomRest
        ]);
    }



}
