<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'full_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_phone' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'religion' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'nid_number' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relation' => ['nullable', 'string', 'max:255'],
            'nominee_phone' => ['nullable', 'string', 'max:255'],
            'reference_name' => ['nullable', 'string', 'max:255'],
            'reference_phone' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'join_date' => ['required', 'date'],
            'share_number' => ['required', 'integer', 'min:1'],
            'membership_status' => ['required', 'in:active,revoked,checked_out'],
        ]);

        $user = DB::transaction(function () use ($request): User {
            $memberCode = Member::nextMemberCode();

            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'member',
                'status' => 'active',
            ]);

            Member::create([
                'user_id' => $user->id,
                'member_code' => $memberCode,
                'full_name' => $request->full_name,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'spouse_name' => $request->spouse_name,
                'spouse_phone' => $request->spouse_phone,
                'blood_group' => $request->blood_group,
                'religion' => $request->religion,
                'education' => $request->education,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'phone' => $request->phone,
                'phone_search' => preg_replace('/\D+/', '', (string) $request->phone) ?: null,
                'nid_number' => $request->nid_number,
                'date_of_birth' => $request->date_of_birth,
                'occupation' => $request->occupation,
                'present_address' => $request->present_address,
                'permanent_address' => $request->permanent_address,
                'nominee_name' => $request->nominee_name,
                'nominee_relation' => $request->nominee_relation,
                'nominee_phone' => $request->nominee_phone,
                'reference_name' => $request->reference_name,
                'reference_phone' => $request->reference_phone,
                'remarks' => $request->remarks,
                'join_date' => $request->join_date,
                'share_number' => $request->share_number,
                'membership_status' => $request->membership_status,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
