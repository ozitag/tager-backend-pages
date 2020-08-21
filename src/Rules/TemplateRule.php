<?php

namespace OZiTAG\Tager\Backend\Pages\Rules;

use Illuminate\Contracts\Validation\Rule;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;

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
        $tagerTemplates = new TagerPagesTemplates();
        $template = $tagerTemplates->get($value);
        
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
