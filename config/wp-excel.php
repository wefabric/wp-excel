<?php

return [
    /**
     * https://docs.laravel-excel.com/3.0/exports/
     * Register the post types which uses the Excel export.
     * For example
     * 1. run: php console make:export PostsExport
     * 2. Add the class below
     */
    'post_types' => [
//        'post' => \App\Exports\PostsExport::class,
    ],
    /**
     * https://docs.laravel-excel.com/3.0/exports/export-formats.html
     */
    'default_format' => 'xlsx',

    /**
     * The storage disk which will be used to save the files before downloading.
     * It needs to be accessed public to successfully download
     */
    'storage' => 'public',

    /**
     * The title to use in the WordPress backend bulk action select
     */
    'export_title' => 'Exporteer naar excel'
];
