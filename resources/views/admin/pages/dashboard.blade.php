@extends('admin.layouts.master')
@section('title', 'Dashboard')

@section('css')
@endsection

@section('style')
<style>
.profile-box {
  background: linear-gradient(103.75deg, #33B1EE -13.9%, var(--theme-deafult) 79.68%);
  color: #fff;
}
.profile-box .cartoon {
  position: absolute;
  bottom: -15px;
  right: 20px;
  animation: bounce-effect 5s infinite ease-in;
}
[dir=rtl] .profile-box .cartoon {
  right: unset !important;
  left: 5px;
}
@media (max-width: 1660px) {
  .profile-box .cartoon {
    right: 30px;
    text-align: right;
  }
  [dir=rtl] .profile-box .cartoon {
    left: -6px;
    text-align: left;
  }
  .profile-box .cartoon img {
    width: 80%;
  }
}
@media (max-width: 1500px) {
  .profile-box .cartoon img {
    width: 60%;
  }
}
@media (max-width: 767px) {
  .profile-box .cartoon {
    right: 10px;
  }
  [dir=rtl] .profile-box .cartoon {
    left: -10px;
  }
  .profile-box .cartoon img {
    width: 52%;
  }
}
@media (max-width: 575px) {
  .profile-box .cartoon {
    right: 30px;
  }
  [dir=rtl] .profile-box .cartoon {
    left: 10px;
  }
  .profile-box .cartoon img {
    width: 48%;
  }
}
.profile-box .greeting-user p {
  width: 60%;
}
@media (max-width: 1500px) {
  .profile-box .greeting-user p {
    width: 70%;
  }
}
@media (max-width: 1199px) {
  .profile-box .greeting-user p {
    width: 80%;
  }
}
@media (max-width: 767px) {
  .profile-box .greeting-user p {
    width: 98%;
  }
}
@media (max-width: 600px) {
  .profile-box .greeting-user p {
    width: 100%;
  }
}
@media (max-width: 575px) {
  .profile-box .greeting-user p {
    width: 98%;
  }
}
.profile-box .whatsnew-btn {
  margin-top: 3.5rem;
}
@media (max-width: 1500px) {
  .profile-box .whatsnew-btn {
    margin-top: 1.7rem;
  }
}
@media (max-width: 991px) {
  .profile-box .whatsnew-btn {
    margin-top: 3.5rem;
  }
}
@media (max-width: 638px) {
  .profile-box .whatsnew-btn {
    margin-top: 2rem;
  }
}
@media (max-width: 767px) {
  .profile-box .whatsnew-btn .btn {
    padding: 6px 10px;
  }
}



.widget-1 {
  background-image: url(../images/dashboard/widget-bg.png);
  background-size: cover;
  margin-bottom: 25px;
}
.widget-1 i {
  font-weight: 700;
  font-size: 12px;
}
.widget-1 .f-w-500 {
  display: flex;
  align-items: center;
}
@media (max-width: 1580px) and (min-width: 1200px) {
  .widget-1 .f-w-500 {
    display: none;
  }
}
.widget-1 .card-body {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  padding: 18px 25px;
}
@media (max-width: 1720px) {
  .widget-1 .card-body {
    padding: 18px;
  }
}
.widget-1 .widget-round {
  position: relative;
  display: inline-block;
  border-width: 1px;
  border-style: solid;
  border-radius: 100%;
}
.widget-1 .widget-round .bg-round {
  width: 56px;
  height: 56px;
  box-shadow: 1px 2px 21px -2px rgba(214, 214, 227, 0.83);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 100%;
  margin: 6px;
  position: relative;
  z-index: 1;
}
.widget-1 .widget-round .bg-round svg {
  width: 24px;
  height: 24px;
}
.widget-1 .widget-round .bg-round .half-circle {
  height: 52px;
  position: absolute;
  left: -9px;
  width: 35px;
  bottom: -8px;
  background: #fff;
  z-index: -1;
}
@media (max-width: 1600px) {
  .widget-1 .widget-round .bg-round {
    width: 40px;
    height: 40px;
  }
  .widget-1 .widget-round .bg-round svg {
    width: 22px;
    height: 22px;
  }
  .widget-1 .widget-round .bg-round .half-circle {
    height: 40px;
    left: -10px;
    width: 30px;
    bottom: -8px;
  }
}
.widget-1 .widget-round.primary {
  border-color: var(--theme-deafult);
}
.widget-1 .widget-round.secondary {
  border-color: var(--theme-secondary);
}
.widget-1 .widget-round.success {
  border-color: #54BA4A;
}
.widget-1 .widget-round.warning {
  border-color: #FFAA05;
}
.widget-1 .widget-content {
  display: flex;
  align-items: center;
  gap: 15px;
}
@media (max-width: 1600px) {
  .widget-1 .widget-content {
    gap: 10px;
  }
}
.widget-1 .widget-content h4 {
  margin-bottom: 4px;
}
.widget-1:hover {
  transform: translateY(-5px);
  transition: 0.5s;
}
.widget-1:hover .widget-round .svg-fill:not(.half-circle) {
  animation: tada 1.5s ease infinite;
}

.widget-with-chart .card-body {
  align-items: center;
}


.order-chart > div {
  margin-top: -25px;
  margin-bottom: -43px;
}
.order-chart svg path {
  clip-path: inset(7% 0% 0% 0% round 0.6rem);
}

.growth-wrap .card-header {
  position: relative;
  z-index: 1;
}

.growth-wrapper > div {
  margin-top: -54px;
  margin-bottom: -25px;
}
@media (max-width: 1481px) {
  .growth-wrapper > div {
    margin-top: -50px;
  }
}

.profit-chart > div {
  margin-top: -45px;
  margin-bottom: -48px;
}
.profit-chart .apexcharts-canvas .apexcharts-tooltip-text-label {
  display: none;
}


/*chart styles*/
.widget-charts .widget-1 .card-body {
  padding: 30px 25px;
}

.chart-widget-top #chart-widget1,
.chart-widget-top #chart-widget2,
.chart-widget-top #chart-widget3 {
  margin-bottom: -14px;
}
.chart-widget-top #chart-widget1 .apexcharts-xaxistooltip,
.chart-widget-top #chart-widget2 .apexcharts-xaxistooltip,
.chart-widget-top #chart-widget3 .apexcharts-xaxistooltip {
  display: none;
}
.chart-widget-top span {
  color: #6C757D;
}

