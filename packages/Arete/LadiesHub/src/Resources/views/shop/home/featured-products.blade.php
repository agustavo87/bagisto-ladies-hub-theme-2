@if (count(app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts()))
    <section class="featured-products">

        <div class="featured-heading">
            <h2> {{ __('shop::app.home.featured-products') }} </h2>
            <hr class="featured-separator">
        </div>

        <div class="featured-grid product-grid-4">

            @foreach (app('Webkul\Product\Repositories\ProductRepository')->getFeaturedProducts() as $productFlat)

                @if (core()->getConfigData('catalog.products.homepage.out_of_stock_items'))
                    @include ('shop::products.list.card', ['product' => $productFlat])
                @else
                    @if ($productFlat->isSaleable())
                        @include ('shop::products.list.card', ['product' => $productFlat])
                    @endif
                @endif

            @endforeach

        </div>
    </section>
@endif