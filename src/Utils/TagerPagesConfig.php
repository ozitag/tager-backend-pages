<?php

namespace OZiTAG\Tager\Backend\Pages\Utils;

class TagerPagesConfig
{
    private static function config($param = null, $default = null)
    {
        return \config('tager-pages' . (empty($param) ? '' : '.' . $param), $default);
    }

    private static function getStorageScenario($id)
    {
        return self::config('file_storage_scenarios.' . $id);
    }

    public static function getContentImageScenario()
    {
        return self::getStorageScenario('content');
    }

    public static function getPageImageScenario()
    {
        return self::getStorageScenario('cover');
    }

    public static function getOpenGraphScenario()
    {
        return self::getStorageScenario('openGraph');
    }

    public static function getTemplatesConfig()
    {
        return self::config('templates', []);
    }

    public static function isSeoKeywordsEnabled(): bool
    {
        return (bool)self::config('seoKeywordsEnabled', false);
    }
}
