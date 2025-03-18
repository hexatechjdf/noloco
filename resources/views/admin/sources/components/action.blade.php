<div class="d-flex">
    <a class="btn btn-primary btn-sm m-1 add-source" data-id="{{ $source->id }}"><i class="bi bi-pencil"></i></a>
    <a class="btn btn-danger btn-sm  m-1" href="{{ route('admin.sources.delete', $source->id) }}"
        onclick="event.preventDefault(); deleteMsg('{{ route('admin.sources.delete', $source->id) }}')">
        <i class="bi bi-trash"></i>
    </a>
</div>
