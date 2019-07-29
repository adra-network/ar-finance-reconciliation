@extends('layouts.admin')
@section('content')

    <div class="row">
        <div class="col">

            <form id="parametersForm" action="{{ route('phone.transactions.index') }}" METHOD="GET">
                <div class="row">
                    <div class="col-2">
                        <div class="form-group">
                            <label>Phone number</label>
                            <select name="numberFilter" class="form-control">
                                <option value="">All numbers</option>
                                @foreach($numbers as $number)
                                    <option value="{{ $number->phone_number }}" {{ $number->phone_number === $params->numberFilter ? 'selected' : null }}>
                                        {{ $number->phone_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div>
                                <label>Transaction date</label>
                                <span class="clear-datepicker pull-right">
                                    clear
                                </span>
                            </div>

                            <input type="text" name="dateFilter"
                                   value="{{ implode(' - ', $params->getDateFilterStrings() ?? []) }}"
                                   class="form-control" placeholder="All dates"/>

                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Group by</label>
                            <br>
                            <input type="radio" name="groupBy"
                                   value="phone_number" {{ $params->groupBy === 'phone_number' ? 'checked' : null }}>
                            Phone number
                            | <input type="radio" name="groupBy"
                                     value="date" {{ $params->groupBy === 'date' ? 'checked' : null }}> Date
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label>Show $0 values</label>
                            <br>
                            <input class="show-zero-charges" name="showZeroCharges"
                                   type="checkbox" {{ request()->query('showZeroCharges', false) ? 'checked' : null }}>
                        </div>
                    </div>
                </div>
                @include('phone::transactions._pagination')
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col">

            <table class="table table-striped">

                <thead>
                <tr>
                    <th>{{ str_replace('_', ' ', ucfirst($params->groupBy)) }}</th>
                    <th>{{ str_replace('_', ' ', ucfirst($params->groupByInverse)) }}</th>
                    <th>Min</th>
                    <th>Cost</th>
                    <th>Charge to</th>
                    <th>Comments</th>
                </tr>
                </thead>

                @foreach($groups as $group)

                    <tr>
                        <td>{{ $group->groupKey }}</td>
                        <td colspan="5"></td>
                    </tr>

                    @foreach($group->getTransactions() as $transaction)

                        <tr>
                            <td></td>
                            <td>{{ $transaction[$params->groupByInverse] }}</td>
                            <td>{{ $transaction->minutes_used }}</td>
                            <td>{{ number_format($transaction->total_charges, 2) }}</td>
                            <td></td>
                            <td></td>
                        </tr>

                    @endforeach

                @endforeach

            </table>

        </div>
    </div>

@endsection
@section('scripts')
    @parent

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <script>
        $(document).ready(function () {
            let $numberFilter = $('select[name="numberFilter"]')
            let $groupBy = $('input[name="groupBy"]')
            let $parametersForm = $('#parametersForm')
            let $dateFilter = $('input[name="dateFilter"]')
            let $clearPickerButton = $('.clear-datepicker')
            let $showZeroCharges = $('input[name="showZeroCharges"]')

            const DATE_FORMAT = '{{ \Phone\DTO\TransactionListParameters::DATE_FORMAT_JS }}';
            $dateFilter.daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    format: DATE_FORMAT
                }
            });

            $dateFilter.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format(DATE_FORMAT) + ' - ' + picker.endDate.format(DATE_FORMAT));
                $parametersForm.submit()
            });


            $numberFilter.on('change', function () {
                $parametersForm.submit()
            })
            $groupBy.on('change', function () {
                $parametersForm.submit()
            })
            $dateFilter.on('change', function () {
                $parametersForm.submit()
            })
            $clearPickerButton.on('click', function () {
                $dateFilter.val(null)
                $parametersForm.submit()
            })
            $showZeroCharges.on('click', function () {
                $parametersForm.submit()
            })
        })
    </script>

    <style>
        .clear-datepicker {
            cursor: pointer;
            color: indianred;
        }

        .clear-datepicker:hover {
            text-decoration: underline;
        }
    </style>

@endsection