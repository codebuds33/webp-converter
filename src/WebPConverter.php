<?php


namespace CodeBuds\WebPConverter;

use Exception;
use GdImage;
use Symfony\Component\HttpFoundation\File\File;

class WebPConverter
{
    /**
     * @param string $path
     * @param string $extension
     * @throws Exception
     */
    private static function createImageResource(string $path, string $extension): GdImage
    {
        if ($extension === 'png') {
            $imageResource = imagecreatefrompng($path);
        } elseif ($extension === 'jpeg' || $extension === 'jpg') {
            $imageResource = imagecreatefromjpeg($path);
        } elseif ($extension === 'bmp') {
            $imageResource = imagecreatefrombmp($path);
        } elseif ($extension === 'gif') {
            $imageResource = imagecreatefromgif($path);
        } else {
            throw new Exception("No valid file type provided for {$path}");
        }
        self::setColorsAndAlpha($imageResource);
        return $imageResource;
    }

    private static function setColorsAndAlpha(GdImage $image): void
    {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    /**
     * @param array $options
     * @throws Exception
     */
    private static function verifyOptions(array &$options): void
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

    private static function setPathAndFilenameOptions(array &$options, File $file): void
    {
        $options['savePath'] ??= $file->getPath();
        $options['filename'] ??= substr($file->getFilename(), 0, strrpos($file->getFilename(), '.'));
    }

    private static function createWebPPath(array $options): string
    {
        [
            'savePath' => $savePath,
            'filename' => $filename,
            'filenameSuffix' => $filenameSuffix
        ] = $options;

        return "{$savePath}/{$filename}{$filenameSuffix}.webp";
    }

    /**
     * @throws Exception
     */
    public static function createWebPImage(string|File $image, array $options = []): array
    {
        $file = ($image instanceof File) ? $image : new File($image);
        $fullPath = $file->getRealPath();

        self::setPathAndFilenameOptions($options, $file);

        self::verifyOptions($options);

        [
            'saveFile' => $saveFile,
            'force' => $force,
            'quality' => $quality,
        ] = $options;

        $extension = $file->guessExtension();

        if ($file->guessExtension() === "webp") {
            throw new Exception("{$fullPath} is already webP");
        }

        $imageResource = self::createImageResource($fullPath, $extension);
        $webPPath = self::createWebPPath($options);

        if ($saveFile) {
            if ($force && file_exists($webPPath)) {
                throw new Exception("The webp file already exists, set the force option to true if you want to override it");
            }
            imagewebp($imageResource, $webPPath, $quality);
        }
        return [
            "resource" => $imageResource,
            "path" => $webPPath,
            "options" => $options
        ];
    }

    /**
     * @throws Exception
     */
    public static function convertedWebPImageExists(string|File $file, array $options = []): bool
    {
        return (file_exists(self::convertedWebPImagePath($file, $options)));
    }

    /**
     * @throws Exception
     */
    public static function convertedWebPImagePath(string|File $file, array $options = []): string
    {
        $file instanceof File ?: $file = new File($file);
        self::setPathAndFilenameOptions($options, $file);
        self::verifyOptions($options);
        return self::createWebPPath($options);
    }
}
