<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Storage;

class MessageArchiver
{
    protected string $directory = 'logs_messages';
    protected int $batchSize = 250;

    /**
     * Check if the total message count has reached a multiple of 250.
     * If so, archive the latest batch into a JSON file.
     */
    public function checkAndArchive(): ?string
    {
        $totalMessages = Message::count();

        // Only archive when we hit exact multiples of batchSize
        if ($totalMessages === 0 || $totalMessages % $this->batchSize !== 0) {
            return null;
        }

        $archiveNumber = intdiv($totalMessages, $this->batchSize);
        $filename = $this->directory . '/archive_' . str_pad($archiveNumber, 3, '0', STR_PAD_LEFT) . '.json';

        // Don't overwrite if this archive already exists
        if (Storage::exists($filename)) {
            return null;
        }

        // Get the 250 messages for this batch
        $offset = ($archiveNumber - 1) * $this->batchSize;
        $messages = Message::with('sender.detail')
            ->orderBy('id', 'asc')
            ->skip($offset)
            ->take($this->batchSize)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->sender->name,
                    'sender_dome' => $msg->sender->detail->dome ?? 'Inconnu',
                    'target_dome' => $msg->target_dome,
                    'content' => $msg->content,
                    'metadata' => $msg->metadata,
                    'sent_at' => $msg->created_at->format('Y-m-d H:i:s'),
                ];
            });

        $archiveData = [
            'archive_id' => $archiveNumber,
            'created_at' => now()->toIso8601String(),
            'dome_system' => 'Mars Colony - Communication Logs',
            'message_range' => ($offset + 1) . ' - ' . ($offset + $this->batchSize),
            'message_count' => $messages->count(),
            'messages' => $messages->toArray(),
        ];

        Storage::put($filename, json_encode($archiveData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filename;
    }

    /**
     * Force an archive of all current messages (for testing or manual trigger).
     */
    public function forceArchive(): ?string
    {
        $totalMessages = Message::count();

        if ($totalMessages === 0) {
            return null;
        }

        $archiveNumber = intdiv($totalMessages - 1, $this->batchSize) + 1;
        $filename = $this->directory . '/manual_archive_' . now()->format('Ymd_His') . '.json';

        $messages = Message::with('sender.detail')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->sender->name,
                    'sender_dome' => $msg->sender->detail->dome ?? 'Inconnu',
                    'target_dome' => $msg->target_dome,
                    'content' => $msg->content,
                    'metadata' => $msg->metadata,
                    'sent_at' => $msg->created_at->format('Y-m-d H:i:s'),
                ];
            });

        $archiveData = [
            'archive_id' => 'manual',
            'created_at' => now()->toIso8601String(),
            'dome_system' => 'Mars Colony - Communication Logs (Manual Archive)',
            'message_count' => $messages->count(),
            'messages' => $messages->toArray(),
        ];

        Storage::put($filename, json_encode($archiveData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filename;
    }

    /**
     * Get the list of all archive files with their metadata.
     */
    public function getArchives(): array
    {
        if (!Storage::exists($this->directory)) {
            return [];
        }

        $files = Storage::files($this->directory);
        $archives = [];

        foreach ($files as $file) {
            $content = json_decode(Storage::get($file), true);
            $archives[] = [
                'filename' => basename($file),
                'path' => $file,
                'archive_id' => $content['archive_id'] ?? '?',
                'created_at' => $content['created_at'] ?? null,
                'message_count' => $content['message_count'] ?? 0,
                'message_range' => $content['message_range'] ?? 'N/A',
                'size' => round(Storage::size($file) / 1024, 2) . ' KB',
            ];
        }

        return $archives;
    }

    /**
     * Get the content of a specific archive file.
     */
    public function getArchiveContent(string $filename): ?array
    {
        $path = $this->directory . '/' . $filename;

        if (!Storage::exists($path)) {
            return null;
        }

        return json_decode(Storage::get($path), true);
    }
}
