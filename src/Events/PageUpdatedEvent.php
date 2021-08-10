<?php

namespace OZiTAG\Tager\Backend\Events;

use Illuminate\Support\Facades\App;
use OZiTAG\Tager\Backend\Crud\Events\UpdateModelEvent;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class PageUpdatedEvent extends UpdateModelEvent
{
    public function getPage(): TagerPage
    {
        $repository = App::make(PagesRepository::class);

        return $repository->find($this->getModelId());
    }
}