<?php

namespace App\Http\Requests\API\Card;

use App\Rules\ContentLength;
use App\Rules\MaxImageCount;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', new ContentLength(768), new MaxImageCount(1)],
            'tags' => ['string', 'nullable'],
        ];
    }
}
