<?php

namespace App\Traits;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

trait UploadFile {
    public function uploadSingleImage( UploadedFile $uploadedFile, string $folder, string $disk = 'public', bool $resize = false, $resize_value = 120 ) {

        // Temp folder uploaded file save

        $temp_upload = app()->basePath( 'public/uploads' );
        // Check extention from mimes

        // Filename set & remove space in filename
        $file_name = pathinfo( $uploadedFile->getClientOriginalName(), PATHINFO_FILENAME );
        $file_name = preg_replace( "/[^A-Za-z0-9]/", '', $file_name );
        $file_name = preg_replace( "/\s+/", '-', $file_name );
        $file_name = $file_name . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();

        // Save as original
        try {
            Image::make( $uploadedFile )->orientate()->save( $temp_upload . $file_name )->destroy();
            $path = app( 'filesystem' )->disk( $disk )->putFileAs( $folder, new File( $temp_upload . $file_name ), $file_name, 'public' );

            if ( $resize ) {
                Image::make( $uploadedFile )->orientate()->resize( $resize_value, null, function ( $constraint ) {
                    $constraint->aspectRatio();
                } )->save( $temp_upload . $file_name )->destroy();
                $resized_path = app( 'filesystem' )->disk( $disk )->putFileAs( $folder . '/resized', new File( $temp_upload . $file_name ), $file_name, 'public' );
            }
        } catch ( Exception $exception ) {
            Log::debug( 'Image upload error', [$exception->getMessage()] );
            return [];
        }

        if ( !$path ) {
            Log::debug( 'Image not uploaded. Local storage permission issue' );
            return [];
        } else {
            unlink( $temp_upload . $file_name );
            return [
                'full_path'    => $path,
                'resized_path' => $resize ? $resized_path : null,
                'file_name'    => $file_name,
                'uploaded_dir' => '/' . pathinfo( $path, PATHINFO_DIRNAME ),
            ];
        }
        // If resize true also save in resize format
        // return full file information
    }
}