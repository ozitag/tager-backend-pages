<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Queue\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class GetTemplateByAliasJob
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function handle()
    {
        $config = TagerPagesConfig::getTemplatesConfig();

        if (!isset($config[$this->alias])) {
            return null;
        }

        return $config[$this->alias];
    }
}
