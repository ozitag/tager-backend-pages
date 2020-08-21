<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Blog\Jobs\GetPriorityForNewCategoryJob;
use OZiTAG\Tager\Backend\Blog\Utils\TagerBlogConfig;
use OZiTAG\Tager\Backend\Blog\Utils\TagerBlogUrlHelper;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Blog\Jobs\GetCategoryUrlAliasJob;
use OZiTAG\Tager\Backend\Blog\Repositories\CategoryRepository;
use OZiTAG\Tager\Backend\Blog\Resources\Admin\AdminCategoryResource;
use OZiTAG\Tager\Backend\Blog\Requests\CreateBlogCategoryRequest;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class ModuleInfoFeature extends Feature
{
    public function handle()
    {
        return new JsonResource([
            'contentImageScenario' => TagerPagesConfig::getContentImageScenario(),
        ]);
    }
}
