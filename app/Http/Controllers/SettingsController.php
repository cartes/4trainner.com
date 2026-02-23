<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SettingsController extends Controller
{
    /**
     * Display the settings view.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        // Load user meta into a keyed array for easy access in frontend
        $metaData = $user->meta->pluck('meta_value', 'meta_key')->toArray();

        return view('settings.index', compact('user', 'metaData'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],

            // Extended profile fields
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],

            // Notification preferences
            'notify_new_routines' => ['nullable', 'boolean'],
            'notify_messages' => ['nullable', 'boolean'],
            'notify_marketing' => ['nullable', 'boolean'],
            'notify_new_students' => ['nullable', 'boolean'],
            'notify_system_alerts' => ['nullable', 'boolean'],
        ]);

        // 1. Update basic user data
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // 2. Update metadata
        $metaKeys = [
            'phone',
            'bio',
            'notify_new_routines',
            'notify_messages',
            'notify_marketing',
            'notify_new_students',
            'notify_system_alerts'
        ];

        foreach ($metaKeys as $key) {
            // Check if the field was present in the request (even if null/false)
            if ($request->has($key)) {
                $value = $validated[$key] ?? null;

                // Convert booleans to string for storage
                if (is_bool($value)) {
                    $value = $value ? '1' : '0';
                }

                if ($value !== null && $value !== '') {
                    $user->meta()->updateOrCreate(
                        ['meta_key' => $key],
                        ['meta_value' => $value]
                    );
                } else {
                    // if empty, remove the meta record to keep table clean
                    $user->meta()->where('meta_key', $key)->delete();
                }
            }
        }

        return redirect()->back()->with('status', 'profile-updated');
    }
}
