<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;

class ModuleInfoFeature extends Feature
{
    public function handle()
    {
        return new JsonResource([
            'seoKeywordsEnabled' => TagerPagesConfig::isSeoKeywordsEnabled(),
            'fileScenarios' => [
                'image' => TagerPagesConfig::getPageImageScenario(),
                'content' => TagerPagesConfig::getContentImageScenario(),
                'openGraph' => TagerPagesConfig::getOpenGraphScenario(),
            ],
        ]);
    }
}
