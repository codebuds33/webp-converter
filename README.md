# Webp Converter

I had already found a great webP conversion package [rosell-dk/webp-convert](https://packagist.org/packages/rosell-dk/webp-convert) that seems to work in many cases.

However, most of my projects will now be running on PHP7.4 and Symfony5, so I created a very small converter for those situations.

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
Multiple exceptions can be thrown. First of all the Symfony File is used for the `guessExtension()` function.
This allows us to check the type of file provided and will make sure that whether the file is called .JPG, .jpeg, .jpg, ... or something else but it ends up being a jpeg file this is what will be used.
If the file is not one of the allowed types (jpeg, png, gif and bmp) an exception will be thrown.

If the provided image is already a webP  image the exception will send that information.


Finally, if the file is one of the allowed types but something goes wrong during the conversion made by GD that exception will be forwarded.

## Return values

If no exceptions are present the return will consist of the webP image ressource and what the path would be for the image if you want it saved in the same directory as the original one.

## Options

You can also set options but this is not required.

The `saveFile` option is false by default. As mentioned before this will only return the ressource, and the possible path for the webP image.
This means you then need to run the gd function to save the image.

However, if you set this function to true the image will be saved automatically by trigerring the gd `imagewebp` function.

```php
$saveFile = $options['saveFile'] ??= false;
if($saveFile)
{
    imagewebp($imageRessource, $webPPath, $quality);
}
```

As that is possible, the second option is `quality` which by default is 80. This is only useful if the `saveFile` option is set to true (as the image is not created and saved otherwise).