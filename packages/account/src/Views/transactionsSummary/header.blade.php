<div class="form-group">
    <div class="row">
        <div class="col-sm-3">
            <select name="account_select" id="account_select"
                    class="check-after-change form-control form-control-sm">
                <option value="0">-- {{ trans('global.account.choose_account') }} --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" {{ $account->id == $account_id ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
            <select name="month_select" id="month_select" class="check-after-change form-control form-control-sm">
                <option value="">-- {{ trans('global.account.choose_month') }} --</option>
                @foreach ($months as $key => $value)
                    <option value="{{ $value }}" {{ $selectedMonth === $value ? 'selected' : '' }}>{{ $key }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>