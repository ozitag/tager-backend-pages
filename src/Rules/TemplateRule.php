<?php

namespace OZiTAG\Tager\Backend\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class TemplateRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $template = TagerPagesConfig::getTemplateConfig($value);
        if (!$template) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Template not found';
    }
}
