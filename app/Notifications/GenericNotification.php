<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GenericNotification extends Notification implements ShouldQueue, ShouldBroadcast {

    use Queueable;

    private $user = null;
    private $data = null;
    private $channels = ['broadcast'];
    private $type = 'NOTIFICATION';

    /**
     * 
     * @param type $user The user to notify (used in broadcastON)
     * @param \App\Models\Notification $data
     * @param type $type
     * @param type $channels
     */
    public function __construct($user, \App\Models\Notification $data, $type = 'NOTIFICATION', $channels = ['database', 'broadcast', 'fcm']) {
        $this->user = $user;
        $this->data = $data;
        $index = array_search('fcm', $channels);
        if ($index !== false) {
            unset($channels[$index]);
            $channels[] = \Cypretex\PushNotification\Channels\FcmChannel::class;
        }
        $this->channels = $channels;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        if ($notifiable->show_notifications) {
            return $this->channels;
        }
        return ['broadcast'];
    }

    public function toFcm($notifiable) {
        $icon = asset('/img/icon-inverted.png');
        if ($this->data->getSenderId() && !$this->data->getIcon()) {
            $sender = \App\Models\User::find($this->data->getSenderId());
            $icon = $sender->medium_avatar_url;
        } else {
            $icon = $this->data->getIcon();
        }


        $message = new \Cypretex\PushNotification\Messages\PushMessage();

        if ($this->data->getDestType() === 'mobile') {
            $message->title = $this->data->getTitle();
            $message->body = $this->data->getMessage();
            $message->sound = 'default';
            $message->extra = [
                'action' => $this->data->getAction(),
            ];
            return $message;
        }

        /* $message->title = $this->data->getTitle();
          $message->body = $this->data->getMessage();
          $message->sound = $this->data->getSound(true); */
        $message->extra([
            //notification to browser
            'notification' => [
                'title' => $this->data->getTitle(),
                'icon' => $icon,
                //'image' => $icon,
                'body' => $this->data->getMessage(),
                'sound' => true,
                'attributes' => $this->data->getAttributes(),
                'type' => $this->data->getType(),
                'data' => [
                    'action' => $this->data->getAction(),
                ]
            ],
        ]);
        return $message;
    }

    /*
      public function toBroadcast($notifiable) {
      return (new BroadcastMessage(['notifiable' => $notifiable, 'payload' => $this->toArray($notifiable)]))->onQueue('broadcasts');
      } */

    public function toBroadcast($notifiable) {
        return new BroadcastMessage(['payload' => $this->toArray($notifiable)]);
    }

    public function broadcastType() {
        return $this->type;
    }

    public function broadcastOn() {
        return new \Illuminate\Broadcasting\PrivateChannel("notifications." . $this->user->id);
        //return ["notifications." . $this->user->id];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        return (new MailMessage)
                        ->greeting($this->data->getTitle())
                        ->level($this->data->getType())
                        ->subject($this->data->getTitle())
                        ->line($this->data->getMessage())
                        ->action('Más detalles', route('notifications.read', ['id' => $this->id]))
                        ->line('Gracias por usar mercadobit!');
    }

    function getUser() {
        return $this->user;
    }

    function getData() {
        return $this->data;
    }

    function getChannels() {
        return $this->channels;
    }

    function getType() {
        return $this->type;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setChannels($channels) {
        $this->channels = $channels;
    }

    function setType($type) {
        $this->type = $type;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return $this->data->toArray();
    }

}
