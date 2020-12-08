<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class PAVRepository
{
  /**
   * Store image
   *
   * @param String $location
   * @param File $image
   * @param Int[] $thumbs
   * @param String $baseName
   * @param String $propotion
   * @param Int $quality
   * @return Array
   */
  public function storeImage($location, $image, $thumbs = null, $baseName = null,    $propotion = null,    $quality = 80)
  {

    $fileName = $this->getFileName($image, $baseName);
    $savingFileName = $this->getSavingFileName($fileName);
    $baseDirectory = $this->getBaseDirectory($location);
    $savingDirectory = $this->getSavingDirectory($baseDirectory);
    $uploadingPath = $savingDirectory . $savingFileName;
    $makedImage = Image::make($image);
    if ($propotion) {
      $this->resizeImage($makedImage, $propotion);
    }
    $makedImage->save($uploadingPath, $quality);
    //For datatable thumbnail
    $makedImage->resize(100, 100, function ($constraint) {
      $constraint->aspectRatio();
      $constraint->upsize();
    });
    $makedImage->save($savingDirectory . 'thumb_' . $savingFileName, 40);

    if ($thumbs) {
      $this->createThumbnails($image, $thumbs, $propotion, $savingDirectory, $savingFileName, $quality);
    }
    return [
      'name' => $savingFileName,
      'location' => $location,
      // 'url' => 'storage/' . $location . '/' . $savingFileName,
      // 'thumb_url' => 'storage/' . $location . '/thumb_' . $savingFileName,
      // 'uri' => $location . '/' .  $savingFileName,
    ];
  }

  /**
   * Store images
   *
   * @param String $location
   * @param File[] $images
   * @param Int[] $thumbs
   * @param String $baseName
   * @param String $propotion
   * @param Int $quality
   * @return Array
   */
  public function storeImages($location, $images, $thumbs = null, $baseName = null, $propotion = null, $quality = 80)
  {
    return array_map(function ($image)
    use ($location, $baseName, $thumbs, $propotion, $quality) {
      return $this->storeImage($location, $image, $thumbs, $baseName, $propotion, $quality);
    }, $images);
  }

  public function updateImages($model, $location, $images, $pointer, $thumbs = null, $baseName = null, $propotion = null, $quality = 80): array
  {
    if (count($images) > 0) {

        $databaseImages = json_decode($model->getRawOriginal($pointer));
      $this->deleteImagesFromDatabase($databaseImages, $thumbs);
      $filterdNewImages = $this->storeImages($location, $images, $thumbs);
      return  $filterdNewImages;
    } else {
      return $model->getRawOriginal($pointer);
    }
  }


  /**
   * Delete images and thumbs from storage based on databse images and incoming images array
   *
   * @param Image[] $databaseImages
   * @param Image[] $incomingImages
   * @param Int[] $thumbs
   */
  public function deleteImagesFromDatabase($databaseImages, $thumbs = null)
  {
    if ($databaseImages)
      foreach ($databaseImages as $db_image) {
        $this->deleteImage($db_image, $thumbs);
      }
  }

  /**
   * Delete image using model and pointer
   *
   * @param Model $model
   * @param String $pointer
   * @param Int[] $thumbs
   */
  public function deleteImage($db_image, $thumbs = null)
  {
    $org_image = 'public/' . $db_image->location . '/' . $db_image->name;
    $org_thumb_image = 'public/' . $db_image->location . '/thumb_' . $db_image->name;
    if ($thumbs) {
      $thumbImages = $this->getThumbnailImages($org_image, $thumbs);
      Storage::delete($thumbImages);
    }
    if (Storage::exists($org_image)) {
      Storage::delete($org_image);
    }
    if (Storage::exists($org_thumb_image)) {
      Storage::delete($org_thumb_image);
    }
  }

  // /**
  //  * Delete images using model and pointer
  //  *
  //  * @param Model $model
  //  * @param String $pointer
  //  * @param Int[] $thumbs
  //  */
  // public function deleteImages($model, $pointer, $thumbs = null)
  // {
  //   $databaseImages = json_decode($model->getRawOriginal($pointer));
  //   foreach ($databaseImages as $databaseImage) {
  //     $this->deleteImageByDatabase($databaseImage, $thumbs);
  //   }
  // }

  /**
   * Making filename by file
   *
   * @param File $file
   * @param String $baseName
   */
  private function getFileName($file, $baseName = null)
  {
    $fileName = $file->getClientOriginalName();
    if ($baseName) {
      $extension = pathinfo($fileName, PATHINFO_EXTENSION);
      return $baseName . '.' . $extension;
    }

    return $fileName;
  }

  /**
   * Making saving filename by fileName
   *
   * @param String $fileName
   */
  private function getSavingFileName($fileName)
  {
    $baseName = $this->slugify(pathinfo($fileName, PATHINFO_FILENAME));
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    return $baseName . '_' . time() . '_' . uniqid() . '.' . $extension;
  }

  /**
   * Making saving directory by base directory
   *
   * @param String $baseDirectory
   */
  private function getSavingDirectory($baseDirectory)
  {
    Storage::makeDirectory($baseDirectory, 777);
    return storage_path('app/' . $baseDirectory);
  }

  /**
   * Making base directory by location
   *
   * @param String $location
   */
  private function getBaseDirectory($location)
  {
    return 'public/' . $location . '/';
  }

  /**
   * Resize the image of specified propotion
   *
   * @param Image $image
   * @param String $propotion
   */
  private function resizeImage($makedImage, $propotion)
  {
    if ($propotion) {
      $width = explode("X", $propotion)[0];
      $height = explode("X", $propotion)[1];
      $makedImage->resize($width, $height);
    }
  }

  /**
   * Create a thumbnails of specified percentages
   *
   * @param Image $image
   * @param Int[] $thumbs
   * @param String $propotion
   * @param String $savingDirectory
   * @param String $savingFileName
   * @param Int $quality
   */
  private function createThumbnails($image, $thumbs, $propotion, $savingDirectory, $savingFileName, $quality)
  {
    if ($thumbs) {
      foreach ($thumbs as $thumbSize) {
        $makedImage = Image::make($image);
        if ($propotion) {
          $width = explode("X", $propotion)[0];
          $height = explode("X", $propotion)[1];
        } else {
          $width = $makedImage->width();
          $height = $makedImage->height();
        }
        $width = ($thumbSize / 100) * $width;
        $height = ($thumbSize / 100) * $height;
        $makedImage->resize($width, $height, function ($constraint) use ($propotion) {
          if (!$propotion) $constraint->aspectRatio();
        });
        $fileName = $thumbSize . '_' . $savingFileName;
        $uploadingPath = $savingDirectory . $fileName;
        $makedImage->save($uploadingPath, $quality);
      }
    }
  }

  // /**
  //  * Delete images and thumbs from storage by databse images
  //  *
  //  * @param Image $databaseImage
  //  * @param Int[] $thumbs
  //  */
  // private function deleteImageByDatabase($databaseImage, $thumbs)
  // {
  //   $thumbImages = $this->getThumbnailImages($databaseImage->location, $thumbs);
  //   Storage::delete($thumbImages);
  //   if (Storage::exists($databaseImage->location)) {
  //     Storage::delete($databaseImage->location);
  //   }
  // }

  /**
   * Get thumbnail images from location
   *
   * @param String $location
   * @param Int[] $thumbs
   */
  private function getThumbnailImages($location, $thumbs)
  {
    if ($thumbs && !empty($thumbs)) {
      $dirName = dirname($location);
      $fileName = basename($location);
      $thumbs = array_filter($thumbs, function ($thumb) use ($dirName, $fileName) {
        $thumbFileName = $dirName . '/' . $thumb . '_' . $fileName;
        return Storage::exists($thumbFileName);
      });
      return array_map(function ($thumb) use ($dirName, $fileName) {
        $thumbFileName = $dirName . '/' . $thumb . '_' . $fileName;
        return $thumbFileName;
      }, $thumbs);
    }
  }
  public function slugify($string, $separator = '-')
  {
      $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
      $special_cases = array('&' => 'and', "'" => '');
      $string = mb_strtolower(trim($string), 'UTF-8');
      $string = str_replace(array_keys($special_cases), array_values($special_cases), $string);
      $string = preg_replace($accents_regex, '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
      $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
      $string = preg_replace("/[$separator]+/u", "$separator", $string);
      return $string;
  }
}
