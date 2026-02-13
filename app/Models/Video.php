<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'url', 'file_path', 'highlight_landing', 'highlight_enterprise',
    ];

    /**
     * Try to extract a YouTube video id from the saved URL.
     * Returns the id string or null.
     */
    public function youtubeId()
    {
        if (empty($this->url)) return null;
        $url = $this->url;
        // common patterns
        if (preg_match('/v=([A-Za-z0-9_-]{8,})/', $url, $m)) return $m[1];
        if (preg_match('/youtu\.be\/([A-Za-z0-9_-]{8,})/', $url, $m)) return $m[1];
        if (preg_match('/embed\/([A-Za-z0-9_-]{8,})/', $url, $m)) return $m[1];
        // fallback: any sequence of 11 chars typical for YouTube id
        if (preg_match('/([A-Za-z0-9_-]{11})/', $url, $m)) return $m[1];
        return null;
    }

    /**
     * Check via YouTube Data API whether the remote video is embeddable and public.
     * Returns true/false/null (null when API key not configured or error).
     */
    public function checkYouTubeEmbeddable(): ?bool
    {
        $id = $this->youtubeId();
        if (!$id) return null;
        $key = env('YOUTUBE_API_KEY');
        if (empty($key)) return null;
        $cacheKey = 'yt:embeddable:'.$id;
        return Cache::remember($cacheKey, 60, function() use ($id, $key) {
            try {
                $resp = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'status,contentDetails',
                    'id' => $id,
                    'key' => $key,
                ]);
                if (!$resp->ok()) return null;
                $data = $resp->json();
                if (empty($data['items']) || !isset($data['items'][0])) return false;
                $item = $data['items'][0];
                $status = $item['status'] ?? [];
                // If embeddable explicitly false -> not embeddable
                if (array_key_exists('embeddable', $status) && !$status['embeddable']) return false;
                // If privacyStatus is private -> not embeddable
                if (isset($status['privacyStatus']) && $status['privacyStatus'] === 'private') return false;
                // contentDetails may contain regionRestriction which may block playback in some regions; we won't attempt region checks here
                return true;
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
