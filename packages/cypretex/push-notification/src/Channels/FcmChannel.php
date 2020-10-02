<?php

namespace Cypretex\PushNotification\Channels;

class FcmChannel extends GcmChannel {

    /**
     * {@inheritdoc}
     */
    protected function pushServiceName() {
        return 'fcm';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildData(\Cypretex\PushNotification\Messages\PushMessage $message) {
        if ($message->title || $message->body) {
            $data = [
                'notification' => [
                    'title' => $message->title,
                    'body' => $message->body,
                    'sound' => $message->sound,
                ],
            ];
        } else {
            $data = [];
        }

        if (!empty($message->extra)) {
            $data['data'] = $message->extra;
        }

        return $data;
    }

}
