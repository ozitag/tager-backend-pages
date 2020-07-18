<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class TemplatesFeature extends Feature
{
    public function handle()
    {
        $templates = TagerPagesConfig::getTemplatesConfig();

        $result = [];

        foreach ($templates as $templateId => $templateConfig) {
            $result[] = [
                'id' => $templateId,
                'label' => $templateConfig['label'] ?? 'Template "' . $templateId . '"'
            ];
        }

        return new JsonResource($result);
    }
}
