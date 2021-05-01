@php
    $attributeRepository = app('\Webkul\Attribute\Repositories\AttributeFamilyRepository');
    $comparableAttributes = $attributeRepository->getComparableAttributesBelongsToFamily()->unique();

    $locale = request()->get('locale') ?: app()->getLocale();

    $attributeOptionTranslations = DB::table('attribute_option_translations')->where('locale', $locale)->get()->toJson();
@endphp

@push('scripts')

    <script type="text/x-template" id="compare-product-template">
        <section class="comparison-component">
            <div>
                <button
                    v-if="products.length > 0"
                    class="btn btn-secondary delete-all {{ core()->getCurrentLocale()->direction == 'rtl' ? 'pull-left' : 'pull-right' }}"
                    @click="removeProductCompare('all')">
                    {{ __('shop::app.customer.account.wishlist.deleteall') }}
                </button>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.compare.view.before') !!}

            <div class="compared-products-wrapper" >
                <table class="compare-products">
                    <template v-if="isProductListLoaded && products.length > 0">
                        @php
                            $comparableAttributes = $comparableAttributes->toArray();
    
                            array_splice($comparableAttributes, 1, 0, [[
                                'code' => 'product_image',
                                'admin_name' => __('velocity::app.customer.compare.product_image'),
                            ]]);
    
                            array_splice($comparableAttributes, 2, 0, [[
                                'code' => 'addToCartHtml',
                                'admin_name' => __('velocity::app.customer.compare.actions'),
                            ]]);
                        @endphp


    
                        @foreach ($comparableAttributes as $attribute)
                            <tr>
                                <td>
                                    <span class="fs16">{{ $attribute['admin_name'] }}</span>
                                </td>
    
                                <td :key="`title-${index}`" v-for="(product, index) in products">
                                    @switch ($attribute['code'])
                                        @case('name')
                                            <a :href="`${baseUrl}/${product.url_key}`" class="unset remove-decoration active-hover">
                                                <h3 class="product-name" v-text="product['{{ $attribute['code'] }}']"></h3>
                                            </a>
                                            @break
    
                                        @case('product_image')
                                            <a :href="`${baseUrl}/${product.url_key}`" class="unset product-image">
                                                <img
                                                    class="image-wrapper"
                                                    :src="product['{{ $attribute['code'] }}']"
                                                    :onerror="`this.src='${baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`" alt="" />
                                            </a>
                                            @break
    
                                        @case('price')
                                            <span v-html="product['priceHTML']"></span>
                                            @break
    
                                        @case('addToCartHtml')
                                            <div class="action">
                                                {{-- add to cart --}}
                                                <form :action="routeAddToCart(product.id)" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="product.id}}">
                                                    <input type="hidden" name="quantity" value="1">                                        
                                                    <button  class="btn btn-sm btn-primary addtocart">
                                                        {{ __('shop::app.products.add-to-cart')  }}
                                                    </button>
                                                </form>
                                                {{-- /add to cart --}}

                                                {{-- <div v-html="product.addToCartHtml"></div> --}}
                                                <span class="icon white-cross-sm-icon remove-product" @click="removeProductCompare(product.id)"></span>
                                            </div>
                                            @break
    
                                        @case('color')
                                            <span v-html="product.color_label" class="fs16"></span>
                                            @break
    
                                        @case('size')
                                            <span v-html="product.size_label" class="fs16"></span>
                                            @break
    
                                        @case('description')
                                            <span v-html="product.description" class="desc"></span>
                                            @break
    
                                        @default
                                            @switch ($attribute['type'])
                                                @case('boolean')
                                                    <span
                                                        v-text="product.product['{{ $attribute['code'] }}']
                                                                ? '{{ __('velocity::app.shop.general.yes') }}'
                                                                : '{{ __('velocity::app.shop.general.no') }}'"
                                                    ></span>
                                                    @break;
    
                                                @case('checkbox')
                                                    <span v-if="product.product['{{ $attribute['code'] }}']" v-html="getAttributeOptions(product['{{ $attribute['code'] }}'] ? product : product.product['{{ $attribute['code'] }}'] ? product.product : null, '{{ $attribute['code'] }}', 'multiple')" class="fs16"></span>
                                                    <span v-else class="fs16">__</span>
                                                    @break;
    
                                                @case('select')
                                                    <span 
                                                        class="fs16"
                                                        v-if="product['{{ $attribute['code'] }}']" 
                                                        v-html="
                                                            getAttributeOptions(
                                                                product['{{ $attribute['code'] }}'] 
                                                                    ? product 
                                                                    : product.product['{{ $attribute['code'] }}'] 
                                                                        ? product.product 
                                                                        : null, 
                                                                '{{ $attribute['code'] }}', 
                                                                'single'
                                                            )
                                                        " 
                                                    >
                                                    </span>
                                                    <span v-else class="fs16">__</span>
                                                    @break;

                                                @case('multiselect')
                                                    <span 
                                                        class="fs16"
                                                        v-if="product['{{ $attribute['code'] }}']" 
                                                        v-html="
                                                            getAttributeOptions(
                                                                product['{{ $attribute['code'] }}'] 
                                                                    ? product 
                                                                    : product.product['{{ $attribute['code'] }}'] 
                                                                        ? product.product 
                                                                        : null, 
                                                                '{{ $attribute['code'] }}', 
                                                                'single'
                                                            )
                                                        " 
                                                    >
                                                    </span>
                                                    <span v-else class="fs16">__</span>
                                                    @break;
    
                                                @case ('file')
                                                @case ('image')
                                                    <a :href="`${baseUrl}/${product.url_key}`" class="unset">
                                                        <img
                                                            class="image-wrapper"
                                                            :src="'storage/' + product.product['{{ $attribute['code'] }}']"
                                                            :onerror="`this.src='${baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`" alt="" />
                                                    </a>
                                                    @break;
                                                @default
                                                    <span v-html="product['{{ $attribute['code'] }}'] ? product['{{ $attribute['code'] }}'] : product.product['{{ $attribute['code'] }}'] ? product.product['{{ $attribute['code'] }}'] : '__'" class="fs16"></span>
                                                    @break;
                                            @endswitch
    
                                            @break
    
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </template>
    
                    <span v-else-if="isProductListLoaded && products.length == 0">
                        {{ __('shop::app.customer.compare.empty-text') }}
                    </span>
                </table>
            </div>
            
            {!! view_render_event('bagisto.shop.customers.account.compare.view.after') !!}
        </section>
    </script>

    <script>
        Vue.component('compare-product', {
            template: '#compare-product-template',

            data: function () {
                return {
                    'products': [],
                    'route':{
                        addtocart: '{{ route('cart.add', '%param_1%') }}'
                    },
                    'isProductListLoaded': false,
                    'baseUrl': "{{ url()->to('/') }}",
                    'attributeOptions': JSON.parse(@json($attributeOptionTranslations)),
                    'isCustomer': '{{ auth()->guard('customer')->user() ? "true" : "false" }}' == "true",
                }
            },

            mounted: function () {
                this.getComparedProducts();
            },

            methods: {
                'getComparedProducts': function () {
                    let items = '';
                    let url = `${this.baseUrl}/${this.isCustomer ? 'comparison' : 'detailed-products'}`;

                    let data = {
                        params: {'data': true}
                    }

                    if (! this.isCustomer) {
                        items = this.getStorageValue('compared_product');
                        items = items ? items.join('&') : '';

                        data = {
                            params: {
                                items
                            }
                        };
                    }

                    if (this.isCustomer || (! this.isCustomer && items != "")) {
                        this.$http.get(url, data)
                        .then(response => {
                            this.isProductListLoaded = true;

                            // if (response.data.products.length > 1) {
                            //     $('.compared-producs-wrapper').css('overflow-x', 'scroll');
                            // }

                            this.products = response.data.products;
                        })
                        .catch(error => {
                            this.isProductListLoaded = true;
                            console.log("{{ __('shop::app.common.error') }}");
                        });
                    } else {
                        this.isProductListLoaded = true;
                    }

                },

                'removeProductCompare': function (productId) {
                    if (this.isCustomer) {
                        this.$http.delete(`${this.baseUrl}/comparison?productId=${productId}`)
                        .then(response => {
                            if (productId == 'all') {
                                this.$set(this, 'products', this.products.filter(product => false));
                            } else {
                                this.$set(this, 'products', this.products.filter(product => product.id != productId));
                            }

                            window.flashMessages = [{'type': 'alert-success', 'message': response.data.message }];

                            this.updateCompareCount();
                            this.$root.addFlashMessages();
                        })
                        .catch(error => {
                            console.log("{{ __('shop::app.common.error') }}");
                        });
                    } else {
                        let existingItems = this.getStorageValue('compared_product');
                        let message = productId == "all" ? '{{ __('shop::app.customer.compare.removed-all') }}': '{{ __('shop::app.customer.compare.removed') }}'


                        if (productId == "all") {
                            updatedItems = [];
                            this.$set(this, 'products', []);
                            
                            // window.flashMessages = [{'type': 'alert-success', 'message': '{{ __('shop::app.customer.compare.removed-all') }}' }];
                        } else {
                            updatedItems = existingItems.filter(item => item != productId);
                            this.$set(this, 'products', this.products.filter(product => product.id != productId));
                            // window.flashMessages = [{'type': 'alert-success', 'message': '{{ __('shop::app.customer.compare.removed') }}' }];
                        }

                        this.setStorageValue('compared_product', updatedItems);
                        this.updateCompareCount();

                        window.flashMessages = [{'type': 'alert-success', 'message': message}];

                        this.$root.addFlashMessages();
                    }

                    // this.updateCompareCount();
                },

                // 'getDynamicHTML': function (input) {
                //     var _staticRenderFns;
                //     const { render, staticRenderFns } = Vue.compile(input);

                //     if (this.$options.staticRenderFns.length > 0) {
                //         _staticRenderFns = this.$options.staticRenderFns;
                //     } else {
                //         _staticRenderFns = this.$options.staticRenderFns = staticRenderFns;
                //     }

                //     try {
                //         var output = render.call(this, this.$createElement);
                //     } catch (exception) {
                //         console.log(this.__('error.something_went_wrong'));
                //     }

                //     this.$options.staticRenderFns = _staticRenderFns;

                //     return output;
                // },

                'isMobile': function () {
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    return true
                    } else {
                    return false
                    }
                },

                'getStorageValue': function (key) {
                    let value = window.localStorage.getItem(key);

                    if (value) {
                        value = JSON.parse(value);
                    }

                    return value;
                },

                'setStorageValue': function (key, value) {
                    window.localStorage.setItem(key, JSON.stringify(value));

                    return true;
                },

                'getAttributeOptions': function (productDetails, attributeValues, type) {
                    var attributeOptions = '__';
                    if (productDetails && attributeValues) {
                        var attributeItems;

                        if (type == "multiple") {
                            attributeItems = productDetails[attributeValues].split(',');
                        } else if (type == "single") {
                            attributeItems = productDetails[attributeValues];
                        }

                        attributeOptions = this.attributeOptions.filter(option => {
                            if (type == "multiple") {
                                if (attributeItems.indexOf(option.attribute_option_id.toString()) > -1) {
                                    return true;
                                }
                            } else if (type == "single") {
                                if (attributeItems == option.attribute_option_id.toString()) {
                                    return true;
                                }
                            }

                            return false;
                        });

                        attributeOptions = attributeOptions.map(option => {
                            return option.label;
                        });

                        attributeOptions = attributeOptions.join(', ');
                    }

                    return attributeOptions;
                },

                'updateCompareCount': function () {
                    if (this.isCustomer == "true" || this.isCustomer == true) {
                        this.$http.get(`${this.baseUrl}/items-count`)
                        .then(response => {
                            let count = response.data.compareProductsCount;
                            if (count > 0) {
                                console.log('mostrando');
                                $('.compare-items-count').show();
                            } else {
                                $('.compare-items-count').hide();
                                $('.comparison-component').css('overflow-x', 'unset');
                            }
                            $('#compare-items-count').html(count);
                        })
                        .catch(exception => {
                            window.flashMessages = [{
                                'type': `alert-error`,
                                'message': "{{ __('shop::app.common.error') }}"
                            }];

                            this.$root.addFlashMessages();
                        });
                    } else {
                        let comparedItems = JSON.parse(localStorage.getItem('compared_product'));
                        comparedItemsCount = comparedItems ? comparedItems.length : 0;
                        console.log('compare-page: actualizando compare-count')
                        if(comparedItemsCount > 0) {
                            console.log('compare-page: con items, mostrando')
                            $('.compare-items-count').show();
                        } else {
                            console.log('compare-page: sin items, escondiend')
                            $('.compare-items-count').hide();
                            $('.comparison-component').css('overflow-x', 'unset');
                        }

                        $('#compare-items-count').html(comparedItemsCount);
                    }
                },

                'routeAddToCart': function (product_id) {
                    return this.route.addtocart.replace('%param_1%', product_id)
                }
            }
        });
    </script>
@endpush