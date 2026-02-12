<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;
use App\Models\Tercero;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebugDashboard extends Command
{
    protected $signature = 'debug:dashboard
                            {--tenant=1 : ID del tenant}
                            {--tercero= : ID del tercero (opcional)}
                            {--fecha-inicio= : Fecha inicio (YYYY-MM-DD)}
                            {--fecha-fin= : Fecha fin (YYYY-MM-DD)}
                            {--test=all : Test específico (all, kpis, evolucion, top, estados, facturas-mes, resumen, factura, usuarios, permisos)}';

    protected $description = 'Debug de las funciones del Dashboard';

    public function handle()
    {
        $tenantId = (int) $this->option('tenant');
        $terceroId = $this->option('tercero') ? (int) $this->option('tercero') : null;
        $fechaInicio = $this->option('fecha-inicio') ?? Carbon::now()->startOfYear()->format('Y-m-d');
        $fechaFin = $this->option('fecha-fin') ?? Carbon::now()->format('Y-m-d');
        $test = $this->option('test');

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('🔧 DEBUG DASHBOARD');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->line("📋 Tenant ID: {$tenantId}");
        $this->line("📅 Período: {$fechaInicio} a {$fechaFin}");
        if ($terceroId) {
            $this->line("👤 Tercero ID: {$terceroId}");
        }
        $this->newLine();

        try {
            // Resumen básico siempre
            if ($test === 'all' || $test === 'resumen') {
                $this->testResumen($tenantId);
            }

            // Test de una factura de ejemplo
            if ($test === 'all' || $test === 'factura') {
                $this->testFacturaEjemplo($tenantId);
            }

            // Test de usuarios y permisos
            if ($test === 'all' || $test === 'usuarios') {
                $this->testUsuarios($tenantId);
            }

            // KPIs
            if ($test === 'all' || $test === 'kpis') {
                $this->testKPIs($tenantId, $terceroId, $fechaInicio, $fechaFin);
            }

            // Evolución mensual
            if ($test === 'all' || $test === 'evolucion') {
                $this->testEvolucionMensual($tenantId, $terceroId, $fechaInicio, $fechaFin);
            }

            // Top terceros
            if ($test === 'all' || $test === 'top') {
                $this->testTopTerceros($tenantId, $terceroId, $fechaInicio, $fechaFin);
            }

            // Distribución estados
            if ($test === 'all' || $test === 'estados') {
                $this->testDistribucionEstados($tenantId, $terceroId, $fechaInicio, $fechaFin);
            }

            // Facturas por mes
            if ($test === 'all' || $test === 'facturas-mes') {
                $this->testFacturasPorMes($tenantId, $terceroId, $fechaInicio, $fechaFin);
            }

            // Permisos
            if ($test === 'all' || $test === 'permisos') {
                $this->testPermisos($tenantId);
            }

            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════════');
            $this->info('✅ Debug completado');
            $this->info('═══════════════════════════════════════════════════════════');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    private function testResumen(int $tenantId): void
    {
        $this->info('📊 RESUMEN BÁSICO');
        $this->line('───────────────────────────────────────────────────────────');

        $totalFacturas = Factura::where('tenant_id', $tenantId)->count();
        $totalTerceros = Tercero::where('tenant_id', $tenantId)->count();

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total Facturas', $totalFacturas],
                ['Total Terceros', $totalTerceros],
            ]
        );

        // Estados únicos
        $estados = Factura::where('tenant_id', $tenantId)
            ->selectRaw("COALESCE(estado, 'SIN_ESTADO') as estado, COUNT(*) as cantidad")
            ->groupBy('estado')
            ->get();

        $this->line("\n📋 Estados encontrados:");
        $this->table(
            ['Estado', 'Cantidad'],
            $estados->map(fn($e) => [$e->estado, $e->cantidad])->toArray()
        );

        // Rango de fechas
        $fechaMin = Factura::where('tenant_id', $tenantId)->min('fecha_factura');
        $fechaMax = Factura::where('tenant_id', $tenantId)->max('fecha_factura');

        $this->line("\n📅 Rango de fechas en facturas:");
        $this->line("   Desde: {$fechaMin}");
        $this->line("   Hasta: {$fechaMax}");
        $this->newLine();
    }

    private function testFacturaEjemplo(int $tenantId): void
    {
        $this->info('📄 FACTURA DE EJEMPLO');
        $this->line('───────────────────────────────────────────────────────────');

        $factura = Factura::where('tenant_id', $tenantId)->first();

        if (!$factura) {
            $this->warn('⚠️  No hay facturas');
            return;
        }

        $this->table(
            ['Campo', 'Valor'],
            [
                ['ID', $factura->id],
                ['numero_factura', $factura->numero_factura],
                ['cufe', substr($factura->cufe ?? '', 0, 40) . '...'],
                ['estado', $factura->estado],
                ['fecha_factura', $factura->fecha_factura],
                ['tercero_id', $factura->tercero_id],
                ['subtotal', $factura->subtotal],
                ['iva', $factura->iva],
                ['descuento', $factura->descuento],
                ['total_pagar', $factura->total_pagar],
                ['pdf_path', $factura->pdf_path ?? 'NULL'],
            ]
        );

        // Ver raw de BD
        $this->line("\n📋 Datos raw de BD:");
        $raw = DB::table('facturas')->where('id', $factura->id)->first();
        $this->line("   subtotal (raw): " . var_export($raw->subtotal, true));
        $this->line("   iva (raw): " . var_export($raw->iva, true));
        $this->line("   total_pagar (raw): " . var_export($raw->total_pagar, true));

        $this->newLine();
    }

    private function testUsuarios(int $tenantId): void
    {
        $this->info('👥 USUARIOS Y PERMISOS');
        $this->line('───────────────────────────────────────────────────────────');

        // Mostrar roles disponibles
        $roles = Role::where('tenant_id', $tenantId)->orWhereNull('tenant_id')->get();

        $this->line("\n📋 Roles disponibles:");
        if ($roles->isEmpty()) {
            $this->warn('   ⚠️  No hay roles creados');
        } else {
            $this->table(
                ['ID', 'Nombre', 'Tenant', 'Permisos'],
                $roles->map(fn($r) => [
                    $r->id,
                    $r->name,
                    $r->tenant_id ?? 'Global',
                    $r->permissions->count(),
                ])->toArray()
            );
        }

        $users = User::where('tenant_id', $tenantId)
            ->with('role.permissions')
            ->get();

        foreach ($users as $user) {
            $this->line("\n👤 Usuario: {$user->name} ({$user->email})");
            $this->line("   ID: {$user->id}");
            $this->line("   Status: {$user->status}");
            $this->line("   Role ID: " . ($user->role_id ?? 'NULL'));

            if ($user->role) {
                $this->line("   Role: {$user->role->name}");
                $permisos = $user->role->permissions->pluck('name')->toArray();
                $this->line("   Permisos (" . count($permisos) . "): " . implode(', ', array_slice($permisos, 0, 5)) . (count($permisos) > 5 ? '...' : ''));
            } else {
                $this->warn("   ⚠️  SIN ROL ASIGNADO");
            }
        }
        $this->newLine();
    }

    private function testKPIs(int $tenantId, ?int $terceroId, string $fechaInicio, string $fechaFin): void
    {
        $this->info('📈 TEST KPIs');
        $this->line('───────────────────────────────────────────────────────────');

        $query = Factura::where('tenant_id', $tenantId)
            ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin]);

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $facturas = $query->get();

        $totalFacturas = $facturas->count();
        $totalIngresos = $facturas->sum('total_pagar');

        // Contar por estado
        $porEstado = $facturas->groupBy(fn($f) => strtolower($f->estado ?? 'sin_estado'))
            ->map(fn($group) => [
                'cantidad' => $group->count(),
                'total' => $group->sum('total_pagar'),
            ]);

        $this->table(
            ['KPI', 'Valor'],
            [
                ['Total Facturas', $totalFacturas],
                ['Total Ingresos', '$' . number_format($totalIngresos, 2)],
                ['Aceptadas', $porEstado->get('aceptado')['cantidad'] ?? 0],
                ['Pendientes', ($porEstado->get('pendiente')['cantidad'] ?? 0) + ($porEstado->get('sin_estado')['cantidad'] ?? 0)],
                ['Rechazadas', $porEstado->get('rechazado')['cantidad'] ?? 0],
            ]
        );

        $this->line("\n💰 Totales por estado:");
        foreach ($porEstado as $estado => $data) {
            $this->line("   {$estado}: {$data['cantidad']} facturas = \$" . number_format($data['total'], 2));
        }
        $this->newLine();
    }

    private function testEvolucionMensual(int $tenantId, ?int $terceroId, string $fechaInicio, string $fechaFin): void
    {
        $this->info('📊 TEST EVOLUCIÓN MENSUAL');
        $this->line('───────────────────────────────────────────────────────────');

        try {
            $query = Factura::where('tenant_id', $tenantId)
                ->whereRaw("LOWER(COALESCE(estado, '')) = 'aceptado'")
                ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin])
                ->selectRaw("DATE_FORMAT(fecha_factura, '%Y-%m-01') as mes, SUM(total_pagar) as total")
                ->groupByRaw("DATE_FORMAT(fecha_factura, '%Y-%m-01')")
                ->orderBy('mes');

            if ($terceroId) {
                $query->where('tercero_id', $terceroId);
            }

            $datos = $query->get();

            if ($datos->isEmpty()) {
                $this->warn('⚠️  No hay datos de evolución mensual (solo facturas aceptadas)');

                // Mostrar sin filtro de estado
                $this->line("\n📋 Probando sin filtro de estado:");
                $datosSinFiltro = Factura::where('tenant_id', $tenantId)
                    ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin])
                    ->selectRaw("DATE_FORMAT(fecha_factura, '%Y-%m-01') as mes, estado, SUM(total_pagar) as total")
                    ->groupByRaw("DATE_FORMAT(fecha_factura, '%Y-%m-01'), estado")
                    ->orderBy('mes')
                    ->get();

                $this->table(
                    ['Mes', 'Estado', 'Total'],
                    $datosSinFiltro->map(fn($d) => [
                        Carbon::parse($d->mes)->format('M Y'),
                        $d->estado ?? 'SIN_ESTADO',
                        '$' . number_format($d->total, 2),
                    ])->toArray()
                );
            } else {
                $this->table(
                    ['Mes', 'Total'],
                    $datos->map(fn($d) => [
                        Carbon::parse($d->mes)->format('M Y'),
                        '$' . number_format($d->total, 2),
                    ])->toArray()
                );
            }

            $this->info('✅ Query ejecutada correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testTopTerceros(int $tenantId, ?int $terceroId, string $fechaInicio, string $fechaFin): void
    {
        $this->info('🏆 TEST TOP TERCEROS');
        $this->line('───────────────────────────────────────────────────────────');

        if ($terceroId) {
            $this->warn('⚠️  Filtrado por tercero - Top no aplica');
            return;
        }

        try {
            $datos = DB::table('facturas')
                ->join('terceros', 'facturas.tercero_id', '=', 'terceros.id')
                ->where('facturas.tenant_id', $tenantId)
                ->whereBetween('facturas.fecha_factura', [$fechaInicio, $fechaFin])
                ->select(
                    'terceros.nombre_razon_social',
                    'terceros.id',
                    'terceros.nit',
                    DB::raw('SUM(facturas.total_pagar) as total'),
                    DB::raw('COUNT(*) as cantidad')
                )
                ->groupBy('terceros.id', 'terceros.nombre_razon_social', 'terceros.nit')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            if ($datos->isEmpty()) {
                $this->warn('⚠️  No hay datos de terceros');
            } else {
                $this->table(
                    ['#', 'Tercero', 'NIT', 'Facturas', 'Total'],
                    $datos->map(fn($d, $i) => [
                        $i + 1,
                        substr($d->nombre_razon_social, 0, 25),
                        $d->nit,
                        $d->cantidad,
                        '$' . number_format($d->total, 2),
                    ])->toArray()
                );
            }

            $this->info('✅ Query ejecutada correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testDistribucionEstados(int $tenantId, ?int $terceroId, string $fechaInicio, string $fechaFin): void
    {
        $this->info('📊 TEST DISTRIBUCIÓN ESTADOS');
        $this->line('───────────────────────────────────────────────────────────');

        try {
            $query = Factura::where('tenant_id', $tenantId)
                ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin])
                ->selectRaw("LOWER(COALESCE(estado, 'sin_estado')) as estado_lower, COUNT(*) as cantidad, SUM(total_pagar) as total")
                ->groupByRaw("LOWER(COALESCE(estado, 'sin_estado'))");

            if ($terceroId) {
                $query->where('tercero_id', $terceroId);
            }

            $datos = $query->get();

            $this->table(
                ['Estado', 'Cantidad', 'Total'],
                $datos->map(fn($d) => [
                    $d->estado_lower,
                    $d->cantidad,
                    '$' . number_format($d->total, 2),
                ])->toArray()
            );

            $this->info('✅ Query ejecutada correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testFacturasPorMes(int $tenantId, ?int $terceroId, string $fechaInicio, string $fechaFin): void
    {
        $this->info('📅 TEST FACTURAS POR MES');
        $this->line('───────────────────────────────────────────────────────────');

        try {
            $query = Factura::where('tenant_id', $tenantId)
                ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin])
                ->selectRaw("DATE_FORMAT(fecha_factura, '%Y-%m-01') as mes, COUNT(*) as cantidad")
                ->groupByRaw("DATE_FORMAT(fecha_factura, '%Y-%m-01')")
                ->orderBy('mes');

            if ($terceroId) {
                $query->where('tercero_id', $terceroId);
            }

            $datos = $query->get();

            $this->table(
                ['Mes', 'Cantidad'],
                $datos->map(fn($d) => [
                    Carbon::parse($d->mes)->format('M Y'),
                    $d->cantidad,
                ])->toArray()
            );

            $this->info('✅ Query ejecutada correctamente');
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
        $this->newLine();
    }

    private function testPermisos(int $tenantId): void
    {
        $this->info('🔑 PERMISOS');
        $this->line('───────────────────────────────────────────────────────────');

        // Obtener todos los roles con sus permisos
        $roles = Role::where('tenant_id', $tenantId)->with('permissions')->get();

        foreach ($roles as $role) {
            $this->line("\n📋 Rol: {$role->name} (ID: {$role->id})");
            $this->line("   Tipo: " . ($role->tenant_id ? 'Específico' : 'Global'));
            $this->line("   Permisos (" . $role->permissions->count() . "):");

            if ($role->permissions->isEmpty()) {
                $this->line("   - Ninguno");
            } else {
                $this->table(
                    ['ID', 'Nombre'],
                    $role->permissions->map(fn($p) => [$p->id, $p->name])->toArray()
                );
            }
        }

        $this->newLine();
    }
}

