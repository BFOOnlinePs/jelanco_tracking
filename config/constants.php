<?php

return [
    // socket url
    'socket_io_url' => 'http://192.168.1.21:3000', // http://chic.ps


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

];
