<?php

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'My_ApiKey',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AAAArvciupc:APA91bGgUBZinfq-gzwxgkIHa4v4WbTKRb8nxthb9dh-T2pH_lmwrsh6fRglb3iD5_6F_sZX6H2TBCMDQhQQFlOEibtX0KDz_MIBcslprJcEMsBbFjSBM2Wwno0eh2IrkaLzCmw2VmHs',
        'messagingSenderId' => '751470557847',
        'projectId'=> "vaceatecom-1533615062029",
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
        'passPhrase' => '1234', //Optional
        'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run' => true
    ]
];
