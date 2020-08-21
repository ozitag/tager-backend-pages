<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;

class TemplatesFeature extends Feature
{
    public function handle()
    {
        $templates = TagerPagesTemplates::all();

        $result = [];
        foreach ($templates as $template) {
            $result[] = $template->getJson();
        }

        return new JsonResource($result);
    }
}
