<?php

namespace CodeBuds\WebPConversionBundle\Tests;

use CodeBuds\WebPConverter\WebPConverter;
use PHPUnit\Framework\TestCase;

final class Test extends TestCase
{
	public function testCreateWebPImage(): void
	{
		$convertedPath = __DIR__ . '/Data/jpeg.webp';

		//Before first image creation make sure the final image does not exist
		if(file_exists($convertedPath)) {
			unlink($convertedPath);
		}

		$options['savePath'] = __DIR__ . '/Data';
		$options['saveFile'] = true;

		WebPConverter::createWebPImage(
			__DIR__ . '/Data/jpeg.jpeg',
			$options
		);
		self::assertFileExists(__DIR__ . '/Data/jpeg.webp');

		//Expect an exception when the file already exists
		$this->expectExceptionMessage("The webp file already exists, set the force option to true if you want to override it");
		WebPConverter::createWebPImage(
			__DIR__ . '/Data/jpeg.jpeg',
			$options
		);

	}

	/**
	 * @doesNotPerformAssertions
	 * @depends testCreateWebPImage
	 * @return void
	 * @throws \Exception
	 */
	public function testCreateWebPImageForce(): void
	{
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
