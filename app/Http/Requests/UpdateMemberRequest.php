<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Member;

class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->canEditMembers();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clan_id' => ['sometimes', 'exists:clans,id'],
            'family_id' => ['sometimes', 'exists:families,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            
            'first_name' => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'maiden_name' => ['nullable', 'string', 'max:255'],
            
            'gender' => ['sometimes', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            
            'father_id' => [
                'nullable',
                'exists:members,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $father = Member::find($value);
                        if ($father && $father->gender !== 'male') {
                            $fail('The selected father must be male.');
                        }
                    }
                }
            ],
            
            'mother_id' => [
                'nullable',
                'exists:members,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $mother = Member::find($value);
                        if ($mother && $mother->gender !== 'female') {
                            $fail('The selected mother must be female.');
                        }
                    }
                }
            ],
            
            
            'status' => ['sometimes', Rule::in(['alive', 'deceased'])],
            'date_of_death' => [
                'nullable',
                'date',
                'after:date_of_birth',
                'required_if:status,deceased'
            ],
            'place_of_death' => ['nullable', 'string', 'max:255'],
            
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'street' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            
            'biography' => ['nullable', 'string', 'max:5000'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

