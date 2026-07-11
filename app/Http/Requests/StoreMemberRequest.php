<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Member;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        // Admins can create any member
        if ($user->isAdmin()) {
            return true;
        }

        // Unlinked users can create their own profile (self-registration)
        if ($user->member_id === null) {
            return true;
        }

        // Linked users can only create a member if they are one of the parents or the spouse
        $fatherId = $this->input('father_id');
        $motherId = $this->input('mother_id');
        $spouseId = $this->input('spouse_id');

        return ($fatherId == $user->member_id || $motherId == $user->member_id || $spouseId == $user->member_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isSpouse = $this->filled('spouse_id');

        return [
            'clan_id' => [$isSpouse ? 'nullable' : 'required_without:clan_name', 'nullable', 'exists:clans,id'],
            'clan_name' => [$isSpouse ? 'nullable' : 'required_without:clan_id', 'nullable', 'string', 'max:255'],
            'family_id' => ['nullable', 'exists:families,id'],
            'family_name' => ['nullable', 'string', 'max:255'],

            
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'maiden_name' => ['nullable', 'string', 'max:255'],
            
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
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
            'father_name' => ['nullable', 'string', 'max:255'],
            
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
            'mother_name' => ['nullable', 'string', 'max:255'],
            

            
            
            'spouse_name' => ['nullable', 'string', 'max:255'],
            
            'status' => ['nullable', Rule::in(['alive', 'deceased'])],
            'date_of_death' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('date_of_birth')) {
                        $dob = \Carbon\Carbon::parse($this->input('date_of_birth'));
                        $dod = \Carbon\Carbon::parse($value);
                        if ($dod->lt($dob)) {
                            $fail('Date of death must be after date of birth.');
                        }
                    }
                }
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
            
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            
            'biography' => ['nullable', 'string', 'max:5000'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'clan_id.required' => 'Please select a clan.',
            'family_id.required' => 'Please select a family.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'date_of_death.after' => 'Date of death must be after date of birth.',
            'profile_photo.max' => 'Profile photo must not exceed 5MB.',
        ];
    }
}

