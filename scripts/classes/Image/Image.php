<?php

/**
 * Класс для работы с изображениями
 *
 * @category Image
 * @package Image_Image
 * @author Konstantin Baranov <bar.konstantin@gmail.com>
 * @since 08.02.2015
 */
class Image_Image
{
    var $pathImage;
    var $urlImage;
    var $pathWatermark;
    var $quality = 80;

    /*
    var $path;
    var $originalPath;
    var $picturePath;
    var $errors = array();
    var $_errorArray = array(
        '1' => 'Файл не загрузился',
        '2' => 'Некорректное название файла',
        '3' => 'Не создался файл-миниатюра',
        '4' => 'Не сохраняется файл-миниатюра',
        '5' => 'Файл не скопировался',
	    '6' => 'Невозможно создать каталог',
	    '7' => 'Файл не найден'
    );
    */


    /**
     * Конструктор
     *
     * @param Cfg $config
     * @param string $type
     */
    function __construct($config, $type = 'pics')
    {
        if ($type == 'pics') {
            $this->pathImage = $config->getConstantPathStaticPics();
            $this->urlImage = $config->getConstantUrlStaticPics();
        }
        $this->pathWatermark = $config->getConstantPathStaticImg() . '/watermarks/';
    }


    private function renderRelativeImagePath($imageType, $imageFilename, $imageWidth = 0, $imageHeight = 0, $flagStamp = false)
    {
        $imageFilename = trim($imageFilename);
        $imageWidth = intval($imageWidth);
        $imageHeight = intval($imageHeight);
        if (!is_bool($flagStamp)) {
            $flagStamp = false;
        }

        if (!in_array($imageType, array('orig', 'crop', 'scale')) || !strlen($imageFilename)) {
            return '';
        }

        $postfix = '';
        if ($imageWidth && $imageHeight) {
            $postfix .= '_' . $imageWidth . '_' . $imageHeight;
        }
        if ($flagStamp) {
            $postfix .= '_stamp';
        }

        $path = '/' . $imageType
            . '/' . substr($imageFilename, 0, 2)
            . '/' . substr($imageFilename, 2, 2);

        if (!file_exists($this->pathImage . $path)) {

            mkdir($this->pathImage . $path, 0775, true);

            $folderOwnerInfo = posix_getpwuid(fileowner($this->pathImage . $path));

            if ($folderOwnerInfo['name'] == Cfg::FILE_SYSTEM_SUPERUSER) {
                chown($this->pathImage . $path, Cfg::FILE_SYSTEM_USERNAME);
                chgrp($this->pathImage . $path, Cfg::FILE_SYSTEM_USERNAME);
            }
        }

        return $path . '/' . $imageFilename . $postfix . '.jpg';
    }


    /**
     * Загрузка картинки с удаленного ресурса
     *
     * @param string $remoteSource
     * @param string $newFileName
     * @return bool
     */
    public function uploadFromRemoteResource($remoteSource, $newFileName)
    {
        $pathNewFile = $this->pathImage . $this->renderRelativeImagePath('orig', $newFileName);
        if (file_exists($pathNewFile) && is_array(getimagesize($pathNewFile))) {
            return true;
        }
        if (!is_array(getimagesize($remoteSource))) {
            return false;
        }
        return copy($remoteSource, $pathNewFile);
    }


    /**
     * Загрузка картинки методом POST
     *
     * @param string $fileTmpName
     * @param string $newFileName
     * @return bool
     */
    public function uploadFromPost($fileTmpName, $newFileName)
    {
        if (!is_uploaded_file($fileTmpName)) {
            return false;
        }
        $pathNewFile = $this->pathImage . $this->renderRelativeImagePath('orig', $newFileName);
        if (!move_uploaded_file($fileTmpName, $pathNewFile)) {
            return false;
        }

        return true;
    }