.bar-chart-widget .apexcharts-legend {
  bottom: 0 !important;
}
.bar-chart-widget .apexcharts-legend .apexcharts-legend-series {
  margin: 0 10px !important;
}
.bar-chart-widget .apexcharts-legend .apexcharts-legend-marker {
  margin-right: 5px;
}
.bar-chart-widget .top-content {
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
}
.bar-chart-widget .earning-details {
  height: 100%;
  align-items: center;
  justify-content: center;
  text-align: center;
  display: flex;
  letter-spacing: 1px;
}
.bar-chart-widget .earning-details i {
  font-size: 230px;
  position: absolute;
  opacity: 0.1;
  right: -30px;
  top: 0;
}
.bar-chart-widget .earning-details i:hover {
  transform: rotate(-5deg) scale(1.1);
  transition: all 0.3s ease;
}
.bar-chart-widget .num {
  font-weight: 600;
}
.bar-chart-widget .num .color-bottom {
  color: #000;
}

.skill-chart {
  margin-bottom: -48px;
}
.skill-chart .apexcharts-legend .apexcharts-legend-series span {
  display: block;
}
.skill-chart .apexcharts-legend .apexcharts-legend-series .apexcharts-legend-text {
  margin: 10px 0 20px;
}

.progress-chart {
  margin: -11px 0 -20px;
}
.progress-chart .apexcharts-canvas svg path,
.progress-chart .apexcharts-canvas svg rect {
  clip-path: inset(1% 0% 0% 0% round 1rem);
}

