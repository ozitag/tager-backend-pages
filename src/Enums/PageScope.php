<?php

namespace OZiTAG\Tager\Backend\Pages\Enums;

enum PageScope: string
{
    case View = 'pages.view';
    case Create = 'pages.create';
    case Edit = 'pages.edit';
    case Delete = 'pages.delete';
}
