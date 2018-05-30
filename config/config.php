<?php

return [

  /*
   * Library used to manipulate image.
   *
   * Options: gd (default), imagick, gmagick
   */
  'library' => env('IMAGEUPLOAD_LIBRARY', 'gd'),

  /**
   * Upload Path
   *
   * Default: images/
   */
  'upload_path' => env('IMAGEUPLOAD_UPLOAD_PATH', 'images/'),

  /**
   * Image dimension
   */
  'dimension' => env('IMAGEUPLOAD_DIMENSION', 600),

  /**
   * Storage
   *
   * Options: public, s3
   */
  'storage' => env('IMAGEUPLOAD_STORAGE', 'public'),

];
