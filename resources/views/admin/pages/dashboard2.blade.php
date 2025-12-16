@extends('admin.layouts.master')
@section('title', 'Dashboard')

@section('css')
<style>
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
.card {
  margin-bottom: 30px;
  border: none;
  transition: all 0.3s ease;
  letter-spacing: 0.5px;
  border-radius: 15px;
  box-shadow: 0px 9px 20px rgba(46, 35, 94, 0.07);
}
.card:hover {
  box-shadow: 0 0 40px rgba(8, 21, 66, 0.05);
  transition: all 0.3s ease;
}
.card .card-header {
  background-color: #fff;
  padding: 30px;
  border-bottom: 1px solid #ecf3fa;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
  position: relative;
}
.card .card-header.card-no-border {
  border-bottom: none !important;
}
.card .card-header h5:not(.mb-0), .card .card-header h5:not(.m-0) {
  margin-bottom: 0;
  text-transform: capitalize;
}
.card .card-header > span {
  font-size: 12px;
  color: #52526c;
  margin-top: 5px;
  display: block;
  letter-spacing: 1px;
}
.card .card-header .card-header-right {
  border-radius: 0 0 0 7px;
  right: 35px;
  top: 24px;
  display: inline-block;
  float: right;
  padding: 8px 0;
  position: absolute;
  background-color: #fff;
  z-index: 1;
}
.card .card-header .card-header-right .card-option {
  text-align: right;
  width: 35px;
  height: 20px;
  overflow: hidden;
  transition: 0.3s ease-in-out;
}
.card .card-header .card-header-right .card-option li {
  display: inline-block;
}
.card .card-header .card-header-right .card-option li:first-child i {
  transition: 1s;
  font-size: 16px;
  color: var(--theme-deafult);
}
.card .card-header .card-header-right .card-option li:first-child i.icofont {
  color: unset;
}
.card .card-header .card-header-right i {
  margin: 0 5px;
  cursor: pointer;
  color: #2c323f;
  line-height: 20px;
}
.card .card-header .card-header-right i.icofont-refresh {
  font-size: 13px;
}
.card .card-body {
  padding: 30px;
  background-color: transparent;
}
.card .card-body p:last-child {
  margin-bottom: 0;
}
.card .sub-title {
  padding-bottom: 12px;
  font-size: 18px;
}
.card .card-footer {
  background-color: #fff;
  border-top: 1px solid #ecf3fa;
  padding: 30px;
  border-bottom-left-radius: 15px;
  border-bottom-right-radius: 15px;
}
.card.card-load .card-loader {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  background-color: rgba(255, 255, 255, 0.7);
  z-index: 8;
  align-items: center;
  justify-content: center;
}
.card.card-load .card-loader i {
  margin: 0 auto;
  color: var(--theme-deafult);
  font-size: 20px;
}
.card.full-card {
  position: fixed;
  top: 0;
  z-index: 99999;
  box-shadow: none;
  right: 0;
  border-radius: 0;
  border: 1px solid #efefef;
  width: calc(100vw - 12px);
  height: 100vh;
}
.card.full-card .card-body {
  overflow: auto;
}

.page-body-wrapper .card .sub-title {
  font-family: Rubik, sans-serif;
  font-weight: normal;
  color: #52526c;
}

.card-absolute {
  margin-top: 20px;
}
.card-absolute .card-header {
  position: absolute;
  top: -20px;
  left: 15px;
  border-radius: 0.25rem;
  padding: 10px 15px;
}
.card-absolute .card-header h5 {
  font-size: 17px;
}
.card-absolute .card-body {
  margin-top: 10px;
}

.card-header .border-tab {
  margin-bottom: -13px;
}

