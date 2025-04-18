<div class="d-flex">
    <a class="btn btn-primary btn-sm m-1 add-script" data-id="{{ $item->id }}"><i class="bi bi-pencil"></i></a>
    <a class="btn btn-danger btn-sm  m-1" href="{{ route('admin.dropdown.matchables.delete', $item->id) }}"
        onclick="event.preventDefault(); deleteMsg('{{ route('admin.dropdown.matchables.delete', $item->id) }}')"><i
            class="bi bi-trash"></i></a>
</div>
