<?php

namespace App\Http\Controllers\Api\Chat;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Cypretex\Chat\Facades\ChatFacade as Chat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ConversationsController extends BaseController
{

    public static $_MESSAGESPERPAGE = 50;
    private static $autoMessages = [];

    public function __construct()
    {
        self::$autoMessages = Cache::rememberForever('automessages', function () {
            return \App\Models\AutoMessage::all();
        });
    }

    public function getUserConversations(Request $request, $userId)
    {
        $user = null;
        if ($this->userHasRole('ADMIN')) {
            $user = User::findOrFail($userId);
        } else {
            $user = $this->getUser();
            if ($user->id != $userId) {
                abort(403);
            }
        }
        $conversations = Chat::conversations()->for($user)->limit(200)->page(1)->get();
        $conversationsToSend = [];
        foreach ($conversations as $conversation) {
            $messages = Chat::conversations()->for($user)->limit(25)->page(1)->get();
            if ($conversation->last_message && count($messages)) {
                $conversation->unread_messages_count = Chat::conversation($conversation)->for($user)->unreadCount();
                $conversation->users;
                $add = true;
                foreach ($conversation->users as $cUser) {
                    if ($this->isUserInBlockedZone($cUser)) {
                        $add = false;
                    }
                }
                if ($add) {
                    $conversationsToSend[] = $conversation;
                }
            }
        }
        $response = $this->getResponseInstance();
        $response->setPayload(['data' => $conversationsToSend]);
        return $this->renderResponse();
    }

    public function delete(Request $request, $id)
    {
        $conversation = Chat::conversations()->getById($id);
        if (!$conversation) {
            abort(404);
        }

        if (!$this->userCanViewConversation($conversation, $this->getUser())) {
            abort(404);
        }

        if ($this->getUser()->role === 'ADMIN') {
            \Cypretex\Chat\Models\Conversation::findOrFail($id)->delete();
        } else {
            Chat::conversation($conversation)->for($this->getUser())->clear();
        }



        $response = $this->getResponseInstance();
        $response->setStatusMessage('Se ha eliminado la conversación correctamente!');
        return $this->renderResponse();
    }

    public function getMyConversations(Request $request)
    {
        $user = $this->getUser();
        $conversationsToSend = [];
        //$conversations = $this->getUserConversations($request, $this->getUser()->id);
        $conversations = Chat::conversations()->for($user)->limit(200)->page(1)->get();
        foreach ($conversations as $conversation) {
            $messages = Chat::conversation($conversation)->for($user)->getMessages();
            if ($conversation->last_message && count($messages)) {
                $conversation->unread_messages_count = Chat::conversation($conversation)->for($this->getUser())->unreadCount();
                $conversation->users;
                $add = true;
                foreach ($conversation->users as $cUser) {
                    if ($this->isUserInBlockedZone($cUser)) {
                        $add = false;
                    }
                }
                if ($add) {
                    $conversationsToSend[] = $conversation;
                }
            }
        }
        $response = $this->getResponseInstance();
        $response->setPayload(['data' => $conversationsToSend]);
        return $this->renderResponse();
    }

    public function getById(Request $request, $id)
    {
        $conversation = Chat::conversations()->getById($id);
        if (!$conversation) {
            abort(404);
        }

        if (!$this->userCanViewConversation($conversation, $this->getUser())) {
            abort(404);
        }

        if ($this->getUser()->role == 'ADMIN') {
            $conversation->checked_at = \Carbon\Carbon::now();
            $conversation->save();
            $conversation->messages = $conversation->messages()->where('user_id', '!=', $this->getChatBot()->id)->orderBy('updated_at', 'desc')->get();
        }
        $response = $this->getResponseInstance();
        $response->setPayload($conversation);

        return $this->renderResponse();
    }

    public function getAppointment(Request $request, $chatId)
    {
        $conversation = Chat::conversations()->getById($chatId);
        if (!$conversation) {
            abort(404);
        }
        if (!$this->userCanViewConversation($conversation, $this->getUser())) {
            abort(404);
        }

        $response = $this->getResponseInstance();
        $appoinment = \App\Models\Appointment::where(['chat_id' => $chatId, 'finished' => false])->where('status_name', '!=', 'finalized')->orderBy('date_from', 'ASC')->first();
        $response->setHttpCode(200);
        $response->setPayload($appoinment);
        return $this->renderResponse();
    }

