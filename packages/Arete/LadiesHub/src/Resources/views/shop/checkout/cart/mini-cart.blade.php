<?php $cart = cart()->getCart(); ?>

@if ($cart)
    <?php $items = $cart->items; ?>

    <div class="dropdown-toggle">
        <a class="cart-link" href="{{ route('shop.checkout.cart.index') }}">
            <x-cart-icon class="cart-icon" />
            <span class="count-bag">
                <span class="count">
                    {{ $cart->items->count() }}
                </span>
            </span>
        </a>

        {{-- <span class="name" style="display: none">
            {{ __('shop::app.header.cart') }}
        </span> --}}
        <x-arrow-down-icon class="arrow-down-icon" />
{{-- 
        <i class="icon arrow-down-icon">
        </i> --}}
    </div>

    <div class="dropdown-list">
        <div class="dropdown-container">
            <div class="dropdown-cart">
                <div class="dropdown-header">
                    <p class="heading">
                        {{ __('shop::app.checkout.cart.cart-subtotal') }} -

                        {!! view_render_event('bagisto.shop.checkout.cart-mini.subtotal.before', ['cart' => $cart]) !!}

                        <b>{{ core()->currency($cart->base_sub_total) }}</b>

                        {!! view_render_event('bagisto.shop.checkout.cart-mini.subtotal.after', ['cart' => $cart]) !!}
                    </p>
                </div>

                <div class="dropdown-content">
                    @foreach ($items as $item)

                        <div class="item">
                            <div class="item-image" >
                                @php
                                    $images = $item->product->getTypeInstance()->getBaseImage($item);
                                @endphp
                                <img src="{{ $images['small_image_url'] }}"  alt=""/>
                            </div>

                            <div class="item-details">
                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.name.before', ['item' => $item]) !!}

                                <div class="item-name">{{ $item->name }}</div>

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.name.after', ['item' => $item]) !!}


                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.options.before', ['item' => $item]) !!}

                                @if (isset($item->additional['attributes']))
                                    <div class="item-options">

                                        @foreach ($item->additional['attributes'] as $attribute)
                                            <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                        @endforeach

                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.options.after', ['item' => $item]) !!}


                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.price.before', ['item' => $item]) !!}

                                <div class="item-price"><b>{{ core()->currency($item->base_total) }}</b></div>

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.price.after', ['item' => $item]) !!}


                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.quantity.before', ['item' => $item]) !!}

                                <div class="item-qty">Quantity - {{ $item->quantity }}</div>

                                {!! view_render_event('bagisto.shop.checkout.cart-mini.item.quantity.after', ['item' => $item]) !!}
                            </div>
                        </div>

                    @endforeach
                </div>

                <div class="dropdown-footer">
                    <a href="{{ route('shop.checkout.cart.index') }}">{{ __('shop::app.minicart.view-cart') }}</a>

                    @php
                        $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;
                    @endphp

                    <proceed-to-checkout
                        href="{{ route('shop.checkout.onepage.index') }}"
                        add-class="btn btn-primary btn-lg"
                        text="{{ __('shop::app.minicart.checkout') }}"
                        is-minimum-order-completed="{{ $cart->checkMinimumOrder() }}"
                        minimum-order-message="{{ __('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]) }}"
                        style="color: white;">
                    </proceed-to-checkout>
                </div>
            </div>
        </div>
    </div>

@else
    <div class="dropdown-toggle" style="pointer-events: none;">
            {{-- <span class="icon cart-icon"></span> --}}
            <x-cart-icon class="cart-icon disabled-cart" />
            {{-- <span class="name" style="display: none">{{ __('shop::app.minicart.cart') }}<span class="count"> ({{ __('shop::app.minicart.zero') }}) </span></span> --}}
    </div>
@endif