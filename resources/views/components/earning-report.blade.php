<div class="col-xl-4 col-md-6 col-12 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
                <h5 class="m-0 me-2">Earning & Expense Reports</h5>
                <small class="text-muted">Total Earnings & Expense Overview</small>
            </div>
        </div>
        <div class="card-body pb-0">
            <ul class="p-0 m-0">
                <li class="d-flex mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i
                                    class='ti ti-chart-pie-2 ti-sm'></i></span>
                    </div>
                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                            <h6 class="mb-0">This Month Expense</h6>
                        </div>
                        <div class="user-progress d-flex align-items-center gap-3">
                            <small>{{getPriceFormat($thisMonthExpense)}}</small>
                        </div>
                    </div>
                </li>
                @if(Auth::user()->role_id === \App\Models\Role::SUPER_ADMIN)
                    <li class="d-flex mb-3">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class='ti ti-currency-dollar ti-sm'></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Total Subscription Earning</h6>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-3">
                                <small>{{getPriceFormat($totalSubscriptionAmount)}}</small>
                            </div>
                        </div>
                    </li>
                @endif
                <li class="d-flex mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-secondary"><i
                                    class='ti ti-credit-card ti-sm'></i></span>
                    </div>
                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                            <h6 class="mb-0">Total Expenses</h6>
                        </div>
                        <div class="user-progress d-flex align-items-center gap-3">
                            <small>{{getPriceFormat($totalExpense)}}</small>
                        </div>
                    </div>
                </li>
                <li class="d-flex mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning"><i
                                    class='ti ti-currency-dollar ti-sm'></i></span>
                    </div>
                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                            <h6 class="mb-0">Total Order Paid</h6>
                        </div>
                        <div class="user-progress d-flex align-items-center gap-3">
                            <small>{{getPriceFormat($orderPaidAmount)}}</small>
                        </div>
                    </div>
                </li>
            </ul>
            <div id="reportBarChart"></div>
        </div>
    </div>
</div>


