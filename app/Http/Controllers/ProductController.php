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
    public function __construct( Request $request, ProductRepositoryInterface $productRepository, ProductPriceRepositoryInterface $productPriceRepository ) {
        $this->request                = $request;
        $this->productRepository      = $productRepository;
        $this->productPriceRepository = $productPriceRepository;
    }

    public function index() {

        return view( 'product.index' );
    }

    public function create( CategoryRepositoryInterface $categoryRepository ) {
        $data['categories'] = $categoryRepository->get( false );
        $data['categories'] = $data['categories']->pluck( 'name', 'id' )->toArray();

        return view( 'product.create', $data, );
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
                'cost_price'    => 'required|numeric',
                'selling_price' => 'required|numeric|gt:cost_price',
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
                return response()->json( [
                    'message' => 'Products add failed!!',
                ], 406 );
            }
            try {
                $gallery = $productImageRepository->store( $data );
            } catch ( QueryException $exception ) {
                DB::rollBack();
                app( 'log' )->debug( "product query", [$exception] );
                return response()->json( [
                    'message' => 'Products add failed!!',
                ], 406 );
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

    public function getProductList( Request $request, ProductRepositoryInterface $productRepository ) {
        try {
            $products = $productRepository->get( ['id', 'category_id', 'title', 'sku', 'feature_image', 'status'], ['prices', 'images', 'category'], true );
            return response()->json( [
                'message' => 'Product List',
                'data'    => $products,
            ], 200 );
        } catch ( QueryException | \Exception $exception ) {
            return response()->json( ['message' => $exception->getMessage()] );
        }
    }

    public function updateProduct( $id, Request $request, ProductRepositoryInterface $productRepository ) {
        if ( !$productRepository->find( $id ) ) {
            return response()->json( ['message' => 'Product Not found'] );
        }

        try {
            $data = $this->validate( $this->request, [
                'title'       => 'required|string|min:3|max:255',
                'sku'         => 'required',
                'category_id' => '',

            ] );
            $productRepository->update( $id, $data );
            return response()->json( [
                'message' => 'Product updated.',
            ], 201 );
        } catch ( ValidationException $exception ) {
            return response()->json( ['message' => $exception->getMessage()] );
        }

    }

    public function detailsPage( $id ) {

        $data['product'] = $this->productRepository->find( $id );
        if ( !$data['product'] ) {
            return abort( 404 );
        }
        return view( 'product.details', $data );

    }

    public function getProductDetails() {
        try {
            $data = $this->validate( $this->request, [
                'product_id' => 'required|integer|exists:products,id',
            ] );
            $product = $this->productRepository->detailsById( $data['product_id'], ['*'], ['images', 'prices'] );
            return response()->json( $product, 200 );
        } catch ( ValidationException $exception ) {
            return response()->json( ['message' => $exception->errors()], 422 );
        } catch ( QueryException | \Exception $exception ) {
            return response()->json( [
                'message' => 'Something went worng',
            ], 406 );
        }
    }

    public function priceUpdate() {
        try {
            $data = $this->validate( $this->request, [
                'price_id'      => 'required|integer|exists:product_prices,id',
                'cost_price'    => 'required|numeric',
                'selling_price' => 'required|numeric|gt:cost_price',
                'quantity'      => 'required|numeric|gt:0',
            ] );
            $price = $this->productPriceRepository->update( $data['price_id'], $data );
            return response()->json( [], 204 );
        } catch ( ValidationException $exception ) {
            return response()->json( [
                'message' => $exception->errors(),
            ], 422 );
        } catch ( QueryException | \Exception $exception ) {
            return response()->json( [
                'message' => 'Something Went wrong.',
            ], 406 );
        }
    }

    public function destroy( $id, ProductRepositoryInterface $productRepository ) {
        if ( !$productRepository->find( $id ) ) {
            return response()->json( ['message' => 'Product Not Found'] );
        }

        try {
            $productRepository->destroy( $id );
            return response()->json( [
                'message' => 'Product Deleted.',
            ], 204 );
        } catch ( QueryException | \Exception $exception ) {
            return response()->json( ['message' => $exception->getMessage()] );
        }
    }
}
