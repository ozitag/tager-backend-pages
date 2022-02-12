<?php

namespace OZiTAG\Tager\Backend\Pages\Utils;

class TagerPagesConfig
{
    private static function config($param = null, $default = null)
    {
        return \config('tager-pages' . (empty($param) ? '' : '.' . $param), $default);
    }

    private static function getStorageScenario($id): ?string
    {
        $result = self::config('file_storage_scenarios.' . $id);

        if ($result instanceof \BackedEnum) {
            return $result->value;
        } else {
            return $result;
        }
    }

    public static function getContentImageScenario(): ?string
    {
        return self::getStorageScenario('content');
    }

    public static function getPageImageScenario(): ?string
    {
        return self::getStorageScenario('cover');
    }

    public static function getOpenGraphScenario(): ?string
    {
        return self::getStorageScenario('openGraph');
    }

    public static function getTemplatesConfig(): array
    {
        return self::config('templates', []);
    }

    public static function isSeoKeywordsEnabled(): bool
    {
        return (bool)self::config('seoKeywordsEnabled', false);
    }
}
