<?php


namespace CodeBuds\WebPConverter;

use Exception;
use Symfony\Component\HttpFoundation\File\File;

class WebPConverter
{
    /**
     * @param $path
     * @param $extension
     * @return false|resource
     * @throws Exception
     */
    private static function createImageRessource($path, $extension)
    {
        if ($extension === 'png') {
            $imageRessource = imagecreatefrompng($path);
        } elseif ($extension === 'jpeg') {
            $imageRessource = imagecreatefromjpeg($path);
        } elseif ($extension === 'bmp') {
            $imageRessource = imagecreatefrombmp($path);
        } elseif ($extension === 'gif') {
            $imageRessource = imagecreatefromgif($path);
        } else {
            throw new Exception("No valid file type provided for " . $path);
        }
        self::setColorsAndAlpha($imageRessource);
        return $imageRessource;
    }

    /**
     * @param $image
     */
    private static function setColorsAndAlpha(&$image)
    {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    /**
     * @param File|string $image
     * @param $options
     * @return array|Exception|null
     * @throws Exception
     */
    public static function createWebPImage($image, $options = [])
    {
        $file = ($image instanceof File) ? $image : new File($image);
        $path = ($image instanceof File) ? $image->getPathname() : $image;

        $saveFile = $options['saveFile'] ??= false;
        $quality = $options['quality'] ??= 80;

        $extension = $file->guessExtension();

        if ($file->guessExtension() === "webp") {
            throw new Exception("{$path} is already webP");
        }

        try {
            $imageRessource = self::createImageRessource($path, $extension);
            $webPPath = substr($path, 0, strrpos($path, '.')) . ".webp";

            if ($saveFile) {
                imagewebp($imageRessource, $webPPath, $quality);
            }
            return [
                "ressource" => $imageRessource,
                "path" => $webPPath
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }
}