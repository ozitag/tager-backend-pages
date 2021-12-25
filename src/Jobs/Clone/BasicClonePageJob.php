<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs\Clone;

use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class BasicClonePageJob extends Job
{
    protected TagerPage $page;

    protected string $urlPath;

    public function __construct(TagerPage $page, string $urlPath)
    {
        $this->page = $page;

        $this->urlPath = $urlPath;
    }

    public function handle(Storage $storage)
    {
        $newPage = new TagerPage();

        $newPage->title = $this->page->title . ' (Copy)';
        $newPage->template = $this->page->template;
        $newPage->status = $this->page->status;
        $newPage->parent_id = $this->page->parent_id;
        $newPage->url_path = $this->urlPath;
        $newPage->excerpt = $this->page->excerpt;
        $newPage->body = $this->page->body;
        $newPage->datetime = $this->page->datetime;
        $newPage->page_title = $this->page->page_title;
        $newPage->page_description = $this->page->page_description;
        $newPage->page_keywords = $this->page->page_keywords;

        if ($this->page->image_id) {
            $newPage->image_id = $storage->clone($this->page->image_id);
        }

        if ($this->page->open_graph_image_id) {
            $newPage->open_graph_image_id = $storage->clone($this->page->open_graph_image_id);
        }

        $newPage->save();

        return $newPage;
    }
}
