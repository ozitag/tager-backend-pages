<?php

namespace OZiTAG\Tager\Backend\Pages\Enums;

use OZiTAG\Tager\Backend\Core\Enums\Enum;

final class PageScope extends Enum
{
    const View = 'pages.view';
    const Create = 'pages.create';
    const Edit = 'pages.edit';
    const Delete = 'pages.delete';
}
