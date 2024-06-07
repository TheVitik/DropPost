<?php

namespace App\Enums;

enum FileType: string
{
    case PHOTO = 'photo';
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
    case VOICE = 'voice';
    case STICKER = 'sticker';
    case ANIMATION = 'animation';
    case VIDEO_NOTE = 'video_note';
    case CONTACT = 'contact';
    case LOCATION = 'location';
    case VENUE = 'venue';
    case POLL = 'poll';
    case DICE = 'dice';
}
