<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Get user profile stats
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();

        $stats = [
            'total_devices' => $user->reportedDevices()->count(),
            'found_devices' => $user->reportedDevices()->where('status', 'found')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Update user profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'locale' => 'nullable|in:en,ar',
            'timezone' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        return response()->json([
            'user' => $user->fresh()->load('roles'),
            'message' => 'Profile updated successfully',
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // Revoke all tokens to force re-login
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        // Process and save new avatar
        $image = $request->file('avatar');
        $filename = $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();

        // Resize image to 200x200
        $resizedImage = Image::make($image)->fit(200, 200);

        // Save to storage
        Storage::disk('public')->put('avatars/' . $filename, $resizedImage->encode());

        // Update user record
        $user->update(['avatar' => $filename]);

        return response()->json([
            'avatar_url' => asset('storage/avatars/' . $filename),
            'message' => 'Avatar uploaded successfully',
        ]);
    }

    /**
     * Delete user account
     */
    public function destroy(): JsonResponse
    {
        $user = auth()->user();

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        // Delete user's devices and related data
        $user->reportedDevices()->delete();

        // Delete user
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }
}