    /**
     * Получение файла - уменьшенного изображения
     *
     * @param string $pictureId
     * @param int $width
     * @param int $height
     * @param string $imageType
     * @param bool $flagStamp
     * @return bool|string
     */
    public function getImagePath($pictureId, $width, $height, $imageType = 'crop', $flagStamp = false)
    {
        $pictureId = trim($pictureId);
        $width = intval($width);
        $height = intval($height);

        if (!strlen($pictureId) || !$width || !$height) {
            return false;
        }

        $picturePath = $this->renderRelativeImagePath($imageType, $pictureId, $width, $height, $flagStamp);

        if (!file_exists($this->pathImage . $picturePath)) {
            $this->createResizeImage($pictureId, $width, $height, ($imageType == 'crop'), $flagStamp);
        }

        return $this->urlImage . $picturePath;
    }


    /**
     * Готовит уменьшенное изображение из оригинального
     *
     * @param string $pictureId
     * @param int $resizeToWidth
     * @param int $resizeToHeight
     * @param bool $flagCrop
     * @param bool $flagStamp
     * @return bool|string
     */
    private function createResizeImage($pictureId, $resizeToWidth, $resizeToHeight, $flagCrop, $flagStamp = false)
    {
        $pathSrc = $this->pathImage . $this->renderRelativeImagePath('orig', $pictureId);
        $pathDsc = $this->pathImage . $this->renderRelativeImagePath(($flagCrop ? 'crop' : 'scale'), $pictureId, $resizeToWidth, $resizeToHeight, $flagStamp);

        $image = false;
        if (exif_imagetype($pathSrc) == IMAGETYPE_JPEG) {
            $image = imagecreatefromjpeg($pathSrc);
        } elseif (exif_imagetype($pathSrc) == IMAGETYPE_PNG) {
            $image = imagecreatefrompng($pathSrc);
        } elseif (exif_imagetype($pathSrc) == IMAGETYPE_GIF) {
            $image = imagecreatefromgif($pathSrc);
        }
        if ($image === false) {
            return false;
        }
        list($width, $height) = getimagesize($pathSrc);

        if ($resizeToWidth >= $width) { // если нужная ширина меньше исходной
            $resizeToWidth = $width; // оставим её как есть
        }
        if ($resizeToHeight >= $height) { // если нужная высота меньше исходной
            $resizeToHeight = $height; // оставим её как есть
        }

        $resizeWidthRatio = $width / $resizeToWidth;
        $resizeHeightRatio = $height / $resizeToHeight;
        if ($flagCrop) {
            $resizeRatio = min($resizeHeightRatio, $resizeWidthRatio);
            $resizeX = ($width / 2) - ($resizeToWidth / 2) * $resizeRatio;
            $resizeY = ($height / 2) - ($resizeToHeight / 2) * $resizeRatio;
            $cropWidth = $width - 2 * $resizeX;
            $cropHeight = $height - 2 * $resizeY;
        } else {
            $resizeRatio = max($resizeHeightRatio, $resizeWidthRatio);
            $resizeToWidth = round(($width / $resizeRatio), 0);
            $resizeToHeight = round(($height / $resizeRatio), 0);
            $resizeX = $resizeY = 0;
            $cropWidth = $width;
            $cropHeight = $height;
        }
        if (!$imageNew = imagecreatetruecolor($resizeToWidth, $resizeToHeight)) {
            return false;
        }
        if (!imagecopyresampled($imageNew, $image, 0, 0, $resizeX, $resizeY, $resizeToWidth, $resizeToHeight, $cropWidth, $cropHeight)) {
            return false;
        }

        if ($flagStamp) {
            $this->putWatermark($imageNew, $resizeToWidth);
        }

        if (!imagejpeg($imageNew, $pathDsc, $this->quality)) {
            return false;
        }

        return $pathDsc;
    }


