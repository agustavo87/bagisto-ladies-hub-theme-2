@extends('shop::layouts.master')

@include('shop::guest.compare.compare-products')

@section('page_title')
    {{ __('shop::app.customer.compare.compare_similar_items') }}
@endsection

@section('content-wrapper')
    @guest
    <section class="guest-layout">
        <div class="guest-heading">
            <h2>
                {{ __('shop::app.customer.compare.compare_similar_items') }}
            </h2>
        </div>
        <div class="guest-content">
            <compare-product></compare-product>    
        </div>
    </section>
    @endguest
    @auth
    <compare-product></compare-product>    
    @endauth
@endsection