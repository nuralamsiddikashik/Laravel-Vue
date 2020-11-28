<?php

namespace App\Http\Controllers;

use App\Contracts\CategoryRepositoryInterface;
use App\Contracts\ProductImageRepositoryInterface;
use App\Contracts\ProductPriceRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Traits\UploadFile;
use auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store( ProductRepositoryInterface $productRepository, ProductPriceRepositoryInterface $productPriceRepository, ProductImageRepositoryInterface $productImageRepository ) {
        try {
            // dd( $this->request->all() );
            $data = $this->validate( $this->request, [
                'title'         => 'required|string|max:255',
                'category_id'   => 'required|integer|min:1|exists:categories,id',
                'description'   => 'nullable|string',
                'sku'           => 'required|string|unique:products,sku',
                'status'        => 'required|numeric|min:1|max:3',
                'feature_image' => ['required', 'image', 'mimes:jpg,png,jpeg,svg', 'max:2048'],
                'cost_price'    => 'r equired|numeric',
                'selling_price' => 'required|numeric',
                'quantity'      => 'required|numeric',
                'gallery'       => ['nullable', 'array', 'max:20', function ( $attribute, $value, $fail ) {
                    if ( !$this->request->file( 'gallery' ) ) {
                        $fail( 'Allow only file type' );
                    }
                }],
            ] );

            $data['published_by'] = auth()->user()->id;
            // dd( $data['feature_image']->getClientOriginalExtension() );
            $data['image']  = $this->uploadSingleImage( $data['feature_image'], '/products', 'public', true, 200 );
            $data['images'] = [];
            foreach ( $data['gallery'] as $gallery ) {
                $data['images'][] = $this->uploadSingleImage( $gallery, '/gallery', 'public', true );
            }
            DB::beginTransaction();
            try {

                $product            = $productRepository->store( $data );
                $data['product_id'] = $product->id;
            } catch ( QueryException $exception ) {
                DB::rollBack();
                app( 'log' )->debug( "product query", [$exception] );
                return response()->json( [
                    'message' => 'Products add failed!!',
                ], 406 );
            }

            try {
                $price = $productPriceRepository->store( $data );
            } catch ( QueryException $exception ) {
                DB::rollBack();
                app( 'log' )->debug( "product query", [$exception] );
            }
            try {
                $gallery = $productImageRepository->store( $data );
            } catch ( QueryException $exception ) {
                DB::rollBack();
                app( 'log' )->debug( "product query", [$exception] );
            }

            DB::commit();

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
