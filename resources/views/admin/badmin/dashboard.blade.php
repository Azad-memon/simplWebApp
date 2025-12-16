@extends('admin.layouts.master')
@section('title', '')

@section('css')
@endsection
@section('style')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row size-column">
            <div class="col-xl-7 box-col-12 xl-100">
                <div class="row dash-chart">

    <div class="row mt-4">
    <!-- Today Sales -->
    <div class="col-xl-4 col-md-6">
        <div class="card o-hidden">
            <div class="card-body">
                <div class="ecommerce-widgets media">
                    <div class="media-body">
                        <p class="f-w-500 font-roboto">Today Sales</p>
                        <h4 class="f-w-500 mb-0 f-20">Rs<span class="counter">{{ $todaySales }}</span></h4>
                    </div>
                    <div class="ecommerce-box light-bg-primary">
                        <i class="fa fa-line-chart" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales -->
    <div class="col-xl-4 col-md-6">
        <div class="card o-hidden">
            <div class="card-body">
                <div class="ecommerce-widgets media">
                    <div class="media-body">
                        <p class="f-w-500 font-roboto">This Month Sales</p>
                        <h4 class="f-w-500 mb-0 f-20">Rs<span class="counter">{{ $monthSales }}</span></h4>
                    </div>
                    <div class="ecommerce-box light-bg-primary">
                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Sales -->
    <div class="col-xl-4 col-md-6">
        <div class="card o-hidden">
            <div class="card-body">
                <div class="ecommerce-widgets media">
                    <div class="media-body">
                        <p class="f-w-500 font-roboto">Total Sales</p>
                        <h4 class="f-w-500 mb-0 f-20">Rs<span class="counter">{{ $totalSales }}</span></h4>
                    </div>
                    <div class="ecommerce-box light-bg-primary">
                        <i class="fa fa-money" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                </div>
            </div>
        </div>
    </div>
@endsection
