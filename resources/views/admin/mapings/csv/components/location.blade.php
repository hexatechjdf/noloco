<form method="POST" class="submitForm" action="{{ route('admin.mappings.csv.location.store') }}">
    @csrf
    <input type="hidden" class="csv_id" name="csv_id" value="{{$request->csv_id}}" >
    <div class="mb-3">
        <label  class="form-label">Select Account</label>
        <div class="">
            <select class="form-select" name="account_id" required>
                @foreach($accounts as $acc)
                <option value="{{$acc->id}}">{{$acc->username}} _ {{$acc->domain}}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="mb-3">
        <label for="login" class="form-label">Location id</label>
        <div class="input-group">
            <input type="text" required class="form-control" name="location_id"value="">
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary w-100">Set Location</button>
</form>
