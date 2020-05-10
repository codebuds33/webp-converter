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
            throw new Exception("No valid file type provided for {$path}");
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
    private static function verifyOptions(array &$options)
    {
        $options['saveFile'] ??= false;
        $options['quality'] ??= 80;
        $options['force'] ??= false;
        $options['filenameSuffix'] ??= '';

        [
            'saveFile' => $saveFile,
            'force' => $force,
            'quality' => $quality,
            'savePath' => $savePath,
            'filename' => $filename,
            'filenameSuffix' => $filenameSuffix
        ] = $options;

        if (!is_bool($saveFile)) {
            throw new Exception('The saveFile option can only be a boolean');
        }

        if (!is_bool($force)) {
            throw new Exception('The force option can only be a boolean');
        }

        if (!is_int($quality) || $quality < 1 || $quality > 100) {
            throw new Exception('The quality option needs to be an integer between 1 and 100');
        }

        if (!is_string($savePath) || !is_string($filename) || !is_string($filenameSuffix)) {
            throw new Exception('The savePath, filename and filenameSuffix options can only be strings');
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
        $fullPath = ($image instanceof File) ? $image->getRealPath() : $image;

        $options['savePath'] ??= $file->getPath();
        $options['filename'] ??= substr($file->getFilename(), 0, strrpos($file->getFilename(), '.'));

        self::verifyOptions($options);

        [
            'saveFile' => $saveFile,
            'force' => $force,
            'quality' => $quality,
            'savePath' => $savePath,
            'filename' => $filename,
            'filenameSuffix' => $filenameSuffix
        ] = $options;


        $extension = $file->guessExtension();

        if ($file->guessExtension() === "webp") {
            throw new Exception("{$fullPath} is already webP");
        }

        $imageRessource = self::createImageRessource($fullPath, $extension);
        $webPPath = "{$savePath}/{$filename}{$filenameSuffix}.webp";

        if ($saveFile) {
            if (file_exists($webPPath) && !$force) {
                throw new Exception("The webp file already exists, set the force option to true if you want to override it");
            }
            imagewebp($imageRessource, $webPPath, $quality);
        }
        return [
            "ressource" => $imageRessource,
            "path" => $webPPath
        ];
    }
}