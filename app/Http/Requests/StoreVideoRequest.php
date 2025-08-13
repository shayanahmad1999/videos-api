<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
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
            'title'          => ['required', 'string', 'max:255'],
            // Either provide URLs/paths OR upload files (see controller)
            'src'            => ['nullable', 'string', 'max:2048', 'url'],
            'poster'         => ['nullable', 'string', 'max:2048', 'url'],
            'tracks'         => ['nullable', 'array'],
            'tracks.*.kind'  => ['nullable', 'string', 'max:50'],
            'tracks.*.src'   => ['nullable', 'string', 'max:2048'],
            'tracks.*.srclang' => ['nullable', 'string', 'max:12'],
            'tracks.*.label' => ['nullable', 'string', 'max:100'],

            // Optional file uploads
            'video_file'     => ['nullable', 'file', 'mimetypes:video/mp4,application/vnd.apple.mpegurl', 'max:204800'], // ~200MB
            'poster_file'    => ['nullable', 'image', 'max:5120'], // 5MB
        ];
    }
}
