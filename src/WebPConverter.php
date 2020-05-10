<?php


namespace CodeBuds\WebPConverter;

use Exception;
use Symfony\Component\HttpFoundation\File\File;

class WebPConverter
{
    /**
     * @param string $path
     * @param string $extension
     * @return resource
     * @throws Exception
     */
    private static function createImageRessource(string $path, string $extension)
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
     * @param resource $image
     */
    private static function setColorsAndAlpha(&$image)
    {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    /**
     * @param array $options
     * @throws Exception
     */
    private static function verifyOptions(array $options){
        ['saveFile' => $saveFile, 'quality' => $quality] = $options;
        if(!is_bool($saveFile)){
            throw new Exception('The saveFile option can only be a boolean');
        }

        if(!is_int($quality) || $quality < 1 || $quality > 100){
            throw new Exception('The quality option needs to be an integer between 1 and 100');
        }
    }

    /**
     * @param File|string $image
     * @param array $options
     * @return array
     * @throws Exception
     */
    public static function createWebPImage($image, array $options = []): array
    {
        $file = ($image instanceof File) ? $image : new File($image);
        $path = ($image instanceof File) ? $image->getPathname() : $image;

        $saveFile = $options['saveFile'] ??= false;
        $quality = $options['quality'] ??= 80;

        self::verifyOptions($options);

        $extension = $file->guessExtension();

        if ($file->guessExtension() === "webp") {
            throw new Exception("{$path} is already webP");
        }

        $imageRessource = self::createImageRessource($path, $extension);
        $webPPath = substr($path, 0, strrpos($path, '.')) . ".webp";

        if ($saveFile) {
            imagewebp($imageRessource, $webPPath, $quality);
        }
        return [
            "ressource" => $imageRessource,
            "path" => $webPPath
        ];
    }
}