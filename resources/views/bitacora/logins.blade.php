@extends('layouts.app')

@section('title', 'Bitácora de Logins')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-sign-in-alt"></i> Bitácora de Logins
    </h1>
    <a href="{{ route('bitacora.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Resumen
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Éxito</th>
                        <th>IP</th>
                        <th>User Agent</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logins as $login)
                    <tr>
                        <td>
                            @if($login->usuario)
                            <strong>{{ $login->usuario->nombre }}</strong>
                            <br><small class="text-muted">{{ $login->usuario->email }}</small>
                            @else
                            <span class="text-muted">Usuario no encontrado</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ $login->accion }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $login->exito ? 'success' : 'danger' }}">
                                {{ $login->exito ? 'Éxito' : 'Fallido' }}
                            </span>
                        </td>
                        <td><code>{{ $login->ip_address }}</code></td>
                        <td>
                            <small class="text-muted" title="{{ $login->user_agent }}">
                                {{ Str::limit($login->user_agent, 50) }}
                            </small>
                        </td>
                        <td>{{ $login->fecha_login->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay logins registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $logins->links() }}
        </div>
    </div>
</div>
@endsection