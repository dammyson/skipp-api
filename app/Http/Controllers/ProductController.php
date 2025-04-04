<?php
       
namespace App\Http\Controllers;
       
use League\Csv\Reader;
use App\Models\Product;
use League\Csv\Statement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController as BaseController;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        
        return $this->sendResponse([], 'Products retrieved successfully.');
    }

    public function getProductsByStoreOrCategory(Request $request)
    {
        // Get the 'store_id' and 'category' from the request query parameters
        $storeId = $request->query('store_id');
        $category = $request->query('category');

        // Start the query for products
        $query = Product::query()->with('store');

        // Filter by store_id if provided
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        // Filter by category if provided
        if ($category) {
            $query->where('category', $category);
        }

        // Execute the query and get the results with the associated store information
        $products = $query->get();

        return $this->sendResponse($products, 'Products retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|uuid|exists:stores,id',
            'code' => 'required|string|max:255',
            'barcode_number' => 'required|string|max:255',
            'barcode_formats' => 'nullable|string',
            'mpn' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'asin' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'dimension' => 'nullable|string|max:255',
            'warranty_length' => 'nullable|string|max:255',
            'brand' => 'required|string|max:255',
            'ingredients' => 'nullable|string',
            'nutrition_facts' => 'nullable|string',
            'size' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

      
        $product = Product::create($validated);

        return $this->sendResponse( $product, 'Product created successfully.', 201);
    }

        /**
     * Create products by uploading a CSV file.
     */
    public function createByCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())->process($csv);

        $errors = [];
        $products = [];

        foreach ($stmt as $record) {
            $validator = Validator::make($record, [
                'store_id' => 'required|uuid|exists:stores,id',
                'code' => 'required|string|max:255',
                'barcode_number' => 'required|string|max:255',
                'barcode_formats' => 'nullable|string',
                'mpn' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'asin' => 'nullable|string|max:255',
                'title' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'manufacturer' => 'required|string|max:255',
                'serial_number' => 'nullable|string|max:255',
                'weight' => 'nullable|string|max:255',
                'dimension' => 'nullable|string|max:255',
                'warranty_length' => 'nullable|string|max:255',
                'brand' => 'required|string|max:255',
                'ingredients' => 'nullable|string',
                'nutrition_facts' => 'nullable|string',
                'size' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'record' => $record,
                    'errors' => $validator->errors()
                ];
                continue;
            }

            $products[] = Product::create($validator->validated());
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }
        return $this->sendResponse( $products, 'Products created successfully.', 201);
    }

    public function scan($code){

        try {

            $product = Product::where('barcode_number', $code)->first();
  
            return $this->sendResponse($product, 'Products retrieved successfully.');
          
        
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
            return response()->json([
                'status' => "failed",
                'message' =>  $message
            ],500);
        }
        
    }


    public function uploadImages(Request $request)
    {

        // dd("i ran");
        $validateRequest = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $file = $request->file('image');
        $path = Storage::disk('cloudinary')->putFile('uploads', $file);
        // dump($path);
        $url = Storage::disk('cloudinary')->url($path);

        // dump($url);

        // $cloudinaryImage = $request->file('image')->storeOnCloudinary('products');
        //  dump($cloudinaryImage);
        // $url = $cloudinaryImage->getSecurePath();
        // $public_id = $cloudinaryImage->getPublicId();

        return response()->json([
            "url"=> $url,
            // "public_id" => $public_id
        ]);

    }

    public function getProductById($productId) {
        $product = Product::find($productId)->load('category');

        if (!$product) {
            return response()->json([
                "error" => true,
                "message" => "Product does not exist"
            ]);
        }


        return response()->json([
            "error" => false,
            "product" => $product
        ]);
    }

    public function getProductByCode($code) {
        $product = Product::where('code', $code)->with('category')->first();

        if (!$product) {
            return response()->json([
                "error" => true,
                "message" => "Product does not exist"
            ]);
        }

        return response()->json([
            "error" => false,
            "product" => $product
        ]);
    }
}