    public function initConversation(Request $request)
    {
        $toUserId = $request->get('to');
        $toUser = User::findOrFail($toUserId);
        $fromUser = $this->getUser();

        $conversation = self::getConversationBetweenUsers($fromUser, $toUser);

        $response = $this->getResponseInstance();
        $response->setPayload($conversation);


        return $this->renderResponse();
    }

    public static function getConversationBetweenUsers($user01, $user02)
    {
        $conversation = Chat::conversations()->between($user01->id, $user02->id);
        if (!$conversation) {
            DB::beginTransaction();
            try {
                $participants = [$user01->id, $user02->id];
                $conversation = Chat::createConversation($participants);
                $notification = new \App\Models\Notification('Nueva conversación!');
                $notification->setType('CHAT_CONVERSATION_CREATED');
                $notification->setSenderId($user01->id);
                $notification->addAttribute('conversation', $conversation->toArray());
                $user01->notify(new \App\Notifications\GenericNotification($user01, $notification, 'CHAT_CONVERSATION_CREATED', ['broadcast']));
                $user02->notify(new \App\Notifications\GenericNotification($user02, $notification, 'CHAT_CONVERSATION_CREATED', ['broadcast']));
                DB::commit();

                self::sendAutoMessage($conversation, 0, $user01);
                self::sendAutoMessage($conversation, 0, $user02);
            } catch (\Exception $ex) {
                DB::rollback();
                throw $ex;
            }
        }
        $conversation->users;
        return $conversation;
    }

    public function getMessages(Request $request, $conversationId)
    {
        $conversation = Chat::conversations()->getById($conversationId);
        if (!$conversation) {
            abort(404);
        }

        if (!$this->userCanViewConversation($conversation, $this->getUser())) {
            abort(404);
        }

        $messages = Chat::conversation($conversation)->for($this->getUser())->setPaginationParams([
            'page' => $page,
            'perPage' => self::$_MESSAGESPERPAGE,
            'sorting' => "desc",
            'columns' => [
                '*'
            ]
        ])->getMessages();


        $response = $this->getResponseInstance();
        $response->setPayload($messages);
        return $this->renderResponse();
    }

    public function readMessages(Request $request, $conversationId)
    {
        $conversation = Chat::conversations()->getById($conversationId);
        $page = $request->get('page', 1);
        if (!$conversation) {
            abort(404);
        }

        if (!$this->userCanViewConversation($conversation, $this->getUser())) {
            abort(404);
        }
        $messages = Chat::conversation($conversation)->for($this->getUser())->setPaginationParams([
            'page' => $page,
            'perPage' => self::$_MESSAGESPERPAGE,
            'sorting' => "desc",
            'columns' => [
                '*'
            ]
        ])->getMessages();
        Chat::conversation($conversation)->for($this->getUser())->readAll();
        $response = $this->getResponseInstance();
        $response->setPayload($messages);
        return $this->renderResponse();
    }

    public function markAsReaded(Request $request, $conversationId)
    {
        $conversation = Chat::conversations()->getById($conversationId);
        if (!$conversation) {
            abort(404);
        }

        if (!$this->userCanViewConversation($conversation, $this->getUser())) {
            abort(404);
        }
        if ($this->getUser()->role === 'ADMIN') {
            $conversation->checked_at = \Carbon\Carbon::now();
            $conversation->save();
        } else {
            Chat::conversation($conversation)->for($this->getUser())->readAll();
        }
        $response = $this->getResponseInstance();
        $response->setPayload(['success' => true]);
        return $this->renderResponse();
    }

    public function deleteMessages(Request $request, $conversationId)
    {
        $messagesIds = $request->get('messages');
        $messagesDeleted = [];
        foreach ($messagesIds as $messageId) {
            $message = Chat::message($message)->for($this->getUser());
            if ($message) {
                $messagesDeleted[] = $message->id;
                $message->delete();
            }
        }
        $response = $this->getResponseInstance();
        $response->setPayload($messagesDeleted);
        return $this->renderResponse();
    }

