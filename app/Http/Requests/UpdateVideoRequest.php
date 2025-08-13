<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'          => ['sometimes', 'required', 'string', 'max:255'],
            'src'            => ['sometimes', 'nullable', 'string', 'max:2048', 'url'],
            'poster'         => ['sometimes', 'nullable', 'string', 'max:2048', 'url'],
            'tracks'         => ['sometimes', 'nullable', 'array'],
            'tracks.*.kind'  => ['nullable', 'string', 'max:50'],
            'tracks.*.src'   => ['nullable', 'string', 'max:2048'],
            'tracks.*.srclang' => ['nullable', 'string', 'max:12'],
            'tracks.*.label' => ['nullable', 'string', 'max:100'],

            'video_file'     => ['sometimes', 'nullable', 'file', 'mimetypes:video/mp4,application/vnd.apple.mpegurl', 'max:204800'],
            'poster_file'    => ['sometimes', 'nullable', 'image', 'max:5120'],
        ];
    }
}
