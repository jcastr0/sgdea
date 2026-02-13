@extends('layouts.sgdea')

@section('title', 'Panel de Administración Global')
@section('page-title', 'Panel de Administración Global')

@section('content')
<div class="space-y-6" x-data="adminDashboard()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Panel de Administración Global
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Bienvenido, {{ $adminGlobal->name ?? 'Administrador' }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="refreshData()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors cursor-pointer">
                <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
            <a href="{{ route('admin.tenants.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Tenant
            </a>
        </div>
    </div>

    {{-- KPIs Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Tenants --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tenants</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $kpis['totalTenants'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            @if(isset($kpis['tenantsChange']))
            <p class="text-xs mt-2 {{ $kpis['tenantsChange'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $kpis['tenantsChange'] >= 0 ? '+' : '' }}{{ $kpis['tenantsChange'] }} este mes
            </p>
            @endif
        </div>

        {{-- Total Usuarios --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Usuarios</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $kpis['totalUsers'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            @if(isset($kpis['usersChange']))
            <p class="text-xs mt-2 {{ $kpis['usersChange'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $kpis['usersChange'] >= 0 ? '+' : '' }}{{ $kpis['usersChange'] }} este mes
            </p>
            @endif
        </div>

        {{-- Total Facturas --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Facturas</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($kpis['totalFacturas'] ?? 0) }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            @if(isset($kpis['facturasChange']))
            <p class="text-xs mt-2 {{ $kpis['facturasChange'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $kpis['facturasChange'] >= 0 ? '+' : '' }}{{ number_format($kpis['facturasChange']) }} este mes
            </p>
            @endif
        </div>

        {{-- Storage --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Storage Usado</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $kpis['storageUsed'] ?? '0 MB' }}</p>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                </div>
            </div>
            @if(isset($kpis['storagePercent']))
            <div class="mt-2">
                <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $kpis['storagePercent'] > 80 ? 'bg-red-500' : ($kpis['storagePercent'] > 60 ? 'bg-amber-500' : 'bg-green-500') }}"
                         style="width: {{ min($kpis['storagePercent'], 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $kpis['storagePercent'] }}% utilizado</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Tenants por Estado --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tenants por Estado</h3>
            <div class="flex items-center justify-center">
                <canvas id="tenantsByStatusChart" width="300" height="300"></canvas>
            </div>
            <div class="flex flex-wrap justify-center gap-4 mt-4">
                @foreach($tenantsByStatus ?? [] as $status => $count)
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $status === 'active' ? 'bg-green-500' : ($status === 'suspended' ? 'bg-red-500' : ($status === 'trial' ? 'bg-amber-500' : 'bg-gray-500')) }}"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $status }}: {{ $count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tendencia de Crecimiento --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tendencia de Crecimiento</h3>
            <canvas id="growthTrendChart" height="200"></canvas>
        </div>
    </div>

    {{-- Alerts and Top Tenants Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Alertas del Sistema --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Alertas del Sistema</h3>
            <div class="space-y-3">
                @forelse($alerts ?? [] as $alert)
                <div class="flex items-start gap-3 p-3 rounded-lg {{ $alert['type'] === 'warning' ? 'bg-amber-50 dark:bg-amber-900/20' : ($alert['type'] === 'danger' ? 'bg-red-50 dark:bg-red-900/20' : 'bg-green-50 dark:bg-green-900/20') }}">
                    @if($alert['type'] === 'warning')
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    @elseif($alert['type'] === 'danger')
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @else
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                    <div>
                        <p class="text-sm font-medium {{ $alert['type'] === 'warning' ? 'text-amber-800 dark:text-amber-200' : ($alert['type'] === 'danger' ? 'text-red-800 dark:text-red-200' : 'text-green-800 dark:text-green-200') }}">
                            {{ $alert['message'] }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="flex items-center gap-3 p-3 rounded-lg bg-green-50 dark:bg-green-900/20">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">Todo está funcionando correctamente</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top 5 Tenants --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top 5 Tenants</h3>
            <div class="space-y-4">
                @forelse($topTenants ?? [] as $index => $tenant)
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                        {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : ($index === 1 ? 'bg-gray-200 text-gray-700' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $tenant->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $tenant->users_count ?? 0 }} usuarios · {{ number_format($tenant->facturas_count ?? 0) }} facturas
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($tenant->total_facturado ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No hay tenants registrados</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Actividad Reciente --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actividad Reciente</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-gray-200 dark:border-slate-700">
                        <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acción</th>
                        <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Usuario</th>
                        <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Modelo</th>
                        <th class="pb-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($recentActivity ?? [] as $activity)
                    <tr>
                        <td class="py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ ($activity['action'] ?? '') === 'create' || ($activity['action'] ?? '') === 'created' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                                   (($activity['action'] ?? '') === 'update' || ($activity['action'] ?? '') === 'updated' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' :
                                   (($activity['action'] ?? '') === 'delete' || ($activity['action'] ?? '') === 'deleted' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' :
                                   'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400')) }}">
                                {{ ucfirst($activity['action'] ?? '-') }}
                            </span>
                        </td>
                        <td class="py-3 text-sm text-gray-900 dark:text-white">
                            {{ $activity['user_name'] ?? 'Sistema' }}
                        </td>
                        <td class="py-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ $activity['model'] ?? '-' }}
                        </td>
                        <td class="py-3 text-sm text-gray-500 dark:text-gray-400">
                            {{ $activity['time_ago'] ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No hay actividad reciente
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.tenants.index') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 transition-colors cursor-pointer">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Gestionar Tenants</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ver todos los tenants</p>
            </div>
        </a>

        <a href="{{ route('admin.usuarios.pendientes') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors cursor-pointer">
            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Usuarios Pendientes</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aprobar nuevos usuarios</p>
            </div>
        </a>

        <a href="{{ route('admin.auditoria.index') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-700 transition-colors cursor-pointer">
            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Auditoría</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ver registros de auditoría</p>
            </div>
        </a>

        <a href="{{ route('admin.tenants.create') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-700 transition-colors cursor-pointer">
            <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Nuevo Tenant</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Crear nueva empresa</p>
            </div>
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function adminDashboard() {
    return {
        loading: false,

        init() {
            this.initCharts();
        },

        refreshData() {
            this.loading = true;
            setTimeout(() => {
                window.location.reload();
            }, 500);
        },

        initCharts() {
            // Tenants by Status Chart
            const statusCtx = document.getElementById('tenantsByStatusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode(array_keys($tenantsByStatus ?? [])) !!},
                        datasets: [{
                            data: {!! json_encode(array_values($tenantsByStatus ?? [])) !!},
                            backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#6b7280'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        cutout: '60%'
                    }
                });
            }

            // Growth Trend Chart
            const growthCtx = document.getElementById('growthTrendChart');
            if (growthCtx) {
                const growthData = @json($growthTrend ?? []);
                new Chart(growthCtx, {
                    type: 'line',
                    data: {
                        labels: growthData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Tenants',
                                data: growthData.map(d => d.tenants),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Usuarios',
                                data: growthData.map(d => d.users),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    }
}
</script>
@endpush
@endsection

