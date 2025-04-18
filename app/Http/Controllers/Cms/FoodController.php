<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Food;
use Exception;

class FoodController extends Controller
{
    public function getAllFoods() {
        try {
            $foods = Food::all();

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $foods
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'error',
                'status' => 500,
                'data' => null
            ], 500);
        }
    }

    public function createFood(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|string|in:food,drink',
            'nutrition_facts' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'status' => 422,
                'data' => $validator->errors()
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'nutrition_facts' => $request->nutrition_facts
        ];

        try {
            if ($request->file('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $path = $file->move(public_path('/images/food'), str_replace(' ', '_', $fileName));
                $data['image'] = $path;
            }

            $food = Food::create($data);

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $food
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'error: ' . $e->getMessage(),
                'status' => 500,
                'data' => null
            ], 500);
        }
    }
}