.bottom-content span {
  color: #52526c;
}
.bottom-content .block-bottom {
  display: block;
}
/*End chart style*/
.f-light {
  color: #52526C;
  opacity: 0.8;
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
  padding: 30px !important;
  background-color: transparent !important;
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

.chart-right {
  position: relative;
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
.current-sale-container > div {
  margin: -22px 0 -30px -16px;
}
@media (max-width: 1199px) {
  .current-sale-container > div {
    margin-bottom: 0;
  }
}
@media (max-width: 404px) {
  .current-sale-container > div {
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
  .current-sale-container.order-container > div {
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

/* .recent-wrapper {
  align-items: center;
} */
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
  .recent-wrapper > div {
    width: 100%;
  }
}
@media (max-width: 1560px) and (min-width: 1400px) {
  .recent-wrapper > div:last-child {
    display: none;
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
.dashboard-page-circle {
  --recent-chart-bg: #FCFCFD;
}
</style>


@endsection

@section('content')
    <div class="container-fluid">
     <div class="row widget-grid">
           <div class="col-xxl-4 col-sm-6 box-col-6">
            <div class="row">
              <div class="col-xl-12">
                <div class="card widget-1">
                  <div class="card-body">
                    <div class="widget-content">
                      <div class="widget-round secondary">
                        <div class="bg-round">
                          <svg class="svg-fill">
                            <use href="assets/svg/icon-sprite.svg#cart"> </use>
                          </svg>
                          <svg class="half-circle svg-fill">
                            <use href="assets/svg/icon-sprite.svg#halfcircle"></use>
                          </svg>
                        </div>
                      </div>
                      <div>
                        <h4>{{ $totalPurchases }}</h4><span class="f-light">Orders</span>
                      </div>
                    </div>
                    <div class="font-secondary f-w-500"><i class="icon-arrow-up icon-rotate me-1"></i><span>+50%</span></div>
                  </div>
                </div>
                <div class="col-xl-12">
                  <div class="card widget-1">
                    <div class="card-body">
                      <div class="widget-content">
                        <div class="widget-round primary">
                          <div class="bg-round">
                            <svg class="svg-fill">
                              <use href="assets/svg/icon-sprite.svg#tag"> </use>
                            </svg>
                            <svg class="half-circle svg-fill">
                              <use href="assets/svg/icon-sprite.svg#halfcircle"></use>
                            </svg>
                          </div>
                        </div>
                        <div>
                          <h4>{{ $totalSales }}</h4><span class="f-light">Sales</span>
                        </div>
                      </div>
                      <div class="font-primary f-w-500"><i class="icon-arrow-up icon-rotate me-1"></i><span>+70%</span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
           <div class="col-xxl-4 col-sm-6 box-col-6">
            <div class="row">
              <div class="col-xl-12">
                <div class="card widget-1">
                  <div class="card-body">
                    <div class="widget-content">
                      <div class="widget-round warning">
                        <div class="bg-round">
                          <svg class="svg-fill">
                            <use href="assets/svg/icon-sprite.svg#return-box"> </use>
                          </svg>
                          <svg class="half-circle svg-fill">
                            <use href="assets/svg/icon-sprite.svg#halfcircle"></use>
                          </svg>
                        </div>
                      </div>
                      <div>
                        <h4>0</h4><span class="f-light">Sales return</span>
                      </div>
                    </div>
                    <div class="font-warning f-w-500"><i class="icon-arrow-down icon-rotate me-1"></i><span>-20%</span></div>
                  </div>
                </div>
                <div class="col-xl-12">
                  <div class="card widget-1">
                    <div class="card-body">
                      <div class="widget-content">
                        <div class="widget-round success">
                          <div class="bg-round">
                            <svg class="svg-fill">
                              <use href="assets/svg/icon-sprite.svg#rate"> </use>
                            </svg>
                            <svg class="half-circle svg-fill">
                              <use href="assets/svg/icon-sprite.svg#halfcircle"></use>
                            </svg>
                          </div>
                        </div>
                        <div>
                          <h4>0</h4><span class="f-light">Purchase rate</span>
                        </div>
                      </div>
                      <div class="font-success f-w-500"><i class="icon-arrow-up icon-rotate me-1"></i><span>+70%</span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
       <div class="col-xxl-4 col-sm-6 box-col-6">
            <div class="row">
              <div class="col-xxl-12 col-xl-6 box-col-12">
                <div class="card widget-1 widget-with-chart">
                  <div class="card-body">
                    <div>
                      <h4 class="mb-1">{{ $totalPurchases }}</h4><span class="f-light">Orders</span>
                    </div>
                    <div class="order-chart">
                      <div id="orderchart"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xxl-12 col-xl-6 box-col-12">
                <div class="card widget-1 widget-with-chart">
                  <div class="card-body">
                    <div>
                      <h4 class="mb-1">{{ $totalSales }}</h4><span class="f-light">Profit</span>
                    </div>
                    <div class="profit-chart">
                      <div id="profitchart"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

           <div class="col-xxl-8 col-lg-12 box-col-12">
            <div class="card">
              <div class="card-header card-no-border">
                <h5>Overall balance</h5>
              </div>
              <div class="card-body pt-0">
                <div class="row m-0 overall-card">
                  <div class="col-xl-9 col-md-12 col-sm-7 p-0">
                    <div class="chart-right">
                      <div class="row">
                        <div class="col-xl-12">
                          <div class="card-body p-0">
                            <ul class="balance-data">
                              <li><span class="circle bg-warning"> </span><span class="f-light ms-1">Earning</span></li>
                              <li><span class="circle bg-primary"> </span><span class="f-light ms-1">Expense</span></li>
                            </ul>
                            <div class="current-sale-container">
                              <div id="chart-currently"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-xl-3 col-md-12 col-sm-5 p-0 d-none">
                    <div class="row g-sm-4 g-2">
                      <div class="col-xl-12 col-md-4">
                        <div class="light-card balance-card widget-hover">
                          <div class="svg-box">
                            <svg class="svg-fill">
                              <use href="assets/svg/icon-sprite.svg#income"></use>
                            </svg>
                          </div>
                          <div> <span class="f-light">Income</span>
                            <h6 class="mt-1 mb-0">$22,678</h6>
                          </div>
                          <div class="ms-auto text-end">
                            <div class="dropdown icon-dropdown">
                              <button class="btn dropdown-toggle" id="incomedropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="incomedropdown"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday </a></div>
                            </div><span class="font-success">+$456</span>
                          </div>
                        </div>
                      </div>
                      <div class="col-xl-12 col-md-4">
                        <div class="light-card balance-card widget-hover">
                          <div class="svg-box">
                            <svg class="svg-fill">
                              <use href="assets/svg/icon-sprite.svg#expense"></use>
                            </svg>
                          </div>
                          <div> <span class="f-light">Expense</span>
                            <h6 class="mt-1 mb-0">$12,057</h6>
                          </div>
                          <div class="ms-auto text-end">
                            <div class="dropdown icon-dropdown">
                              <button class="btn dropdown-toggle" id="expensedropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="expensedropdown"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday </a></div>
                            </div><span class="font-danger">+$256</span>
                          </div>
                        </div>
                      </div>
                      <div class="col-xl-12 col-md-4">
                        <div class="light-card balance-card widget-hover">
                          <div class="svg-box">
                            <svg class="svg-fill">
                              <use href="assets/svg/icon-sprite.svg#doller-return"></use>
                            </svg>
                          </div>
                          <div> <span class="f-light">Cashback</span>
                            <h6 class="mt-1 mb-0">8,475</h6>
                          </div>
                          <div class="ms-auto text-end">
                            <div class="dropdown icon-dropdown">
                              <button class="btn dropdown-toggle" id="cashbackdropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cashbackdropdown"><a class="dropdown-item" href="#">Today</a><a class="dropdown-item" href="#">Tomorrow</a><a class="dropdown-item" href="#">Yesterday </a></div>
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
            <div class="col-xxl-4 col-xl-7 col-md-6 col-sm-5 box-col-6">
            <div class="card height-equal">
              <div class="card-header card-no-border">
                <div class="header-top">
                  <h5>Recent Orders</h5>
                  <div class="card-header-right-icon">
                    <div class="dropdown icon-dropdown">
                      <button class="btn dropdown-toggle" id="recentdropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-more-alt"></i></button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentdropdown"><a class="dropdown-item" href="#">Weekly</a><a class="dropdown-item" href="#">Monthly</a><a class="dropdown-item" href="#">Yearly</a></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body pt-0">
                <div class="row recent-wrapper">
                  <div class="col-xl-6">
                    <div class="recent-chart dashboard-page-circle">
                      <div id="recentchart"></div>
                    </div>
                  </div>
                  <div class="col-xl-6">
                    <ul class="order-content">
                      <li> <span class="recent-circle bg-primary"> </span>
                        <div> <span class="f-light f-w-500">Cancelled </span>
                          <h4 class="mt-1 mb-0">2,302<span class="f-light f-14 f-w-400 ms-1">(Last 6 Month) </span></h4>
                        </div>
                      </li>
                      <li> <span class="recent-circle bg-info"></span>
                        <div> <span class="f-light f-w-500">Delivered</span>
                          <h4 class="mt-1 mb-0">9,302<span class="f-light f-14 f-w-400 ms-1">(Last 6 Month) </span></h4>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- End of widget-grid -->
    </div>
@endsection

@section('script')

<script src="{{ asset('assets/js/chart/echart/config.js')}}"></script>
<script src="{{ asset('assets/js/dashboard/default.js')}}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js')}}"></script>
<script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js')}}"></script>

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
