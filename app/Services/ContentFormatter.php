<?php

namespace App\Services;

class ContentFormatter
{
    public function htmlToTelegram(string $html): string
    {
        // Convert HTML to Telegram format
        // remove <p> tags, replace </p> with %0A

        $html = str_replace('<p>', '', $html);
        $html = str_replace('&nbsp;', '', $html);
        $html = str_replace('<br>', '', $html);
        $html = str_replace('</p>', "\n", $html);

        // if exists ul with li, replace <li> with - and </li> with %0A and remove <ul> and </ul>
        $html = str_replace('<ul>', '', $html);
        $html = str_replace('</ul>', '', $html);
        $html = str_replace('<ol>', '', $html);
        $html = str_replace('</ol>', '', $html);
        $html = str_replace('<li>', '- ', $html);
        $html = str_replace('</li>', "\n", $html);


        return $html;
    }
}