<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        $total_qty = $products->sum('quantity');
        $total_val = $products->sum('total_value');
        return view('products.index', compact('products','total_qty','total_val'));
    }

    public function store(Request $request)
    {
        $product = $this->insertOrUpdate($request);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = $this->insertOrUpdate($request,$id);

        return response()->json($product);
    }

    private function insertOrUpdate($request,$id=0){

        $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1'
        ]);

        if($id > 0){
            $product = Product::findOrFail($id);
            $product->update([
                'name' => $request->name,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total_value' => $request->quantity * $request->price
            ]);
        }else{
            $product = Product::create([
                'name' => $request->name,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total_value' => $request->quantity * $request->price,
                'created_at' => Carbon::now()
            ]);
        }
        $this->saveToJson();

        return $product;
    }

    private function saveToJson()
    {
        $products = Product::all();

        $jsonData = $products->toJson(JSON_PRETTY_PRINT);

        Storage::disk('public')->put('products.json', $jsonData);
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            $this->saveToJson();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}