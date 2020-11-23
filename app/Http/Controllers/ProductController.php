<?php

namespace App\Http\Controllers;

use App\Contracts\CategoryRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Traits\UploadFile;
use auth;
use Illuminate\Http\Request;

class ProductController extends Controller {
    use UploadFile;
    public function __construct( Request $request ) {
        $this->request = $request;
    }

    public function index( CategoryRepositoryInterface $categoryRepository ) {
        $data['categories'] = $categoryRepository->get( false );
        $data['categories'] = $data['categories']->pluck( 'name', 'id' )->toArray();

        return view( 'product.index', $data, );
    }

    public function store( ProductRepositoryInterface $productRepository ) {
        try {
            // dd( $this->request->all() );
            $data = $this->validate( $this->request, [
                'title'         => 'required|string|max:255',
                'category_id'   => 'required|integer|min:1|exists:categories,id',
                'description'   => 'nullable|string',
                'sku'           => 'required|string|unique:products,sku',
                'status'        => 'required|numeric|min:1|max:3',
                'feature_image' => ['required', 'image', 'mimes:jpg,png,jpeg,svg', 'max:2048'],
            ] );

            $data['published_by'] = auth()->user()->id;
            // dd( $data['feature_image']->getClientOriginalExtension() );
            $data['image'] = $this->uploadSingleImage( $data['feature_image'], '/products', 'public', true, 200 );
            $productRepository->store( $data );
            return response()->json( [
                'message' => 'Products Added',
            ], 201 );
        } catch ( ValidationException $exception ) {
            // dd( $exception->errors() );
            return response()->json( ['message' => $exception->getMessage()] );
        } catch ( QueryException | \Exception $exception ) {
            dd( $exception->getMessage() );
            return response()->json( ['message' => 'Something went worng'] );
        }
    }

    public function getProductList( ProductRepositoryInterface $productRepository ) {

        try {
            $products = $productRepository->get();
            return response()->json( [
                'message' => 'Product List',
                'data'    => $products,
            ], 200 );
        } catch ( QueryException | \Exception $exception ) {
            return response()->json( ['message' => $exception->getMessage()] );
        }

    }
}
