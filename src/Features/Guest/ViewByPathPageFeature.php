<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Guest;

use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageByUrlPathJob;
use OZiTAG\Tager\Backend\Pages\Resources\PageFullResource;

class ViewByPathPageFeature extends Feature
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle()
    {
        $model = $this->run(GetPageByUrlPathJob::class, [
            'path' => $this->path
        ]);

        return new PageFullResource($model);
    }
}