    public static function getChatBot()
    {
        $bot = User::where(['role' => 'CHAT_BOT'])->first();
        if (!$bot) {
            $bot = User::create([
                'name' => 'Chatbot',
                'status' => 'CANNOT_LOGIN',
                'role' => 'CHAT_BOT',
                'email' => 'chatbot@vaceate.com',
                'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                'password' => 'none',
            ]);
        }
        return $bot;
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $response = $this->getResponseInstance();
        try {
            DB::beginTransaction();
            $conversation = Chat::conversations()->getById($conversationId);
            if (!$conversation) {
                abort(404);
            }

            if (!$this->userCanViewConversation($conversation, $this->getUser())) {
                abort(404);
            }

            $fields = $request->only(['body', 'type', 'id', 'file']);
            $type = $request->get('type', 'text');
            $body = $request->get('body', null);
            $id = $request->get('id', \Ramsey\Uuid\Uuid::uuid4()->toString());

            if ($type != 'text') {
                switch ($type) {
                    case 'location':
                        $body = json_encode($body);
                        break;
                    case 'image':

                        $file = $request->file('file');

                        if (!$file) {
                            $response->setHttpCode(400);
                            $response->setStatusMessage("Cannot parse message!");
                            $response->setPayload(['id' => $request->get('id')]);
                            return $response->render();
                        }

                        $resource = new \App\Models\ChatImageMessage();
                        $resource->owner_type = \Cypretex\Chat\Models\Conversation::class;
                        $resource->owner_id = $conversation->id;
                        $resource->mime_type = $file->getMimeType();
                        $resource->size = $file->getSize() / 1024;
                        $resource->saveImage($file);
                        $resource->save();

                        $body = $resource->toJson();
                        break;
                    default:
                        $body = null;
                }
            }


            if ($body) {
                $message = new \Cypretex\Chat\Models\Message();
                $message = Chat::message($body)
                    ->id($id)
                    ->type($type)
                    //->tempId($request->get('temp_id'))
                    ->from($this->getUser())
                    ->to($conversation)
                    ->send();
                //$message->temp_id = $request->get('temp_id');
                //notify to dest
                $dests = $conversation->users;
                $notification = new \App\Models\Notification();
                $notification->setAction('/chats/' . $conversationId);
                $notification->setIcon($this->getUser()->small_avatar_url);
                $notification->setTitle(config('app.name'));
                $notification->setSound('notification');
                $notification->setMessage('Nuevo mensaje de ' . $this->getUser()->name);
                $notification->setDestType('browser');
                //$notification->setMessage();
                foreach ($dests as $dest) {
                    if ($dest->id != $this->getUser()->id) {
                        $dest->notify(new \App\Notifications\GenericNotification($this->getUser(), $notification, 'CHAT_MESSAGE_SENDED', ['fcm']));
                    }
                }

                $notification->setDestType('mobile');
                //$notification->setMessage();
                foreach ($dests as $dest) {
                    if ($dest->id != $this->getUser()->id) {
                        $dest->notify(new \App\Notifications\GenericNotification($this->getUser(), $notification, 'CHAT_MESSAGE_SENDED', ['fcm']));
                    }
                }

                $response->setPayload($message);



                DB::commit();

                event(new \App\Events\AdminEvent('CHAT_MESSAGE', $message));

                try {
                    $d = DB::table('mc_messages')
                        ->select(DB::raw('COUNT(id) as count'))
                        ->where(['conversation_id' => $conversation->id])->where('type', '!=', 'text_bot')->first();

                    $users = $conversation->users;

                    foreach ($users as $u) {
                        self::sendAutoMessage($conversation, $d->count, $u);
                    }
                } catch (\Exception $ex) { }


                //$message = new \Cypretex\Chat\Models\Message();
                /* $messageBot = Chat::message('Bot')
                  ->id(\Ramsey\Uuid\Uuid::uuid4())
                  ->type('text')
                  ->from($this->getChatBot()->id)
                  ->to($conversation)
                  ->send(); */
            } else {
                $response->setHttpCode(400);
                $response->setStatusMessage("Cannot parse message!");
                $response->setPayload(['id' => $request->get('id')]);
            }
        } catch (\Exception $ex) {
            $response->setHttpCode(500);
            $response->setStatusMessage($ex->getMessage());
            DB::rollback();
        }

        return $this->renderResponse();
    }