    /**
     * Определение индекса светимости пиксела
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @return int
     */
    private function pixelLuminosity(&$image, $x, $y) {
        $rgb = imagecolorat($image, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $luminosity = ceil(($r + $g + $b) / 3);
        return $luminosity;
    }


    /**
     * Определение индекса светимости заданной области
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     * @return int
     */
    private function areaLuminosity(&$image, $x, $y, $w, $h) {
        $luminosity = 0;
        for ($xItem = $x; $xItem <= ($x + $w); $xItem++) {
            for ($yItem = $y; $yItem <= ($y + $h); $yItem++) {
                $luminosity += $this->pixelLuminosity($image, $xItem, $yItem);
            }
        }
        return ($luminosity / ($w * $h));
    }


    /**
     * Наложение водяного знака на изображение
     *
     * @param resource $image
     * @param integer $imageWidth
     * @param int $regionId
     * @return int
     */
    private function putWatermark(&$image, $imageWidth, $regionId = 66)
    {
        if (!is_resource($image)) {
            return false;
        }

        $imageWidth = intval($imageWidth);

        // Разрешенные параметры региона
        $regionAllow = array(66, 74);
        if (!in_array($regionId, $regionAllow)) {
            $regionId = 66;
        }

        // Параметры логотипа в зависимости от региона и размера
        // 'area' - расположение слова "example" на логотипе (отступ слева, отступ сверху, ширина, высота)
        $watermarks = array(
            66 => array(
                'light' => '259x101_wh.png',
                'dark' => '259x101_bl.png',
                'width' => 259,
                'height' => 101,
                'area' => array('x'=>10, 'y'=>20, 'w'=>190, 'h'=>30)
            ),
            74 => array(
                'light' => '259x101_wh_ch.png',
                'dark' => '259x101_bl_ch.png',
                'width' => 259,
                'height' => 101,
                'area' => array('x'=>10, 'y'=>25, 'w'=>190, 'h'=>30)
            )
        );

        // Коэффициент размеров картинки и штампа
        $ratio = $imageWidth / 800;

        $watermarkData = $watermarks[$regionId];

        $width = round($watermarkData['width'] * $ratio);
        $height = round($watermarkData['height'] * $ratio);
        $areaX = round($watermarkData['area']['x'] * $ratio);
        $areaY = round($watermarkData['area']['y'] * $ratio);
        $areaW = round($watermarkData['area']['w'] * $ratio);
        $areaH = round($watermarkData['area']['h'] * $ratio);
        $luminosity = $this->areaLuminosity($image, $areaX, $areaY, $areaW, $areaH);

        if ($luminosity > 100) {
            $watermarkSource = $this->pathWatermark . $watermarkData['dark'];
        } else {
            $watermarkSource = $this->pathWatermark . $watermarkData['light'];
        }

        $stamp = imagecreatefrompng($watermarkSource);

        $stampResize = imagecreatetruecolor($width, $height);
        imagealphablending($stampResize, false);
        imagesavealpha($stampResize, true);
        imagecopyresampled($stampResize, $stamp, 0, 0, 0, 0, $width, $height, $watermarkData['width'], $watermarkData['height']);

        imagecopy($image, $stampResize, 0, 0, 0, 0, $width, $height);
        imagedestroy($stamp);

        return true;
    }


    /**
     * Получение файла
     *
     * @param string $fileId
     * @return bool
     */
    /*
    function get($fileId)
    {
        $result = false;
        if (preg_match('/^[a-f0-9]+_(\d+_\d+(_crop|_c|)|original)$/', $fileId)) {
            if ($file = $this->_getCacheFile($fileId)) { //если запрашивают оригинал, или нужный файл превью уже существует
                $result = $this->_returnFile($file);
            } elseif ($file = $this->_getMiniFile($fileId)) {
                $result = $this->_returnFile($file);
            }
        }
        if (!$result) {
            $this->_emptyFile();
        }
        return $result;
    }
    */


    /**
     * Проверяет наличие кэш-файла (или оригинального файла)
     *
     * @param string $fileId
     * @return string|bool
     */
    /*
    function _getCacheFile($fileId)
    {
        $original = preg_match('|original|i', $fileId);
        if ($path = $this->_getFilePath($fileId, $original)) {

            if ($original) {
                $parts = explode('_', $fileId);
                $fileId = $parts[0];
            }
            $fileId = str_replace('_crop', '_c', $fileId);
            $path .= $fileId . '.jpg';

            if (file_exists($path)) {
                return $path;
            }
        }
        return false;
    }
    */


    /**
     * Получение файла - уменьшенного изображения
     *
     * @param string $fileId
     * @return bool|string
     */
    /*
    function _getMiniFile($fileId)
    {
        $parts = explode('.', $fileId);
        list($pictureId, $width, $height, $crop) = explode('_', $parts[0]);
        if (!$pictureId || !$width || !$height) {
            return false;
        }
        $width = (int)$width;
        $height = (int)$height;
        if (!$width || !$height) {
            return false;
        }
        if (empty($crop)) {
            $crop = '';
        }

        if (!$this->_getFilePath($pictureId, true)) {
            return false;
        }
        $originalFile = $this->_getFilePath($pictureId, true) . $pictureId . '.jpg';
        if (!file_exists($originalFile)) {
            return false;
        }

        if ($file = $this->createResizeImage($originalFile, $pictureId, $width, $height, $crop ? true : false)) {
            return $file;
        }
        return false;
    }
    */


    /**
     * Выводит файл
     *
     * @param string $file
     * @return bool
     */
    /*
    function _returnFile($file)
    {
        if (!file_exists($file)) {
            $this->_setError(7);
            return false;
        }
        header('Content-Type: image/jpg');
        header("Content-Length: " . filesize($file));
        readfile($file);
        return true;
    }
    */


    /**
     * Возвращает путь к папке с файлом
     *
     * @param bool $original
     * @return string
     */
    /*
    function _getBasePath($original = false)
    {
        return $original ? $this->originalPath : $this->picturePath;
    }
    */


    /**
     * Возвращает название файла
     *
     * @param string $pictureId
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @return string
     */
    /*
    function _getFileName($pictureId, $width, $height, $crop)
    {
        return $pictureId . '_' . $width . '_' . $height . ($crop ? '_c' : '') . '.jpg';
    }
    */


    /**
     * Выдает пустой файл
     *
     * @return bool
     */
    /*
    function _emptyFile()
    {
        $im = imagecreatetruecolor(1, 1);
        $black = ImageColorAllocate($im, 0, 0, 0);
        $trans = imagecolortransparent($im, $black);
        ImageFill($im, 0, 0, $black);
        header('Content-Type: image/png');
        imagepng($im);
        return true;
    }
    */


    /**
     * Выдает путь к файлу
     *
     * @param string $fileId
     * @param bool $original
     * @param bool $make
     * @return string|bool
     */
    /*
    function _getFilePath($fileId, $original = false, $make = false)
    {
        if (empty($fileId)) {
            return false;
        }
        if (!preg_match('|^[a-f0-9]{5}|i', $fileId)) {
            $this->_setError(2);
            return false;
        }

        $path = $this->_getBasePath($original) . substr($fileId, 0, 2);
        if (!$this->_getMakeDir($path, $make)) {
            $this->_setError(6);
            return false;
        }
        $path .= '/' . substr($fileId, 2, 2);
        if (!$this->_getMakeDir($path, $make)) {
            $this->_setError(6);
            return false;
        }
        return $path . '/';
    }
    */


    /**
     * Проверяет существование каталога и создает если нужно
     *
     * @param string $path
     * @param bool $make
     * @return bool
     */
    /*
    function _getMakeDir($path, $make = false) {
        if (file_exists($path)) {
            if (!is_dir($path) || !is_writable($path) || !is_readable($path)) {
                return false;
            }
        } elseif ($make !== true) {
            return false;
        } elseif ($make == true) {
            if (!mkdir($path)) {
                return false;
            }
        }
        return true;
    }
    */


    /**
     * Добавляет сообщение об ошибке
     *
     * @param string $error
     * @return bool
     */
    /*
    function _setError($error)
    {
        $this->errors[] = $error;
        return true;
    }
    */


    /**
     * Вывод сообщений об ошибках
     *
     * @return string
     */
    /*
    function getErrors($separator)
    {
        return join($separator, $this->errors);
    }
    */
}
