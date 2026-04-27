<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\MessageArchiver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userDome = $user->detail->dome ?? 'A';

        // Get messages sent TO the user's dome, AND messages sent BY the user.
        $messages = Message::where('target_dome', $userDome)
            ->orWhere('sender_id', $user->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('messages.index', compact('messages', 'userDome'));
    }

    public function fetch(Request $request)
    {
        $user = Auth::user();
        $userDome = $user->detail->dome ?? 'A';
        $lastId = $request->query('last_id', 0);

        $newMessages = Message::where('id', '>', $lastId)
            ->where(function ($query) use ($userDome, $user) {
                $query->where('target_dome', $userDome)
                    ->orWhere('sender_id', $user->id);
            })
            ->with('sender.detail')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name,
                    'sender_dome' => $msg->sender->detail->dome ?? 'Inconnu',
                    'target_dome' => $msg->target_dome,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at->format('H:i:s'),
                ];
            });

        // Get who is typing to this user's dome
        $typingUser = Cache::get("typing_to_{$userDome}");
        // Ensure you don't see yourself typing
        if ($typingUser === $user->name) {
            $typingUser = null;
        }

        return response()->json([
            'messages' => $newMessages,
            'typing' => $typingUser
        ]);
    }

    public function typing(Request $request)
    {
        $request->validate([
            'target_dome' => 'required|string',
        ]);

        $user = Auth::user();

        // Store in cache that this user is typing to the target dome for 3 seconds
        Cache::put("typing_to_{$request->target_dome}", $user->name, 3);

        return response()->json(['status' => 'ok']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'target_dome' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $user = Auth::user();

        $message = Message::create([
            'sender_id' => $user->id,
            'target_dome' => $request->target_dome,
            'content' => $request->content,
            'metadata' => [
                'priority' => 'normal',
                'system_time' => now()->toIso8601String(),
            ]
        ]);

        // Check if we need to archive messages
        $archiver = new MessageArchiver();
        $archivedFile = $archiver->checkAndArchive();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Message envoyé',
                'archived' => $archivedFile,
            ]);
        }

        return redirect()->route('messages.index')->with('status', 'Message transmis avec succès.');
    }

    public function archives()
    {
        $archiver = new MessageArchiver();
        $archives = $archiver->getArchives();

        return view('messages.archives', compact('archives'));
    }

    public function showArchive(string $filename)
    {
        $archiver = new MessageArchiver();
        $content = $archiver->getArchiveContent($filename);

        if (!$content) {
            abort(404, 'Archive introuvable.');
        }

        return view('messages.archive-detail', compact('content', 'filename'));
    }

    public function forceArchive()
    {
        $archiver = new MessageArchiver();
        $file = $archiver->forceArchive();

        if ($file) {
            return redirect()->route('messages.archives')->with('status', 'Archive créée : ' . basename($file));
        }

        return redirect()->route('messages.archives')->with('error', 'Aucun message à archiver.');
    }
}
