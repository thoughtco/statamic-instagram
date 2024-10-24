<?php

namespace Thoughtco\StatamicInstagram\Tags;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Instagram\Api;
use Instagram\Model\Media;
use Instagram\Model\Profile;
use Statamic\Tags\Tags;

class Instagram extends Tags
{
    /**
     * The {{ instagram profile="user_handle" }} tag.
     *
     * @return string|array
     */
    public function index()
    {
        $limit = $this->params->int('limit', 12);

        $cache_key = config('statamic-instagram.cache.key_prefix').'_'.$limit;

        if (! $profileHandle = $this->params->get('profile')) {
            return [];
        }

        try {
            return $this->output(Cache::remember(
                $cache_key,
                now()->addSeconds(config('statamic-instagram.cache.duration')),
                fn() => $this->getData($profileHandle, $limit)
            ));
        } catch (\Exception $exception) {
            Log::alert('Instagram error : '.$exception->getMessage());

            dd($exception);

            return [];
        }
    }

    private function output($data): array
    {
        if ($as = $this->params->get('as')) {
            $data = [$as => $data];
        }

        return $data;
    }

    private function getData(string $profileHandle, int $limit)
    {
        $profile = $this->getProfileData($profileHandle);

        return [
            'profile' => $profile->toArray(),
            'media' => $this->getMedia($profile, $limit),
        ];
    }

    private function getProfileData(string $profileHandle): Profile
    {
        return app(Api::class)->getProfile($profileHandle);
    }

    private function getMedia(Profile $profile, $limit = 12): array
    {
        $media = [];

        do {
            $profile = app(Api::class)->getMoreMedias($profile);

            $media = array_merge($media, $profile->getMedias());
        } while (count ($media) < $limit && $profile->hasMoreMedias());

        return collect($media)
            ->take($limit)
            ->map(function ($media) {
                $media = $media->toArray();
                $media['date'] = Carbon::parse($media['date']);

                if ($media['video']) {
                    $mediaClass = new Media();
                    $mediaClass->setLink($media['link']);

                    $media['videoUrl'] = app(Api::class)->getMediaDetailed($mediaClass)?->getVideoUrl();
                }

                return $media;
            })
            ->all();
    }
}
