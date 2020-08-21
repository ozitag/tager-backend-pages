<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;

class ViewTemplateFeature extends Feature
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function handle()
    {
        $template = TagerPagesTemplates::get($this->alias);
        if (!$template) {
            abort('404', 'Template not found');
        }

        return new JsonResource($template->getFullJson());
    }
}
