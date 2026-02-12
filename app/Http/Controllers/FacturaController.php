<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Tercero;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FacturaController extends Controller
{
    // Middleware se configura en las rutas en Laravel 12+

    /**
     * Listado de facturas con búsqueda avanzada
     * Ahora usa componente Livewire para mejor UX
     */
    public function index(Request $request)
    {
        // La nueva vista usa Livewire para manejar filtros y paginación
        return view('facturas.index');
    }

    /**
     * Vista detalle de factura con historial de cambios
     */
    public function show(Factura $factura)
    {
        $this->autorizarTenant($factura);

        // Cargar relaciones
        $factura->load('tercero');

        // Obtener historial de cambios de la factura
        $historial = AuditLog::where('model_type', Factura::class)
            ->where('model_id', $factura->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('facturas.show', [
            'factura' => $factura,
            'historial' => $historial,
        ]);
    }

    /**
     * Formulario crear factura
     */
    public function create()
    {
        $tenantId = session('tenant_id');
        $terceros = Tercero::byTenant($tenantId)->orderBy('nombre_razon_social')->get();

        return view('facturas.create', ['terceros' => $terceros]);
    }

    /**
     * Guardar nueva factura
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'numero_factura' => 'required|string|max:50',
            'tercero_id' => 'required|exists:terceros,id',
            'fecha_factura' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_factura',
            'subtotal' => 'required|numeric|min:0',
            'iva' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'motonave' => 'nullable|string|max:255',
            'trb' => 'nullable|string|max:255',
            'servicio_descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,pagada,cancelada',
            'pdf' => 'nullable|mimes:pdf|max:5120',
        ]);

        // Generar CUFE
        $cufe = $this->generarCufe($validated['numero_factura'], $validated['fecha_factura']);

        // Verificar unicidad de CUFE
        if (Factura::where('tenant_id', $tenantId)->where('cufe', $cufe)->exists()) {
            return back()->withErrors(['cufe' => 'Esta factura ya está registrada.'])->withInput();
        }

        // Calcular total
        $total = ($validated['subtotal'] + ($validated['iva'] ?? 0)) - ($validated['descuento'] ?? 0);

        // Procesar PDF si existe
        $pdfPath = null;
        $hashPdf = null;
        if ($request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            $pdfPath = $pdfFile->store("tenant-{$tenantId}/facturas", 'local');
            $hashPdf = Factura::generarHashPdf(storage_path('app/' . $pdfPath));
        }

        // Crear factura
        $factura = Factura::create([
            'tenant_id' => $tenantId,
            'cufe' => $cufe,
            'numero_factura' => $validated['numero_factura'],
            'tercero_id' => $validated['tercero_id'],
            'fecha_factura' => $validated['fecha_factura'],
            'fecha_vencimiento' => $validated['fecha_vencimiento'],
            'subtotal' => $validated['subtotal'],
            'iva' => $validated['iva'] ?? 0,
            'descuento' => $validated['descuento'] ?? 0,
            'total_pagar' => $total,
            'motonave' => $validated['motonave'],
            'trb' => $validated['trb'],
            'servicio_descripcion' => $validated['servicio_descripcion'],
            'pdf_path' => $pdfPath,
            'hash_pdf' => $hashPdf,
            'estado' => $validated['estado'],
        ]);

        // Registrar auditoría
        $this->registrarAuditoria('crear', 'factura', $factura->id, [
            'numero_factura' => $factura->numero_factura,
            'cufe' => $factura->cufe,
            'total_pagar' => $factura->total_pagar,
        ]);

        return redirect()->route('facturas.show', $factura)
            ->with('success', 'Factura creada exitosamente.');
    }

    /**
     * Formulario editar factura
     */
    public function edit(Factura $factura)
    {
        $this->autorizarTenant($factura);
        $tenantId = session('tenant_id');
        $terceros = Tercero::byTenant($tenantId)->orderBy('nombre_razon_social')->get();

        return view('facturas.edit', [
            'factura' => $factura,
            'terceros' => $terceros,
        ]);
    }

    /**
     * Actualizar factura
     */
    public function update(Request $request, Factura $factura)
    {
        $this->autorizarTenant($factura);
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'numero_factura' => 'required|string|max:50',
            'tercero_id' => 'required|exists:terceros,id',
            'fecha_factura' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_factura',
            'subtotal' => 'required|numeric|min:0',
            'iva' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'motonave' => 'nullable|string|max:255',
            'trb' => 'nullable|string|max:255',
            'servicio_descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,pagada,cancelada',
            'pdf' => 'nullable|mimes:pdf|max:5120',
        ]);

        // Guardar datos anteriores
        $datosAnteriores = $factura->only([
            'numero_factura',
            'fecha_factura',
            'subtotal',
            'iva',
            'descuento',
            'total_pagar',
            'estado',
        ]);

        // Calcular total
        $total = ($validated['subtotal'] + ($validated['iva'] ?? 0)) - ($validated['descuento'] ?? 0);

        // Procesar nuevo PDF si existe
        if ($request->hasFile('pdf')) {
            // Eliminar PDF anterior si existe
            if ($factura->pdf_path && Storage::exists('local/' . $factura->pdf_path)) {
                Storage::delete('local/' . $factura->pdf_path);
            }

            $pdfFile = $request->file('pdf');
            $factura->pdf_path = $pdfFile->store("tenant-{$tenantId}/facturas", 'local');
            $factura->hash_pdf = Factura::generarHashPdf(storage_path('app/' . $factura->pdf_path));
        }

        // Actualizar
        $factura->update([
            'numero_factura' => $validated['numero_factura'],
            'tercero_id' => $validated['tercero_id'],
            'fecha_factura' => $validated['fecha_factura'],
            'fecha_vencimiento' => $validated['fecha_vencimiento'],
            'subtotal' => $validated['subtotal'],
            'iva' => $validated['iva'] ?? 0,
            'descuento' => $validated['descuento'] ?? 0,
            'total_pagar' => $total,
            'motonave' => $validated['motonave'],
            'trb' => $validated['trb'],
            'servicio_descripcion' => $validated['servicio_descripcion'],
            'estado' => $validated['estado'],
        ]);

        // Registrar auditoría
        $this->registrarAuditoria('actualizar', 'factura', $factura->id, [
            'anterior' => $datosAnteriores,
            'nuevo' => $validated,
        ]);

        return redirect()->route('facturas.show', $factura)
            ->with('success', 'Factura actualizada exitosamente.');
    }

    /**
     * Eliminar factura
     */
    public function destroy(Factura $factura)
    {
        $this->autorizarTenant($factura);

        // Eliminar PDF si existe
        if ($factura->pdf_path && Storage::exists('local/' . $factura->pdf_path)) {
            Storage::delete('local/' . $factura->pdf_path);
        }

        $facturaData = [
            'numero_factura' => $factura->numero_factura,
            'cufe' => $factura->cufe,
            'total_pagar' => $factura->total_pagar,
        ];

        $factura->delete();

        // Registrar auditoría
        $this->registrarAuditoria('eliminar', 'factura', $factura->id, $facturaData);

        return redirect()->route('facturas.index')
            ->with('success', 'Factura eliminada exitosamente.');
    }

    /**
     * Mostrar PDF en línea
     */
    public function showPdf(Factura $factura)
    {
        $this->autorizarTenant($factura);

        // Ruta del PDF: storage/app/private/facturas/{tenant_id}/{factura_id}.pdf
        $path = storage_path("app/private/facturas/{$factura->tenant_id}/{$factura->id}.pdf");

        if (!file_exists($path)) {
            abort(404, 'El archivo PDF no se encuentra.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Factura-' . $factura->numero_factura . '.pdf"'
        ]);
    }

    /**
     * Descargar PDF
     */
    public function downloadPdf(Factura $factura)
    {
        $this->autorizarTenant($factura);

        // Ruta del PDF: storage/app/private/facturas/{tenant_id}/{factura_id}.pdf
        $path = storage_path("app/private/facturas/{$factura->tenant_id}/{$factura->id}.pdf");

        if (!file_exists($path)) {
            abort(404, 'El archivo PDF no se encuentra.');
        }

        return response()->download($path, 'Factura-' . $factura->numero_factura . '.pdf', [
            'Content-Type' => 'application/pdf'
        ]);
    }

    /**
     * Generar CUFE (Código Único de Factura Electrónica)
     * En producción, esto se genera según normas de DIAN
     */
    private function generarCufe($numeroFactura, $fechaFactura)
    {
        return strtoupper(hash('sha256', $numeroFactura . $fechaFactura . microtime()));
    }

    /**
     * Autorizar que la factura pertenece al tenant actual
     */
    private function autorizarTenant(Factura $factura)
    {
        if ($factura->tenant_id !== session('tenant_id')) {
            abort(403, 'No tienes acceso a esta factura.');
        }
    }

    /**
     * Registrar en auditoría
     */
    private function registrarAuditoria($accion, $modelo, $modeloId, $detalles)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'tenant_id' => session('tenant_id'),
                'accion' => $accion,
                'modelo' => $modelo,
                'modelo_id' => $modeloId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'detalles' => json_encode($detalles),
            ]);
        }
    }
}

