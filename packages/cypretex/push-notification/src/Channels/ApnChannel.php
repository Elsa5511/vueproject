<?php

namespace Cypretex\PushNotification\Channels;

use Cypretex\PushNotification\Messages\PushMessage;

class ApnChannel extends PushChannel
{
    /**
     * {@inheritdoc}
     */
    protected function pushServiceName()
    {
        return 'apn';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildData(PushMessage $message)
    {
        $data = [
            'aps' => [
                'alert' => [
                    'title' => $message->title,
                    'body' => $message->body,
                ],
                'sound' => $message->sound,
            ],
        ];

        if (! empty($message->extra)) {
            $data['extraPayLoad'] = $message->extra;
        }

        if (is_numeric($message->badge)) {
            $data['aps']['badge'] = $message->badge;
        }

        return $data;
    }
}