.custom-card {
  overflow: hidden;
  padding: 30px;
}
.custom-card .card-header {
  padding: 0;
}
.custom-card .card-header img {
  border-radius: 50%;
  margin-top: -100px;
  transform: scale(1.5);
}
.custom-card .card-profile {
  text-align: center;
}
.custom-card .card-profile img {
  height: 110px;
  padding: 7px;
  background-color: #fff;
  z-index: 1;
  position: relative;
}
.custom-card .card-social {
  text-align: center;
}
.custom-card .card-social li {
  display: inline-block;
  padding: 15px 0;
}
.custom-card .card-social li:last-child a {
  margin-right: 0;
}
.custom-card .card-social li a {
  padding: 0;
  margin-right: 15px;
  color: rgb(188, 198, 222);
  font-size: 16px;
  transition: all 0.3s ease;
}
.custom-card .card-social li a:hover {
  color: var(--theme-deafult);
  transition: all 0.3s ease;
}
.custom-card .profile-details h6 {
  margin-bottom: 30px;
  margin-top: 10px;
  color: #52526c;
  font-size: 14px;
}
.custom-card .card-footer {
  padding: 0;
}
.custom-card .card-footer > div {
  padding: 15px;
  text-align: center;
}
.custom-card .card-footer > div + div {
  border-left: 1px solid #efefef;
}
.custom-card .card-footer > div h3 {
  margin-bottom: 0;
  font-size: 24px;
}
.custom-card .card-footer > div h6 {
  font-size: 14px;
  color: #52526c;
}
.custom-card .card-footer > div h5 {
  font-size: 16px;
  margin-bottom: 0;
}
.custom-card .card-footer > div i {
  font-size: 24px;
  display: inline-block;
  margin-bottom: 15px;
}
.custom-card .card-footer > div .m-b-card {
  margin-bottom: 10px;
}

</style>
@endsection


