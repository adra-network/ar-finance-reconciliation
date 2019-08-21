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
                                <option value="">
                                    @if(auth()->user()->isAdmin() && !$params->numberFilter)
                                        Choose number
                                    @else
                                        All numbers
                                    @endif
                                </option>
                                @foreach($numbers as $number)
                                    <option value="{{ $number->phone_number }}" {{ $number->phone_number === $params->numberFilter ? 'selected' : null }}>
                                        {{ $number->phone_number }} @if(auth()->user()->isAdmin())({{ data_get($number, 'user.name', 'unassigned') }})@endif
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
                    <th>&nbsp;</th>
                </tr>
                </thead>

                @forelse($groups as $group)

                    <tr>
                        <td>
                            @if($params->groupBy === \Phone\DTO\TransactionListParameters::GROUP_BY_DATE)
                                {{ $group->groupKey }}
                            @else
                                <phone-transaction-button :view="'phone-transaction'"
                                                          :caller_phone_number="{{ json_encode($group->getTransactions()->first()->only('caller_phone_number_id', 'phone_number')) }}"></phone-transaction-button>
                            @endif
                        </td>
                        <td colspan="6"></td>
                    </tr>

                    @foreach($group->getTransactions() as $transaction)

                        <tr>
                            <td></td>
                            <td>{{ $transaction->getFieldByGrouping($params->groupByInverse) }}</td>
                            <td>{{ $transaction->minutes_used }}</td>
                            <td>{{ number_format($transaction->total_charges, 2) }}</td>
                            <td>{{ data_get($transaction->allocatedTo, 'charge_to', null) }}</td>
                            <td>{{ $transaction->comment }}</td>
                            <td>
                                <phone-transaction-button :view="'phone-transaction'" :transaction_id="{{ $transaction->id }}"></phone-transaction-button>
                            </td>
                        </tr>

                    @endforeach

                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            No transactions found by this filter
                        </td>
                    </tr>
                @endforelse
            </table>

        </div>
    </div>
    <phone-transaction-modal ref="PhoneTransactionModal"></phone-transaction-modal>

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
