<?php

namespace OZiTAG\Tager\Backend\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class UrlPathRule implements Rule
{
    protected ?int $id = null;

    protected ?string $value = null;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;
        $path = preg_replace('#\/+$#si', '', $value);
        if (empty($path)) {
            $path = '/';
        }

        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }

        /** @var PagesRepository $repository */
        $repository = App::make(PagesRepository::class);

        $existedPage = $repository->findByUrlPath($path);

        if ($this->id === null) {
            return $existedPage === null;
        } else {
            return $existedPage === null || $existedPage->id == $this->id;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('tager-pages::errors.url_busy', ['url_path' => $this->value]);
    }
}
