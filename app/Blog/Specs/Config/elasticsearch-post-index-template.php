<?php

return [
    'body' => [
        'mappings' => [
            'properties' => [
                'id' => ['type' => 'integer'],
                'name' => [
                    'type' => 'text',
                    'analyzer' => 'standard',
                    'fields' => [
                        'keyword' => ['type' => 'keyword']
                    ]
                ],
                'slug' => ['type' => 'keyword'],
                'description' => [
                    'type' => 'text',
                    'analyzer' => 'standard'
                ],
                'category_id' => ['type' => 'integer'],
                'category_name' => [
                    'type' => 'text',
                    'fields' => [
                        'keyword' => ['type' => 'keyword']
                    ]
                ],
                'user_id' => ['type' => 'integer'],
                'user_name' => [
                    'type' => 'text',
                    'fields' => [
                        'keyword' => ['type' => 'keyword']
                    ]
                ],
                'is_published' => ['type' => 'boolean'],
                'image' => ['type' => 'keyword'],
                'created_at' => ['type' => 'date'],
                'updated_at' => ['type' => 'date'],
            ]
        ],
        'settings' => [
            'number_of_shards' => 1,
            'number_of_replicas' => 0,
            'analysis' => [
                'analyzer' => [
                    'standard' => [
                        'type' => 'standard',
                        'stopwords' => '_english_'
                    ]
                ]
            ]
        ]
    ]
];
