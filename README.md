# php-fw

## Image

### About

В каталоге с пользовательскими картинками: `/static/pics` находятся папки:
* `/orig` - для хранения оригиналов
* `/crop` - для хранения картинок, обрезанных до определенных размеров
* `/scale` - для хранения картинок, вписанных в рамку определенного размера (они требуются, когда важно показать все картинку в ограниченной области, чтобы ничего не было обрезано)


В каждой из этих папок - одинаковая иерархия: `/AB/CD/ABCDXXXXXXX…`
* `AB` - первые два символа из названия файла
* `CD` - вторые два символа из названия файла


В папке `/orig` - названия файла имеют вид: `<MD5>.jpg`


В папках `/crop` и `/scale` - названия файлов могут иметь вид:
* `<MD5>_<WIDTH>_<HEIGHT>.jpg` - картинка нужного размера без водяного знака
* `<MD5>_<WIDTH>_<HEIGHT>_stamp.jpg` - картинка нужного размера с водяным знаком

### Using
```php
<?php

require_once 'scripts/classes/Config.php';
require_once Config::PATH_SCRIPTS_CLASSES . '/Image/Image.php';

$config = new Config();
$image = new Image_Image($config);

// Uploading origin image from remote resource
$remoteSource = 'https://www.google.ru/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png';
$newFileName = md5($remoteSource);
$image->uploadFromRemoteResource($remoteSource, $newFileName);

// Uploading origin image from POST
$fileTmpName = 'jdKGirhJ.jpg';
$newFileName = md5($fileTmpName);
$image->uploadFromPost($fileTmpName, $newFileName);

// Getting of path of image cropped by sizes, without a watermark
$imagePath = $image->getImagePath($pictureId, $width, $height);
$imagePath = $image->getImagePath($pictureId, $width, $height, 'crop');
$imagePath = $image->getImagePath($pictureId, $width, $height, 'crop', false);

// Getting of path of image scaled by sizes, without a watermark
$imagePath = $image->getImagePath($pictureId, $width, $height, 'scale');
$imagePath = $image->getImagePath($pictureId, $width, $height, 'scale', false);

// Getting of path of image cropped or scaled by sizes, with a watermark
$imagePath = $image->getImagePath($pictureId, $width, $height, 'crop', true); // image cropped
$imagePath = $image->getImagePath($pictureId, $width, $height, 'scale', true); // image scaled
```
