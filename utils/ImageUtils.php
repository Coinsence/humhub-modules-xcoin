<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 30‏/7‏/2021, Fri
 **/

namespace humhub\modules\xcoin\utils;


use humhub\modules\file\libs\ImageConverter;
use humhub\modules\file\models\File;
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

        $path .= '' . $prefix .'.jgp';

        return $path;
    }

    static function checkImageSize($files)
    {
        $valid = true;

        if (!$files) {
            return $valid;
        }
        if (is_string($files)) {
            $files = array_map('trim', explode(',', $files));
        } elseif ($files instanceof File) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if (is_string($file) && $file != '') {
                $file = File::findOne(['guid' => $file]);
            }

            if ($file === null || !$file instanceof File) {
                continue;
            }

            if($file->size/1024 >100){
                $valid = false;
                return $valid;
            }
        }
        return $valid;
    }
}
