@extends('shop::layouts.master')

@include('shop::guest.compare.compare-products')

@section('page_title')
    {{ __('shop::app.customer.compare.compare_similar_items') }}
@endsection

@section('content-wrapper')

    <div class="account-content customer">
        @include('shop::customers.account.partials.sidemenu')

        <div class="account-layout">

            <div class="account-head">

                <span class="back-icon"><a href="{{ route('customer.profile.index') }}"><i class="icon icon-menu-back"></i></a></span>
    
                <span class="account-heading" style="margin-top: 2rem; margin-left: 0.5rem;">
                    <h2>
                        {{ __('shop::app.customer.compare.compare_similar_items') }}
                    </h2>
                </span>
    
                <span class="account-action">
                </span>
    
                <div class="horizontal-rule"></div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.comparison.list.before') !!}

            <div class="account-items-list">
                <div class="account-table-content atc-compare">
                    <compare-product></compare-product>
                </div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.comparison.list.after') !!}

        </div>

    </div>

@endsection
