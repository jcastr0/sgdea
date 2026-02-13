<div class="max-w-4xl mx-auto" wire:loading.class="opacity-75 pointer-events-none" wire:target="nextStep,prevStep,createTenant,goToStep">
    {{-- Indicador de carga global --}}
    <div wire:loading wire:target="nextStep,prevStep,createTenant,goToStep" class="fixed top-4 right-4 z-50 flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-lg shadow-lg">
        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="font-medium">Procesando...</span>
    </div>

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors cursor-pointer mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Tenants
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Crear Nuevo Tenant</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Complete los pasos para crear una nueva empresa en el sistema</p>
    </div>

    {{-- Stepper --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            @foreach(['Información', 'Configuración', 'Branding', 'Administrador', 'Confirmar'] as $index => $step)
                <div class="flex items-center {{ $index < 4 ? 'flex-1' : '' }}">
                    {{-- Step Circle --}}
                    <button type="button" wire:click="goToStep({{ $index + 1 }})"
                        class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold transition-all duration-300 cursor-pointer
                            {{ $currentStep === $index + 1 ? 'bg-blue-600 text-white' : '' }}
                            {{ $currentStep > $index + 1 ? 'bg-green-500 text-white' : '' }}
                            {{ $currentStep < $index + 1 ? 'bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-gray-400' : '' }}">
                        @if($currentStep > $index + 1)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </button>
                    {{-- Step Label --}}
                    <span class="hidden sm:block ml-2 text-sm font-medium transition-colors
                        {{ $currentStep === $index + 1 ? 'text-blue-600 dark:text-blue-400' : '' }}
                        {{ $currentStep > $index + 1 ? 'text-green-600 dark:text-green-400' : '' }}
                        {{ $currentStep < $index + 1 ? 'text-gray-400 dark:text-gray-500' : '' }}">
                        {{ $step }}
                    </span>
                    {{-- Connector Line --}}
                    @if($index < 4)
                        <div class="flex-1 h-1 mx-4 rounded transition-colors duration-300
                            {{ $currentStep > $index + 1 ? 'bg-green-500' : 'bg-gray-200 dark:bg-slate-700' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Error general --}}
    @error('general')
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            </div>
        </div>
    @enderror

    {{-- PASO 1: Información Básica --}}
    @if($currentStep === 1)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Información de la Empresa
            </h2>

            <div class="space-y-6">
                {{-- Nombre de la Empresa --}}
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre de la Empresa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="company_name" wire:model.live="company_name"
                        class="w-full px-4 py-3 rounded-lg border bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                            {{ $errors->has('company_name') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}"
                        placeholder="Ej: Marítimos Arboleda S.A.S">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Identificador (slug) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="slug" wire:model="slug"
                            class="flex-1 px-4 py-3 rounded-lg border bg-gray-50 dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                {{ $errors->has('slug') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}"
                            placeholder="maritimos-arboleda">
                        <button type="button" wire:click="generateSlug"
                            class="px-4 py-3 bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-500 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Se genera automáticamente a partir del nombre. Solo letras minúsculas, números y guiones.</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dominio --}}
                <div>
                    <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Dominio de Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">@</span>
                        <input type="text" id="domain" wire:model="domain"
                            class="w-full pl-8 pr-4 py-3 rounded-lg border bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                {{ $errors->has('domain') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}"
                            placeholder="maritimosarboleda.com">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dominio de email permitido para los usuarios (ej: empresa.com)</p>
                    @error('domain')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    @endif

    {{-- PASO 2: Configuración --}}
    @if($currentStep === 2)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Configuración del Tenant
            </h2>

            <div class="space-y-8">
                {{-- Plan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Plan de Suscripción <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($plans as $key => $planData)
                            <button type="button" wire:click="$set('plan', '{{ $key }}')"
                                class="relative p-4 rounded-lg border-2 cursor-pointer transition-all text-left
                                    {{ $plan === $key
                                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                        : 'border-gray-200 dark:border-slate-600 hover:border-gray-300 dark:hover:border-slate-500' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $planData['name'] }}</span>
                                    @if($plan === $key)
                                        <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $planData['description'] }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $planData['limits'] }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Límites personalizados --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="max_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Límite de Usuarios
                        </label>
                        <input type="number" id="max_users" wire:model="max_users" min="0" max="1000"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0 = Sin límite</p>
                    </div>
                    <div>
                        <label for="max_storage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Almacenamiento (GB)
                        </label>
                        <input type="number" id="max_storage" wire:model="max_storage" min="0" max="1000"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0 = Sin límite</p>
                    </div>
                </div>

                {{-- Estado inicial --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Estado Inicial <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach($statusOptions as $key => $option)
                            <button type="button" wire:click="$set('status', '{{ $key }}')"
                                class="relative p-4 rounded-lg border-2 cursor-pointer transition-all text-left
                                    {{ $status === $key
                                        ? 'border-' . $option['color'] . '-500 bg-' . $option['color'] . '-50 dark:bg-' . $option['color'] . '-900/20'
                                        : 'border-gray-200 dark:border-slate-600 hover:border-gray-300 dark:hover:border-slate-500' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $option['label'] }}</span>
                                    @if($status === $key)
                                        <div class="w-4 h-4 bg-{{ $option['color'] }}-500 rounded-full flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $option['description'] }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- PASO 3: Branding --}}
    @if($currentStep === 3)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                Branding y Colores
            </h2>

            <div class="space-y-8">
                {{-- Paletas predefinidas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Paletas de Colores Predefinidas
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        @foreach($colorPresets as $index => $preset)
                            <button type="button" wire:click="applyColorPreset({{ $index }})"
                                class="p-3 rounded-lg border-2 cursor-pointer transition-all hover:shadow-md
                                    {{ $color_primary === $preset['primary'] && $color_secondary === $preset['secondary']
                                        ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800'
                                        : 'border-gray-200 dark:border-slate-600' }}">
                                <div class="flex gap-1 mb-2">
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $preset['primary'] }}"></div>
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $preset['secondary'] }}"></div>
                                    <div class="w-6 h-6 rounded" style="background-color: {{ $preset['accent'] }}"></div>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $preset['name'] }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Colores personalizados --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="color_primary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color Primario
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="color_primary" wire:model.live="color_primary"
                                class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                            <input type="text" wire:model.live="color_primary"
                                class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase font-mono"
                                maxlength="7">
                        </div>
                    </div>
                    <div>
                        <label for="color_secondary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color Secundario
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="color_secondary" wire:model.live="color_secondary"
                                class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                            <input type="text" wire:model.live="color_secondary"
                                class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase font-mono"
                                maxlength="7">
                        </div>
                    </div>
                    <div>
                        <label for="color_accent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Color de Acento
                        </label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="color_accent" wire:model.live="color_accent"
                                class="w-12 h-12 rounded-lg border border-gray-300 dark:border-slate-600 cursor-pointer">
                            <input type="text" wire:model.live="color_accent"
                                class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase font-mono"
                                maxlength="7">
                        </div>
                    </div>
                </div>

                {{-- Preview de colores --}}
                <div class="p-4 rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Vista previa de botones</p>
                    <div class="flex flex-wrap gap-4">
                        <button type="button" class="px-4 py-2 rounded-lg text-white font-medium transition-transform hover:scale-105" style="background-color: {{ $color_primary }}">
                            Botón Primario
                        </button>
                        <button type="button" class="px-4 py-2 rounded-lg text-white font-medium transition-transform hover:scale-105" style="background-color: {{ $color_secondary }}">
                            Botón Secundario
                        </button>
                        <button type="button" class="px-4 py-2 rounded-lg text-white font-medium transition-transform hover:scale-105" style="background-color: {{ $color_accent }}">
                            Botón Acento
                        </button>
                    </div>
                </div>

                {{-- Logo del Tenant --}}
                <div class="p-4 rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Logo de la Empresa (Opcional)
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Opción 1: Subir archivo --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Subir imagen</label>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-20 h-20 bg-white dark:bg-slate-700 rounded-lg flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-slate-600">
                                    @if($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview" class="max-w-full max-h-full object-contain">
                                    @elseif($svg_logo)
                                        <div class="w-full h-full flex items-center justify-center p-2">
                                            {!! $svg_logo !!}
                                        </div>
                                    @else
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input type="file" wire:model="logo" accept="image/*,.svg"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50 file:cursor-pointer">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG o SVG. Máx 2MB.</p>
                                    @if($logo)
                                        <button type="button" wire:click="$set('logo', null)"
                                            class="mt-2 text-xs text-red-600 hover:text-red-700 dark:text-red-400 cursor-pointer">
                                            Eliminar
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Opción 2: Pegar SVG --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">O pegar código SVG</label>
                            <textarea wire:model.live.debounce.500ms="svg_logo" rows="4" placeholder="<svg>...</svg>"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-xs font-mono"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pegue el código SVG directamente aquí.</p>
                            @if($svg_logo)
                                <button type="button" wire:click="$set('svg_logo', '')"
                                    class="mt-2 text-xs text-red-600 hover:text-red-700 dark:text-red-400 cursor-pointer">
                                    Limpiar SVG
                                </button>
                            @endif
                            @error('svg_logo')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Toggle Modo Oscuro --}}
                <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Permitir modo oscuro</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Los usuarios podrán cambiar entre tema claro y oscuro</p>
                    </div>
                    <button type="button" wire:click="$toggle('dark_mode_enabled')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer
                            {{ $dark_mode_enabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-slate-600' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                            {{ $dark_mode_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PASO 4: Usuario Admin --}}
    @if($currentStep === 4)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Usuario Administrador
            </h2>

            <div class="space-y-6">
                {{-- Nombre --}}
                <div>
                    <label for="admin_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="admin_name" wire:model="admin_name"
                        class="w-full px-4 py-3 rounded-lg border bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                            {{ $errors->has('admin_name') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}"
                        placeholder="Juan Pérez">
                    @error('admin_name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="admin_email" wire:model="admin_email"
                        class="w-full px-4 py-3 rounded-lg border bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                            {{ $errors->has('admin_email') ? 'border-red-500' : 'border-gray-300 dark:border-slate-600' }}"
                        placeholder="admin{{ '@' . ($domain ?: 'empresa.com') }}">
                    @if($domain)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Recomendado usar un email con el dominio: <strong>{{ '@' . $domain }}</strong>
                        </p>
                    @endif
                    @error('admin_email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="admin_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Contraseña
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="text" id="admin_password" wire:model="admin_password"
                            class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors font-mono"
                            placeholder="Dejar vacío para generar automáticamente">
                        <button type="button" wire:click="generatePassword"
                            class="px-4 py-3 bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-500 transition-colors whitespace-nowrap cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Generar
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Si lo deja vacío, se generará una contraseña segura automáticamente</p>
                    @error('admin_password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Enviar email --}}
                <div class="flex items-center justify-between p-4 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20">
                    <div>
                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Enviar email de bienvenida</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">Se enviarán las credenciales de acceso al administrador</p>
                    </div>
                    <button type="button" wire:click="$toggle('send_welcome_email')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer
                            {{ $send_welcome_email ? 'bg-blue-600' : 'bg-gray-200 dark:bg-slate-600' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                            {{ $send_welcome_email ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PASO 5: Confirmación --}}
    @if($currentStep === 5)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Resumen y Confirmación
            </h2>

            <div class="space-y-6">
                {{-- Resumen de empresa --}}
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Información de la Empresa
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Nombre</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $company_name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Identificador</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $slug ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Dominio de Email</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ '@' . ($domain ?: '-') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Estado</dt>
                            <dd class="text-sm font-medium">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $status === 'trial' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : '' }}
                                    {{ $status === 'suspended' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
                                    {{ $statusOptions[$status]['label'] ?? $status }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Resumen de plan --}}
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Configuración
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Plan</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $plans[$plan]['name'] ?? $plan }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Límite de Usuarios</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $max_users > 0 ? $max_users : 'Sin límite' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Almacenamiento</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $max_storage > 0 ? $max_storage . ' GB' : 'Sin límite' }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Resumen de colores --}}
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                        Branding
                    </h3>
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg border border-gray-200 dark:border-slate-600" style="background-color: {{ $color_primary }}"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Primario</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg border border-gray-200 dark:border-slate-600" style="background-color: {{ $color_secondary }}"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Secundario</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg border border-gray-200 dark:border-slate-600" style="background-color: {{ $color_accent }}"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Acento</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Modo oscuro:</span>
                            <span class="text-sm font-medium {{ $dark_mode_enabled ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                                {{ $dark_mode_enabled ? 'Habilitado' : 'Deshabilitado' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Resumen de usuario --}}
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Usuario Administrador
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Nombre</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $admin_name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $admin_email ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Contraseña</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $admin_password ? '••••••••••••' : '(Se generará automáticamente)' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Email de Bienvenida</dt>
                            <dd class="text-sm font-medium {{ $send_welcome_email ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                                {{ $send_welcome_email ? 'Se enviará' : 'No se enviará' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    @endif

    {{-- Navigation Buttons --}}
    <div class="flex items-center justify-between mt-8">
        @if($currentStep > 1)
            <button type="button" wire:click="prevStep" wire:loading.attr="disabled" wire:target="prevStep,nextStep,createTenant"
                class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading wire:target="prevStep">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <svg wire:loading.remove wire:target="prevStep" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span wire:loading wire:target="prevStep">Procesando...</span>
                <span wire:loading.remove wire:target="prevStep">Anterior</span>
            </button>
        @else
            <div></div>
        @endif

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tenants.index') }}"
                class="px-6 py-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 font-medium transition-colors cursor-pointer">
                Cancelar
            </a>

            @if($currentStep < $totalSteps)
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled" wire:target="prevStep,nextStep,createTenant"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-lg font-medium transition-colors cursor-pointer disabled:cursor-not-allowed">
                    <span wire:loading wire:target="nextStep">Procesando...</span>
                    <span wire:loading.remove wire:target="nextStep">Siguiente</span>
                    <span wire:loading wire:target="nextStep">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <svg wire:loading.remove wire:target="nextStep" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <button type="button" wire:click="createTenant" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white rounded-lg font-medium transition-colors cursor-pointer">
                    <span wire:loading wire:target="createTenant">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="createTenant">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="createTenant">Creando...</span>
                    <span wire:loading.remove wire:target="createTenant">Crear Tenant</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Success Modal --}}
    @if($showSuccessModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-md w-full p-6 animate-in zoom-in-95 duration-200">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">¡Tenant Creado Exitosamente!</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">El tenant y el usuario administrador han sido creados.</p>

                    <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 text-left mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Credenciales de Acceso</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Email:</span>
                                <span class="text-sm font-mono text-gray-900 dark:text-white">{{ $admin_email }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Contraseña:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-mono text-gray-900 dark:text-white bg-gray-100 dark:bg-slate-600 px-2 py-1 rounded">{{ $generatedPassword ?: $admin_password }}</span>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $generatedPassword ?: $admin_password }}')"
                                        class="text-blue-500 hover:text-blue-600 cursor-pointer" title="Copiar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 p-2 bg-amber-50 dark:bg-amber-900/20 rounded border border-amber-200 dark:border-amber-800">
                            <p class="text-xs text-amber-600 dark:text-amber-400 flex items-start gap-2">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Guarda estas credenciales de forma segura. No se mostrarán de nuevo.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.tenants.show', $createdTenantId ?? 1) }}"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors cursor-pointer text-center">
                            Ver Tenant
                        </a>
                        <a href="{{ route('admin.tenants.index') }}"
                            class="flex-1 px-4 py-2 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors cursor-pointer text-center">
                            Ir al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

