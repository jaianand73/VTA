<?php

namespace App\Services;

use App\Models\EmailIntakeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager;

class EmailIntakeService
{
    public function fetch(): int
    {
        $fetched = 0;

        try {
            $cm = new ClientManager();
            $client = $cm->make([
                'host' => config('imap.host', env('IMAP_HOST', 'localhost')),
                'port' => config('imap.port', env('IMAP_PORT', 993)),
                'encryption' => config('imap.encryption', env('IMAP_ENCRYPTION', 'ssl')),
                'validate_cert' => config('imap.validate_cert', env('IMAP_VALIDATE_CERT', true)),
                'username' => config('imap.username', env('IMAP_USERNAME')),
                'password' => config('imap.password', env('IMAP_PASSWORD')),
                'protocol' => config('imap.protocol', env('IMAP_PROTOCOL', 'imap')),
            ]);

            $client->connect();
            $inbox = $client->getFolder('INBOX');
            $messages = $inbox->messages()->unseen()->get();

            foreach ($messages as $message) {
                $attachments = [];
                $hasAttachments = false;

                foreach ($message->getAttachments() as $attachment) {
                    $hasAttachments = true;
                    $path = storage_path('app/vta-documents/email-attachments/' . $attachment->getName());
                    $dir = dirname($path);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    file_put_contents($path, $attachment->getContent());
                    $attachments[] = $path;
                }

                EmailIntakeLog::create([
                    'from_email' => $message->getFrom()[0]->mail ?? 'unknown',
                    'from_name' => $message->getFrom()[0]->personal ?? null,
                    'subject' => $message->getSubject(),
                    'body' => $message->getTextBody(),
                    'received_at' => Carbon::now(),
                    'has_attachments' => $hasAttachments,
                    'attachment_paths' => !empty($attachments) ? json_encode($attachments) : null,
                    'processed' => false,
                ]);

                $message->setFlag('SEEN');
                $fetched++;
            }

            $client->disconnect();
        } catch (\Exception $e) {
            Log::error('Email intake fetch failed: ' . $e->getMessage());
        }

        return $fetched;
    }
}
