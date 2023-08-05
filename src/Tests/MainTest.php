<?php

namespace CodeBuds\WebPConverter\Tests;

use CodeBuds\WebPConverter\WebPConverter;
use PHPUnit\Framework\TestCase;

final class MainTest extends TestCase
{
    public const DATA_PATH =  __DIR__ . '/Data';
    public const INITIAL_IMAGE_PATH =   self::DATA_PATH . '/jpeg.jpeg';
    public const CONVERTED_IMAGE_PATH =  self::DATA_PATH . '/jpeg.webp';

    public function testCreateWebPImage(): void
    {
        $options = [];
        //Before first image creation make sure the final image does not exist
        if (file_exists(self::CONVERTED_IMAGE_PATH)) {
            unlink(self::CONVERTED_IMAGE_PATH);
        }

        $options['savePath'] = self::DATA_PATH;
        $options['saveFile'] = true;

        WebPConverter::createWebPImage(
            self::INITIAL_IMAGE_PATH,
            $options
        );

        self::assertFileExists(self::CONVERTED_IMAGE_PATH);


        $initialFileSize = filesize(self::INITIAL_IMAGE_PATH);
        $convertedFileSize = filesize(self::CONVERTED_IMAGE_PATH);

        $this->assertLessThan($initialFileSize, $convertedFileSize);

        //Expect an exception when the file already exists
        $this->expectExceptionMessage("The webp file already exists, set the force option to true if you want to override it");
        WebPConverter::createWebPImage(
            self::INITIAL_IMAGE_PATH,
            $options
        );
    }

    /**
  * @doesNotPerformAssertions
  * @depends testCreateWebPImage
  * @throws \Exception
  */
 public function testCreateWebPImageForce(): void
 {
     $options = [];
     $options['savePath'] = __DIR__ . '/Data';
     $options['saveFile'] = true;
     //If force is set to true no exception will be thrown
     $options['force'] = true;
     WebPConverter::createWebPImage(
         __DIR__ . '/Data/jpeg.jpeg',
         $options
     );
 }
}
