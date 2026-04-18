<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageValidateRequest;
use App\Services\StorageImage\StorageImageServiceInterface;
use Illuminate\Http\Request;

class StorageImageController extends Controller
{
    public function __construct(protected StorageImageServiceInterface $storageImageService)
    {
        
    }
    // get presigned url for upload image to s3
    public function getPresignedUrl(ImageValidateRequest $request)
    {
        $presignedURL = $this->storageImageService->getPresignedUrl($request->file('product_image_url'), $request->input('folder'));

        return $this->successResponse($presignedURL, 'Get presigned URL successfully');
    }
    
}
