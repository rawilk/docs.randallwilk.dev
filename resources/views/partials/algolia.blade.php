@once
@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/docsearch.js@2/dist/cdn/docsearch.min.css">
@endpush
@endonce

<div class="relative rounded-md shadow-sm mb-4">
    <input type="search"
           class="algolia-search form-input w-full"
           placeholder="Search docs"
           id="{{ $elementId }}"
    >
</div>

@once
@push('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/docsearch.js@2/dist/cdn/docsearch.min.js"></script>
@endpush
@endonce

@push('js')
<script type="text/javascript">
    docsearch({
        apiKey: '{{ config('services.algolia.key') }}',
        indexName: '{{ config('services.algolia.index') }}',
        inputSelector: '#{{ $elementId }}',
        algoliaOptions: {
            hitsPerPage: 5,
            facetFilters: ['project:{{ $repository->name }}', 'version:{{ $version }}']
        },
    });
</script>
@endpush
