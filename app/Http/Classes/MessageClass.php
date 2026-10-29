<?php

namespace App\Http\Classes;

use App\Http\Interfaces\MessageInterface;

class MessageClass implements MessageInterface
{
    public static function chat_users_id($sender_id, $recipient_id)
    {
        $chat_users_id = '';
        
        if ($sender_id < $recipient_id)
        {
            $chat_users_id = $sender_id.'_'.$recipient_id;
        }
        else
        {
            $chat_users_id = $recipient_id.'_'.$sender_id;
        }

        return $chat_users_id;
    }
}