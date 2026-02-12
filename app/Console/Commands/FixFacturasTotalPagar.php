<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;

class FixFacturasTotalPagar extends Command
{
    protected $signature = 'fix:facturas-total {--tenant= : ID del tenant (opcional, si no se especifica arregla todos)}';

    protected $description = 'Arregla el campo total_pagar copiando el valor de subtotal donde total_pagar es 0';

    public function handle()
    {
        $tenantId = $this->option('tenant');

        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('🔧 Arreglando total_pagar en facturas');
        $this->info('═══════════════════════════════════════════════════════════');

        $query = Factura::where('total_pagar', 0)
            ->where('subtotal', '>', 0);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
            $this->line("📋 Filtrando por Tenant ID: {$tenantId}");
        }

        $count = $query->count();
        $this->line("📊 Facturas a corregir: {$count}");

        if ($count === 0) {
            $this->info('✅ No hay facturas que corregir');
            return 0;
        }

        if (!$this->confirm("¿Desea continuar con la corrección de {$count} facturas?")) {
            $this->warn('Operación cancelada');
            return 0;
        }

        // Actualizar usando raw SQL para mejor rendimiento
        $updated = DB::table('facturas')
            ->where('total_pagar', 0)
            ->where('subtotal', '>', 0)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->update(['total_pagar' => DB::raw('subtotal + iva - descuento')]);

        $this->info("✅ Facturas actualizadas: {$updated}");

        // Mostrar ejemplo de verificación
        $ejemplo = Factura::where('total_pagar', '>', 0)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->first();

        if ($ejemplo) {
            $this->newLine();
            $this->info('📋 Ejemplo de factura corregida:');
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['numero_factura', $ejemplo->numero_factura],
                    ['subtotal', '$' . number_format($ejemplo->subtotal, 2)],
                    ['iva', '$' . number_format($ejemplo->iva, 2)],
                    ['descuento', '$' . number_format($ejemplo->descuento, 2)],
                    ['total_pagar', '$' . number_format($ejemplo->total_pagar, 2)],
                ]
            );
        }

        return 0;
    }
}
