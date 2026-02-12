<?php

namespace App\Http\Controllers;

use App\Models\Tercero;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TerceroController extends Controller
{
    // Middleware se configura en las rutas en Laravel 12+

    /**
     * Listar terceros del tenant actual (paginado)
     */
    public function index(Request $request)
    {
        // Usar vista con Livewire
        return view('terceros.index');
    }

    /**
     * Mostrar detalle de un tercero
     */
    public function show(Tercero $tercero)
    {
        $this->autorizarTenant($tercero);

        $tenantId = session('tenant_id');

        // Estadísticas del tercero
        $stats = \App\Models\Factura::where('tercero_id', $tercero->id)
            ->where('tenant_id', $tenantId)
            ->selectRaw('
                COUNT(*) as total_facturas,
                COALESCE(SUM(total_pagar), 0) as total_facturado,
                MAX(fecha_factura) as ultima_factura
            ')
            ->first();

        // Últimas 5 facturas
        $ultimasFacturas = \App\Models\Factura::where('tercero_id', $tercero->id)
            ->where('tenant_id', $tenantId)
            ->orderBy('fecha_factura', 'desc')
            ->limit(5)
            ->get();

        // Historial de auditoría del tercero
        $historial = AuditLog::where('model_type', Tercero::class)
            ->where('model_id', $tercero->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('terceros.show', [
            'tercero' => $tercero,
            'stats' => [
                'total_facturas' => $stats->total_facturas ?? 0,
                'total_facturado' => $stats->total_facturado ?? 0,
                'ultima_factura' => $stats->ultima_factura,
            ],
            'ultimasFacturas' => $ultimasFacturas,
            'historial' => $historial,
        ]);
    }

    /**
     * Mostrar formulario de crear tercero
     */
    public function create()
    {
        return view('terceros.create');
    }

    /**
     * Guardar nuevo tercero
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');

        // Validar entrada
        $validated = $request->validate([
            'nit' => 'required|string|max:20',
            'nombre_razon_social' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string|max:1000',
        ]);

        // Validar NIT colombiano
        if (!Tercero::validarNitColombia($validated['nit'])) {
            return back()->withErrors(['nit' => 'NIT inválido. Debe contener solo números.'])->withInput();
        }

        // Verificar unicidad del NIT en el tenant
        if (Tercero::byTenant($tenantId)->where('nit', $validated['nit'])->exists()) {
            return back()->withErrors(['nit' => 'Este NIT ya existe en tu empresa.'])->withInput();
        }

        // Buscar duplicados
        $duplicados = Tercero::buscarDuplicados(
            $tenantId,
            $validated['nombre_razon_social'],
            $validated['nit']
        );

        if ($duplicados && !$request->filled('confirmar_crear')) {
            // Retornar vista con modal de duplicados
            return view('terceros.create', [
                'tercero' => $validated,
                'duplicados' => $duplicados,
            ])->with('mostrar_duplicados', true);
        }

        // Crear tercero
        $tercero = Tercero::create([
            'tenant_id' => $tenantId,
            ...$validated,
        ]);

        // Registrar en auditoría
        $this->registrarAuditoria('crear', 'tercero', $tercero->id, [
            'nit' => $tercero->nit,
            'nombre' => $tercero->nombre_razon_social,
        ]);

        return redirect()->route('terceros.index')
            ->with('success', 'Tercero creado exitosamente.');
    }

    /**
     * Mostrar formulario de editar tercero
     */
    public function edit(Tercero $tercero)
    {
        $this->autorizarTenant($tercero);

        return view('terceros.edit', ['tercero' => $tercero]);
    }

    /**
     * Actualizar tercero
     */
    public function update(Request $request, Tercero $tercero)
    {
        $this->autorizarTenant($tercero);
        $tenantId = session('tenant_id');

        // Validar entrada
        $validated = $request->validate([
            'nit' => 'required|string|max:20',
            'nombre_razon_social' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,inactivo',
        ]);

        // Validar NIT colombiano
        if (!Tercero::validarNitColombia($validated['nit'])) {
            return back()->withErrors(['nit' => 'NIT inválido.'])->withInput();
        }

        // Verificar unicidad del NIT (excluyendo el tercero actual)
        if (Tercero::byTenant($tenantId)
            ->where('nit', $validated['nit'])
            ->where('id', '!=', $tercero->id)
            ->exists()
        ) {
            return back()->withErrors(['nit' => 'Este NIT ya existe en tu empresa.'])->withInput();
        }

        // Buscar duplicados (excluyendo el tercero actual)
        $duplicados = Tercero::buscarDuplicados(
            $tenantId,
            $validated['nombre_razon_social'],
            $validated['nit'],
            $tercero->id
        );

        if ($duplicados && !$request->filled('confirmar_actualizar')) {
            return view('terceros.edit', [
                'tercero' => $tercero,
                'duplicados' => $duplicados,
            ])->with('mostrar_duplicados', true);
        }

        // Guardar datos anteriores para auditoría
        $camposAnteriores = $tercero->only([
            'nit',
            'nombre_razon_social',
            'direccion',
            'telefono',
            'email',
            'estado',
        ]);

        // Actualizar
        $tercero->update($validated);

        // Registrar en auditoría
        $this->registrarAuditoria('actualizar', 'tercero', $tercero->id, [
            'anterior' => $camposAnteriores,
            'nuevo' => $validated,
        ]);

        return redirect()->route('terceros.index')
            ->with('success', 'Tercero actualizado exitosamente.');
    }

    /**
     * Eliminar tercero
     */
    public function destroy(Tercero $tercero)
    {
        $this->autorizarTenant($tercero);

        // Verificar que no tiene facturas
        if ($tercero->tieneFacturas()) {
            return back()->withErrors([
                'delete' => 'No puedes eliminar este tercero porque tiene facturas asociadas.',
            ]);
        }

        $terceroData = [
            'nit' => $tercero->nit,
            'nombre' => $tercero->nombre_razon_social,
        ];

        // Eliminar
        $tercero->delete();

        // Registrar en auditoría
        $this->registrarAuditoria('eliminar', 'tercero', $tercero->id, $terceroData);

        return redirect()->route('terceros.index')
            ->with('success', 'Tercero eliminado exitosamente.');
    }

    /**
     * Buscar duplicados (AJAX)
     */
    public function searchDuplicates(Request $request)
    {
        $tenantId = session('tenant_id');

        $validated = $request->validate([
            'nombre_razon_social' => 'required|string',
            'nit' => 'required|string',
        ]);

        $duplicados = Tercero::buscarDuplicados(
            $tenantId,
            $validated['nombre_razon_social'],
            $validated['nit']
        );

        return response()->json([
            'tiene_duplicados' => !empty($duplicados),
            'duplicados' => $duplicados,
        ]);
    }

    /**
     * Autorizar que el tercero pertenece al tenant actual
     */
    private function autorizarTenant(Tercero $tercero)
    {
        if ($tercero->tenant_id !== session('tenant_id')) {
            abort(403, 'No tienes acceso a este tercero.');
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

