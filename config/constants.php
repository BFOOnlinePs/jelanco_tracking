<?php

return [
    // socket url used for real time comments
    'socket_io_url' => env('SOCKET_IO_URL'),

    // extensions
    'thumbnail_extension' => 'jpg',

    // storage paths
    'tasks_attachments_path' => 'tasks_attachments/',
    'comments_attachments_path' => 'comments_attachments/',
    'submissions_attachments_path' => 'uploads/',
    'thumbnail_path' => 'thumbnails/',
    'profile_images_path' => 'profile_images/',


    // notification constants
    'notification' => [
        'type' => 'type',
        'type_id' => 'type_id',
    ],


    // notification type / screens
    'notification_type' => [
        'task' => 'task',
        'submission' => 'submission',
        'comment' => 'comment',

    ],

   
    'app_storage_path' => 'app/public/',
    'thumbnail_storage_path' => 'app/public/thumbnails/',

    // host
    // 'app_storage_path' => 'app/',
    // 'thumbnail_storage_path' => 'app/thumbnails/',

];
