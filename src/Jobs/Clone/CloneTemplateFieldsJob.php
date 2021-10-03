<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs\Clone;

use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class CloneTemplateFieldsJob extends Job
{
    protected TagerPage $oldPage;

    protected TagerPage $newPage;

    public function __construct(TagerPage $oldPage, TagerPage $newPage)
    {
        $this->oldPage = $oldPage;

        $this->newPage = $newPage;
    }

    public function handle(PagesRepository $pagesRepository, Storage $storage)
    {
        return $this->newPage;
    }
}