@section('content')
    <div class="container-fluid">
                <div class="row size-column">
                    <div class="col-xxl-10 col-md-12 box-col-8 grid-ed-12">
                        <div class="row">
                            <div class="col-xxl-5 col-md-7 box-col-7">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card o-hidden">
                                            <div class="card-body balance-widget"><span class="f-w-500 f-light">Total Balance</span>
                                                <h4 class="mb-3 mt-1 f-w-500 mb-0 f-22">$<span class="counter">245,154.00 </span><span class="f-light f-14 f-w-400 ms-1">this month</span></h4><a class="purchase-btn btn btn-primary btn-hover-effect f-w-500" href="#">Tap Up Balance</a>
                                                <div class="mobile-right-img"><img class="left-mobile-img" src="assets/images/dashboard-2/widget-img.png" alt=""><img class="mobile-img" src="assets/images/dashboard-2/mobile.gif" alt="mobile with coin"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card small-widget">
                                            <div class="card-body primary"> <span class="f-light">New Orders</span>
                                                <div class="d-flex align-items-end gap-1">
                                                    <h4>2,435</h4><span class="font-primary f-12 f-w-500"><i class="icon-arrow-up"></i><span>+50%</span></span>
                                                </div>
                                                <div class="bg-gradient">
                                                    <svg class="stroke-icon svg-fill">
                                                        <use href="assets/svg/icon-sprite.svg#new-order"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card small-widget">
                                            <div class="card-body warning"><span class="f-light">New Customers</span>
                                                <div class="d-flex align-items-end gap-1">
                                                    <h4>2,908</h4><span class="font-warning f-12 f-w-500"><i class="icon-arrow-up"></i><span>+20%</span></span>
                                                </div>
                                                <div class="bg-gradient">
                                                    <svg class="stroke-icon svg-fill">
                                                        <use href="assets/svg/icon-sprite.svg#customers"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card small-widget">
                                            <div class="card-body secondary"><span class="f-light">Average Sale</span>
                                                <div class="d-flex align-items-end gap-1">
                                                    <h4>$389k</h4><span class="font-secondary f-12 f-w-500"><i class="icon-arrow-down"></i><span>-10%</span></span>
                                                </div>
                                                <div class="bg-gradient">
                                                    <svg class="stroke-icon svg-fill">
                                                        <use href="assets/svg/icon-sprite.svg#sale"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card small-widget">
                                            <div class="card-body success"><span class="f-light">Gross Profit</span>
                                                <div class="d-flex align-items-end gap-1">
                                                    <h4>$3,908</h4><span class="font-success f-12 f-w-500"><i class="icon-arrow-up"></i><span>+80%</span></span>
                                                </div>
                                                <div class="bg-gradient">
                                                    <svg class="stroke-icon svg-fill">
                                                        <use href="assets/svg/icon-sprite.svg#profit"></use>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-md-5 col-sm-6 box-col-5">
                                <div class="appointment">
                                    <div class="card">
                                        <div class="card-header card-no-border">
                                            <div class="header-top">
                                                <h5 class="m-0">Valuable Customer</h5>
                                                <div class="card-header-right-icon">
                                                    <div class="dropdown icon-dropdown">
                                                        <button class="btn dropdown-toggle" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="appointment-table customer-table table-responsive">
                                                <table class="table table-bordernone">
                                                    <tbody>
                                                        <tr>
                                                            <td><img class="img-fluid img-40 rounded-circle me-2" src="assets/images/dashboard/user/1.jpg" alt="user"></td>
                                                            <td class="img-content-box"><a class="f-w-500" href="user-profile-2.html">Jane Cooper</a><span class="f-light">alma.lawson@gmail.com</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><img class="img-fluid img-40 rounded-circle me-2" src="assets/images/dashboard/user/2.jpg" alt="user"></td>
                                                            <td class="img-content-box"><a class="f-w-500" href="user-profile-2.html">Cameron Willia</a><span class="f-light">tim.jennings@gmail.com</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><img class="img-fluid img-40 rounded-circle me-2" src="assets/images/dashboard/user/9.jpg" alt="user"></td>
                                                            <td class="img-content-box"><a class="f-w-500" href="user-profile-2.html">Floyd Miles</a><span class="f-light">kenzi.lawson@gmail.com</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><img class="img-fluid img-40 rounded-circle me-2" src="assets/images/dashboard/user/5.jpg" alt="user"></td>
                                                            <td class="img-content-box"><a class="f-w-500" href="user-profile-2.html">Dianne Russell</a><span class="f-light">curtis.weaver@gmail.com</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><img class="img-fluid img-40 rounded-circle me-2" src="assets/images/dashboard/user/3.jpg" alt="user"></td>
                                                            <td class="img-content-box"><a class="f-w-500" href="user-profile-2.html">Guy Hawkins</a><span class="f-light">curtis.weaver@gmail.com</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-sm-6 box-col-6">
                                <div class="card">
                                    <div class="card-header card-no-border">
                                        <div class="header-top">
                                            <h5 class="m-0">Order this month</h5>
                                            <div class="card-header-right-icon">
                                                <div class="dropdown icon-dropdown">
                                                    <button class="btn dropdown-toggle" id="orderButton" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderButton"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday</a></div>
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
                            <div class="col-xxl-3 col-md-6 box-col-6">
                                <div class="card">
                                    <div class="card-header card-no-border">
                                        <h5>Monthly Profits</h5><span class="f-light f-w-500 f-14">(Total profit growth of 30%)</span>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="monthly-profit">
                                            <div id="profitmonthly"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-9 box-col-12">
                                <div class="card">
                                    <div class="card-header card-no-border">
                                        <h5>Order Overview</h5>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="row m-0 overall-card overview-card">
                                            <div class="col-xl-9 col-md-8 col-sm-7 p-0 box-col-7">
                                                <div class="chart-right">
                                                    <div class="row">
                                                        <div class="col-xl-12">
                                                            <div class="card-body p-0">
                                                                <ul class="balance-data">
                                                                    <li><span class="circle bg-secondary"></span><span class="f-light ms-1">Refunds</span></li>
                                                                    <li><span class="circle bg-primary"> </span><span class="f-light ms-1">Earning</span></li>
                                                                    <li><span class="circle bg-success"> </span><span class="f-light ms-1">Order</span></li>
                                                                </ul>
                                                                <div class="current-sale-container order-container">
                                                                    <div class="overview-wrapper" id="orderoverview"></div>
                                                                    <div class="back-bar-container">
                                                                        <div id="order-bar"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-3 col-md-4 col-sm-5 p-0 box-col-5">
                                                <div class="row g-sm-3 g-2">
                                                    <div class="col-md-12">
                                                        <div class="light-card balance-card widget-hover">
                                                            <div class="svg-box">
                                                                <svg class="svg-fill">
                                                                    <use href="assets/svg/icon-sprite.svg#orders"></use>
                                                                </svg>
                                                            </div>
                                                            <div> <span class="f-light">Orders</span>
                                                                <h6 class="mt-1 mb-0">10,098 </h6>
                                                            </div>
                                                            <div class="ms-auto text-end">
                                                                <div class="dropdown icon-dropdown">
                                                                    <button class="btn dropdown-toggle" id="orderdropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderdropdown"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday </a></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="light-card balance-card widget-hover">
                                                            <div class="svg-box">
                                                                <svg class="svg-fill">
                                                                    <use href="assets/svg/icon-sprite.svg#expense"></use>
                                                                </svg>
                                                            </div>
                                                            <div> <span class="f-light">Earning</span>
                                                                <h6 class="mt-1 mb-0">$12,678</h6>
                                                            </div>
                                                            <div class="ms-auto text-end">
                                                                <div class="dropdown icon-dropdown">
                                                                    <button class="btn dropdown-toggle" id="earningdropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningdropdown"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday </a></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="light-card balance-card widget-hover">
                                                            <div class="svg-box">
                                                                <svg class="svg-fill">
                                                                    <use href="assets/svg/icon-sprite.svg#doller-return"></use>
                                                                </svg>
                                                            </div>
                                                            <div> <span class="f-light">Refunds</span>
                                                                <h6 class="mt-1 mb-0">3,001</h6>
                                                            </div>
                                                            <div class="ms-auto text-end">
                                                                <div class="dropdown icon-dropdown">
                                                                    <button class="btn dropdown-toggle" id="incomedropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="incomedropdown"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday</a></div>
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
                            <div class="col-xxl-3 col-xl-4 col-sm-6 box-col-6 wow zoomIn">
                                <div class="card purchase-card discover"><img class="img-fluid" src="assets/images/dashboard-2/discover.png" alt="vector discover">
                                    <div class="card-body pt-3">
                                        <h5 class="mb-1">Discover Pro</h5>
                                        <p class="f-light">Amet minim mollit non deserunt ullamco est sit aliqua dolor </p><a class="purchase-btn btn btn-hover-effect btn-primary f-w-500" href="https://1.envato.market/3GVzd" target="_blank">Update Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-xl-4 col-sm-6 box-col-6">
                                <div class="card visitor-card">
                                    <div class="card-header card-no-border">
                                        <div class="header-top">
                                            <h5 class="m-0">Visitors<span class="f-14 font-primary f-w-500 ms-1">
                                                    <svg class="svg-fill me-1">
                                                        <use href="assets/svg/icon-sprite.svg#user-visitor"></use>
                                                    </svg>(+2.8)</span></h5>
                                            <div class="card-header-right-icon">
                                                <div class="dropdown icon-dropdown">
                                                    <button class="btn dropdown-toggle" id="visitorButton" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="visitorButton"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday</a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="visitors-container">
                                            <div id="visitor-chart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-5 col-xl-4 box-col-12">
                                <div class="card recent-order">
                                    <div class="card-header card-no-border">
                                        <div class="header-top">
                                            <h5 class="m-0">Recent Orders</h5>
                                            <div class="card-header-right-icon">
                                                <div class="dropdown icon-dropdown">
                                                    <button class="btn dropdown-toggle" id="recentButton" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentButton"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday</a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <div class="recent-sliders">
                                            <div class="nav nav-pills" id="v-pills-tab" role="tablist">
                                                <button class="active frame-box" id="v-pills-shirt-tab" data-bs-toggle="pill" data-bs-target="#v-pills-shirt" type="button" role="tab" aria-controls="v-pills-shirt" aria-selected="true"><span class="frame-image"><img src="assets/images/dashboard-2/order/1.png" alt="vector T-shirt"></span></button>
                                                <button class="frame-box" id="v-pills-television-tab" data-bs-toggle="pill" data-bs-target="#v-pills-television" type="button" role="tab" aria-controls="v-pills-television" aria-selected="false"><span class="frame-image"><img src="assets/images/dashboard-2/order/2.png" alt="vector television"></span></button>
                                                <button class="frame-box" id="v-pills-headphone-tab" data-bs-toggle="pill" data-bs-target="#v-pills-headphone" type="button" role="tab" aria-controls="v-pills-headphone" aria-selected="false"><span class="frame-image"><img src="assets/images/dashboard-2/order/3.png" alt="vector headphone"></span></button>
                                                <button class="frame-box" id="v-pills-chair-tab" data-bs-toggle="pill" data-bs-target="#v-pills-chair" type="button" role="tab" aria-controls="v-pills-chair" aria-selected="false"><span class="frame-image"><img src="assets/images/dashboard-2/order/4.png" alt="vector chair"></span></button>
                                                <button class="frame-box" id="v-pills-lamp-tab" data-bs-toggle="pill" data-bs-target="#v-pills-lamp" type="button" role="tab" aria-controls="v-pills-lamp" aria-selected="false"><span class="frame-image"><img src="assets/images/dashboard-2/order/5.png" alt="vector lamp"></span></button>
                                            </div>
                                            <div class="tab-content" id="v-pills-tabContent">
                                                <div class="tab-pane fade show active" id="v-pills-shirt" role="tabpanel" aria-labelledby="v-pills-shirt-tab">
                                                    <div class="recent-table table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="f-light">Item</th>
                                                                    <th class="f-light">Qty</th>
                                                                    <th class="f-light">Price</th>
                                                                    <th class="f-light">Status</th>
                                                                    <th class="f-light">Total Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/4.png" alt="t-shirt"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">T-shirt</a></h6><span class="f-light f-12">Id : #CFDE-2163</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X1</td>
                                                                    <td class="f-w-500">$56.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-success">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Verified
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$100.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/3.png" alt="t-shirt"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Pink T-shirt</a></h6><span class="f-light f-12">Id : #CFDE-2780</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X2</td>
                                                                    <td class="f-w-500">$156.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-danger">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Rejected
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$870.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="v-pills-television" role="tabpanel" aria-labelledby="v-pills-television-tab">
                                                    <div class="recent-table table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="f-light">Item</th>
                                                                    <th class="f-light">Qty</th>
                                                                    <th class="f-light">Price</th>
                                                                    <th class="f-light">Status</th>
                                                                    <th class="f-light">Total Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/5.png" alt="television"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Sony</a></h6><span class="f-light f-12">Id : #CFDE-2163</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X1</td>
                                                                    <td class="f-w-500">$56.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-danger">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Rejected
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$390.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/6.png" alt="television"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Samsung</a></h6><span class="f-light f-12">Id : #CFDE-2780</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X2</td>
                                                                    <td class="f-w-500">$100.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-success">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Verified
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$870.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="v-pills-headphone" role="tabpanel" aria-labelledby="v-pills-headphone-tab">
                                                    <div class="recent-table table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="f-light">Item</th>
                                                                    <th class="f-light">Qty</th>
                                                                    <th class="f-light">Price</th>
                                                                    <th class="f-light">Status</th>
                                                                    <th class="f-light">Total Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/1.png" alt="headephones"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Sony</a></h6><span class="f-light f-12">Id : #CFDE-2163</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X1</td>
                                                                    <td class="f-w-500">$56.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-success">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Verified
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$100.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/2.png" alt="headephones"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Sennheiser</a></h6><span class="f-light f-12">Id : #CFDE-2780</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X2</td>
                                                                    <td class="f-w-500">$156.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-danger">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Rejected
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$100.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="v-pills-chair" role="tabpanel" aria-labelledby="v-pills-chair-tab">
                                                    <div class="recent-table table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="f-light">Item</th>
                                                                    <th class="f-light">Qty</th>
                                                                    <th class="f-light">Price</th>
                                                                    <th class="f-light">Status</th>
                                                                    <th class="f-light">Total Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/7.png" alt="chair"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Chair</a></h6><span class="f-light f-12">Id : #CFDE-2163</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X1</td>
                                                                    <td class="f-w-500">$48.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-success">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Verified
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$50.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/8.png" alt="chair"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Office chair</a></h6><span class="f-light f-12">Id : #CFDE-2780</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X2</td>
                                                                    <td class="f-w-500">$73.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-danger">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Rejected
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$75.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="v-pills-lamp" role="tabpanel" aria-labelledby="v-pills-lamp-tab">
                                                    <div class="recent-table table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="f-light">Item</th>
                                                                    <th class="f-light">Qty</th>
                                                                    <th class="f-light">Price</th>
                                                                    <th class="f-light">Status</th>
                                                                    <th class="f-light">Total Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/9.png" alt="lamp"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Lamp</a></h6><span class="f-light f-12">Id : #CFDE-2163</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X1</td>
                                                                    <td class="f-w-500">$20.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-success">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Verified
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$25.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="product-content">
                                                                            <div class="order-image"><img src="assets/images/dashboard-2/order/sub-product/10.png" alt="lamp"></div>
                                                                            <div>
                                                                                <h6 class="f-14 mb-0"><a href="order-history-2.html">Bedside lamp</a></h6><span class="f-light f-12">Id : #CFDE-2780</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">X2</td>
                                                                    <td class="f-w-500">$70.00</td>
                                                                    <td class="f-w-500">
                                                                        <div class="recent-status font-danger">
                                                                            <svg class="me-1">
                                                                                <use href="assets/svg/icon-sprite.svg#24-hour"> </use>
                                                                            </svg>Rejected
                                                                        </div>
                                                                    </td>
                                                                    <td class="f-w-500">$88.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-xl-3 col-md-4 grid-ed-none box-col-4e d-xxl-block d-none">
                        <div class="card">
                            <div class="card-header card-no-border">
                                <h5>Top Categories</h5>
                            </div>
                            <div class="card-body pt-0">
                                <ul class="categories-list">
                                    <li class="d-flex">
                                        <div class="bg-light"> <img src="assets/images/dashboard-2/category/1.png" alt="vector burger"></div>
                                        <div>
                                            <h6 class="mb-0"><a href="product-2.html">Food & Drinks</a></h6><span class="f-light f-12 f-w-500">(12,200)</span>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="bg-light"> <img src="assets/images/dashboard-2/category/2.png" alt="vector furniture"></div>
                                        <div>
                                            <h6 class="mb-0"><a href="product-2.html">Furniture</a></h6><span class="f-light f-12 f-w-500">(7,510)</span>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="bg-light"> <img src="assets/images/dashboard-2/category/3.png" alt="vector grocery box"></div>
                                        <div>
                                            <h6 class="mb-0"><a href="product-2.html">Grocery</a></h6><span class="f-light f-12 f-w-500">(15,475)</span>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="bg-light"> <img src="assets/images/dashboard-2/category/4.png" alt="vector game remote"></div>
                                        <div>
                                            <h6 class="mb-0"><a href="product-2.html">Electronics</a></h6><span class="f-light f-12 f-w-500">(27,840)</span>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="bg-light"> <img src="assets/images/dashboard-2/category/5.png" alt="vector toys"></div>
                                        <div>
                                            <h6 class="mb-0"><a href="product-2.html">Toys & Games</a></h6><span class="f-light f-12 f-w-500">(8,788)</span>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="bg-light"> <img src="assets/images/dashboard-2/category/6.png" alt="vector monitor"></div>
                                        <div>
                                            <h6 class="mb-0"><a href="product-2.html">Desktop</a></h6><span class="f-light f-12 f-w-500">(10,673)</span>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="bg-light"> <img src="assets/images/dashboard-2/category/7.png" alt="vector mobile"></div>
                                        <div>
                                            <h6 class="mb-0"><a href="product-2.html">Mobile & Accessories</a></h6><span class="f-light f-12 f-w-500">(5,129) </span>
                                        </div>
                                    </li>
                                </ul>
                                <div class="recent-activity notification">
                                    <h5>Recent Activity</h5>
                                    <ul>
                                        <li class="d-flex">
                                            <div class="activity-dot-primary"></div>
                                            <div class="w-100 ms-3">
                                                <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">8th March, 2022 </span></p>
                                                <h6>Added Bew Items<span class="dot-notification"></span></h6><span class="f-light">Quisque a consequat ante sit amet magna...</span>
                                                <div class="recent-images">
                                                    <ul>
                                                        <li>
                                                            <div class="recent-img-wrap"><img src="assets/images/dashboard-2/product/1.png" alt="chair"></div>
                                                        </li>
                                                        <li>
                                                            <div class="recent-img-wrap"><img src="assets/images/dashboard-2/product/2.png" alt="chair"></div>
                                                        </li>
                                                        <li>
                                                            <div class="recent-img-wrap"><img src="assets/images/dashboard-2/product/3.png" alt="men t-shirt"></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="d-flex">
                                            <div class="activity-dot-warning"></div>
                                            <div class="w-100 ms-3">
                                                <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">2nd Sep, Today</span></p>
                                                <h6>Anamika Comments this Poducts<span class="dot-notification"></span></h6><span>Quisque a consequat ante sit amet magna...</span>
                                            </div>
                                        </li>
                                        <li class="d-flex">
                                            <div class="activity-dot-secondary"></div>
                                            <div class="w-100 ms-3">
                                                <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">3nd Sep, 2022</span></p>
                                                <h6>Jacksion Purchase <span class="dot-notification"></span></h6><span>Quisque a consequat ante sit amet magna...</span>
                                            </div>
                                        </li>
                                        <li class="d-flex">
                                            <div class="activity-dot-success"></div>
                                            <div class="w-100 ms-3">
                                                <p class="d-flex justify-content-between mb-2"><span class="date-content light-background">2nd Sep, Today</span></p>
                                                <h6>Anamika Comments this Poducts<span class="dot-notification"></span></h6><span>Quisque a consequat ante sit amet magna...</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection



