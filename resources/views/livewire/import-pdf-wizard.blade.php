<div class="import-pdf-wizard">
    {{-- Header con título --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Importación de PDFs</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Asocia archivos PDF a las facturas existentes mediante CUFE</p>
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
                    Subir PDFs
                </span>
            </div>

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
                    Análisis
                </span>
            </div>

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

        {{-- ==================== PASO 1: Subir PDFs ==================== --}}
        @if($currentStep === 1)
        <div class="p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Seleccionar archivos PDF</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sube uno o más archivos PDF con facturas</p>
                </div>
            </div>

            {{-- Zona de Drag & Drop --}}
            <div x-data="{ isDragging: false }"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                 :class="{ 'border-red-500 bg-red-50 dark:bg-red-900/20': isDragging }"
                 class="relative border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl p-8 text-center transition-all duration-200 hover:border-red-400">

                <input type="file"
                       wire:model="pdfFiles"
                       accept=".pdf"
                       multiple
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       x-ref="fileInput">

                <div class="space-y-4">
                    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    @if(count($uploadedFiles) > 0)
                        <div class="text-green-600 dark:text-green-400">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="font-medium">{{ count($uploadedFiles) }} archivo(s) seleccionado(s)</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Tamaño total: {{ $this->formatBytes($totalFilesSize) }}</p>
                        </div>
                    @else
                        <div>
                            <p class="text-gray-600 dark:text-gray-300 font-medium">
                                Arrastra y suelta tus archivos PDF aquí
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                o <span class="text-red-600 dark:text-red-400 underline cursor-pointer">haz clic para seleccionar</span>
                            </p>
                        </div>
                    @endif

                    <div class="flex items-center justify-center gap-4 text-xs text-gray-400">
                        <span>📄 Solo archivos PDF</span>
                        <span>•</span>
                        <span>Múltiples archivos permitidos</span>
                        <span>•</span>
                        <span>Máx. 50MB por archivo</span>
                    </div>
                </div>

                {{-- Loading indicator --}}
                <div wire:loading wire:target="pdfFiles" class="absolute inset-0 bg-white/80 dark:bg-slate-800/80 flex items-center justify-center rounded-xl">
                    <div class="flex items-center gap-3">
                        <svg class="animate-spin h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300">Cargando archivos...</span>
                    </div>
                </div>
            </div>

            {{-- Lista de archivos seleccionados --}}
            @if(count($uploadedFiles) > 0)
            <div class="mt-6 space-y-2">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Archivos seleccionados:</h3>
                <div class="max-h-48 overflow-y-auto space-y-2">
                    @foreach($uploadedFiles as $file)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <span class="flex-1 text-sm text-gray-700 dark:text-gray-300 truncate">{{ $file['name'] }}</span>
                        <span class="text-xs text-gray-500">{{ $this->formatBytes($file['size']) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Error de validación --}}
            @error('pdfFiles.*')
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
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Opciones de procesamiento</h3>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="extractAdditionalData"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Extraer datos adicionales</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Extrae motonave, TRB, descripción del servicio y otros datos del PDF</p>
                    </div>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="saveToStaging"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Guardar en staging</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Si un CUFE no tiene factura asociada, guardar el PDF para procesamiento posterior</p>
                    </div>
                </label>
            </div>

            {{-- Botón de acción --}}
            <div class="mt-6 flex justify-end">
                <button wire:click="analyzeFiles"
                        wire:loading.attr="disabled"
                        wire:target="analyzeFiles,pdfFiles"
                        :disabled="$wire.uploadedFiles.length === 0"
                        @if(empty($uploadedFiles)) disabled @endif
                        class="cursor-pointer px-6 py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="analyzeFiles">Analizar PDFs</span>
                    <span wire:loading wire:target="analyzeFiles" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Analizando...
                    </span>
                    <svg wire:loading.remove wire:target="analyzeFiles" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        {{-- ==================== PASO 2: Análisis de CUFEs ==================== --}}
        @if($currentStep === 2)
        <div class="p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Análisis de CUFEs encontrados</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Revisa los CUFEs detectados y su estado de asociación</p>
                </div>
            </div>

            {{-- Resumen general --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCufesFound }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">CUFEs encontrados</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $cufesAvailable }}</div>
                    <div class="text-xs text-green-700 dark:text-green-300">Disponibles para asociar</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $cufesAlreadyAssociated }}</div>
                    <div class="text-xs text-yellow-700 dark:text-yellow-300">Ya tienen PDF</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $cufesNotFound }}</div>
                    <div class="text-xs text-red-700 dark:text-red-300">Sin factura en BD</div>
                </div>
            </div>

            {{-- Detalle por archivo --}}
            <div class="space-y-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Detalle por archivo:</h3>

                @foreach($analysisResults as $index => $fileData)
                <div class="border border-gray-200 dark:border-slate-600 rounded-lg overflow-hidden">
                    <div class="p-4 bg-gray-50 dark:bg-slate-700/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $fileData['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $fileData['pages'] }} páginas • {{ $this->formatBytes($fileData['size']) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                {{ $fileData['total_cufes'] }} CUFEs
                            </span>
                        </div>
                    </div>

                    {{-- CUFEs del archivo --}}
                    @if($fileData['total_cufes'] > 0)
                    <div class="p-4 space-y-2 max-h-48 overflow-y-auto">
                        @foreach($fileData['comparacion']['disponibles'] as $cufeData)
                        <div class="flex items-center gap-3 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm">
                            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium text-green-700 dark:text-green-300">{{ $cufeData['numero_factura'] }}</span>
                            <span class="text-green-600 dark:text-green-400 text-xs">Listo para asociar</span>
                            <span class="text-gray-500 text-xs ml-auto">Pág. {{ implode(', ', $cufeData['paginas']) }}</span>
                        </div>
                        @endforeach

                        @foreach($fileData['comparacion']['ya_asociadas'] as $cufeData)
                        <div class="flex items-center gap-3 p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-sm">
                            <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="font-medium text-yellow-700 dark:text-yellow-300">{{ $cufeData['numero_factura'] }}</span>
                            <span class="text-yellow-600 dark:text-yellow-400 text-xs">Ya tiene PDF</span>
                        </div>
                        @endforeach

                        @foreach($fileData['comparacion']['no_encontradas'] as $cufeData)
                        <div class="flex items-center gap-3 p-2 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm">
                            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="font-mono text-xs text-red-600 dark:text-red-400">{{ substr($cufeData['cufe'], 0, 24) }}...</span>
                            <span class="text-red-500 text-xs">Sin factura en BD</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="p-4 text-center text-gray-500">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm">No se encontraron CUFEs en este archivo</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Opciones seleccionadas --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Opciones de procesamiento</h3>
                <div class="flex flex-wrap gap-4 text-sm">
                    <span class="flex items-center gap-2">
                        @if($extractAdditionalData)
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                        <span class="text-gray-600 dark:text-gray-300">Extraer datos adicionales</span>
                    </span>
                    <span class="flex items-center gap-2">
                        @if($saveToStaging)
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                        <span class="text-gray-600 dark:text-gray-300">Guardar en staging</span>
                    </span>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex justify-between">
                <button wire:click="previousStep"
                        class="cursor-pointer px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Volver
                </button>

                @if($cufesAvailable > 0 || ($saveToStaging && $cufesNotFound > 0))
                <button wire:click="confirmAndProcess"
                        wire:loading.attr="disabled"
                        wire:target="confirmAndProcess"
                        class="cursor-pointer px-6 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="confirmAndProcess">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="confirmAndProcess">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="confirmAndProcess">Procesar {{ $cufesAvailable }} asociaciones</span>
                    <span wire:loading wire:target="confirmAndProcess">Procesando...</span>
                </button>
                @else
                <div class="px-6 py-3 bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400 font-medium rounded-lg">
                    No hay CUFEs disponibles para procesar
                </div>
                @endif
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Procesando PDFs...</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Asociando archivos a las facturas</p>
                </div>
            </div>

            {{-- Barra de progreso --}}
            <div class="mb-6">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600 dark:text-gray-300">Progreso</span>
                    <span class="font-medium text-blue-600 dark:text-blue-400">{{ $this->getProgressPercentage() }}%</span>
                </div>
                <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-red-500 to-red-600 rounded-full transition-all duration-300 relative"
                         style="width: {{ $this->getProgressPercentage() }}%">
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Archivo {{ $processedFiles }} de {{ count($analysisResults) }}
                </div>
            </div>

            {{-- Archivo actual --}}
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Procesando:</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $currentFile ?: 'Iniciando...' }}</p>
                        @if($currentCufe)
                        <p class="text-xs text-gray-500 mt-1">CUFE: {{ $currentCufe }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Contadores en tiempo real --}}
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $successCount }}</div>
                    <div class="text-xs text-green-700 dark:text-green-300">Asociados</div>
                </div>
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stagingCount }}</div>
                    <div class="text-xs text-blue-700 dark:text-blue-300">Staging</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $skippedCount }}</div>
                    <div class="text-xs text-yellow-700 dark:text-yellow-300">Omitidos</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $errorCount }}</div>
                    <div class="text-xs text-red-700 dark:text-red-300">Errores</div>
                </div>
            </div>

            {{-- Log de procesamiento --}}
            @if(count($processingLog) > 0)
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Últimos registros procesados</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach(array_slice($processingLog, -5) as $log)
                        <div class="flex items-center gap-3 p-3 rounded-lg text-sm
                            {{ $log['status'] === 'success' ? 'bg-green-50 dark:bg-green-900/20' : '' }}
                            {{ $log['status'] === 'staging' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}
                            {{ $log['status'] === 'skipped' ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}
                            {{ $log['status'] === 'error' ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                            @if($log['status'] === 'success')
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @elseif($log['status'] === 'staging')
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            @elseif($log['status'] === 'skipped')
                                <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $log['factura'] }}</span>
                            <span class="text-gray-500 dark:text-gray-500 ml-auto">{{ $log['message'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Advertencia --}}
            <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        <strong>No cierres esta ventana</strong> mientras se procesan los PDFs.
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
                        <h2 class="text-lg font-semibold text-green-600 dark:text-green-400">¡Procesamiento completado!</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Los PDFs fueron procesados correctamente</p>
                    </div>
                @else
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">Procesamiento completado con observaciones</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Algunos archivos tuvieron problemas</p>
                    </div>
                @endif
            </div>

            {{-- Resumen de resultados --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
                <div class="p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_files'] ?? 0 }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Archivos</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $summary['success'] ?? 0 }}</div>
                    <div class="text-xs text-green-700 dark:text-green-300">Asociados</div>
                </div>
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $summary['staging'] ?? 0 }}</div>
                    <div class="text-xs text-blue-700 dark:text-blue-300">En staging</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $summary['skipped'] ?? 0 }}</div>
                    <div class="text-xs text-yellow-700 dark:text-yellow-300">Omitidos</div>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $summary['errors'] ?? 0 }}</div>
                    <div class="text-xs text-red-700 dark:text-red-300">Errores</div>
                </div>
            </div>

            {{-- Errores detallados --}}
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
                        class="cursor-pointer px-6 py-3 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Nueva importación
                </button>

                <div class="flex gap-3">
                    @if($importLogId)
                    <a href="{{ route('import.report', $importLogId) }}"
                       class="cursor-pointer px-6 py-3 border border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ver reporte
                    </a>
                    @endif

                    <a href="{{ route('facturas.index') }}"
                       class="cursor-pointer px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Ver Facturas
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

