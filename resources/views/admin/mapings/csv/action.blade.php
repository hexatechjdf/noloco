
<div>
    <a class="btn btn-primary btn-sm m-1 manage_accounts" data-id="{{$item->id}}" href="javascript:;"><i class="bi bi-plus"></i>Account</a>
    {{-- <a class="btn btn-warning btn-sm m-1 set_location " data-id="{{$item->id}}" href="javascript:;"><i class="bi bi-plus"></i>Location</a> --}}
    <a class="btn btn-warning btn-sm m-1 " href="{{route('admin.mappings.csv.manage',$item->id)}}"><i class="bi bi-gear"></i>Manage</a>
    <a class="btn btn-secondary btn-sm m-1 " href="{{route('admin.mappings.csv.create',$item->id)}}"><i class="bi bi-pencil-square"></i>Edit</a>
</div>
