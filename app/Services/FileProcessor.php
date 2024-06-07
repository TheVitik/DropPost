<?php

namespace App\Services;

use App\Enums\FileType;
use Illuminate\Http\File;

class FileProcessor
{
    /**
     * Upload files to the storage
     *
     * @param array|File[] $files
     * @return array
     */
    public function uploadFiles(array $files): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            $path = $file->store('uploads');
            $uploadedFiles[] = [
                'name' => $file->getClientOriginalName(),
                'type' => $this->determineTelegramType($file->getMimeType()),
                'mime' => $file->getMimeType(),
                'path' => $path,
            ];
        }

        return $uploadedFiles;
    }

    private function determineTelegramType(string $mimeType): string
    {
        $types = [
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/tiff', 'image/bmp', 'image/jpg'],
            'audio' => [
                'audio/mpeg',
                'audio/ogg',
                'audio/wav',
                'audio/webm',
                'audio/3gpp',
                'audio/3gpp2',
                'audio/mp3',
                'audio/mp4',
                'audio/m4a',
                'audio/aac',
                'audio/flac',
                'audio/opus',
                'audio/x-wav',
                'audio/x-ms-wma',
                'audio/x-ms-wmv',
                'audio/x-aiff',
                'audio/x-midi',
                'audio/x-pn-realaudio',
            ],
            'video' => ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/mov'],
            'document' => ['application/pdf', ' * '],
        ];

        return match (true) {
            in_array($mimeType, $types['image']) => 'photo',
            in_array($mimeType, $types['audio']) => 'audio',
            in_array($mimeType, $types['video']) => 'video',
            default => 'document',
        };
    }
}