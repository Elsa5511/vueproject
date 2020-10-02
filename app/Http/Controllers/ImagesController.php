<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImagesController extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public function isValidImage($imageFile) {

        if (is_readable($imageFile)) {
            $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
            $contentType = mime_content_type($imageFile);
            if (in_array($contentType, $allowedMimeTypes)) {
                return $contentType;
            }
        }
        return false;
    }

    public function dispatchImage(\Illuminate\Http\Request $request) {


        // Obtengo los parametroes enviados
        $sectionName = str_replace('.', DIRECTORY_SEPARATOR, $request->section);
        $id = $request->folder;
        $thumb = $request->thumb; // ej: 200x25
        $fileName = $request->filename;

        /* if ($id == '0') {
          $id = '';
          } */

        $id = str_replace('.', '/', $id);

        if (!preg_match('/^([0-9]+)([x_-])([0-9]+)$/', $thumb, $size)) {
            exit();
        }



        // Obtengo ancho y alto
        $requestWidth = (int) $size[1];
        $requestHeight = (int) $size[3];

        // Seteo las dimensiones minimas de la imagen solicitada
        if ($requestWidth < 15 && $requestWidth != 0)
            $requestWidth = 15;
        if ($requestHeight < 15 && $requestHeight != 0)
            $requestHeight = 15;


        $fill = false;
        $noCrop = false;
        if ($size[2] == '-') {
            $noCrop = true;
        } elseif ($size[2] == '_') {
            $fill = true;
            $noCrop = true;
            $fillWidth = $requestWidth;
            $fillHeight = $requestHeight;
        }
        


        // Determino de donde se encuentra la imagen original
        switch ($sectionName) {
            default:
                $idPath = $id == '0' ? '' : $id;
                $originalImageFile = storage_path('uploads') . DIRECTORY_SEPARATOR . $sectionName . DIRECTORY_SEPARATOR . $idPath . DIRECTORY_SEPARATOR . $fileName;
                break;
        }


        // Filtro los tamaÃ±os posibles,
        // para evitar que se pueda crear cache de infinitos tamaÃ±os
        switch ($thumb) {
            case '100x80':
            case '0x0':
                break;
            default:
                // $originalImageFile='';
                break;
        }

        if (!is_readable($originalImageFile)) {
            $originalImageFile = public_path('img') . '/defaults/' . $sectionName . '/not-found.png';
            if (!is_readable($originalImageFile)) {
                $originalImageFile = public_path('img') . '/defaults/not-found.png';
            }
        }

        $imageMimeType = $this->isValidImage($originalImageFile);




        if (!$imageMimeType || preg_match('/\.bmp/i', $fileName)) {
            $requestWidth = $requestHeight = 10;
            $originalImageFile = imagecreatetruecolor($requestWidth, $requestHeight);
            $bg = imagecolorallocate($originalImageFile, 150, 150, 150);
            imagefilledrectangle($originalImageFile, 0, 0, ($requestWidth - 1), ($requestHeight - 1), $bg);
            header('Content-Type: image/jpeg');
            imagejpeg($originalImageFile);
            imagedestroy($originalImageFile);
            exit();
        }

        // Creo el directorio donde se almacena la imagen generada si no existe.
        $dir_cache = public_path('cache/images/' . $sectionName . '/' . $id . '/' . $thumb);
        $file = File::makeDirectory($dir_cache, $mode = 0777, true, true);
        //Storage::makeCacheDir("$sectionName/$id/$thumb/");
        // Si existe la imagen redirecciono a la version cacheada
        $img_cache = $dir_cache . DIRECTORY_SEPARATOR . $fileName;


        if (!isset($_GET['no-cache']) && is_file($img_cache)) {
            header("HTTP/1.1 302 Found");
            header("location: " . url("cache/images/$sectionName/$id/$thumb", $fileName));
            exit();
        }


        // Defino la cabecera de la imagen
        header('Content-Type: image/jpeg');
        $png = false;
        // Creo jpg a partir de la imagen original
        switch ($imageMimeType) {
            case 'image/jpeg':
                $originalImage = @imagecreatefromjpeg($originalImageFile);
                break;
            case 'image/gif':
                $originalImage = @imagecreatefromgif($originalImageFile);
                break;
            case 'image/png':
                $originalImage = @imagecreatefrompng($originalImageFile);
                $png = true;
                break;
            default:
                exit();
        }

// Obtengo las dimensiones de la imagen original
        $originalWidth = imagesx($originalImage);
        $originalHeight = imagesy($originalImage);

// Calculo relacion de aspecto de la imagen original y la solicitada.
        $rRel = 0;
        if ($requestHeight > 0 && $requestWidth > 0) {
            $rRel = ($requestWidth / $requestHeight);
        }

        $oRel = ($originalWidth / $originalHeight);
        $isWide = $rRel < $oRel;
// 
        if ($noCrop) {
            // Determino el aspecto de la imagen original.
            if ($isWide) {
                if ($originalWidth < $requestWidth) {
                    $requestWidth = $originalWidth;
                }
                $requestHeight = 0;
            } else {
                if ($originalHeight < $requestHeight) {
                    $requestHeight = $originalHeight;
                }
                $requestWidth = 0;
            }
        }


// Calculo dimensiones
        if ($requestWidth == 0 && $requestHeight == 0) {
            @copy($originalImageFile, $img_cache);
            echo(@file_get_contents($originalImageFile));
            exit;
        } elseif ($requestWidth == 0) {
            $requestWidth = round($originalWidth * ($requestHeight / $originalHeight));
        } elseif ($requestHeight == 0) {
            $requestHeight = round($originalHeight * ($requestWidth / $originalWidth));
        }


// Calculo el las dimensiones de la imagen segun se solicita
        if ($isWide) {
            $srcHeight = $originalHeight;
            $srcWidth = ( $requestWidth / $requestHeight) * $originalHeight;
            $srcPositionX = ($originalWidth - $srcWidth) / 2;
            $srcPositionY = 0;
        } else {
            $srcWidth = $originalWidth;
            $srcHeight = ($requestHeight / $requestWidth) * $originalWidth;
            $srcPositionX = 0;
            $srcPositionY = ($originalHeight - $srcHeight) / 5;
        }

// Creo la imagen
        $finalImage = imagecreatetruecolor($requestWidth, $requestHeight);

        if ($png) {
            imagealphablending($finalImage, false);
            imagesavealpha($finalImage, true);
            imagecopyresampled($finalImage, $originalImage, 0, 0, (int) $srcPositionX, (int) $srcPositionY, $requestWidth, $requestHeight, (int) $srcWidth, (int) $srcHeight);
            imagepng($finalImage, $img_cache);
        } else {
            if (!$fill) {
                imagecopyresampled($finalImage, $originalImage, 0, 0, (int) $srcPositionX, (int) $srcPositionY, $requestWidth, $requestHeight, (int) $srcWidth, (int) $srcHeight);
                imagejpeg($finalImage, $img_cache, 93);
            } else {
                $fillImage = imagecreatetruecolor($fillWidth, $fillHeight);
                imagefilledrectangle($fillImage, 0, 0, ($fillWidth), ($fillHeight), imagecolorallocate($fillImage, 255, 255, 255));
                imagecopyresampled($fillImage, $originalImage, (int) (max(1, ($fillWidth - $requestWidth) / 2)), (int) (max(1, ( $fillHeight - $requestHeight) / 2)), 0, 0, $requestWidth, $requestHeight, $srcWidth - 1, $srcHeight - 1);
                imagejpeg($fillImage, $img_cache, 93);
                imagedestroy($fillImage);
            }
        }
        imagedestroy($finalImage);
        imagedestroy($originalImage);
        echo(@file_get_contents($img_cache));
        exit;
    }

}
