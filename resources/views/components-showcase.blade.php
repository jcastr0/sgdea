@extends('layouts.showcase')

@section('title', 'Component Library')


@section('content')
<div class="space-y-12">
    {{-- Header de la página --}}
    <div class="border-b border-gray-200 dark:border-slate-700 pb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Component Library</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Biblioteca visual de todos los componentes del sistema de diseño SGDEA.
                </p>
            </div>
            {{-- Toggle Dark Mode --}}
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Modo</span>
                <button @click="$store.darkMode.toggle()"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                        :class="$store.darkMode.on ? 'bg-blue-600' : 'bg-gray-200'">
                    <span class="sr-only">Toggle dark mode</span>
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                          :class="$store.darkMode.on ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
                <span class="text-sm text-gray-600 dark:text-gray-400" x-text="$store.darkMode.on ? 'Oscuro' : 'Claro'"></span>
            </div>
        </div>
    </div>

    {{-- Navegación rápida --}}
    <nav class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-4 sticky top-28 z-10">
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Navegación Rápida</h2>
        <div class="flex flex-wrap gap-2">
            <a href="#tipografia" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Tipografía</a>
            <a href="#colores" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Colores</a>
            <a href="#botones" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Botones</a>
            <a href="#formularios" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Formularios</a>
            <a href="#cards" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Cards</a>
            <a href="#modales" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Modales</a>
            <a href="#alertas" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Alertas</a>
            <a href="#tablas" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Tablas</a>
            <a href="#paginacion" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Paginación</a>
            <a href="#navegacion" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Navegación</a>
            <a href="#badges" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Badges</a>
            <a href="#loading" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Loading</a>
            <a href="#iconos" class="px-3 py-1.5 text-sm bg-gray-100 dark:bg-slate-700 rounded-md hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">Iconos</a>
        </div>
    </nav>

    {{-- ==================== SECCIÓN 1: TIPOGRAFÍA ==================== --}}
    <section id="tipografia" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">1. Tipografía</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Headings --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Headings</h3>
                    <div class="space-y-3 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <h1 class="text-4xl font-bold">Heading 1 - text-4xl font-bold</h1>
                        <h2 class="text-3xl font-bold">Heading 2 - text-3xl font-bold</h2>
                        <h3 class="text-2xl font-semibold">Heading 3 - text-2xl font-semibold</h3>
                        <h4 class="text-xl font-semibold">Heading 4 - text-xl font-semibold</h4>
                        <h5 class="text-lg font-medium">Heading 5 - text-lg font-medium</h5>
                        <h6 class="text-base font-medium">Heading 6 - text-base font-medium</h6>
                    </div>
                </div>

                {{-- Párrafos y textos --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Párrafos y Textos</h3>
                    <div class="space-y-3 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <p class="text-base">Texto normal (text-base) - Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        <p class="text-sm">Texto pequeño (text-sm) - Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        <p class="text-xs">Texto extra pequeño (text-xs) - Lorem ipsum dolor sit amet.</p>
                        <p class="text-lg">Texto grande (text-lg) - Lorem ipsum dolor sit amet.</p>
                    </div>
                </div>

                {{-- Estilos de texto --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Estilos de Texto</h3>
                    <div class="flex flex-wrap gap-4 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <span class="font-bold">Bold</span>
                        <span class="font-semibold">Semibold</span>
                        <span class="font-medium">Medium</span>
                        <span class="font-normal">Normal</span>
                        <span class="font-light">Light</span>
                        <span class="italic">Italic</span>
                        <span class="underline">Underline</span>
                        <span class="line-through">Strikethrough</span>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Link</a>
                    </div>
                </div>

                {{-- Código de ejemplo --}}
                <div x-data="{ showCode: false }">
                    <button @click="showCode = !showCode" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        <span x-text="showCode ? 'Ocultar código' : 'Ver código'"></span>
                    </button>
                    <pre x-show="showCode" x-collapse class="mt-2 p-4 bg-slate-800 text-slate-100 rounded-lg text-sm overflow-x-auto">
<code>&lt;h1 class="text-4xl font-bold"&gt;Heading 1&lt;/h1&gt;
&lt;p class="text-base"&gt;Párrafo normal&lt;/p&gt;
&lt;span class="font-bold"&gt;Texto en negrita&lt;/span&gt;
&lt;a href="#" class="text-blue-600 hover:underline"&gt;Enlace&lt;/a&gt;</code></pre>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 2: COLORES ==================== --}}
    <section id="colores" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">2. Colores</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Colores Primarios --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Primarios (Blue)</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <div class="text-center">
                            <div class="h-16 bg-blue-50 rounded-lg"></div>
                            <span class="text-xs">50</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-100 rounded-lg"></div>
                            <span class="text-xs">100</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-200 rounded-lg"></div>
                            <span class="text-xs">200</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-300 rounded-lg"></div>
                            <span class="text-xs">300</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-400 rounded-lg"></div>
                            <span class="text-xs">400</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-500 rounded-lg"></div>
                            <span class="text-xs">500</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-600 rounded-lg"></div>
                            <span class="text-xs">600</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-700 rounded-lg"></div>
                            <span class="text-xs">700</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-800 rounded-lg"></div>
                            <span class="text-xs">800</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-900 rounded-lg"></div>
                            <span class="text-xs">900</span>
                        </div>
                    </div>
                </div>

                {{-- Colores de Estado --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Colores de Estado</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="h-16 bg-green-500 rounded-lg"></div>
                            <span class="text-sm font-medium">Success</span>
                            <span class="text-xs text-gray-500 block">green-500</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-red-500 rounded-lg"></div>
                            <span class="text-sm font-medium">Danger/Error</span>
                            <span class="text-xs text-gray-500 block">red-500</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-yellow-500 rounded-lg"></div>
                            <span class="text-sm font-medium">Warning</span>
                            <span class="text-xs text-gray-500 block">yellow-500</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-blue-500 rounded-lg"></div>
                            <span class="text-sm font-medium">Info</span>
                            <span class="text-xs text-gray-500 block">blue-500</span>
                        </div>
                    </div>
                </div>

                {{-- Grises --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Escala de Grises</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <div class="text-center">
                            <div class="h-16 bg-gray-50 rounded-lg border dark:border-slate-600"></div>
                            <span class="text-xs">50</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-100 rounded-lg"></div>
                            <span class="text-xs">100</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-200 rounded-lg"></div>
                            <span class="text-xs">200</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-300 rounded-lg"></div>
                            <span class="text-xs">300</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-400 rounded-lg"></div>
                            <span class="text-xs">400</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-500 rounded-lg"></div>
                            <span class="text-xs">500</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-600 rounded-lg"></div>
                            <span class="text-xs">600</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-700 rounded-lg"></div>
                            <span class="text-xs">700</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-800 rounded-lg"></div>
                            <span class="text-xs">800</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-gray-900 rounded-lg"></div>
                            <span class="text-xs">900</span>
                        </div>
                    </div>
                </div>

                {{-- Slate (Dark mode) --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Slate (Dark Mode)</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <div class="text-center">
                            <div class="h-16 bg-slate-600 rounded-lg"></div>
                            <span class="text-xs">600</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-slate-700 rounded-lg"></div>
                            <span class="text-xs">700</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-slate-800 rounded-lg"></div>
                            <span class="text-xs">800</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-slate-900 rounded-lg"></div>
                            <span class="text-xs">900</span>
                        </div>
                        <div class="text-center">
                            <div class="h-16 bg-slate-950 rounded-lg"></div>
                            <span class="text-xs">950</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 3: BOTONES ==================== --}}
    <section id="botones" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">3. Botones</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Variantes --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Variantes</h3>
                    <div class="flex flex-wrap gap-3 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <x-button variant="primary">Primary</x-button>
                        <x-button variant="secondary">Secondary</x-button>
                        <x-button variant="success">Success</x-button>
                        <x-button variant="danger">Danger</x-button>
                        <x-button variant="warning">Warning</x-button>
                        <x-button variant="outline">Outline</x-button>
                        <x-button variant="ghost">Ghost</x-button>
                    </div>
                </div>

                {{-- Tamaños --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Tamaños</h3>
                    <div class="flex flex-wrap items-center gap-3 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <x-button size="sm">Small</x-button>
                        <x-button size="md">Medium</x-button>
                        <x-button size="lg">Large</x-button>
                    </div>
                </div>

                {{-- Estados --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Estados</h3>
                    <div class="flex flex-wrap items-center gap-3 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <x-button>Normal</x-button>
                        <x-button disabled>Disabled</x-button>
                        <x-button loading>Loading</x-button>
                    </div>
                </div>

                {{-- Con iconos --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Con Iconos</h3>
                    <div class="flex flex-wrap gap-3 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <x-button>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar
                        </x-button>
                        <x-button variant="danger">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </x-button>
                        <x-button variant="success">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar
                        </x-button>
                    </div>
                </div>

                {{-- Código --}}
                <div x-data="{ showCode: false }">
                    <button @click="showCode = !showCode" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        <span x-text="showCode ? 'Ocultar código' : 'Ver código'"></span>
                    </button>
                    <pre x-show="showCode" x-collapse class="mt-2 p-4 bg-slate-800 text-slate-100 rounded-lg text-sm overflow-x-auto">
<code>&lt;x-button variant="primary"&gt;Primary&lt;/x-button&gt;
&lt;x-button variant="danger" size="lg"&gt;Large Danger&lt;/x-button&gt;
&lt;x-button disabled&gt;Disabled&lt;/x-button&gt;
&lt;x-button loading&gt;Loading&lt;/x-button&gt;</code></pre>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 4: FORMULARIOS ==================== --}}
    <section id="formularios" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">4. Formularios</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Text Inputs --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Text Inputs</h3>
                    <div class="grid gap-4 sm:grid-cols-2 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <x-input label="Input Normal" placeholder="Escribe algo..." />
                        <x-input label="Input Requerido" placeholder="Campo requerido" required />
                        <x-input label="Con Ayuda" placeholder="Email" helper="Ingresa tu correo electrónico" />
                        <x-input label="Con Error" placeholder="Nombre" error="Este campo es requerido" />
                        <x-input label="Deshabilitado" placeholder="No editable" disabled value="Valor fijo" />
                        <x-input label="Solo Lectura" placeholder="Solo lectura" readonly value="Solo lectura" />
                    </div>
                </div>

                {{-- Input con iconos --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Inputs con Iconos</h3>
                    <div class="grid gap-4 sm:grid-cols-2 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <x-input label="Buscar" placeholder="Buscar...">
                            <x-slot name="prefix">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </x-slot>
                        </x-input>
                        <x-input label="Email" type="email" placeholder="correo@ejemplo.com">
                            <x-slot name="prefix">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </x-slot>
                        </x-input>
                    </div>
                </div>

                {{-- Textarea --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Textarea</h3>
                    <div class="p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                            <textarea rows="3"
                                class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Escribe una descripción..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Select --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Select</h3>
                    <div class="grid gap-4 sm:grid-cols-2 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Nativo</label>
                            <select class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="1">Opción 1</option>
                                <option value="2">Opción 2</option>
                                <option value="3">Opción 3</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Multiple</label>
                            <select multiple class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                <option value="1">Opción 1</option>
                                <option value="2">Opción 2</option>
                                <option value="3">Opción 3</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Checkboxes y Radio --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Checkboxes y Radio Buttons</h3>
                    <div class="grid gap-6 sm:grid-cols-2 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Checkboxes</span>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Opción 1</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Opción 2 (checked)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" disabled class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                    <span class="ml-2 text-sm text-gray-400">Opción 3 (disabled)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Radio Buttons</span>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="radio-demo" class="border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Opción A</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="radio-demo" checked class="border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Opción B (selected)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="radio-demo" disabled class="border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800">
                                    <span class="ml-2 text-sm text-gray-400">Opción C (disabled)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Date Picker --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Date Picker</h3>
                    <div class="grid gap-4 sm:grid-cols-2 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                            <input type="date" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha y Hora</label>
                            <input type="datetime-local" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- File Input --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">File Input</h3>
                    <div class="p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subir Archivo</label>
                            <input type="file" class="w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-medium
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-blue-900/50 dark:file:text-blue-300">
                        </div>
                        {{-- Drag & Drop Zone --}}
                        <div class="border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg p-8 text-center hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium text-blue-600 dark:text-blue-400">Haz clic para subir</span> o arrastra y suelta
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">PNG, JPG, PDF hasta 10MB</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 5: CARDS ==================== --}}
    <section id="cards" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">5. Cards</h2>
            </x-slot>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {{-- Card Básica --}}
                <x-card>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Card Básica</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Esta es una card simple con contenido básico.</p>
                </x-card>

                {{-- Card con Header --}}
                <x-card>
                    <x-slot name="header">
                        <h3 class="font-semibold">Card con Header</h3>
                    </x-slot>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Contenido de la card con header separado.</p>
                </x-card>

                {{-- Card con Header y Footer --}}
                <x-card>
                    <x-slot name="header">
                        <h3 class="font-semibold">Card Completa</h3>
                    </x-slot>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Card con header y footer.</p>
                    <x-slot name="footer">
                        <div class="flex justify-end gap-2">
                            <x-button variant="ghost" size="sm">Cancelar</x-button>
                            <x-button size="sm">Guardar</x-button>
                        </div>
                    </x-slot>
                </x-card>

                {{-- Card Interactiva --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-400 transition-all cursor-pointer">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Card Interactiva</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Pasa el mouse para ver el efecto hover.</p>
                </div>

                {{-- Card con Imagen --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                    <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600"></div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Card con Imagen</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Card con área de imagen/banner.</p>
                    </div>
                </div>

                {{-- Card de Estadística --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Ventas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">$12,500</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-green-600 dark:text-green-400">+12% vs mes anterior</p>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 6: MODALES ==================== --}}
    <section id="modales" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">6. Modales</h2>
            </x-slot>

            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-400">Haz clic en los botones para abrir los diferentes tipos de modales.</p>

                <div class="flex flex-wrap gap-3">
                    {{-- Modal Básico --}}
                    <x-button @click="$dispatch('open-modal', 'modal-basico')">Modal Básico</x-button>
                    <x-modal name="modal-basico" title="Modal Básico">
                        <p class="text-gray-600 dark:text-gray-400">Este es el contenido del modal básico. Puedes poner cualquier contenido aquí.</p>
                        <x-slot name="footer">
                            <x-button variant="ghost" @click="$dispatch('close-modal', 'modal-basico')">Cerrar</x-button>
                        </x-slot>
                    </x-modal>

                    {{-- Modal de Confirmación --}}
                    <x-button variant="danger" @click="$dispatch('open-modal', 'modal-confirmacion')">Modal Confirmación</x-button>
                    <x-modal name="modal-confirmacion" title="¿Estás seguro?">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">Esta acción no se puede deshacer. ¿Deseas continuar?</p>
                        </div>
                        <x-slot name="footer">
                            <x-button variant="ghost" @click="$dispatch('close-modal', 'modal-confirmacion')">Cancelar</x-button>
                            <x-button variant="danger" @click="$dispatch('close-modal', 'modal-confirmacion')">Eliminar</x-button>
                        </x-slot>
                    </x-modal>

                    {{-- Modal de Formulario --}}
                    <x-button variant="success" @click="$dispatch('open-modal', 'modal-formulario')">Modal Formulario</x-button>
                    <x-modal name="modal-formulario" title="Nuevo Registro" size="lg">
                        <form class="space-y-4">
                            <x-input label="Nombre" placeholder="Ingresa el nombre" />
                            <x-input label="Email" type="email" placeholder="correo@ejemplo.com" />
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                                <textarea rows="3" class="w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white focus:border-blue-500 focus:ring-blue-500" placeholder="Descripción opcional..."></textarea>
                            </div>
                        </form>
                        <x-slot name="footer">
                            <x-button variant="ghost" @click="$dispatch('close-modal', 'modal-formulario')">Cancelar</x-button>
                            <x-button variant="success" @click="$dispatch('close-modal', 'modal-formulario')">Guardar</x-button>
                        </x-slot>
                    </x-modal>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 7: ALERTAS ==================== --}}
    <section id="alertas" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">7. Alertas y Notificaciones</h2>
            </x-slot>

            <div class="space-y-6">
                {{-- Alertas de Estado --}}
                <div class="space-y-3">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Alertas de Estado</h3>
                    <x-alert type="success">
                        <strong>¡Éxito!</strong> La operación se completó correctamente.
                    </x-alert>
                    <x-alert type="danger">
                        <strong>Error:</strong> No se pudo completar la operación.
                    </x-alert>
                    <x-alert type="warning">
                        <strong>Advertencia:</strong> Revisa los datos antes de continuar.
                    </x-alert>
                    <x-alert type="info">
                        <strong>Info:</strong> Hay actualizaciones disponibles.
                    </x-alert>
                </div>

                {{-- Alertas Dismissibles --}}
                <div class="space-y-3">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Alertas Dismissibles</h3>
                    <x-alert type="success" dismissible>
                        Haz clic en la X para cerrar esta alerta.
                    </x-alert>
                    <x-alert type="info" dismissible>
                        Esta alerta también se puede cerrar.
                    </x-alert>
                </div>

                {{-- Toast Demo --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Toast Notifications</h3>
                    <div class="flex flex-wrap gap-3">
                        <x-button variant="success" size="sm" onclick="showToast('success', '¡Guardado correctamente!')">Toast Success</x-button>
                        <x-button variant="danger" size="sm" onclick="showToast('error', 'Error al procesar')">Toast Error</x-button>
                        <x-button variant="warning" size="sm" onclick="showToast('warning', 'Revisa los datos')">Toast Warning</x-button>
                        <x-button variant="outline" size="sm" onclick="showToast('info', 'Información importante')">Toast Info</x-button>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 8: TABLAS ==================== --}}
    <section id="tablas" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">8. Tablas</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Tabla Básica --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Tabla Básica Responsive</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-900 divide-y divide-gray-200 dark:divide-slate-700">
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Juan Pérez</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">juan@ejemplo.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">Activo</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Editar</button>
                                        <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">María García</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">maria@ejemplo.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full">Pendiente</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Editar</button>
                                        <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Carlos López</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">carlos@ejemplo.com</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">Inactivo</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Editar</button>
                                        <button class="text-red-600 dark:text-red-400 hover:underline">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Estado Vacío --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Estado Vacío</h3>
                    <div class="text-center py-12 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay datos</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza creando un nuevo registro.</p>
                        <div class="mt-6">
                            <x-button>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Nuevo Registro
                            </x-button>
                        </div>
                    </div>
                </div>

                {{-- Estado de Carga (Skeleton) --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Estado de Carga (Skeleton)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead class="bg-gray-50 dark:bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td class="px-6 py-4"><div class="h-4 bg-gray-200 dark:bg-slate-700 rounded animate-pulse w-32"></div></td>
                                    <td class="px-6 py-4"><div class="h-4 bg-gray-200 dark:bg-slate-700 rounded animate-pulse w-48"></div></td>
                                    <td class="px-6 py-4"><div class="h-6 bg-gray-200 dark:bg-slate-700 rounded-full animate-pulse w-16"></div></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 9: PAGINACIÓN ==================== --}}
    <section id="paginacion" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">9. Paginación</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Paginación Numérica --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Paginación Numérica</h3>
                    <nav class="flex items-center justify-between">
                        <p class="text-sm text-gray-700 dark:text-gray-400">
                            Mostrando <span class="font-medium">1</span> a <span class="font-medium">10</span> de <span class="font-medium">97</span> resultados
                        </p>
                        <div class="flex items-center gap-1">
                            <button class="px-3 py-2 text-sm font-medium text-gray-500 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50" disabled>Anterior</button>
                            <button class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg">1</button>
                            <button class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">2</button>
                            <button class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">3</button>
                            <span class="px-3 py-2 text-gray-500">...</span>
                            <button class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">10</button>
                            <button class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">Siguiente</button>
                        </div>
                    </nav>
                </div>

                {{-- Paginación Simple --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Paginación Simple</h3>
                    <nav class="flex items-center justify-between">
                        <button class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Anterior
                        </button>
                        <button class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">
                            Siguiente
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </nav>
                </div>

                {{-- Con selector de items --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Con Selector de Items por Página</h3>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-gray-600 dark:text-gray-400">Mostrar</label>
                            <select class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                            <span class="text-sm text-gray-600 dark:text-gray-400">por página</span>
                        </div>
                        <nav class="flex items-center gap-1">
                            <button class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" disabled>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <span class="px-3 py-1 text-sm text-gray-700 dark:text-gray-300">Página 1 de 10</span>
                            <button class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 10: NAVEGACIÓN ==================== --}}
    <section id="navegacion" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">10. Navegación</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Breadcrumbs --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Breadcrumbs</h3>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                    </svg>
                                    Inicio
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <a href="#" class="ml-1 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 md:ml-2">Facturas</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="ml-1 text-gray-700 dark:text-gray-200 font-medium md:ml-2">Detalle</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>

                {{-- Tabs --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Tabs</h3>
                    <div x-data="{ activeTab: 'tab1' }">
                        <div class="border-b border-gray-200 dark:border-slate-700">
                            <nav class="flex gap-4">
                                <button @click="activeTab = 'tab1'"
                                    :class="activeTab === 'tab1' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                    General
                                </button>
                                <button @click="activeTab = 'tab2'"
                                    :class="activeTab === 'tab2' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                    Configuración
                                </button>
                                <button @click="activeTab = 'tab3'"
                                    :class="activeTab === 'tab3' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                                    Notificaciones
                                </button>
                            </nav>
                        </div>
                        <div class="py-4">
                            <div x-show="activeTab === 'tab1'">Contenido del tab General</div>
                            <div x-show="activeTab === 'tab2'">Contenido del tab Configuración</div>
                            <div x-show="activeTab === 'tab3'">Contenido del tab Notificaciones</div>
                        </div>
                    </div>
                </div>

                {{-- Dropdown Menu --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Dropdown Menu</h3>
                    <div x-data="{ open: false }" class="relative inline-block">
                        <button @click="open = !open" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700">
                            Opciones
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 z-10">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-t-lg">Editar</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">Duplicar</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700">Archivar</a>
                            <hr class="border-gray-200 dark:border-slate-700">
                            <a href="#" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-b-lg">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 11: BADGES Y TAGS ==================== --}}
    <section id="badges" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">11. Badges y Tags</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Badges de Estado --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Badges de Estado</h3>
                    <div class="flex flex-wrap gap-3">
                        <span class="px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">Activo</span>
                        <span class="px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full">Pendiente</span>
                        <span class="px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">Inactivo</span>
                        <span class="px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">En Proceso</span>
                        <span class="px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full">Borrador</span>
                        <span class="px-2.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">Premium</span>
                    </div>
                </div>

                {{-- Badges con Icono --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Badges con Icono</h3>
                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Verificado
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Rechazado
                        </span>
                    </div>
                </div>

                {{-- Tags Removibles --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Tags Removibles</h3>
                    <div class="flex flex-wrap gap-2" x-data="{ tags: ['Laravel', 'PHP', 'JavaScript', 'Tailwind'] }">
                        <template x-for="(tag, index) in tags" :key="index">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                <span x-text="tag"></span>
                                <button @click="tags.splice(index, 1)" class="ml-2 hover:text-blue-600 dark:hover:text-blue-300">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Counters --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Counters</h3>
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="relative">
                            <span class="text-gray-700 dark:text-gray-300">Mensajes</span>
                            <span class="absolute -top-2 -right-6 px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">5</span>
                        </span>
                        <button class="relative px-4 py-2 bg-gray-100 dark:bg-slate-700 rounded-lg text-gray-700 dark:text-gray-300">
                            Notificaciones
                            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center text-xs font-bold bg-blue-500 text-white rounded-full">12</span>
                        </button>
                        <button class="relative p-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-0 right-0 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 12: LOADING STATES ==================== --}}
    <section id="loading" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">12. Loading States</h2>
            </x-slot>

            <div class="space-y-8">
                {{-- Spinners --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Spinners</h3>
                    <div class="flex flex-wrap items-center gap-6 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        {{-- Spinner pequeño --}}
                        <div class="text-center">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="block text-xs mt-1 text-gray-500">Small</span>
                        </div>
                        {{-- Spinner mediano --}}
                        <div class="text-center">
                            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="block text-xs mt-1 text-gray-500">Medium</span>
                        </div>
                        {{-- Spinner grande --}}
                        <div class="text-center">
                            <svg class="animate-spin h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="block text-xs mt-1 text-gray-500">Large</span>
                        </div>
                        {{-- Spinner con colores --}}
                        <div class="text-center">
                            <svg class="animate-spin h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="block text-xs mt-1 text-gray-500">Green</span>
                        </div>
                    </div>
                </div>

                {{-- Skeleton Loaders --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Skeleton Loaders</h3>
                    <div class="space-y-4 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        {{-- Card Skeleton --}}
                        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 shadow">
                            <div class="flex items-center space-x-4">
                                <div class="h-12 w-12 bg-gray-200 dark:bg-slate-700 rounded-full animate-pulse"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded animate-pulse w-3/4"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded animate-pulse w-1/2"></div>
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded animate-pulse"></div>
                                <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded animate-pulse w-5/6"></div>
                                <div class="h-3 bg-gray-200 dark:bg-slate-700 rounded animate-pulse w-4/6"></div>
                            </div>
                        </div>
                        {{-- List Skeleton --}}
                        <div class="space-y-3">
                            @for($i = 0; $i < 3; $i++)
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 bg-gray-200 dark:bg-slate-700 rounded animate-pulse"></div>
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 dark:bg-slate-700 rounded animate-pulse w-2/3"></div>
                                </div>
                                <div class="h-8 w-20 bg-gray-200 dark:bg-slate-700 rounded animate-pulse"></div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Progress Bars --}}
                <div>
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Progress Bars</h3>
                    <div class="space-y-4 p-4 bg-gray-50 dark:bg-slate-900 rounded-lg">
                        {{-- Progress básico --}}
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Progreso</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">45%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                        {{-- Progress con colores --}}
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Completado</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        {{-- Progress warning --}}
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Espacio usado</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">90%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 90%"></div>
                            </div>
                        </div>
                        {{-- Progress animado --}}
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Cargando...</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full animate-pulse" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

    {{-- ==================== SECCIÓN 13: ICONOS ==================== --}}
    <section id="iconos" class="scroll-mt-24">
        <x-card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">13. Iconos (Heroicons)</h2>
            </x-slot>

            <div class="space-y-6">
                <p class="text-gray-600 dark:text-gray-400">Muestra de iconos SVG comúnmente utilizados en el sistema. Usando Heroicons (outline).</p>

                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-4">
                    {{-- Home --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Home</span>
                    </div>
                    {{-- User --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">User</span>
                    </div>
                    {{-- Cog/Settings --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Settings</span>
                    </div>
                    {{-- Search --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Search</span>
                    </div>
                    {{-- Bell --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Bell</span>
                    </div>
                    {{-- Mail --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Mail</span>
                    </div>
                    {{-- Document --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Document</span>
                    </div>
                    {{-- Folder --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Folder</span>
                    </div>
                    {{-- Plus --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Plus</span>
                    </div>
                    {{-- Trash --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Trash</span>
                    </div>
                    {{-- Edit/Pencil --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Edit</span>
                    </div>
                    {{-- Eye --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Eye</span>
                    </div>
                    {{-- Download --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Download</span>
                    </div>
                    {{-- Upload --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Upload</span>
                    </div>
                    {{-- Check --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Check</span>
                    </div>
                    {{-- X --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">X</span>
                    </div>
                    {{-- Chart --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Chart</span>
                    </div>
                    {{-- Calendar --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Calendar</span>
                    </div>
                    {{-- Filter --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Filter</span>
                    </div>
                    {{-- Logout --}}
                    <div class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="text-xs mt-1 text-gray-500">Logout</span>
                    </div>
                </div>
            </div>
        </x-card>
    </section>

</div>

@endsection

@push('scripts')
<script>
    // Función para mostrar toast notifications (demo)
    function showToast(type, message) {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-slide-in`;
        toast.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto hover:opacity-80">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        `;
        container.appendChild(toast);

        // Auto-remove después de 3 segundos
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endpush
