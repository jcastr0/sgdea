@extends('layouts.app')

@section('title', 'Editar Tenant')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil"></i> Editar Tenant
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

                    <form action="{{ route('admin.tenants.update', $tenant->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="domain" class="form-label">Dominio</label>
                            <input type="text" class="form-control @error('domain') is-invalid @enderror"
                                   id="domain" name="domain" value="{{ old('domain', $tenant->domain) }}" required>
                            @error('domain')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>
                                    Activo
                                </option>
                                <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactivo
                                </option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="bi bi-check-circle"></i> Actualizar
                            </button>
                            <a href="{{ route('admin.tenants.show', $tenant->id) }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

