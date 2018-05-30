<?php

namespace Pelima\Imageupload;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class Imageupload
{
    private $intervention;
    private $driver;
    private $imageUploadPath;
    private $imageDimension;
    private $hashedFilePath;
    private $fileStorage;
    private $model;
    
    public function __construct(ImageManager $intervention)
    {
        $this->intervention = $intervention;
        $this->configurations();
        $this->model = new ImageuploadModel();
    }

    public function upload(Request $request)
    {
        if ($request->file()) {

            // Get input key
            foreach($request->file() as $key => $file) {
                if ($request->hasFile($key)) {
                    // Check if user upload image
                    if ($this->isImageFile($request->file($key))) {
                        // If image, process it..
                        return $this->processImage($file);
                    }
                }
            }
        }

        return false;

    }

    /**
     * Validate the image file
     *
     * @param $file
     * @return bool
     */
    private function isImageFile($file)
    {
        // Acceptable file extensions
        $extensions = [
            'png',
            'jpeg',
            'jpg',
            'gif',
        ];

        // Check file extension is image format
        if (in_array($file->extension(), $extensions)) {
            // Hash image name and get path
            $this->hashedFilePath = $file->hashName($this->imageUploadPath);
            return true;
        }
        return false;
    }

    /**
     * Store file
     *
     * @param $file
     * @return bool
     */
    private function storeFile($file)
    {
        // Store file
        if (Storage::disk($this->fileStorage)->put($this->hashedFilePath, (string) $file->encode())) {

            // Save file name in the database
            $payload['file_path'] = str_replace($this->imageUploadPath, '', $this->hashedFilePath);

            // Model to save path and file name
            return $this->model->create($payload);
        }
        return false;
    }

    /**
     * Process image
     *
     * @param $file
     * @return id|bool
     */
    private function processImage($file)
    {
        // Create image file
        $image = $this->intervention->make($file);

        // Create square image canvas
        $this->createSquareCanvas($image);

        return $this->storeFile($image);
    }

    /**
     * Configuration settings
     *
     * @return $this
     */
    private function configurations()
    {
        $this->driver = config('imageupload.library', 'gd');
        $this->imageUploadPath = config('imageupload.upload_path', 'images/');
        $this->imageDimension = config('imageupload.dimension', 600);
        $this->fileStorage = config('imageupload.storage', 'public');

        $this->intervention->configure(['driver' => $this->driver]);

        return $this;
    }

    /**
     * Create square canvas
     *
     * @param $image
     * @return mixed
     */
    private function createSquareCanvas($image)
    {
        $width  = $image->width();
        $height = $image->height();
        
        // Get default dimension
        $dimension = $this->imageDimension;
        
        $vertical   = (($width < $height) ? true : false);
        $horizontal = (($width > $height) ? true : false);
        $square     = (($width = $height) ? true : false);

        if ($vertical) {
            $top = $bottom = 0;
            $newHeight = ($dimension) - ($bottom + $top);
            $image->resize(null, $newHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
            
        } else if ($horizontal) {
            $right = $left = 0;
            $newWidth = ($dimension) - ($right + $left);
            $image->resize($newWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            
        } else if ($square) {
            $right = $left = 0;
            $newWidth = ($dimension) - ($left + $right);
            $image->resize($newWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            
        }

        $image->resizeCanvas($dimension, $dimension, 'center', false, '#ffffff');
        $image->fit($this->imageDimension);
        
        return $image;
    }

    /**
     * Index page: Upload image
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }
}
