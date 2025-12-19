@extends('layouts.app')

@section('content')

<div class="container">

    <form method="GET" action="{{ route('datasets.public') }}" class="mb-4" data-role="dataset-search-form">
        <div class="search-bar d-flex align-items-center mb-2">
            <select name="type" class="search-bar__category form-select border-0">
                <option value="" {{ empty($type) ? 'selected' : '' }}>Semua Dataset</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (string)($type ?? '') === (string)$category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <input
                type="text"
                name="q"
                value="{{ $q ?? '' }}"
                class="search-bar__input form-control border-0"
                placeholder="Cari dataset">

            <button class="search-bar__button btn" type="submit">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="d-flex flex-column flex-md-row gap-2 mt-2 align-items-stretch align-items-md-center">
            <div class="flex-grow-1 d-flex gap-2">
                <div class="flex-grow-1">
                    <label class="form-label mb-1 small text-muted">Tahun (opsional)</label>
                    <input type="number" name="year" class="form-control form-control-sm" placeholder="Mis. 2024" value="{{ $year ?? request('year') }}">
                </div>
                <div class="flex-grow-1">
                    <label class="form-label mb-1 small text-muted">Format File</label>
                    <select name="format" class="form-select form-select-sm">
                        <option value="" {{ empty($format) && !request('format') ? 'selected' : '' }}>Semua format</option>
                        <option value="csv" {{ ($format ?? request('format')) === 'csv' ? 'selected' : '' }}>CSV</option>
                        <option value="xlsx" {{ ($format ?? request('format')) === 'xlsx' ? 'selected' : '' }}>XLSX</option>
                        <option value="pdf" {{ ($format ?? request('format')) === 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="txt" {{ ($format ?? request('format')) === 'txt' ? 'selected' : '' }}>TXT</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label mb-1 small text-muted">Urutkan</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="latest" {{ ($sort ?? request('sort','latest')) === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ ($sort ?? request('sort')) === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="title_az" {{ ($sort ?? request('sort')) === 'title_az' ? 'selected' : '' }}>Judul A - Z</option>
                        <option value="title_za" {{ ($sort ?? request('sort')) === 'title_za' ? 'selected' : '' }}>Judul Z - A</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <h2 class="mb-4 fw-bold text-primary">Daftar Dataset</h2>

    <div id="dataset-alert" class="alert alert-info d-none"></div>

    <div id="dataset-list" class="row g-4">
        @foreach($datasets as $data)
        <div class="col-md-4">

            <div class="card dataset-card shadow-sm border-0">

                {{-- Gambar Dataset --}}
                @if($data->image)
                <img src="{{ asset('storage/'.$data->image) }}"
                     class="card-img-top dataset-image">
                @endif

                <div class="card-body">

                    {{-- Judul --}}
                    <h5 class="dataset-title">{{ $data->title }}</h5>

                    {{-- Deskripsi --}}
                    <p class="dataset-desc">{{ Str::limit($data->description, 90) }}</p>

                    {{-- Kategori & Tahun --}}
                    <div class="mb-3">
                        <span class="badge bg-primary">
                            {{ $data->category->name ?? 'Tidak ada kategori' }}
                        </span>

                        <span class="badge bg-secondary">
                            {{ $data->year }}
                        </span>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('datasets.detail', $data->id) }}"
                           class="btn btn-outline-secondary btn-sm">
                            Detail
                        </a>

                        <a href="{{ route('datasets.viewFile', $data->id) }}"
                           class="btn btn-outline-primary btn-sm">
                            Lihat File
                        </a>

                        <a href="{{ route('datasets.downloadFile', $data->id) }}"
                           class="btn btn-primary btn-sm">
                            Download
                        </a>
                    </div>

                </div>
            </div>

        </div>
        @endforeach
    </div>

    {{-- Pagination sederhana (akan diisi oleh JavaScript) --}}
    <nav class="mt-4">
        <ul id="dataset-pagination" class="pagination justify-content-center mb-0"></ul>
    </nav>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form        = document.querySelector('form[data-role="dataset-search-form"]');
    const select      = form.querySelector('select[name="type"]');
    const input       = form.querySelector('input[name="q"]');
    const list        = document.getElementById('dataset-list');
    const alertBox    = document.getElementById('dataset-alert');
    const pagination  = document.getElementById('dataset-pagination');
    const yearInput   = form.querySelector('input[name="year"]');
    const formatInput = form.querySelector('select[name="format"]');
    const sortInput   = form.querySelector('select[name="sort"]');

    const perPage = 6; // jumlah kartu per halaman
    let currentPage = 1;
    let currentData = [];

    function renderPage(page) {
        if (!currentData.length) return;

        const total = currentData.length;
        const lastPage = Math.ceil(total / perPage) || 1;

        if (page < 1) page = 1;
        if (page > lastPage) page = lastPage;
        currentPage = page;

        // render kartu dataset
        list.innerHTML = '';

        const start = (page - 1) * perPage;
        const end   = start + perPage;
        const slice = currentData.slice(start, end);

        slice.forEach(function (item) {
            const categoryName = item.category ? item.category.name : 'Tidak ada kategori';
            const year = item.year ?? '';
            const desc = (item.description || '').length > 90
                ? item.description.slice(0, 87) + '...'
                : (item.description || '');

            const imageHtml = item.image
                ? `<img src="/storage/${item.image}" class="card-img-top dataset-image">`
                : '';

            list.insertAdjacentHTML('beforeend', `
                <div class="col-md-4">
                    <div class="card dataset-card shadow-sm border-0">
                        ${imageHtml}
                        <div class="card-body">
                            <h5 class="dataset-title">${item.title}</h5>
                            <p class="dataset-desc">${desc}</p>
                            <div class="mb-3">
                                <span class="badge bg-primary">${categoryName}</span>
                                <span class="badge bg-secondary">${year}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="/data/${item.id}" class="btn btn-outline-secondary btn-sm">Detail</a>
                                <a href="/data/view/${item.id}" class="btn btn-outline-primary btn-sm">Lihat File</a>
                                <a href="/data/download/${item.id}" class="btn btn-primary btn-sm">Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        });

        // render pagination sederhana: 1 2 3 >
        pagination.innerHTML = '';

        const makePageButton = (label, pageNumber, disabled = false, active = false) => {
            const classes = ['page-item'];
            if (disabled) classes.push('disabled');
            if (active) classes.push('active');

            const li = document.createElement('li');
            li.className = classes.join(' ');

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'page-link border-0 bg-transparent';
            btn.textContent = label;

            if (!disabled) {
                btn.addEventListener('click', function () {
                    if (pageNumber !== currentPage) {
                        renderPage(pageNumber);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            }

            li.appendChild(btn);
            pagination.appendChild(li);
        };

        // nomor halaman (maksimal 5 tampilan sederhana)
        const maxButtons = 5;
        let startPage = Math.max(1, currentPage - 2);
        let endPage   = Math.min(lastPage, startPage + maxButtons - 1);
        if (endPage - startPage + 1 < maxButtons) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }

        for (let p = startPage; p <= endPage; p++) {
            makePageButton(String(p), p, false, p === currentPage);
        }

        // tombol berikutnya (>)
        makePageButton('›', currentPage + 1, currentPage >= lastPage, false);
    }

    async function loadDatasets() {
        const params = new URLSearchParams({
            type: select.value || '',
            q: input.value || '',
            year: yearInput.value || '',
            format: formatInput.value || '',
            sort: sortInput.value || 'latest'
        });

        const res  = await fetch(`/api/datasets?${params.toString()}`);
        const json = await res.json();

        currentData = json.data || [];
        list.innerHTML = '';

        if (!currentData.length) {
            alertBox.classList.remove('d-none');
            alertBox.innerHTML = input.value
                ? `Tidak ada dataset yang cocok dengan kata kunci <strong>${input.value}</strong>.`
                : 'Belum ada dataset yang dapat ditampilkan.';
            pagination.innerHTML = '';
            return;
        }

        alertBox.classList.add('d-none');
        renderPage(1);
    }

    // pertama kali load dari API (override render server jika JS aktif)
    loadDatasets();

    // submit manual (klik ikon search)
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        loadDatasets();
    });

    // ganti kategori otomatis reload dari API
    select.addEventListener('change', function (e) {
        e.preventDefault();
        loadDatasets();
    });
});
</script>
@endpush
