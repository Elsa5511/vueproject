<?php

return[
    'default_country_id' => env('DEFAULT_COUNTRY_ID', 1),
    'maps_tiles' => 'https://mt0.google.com/vt/lyrs=m&hl=es&x={x}&y={y}&z={z}&s=Ga',
    'maps_max_zoom' => 20,
    'price_options' => [
        1, 2, 3, 4, 5, 6, 12, 24
    ],
    'currencies' => [
        'USD' => [
            'symbol' => 'USD',
            'name' => 'Dolares',
            'provider_available' => true,
            'filter_min' => '50',
            'filter_max' => '350',
        ],
        'DOP' => [
            'symbol' => 'DOP',
            'name' => 'Pesos Dominicanos',
            'provider_available' => false,
            'filter_min' => '2500',
            'filter_max' => '17500',
        ]
    ],
    'hair_colors' => [
        'BLACK' => 'Negro',
        'BLOND' => 'Rubio',
        'BROWN' => 'Castaño',
        'MAHOGANY' => 'Caoba',
        'ASHEN' => 'Cenizo',
    ],
    'skin_colors' => [
        'INDIAN' => 'Indio',
        'BLACK' => 'Negro',
        'WHITE' => 'Blanco',
    ],
    'corporal_complexions' => [
        'NORMAL' => 'Normal',
        'ATLETIC' => 'Atlético',
        'SKINY' => 'Delgado',
        'VOLUPTUOUS' => 'Voluptuoso',
        'CHUBBY' => 'Llenita',
    ],
    'eyes_colors' => [
        'BROWN' => 'Marrones',
        'BLACK' => 'Negros',
        'GREEN' => 'Verdes',
        'BLUE' => 'Azules',
        'OTHER' => 'Otro',
    ],
    'genders' => [
        'FEMALE' => 'Femenino',
        'MALE' => 'Masculino',
        'TRANSEXUAL' => 'Transexual',
    ],
    'sexual_orientations' => [
        'HETEROSEXUAL' => 'Heterosexual',
        'HOMOSEXUAL' => 'Homosexual',
        'BI-SEXUAL' => 'Bi-Sexual'
    ],
    'position_traker' => [
        'default_interval' => 6000, //10 minutes
        'eager_interval' => 20, //1 minute
        'distance_filter' => 0, //distance filter
    ],
    'min_reservation_hours' => 1,
    'reservation_interval' => 30,
        /* 'hours_intervals' => [
          '00:00' => '00:00 AM',
          '00:30' => '00:30 AM',
          '01:00' => '01:00 AM',
          '01:30' => '01:30 AM',
          '02:00' => '02:00 AM',
          '02:30' => '02:30 AM',
          '03:00' => '03:00 AM',
          '03:30' => '03:30 AM',
          '04:00' => '04:00 AM',
          '04:30' => '04:30 AM',
          '05:00' => '05:00 AM',
          '05:30' => '05:30 AM',
          '06:00', '06:30',
          '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
          '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30',
          '21:00', '21:30', '22:00', '22:30', '23:00', '23:30'
          ], */
];
