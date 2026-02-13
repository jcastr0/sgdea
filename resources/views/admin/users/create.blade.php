@extends('layouts.sgdea')

@section('title', 'Crear Usuario')
@section('page-title', 'Nuevo Usuario')

@section('breadcrumbs')
<x-breadcrumb :items="[
    ['label' => 'Panel Global', 'url' => route('admin.dashboard')],
    ['label' => 'Usuarios', 'url' => route('admin.users.index')],
    ['label' => 'Crear'],
]" />
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Crear Nuevo Usuario</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Complete el formulario para crear un nuevo usuario en el sistema.
        </p>
    </div>

    {{-- Formulario --}}
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-6 space-y-6">

            {{-- Información Personal --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Personal</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nombre completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
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
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
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
                                <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
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
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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

            {{-- Seguridad --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Seguridad</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Contraseña
                        </label>
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                      bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                      {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}"
                               placeholder="Dejar vacío para generar automáticamente">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Si se deja vacío, se generará una contraseña aleatoria.
                        </p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                       bg-white dark:bg-slate-700 text-gray-900 dark:text-white
                                       {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="pending_approval" {{ old('status') == 'pending_approval' ? 'selected' : '' }}>Pendiente de aprobación</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

        </div>

        {{-- Botones de acción --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="cursor-pointer px-4 py-2.5 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="cursor-pointer px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                Crear Usuario
            </button>
        </div>
    </form>
</div>
@endsection

