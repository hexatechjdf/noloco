<div class="mt-3">
    <div class="card">
        <div class="card-body">
            @if(count($data) > 0)
            <div class="card-title">
        <h5>Open Leads</h5>
            </div>
            <hr>
            <table class="table ">
        <thead>
          <tr>
            <th scope="col">Id</th>
            <th scope="col">Name</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
              <tr>
                <th scope="row">{{@$d['id']}}</th>
                <td>{{@$d['name']}}</td>
                <td>
                    @php($url = ghlRedurect($d['locationId'], $d['id'],'oppertunity'))
                    <a class="btn btn-warning btn-sm" target="_blank" href="{{$url}}">Open in new tab</a>
                    <a class="btn btn-primary btn-sm open_in_same_window" href="{{$url}}">Open in same window</a>
                </td>
              </tr>
            @endforeach
        </tbody>
      </table>

            @else
            <div class="row">
            <div class="col-md-12 text-center">
                <p class="text-center">No opportunity found for this contact</p>
                {{-- <button class="btn btn-primary create_opportunity">Create Opportunity</button> --}}
            </div>
            </div>
            @endif
        </div>
    </div>
</div>
