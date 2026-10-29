<?php

namespace App\Http\Interfaces;

interface MessageInterface
{
    public static function chat_users_id($sender_id, $recipient_id);
}
