<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'elasticsearch:9200'),
    'index' => env('ELASTICSEARCH_INDEX', 'blog_posts'),
    'username' => env('ELASTICSEARCH_USERNAME', 'elastic'),
    'password' => env('ELASTICSEARCH_PASSWORD', 'changeme'),
];
