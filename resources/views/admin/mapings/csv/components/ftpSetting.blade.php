<!-- Accounts List Tab -->
<div class="tab-pane fade show active" id="accounts" role="tabpanel">

    <div class="d-flex justify-content-between mb-2">
      <h4>List of Accounts</h4>
      <button class="btn btn-warning add_account" data-csvid={{$idd}}>Add Account</button>
    </div>
    <hr>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ftpAccountsList">
            <!-- Dynamic accounts will be loaded here -->
            @foreach($accounts as $acc)
            <tr>
                <td>{{$acc->main_username}}</td>
                <td>{{@$acc->location_id}}</td>
                <td>
                    <button class="btn btn-sm btn-warning add_account" data-username={{$acc->username}} data-location={{$acc->location_id}} data-csvid="{{$idd}}" data-id="{{$acc->id}}">Edit</button>
                    <button class="btn btn-sm btn-danger remove_ftp" data-id="{{$acc->id}}">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Settings Tab -->

