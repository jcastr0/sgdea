<div class="import-wizard" wire:poll.500ms="$refresh">
    {{-- Header con título y descripción --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Centro de Importación</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Importa facturas desde archivos Excel de manera fácil y rápida</p>
    </div>

    {{-- Stepper Visual --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            {{-- Paso 1 --}}
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300
                    {{ $currentStep >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-500' }}">
                    @if($currentStep > 1)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <span class="text-sm font-semibold">1</span>
                    @endif
                </div>
                <span class="ml-3 text-sm font-medium {{ $currentStep >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500' }}">
                    Seleccionar Archivo
                </span>
            </div>

            {{-- Línea conectora --}}
            <div class="flex-1 mx-4 h-1 rounded {{ $currentStep > 1 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-slate-700' }}"></div>

            {{-- Paso 2 --}}
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300
                    {{ $currentStep >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-500' }}">
                    @if($currentStep > 2)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <span class="text-sm font-semibold">2</span>
                    @endif
                </div>
                <span class="ml-3 text-sm font-medium {{ $currentStep >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500' }}">
                    Vista Previa
                </span>
            </div>

            {{-- Línea conectora --}}
            <div class="flex-1 mx-4 h-1 rounded {{ $currentStep > 2 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-slate-700' }}"></div>

            {{-- Paso 3 --}}
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300
                    {{ $currentStep >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-500' }}">
                    @if($currentStep > 3)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <span class="text-sm font-semibold">3</span>
                    @endif
                </div>
                <span class="ml-3 text-sm font-medium {{ $currentStep >= 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500' }}">
                    Procesando
                </span>
            </div>

            {{-- Línea conectora --}}
            <div class="flex-1 mx-4 h-1 rounded {{ $currentStep > 3 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-slate-700' }}"></div>

            {{-- Paso 4 --}}
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full transition-all duration-300
                    {{ $currentStep >= 4 ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-slate-700 text-gray-500' }}">
                    @if($currentStep >= 4)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <span class="text-sm font-semibold">4</span>
                    @endif
                </div>
                <span class="ml-3 text-sm font-medium {{ $currentStep >= 4 ? 'text-green-600 dark:text-green-400' : 'text-gray-500' }}">
                    Resultados
                </span>
            </div>
        </div>
    </div>

    {{-- Contenido según el paso actual --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">

        {{-- ==================== PASO 1: Selección de archivo ==================== --}}
        @if($currentStep === 1)
        <div class="p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Seleccionar archivo Excel</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sube tu archivo Excel con los datos de las facturas</p>
                </div>
            </div>

            {{-- Zona de Drag & Drop --}}
            <div x-data="{ isDragging: false }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                 :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/20': isDragging }"
                 class="relative border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl p-8 text-center transition-all duration-200 hover:border-blue-400">

                <input type="file"
                       wire:model="excelFile"
                       accept=".xlsx,.xls,.csv"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       x-ref="fileInput">

                <div class="space-y-4">
                    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    @if($fileName)
                        <div class="flex items-center justify-center gap-2 text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium">{{ $fileName }}</span>
                            <span class="text-sm text-gray-500">({{ $this->formatBytes($fileSize) }})</span>
                        </div>
                    @else
                        <div>
                            <p class="text-gray-600 dark:text-gray-300 font-medium">
                                Arrastra y suelta tu archivo aquí
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                o <span class="text-blue-600 dark:text-blue-400 underline cursor-pointer">haz clic para seleccionar</span>
                            </p>
                        </div>
                    @endif

                    <div class="flex items-center justify-center gap-4 text-xs text-gray-400">
                        <span>📊 .xlsx</span>
                        <span>📊 .xls</span>
                        <span>📄 .csv</span>
                        <span>•</span>
                        <span>Máx. 10MB</span>
                    </div>
                </div>

                {{-- Loading indicator --}}
                <div wire:loading wire:target="excelFile" class="absolute inset-0 bg-white/80 dark:bg-slate-800/80 flex items-center justify-center rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">Cargando archivo...</span>
                    </div>
                </div>
            </div>

            {{-- Error de validación --}}
            @error('excelFile')
                <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                </div>
            @enderror

            {{-- Opciones de importación --}}
            <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Opciones de importación</h3>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="validateDuplicates"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Validar duplicados</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Previene importar facturas que ya existen (por CUFE)</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="createTerceros"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Crear terceros automáticamente</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Si el cliente/proveedor no existe, se crea automáticamente</p>
                    </div>
                </label>
            </div>

            {{-- Botón de acción --}}
            <div class="mt-6 flex justify-end">
                <button wire:click="validateAndPreview"
                        wire:loading.attr="disabled"
                        wire:target="validateAndPreview"
                        @if(!$fileName) disabled @endif
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="validateAndPreview">Continuar</span>
                    <span wire:loading wire:target="validateAndPreview" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Validando...
                    </span>
                    <svg wire:loading.remove wire:target="validateAndPreview" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        {{-- ==================== PASO 2: Vista previa ==================== --}}
        @if($currentStep === 2)
        <div class="p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Vista previa de datos</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Revisa los datos antes de importar</p>
                </div>
            </div>

            {{-- Info del archivo --}}
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $fileName }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-full">
                            {{ $totalRows }} registros
                        </span>
                        <span class="text-gray-500">{{ $this->formatBytes($fileSize) }}</span>
                    </div>
                </div>
            </div>

            {{-- Mapeo de columnas detectado --}}
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Columnas detectadas</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($columnMapping as $campo => $index)
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ str_replace('_', ' ', ucfirst($campo)) }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Tabla de preview --}}
            <div class="mb-6 overflow-x-auto">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Primeras 5 filas</h3>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">#</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Factura</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">NIT</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Cliente</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Fecha</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-600">
                        @foreach($previewRows as $preview)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 text-gray-500">{{ $preview['fila'] }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $preview['valores']['numero_factura'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $preview['valores']['nit'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $preview['valores']['nombre_cliente'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $preview['valores']['fecha_factura'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white font-medium">{{ $preview['valores']['total'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Opciones seleccionadas --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Opciones de importación</h3>
                <div class="flex flex-wrap gap-4 text-sm">
                    <span class="flex items-center gap-2">
                        @if($validateDuplicates)
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                        <span class="text-gray-600 dark:text-gray-300">Validar duplicados</span>
                    </span>
                    <span class="flex items-center gap-2">
                        @if($createTerceros)
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                        <span class="text-gray-600 dark:text-gray-300">Crear terceros automáticamente</span>
                    </span>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-between">
                <button wire:click="previousStep"
                        class="px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </button>
                <button wire:click="confirmAndProcess"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Iniciar Importación
                </button>
            </div>
        </div>
        @endif

        {{-- ==================== PASO 3: Procesando ==================== --}}
        @if($currentStep === 3)
        <div class="p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Importando datos...</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Por favor espera mientras procesamos el archivo</p>
                </div>
            </div>

            {{-- Barra de progreso --}}
            <div class="mb-6">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600 dark:text-gray-300">Progreso</span>
                    <span class="font-medium text-blue-600 dark:text-blue-400">{{ $this->getProgressPercentage() }}%</span>
                </div>
                <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-300 relative"
                         style="width: {{ $this->getProgressPercentage() }}%">
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Procesando registro {{ $processedRows }} de {{ $totalRows }}
                </div>
            </div>

            {{-- Registro actual --}}
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Procesando factura:</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $currentRecord ?: 'Iniciando...' }}</p>
                    </div>
                </div>
            </div>

            {{-- Contadores en tiempo real --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $successCount }}</div>
                    <div class="text-sm text-green-700 dark:text-green-300">Exitosos</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $duplicateCount }}</div>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300">Duplicados</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $errorCount }}</div>
                    <div class="text-sm text-red-700 dark:text-red-300">Errores</div>
                </div>
            </div>

            {{-- Log de procesamiento (últimos 5) --}}
            @if(count($processingLog) > 0)
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Últimos registros procesados</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach(array_slice($processingLog, -5) as $log)
                        <div class="flex items-center gap-3 p-3 rounded-lg text-sm
                            {{ $log['status'] === 'success' ? 'bg-green-50 dark:bg-green-900/20' : '' }}
                            {{ $log['status'] === 'duplicate' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}
                            {{ $log['status'] === 'error' ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                            @if($log['status'] === 'success')
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($log['status'] === 'duplicate')
                                <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            @else
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            <span class="font-medium text-gray-700 dark:text-gray-300">Fila {{ $log['fila'] }}:</span>
                            <span class="text-gray-600 dark:text-gray-400">{{ $log['factura'] }}</span>
                            <span class="text-gray-500 dark:text-gray-500 ml-auto">{{ $log['message'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Mensaje de no cerrar --}}
            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        <strong>No cierres esta ventana</strong> mientras se procesa la importación.
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================== PASO 4: Resultados ==================== --}}
        @if($currentStep === 4)
        <div class="p-6 sm:p-8">
            {{-- Header con estado --}}
            <div class="flex items-center gap-3 mb-6">
                @if($errorCount === 0 && !isset($summary['fatal_error']))
                    <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-green-600 dark:text-green-400">¡Importación completada!</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Todos los registros fueron procesados correctamente</p>
                    </div>
                @else
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">Importación completada con observaciones</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Algunos registros tuvieron problemas</p>
                    </div>
                @endif
            </div>

            {{-- Resumen de resultados --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $summary['total'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total procesados</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $summary['success'] ?? 0 }}</div>
                    <div class="text-sm text-green-700 dark:text-green-300">Exitosos</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-center">
                    <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $summary['duplicates'] ?? 0 }}</div>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300">Duplicados</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-center">
                    <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $summary['errors'] ?? 0 }}</div>
                    <div class="text-sm text-red-700 dark:text-red-300">Errores</div>
                </div>
            </div>

            {{-- Errores detallados si existen --}}
            @if(count($importErrors) > 0)
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                <h3 class="text-sm font-semibold text-red-700 dark:text-red-300 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Errores encontrados
                </h3>
                <ul class="space-y-1 text-sm text-red-600 dark:text-red-400 max-h-40 overflow-y-auto">
                    @foreach($importErrors as $error)
                        <li class="flex items-start gap-2">
                            <span class="text-red-400">•</span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Acciones --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-between">
                <button wire:click="resetWizard"
                        class="px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Nueva importación
                </button>

                <div class="flex gap-3">
                    @if($importLogId)
                    <a href="{{ route('import.report', $importLogId) }}"
                       class="px-6 py-3 border border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ver reporte
                    </a>
                    @endif

                    <a href="{{ route('facturas.index') }}"
                       class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ir a Facturas
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

