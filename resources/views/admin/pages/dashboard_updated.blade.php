@extends('admin.layouts.master')
@section('title', 'Dashboard')

@section('css')
@endsection

@section('style')
    <style>
        /**=====================
                                        3.9 Dashboard_2 CSS Start
                                    ==========================**/
        .widget-decor {
            position: absolute;
            height: 60px;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .balance-widget {
            background-image: url(../images/dashboard-2/balance-bg.png);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: right;
        }

        .balance-widget .mobile-right-img {
            position: absolute;
            top: 10px;
            right: 15px;
        }

        .balance-widget .mobile-right-img .left-mobile-img {
            margin-right: -20px;
        }

        .balance-widget .mobile-right-img .mobile-img {
            height: 130px;
        }

        [dir=rtl] .balance-widget .mobile-right-img {
            right: unset;
            left: 15px;
        }

        @media (max-width: 480px) {
            .balance-widget .mobile-right-img {
                right: 0;
            }

            .balance-widget .mobile-right-img .mobile-img {
                height: 100px;
            }

            [dir=rtl] .balance-widget .mobile-right-img {
                right: unset;
                left: 0;
            }
        }

        [dir=rtl] .balance-widget {
            text-align: right;
        }

        .balance-widget.card-body {
            padding: 20px 15px;
        }

        .balance-widget .purchase-btn {
            min-width: 170px;
        }

        .small-widget {
            overflow: hidden;
        }

        .small-widget h4 {
            margin-bottom: -3px;
        }

        .small-widget i {
            font-weight: 700;
            font-size: 11px;
        }

        .small-widget .card-body {
            padding: 24px 15px;
        }

        .small-widget .bg-gradient {
            width: 66px;
            height: 67px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 100px;
            right: -12px;
            top: 50%;
            position: absolute;
            transform: translateY(-50%);
        }

        [dir=rtl] .small-widget .bg-gradient {
            right: unset;
            left: -12px;
            transform: translateY(-50%) scaleX(-1);
        }

        @media (max-width: 1399px) {
            .small-widget .bg-gradient {
                width: 60px;
                height: 60px;
            }
        }

        .small-widget .bg-gradient svg {
            width: 25px;
            height: 25px;
        }

        @media (max-width: 1399px) {
            .small-widget .bg-gradient svg {
                width: 22px;
                height: 22px;
            }
        }

        .small-widget:hover {
            transform: translateY(-5px);
            transition: 0.5s;
        }

        .small-widget:hover .bg-gradient svg {
            animation: tada 1.5s ease infinite;
        }

        @media (max-width: 1470px) {
            .appointment .customer-table {
                height: 268px;
            }
        }

        @media (max-width: 1399px) {
            .appointment .customer-table {
                height: unset;
            }
        }

        .order-wrapper {
            margin: 0 -24px -17px -17px;
        }

        .categories-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        @media (max-width: 767px) {
            .categories-list {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }

        .categories-list li {
            gap: 10px;
        }

        .categories-list li .bg-light {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .categories-list li .bg-light img {
            width: 25px;
            height: 25px;
            object-fit: contain;
            transition: 0.5s;
        }

        .categories-list li h6 a {
            transition: 0.5s;
            color: var(--body-font-color);
        }

        .categories-list li:hover .bg-light img {
            transition: 0.5s;
            transform: scale(1.1);
        }

        .categories-list li:hover h6 a {
            transition: 0.5s;
            color: var(--theme-deafult);
        }

        .monthly-profit {
            margin-top: -10px;
        }

        @media (max-width: 1584px) {
            .monthly-profit {
                margin: -10px -8px 0;
            }
        }

        @media (max-width: 1520px) {
            .monthly-profit {
                margin: -10px -16px 0;
            }
        }

        @media (max-width: 1500px) {
            .monthly-profit {
                margin: -10px -14px 0;
            }
        }

        @media (max-width: 1472px) {
            .monthly-profit {
                margin: -10px -20px 0;
            }
        }

        @media (max-width: 1424px) {
            .monthly-profit {
                margin: -10px -25px 0;
            }
        }

        .monthly-profit .apexcharts-canvas .apexcharts-legend-marker {
            margin-right: 6px;
        }

        .monthly-profit .apexcharts-canvas .apexcharts-datalabels-group .apexcharts-datalabel-value {
            font-size: 14px;
            font-weight: 500;
            font-family: Rubik, sans-serif !important;
            fill: var(--chart-text-color);
        }

        .overview-wrapper {
            position: relative;
            z-index: 1;
        }

        .back-bar-container {
            position: absolute;
            width: calc(100% - 64px);
            bottom: -8px;
            margin: 0 auto !important;
            left: 28px;
        }

        @media (max-width: 1199px) {
            .back-bar-container {
                bottom: 22px;
            }
        }

        @media (max-width: 480px) {
            .back-bar-container {
                width: calc(100% - 34px);
                left: 18px;
            }
        }

        @media (max-width: 327px) {
            .back-bar-container {
                bottom: 42px;
            }
        }

        .overview-card .balance-data {
            right: 12px;
        }

        [dir=rtl] .overview-card .balance-data {
            right: unset;
            left: 12px;
        }

        @media (max-width: 767px) {
            .overview-card .balance-data {
                right: -40%;
            }

            [dir=rtl] .overview-card .balance-data {
                left: -40%;
            }
        }

        .order-container .apexcharts-canvas .apexcharts-marker {
            stroke-width: 4;
        }

        .purchase-card.discover {
            margin-top: 102px;
        }

        .purchase-card.discover img {
            width: 224px;
            margin: 0 auto;
            margin-top: -90px;
        }

        @media (max-width: 1550px) {
            .purchase-card.discover img {
                margin-top: -110px;
            }
        }

        @media (max-width: 1399px) {
            .purchase-card.discover img {
                margin-top: -90px;
            }
        }

        @media (max-width: 991px) {
            .purchase-card.discover img {
                margin-top: -110px;
            }
        }

        @media (max-width: 618px) {
            .purchase-card.discover img {
                width: 200px;
            }
        }

        .visitor-card .card-header svg {
            width: 18px;
            height: 18px;
            fill: var(--theme-deafult);
        }

        .visitors-container {
            margin: 0 -12px -27px -17px;
        }

        .visitors-container svg .apexcharts-series path {
            clip-path: inset(1% 0% 0% 0% round 3rem);
        }

        .visitors-container svg .apexcharts-legend.apexcharts-align-left .apexcharts-legend-series {
            display: flex;
        }

        .recent-order .nav {
            gap: 8px;
            flex-wrap: nowrap;
            overflow: auto;
            padding-bottom: 5px;
            display: flex;
        }

        .recent-order .frame-box {
            border: 1px solid transparent;
            padding: 0;
            transition: 0.5s;
        }

        .recent-order .frame-box.active {
            border: 1px solid var(--theme-deafult);
        }

        .recent-order .frame-box:hover {
            transform: translateY(-5px);
            transition: 0.5s;
        }

        .recent-order .tab-content {
            margin-top: 16px;
        }

        .recent-table table thead {
            background: var(--light2);
        }

        .recent-table table thead th {
            padding-top: 9px;
            padding-bottom: 9px;
            border-bottom: none;
        }

        .recent-table table tr td,
        .recent-table table tr th {
            padding: 12px 8px;
            vertical-align: middle;
        }

        .recent-table table tr td:first-child,
        .recent-table table tr th:first-child {
            min-width: 157px;
        }

        @media (max-width: 1660px) {

            .recent-table table tr td:nth-child(2),
            .recent-table table tr th:nth-child(2) {
                min-width: 97px;
            }
        }

        .recent-table table tr td:last-child,
        .recent-table table tr th:last-child {
            min-width: 96px;
        }

        .recent-table table tr td:first-child {
            padding-left: 0;
        }

        [dir=rtl] .recent-table table tr td:first-child {
            padding-left: unset;
            padding-right: 0;
        }

        .recent-table table tr td:last-child {
            padding-right: 0;
        }

        [dir=rtl] .recent-table table tr td:last-child {
            padding-left: 0;
            padding-right: unset;
        }

        .recent-table table tr:last-child td {
            padding-bottom: 0;
            border-bottom: none;
        }

        .recent-table table tr .product-content h6 a {
            color: var(--body-font-color);
            transition: 0.5s;
        }

        .recent-table table tr:hover .product-content h6 a {
            color: var(--theme-deafult);
            transition: 0.5s;
        }

        .recent-table .product-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .recent-table .product-content .order-image {
            background: var(--light2);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .recent-table svg {
            width: 20px;
            height: 20px;
        }

        .recent-table .recent-status {
            display: flex;
            align-items: center;
        }

        .recent-table .recent-status.font-success svg {
            fill: #54BA4A;
        }

        .recent-table .recent-status.font-danger svg {
            fill: #FC4438;
        }

        .recent-activity h5 {
            padding: 30px 0 20px;
            margin-bottom: 0;
        }

        .recent-activity h6 {
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            -webkit-line-clamp: 2;
            display: -webkit-box;
        }

        .recent-activity ul li span {
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            -webkit-line-clamp: 2;
            display: -webkit-box;
        }

        .notification li .recent-images ul::before {
            display: none;
        }

        .notification li .recent-images li {
            padding-bottom: 3px;
        }

        .recent-images {
            margin-top: 10px;
        }

        .recent-images ul {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .recent-images li {
            border: 1px dashed var(--recent-dashed-border);
            padding: 3px;
            border-radius: 2px;
        }

        .recent-images li .recent-img-wrap {
            width: 40px;
            height: 40px;
            background: var(--light2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .frame-box {
            background: var(--recent-box-bg);
            border-radius: 10px;
            min-width: 76px;
            box-shadow: 2px 2px 2px var(--recent-border);
        }

        @media (max-width: 575px) {
            .frame-box {
                min-width: 65px;
            }
        }

        .frame-box .frame-image {
            min-width: 62px;
            height: 62px;
            border-color: var(--recent-border) var(--white) var(--white) var(--recent-border);
            border-width: 1px;
            border-style: solid;
            margin: 6px;
            display: flex;
            align-items: center;
            border-radius: 4px;
        }

        @media (max-width: 575px) {
            .frame-box .frame-image {
                min-width: 50px;
                height: 50px;
                margin: 4px;
            }
        }

        .frame-box img {
            margin: 0 auto;
        }

        @media (max-width: 575px) {
            .frame-box img {
                height: 30px;
            }
        }

        .support-ticket-font ul {
            font-size: 12px;
        }

        .new-update .media .media-body span,
        .new-update .media .media-body p {
            font-weight: 500;
        }

        .activity-dot-primary {
            min-width: 6px;
            height: 6px;
            background-color: #7366FF;
            border-radius: 100%;
            outline: 5px solid rgba(115, 102, 255, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-primary {
            min-width: 12px;
            height: 12px;
            background-color: #7366FF;
            outline: 5px solid rgba(115, 102, 255, 0.25);
            position: relative;
            z-index: 2;
        }

        .activity-dot-secondary {
            min-width: 6px;
            height: 6px;
            background-color: #FF3364;
            border-radius: 100%;
            outline: 5px solid rgba(255, 51, 100, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-secondary {
            min-width: 12px;
            height: 12px;
            background-color: #FF3364;
            outline: 5px solid rgba(255, 51, 100, 0.25);
            position: relative;
            z-index: 2;
        }

        .activity-dot-success {
            min-width: 6px;
            height: 6px;
            background-color: #54BA4A;
            border-radius: 100%;
            outline: 5px solid rgba(84, 186, 74, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-success {
            min-width: 12px;
            height: 12px;
            background-color: #54BA4A;
            outline: 5px solid rgba(84, 186, 74, 0.25);
            position: relative;
            z-index: 2;
        }

        .activity-dot-danger {
            min-width: 6px;
            height: 6px;
            background-color: #FC4438;
            border-radius: 100%;
            outline: 5px solid rgba(252, 68, 56, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-danger {
            min-width: 12px;
            height: 12px;
            background-color: #FC4438;
            outline: 5px solid rgba(252, 68, 56, 0.25);
            position: relative;
            z-index: 2;
        }

        .activity-dot-info {
            min-width: 6px;
            height: 6px;
            background-color: #16C7F9;
            border-radius: 100%;
            outline: 5px solid rgba(22, 199, 249, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-info {
            min-width: 12px;
            height: 12px;
            background-color: #16C7F9;
            outline: 5px solid rgba(22, 199, 249, 0.25);
            position: relative;
            z-index: 2;
        }

        .activity-dot-light {
            min-width: 6px;
            height: 6px;
            background-color: #f4f4f4;
            border-radius: 100%;
            outline: 5px solid rgba(244, 244, 244, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-light {
            min-width: 12px;
            height: 12px;
            background-color: #f4f4f4;
            outline: 5px solid rgba(244, 244, 244, 0.25);
            position: relative;
            z-index: 2;
        }

        .activity-dot-dark {
            min-width: 6px;
            height: 6px;
            background-color: #2c323f;
            border-radius: 100%;
            outline: 5px solid rgba(44, 50, 63, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-dark {
            min-width: 12px;
            height: 12px;
            background-color: #2c323f;
            outline: 5px solid rgba(44, 50, 63, 0.25);
            position: relative;
            z-index: 2;
        }

        .activity-dot-warning {
            min-width: 6px;
            height: 6px;
            background-color: #FFAA05;
            border-radius: 100%;
            outline: 5px solid rgba(255, 170, 5, 0.25);
            position: relative;
            z-index: 2;
        }

        .timeline-dot-warning {
            min-width: 12px;
            height: 12px;
            background-color: #FFAA05;
            outline: 5px solid rgba(255, 170, 5, 0.25);
            position: relative;
            z-index: 2;
        }

        @media only screen and (max-width: 1800px) and (min-width: 1400px) {
            .grid-ed-none {
                display: none !important;
            }

            .grid-ed-12 {
                width: 100%;
            }
        }

        @media only screen and (max-width: 1660px) and (min-width: 1400px) {
            .col-ed-12 {
                width: 100%;
            }

            .col-ed-7 {
                width: 58%;
            }

            .col-ed-5 {
                width: 42%;
            }

            .col-ed-9 {
                width: 75%;
            }

            .col-ed-3 {
                width: 25%;
            }

            .col-ed-6 {
                width: 50%;
            }

            .col-ed-4 {
                width: 33.33333333%;
            }

            .col-ed-8 {
                width: 66.66%;
            }

            .col-ed-none {
                display: none !important;
            }
        }

        @media only screen and (max-width: 1660px) and (min-width: 1200px) {
            .xl-30 {
                max-width: 30%;
                flex: 0 0 30%;
            }

            .xl-70 {
                max-width: 70%;
                flex: 0 0 70%;
            }
        }

        @media only screen and (max-width: 420px) {
            .size-column .col-6 {
                width: 100%;
            }
        }


        .balance-card {
            display: flex;
            padding: 15px;
            border-radius: 5px;
            gap: 15px;
            transition: 0.5s;
        }

        @media (max-width: 1199px) {
            .balance-card {
                gap: 8px;
            }
        }

        .balance-card .svg-box {
            width: 43px;
            height: 43px;
            background: #fff;
            box-shadow: 0px 71.2527px 51.5055px rgba(229, 229, 245, 0.189815), 0px 42.3445px 28.0125px rgba(229, 229, 245, 0.151852), 0px 21.9866px 14.2913px rgba(229, 229, 245, 0.125), 0px 8.95749px 7.16599px rgba(229, 229, 245, 0.0981481), 0px 2.03579px 3.46085px rgba(229, 229, 245, 0.0601852);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 1199px) {
            .balance-card .svg-box {
                width: 35px;
                height: 35px;
            }
        }

        .balance-card .svg-box svg {
            height: 20px;
            fill: rgba(82, 82, 108, 0.85);
        }

        @media (max-width: 1199px) {
            .balance-card .svg-box svg {
                height: 17px;
            }
        }

        .balance-data {
            display: flex;
            gap: 15px;
            position: absolute;
            top: -50px;
            right: 2%;
        }

        [dir=rtl] .balance-data {
            right: unset;
            left: 2%;
        }

        @media (max-width: 991px) {
            .balance-data {
                top: -42px;
                right: -65%;
            }

            [dir=rtl] .balance-data {
                left: -65%;
            }
        }

        @media (max-width: 575px) {
            .balance-data {
                display: none;
            }
        }

        .balance-data li {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .balance-data .circle {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 100%;
        }

        .current-sale-container {
            padding-right: 12px;
        }

        [dir=rtl] .current-sale-container {
            padding-right: unset;
            padding-left: 12px;
        }

        .current-sale-container>div {
            margin: -22px 0 -30px -16px;
        }

        @media (max-width: 1199px) {
            .current-sale-container>div {
                margin-bottom: 0;
            }
        }

        @media (max-width: 404px) {
            .current-sale-container>div {
                margin-bottom: -30px;
            }
        }

        .current-sale-container .apexcharts-xaxistooltip {
            color: var(--theme-deafult);
            background: rgba(115, 102, 255, 0.1);
            border: 1px solid var(--theme-deafult);
        }

        .current-sale-container .apexcharts-xaxistooltip-bottom:before {
            border-bottom-color: var(--theme-deafult);
        }

        .current-sale-container .apexcharts-tooltip.light .apexcharts-tooltip-title {
            background: rgba(115, 102, 255, 0.1);
            color: var(--theme-deafult);
        }

        @media (max-width: 575px) {
            .current-sale-container.order-container {
                padding-right: 0;
            }

            [dir=rtl] .current-sale-container.order-container {
                padding-left: 0;
            }
        }

        @media (max-width: 404px) {
            .current-sale-container.order-container>div {
                margin-bottom: 0;
            }
        }

        .recent-circle {
            min-width: 10px;
            height: 10px;
            border-radius: 100%;
            display: inline-block;
            margin-top: 5px;
        }

        .recent-wrapper {
            align-items: center;
        }

        .recent-wrapper .order-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 36px;
        }

        @media (max-width: 1199px) {
            .recent-wrapper .order-content {
                gap: 20px;
            }
        }

        @media (max-width: 575px) {
            .recent-wrapper .order-content {
                justify-content: center;
                flex-wrap: wrap;
                flex-direction: row;
            }
        }

        .recent-wrapper .order-content li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        @media (max-width: 1660px) {
            .recent-wrapper .recent-chart .apexcharts-canvas .apexcharts-datalabel-label {
                font-size: 15px;
            }
        }

        @media (max-width: 1560px) and (min-width: 1400px) {
            .recent-wrapper>div {
                width: 100%;
            }
        }

        @media (max-width: 1560px) and (min-width: 1400px) {
            .recent-wrapper>div:last-child {
                display: none;
            }
        }

        /**=====================
                                        3.9 Dashboard_2 CSS End
                                    ==========================**/
    </style>


@endsection

@section('content')
    <div class="container-fluid">
        <div class="row size-column">
      <div style=" ">
        <select id="branch_id" class="form-select" style="width:180px;margin-bottom: 10px;">
            <option value="">All Branches</option>
            <option value="1">Bukhari</option>
            <option value="2">test branch</option>
        </select>
    </div>
            <div class="col-xxl-12 col-md-12 box-col-8 grid-ed-12">
                <div class="row">
                    <!-- PRODUCT WISE REPORT SECTION -->
                    <div class="col-xxl-12 col-md-7 box-col-7">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card o-hidden">
                                    <div class="card-body balance-widget">
                                        <span class="f-w-500 f-light">Total Earnings</span>
                                        <h4 class="mb-3 mt-1 f-w-500 mb-0 f-22">Rs<span class="counter" id="total_earnings">0
                                            {{-- </span><span class="f-light f-14 f-w-400 ms-1">this month</span> --}}
                                        </h4>
                                        {{-- <a class="purchase-btn btn btn-primary btn-hover-effect f-w-500" href="#">Tap
                                            Up Balance</a> --}}
                                        <div class="mobile-right-img">
                                            <img class="left-mobile-img"
                                                src="{{ asset('assets/images/dashboard-2/widget-img.png') }}"
                                                alt="">
                                            <img class="mobile-img"
                                                src="{{ asset('assets/images/dashboard-2/mobile.gif') }}"
                                                alt="mobile with coin">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card small-widget">
                                    <div class="card-body primary">
                                        <span class="f-light">New Orders</span>
                                        <div class="d-flex align-items-end gap-1">
                                            <h4 id="new_orders">0</h4>
                                            {{-- <span class="font-primary f-12 f-w-500">
                                                <i class="icon-arrow-up"></i><span>+50%</span>
                                            </span> --}}
                                        </div>
                                        <div class="bg-gradient">
                                            <svg class="stroke-icon svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#new-order') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card small-widget">
                                    <div class="card-body warning">
                                        <span class="f-light">New Customers</span>
                                        <div class="d-flex align-items-end gap-1">
                                            <h4 id="new_customers">0</h4>
                                            {{-- <span class="font-warning f-12 f-w-500"><i
                                                    class="icon-arrow-up"></i><span>+20%</span> --}}
                                            </span>
                                        </div>
                                        <div class="bg-gradient">
                                            <svg class="stroke-icon svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#customers') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card small-widget">
                                    <div class="card-body secondary">
                                        <span class="f-light">Average Sale</span>
                                        <div class="d-flex align-items-end gap-1">
                                            <h4 id="average_sale">0</h4>
                                            {{-- <span class="font-secondary f-12 f-w-500">
                                                <i class="icon-arrow-down"></i><span>-10%</span>
                                                </span> --}}
                                        </div>
                                        <div class="bg-gradient">
                                            <svg class="stroke-icon svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#sale') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card small-widget">
                                    <div class="card-body success">
                                        <span class="f-light">Gross Profit</span>
                                        <div class="d-flex align-items-end gap-1">
                                             <h4 id="gross_profit">0</h4>
                                            {{-- <span class="font-success f-12 f-w-500"><i
                                                    class="icon-arrow-up"></i><span>+80%</span></span> --}}
                                        </div>
                                        <div class="bg-gradient">
                                            <svg class="stroke-icon svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#profit') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-6 col-sm-6 box-col-12 d-none">
                        <div class="card">
                            <div class="card-header card-no-border">
                                <div class="header-top">
                                    <h5 class="m-0">Order this month</h5>
                                    <div class="card-header-right-icon">
                                        <div class="dropdown icon-dropdown">
                                            <button class="btn dropdown-toggle" id="orderButton" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="icon-more-alt"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderButton">
                                                <a class="dropdown-item" href="#">Today</a>
                                                <a class="dropdown-item" href="#">Tomorrow</a>
                                                <a class="dropdown-item" href="#">Yesterday</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="light-card balance-card d-inline-block">
                                    <h4 class="mb-0">$12,000 <span class="f-light f-14">(15,080 To Goal)</span></h4>
                                </div>
                                <div class="order-wrapper">
                                    <div id="order-goal"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-12 box-col-12">
                        <div class="card">
                            <div class="card-header card-no-border d-flex justify-content-between align-items-center">
                                <h5>Product Wise Report</h5>

                                <div class="d-flex gap-2">

                                    <!-- Daily / Monthly Select -->
                                    <select id="report_type" class="form-select">
                                        <option value="daily">Daily</option>
                                        <option value="monthly" selected>Monthly</option>
                                    </select>

                                    <!-- Daily Date Input -->
                                    <input type="date" id="daily_date" class="form-control"
                                        style="display:none; width:160px;" max="{{ date('Y-m-d') }}">

                                    <!-- Monthly Date Input -->
                                    <input type="month" id="month_date" class="form-control"
                                        style="display:block; width:160px;" value="{{ date('Y-m') }}"
                                        max="{{ date('Y-m') }}">

                                    <!-- Button -->
                                    <button class="btn btn-primary" id="loadProductReport">Load</button>

                                </div>
                            </div>

                            <div class="card-body pt-0">
                                <div class="row m-0 overall-card overview-card">

                                    <div class="col-xl-9 col-md-8 col-sm-7 p-0">
                                        <div class="chart-right">
                                            <div class="card-body p-0">

                                                <ul class="balance-data">
                                                    <li><span class="circle bg-primary"></span><span
                                                            class="f-light ms-1">Quantity Sold</span></li>
                                                    {{-- <li><span class="circle bg-success"></span><span
                                                            class="f-light ms-1">Total Revenue</span></li> --}}
                                                </ul>

                                                <div class="current-sale-container order-container"
                                                    style="position:relative;">
                                                    <!-- Loader -->
                                                    <div class="current-sale-container order-container"
                                                        style="position:relative;">
                                                        <!-- Loader Overlay -->
                                                        <div id="chartLoader"
                                                            style="
                                                            display:none;
                                                            position:absolute;
                                                            top:0;
                                                            left:0;
                                                            width:100%;
                                                            height:100%;
                                                            background:rgba(255,255,255,0.7);
                                                            display:flex;
                                                            align-items:center;
                                                            justify-content:center;
                                                            z-index:10;
                                                        ">
                                                            <div class="spinner-border text-primary" role="status">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </div>

                                                        <!-- Chart -->
                                                        <div class="overview-wrapper" id="productReportChart"></div>
                                                    </div>

                                                    <!-- Chart -->
                                                    <div class="overview-wrapper" id="productReportChart"></div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-4 col-sm-5 p-0">
                                        <div class="row g-sm-3 g-2">

                                            <div class="col-md-12">
                                                <div class="light-card balance-card widget-hover">
                                                    <div>
                                                        <span class="f-light">Total Products Sold</span>
                                                        <h6 id="total_products_sold" class="mt-1 mb-0">0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="light-card balance-card widget-hover">
                                                    <div>
                                                        <span class="f-light">Total Revenue</span>
                                                        <h6 id="total_revenue" class="mt-1 mb-0">$0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="light-card balance-card widget-hover">
                                                    <div>
                                                        <span class="f-light">Top Product</span>
                                                        <h6 id="top_product" class="mt-1 mb-0">N/A</h6>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-12 box-col-12">
                        <div class="card shadow-lg rounded-3">
                            <div class="card-header card-no-border d-flex justify-content-between align-items-center">
                                <h5 class="text-primary fw-bold">Order Report</h5>

                                <div class="d-flex gap-2">
                                    <!-- Report Type -->
                                    <select id="order_report_type" class="form-select border-0 shadow-sm">
                                        <option value="daily">Daily</option>
                                         <option value="monthly" selected>Monthly</option>
                                    </select>

                                    <input type="date" id="order_daily_date" class="form-control shadow-sm rounded-2"
                                        style="display:none; width:160px;">
                                    <input type="month" id="order_month_date"  class="form-control"
                                        style="display:block; width:160px;" value="{{ date('Y-m') }}"
                                        max="{{ date('Y-m') }}">

                                    <button class="btn btn-primary px-4 shadow-sm" id="loadOrderReport">Load</button>
                                </div>
                            </div>

                            <div class="card-body pt-0">
                                 <div id="order_loader" style="display:none;" class="text-center py-5">
                                    <div class="spinner-border text-primary" style="width:3rem; height:3rem;" role="status"></div>
                                    <p class="mt-3 fw-bold text-primary">Loading report...</p>
                                </div>

                                <!-- Summary Wrapper -->
                                <div class="row m-0 overall-card overview-card">

                                    <div class="col-xl-12 col-md-12 p-0">
                                        <div class="row g-3 g-md-4">

                                            <!-- Total Orders -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Total Orders</span>
                                                        <h6 id="total_orders"
                                                            class="mt-2 mb-0 display-6 text-primary fw-bold">0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Total Revenue -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Total Revenue</span>
                                                        <h6 id="total_revenue_sales"
                                                            class="mt-2 mb-0 display-6 text-success fw-bold">0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Top Product -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Total Discount</span>
                                                        <h6 id="total_discount" class="mt-2 mb-0 text-secondary">0</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Total Refund -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Total Refund</span>
                                                        <h6 id="total_refund" class="mt-2 mb-0 text-secondary">0</h6>
                                                    </div>
                                                </div>
                                            </div>



                                            <!-- Gross Sale -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Gross Sale</span>
                                                        <h6 id="gross_sale" class="mt-2 mb-0 text-primary fw-bold">0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Total Tax -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Total Tax</span>
                                                        <h6 id="total_tax" class="mt-2 mb-0 text-warning fw-bold">0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Cash Sale -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Cash Sale</span>
                                                        <h6 id="cash_sale" class="mt-2 mb-0 text-info fw-bold">0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Card Sale -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Card Sale</span>
                                                        <h6 id="card_sale" class="mt-2 mb-0 text-success fw-bold">0</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Online Sale -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Online Sale</span>
                                                        <h6 id="online_sale" class="mt-2 mb-0 text-primary fw-bold">0
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Credit Sale -->
                                            <div class="col-md-4">
                                                <div class="light-card balance-card widget-hover shadow rounded-3 p-3">
                                                    <div>
                                                        <span class="text-muted">Credit Sale</span>
                                                        <h6 id="credit_sale" class="mt-2 mb-0 text-danger fw-bold">0</h6>
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
            </div>


        </div>
    </div>


@endsection

@section('script')

    <script src="{{ asset('assets/js/chart/echart/config.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/dashboard_2.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script>

        //     let orders = [
        //   { day: 1, earning: 200, expense: 400 },
        //   { day: 2, earning: 200, expense: 600 },
        //   { day: 3, earning: 350, expense: 700 },
        //   { day: 4, earning: 400, expense: 400 },
        //   { day: 5, earning: 200, expense: 700 },
        //    { day: 6, earning: 200, expense: 700 },
        //     { day: 7, earning: 200, expense: 700 },
        // ];

        // let categories = [];
        // let earningData = [];
        // let expenseData = [];

        // orders.forEach(order => {
        //   categories.push(order.day.toString());
        //   earningData.push(order.earning);
        //   expenseData.push(order.expense);
        // });

        // var options = {
        //   series: [
        //     { name: 'Earning', data: earningData },
        //     { name: 'Expense', data: expenseData }
        //   ],
        //   chart: {
        //     type: 'bar',
        //     height: 300,
        //     stacked: true,
        //     toolbar: { show: false },
        //     dropShadow: {
        //       enabled: true,
        //       top: 8,
        //       left: 0,
        //       blur: 10,
        //       color: '#7064F5',
        //       opacity: 0.1
        //     }
        //   },
        //   plotOptions: {
        //     bar: { horizontal: false, columnWidth: '25px', borderRadius: 0 },
        //   },
        //   grid: { show: true, borderColor: 'var(--chart-border)' },
        //   dataLabels: { enabled: false },
        //   stroke: { width: 2, colors: ["#fff"] },
        //   fill: { opacity: 1 },
        //   legend: { show: false },
        //   colors: [CubaAdminConfig.primary, '#AAAFCB'],
        //   yaxis: {
        //     tickAmount: 3,
        //     labels: { show: true, style: { fontFamily: 'Rubik, sans-serif' } },
        //     axisBorder: { show: false },
        //     axisTicks: { show: false },
        //   },
        //   xaxis: {
        //     categories: categories,
        //     labels: { style: { fontFamily: 'Rubik, sans-serif' } },
        //     axisBorder: { show: false },
        //     axisTicks: { show: false },
        //   },
        //   responsive: [
        //     { breakpoint: 1661, options: { chart: { height: 290 } } },
        //     { breakpoint: 767, options: { plotOptions: { bar: { columnWidth: '35px' } }, yaxis: { labels: { show: false } } } },
        //     { breakpoint: 481, options: { chart: { height: 200 } } },
        //     { breakpoint: 420, options: { chart: { height: 170 }, plotOptions: { bar: { columnWidth: '40px' } } } },
        //   ]
        // };


        // var chart = new ApexCharts(document.querySelector("#chart-currently"), options);
        // chart.render();
    </script>

@endsection
