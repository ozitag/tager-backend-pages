<?php

use OZiTAG\Tager\Backend\Fields\Enums\FieldType;

return [
    'file_storage_scenarios' => [
        'cover' => '',
        'content' => '',
        'openGraph' => ''
    ],
    
    'seoKeywordsEnabled' => false,
    
    'templates' => [
        'home' => [
            'label' => 'Home Page',
            'fields' => [
                'title' => [
                    'type' => FieldType::String,
                    'labeel' => 'Head Title'
                ]
            ]
        ]
    ]
];
