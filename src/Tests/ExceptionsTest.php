<?php

namespace CodeBuds\WebPConverter\Tests;

use CodeBuds\WebPConverter\WebPConverter;
use PHPUnit\Framework\TestCase;

final class ExceptionsTest extends TestCase
{
    public const DATA_PATH =  __DIR__ . '/Data';
    public const INITIAL_IMAGE_PATH =   self::DATA_PATH . '/jpeg.jpeg';
    public const CONVERTED_IMAGE_PATH =  self::DATA_PATH . '/jpeg.webp';

    public function testSaveFile(): void
    {
        if (file_exists(self::CONVERTED_IMAGE_PATH)) {
            unlink(self::CONVERTED_IMAGE_PATH);
        }

        $options = [
            'saveFile' => 'yes',
        ];
        $this->expectExceptionMessage("The saveFile option can only be a boolean");
        WebPConverter::createWebPImage(
            self::INITIAL_IMAGE_PATH,
            $options
        );
    }

    public function testForceFile(): void
    {
        if (file_exists(self::CONVERTED_IMAGE_PATH)) {
            unlink(self::CONVERTED_IMAGE_PATH);
        }

        $options = [
            'saveFile' => false,
            'force' => 'yes',
        ];
        $this->expectExceptionMessage("The force option can only be a boolean");
        WebPConverter::createWebPImage(
            self::INITIAL_IMAGE_PATH,
            $options
        );
    }

    public function testQuality(): void
    {
        if (file_exists(self::CONVERTED_IMAGE_PATH)) {
            unlink(self::CONVERTED_IMAGE_PATH);
        }

        $options = [
            'saveFile' => false,
            'quality' => 120,
        ];
        $this->expectExceptionMessage("The quality option needs to be an integer between 1 and 100");
        WebPConverter::createWebPImage(
            self::INITIAL_IMAGE_PATH,
            $options
        );
    }

    public function testSavePath(): void
    {
        if (file_exists(self::CONVERTED_IMAGE_PATH)) {
            unlink(self::CONVERTED_IMAGE_PATH);
        }

        $options = [
            'saveFile' => false,
            'savePath' => 1
        ];
        $this->expectExceptionMessage("The savePath, filename and filenameSuffix options can only be strings");
        WebPConverter::createWebPImage(
            self::INITIAL_IMAGE_PATH,
            $options
        );
    }
}
