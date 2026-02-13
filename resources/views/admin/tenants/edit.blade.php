@extends('layouts.sgdea')

@section('title', 'Editar ' . $tenant->name)
@section('page-title', 'Editar Tenant')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ activeTab: 'general' }">

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tenants.show', $tenant) }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al detalle
            </a>
        </div>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold"
                 style="background: linear-gradient(135deg, {{ $tenant->primary_color ?? '#1a56db' }}, {{ $tenant->secondary_color ?? '#1e3a5f' }})">
                {{ strtoupper(substr($tenant->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $tenant->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Editar configuración</p>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-700 dark:text-red-400">Se encontraron errores:</p>
                    <ul class="mt-1 text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="mb-6 border-b border-gray-200 dark:border-slate-700 overflow-x-auto">
        <nav class="flex gap-1 min-w-max">
            <button type="button" @click="activeTab = 'general'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'general' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                General
            </button>
            <button type="button" @click="activeTab = 'plan'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'plan' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Plan y Límites
            </button>
            <button type="button" @click="activeTab = 'branding'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'branding' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                Branding
            </button>
            <button type="button" @click="activeTab = 'users'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'users' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Usuarios
                <span class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full">{{ $users->count() }}</span>
            </button>
            <button type="button" @click="activeTab = 'danger'"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors cursor-pointer flex items-center gap-2"
                    :class="activeTab === 'danger' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Zona de Peligro
            </button>
        </nav>
    </div>

    {{-- Tab: General --}}
    <div x-show="activeTab === 'general'" x-cloak>
        <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" value="general">

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Información General</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre de la Empresa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Identificador (Slug) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $tenant->slug) }}" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors font-mono">
                    </div>

                    <div>
                        <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dominio de Email <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">@</span>
                            <input type="text" id="domain" name="domain" value="{{ old('domain', $tenant->domain) }}" required
                                   class="w-full pl-8 pr-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="trial" {{ old('status', $tenant->status) === 'trial' ? 'selected' : '' }}>Período de Prueba</option>
                            <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspendido</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="px-6 py-3 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium transition-colors cursor-pointer">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tab: Plan y Límites --}}
    <div x-show="activeTab === 'plan'" x-cloak>
        <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" value="plan">

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Plan y Límites</h2>

                <div class="space-y-6">
                    {{-- Plan Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Plan</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @php $currentPlan = old('plan', $tenant->plan ?? 'professional'); @endphp
                            @foreach(['basic' => ['name' => 'Básico', 'desc' => 'Para empresas pequeñas'], 'professional' => ['name' => 'Profesional', 'desc' => 'Para empresas medianas'], 'enterprise' => ['name' => 'Empresarial', 'desc' => 'Para grandes empresas']] as $planKey => $planData)
                                <label class="relative p-4 rounded-lg border-2 cursor-pointer transition-all
                                    {{ $currentPlan === $planKey ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-slate-600 hover:border-gray-300' }}">
                                    <input type="radio" name="plan" value="{{ $planKey }}" class="sr-only" {{ $currentPlan === $planKey ? 'checked' : '' }}>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $planData['name'] }}</span>
                                        @if($currentPlan === $planKey)
                                            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $planData['desc'] }}</p>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Límites --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                        <div>
                            <label for="max_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Límite de Usuarios
                            </label>
                            <input type="number" id="max_users" name="max_users" value="{{ old('max_users', $tenant->max_users ?? 50) }}" min="0"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0 = Sin límite</p>
                        </div>
                        <div>
                            <label for="max_storage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Almacenamiento Máximo (GB)
                            </label>
                            <input type="number" id="max_storage" name="max_storage" value="{{ old('max_storage', $tenant->max_storage ?? 25) }}" min="0"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0 = Sin límite</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tab: Branding --}}
    <div x-show="activeTab === 'branding'" x-cloak x-data="{
        primaryColor: '{{ $tenant->primary_color ?? '#1a56db' }}',
        secondaryColor: '{{ $tenant->secondary_color ?? '#1e3a5f' }}',
        logoType: 'file',
        logoPreview: '{{ $tenant->logo_path ? asset('storage/' . $tenant->logo_path) : '' }}',
        svgCode: '',
        isSubmitting: false,

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        updateSvgPreview() {
            if (this.svgCode && this.svgCode.trim().startsWith('<svg')) {
                this.logoPreview = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(this.svgCode)));
            }
        }
    }">
        <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" value="branding">

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Branding y Colores</h2>

                <div class="space-y-6">
                    {{-- Logo Section --}}
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo de la Empresa</label>

                        {{-- Current Logo --}}
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-lg border border-gray-300 dark:border-slate-600 bg-gray-100 dark:bg-slate-700 flex items-center justify-center overflow-hidden">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" alt="Logo preview" class="max-w-full max-h-full object-contain">
                                </template>
                                <template x-if="!logoPreview">
                                    <div class="text-2xl font-bold text-gray-400">{{ strtoupper(substr($tenant->name, 0, 2)) }}</div>
                                </template>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($tenant->logo_path)
                                        Logo actual: {{ basename($tenant->logo_path) }}
                                    @else
                                        Sin logo configurado
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Logo Upload Type --}}
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_upload_type" value="file" x-model="logoType" class="text-blue-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Subir archivo</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="logo_upload_type" value="svg" x-model="logoType" class="text-blue-600">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Pegar código SVG</span>
                            </label>
                        </div>

                        {{-- File Upload --}}
                        <div x-show="logoType === 'file'" class="space-y-2">
                            <input type="file" name="logo_file" accept=".png,.jpg,.jpeg,.svg" @change="handleFileUpload($event)"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400 cursor-pointer">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Formatos: PNG, JPG, SVG. Tamaño máximo: 2MB</p>
                        </div>

                        {{-- SVG Code --}}
                        <div x-show="logoType === 'svg'" class="space-y-2">
                            <textarea name="logo_svg" rows="4" x-model="svgCode" @input="updateSvgPreview()"
                                      placeholder='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">...</svg>'
                                      class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pega el código SVG completo (debe comenzar con &lt;svg)</p>
                        </div>
                    </div>

                    {{-- Colores --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                        <div>
                            <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Color Primario
                                <span class="text-xs text-gray-400 ml-1">(Botones, enlaces activos)</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="primary_color" name="primary_color" x-model="primaryColor"
                                       class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                                <input type="text" x-model="primaryColor"
                                       class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase font-mono"
                                       maxlength="7">
                            </div>
                        </div>
                        <div>
                            <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Color Secundario
                                <span class="text-xs text-gray-400 ml-1">(Fondo sidebar modo oscuro)</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="secondary_color" name="secondary_color" x-model="secondaryColor"
                                       class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                                <input type="text" x-model="secondaryColor"
                                       class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase font-mono"
                                       maxlength="7">
                            </div>
                        </div>
                    </div>

                    {{-- Preview de Sidebar --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-slate-700">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Vista previa del Sidebar</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Sidebar Light --}}
                            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 shadow-sm">
                                <div class="px-3 py-2 bg-gray-100 border-b border-gray-200">
                                    <span class="text-xs font-medium text-gray-500">Modo Claro</span>
                                </div>
                                <div class="w-full h-48 bg-white p-3">
                                    {{-- Logo area --}}
                                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden" :style="{ backgroundColor: primaryColor + '15' }">
                                            <template x-if="logoPreview">
                                                <img :src="logoPreview" alt="Logo" class="w-6 h-6 object-contain">
                                            </template>
                                            <template x-if="!logoPreview">
                                                <span class="text-xs font-bold" :style="{ color: primaryColor }">{{ strtoupper(substr($tenant->name, 0, 2)) }}</span>
                                            </template>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800 truncate">{{ $tenant->name }}</span>
                                    </div>
                                    {{-- Menu items --}}
                                    <div class="space-y-1">
                                        <div class="px-3 py-2 rounded-lg text-white text-xs" :style="{ backgroundColor: primaryColor }">
                                            Dashboard (Activo)
                                        </div>
                                        <div class="px-3 py-2 rounded-lg text-gray-600 text-xs hover:bg-gray-100">
                                            Facturas
                                        </div>
                                        <div class="px-3 py-2 rounded-lg text-gray-600 text-xs hover:bg-gray-100">
                                            Terceros
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sidebar Dark --}}
                            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-slate-600 shadow-sm">
                                <div class="px-3 py-2 bg-gray-800 border-b border-gray-700">
                                    <span class="text-xs font-medium text-gray-400">Modo Oscuro</span>
                                </div>
                                <div class="w-full h-48 p-3" :style="{ backgroundColor: secondaryColor }">
                                    {{-- Logo area --}}
                                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-white/10">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden bg-white/10">
                                            <template x-if="logoPreview">
                                                <img :src="logoPreview" alt="Logo" class="w-6 h-6 object-contain">
                                            </template>
                                            <template x-if="!logoPreview">
                                                <span class="text-xs font-bold text-white">{{ strtoupper(substr($tenant->name, 0, 2)) }}</span>
                                            </template>
                                        </div>
                                        <span class="text-sm font-medium text-white truncate">{{ $tenant->name }}</span>
                                    </div>
                                    {{-- Menu items --}}
                                    <div class="space-y-1">
                                        <div class="px-3 py-2 rounded-lg text-white text-xs" :style="{ backgroundColor: primaryColor }">
                                            Dashboard (Activo)
                                        </div>
                                        <div class="px-3 py-2 rounded-lg text-white/70 text-xs">
                                            Facturas
                                        </div>
                                        <div class="px-3 py-2 rounded-lg text-white/70 text-xs">
                                            Terceros
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Preview de Botones --}}
                    <div class="p-4 rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Vista previa de elementos</p>
                        <div class="flex flex-wrap gap-4">
                            <button type="button" class="px-4 py-2 rounded-lg text-white font-medium" :style="{ backgroundColor: primaryColor }">
                                Botón Primario
                            </button>
                            <button type="button" class="px-4 py-2 rounded-lg text-white font-medium" :style="{ backgroundColor: secondaryColor }">
                                Botón Secundario
                            </button>
                            <span class="px-3 py-2 rounded-lg text-sm font-medium" :style="{ backgroundColor: primaryColor + '20', color: primaryColor }">
                                Badge
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            :disabled="isSubmitting"
                            :class="isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 cursor-pointer'"
                            class="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg font-medium transition-colors">
                        <template x-if="!isSubmitting">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="isSubmitting">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isSubmitting ? 'Guardando...' : 'Guardar Cambios'"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tab: Usuarios --}}
    <div x-show="activeTab === 'users'" x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Usuarios del Tenant</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $users->count() }} usuario(s)</span>
            </div>
            <div class="overflow-x-auto">
                @if($users->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-slate-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Último Login</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-sm font-medium text-blue-600 dark:text-blue-400">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            {{ $user->role->name ?? 'Sin rol' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->status === 'active')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Activo</span>
                                        @elseif($user->status === 'pending_approval')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">Pendiente</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ ucfirst($user->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer" title="Ver usuario">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No hay usuarios registrados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab: Danger Zone --}}
    <div x-show="activeTab === 'danger'" x-cloak>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-red-200 dark:border-red-900/50">
            <div class="px-6 py-4 border-b border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-900/20 rounded-t-xl">
                <h2 class="text-lg font-semibold text-red-700 dark:text-red-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Zona de Peligro
                </h2>
            </div>
            <div class="p-6 space-y-6">
                {{-- Suspender/Activar --}}
                <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-slate-700">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $tenant->status === 'active' ? 'Suspender Tenant' : 'Activar Tenant' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $tenant->status === 'active'
                                ? 'El tenant no podrá acceder al sistema mientras esté suspendido.'
                                : 'Restaurar el acceso al sistema para este tenant.' }}
                        </p>
                    </div>
                    <form action="{{ route('admin.tenants.toggle-status', $tenant) }}" method="POST">
                        @csrf
                        @if($tenant->status === 'active')
                            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-colors cursor-pointer">
                                Suspender
                            </button>
                        @else
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors cursor-pointer">
                                Activar
                            </button>
                        @endif
                    </form>
                </div>

                {{-- Eliminar --}}
                <div class="p-4 rounded-lg border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-900/20">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">Eliminar Tenant</h3>
                            <p class="text-sm text-red-600 dark:text-red-400/80 mt-1">
                                Esta acción es irreversible. Se eliminarán todos los datos asociados: usuarios, facturas, terceros, importaciones y archivos.
                            </p>
                        </div>
                    </div>
                    <div class="mt-4" x-data="{ confirmText: '', tenantName: '{{ $tenant->name }}' }">
                        <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                            Escribe <strong class="font-mono">{{ $tenant->name }}</strong> para confirmar:
                        </p>
                        <input type="text" x-model="confirmText" placeholder="Nombre del tenant"
                               class="w-full px-4 py-2 rounded-lg border border-red-300 dark:border-red-800 bg-white dark:bg-slate-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 mb-3">
                        <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    :disabled="confirmText !== tenantName"
                                    :class="confirmText === tenantName ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-red-300 dark:bg-red-900/50 cursor-not-allowed'"
                                    class="w-full px-4 py-2 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar Tenant Permanentemente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

