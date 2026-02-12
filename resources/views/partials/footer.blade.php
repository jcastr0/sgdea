{{-- Footer del layout principal --}}
<footer class="border-t border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800">
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Copyright --}}
            <div class="text-sm text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} <span class="font-medium text-gray-700 dark:text-gray-300">SGDEA</span>.
                Sistema de Gestión Documental Electrónico de Archivo.
            </div>

            {{-- Links y versión --}}
            <div class="flex items-center gap-4">
                <a href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    Documentación
                </a>
                <a href="#" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    Soporte
                </a>
                <span class="text-xs text-gray-400 dark:text-gray-500 px-2 py-1 rounded-full bg-gray-100 dark:bg-slate-700">
                    v{{ config('app.version', '1.0.0') }}
                </span>
            </div>
        </div>
    </div>
</footer>