    private function userCanViewConversation($conversation, $user)
    {
        if (!$user) {
            $user = $this->getUser();
        }
        return $user->can('join-conversation', $conversation);
    }

    public static function sendAutoMessage($conversation, $index, $user)
    {
        if (config('app.mode') !== 'live') {
            return;
        }
        foreach (self::$autoMessages as $autoMessage) {
            if ($index == $autoMessage->position && $autoMessage->enabled && ($user->role == $autoMessage->send_to || $autoMessage->send_to === 'BOTH')) {
                $user->profile;

                //$autoMessage->send_to = $user->role;
                $copy = json_decode(json_encode($autoMessage));
                $copy->message = \App\Lib\TemplateParser::parseTemplate($autoMessage->message, ['user' => $user->toArray()]);
                $copy->send_to = $user->role;
                $messageBot = Chat::message(json_encode($copy))
                    ->id(\Ramsey\Uuid\Uuid::uuid4())
                    ->type('text_bot')
                    ->from(self::getChatBot()->id)
                    ->to($conversation)
                    ->sent_at(time() + 1)
                    ->send();
            }
        }
    }

    public function listChats()
    {
        $user = $this->getUser();
        if (!$user || $user->role != 'ADMIN') {
            abort(403);
        }
        $results = \Cypretex\Chat\Models\Conversation::with('last_message', 'users')->whereHas('last_message')->orderBy('updated_at', 'desc')->get();
        foreach ($results as $result) {
            $result->unread_count = $result->messages()->where('created_at', '>', $result->checked_at)->where('user_id', '!=', $this->getChatBot()->id)->count();
        }
        $response = $this->getResponseInstance();
        $response->setPayload($results);
        return $response->render();
    }

    public function test()
    {

        $conversations = Chat::conversations()->for(User::findOrFail('3c771716-887d-3be1-a3a0-bcb0ed1a99a9'))->limit(200)->page(1)->get();
        /* foreach($conversations as $c){
          $c->last_message= $c->last_message();
          } */
        return $conversations;

        return $conversation->messages()->where(['user_id' => '3c771716-887d-3be1-a3a0-bcb0ed1a99a9'])->count();

        $user01 = User::where(['email' => 'provider127@test.com'])->firstOrFail();
        $user02 = User::where(['email' => 'user21@test.com'])->firstOrFail();
        $participants = [$user01->id, $user02->id];

        $conversation = Chat::conversations()->getById(1);
        $message = Chat::message('Test ' . \Carbon\Carbon::now())
            ->from($user01)
            ->to($conversation)
            ->send();
        return $message;
        /* $conversations = Chat::conversations()->for($user02)->limit(200)->page(1)->get();
          foreach ($conversations as $conversation) {
          $conversation->unreadCount = Chat::conversation($conversation)->for($user02)->unreadCount();
          }
          return $conversations; */

        /* $conversations = Chat::conversations()->for($user01)->limit(200)->page(1)->get();
          foreach ($conversations as $conversation) {
          $conversation->unreadCount = Chat::conversation($conversation)->for($user01)->unreadCount();
          $conversation->users;
          }
          return $conversations;
          return Chat::conversations()->between($user02, $user03); */

        /* $conversation = Chat::conversations()->getById(1);
          $messages = Chat::conversation($conversation)->for($user01)->setPaginationParams([
          'page' => 1,
          'perPage' => 50,
          'sorting' => "desc",
          'columns' => [
          '*'
          ]
          ])->getMessages();
          Chat::conversation($conversation)->for($user01)->readAll();
          return $messages; */
        $conversation = Chat::conversations()->getById(1);
        $message = Chat::message('Response ' . \Carbon\Carbon::now())
            ->from($user02)
            ->to($conversation)
            ->send();
        return $message;
    }
}
