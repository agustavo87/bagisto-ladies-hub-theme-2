<compare-component product-id="{{ $productId }}"></compare-component>

@push('scripts')

    <script type="text/x-template" id="compare-component-template">
        <a
            class="unset text-right compare-component"
            title="{{  __('shop::app.customer.compare.add-tooltip') }}"
            @click="addProductToCompare"
            style="cursor: pointer">
            <x-compare-icon class="icon compare-icon" />
        </a>
    </script>

    <script>
        Vue.component('compare-component', {
            props: ['productId'],

            template: '#compare-component-template',

            data: function () {
                return {
                    'baseUrl': "{{ url()->to('/') }}",
                    'customer': '{{ auth()->guard('customer')->user() ? "true" : "false" }}' == "true",
                }
            },

            methods: {
                'addProductToCompare': function () {
                    if (this.customer == "true" || this.customer == true) {
                        this.$http.put(
                            `${this.baseUrl}/comparison`, {
                                productId: this.productId,
                            }
                        ).then(response => {
                            window.flashMessages = [{
                                'type': `alert-${response.data.status}`,
                                'message': response.data.message
                            }];
                            this.updateCompareCount();
                            this.$root.addFlashMessages()
                        }).catch(error => {
                            window.flashMessages = [{
                                'type': `alert-danger`,
                                'message': "{{ __('shop::app.common.error') }}"
                            }];

                            this.$root.addFlashMessages()
                        });
                    } else {
                        let updatedItems = [this.productId];
                        let existingItems = this.getStorageValue('compared_product');

                        if (existingItems) {
                            if (existingItems.indexOf(this.productId) == -1) {
                                updatedItems = existingItems.concat(updatedItems);

                                this.setStorageValue('compared_product', updatedItems);

                                window.flashMessages = [{
                                    'type': `alert-success`,
                                    'message': "{{ __('shop::app.customer.compare.added') }}"
                                }];

                                this.$root.addFlashMessages()
                                this.updateCompareCount();
                            } else {
                                window.flashMessages = [{
                                    'type': `alert-success`,
                                    'message': "{{ __('shop::app.customer.compare.already_added') }}"
                                }];

                                this.$root.addFlashMessages()
                                this.updateCompareCount();
                            }
                        } else {
                            this.setStorageValue('compared_product', updatedItems);

                            window.flashMessages = [{
                                'type': `alert-success`,
                                'message': "{{ __('shop::app.customer.compare.added') }}"
                            }];

                                this.$root.addFlashMessages()
                        }
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

                'updateCompareCount': function () {
                    console.log('actualizando compare-count');
                    if (this.customer == "true" || this.customer == true) {
                        this.$http.get(`${this.baseUrl}/items-count`)
                        .then(response => {
                            let count = response.data.compareProductsCount;
                            if (count > 0) {
                                console.log('mostrando');
                                $('.compare-items-count').show();
                            } else {
                                $('.compare-items-count').hide();
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

                        if(comparedItemsCount > 0) {
                            $('.compare-items-count').show();
                            console.log('compare: invitado, mostrando')
                        } else {
                            console.log('compare: invitado, escondiendo')
                            $('.compare-items-count').hide();
                        }

                        $('#compare-items-count').html(comparedItemsCount);
                    }
                }
            }
        });
    </script>
@endpush