<?php
    $term = request()->input('term');
    $image_search = request()->input('image-search');

    if (! is_null($term)) {
        $serachQuery = 'term='.request()->input('term');
    }
?>

@php
if(auth()->guard('customer')->check()) {
    $compareCount = app('Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository')
        ->count([
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);
   
} else {
    $compareCount = 0;
}
@endphp

<div class="header-wrapper">
    <div class="header" id="header">
        <div class="header-top" id="header-top">
            <div class="left-content">
                <ul class="search-container">
                    <li class="search-group">
                        <form role="search" action="{{ route('shop.search.index') }}" method="GET" style="display: inherit;">
                            <label for="search-bar" style="position: absolute; z-index: -1;">Search</label>
                            <input
                                required
                                name="term"
                                type="search"
                                value="{{ ! $image_search ? $term : '' }}"
                                class="search-field"
                                id="search-bar"
                                placeholder="{{ __('shop::app.header.search-text') }}"
                            >
    
                            <image-search-component></image-search-component>
    
                            <div class="search-icon-wrapper">
    
                                <button class="" class="background: none;" aria-label="Search">
                                    {{-- <i class="icon icon-search"></i> --}}
                                    <x-icon-search class="icon icon-search" />
                                </button>
                            </div>
                        </form>
                    </li>
                </ul>
            </div>

            <div class="center-content">
                <ul class="logo-container">
                    <li>
                        <a href="{{ route('shop.home.index') }}" aria-label="Logo">
                            {{-- <div  --}}
                                {{-- <strong>Ladies</strong><span class="soft">Hub</span> --}}
                            <x-logo-lg  class="logo" id="main-logo" />
                            {{-- </div> --}}
                            <x-logo-xs class="logo-xs" />
                            {{-- <div class="logo-xs">
                                <strong>L</strong><span class="soft">H</span>
                            </div> --}}
                            {{--
                            Logo based on img src is not modifiable by css
    
                            @if ($logo = core()->getCurrentChannel()->logo_url)
                                <img class="logo" src="{{ $logo }}" alt="" />
                            @else
                                <img class="logo" src="{{ bagisto_asset('images/logo.svg') }}" alt="" />
                            @endif 
                            --}}
                            
                            {{-- <x-lhub-logo  class="logo" />
                            <x-lhub-logo-xs  class="logo-xs" /> --}}
                        </a>
                    </li>
                </ul>
            </div>
    
            <div class="right-content">
    
                <span class="search-box">
                    <x-icon-search class="icon icon-search" id="search" />
                </span>
    
                <ul class="right-content-menu">
    
                    {{-- Compare --}}
    
                    {!! view_render_event('bagisto.shop.layout.header.compare-item.before') !!}
    
                    @php
                        $showCompare = core()->getConfigData('general.content.shop.compare_option') == "1" ? true : false
                    @endphp
    
                    @if ($showCompare)
                        <li class="compare-dropdown-container" id="compare-dropdown-container" >
                            <a
                                @auth('customer')
                                    href="{{ route('velocity.customer.product.compare') }}"
                                @endauth
    
                                @guest('customer')
                                    href="{{ route('velocity.product.compare') }}"
                                @endguest
                                >
                                <x-compare-icon class="compare-icon"/>
                                {{-- <span class="name">
                                    {{ __('shop::app.customer.compare.text') }}
                                </span> --}}
                                <span class="count compare-items-count" style="display: {{ $compareCount ? 'flex' : 'none' }}">
                                    <span id="compare-items-count">{{ $compareCount }}</span>
                                </span>
                            </a>
                        </li>
                    @endif
    
                    {!! view_render_event('bagisto.shop.layout.header.compare-item.after') !!}
    
                    {{-- Compare / --}}
    
                    {{-- Currency --}}
    
                    {!! view_render_event('bagisto.shop.layout.header.currency-item.before') !!}
    
                    @if (core()->getCurrentChannel()->currencies->count() > 1)
                        <li class="currency-switcher">
                            <span class="dropdown-toggle">
                                {{ core()->getCurrentCurrencyCode() }}
                                <x-arrow-down-icon class="arrow-down-icon"
                                {{-- <i class="icon arrow-down-icon"></i> --}}
                            </span>
    
                            <ul class="dropdown-list currency">
                                @foreach (core()->getCurrentChannel()->currencies as $currency)
                                    <li>
                                        @if (isset($serachQuery))
                                            <a href="?{{ $serachQuery }}&currency={{ $currency->code }}">{{ $currency->code }}</a>
                                        @else
                                            <a href="?currency={{ $currency->code }}">{{ $currency->code }}</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
    
                    {!! view_render_event('bagisto.shop.layout.header.currency-item.after') !!}
    
                    {{-- Currency / --}}
    
                    {{-- Cart --}}
    
                    {!! view_render_event('bagisto.shop.layout.header.cart-item.before') !!}
    
                    <li class="cart-dropdown-container">
    
                        @include('shop::checkout.cart.mini-cart')
    
                    </li>
    
                    {!! view_render_event('bagisto.shop.layout.header.cart-item.after') !!}
    
                    {{-- Cart / --}}
    
                    {{-- Account Options --}}
    
                    {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}
    
                    <li class="account-options">
                        <span class="dropdown-toggle">
                            <span class="account-icon-wrapper">
                                <x-account-icon class="account-icon" />
                            </span>
                            {{-- <span class="name">{{ __('shop::app.header.account') }}</span> --}}
                            {{-- <i class="icon arrow-down-icon"></i> --}}
                            <x-arrow-down-icon class="arrow-down-icon" />
                        </span>
    
                        @guest('customer')
                            <ul class="dropdown-list account guest">
                                <li>
                                    <div>
                                        <label style="color: #9e9e9e; font-weight: 700; text-transform: uppercase; font-size: 15px;">
                                            {{ __('shop::app.header.title') }}
                                        </label>
                                    </div>
    
                                    <div style="margin-top: 5px;">
                                        <span style="font-size: 12px;">{{ __('shop::app.header.dropdown-text') }}</span>
                                    </div>
    
                                    <div style="margin-top: 15px;">
                                        <a class="btn btn-primary btn-md" href="{{ route('customer.session.index') }}" style="color: #ffffff">
                                            {{ __('shop::app.header.sign-in') }}
                                        </a>
    
                                        <a class="btn btn-primary btn-md" href="{{ route('customer.register.index') }}" style="float: right; color: #ffffff">
                                            {{ __('shop::app.header.sign-up') }}
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        @endguest
    
                        @auth('customer')
                            @php
                                $showWishlist = core()->getConfigData('general.content.shop.wishlist_option') == "1" ? true : false;
                            @endphp
    
                            <ul class="dropdown-list account customer">
                                <li>
                                    <div>
                                        <label style="color: #9e9e9e; font-weight: 700; text-transform: uppercase; font-size: 15px;">
                                            {{ auth()->guard('customer')->user()->first_name }}
                                        </label>
                                    </div>
    
                                    <ul>
                                        <li>
                                            <a href="{{ route('customer.profile.index') }}">{{ __('shop::app.header.profile') }}</a>
                                        </li>
    
                                        @if ($showWishlist)
                                            <li>
                                                <a href="{{ route('customer.wishlist.index') }}">{{ __('shop::app.header.wishlist') }}</a>
                                            </li>
                                        @endif
    
                                        <li>
                                            <a href="{{ route('shop.checkout.cart.index') }}">{{ __('shop::app.header.cart') }}</a>
                                        </li>
    
                                        <li>
                                            <a href="{{ route('customer.session.destroy') }}">{{ __('shop::app.header.logout') }}</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        @endauth
                    </li>
    
                    {!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}
    
                    {{-- Account Options / --}}
    
                </ul>
    
                <span id="menu-box" class="menu-box" >
                    <x-icon-menu class="icon h-icon-menu" id="hammenu"></x-icon-menu>
                    <x-icon-menu-close class="icon h-icon-menu-close" id="hammenu-close" style="display: none"></x-icon-menu-close>
                </span>
            </div>
        </div>
    
        <div class="header-bottom" id="header-bottom">
            @include('shop::layouts.header.nav-menu.navmenu')
        </div>
    
        <div class="search-responsive mt-10" id="search-responsive">
            <form role="search" action="{{ route('shop.search.index') }}" method="GET" style="display: inherit;">
                <div class="search-content">
                    {{-- <i class="icon icon-menu-back right"></i> --}}
                    <div class="icon-menu-back-section">
                        <x-arrow-back-icon class="x-icon-menu-back" />
                    </div>
                    
                    <image-search-component></image-search-component>
                    <div class="search-input-section">
                        <input 
                            type="search" 
                            name="term" 
                            class="search" 
                            value="{{ ! $image_search ? $term : '' }}"
                            placeholder="{{ __('shop::app.header.search-text') }}">
                    </div>
    
                    <div class="search-button-section">
                        <button>
                            <x-icon-search class="icon icon-search" />
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div> 
</div>
    



@push('scripts')

{{-- Image Search Component --}}
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet" defer></script> 

    <script type="text/x-template" id="image-search-component-template">
        <div v-if="image_search_status">
            <label class="image-search-container" :for="'image-search-container-' + _uid">
                <i class="icon camera-icon"></i>

                <input type="file" :id="'image-search-container-' + _uid" ref="image_search_input" v-on:change="uploadImage()"/>

                <img :id="'uploaded-image-url-' +  + _uid" :src="uploaded_image_url" alt="" width="20" height="20" />
            </label>
        </div>
    </script>

    <script>
        Vue.component('image-search-component', {

            template: '#image-search-component-template',

            data: function() {
                return {
                    uploaded_image_url: '',
                    image_search_status: "{{core()->getConfigData('general.content.shop.image_search') == '1' ? 'true' : 'false'}}" == 'true'
                }
            },

            methods: {
                uploadImage: function() {
                    var imageInput = this.$refs.image_search_input;

                    if (imageInput.files && imageInput.files[0]) {
                        if (imageInput.files[0].type.includes('image/')) {
                            var self = this;

                            if (imageInput.files[0].size <= 2000000) {
                                self.$root.showLoader();

                                var formData = new FormData();

                                formData.append('image', imageInput.files[0]);

                                axios.post("{{ route('shop.image.search.upload') }}", formData, {headers: {'Content-Type': 'multipart/form-data'}})
                                    .then(function(response) {
                                        self.uploaded_image_url = response.data;

                                        var net;

                                        async function app() {
                                            var analysedResult = [];

                                            var queryString = '';

                                            net = await mobilenet.load();

                                            const imgElement = document.getElementById('uploaded-image-url-' +  + self._uid);

                                            try {
                                                const result = await net.classify(imgElement);

                                                result.forEach(function(value) {
                                                    queryString = value.className.split(',');

                                                    if (queryString.length > 1) {
                                                        analysedResult = analysedResult.concat(queryString)
                                                    } else {
                                                        analysedResult.push(queryString[0])
                                                    }
                                                });
                                            } catch (error) {
                                                self.$root.hideLoader();

                                                window.flashMessages = [
                                                    {
                                                        'type': 'alert-error',
                                                        'message': "{{ __('shop::app.common.error') }}"
                                                    }
                                                ];

                                                self.$root.addFlashMessages();
                                            };

                                            localStorage.searched_image_url = self.uploaded_image_url;

                                            queryString = localStorage.searched_terms = analysedResult.join('_');

                                            self.$root.hideLoader();

                                            window.location.href = "{{ route('shop.search.index') }}" + '?term=' + queryString + '&image-search=1';
                                        }

                                        app();
                                    })
                                    .catch(function(error) {
                                        self.$root.hideLoader();

                                        window.flashMessages = [
                                            {
                                                'type': 'alert-error',
                                                'message': "{{ __('shop::app.common.error') }}"
                                            }
                                        ];

                                        self.$root.addFlashMessages();
                                    });
                            } else {

                                imageInput.value = '';

                                        window.flashMessages = [
                                            {
                                                'type': 'alert-error',
                                                'message': "{{ __('shop::app.common.image-upload-limit') }}"
                                            }
                                        ];

                                self.$root.addFlashMessages();

                            }
                        } else {
                            imageInput.value = '';

                            alert('Only images (.jpeg, .jpg, .png, ..) are allowed.');
                        }
                    }
                }
            }
        });
    </script> 

{{-- Image Search Component / --}}

    <script>
        /************************************************** 
         * Dinamic Navbar
         **************************************************/

        // As the navbar is fixed, the body has to be padded acording to #header height 
        const padCalc = new calculatePadding('#header');
                
        const navController = new DinamicNavbar('#header-bottom', {
            disableWidth: 900 // width at witch swaps to mobile menu.
        });
        navController.listen();
        // window.addEventListener('DOMContentLoaded', (event) => {
        //     console.log('tratando de ir arriba');
        //     window.setTimeout(() => window.scroll(0,0), 1) ;
        // })
        const topHeadController = new DinamicTopHeader('#header-top')
        topHeadController.listen();

        

        $(document).ready(function() {
            

            /************************************************** 
             * Menus / Dropdowns
             **************************************************/

            function toggleDropdown(e) {
                var currentElement = $(e.currentTarget);
                console.log({currentElement});

                if (currentElement.hasClass('icon-search')) {
                    // hide search icon
                    currentElement.addClass('hide');

                    // close nav menu if open
                    $('#hammenu-close').hide();
                    $('#hammenu').show();
                    $("#header-bottom").css("display", ""); // is hidden by 
                                                            // class css in mobile range

                    // display search area
                    $("#search-responsive").css("display", "block");
                } else if (currentElement.hasClass('menu-box') && !currentElement.hasClass('open') ) {
                    console.log('menu-box')
                    currentElement.addClass('open');
                    // show standard menu
                    $('#hammenu').hide();
                    $('#hammenu-close').show();
                    $("#header-bottom").css("display", "block");

                    // hide search bar if open
                    $('#search').removeClass('hide');
                    $("#search-responsive").css("display", "none");
                } else {
                    // close search bar
                    $("#search-responsive").css("display", "none");
                    $('#search').removeClass('hide');
                    
                    // close navbar
                    $("#header-bottom").css("display", "");
                    $('#hammenu-close').hide();
                    $('#hammenu').show();
                    $('#menu-box').removeClass('open');
                }
            }

            $('body').delegate('#search, .menu-box, .x-icon-menu-back', 'click', function(e) {
                // console.log('click')
                toggleDropdown(e);
            });

            $(window).resize(function() {
                // reset if screen out of menu range. To be ready if back to 
                // the range
                if (window.innerWidth > 900) {
                    $('#hammenu-close').hide();
                    $('#hammenu').show(); 
                    $("#header-bottom").css('display', '')
                    $('#search').removeClass('hide');
                    $("#search-responsive").css("display", "");
                }
            })

            /************************************************** 
             * Compare UI
             **************************************************/

            @auth('customer')
                let compareCount = @json($compareCount);
                $('#compare-items-count').html(compareCount);
            @endauth

            @guest('customer')
                let comparedItems = JSON.parse(localStorage.getItem('compared_product'));
                let compareCount = comparedItems ? comparedItems.length : 0;

                if(compareCount > 0) {
                    $('.compare-items-count').show();
                } else {
                    console.log('head: escondiendo compare count')
                }

                $('#compare-items-count').html(compareCount);
            @endguest

        });
    </script>
@endpush