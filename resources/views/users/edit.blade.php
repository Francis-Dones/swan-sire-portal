@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Update user information')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-lg shadow-gray-100/20 dark:shadow-none overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl">
                        <i class="ti ti-user-edit text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200">Edit User Profile</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Update user account information</p>
                    </div>
                </div>
                <a href="{{ route('users.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-all">
                    <i class="ti ti-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('users.update', $user->id) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Username -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-user text-gray-400"></i>
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all @error('username') border-red-500 @enderror"
                           required>
                    @error('username')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-mail text-gray-400"></i>
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-map-pin text-gray-400"></i>
                        Address
                    </label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}" 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all">
                    @error('address')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Age -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-cake text-gray-400"></i>
                        Age
                    </label>
                    <input type="number" name="age" value="{{ old('age', $user->age) }}" 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all"
                           min="1" max="150">
                    @error('age')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vessel Name -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-ship text-gray-400"></i>
                        Vessel Name
                    </label>
                    <input type="text" name="vessel_name" value="{{ old('vessel_name', $user->vessel_name) }}" 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all">
                    @error('vessel_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Token Type -->
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <i class="ti ti-key text-gray-400"></i>
                        Token Type
                    </label>
                    <select name="token_type" 
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 dark:focus:border-purple-500 transition-all">
                        <option value="">Select Token Type</option>
                        <option value="personal" {{ old('token_type', $user->token_type) == 'personal' ? 'selected' : '' }}>Personal</option>
                        <option value="business" {{ old('token_type', $user->token_type) == 'business' ? 'selected' : '' }}>Business</option>
                        <option value="enterprise" {{ old('token_type', $user->token_type) == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                    @error('token_type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm font-medium rounded-xl transition-all shadow-md hover:shadow-lg">
                    <i class="ti ti-device-floppy"></i>
                    Update User
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