<?php

return [
    'date_format'         => 'm/d/Y',
    'time_format'         => 'H:i:s',
    'primary_language'    => 'en',
    'available_languages' => [
        'en' => 'English',
    ],
    'pdf_intervals_enabled' => env('PDF_INTERVALS_ENABLED', false)
];
