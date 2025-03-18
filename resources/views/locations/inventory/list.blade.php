{{-- @foreach($contacts as $con)
    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="card contact-card shadow-sm border-0">
            <div class="card-body text-center">
                <h5 class="card-title mb-1">{{ucfirst($con['firstName']) . ' ' . ucfirst($con['lastName']) }}</h5>
                <p class="text-muted">{{ $con['email'] }}</p>
            </div>
        </div>
    </div>
@endforeach --}}


<table id="userTablee" class="table">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contacts as $con)
        <tr>
            <td>{{ucfirst(@$con['firstName'])}}</td>
            <td>{{ucfirst(@$con['lastName']) }}</td>
            <td>{{ @$con['email'] }}</td>
            <td>{{ @$con['phone'] }}</td>
            <td>
                @php($url = ghlRedurect($con['locationId'], $con['id']))
                <a class="btn btn-warning btn-sm" target="_blank" href="{{$url}}">Open in new tab</a>
                <a class="btn btn-primary btn-sm open_in_same_window" href="{{$url}}">Open in same window</a>
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
