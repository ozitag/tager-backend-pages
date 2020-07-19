<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Queue\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Exceptions\InvalidUrlPathException;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class SetPageTemplateJob
{
    /** @var TagerPage */
    private $model;

    private $template;

    private $fields;

    public function __construct($model, $template, $fields)
    {
        $this->model = $model;
        $this->template = $template;
        $this->fields = $fields;
    }

    public function handle()
    {
        $template = TagerPagesConfig::getTemplateConfig($this->template);
        if (!$template) {
            return $this->model;
        }

        $this->model->template = $this->template;
        $this->model->save();

        return $this->model;
    }
}
