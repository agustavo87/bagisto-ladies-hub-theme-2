@inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

@php
    $showCompare = core()->getConfigData('general.content.shop.compare_option') == "1" ? true : false;

    $showWishlist = core()->getConfigData('general.content.shop.wishlist_option') == "1" ? true : false;
@endphp

<div class="cart-wish-wrap">
    <div class="left-add-buttons">
        <form action="{{ route('cart.add', $product->product_id) }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->product_id }}">
            <input type="hidden" name="quantity" value="1">
            <button class="btn btn-sm btn-primary addtocart" {{ $product->isSaleable() ? '' : 'disabled' }}>
                <span class="content-size-default">
                    <x-cart-icon class="icon svg-cart-icon" />
                    {{ ($product->type == 'booking') ?  __('shop::app.products.book-now') :  __('shop::app.products.add-to-cart') }}
                </span>
                <span class="content-size-sm">
                    <x-cart-icon class="icon svg-cart-icon" />
                    <span>
                        {{ ($product->type == 'booking') ?  __('book now') :  __('add') }}
                    </span>  
                </span>
                {{-- <span class="content-size-xs">
                    <x-cart-icon class="icon svg-cart-icon" />
                    <span>Add</span>  
                </span> --}}
            </button>
        </form>
    </div>
    <div class="right-add-buttons">
        @if ($showWishlist)
            @include('shop::products.wishlist')
        @endif
    
        @if ($showCompare)
            @include('shop::products.compare', [
                'productId' => $product->id
            ])
        @endif
    </div>

</div>