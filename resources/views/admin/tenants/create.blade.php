@extends('layouts.app')

@section('title', 'Crear Tenant')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Crear Nuevo Tenant
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.tenants.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="company_name" class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                   id="company_name" name="company_name" value="{{ old('company_name') }}"
                                   required placeholder="Ej: Marítimos Arboleda">
                            @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="domain" class="form-label">Dominio</label>
                            <input type="text" class="form-control @error('domain') is-invalid @enderror"
                                   id="domain" name="domain" value="{{ old('domain') }}"
                                   required placeholder="Ej: maritimosarboleda.com">
                            @error('domain')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">El dominio será usado para acceder a la plataforma</small>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">
                            <i class="bi bi-palette"></i> Configuración Visual
                        </h6>

                        <div class="mb-3">
                            <label for="color_primary" class="form-label">Color Primario</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color"
                                       id="color_primary" name="color_primary"
                                       value="{{ old('color_primary', '#2767C6') }}">
                                <span class="input-group-text">#2767C6 (Default)</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Crear Tenant
                            </button>
                            <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>

                    <div class="alert alert-info mt-4">
                        <strong>Nota importante:</strong> Una vez creado el tenant, el administrador podrá
                        registrarse accediendo al dominio de la empresa (ej: https://maritimosarboleda.com/register).
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



