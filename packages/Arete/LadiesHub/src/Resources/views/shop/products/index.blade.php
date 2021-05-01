@extends('shop::layouts.master')

@section('page_title')
    {{ trim($category->meta_title) != "" ? $category->meta_title : $category->name }}
@stop

@section('seo')
    <meta name="description" content="{{ trim($category->meta_description) != "" ? $category->meta_description : \Illuminate\Support\Str::limit(strip_tags($category->description), 120, '') }}"/>

    <meta name="keywords" content="{{ $category->meta_keywords }}"/>

    @if (core()->getConfigData('catalog.rich_snippets.categories.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getCategoryJsonLd($category) !!}
        </script>
    @endif
@stop

@section('content-wrapper')
    @inject ('productRepository', 'Webkul\Product\Repositories\ProductRepository')

    <div class="main">
        {!! view_render_event('bagisto.shop.products.index.before', ['category' => $category]) !!}

        <div class="category-container">

            @if (in_array($category->display_mode, [null, 'products_only', 'products_and_description']))
                @include ('shop::products.list.layered-navigation')
            @endif

            <div class="category-block" @if ($category->display_mode == 'description_only') style="width: 100%" @endif>
                <div class="hero-image mb-35">
                    @if (!is_null($category->image))
                        <img class="logo" src="{{ $category->image_url }}" alt="" />
                    @endif
                </div>

                @if (in_array($category->display_mode, [null, 'description_only', 'products_and_description']))
                    @if ($category->description)
                        <div class="category-description">
                            {!! $category->description !!}
                        </div>
                    @endif
                @endif

                @if (in_array($category->display_mode, [null, 'products_only', 'products_and_description']))
                    <?php $products = $productRepository->getAll($category->id); ?>

                    @include ('shop::products.list.toolbar')

                    @if ($products->count())

                        @inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

                        @if ($toolbarHelper->getCurrentMode() == 'grid')
                            <div class="product-grid-3">
                                @foreach ($products as $productFlat)

                                    @include ('shop::products.list.card', ['product' => $productFlat])

                                @endforeach
                            </div>
                        @else
                            <div class="product-list">
                                @foreach ($products as $productFlat)

                                    @include ('shop::products.list.card', ['product' => $productFlat])

                                @endforeach
                            </div>
                        @endif

                        {!! view_render_event('bagisto.shop.products.index.pagination.before', ['category' => $category]) !!}

                        <div class="bottom-toolbar">
                            {{ $products->appends(request()->input())->links() }}
                        </div>

                        {!! view_render_event('bagisto.shop.products.index.pagination.after', ['category' => $category]) !!}

                    @else

                        <div class="product-list empty">
                            <h2>{{ __('shop::app.products.whoops') }}</h2>

                            <p>
                                {{ __('shop::app.products.empty') }}
                            </p>
                        </div>

                    @endif
                @endif
            </div>
        </div>

        {!! view_render_event('bagisto.shop.products.index.after', ['category' => $category]) !!}
    </div>
@stop

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.responsive-layred-filter').css('display','none');
            $(".sort-icon, .filter-icon, .filter-close, .sort-close").on('click', function(e){
                var currentElement = $(e.currentTarget);
                if (currentElement.hasClass('sort-icon')) {
                    // currentElement.removeClass('sort-icon');
                    // currentElement.addClass('icon-menu-close-adj');

                    currentElement.css('display', 'none');
                    $('.sort-close').css('display', 'inline-block');

                    // currentElement.next().removeClass();
                    // currentElement.next().addClass('icon filter-icon');
                    $('.filter-close').css('display', 'none');
                    $('.filter-icon').css('display', 'inline-block');
                    $('.responsive-layred-filter').css('display','none');
                    $('.pager').css('display','flex');
                    // $('.pager').css('justify-content','space-around');
                    // $('.pager').css('justify-content','space-around');
                } else if (currentElement.hasClass('filter-icon')) {
                    currentElement.css('display', 'none');
                    $('.filter-close').css('display', 'inline-block');
                    // currentElement.removeClass('filter-icon');
                    // currentElement.addClass('icon-menu-close-adj');
                    // currentElement.prev().removeClass();
                    // currentElement.prev().addClass('icon sort-icon');
                    $('.sort-close').css('display', 'none');
                    $('.sort-icon').css('display', 'inline-block');
                    $('.pager').css('display','none');
                    $('.responsive-layred-filter').css('display','block');
                    $('.responsive-layred-filter').css('margin-top','10px');
                } else {
                    currentElement.css('display','none');
                    $('.responsive-layred-filter').css('display','none');
                    $('.pager').css('display','');
                    // $('.pager').css('justify-content','');
                    $('.filter-close').css('display', 'none');
                    $('.sort-close').css('display', 'none');
                    $('.sort-icon').css('display', 'inline-block');
                    $('.filter-icon').css('display', 'inline-block');
                    // if ($(this).index() == 0) {
                    //     currentElement.addClass('sort-icon');
                    // } else {
                    //     currentElement.addClass('filter-icon');
                    // }
                }
            });
        });
    </script>
@endpush