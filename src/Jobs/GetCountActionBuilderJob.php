<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Http\Request;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class GetCountActionBuilderJob extends Job
{
    public function handle(Request $request, PagesRepository $repository)
    {
        $result = $repository->builder();

        if ($request->has('template')) {
            $result->where('template', $request->query('template'));
        }

        return $result;
    }
}
