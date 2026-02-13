@extends('layouts.sgdea')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Panel Global', 'url' => route('admin.dashboard')],
    ['label' => 'Usuarios', 'url' => route('admin.users.index')],
    ['label' => $user->name],
]" />
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-xl font-bold text-white">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Usuario</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- Mensajes flash --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-start gap-3">
        <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h4 class="font-semibold text-green-800 dark:text-green-300">¡Éxito!</h4>
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- Formulario --}}
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 space-y-6">

            {{-- Información Personal --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Personal</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                      bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                      {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                      bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                      {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Asignación --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Asignación</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tenant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Empresa <span class="text-red-500">*</span>
                        </label>
                        <select id="tenant_id" name="tenant_id" required
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                       bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                       {{ $errors->has('tenant_id') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}">
                            <option value="">Seleccione una empresa</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('tenant_id', $user->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <select id="role_id" name="role_id" required
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                       bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                       {{ $errors->has('role_id') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}">
                            <option value="">Seleccione un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} ({{ $role->tenant?->name ?? 'Global' }})
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Estado --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estado</h2>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full sm:w-1/2 px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                   bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                   {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}">
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="pending_approval" {{ old('status', $user->status) == 'pending_approval' ? 'selected' : '' }}>Pendiente de aprobación</option>
                        <option value="blocked" {{ old('status', $user->status) == 'blocked' ? 'selected' : '' }}>Bloqueado</option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>

        {{-- Botones de acción --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users.show', $user) }}"
               class="cursor-pointer text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                ← Ver perfil completo
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="cursor-pointer px-4 py-2.5 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="cursor-pointer px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </form>

    {{-- Acciones adicionales --}}
    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Adicionales</h3>
        <div class="flex flex-wrap gap-3">
            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-amber-100 dark:bg-amber-900/30 hover:bg-amber-200 dark:hover:bg-amber-900/50 text-amber-800 dark:text-amber-300 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Resetear Contraseña
                </button>
            </form>

            @if($user->role?->slug !== 'super_admin' && $user->id !== 1)
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 text-red-800 dark:text-red-300 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar Usuario
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

