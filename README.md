![Code Coverage Badge](./plugin/.coverage/badge.svg)

# Webp Converter

I had already found a great webP conversion package [rosell-dk/webp-convert](https://packagist.org/packages/rosell-dk/webp-convert) that seems to work in many cases.

~However, most of my projects will now be running on PHP7.4 and Symfony5, so I created a very small converter for those situations.~


## Usage

You can use composer to get this package :

`composer require codebuds/webp-converter`

Then, inside a Symfony controller for example you can use it like the following :

``` php
use CodeBuds\WebPConverter\WebPConverter;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index()
    {
        $path  = "/var/www/symfony/public/images/test.png";
        try {
            $webp = WebPConverter::createWebpImage($path, ['saveFile' => true, 'quality' => 10]);
        } catch(Exception $e) {
            // Do something with the exception
        }
        return $this->render('page/home.html.twig');
    }
```

The `WebPConverter::createWebpImage` static function needs the resource for the image.

This can either just be the path to the image, in which case the function itself will try to create the `Symfony\Component\HttpFoundation\File\File` from the path.

You can also directly pass the File element if you want :

```php
public function index()
{
    // ...
    $file  = new File("/var/www/symfony/public/images/test.png");
    $webp = WebPConverter::createWebpImage($file);
    // ...
}
```

## Exceptions
Multiple exceptions can be thrown. First the Symfony File is used for the `guessExtension()` function.
This allows us to check the type of file provided and will make sure whether the file is called .JPG, .jpeg, .jpg, ... or something else, but it ends up being a jpeg file this is what will be used.
If the file is not one of the allowed types (jpeg, png, gif and bmp) an exception will be thrown.

If the provided image is already a webP image the exception will send that information.

If the saveFile or the force options are not booleans, or the quality option is not an integer between 1 and 100 the information will be in an exception.

The filename, filenameSuffix and savePath options need to be strings.

If saveFile is set to true and force to the default false value, and the webP image already exists an exception will be thrown to let the user know force needs to be set to true is you want to override an existing file.

Finally, if the file is one of the allowed types but something goes wrong during the conversion made by GD that exception will be forwarded.

## Return values

If no exceptions are present the return will consist of the webP image ressource and what the path would be for the image if you want it saved in the same directory as the original one.

## Options

You can also set options but this is not required.

The `saveFile` option is false by default. As mentioned before this will only return the ressource, and the possible path for the webP image.
This means you then need to run the gd function to save the image.

However, if you set this function to true the image will be saved automatically by triggering the gd `imagewebp` function.

```php
$saveFile = $options['saveFile'] ??= false;
if($saveFile)
{
    imagewebp($imageRessource, $webPPath, $quality);
}
```

If you want to save the file directly there are more options to customize the webP file and it's location :

```php
$path = '/var/www/symfony/public/images/a_file.jpg';

WebPConverter::createWebpImage(
    $path,
    [
    'saveFile' => true,
    'force' => true,
    'filename' => 'a_new_file',
    'filenameSuffix' => '_q50',
    'quality' => 50,
    'savePath' => '/var/www/symfony/public/webp'
    ]
);
```

This example will create the webP image /var/www/symfony/public/webp/a_new_file_q50.webp

Default values :

- `saveFile` => false
- `force` => false
- `quality` => 80
- `filename` => the same as the file that is going to be converted.
- `savePath` => the same as the path of the file that is going to be converted.
- `filenameSuffix` => an empty string


As that is possible, the second option is `quality` which by default is 80. This is only useful if the `saveFile` option is set to true (as the image is not created and saved otherwise).
