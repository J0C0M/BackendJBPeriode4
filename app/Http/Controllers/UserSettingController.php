<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserSettingController extends Controller
{
    /**
     * Display user settings page
     */
    public function index()
    {
        $user = Auth::user();
        $settings = $user->settings ?: new UserSetting();

        return view('settings.index', compact('settings'));
    }

    /**
     * Update user settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'friend_requests_notifications' => 'boolean',
            'game_invitations_notifications' => 'boolean',
            'privacy_level' => 'in:public,friends_only,private',
            'show_statistics' => 'boolean',
            'theme' => 'in:light,dark',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Get or create user settings
        $settings = $user->settings ?: new UserSetting(['user_id' => $user->id]);

        // Update settings
        $settings->fill([
            'email_notifications' => $request->has('email_notifications'),
            'friend_requests_notifications' => $request->has('friend_requests_notifications'),
            'game_invitations_notifications' => $request->has('game_invitations_notifications'),
            'privacy_level' => $request->input('privacy_level', 'public'),
            'show_statistics' => $request->has('show_statistics'),
            'theme' => $request->input('theme', 'light'),
        ]);

        $settings->save();

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('avatars'), $filename);
            $data['avatar'] = 'avatars/' . $filename;

            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
        }

        $user->update($data);

        return redirect()->route('settings.index')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Get user settings as JSON (for AJAX)
     */
    public function getSettings()
    {
        $user = Auth::user();
        $settings = $user->settings ?: new UserSetting();

        return response()->json([
            'settings' => $settings->toArray(),
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ]
        ]);
    }

    /**
     * Update settings via AJAX
     */
    public function updateSettingsAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'friend_requests_notifications' => 'boolean',
            'game_invitations_notifications' => 'boolean',
            'privacy_level' => 'in:public,friends_only,private',
            'show_statistics' => 'boolean',
            'theme' => 'in:light,dark',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $settings = $user->settings ?: new UserSetting(['user_id' => $user->id]);

        $settings->fill($request->only([
            'email_notifications',
            'friend_requests_notifications',
            'game_invitations_notifications',
            'privacy_level',
            'show_statistics',
            'theme'
        ]));

        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully!',
            'settings' => $settings->toArray()
        ]);
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|current_password',
            'confirmation' => 'required|in:DELETE',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Delete avatar if exists
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        // Logout and delete user
        Auth::logout();
        $user->delete();

        return redirect()->route('home')
            ->with('success', 'Account deleted successfully.');
    }
}
