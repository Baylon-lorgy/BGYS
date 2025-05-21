<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function switchToTenantDatabase()
    {
        if (Auth::guard('tenant')->check()) {
            $tenant = Auth::guard('tenant')->user();
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }
    }

    public function index()
    {
        $this->switchToTenantDatabase();
        
        $products = DB::connection('tenant')
            ->table('products')
            ->join('sections', 'products.section_id', '=', 'sections.id')
            ->select('products.*', 'sections.name as section_name')
            ->orderBy('products.created_at', 'desc')
            ->paginate(10);

        return view('tenant.products.index', compact('products'));
    }

    public function create()
    {
        $this->switchToTenantDatabase();
        
        // Check if user can add more products based on their plan
        $tenant = Auth::guard('tenant')->user();
        $productCount = DB::connection('tenant')->table('products')->count();
        $maxProducts = $tenant->plan === 'pro' ? PHP_INT_MAX : 3;
        
        if ($productCount >= $maxProducts) {
            return redirect()->route('tenant.products.index')
                ->with('error', 'You have reached the maximum number of products for your plan. Please upgrade to add more products.');
        }

        $sections = DB::connection('tenant')
            ->table('sections')
            ->where('status', 'active')
            ->get();

        return view('tenant.products.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $this->switchToTenantDatabase();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'section_id' => 'required|exists:tenant.sections,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // 2MB max
            'status' => 'required|in:active,inactive'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        DB::connection('tenant')->table('products')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'section_id' => $request->section_id,
            'stock' => $request->stock,
            'image' => $imagePath,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('tenant.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $this->switchToTenantDatabase();
        
        $product = DB::connection('tenant')
            ->table('products')
            ->where('id', $id)
            ->first();

        if (!$product) {
            return redirect()->route('tenant.products.index')
                ->with('error', 'Product not found.');
        }

        $sections = DB::connection('tenant')
            ->table('sections')
            ->where('status', 'active')
            ->get();

        return view('tenant.products.edit', compact('product', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $this->switchToTenantDatabase();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'section_id' => 'required|exists:tenant.sections,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // 2MB max
            'status' => 'required|in:active,inactive'
        ]);

        $product = DB::connection('tenant')
            ->table('products')
            ->where('id', $id)
            ->first();

        if (!$product) {
            return redirect()->route('tenant.products.index')
                ->with('error', 'Product not found.');
        }

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        DB::connection('tenant')->table('products')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'section_id' => $request->section_id,
                'stock' => $request->stock,
                'image' => $imagePath,
                'status' => $request->status,
                'updated_at' => now()
            ]);

        return redirect()->route('tenant.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $this->switchToTenantDatabase();
        
        $product = DB::connection('tenant')
            ->table('products')
            ->where('id', $id)
            ->first();

        if (!$product) {
            return redirect()->route('tenant.products.index')
                ->with('error', 'Product not found.');
        }

        // Delete product image if it exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        DB::connection('tenant')
            ->table('products')
            ->where('id', $id)
            ->delete();

        return redirect()->route('tenant.products.index')
            ->with('success', 'Product deleted successfully.');
    }
} 