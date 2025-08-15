<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => (string) $this->id,
            'user_id'   => (int) $this->user_id,
            'title'     => $this->title,
            'src'       => $this->src,
            'poster'    => $this->poster,
            'tracks'    => $this->tracks ?? [],

            // ---------- optional better code ----------- //
            // 'tracks' => $this->whenLoaded(
            //     'tracks',
            //     function () {
            //         return $this->tracks->map(function ($t) {
            //             return [
            //                 'kind' => $t->kind ?? 'subtitles',
            //                 'src' => $t->src,
            //                 'srclang' => $t->srclang,
            //                 'label' => $t->label,
            //             ];
            //         });
            //     },
            //     [],
            // ),
            // ---------- optional better code ----------- //

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
