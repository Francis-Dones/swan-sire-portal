@extends('layouts.app')
@section('title', 'Change Password')
@section('page-title', 'Change Password')
@section('page-subtitle', 'Update user password')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg shadow-gray-100/20 dark:shadow-none overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl">
                        <i class="ti ti-key text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">Change Password for {{ $user->username }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Set a new secure password for this user</p>
                    </div>
                </div>
                <a href="{{ route('users.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-all">
                    <i class="ti ti-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('users.update-password', $user->id) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="max-w-md mx-auto space-y-6">
                <!-- New Password -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-lock text-gray-400"></i>
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all @error('password') border-red-500 @enderror"
                           required>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                        <i class="ti ti-info-circle"></i>
                        Password must be at least 8 characters
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-lock-check text-gray-400"></i>
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all"
                           required>
                </div>

                <!-- Password Requirements -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-1">
                        <i class="ti ti-shield-check"></i>
                        Password requirements:
                    </p>
                    <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1 list-disc list-inside">
                        <li>Minimum 8 characters</li>
                        <li>Use a mix of letters, numbers, and symbols</li>
                        <li>Avoid common passwords</li>
                    </ul>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                    <i class="ti ti-device-floppy"></i>
                    Update Password
                </button>
                <a href="{{ route('users.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-all">
                    <i class="ti ti-x"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection