@extends('layouts.public')

@section('title', 'Contacto - Tienda Online')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="h2 mb-4">Contáctanos</h1>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-map-marker-alt fa-2x text-primary mb-3"></i>
                            <h5>Dirección</h5>
                            <p class="text-muted">Av. Principal #123<br>Ciudad, Estado</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                            <h5>Teléfono</h5>
                            <p class="text-muted">+1 (555) 123-4567<br>Lun - Vie: 9am - 6pm</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                            <h5>Email</h5>
                            <p class="text-muted">info@tiendaonline.com<br>ventas@tiendaonline.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                            <h5>Horario</h5>
                            <p class="text-muted">Lun - Vie: 9am - 6pm<br>Sáb: 9am - 2pm</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Envíanos un Mensaje</h5>
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="asunto" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="mensaje" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Enviar Mensaje
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection