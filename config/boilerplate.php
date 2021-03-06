<?php

return [

    'billables' => [

        'user' => [
            'trial_days' => 7,
        
            'plans' => [
                [
                    'name' => 'Hobby',
                    'short_description' => 'The hobby plan is a great starting point for launching your next application.',
                    'monthly_id' => 7603,
                    'yearly_id' => 7605,
                    'features' => [
                        'Single Server',
                        '50 Deployments',
                        'Email Support',
                    ],
                    'archived' => false,
                ],

                [
                    'name' => 'Growth',
                    'short_description' => 'Take your application to the next level with our growth plan.',
                    'monthly_id' => 7604,
                    'yearly_id' => 7606,
                    'features' => [
                        'Unlimited Server',
                        '500 Deployments',
                        'Priority Support',
                    ],
                    'archived' => false,
                ],
            ]
        ]

    ]
    
];