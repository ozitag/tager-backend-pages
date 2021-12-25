<?php

namespace OZiTAG\Tager\Backend\Pages\Utils;

use OZiTAG\Tager\Backend\Pages\Structures\Template;

class TagerPagesTemplates
{
    static $loaded = false;

    static $templates = [];

    private static function load()
    {
        self::$templates = [];

        $templatesConfig = TagerPagesConfig::getTemplatesConfig();
        foreach ($templatesConfig as $id => $template) {
            if ($template instanceof Template) {
                $model = $template;
            } else if (is_array($template)) {
                if (!isset($template['label']) || empty($template['label'])) {
                    continue;
                }

                $model = new Template($template['label'], $template['fields'] ?? []);
            } else if (is_string($template)) {
                $model = new $template;
            }

            if (!$model instanceof Template) {
                continue;
            }

            $model->setId($id);

            self::$templates[] = $model;
        }

        self::$loaded = true;
    }

    /**
     * @return Template[]
     */
    public static function all()
    {
        if (!self::$loaded) {
            self::load();
        }

        return self::$templates;
    }

    /**
     * @param $id
     * @return Template|null
     */
    public static function get($id)
    {
        if (!self::$loaded) {
            self::load();
        }

        foreach (self::$templates as $template) {
            if ($template->getId() == $id) {
                return $template;
            }
        }

        return null;
    }
}
