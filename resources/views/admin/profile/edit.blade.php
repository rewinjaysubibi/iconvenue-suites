@extends('layouts.admin')

@section('page-title', 'My Profile')

@section('main-content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Profile Image Section -->
            <div class="mb-8 text-center">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Profile Picture</h3>
                
                <div class="flex flex-col items-center">
                    <div class="relative mb-4">
                        @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" 
                             alt="Profile" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-purple-200 shadow-lg"
                             id="profilePreview">
                        @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-4xl font-bold border-4 border-purple-200 shadow-lg"
                             id="profilePreview">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="flex gap-2">
                        <label class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all duration-200 cursor-pointer">
                            <i class="fas fa-camera mr-2"></i>Change Photo
                            <input type="file" name="profile_image" accept="image/*" class="hidden" id="profileImageInput">
                        </label>
                        
                        @if($user->profile_image)
                        <form action="{{ route('admin.profile.removeImage') }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all duration-200"
                                    onclick="return confirm('Remove profile picture?')">
                                <i class="fas fa-trash-alt mr-2"></i>Remove
                            </button>
                        </form>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-2">JPG, PNG or GIF (Max 2MB)</p>
                </div>
            </div>

            <hr class="my-8">

            <!-- Basic Information -->
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Basic Information</h3>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Role</label>
                        <input type="text" value="{{ ucfirst($user->role->name) }}" disabled 
                            class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <input type="text" value="{{ $user->is_active ? 'Active' : 'Inactive' }}" disabled 
                            class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600">
                    </div>
                </div>
            </div>

            <hr class="my-8">

            <!-- Change Password -->
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Change Password</h3>
                <p class="text-sm text-gray-600 mb-4">Leave blank if you don't want to change your password</p>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-semibold mb-2">Current Password</label>
                        <input type="password" name="current_password" 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        @error('current_password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">New Password</label>
                        <input type="password" name="new_password" 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        @error('new_password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-lg hover:shadow-lg transition-all duration-200 font-semibold">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-400 transition font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('profileImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Profile" class="w-32 h-32 rounded-full object-cover border-4 border-purple-200 shadow-lg">`;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
