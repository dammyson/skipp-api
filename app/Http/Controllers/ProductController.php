<?php
       
namespace App\Http\Controllers;
       
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Product;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Facades\Validator;

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
}