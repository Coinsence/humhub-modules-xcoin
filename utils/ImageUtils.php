<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 30‏/7‏/2021, Fri
 **/

namespace humhub\modules\xcoin\utils;


use humhub\modules\file\libs\ImageConverter;
use Yii;
use yii\helpers\FileHelper;

class ImageUtils
{
    static private function compress($sourceFile,$targetFile)
    {
        ImageConverter::TransformToJpeg($sourceFile, $targetFile);

    }

    static function resizeImage($sourceFile, $type, $width, $height, $name)
    {
        $targetPath = self::getPath($name, $type);
        if (!file_exists($targetPath)) {
            ImageConverter::Resize($sourceFile, $targetPath, ['width' => $width, 'height' => $height]);
        }
        return $targetPath;
    }

    static private function getPath($prefix = '', $folderImage)
    {
        $path = Yii::getAlias('@webroot/uploads/' . $folderImage . '/');
        FileHelper::createDirectory($path);

        $path .= '';
        $path .= $prefix;
        $path .= '.jpg';

        return $path;
    }
}
