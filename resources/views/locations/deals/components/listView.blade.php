<div class="mt-3">
@if(count($deals) > 0)
<table class="table ">
    <thead>
      <tr>
        <th scope="col">Id</th>
        <th scope="col">UUIID</th>
        <th scope="col">Status</th>
        <th scope="col">Type</th>
        <th scope="col">Action</th>
      </tr>
    </thead>
    <tbody>
        @foreach($deals as $deal)
          <tr>
            <th scope="row">{{@$deal['id']}}</th>
            <td>{{@$deal['uuid']}}</td>
            <td>{{@$deal['status']}}</td>
            <td>{{@$deal['type']}}</td>
            <td>
                <a class="btn btn-warning" href="https://app2.starautocrm.com/deals/view/{{$deal['uuid']}}/deal-structure" target="_blank">Manage</a>
            </td>
          </tr>
        @endforeach

    </tbody>
  </table>

@else
<div class="row">
<div class="col-md-12 text-center">
    <p class="text-center">No deals found for this contact</p>
</div>
</div>
@endif
</div>

