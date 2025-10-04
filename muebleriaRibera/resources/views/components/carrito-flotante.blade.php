<div class="cart-floating" id="cartFloating">
    <!-- Botón flotante -->
    <button class="cart-floating-btn" id="cartFloatingBtn">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">
            {{ auth()->check() && auth()->user()->carritoItems ? auth()->user()->carritoItems->count() : 0 }}
        </span>
    </button>

    <!-- Panel del carrito -->
    <div class="cart-panel" id="cartPanel">
        <div class="cart-header">
            <h5 class="mb-0">
                <i class="fas fa-shopping-cart me-2"></i>Mi Carrito
            </h5>
            <button class="cart-close-btn" id="cartCloseBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="cart-body" id="cartBody">
            @auth
                @if(auth()->user()->carritoItems && auth()->user()->carritoItems->count() > 0)
                    @foreach(auth()->user()->carritoItems as $item)
                    <div class="cart-item" data-item-id="{{ $item->id }}">
                        <div class="cart-item-image">
                            @if($item->producto->imagen_url)
                            <img src="{{ asset('storage/' . $item->producto->imagen_url) }}" 
                                 alt="{{ $item->producto->nombre }}">
                            @else
                            <div class="no-image">
                                <i class="fas fa-box"></i>
                            </div>
                            @endif
                        </div>
                        <div class="cart-item-details">
                            <h6 class="cart-item-title">{{ Str::limit($item->producto->nombre, 30) }}</h6>
                            <p class="cart-item-price">{{ $item->producto->precio_bs_formateado }}</p>
                            <div class="cart-item-quantity">
                                <button class="quantity-btn minus" data-item-id="{{ $item->id }}">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity">{{ $item->cantidad }}</span>
                                <button class="quantity-btn plus" data-item-id="{{ $item->id }}">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button class="cart-item-remove" data-item-id="{{ $item->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @endforeach
                @else
                    <div class="cart-empty">
                        <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                        <p>Tu carrito está vacío</p>
                    </div>
                @endif
            @else
                <div class="cart-empty">
                    <i class="fas fa-sign-in-alt fa-2x mb-3"></i>
                    <p>Inicia sesión para ver tu carrito</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-2">
                        Iniciar Sesión
                    </a>
                </div>
            @endauth
        </div>

        @auth
        @if(auth()->user()->carritoItems && auth()->user()->carritoItems->count() > 0)
        <div class="cart-footer">
            <div class="cart-total">
                <strong>Total: 
                    <span id="cartTotal">
                        Bs {{ number_format(auth()->user()->carritoItems->sum(function($item) { 
                            return $item->producto->precio_bs * $item->cantidad; 
                        }), 2) }}
                    </span>
                </strong>
            </div>
            <div class="cart-actions">
                <a href="{{ route('carrito.index') }}" class="btn btn-outline-primary btn-sm">
                    Ver Carrito Completo
                </a>
                <form action="{{ route('chats.crear-desde-carrito') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">
                        Iniciar Compra
                    </button>
                </form>
            </div>
        </div>
        @endif
        @endauth
    </div>
</div>