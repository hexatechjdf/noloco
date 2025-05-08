<!-- Accounts List Tab -->
<div class="tab-pane fade show active" id="accounts" role="tabpanel">

    <div class="d-flex justify-content-between mb-2">
      <h4>List of Accounts</h4>
      <a class="btn btn-warning " href="{{ route('admin.mappings.csv.ftp.form',[$request->csv_id, null]) }}">Add Account</a>
    </div>
    <hr>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ftpAccountsList">
            @foreach($accounts as $acc)
            <tr>
                <td>{{$acc->username}}</td>
                <td>
                    <a class="btn btn-sm btn-warning " href="{{ route('admin.mappings.csv.ftp.form',[$acc->mapping_id,$acc->id]) }}">Edit</a>
                    <button class="btn btn-sm btn-danger remove_ftp" data-id="{{$acc->id}}">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Settings Tab -->

