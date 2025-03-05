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


<table id="userTable" class="table">
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
                <div class="dropdown">
                    <button class="btn btn-light p-0 border-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @php($url = ghlRedurect($con['locationId'], $con['id']))
                        <li><a class="dropdown-item" target="_blank" href="{{$url}}">Open in new tab</a></li>
                        <li><a class="dropdown-item open_in_same_window" href="{{$url}}">Open in same window</a></li>
                    </ul>
                </div>
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
