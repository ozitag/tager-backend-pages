<?php

namespace OZiTAG\Tager\Backend\Pages;

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

    public static function getPageImageScenario()
    {
        return self::getStorageScenario('page_image');
    }

    public static function getOpenGraphScenario()
    {
        return self::getStorageScenario('open_graph');
    }

    public static function getTemplatesConfig()
    {
        return self::config('templates', []);
    }

    public static function getTemplateConfig($template)
    {
        $items = self::config('templates', []);
        return isset($items[$template]) ? $items[$template] : null;
    }

    public static function getField($template, $field)
    {
        $templateModel = self::getTemplateConfig($template);
        if (!$templateModel || !isset($templateModel['fields'][$field])) {
            return null;
        }

        return $templateModel['fields'][$field];
    }
}
