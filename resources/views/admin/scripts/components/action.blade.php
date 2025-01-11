<div class="d-flex">
    <a class="btn btn-primary btn-sm m-1 add-script" data-id="{{ $script->id }}"><i class="bi bi-pencil"></i></a>
    <a class="btn btn-danger btn-sm  m-1" href="{{ route('admin.scripts.delete', $script->id) }}"
        onclick="event.preventDefault(); deleteMsg('{{ route('admin.scripts.delete', $script->id) }}')"><i
            class="bi bi-trash"></i></a>

</div>
