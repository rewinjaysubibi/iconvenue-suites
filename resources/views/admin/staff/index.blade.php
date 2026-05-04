@extends('layouts.admin')

@section('page-title', 'Staff Management')

@section('main-content')
<div class="mb-6">
    <a href="{{ route('admin.staff.create') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-plus mr-2"></i>Add Staff Member
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($staff as $member)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-semibold text-gray-800">{{ $member->name }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $member->email }}</td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $member->role->name == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($member->role->name) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $member->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-2">
                        <!-- Toggle Active/Deactivate Button -->
                        @if($member->id !== auth()->id())
                        <form action="{{ route('admin.staff.toggle', $member) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r {{ $member->is_active ? 'from-green-500 to-green-600' : 'from-gray-500 to-gray-600' }} text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="{{ $member->is_active ? 'Deactivate Staff Member' : 'Activate Staff Member' }}"
                                    onclick="return confirm('{{ $member->is_active ? 'Deactivate this staff member?' : 'Activate this staff member?' }}')">
                                <i class="fas fa-{{ $member->is_active ? 'user-times' : 'user-check' }} mr-1"></i>{{ $member->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @endif
                        
                        <!-- Edit Button -->
                        <a href="{{ route('admin.staff.edit', $member) }}" 
                           class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                           title="Edit Staff">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        
                        <!-- Delete Button (Cannot delete yourself) -->
                        @if($member->id !== auth()->id())
                        <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-semibold rounded-lg hover:shadow-lg transition-all duration-200" 
                                    title="Delete Staff"
                                    onclick="return confirm('Permanently delete this staff member? This action cannot be undone.')">
                                <i class="fas fa-trash-alt mr-1"></i>Delete
                            </button>
                        </form>
                        @else
                        <span class="inline-flex items-center px-3 py-2 bg-gray-300 text-gray-500 text-xs font-semibold rounded-lg cursor-not-allowed" 
                              title="Cannot delete yourself">
                            <i class="fas fa-lock mr-1"></i>Current User
                        </span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-600">No staff members found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $staff->links() }}
</div>
@endsection
