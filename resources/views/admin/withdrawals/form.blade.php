<form class="form form-horizontal form-enterkey" method="POST" action="{{ $action }}" autocomplete="off">
    {{ method_field('PUT') }}
    @csrf

    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">Withdrawal method</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ strtoupper($model->method) }}" readonly disabled class="form-control">           
        </div>
    </div>

    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">User name</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ $model->user->name }}" readonly disabled class="form-control">           
        </div>
    </div>

    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">User email</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ $model->user->email }}" readonly disabled class="form-control">           
        </div>
    </div>

    <hr />

    <h4>Withdrawal data:</h4>

    @if(strtolower($model->method)=='paypal')
    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">Paypal email</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ $model->method_data['email'] }}" readonly disabled class="form-control">           
        </div>
    </div>
    @else
    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">Name</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ $model->method_data['name'] }}" readonly disabled class="form-control">           
        </div>
    </div>
    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">Address</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ $model->method_data['address'] }}" readonly disabled class="form-control">           
        </div>
    </div>
    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">City</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ $model->method_data['city'] }}" readonly disabled class="form-control">           
        </div>
    </div>
    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">State</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ App\Models\State::find($model->method_data['state'])->name }}" readonly disabled class="form-control">           
        </div>
    </div>
    <div class="form-group row">
        <label for="method" class="col-12 col-md-2 col-form-label">Postal code</label>
        <div class="col-12 col-md-10">
            <input id="method" type="text"  value="{{ $model->method_data['postal_code'] }}" readonly disabled class="form-control">           
        </div>
    </div>
    @endif

    <hr/>
    <div class="form-group row">
        <label for="status" class="col-12 col-md-2 col-form-label">Status</label>
        <div class="col-12 col-md-10">
            <select name="status" id="status" required class="form-control">
                @foreach(App\Models\Withdrawal::STATUSES as $v=>$l)
                <option @if($model->status==$v) selected @endif value="{{ $v }}">{{ $l }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="form-actions">
        <div class="col-sm-10 pull-right">
            <a href='{{ route('admin.withdrawals.index') }}' class="btn btn-default pull-left"><i class='fa fa-reply'></i> Cancel </a>

            <button type='submit' class="btn btn-success pull-left btn-delete" style="margin-left: 40px"><i class='fa fa-save'></i> Save changes</button>

        </div>
    </div>
</form>