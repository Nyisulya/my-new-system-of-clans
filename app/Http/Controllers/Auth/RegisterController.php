<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * Also creates a member profile and links them together.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Create Member profile first
        $member = \App\Models\Member::create([
            'first_name' => $data['name'],
            'last_name' => '', // Can be updated later by user
            'status' => 'alive',
            'generation_number' => 1, // Root generation for self-registered members
            'clan_id' => null,
            'family_id' => null,
            'gender' => 'male', // Default, can be updated later
            'date_of_birth' => null,
        ]);

        // Create User account linked to Member
        return User::create([
            'name' => $data['name'],
            'email' => null, // No email for local users
            'password' => Hash::make($data['password']),
            'member_id' => $member->id,
            'role' => 'member', // Regular member role (can view all, edit only self)
            'is_active' => true,
        ]);
    }
}
