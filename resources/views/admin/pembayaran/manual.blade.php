@extends('layouts.admin')

@section('title', 'Pembayaran Manual')

@section('page-title', 'Pembayaran Manual')

@push('styles')
    @vite('resources/css/admin/pembayaran.css')

@endpush

@section('content')

<div class="pembayaran-container">

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div class="pembayaran-header pembayaran-manual-header">

        <div class="pembayaran-header-content">

            <div class="pembayaran-header-icon">
                <i data-lucide="hand-coins"></i>
            </div>

            <div>
                <h2>
                    Pembayaran Manual
                </h2>

                <p>
                    Catat pembayaran tunai yang diterima langsung oleh admin.
                </p>
            </div>

        </div>

        <a
            href="{{ route('admin.pembayaran.index') }}"
            class="btn-kembali"
        >
            <i data-lucide="arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- =====================================================
         ALERT SUCCESS
    ====================================================== --}}
    @if(session('success'))

        <div class="pembayaran-alert pembayaran-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         ALERT ERROR
    ====================================================== --}}
    @if(session('error'))

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="circle-alert"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- =====================================================
         VALIDATION ERROR
    ====================================================== --}}
    @if($errors->any())

        <div class="pembayaran-alert pembayaran-alert-error">

            <i data-lucide="triangle-alert"></i>

            <div>

                <strong>
                    Terdapat kesalahan:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =====================================================
         FORM CARD
    ====================================================== --}}
    <div class="pembayaran-card manual-card">


        {{-- =================================================
             CARD HEADER
        ================================================== --}}
        <div class="manual-title">

            <div class="manual-icon">
                <i data-lucide="banknote"></i>
            </div>

            <div>

                <h3>
                    Pembayaran Cash
                </h3>

                <p>
                    Masukkan data pembayaran yang diterima secara langsung.
                </p>

            </div>

        </div>


        {{-- =================================================
             FORM
        ================================================== --}}
        <form
            action="{{ route('admin.pembayaran.manual.store') }}"
            method="POST"
            id="formManualPembayaran"
        >

            @csrf


            {{-- =================================================
                 SISWA / TAGIHAN
            ================================================== --}}
            <div class="form-group">

                <label for="manual_search">

                    <i data-lucide="user-round"></i>

                    Siswa / Tagihan

                    <span class="required-mark">
                        *
                    </span>

                </label>


                <div class="manual-search-wrapper">

                    <div class="manual-search-input-wrapper">

                        <input
                            type="text"
                            id="manual_search"
                            class="manual-search-input"
                            placeholder="Cari nama siswa, NIS, kategori, atau bulan..."
                            autocomplete="off"
                        >

                        <button
                            type="button"
                            id="manualSearchClear"
                            class="manual-search-clear"
                            aria-label="Hapus pencarian"
                        >
                            <i data-lucide="x"></i>
                        </button>

                        <span class="manual-search-icon">
                            <i data-lucide="search"></i>
                        </span>

                    </div>


                    <div
                        class="manual-search-result"
                        id="manualSearchResult"
                    >

                        <div class="manual-search-count">
                            Ketik untuk mencari siswa atau tagihan
                        </div>

                        <div id="manualSearchItems"></div>

                    </div>

                </div>


                {{-- =================================================
                     SELECT ASLI UNTUK FORM
                ================================================== --}}
                <select
                    name="tagihan_id"
                    id="tagihan_id"
                    class="manual-hidden-select"
                    required
                >

                    <option value="">
                        -- Pilih Siswa dan Tagihan --
                    </option>

                    @foreach($tagihan as $item)

                        @php

                            $sudahDibayar = $item->pembayaran
                                ->whereIn('status', [
                                    'dibayar',
                                    'disetujui'
                                ])
                                ->sum('nominal');

                            $sisa = max(
                                (float) $item->nominal -
                                (float) $sudahDibayar,
                                0
                            );

                            $namaKategori = strtolower(
                                trim($item->kategori->nama ?? '')
                            );

                            $isSpp = $namaKategori === 'spp';


                            /*
                             * Total sisa SPP mulai dari
                             * bulan yang dipilih sampai
                             * bulan terakhir.
                             */
                            $totalSisaSpp = $sisa;

                            if ($isSpp) {

                                $totalSisaSpp = $tagihan
                                    ->filter(function ($spp) use ($item) {

                                        $kategoriSpp = strtolower(
                                            trim(
                                                $spp->kategori->nama ?? ''
                                            )
                                        );

                                        if ($kategoriSpp !== 'spp') {
                                            return false;
                                        }

                                        if (
                                            (int) $spp->siswa_id !==
                                            (int) $item->siswa_id
                                        ) {
                                            return false;
                                        }

                                        if (
                                            (int) $spp->tahun_ajaran_id !==
                                            (int) $item->tahun_ajaran_id
                                        ) {
                                            return false;
                                        }

                                        if (
                                            !$item->tahun ||
                                            !$item->bulan
                                        ) {
                                            return false;
                                        }

                                        if (
                                            !$spp->tahun ||
                                            !$spp->bulan
                                        ) {
                                            return false;
                                        }

                                        if (
                                            (int) $spp->tahun >
                                            (int) $item->tahun
                                        ) {
                                            return true;
                                        }

                                        if (
                                            (int) $spp->tahun ===
                                            (int) $item->tahun &&
                                            (int) $spp->bulan >=
                                            (int) $item->bulan
                                        ) {
                                            return true;
                                        }

                                        return false;

                                    })
                                    ->sum(function ($spp) {

                                        $dibayar = $spp->pembayaran
                                            ->whereIn('status', [
                                                'dibayar',
                                                'disetujui'
                                            ])
                                            ->sum('nominal');

                                        return max(
                                            (float) $spp->nominal -
                                            (float) $dibayar,
                                            0
                                        );

                                    });

                            }


                            $namaBulan = '';

                            if (
                                $isSpp &&
                                $item->bulan &&
                                $item->tahun
                            ) {

                                $namaBulan =
                                    \Carbon\Carbon::create(
                                        $item->tahun,
                                        $item->bulan,
                                        1
                                    )->translatedFormat('F');

                            }


                            $namaSiswa =
                                $item->siswa->nama ?? '-';

                            $nis =
                                $item->siswa->nis ?? '';

                            $kategori =
                                $item->kategori->nama ?? '-';

                            $tahunAjaran =
                                $item->tahunAjaran->nama ?? '-';


                            /*
                             * String pencarian.
                             */
                            $searchText = strtolower(
                                $namaSiswa . ' ' .
                                $nis . ' ' .
                                $kategori . ' ' .
                                $tahunAjaran . ' ' .
                                $namaBulan . ' ' .
                                ($item->tahun ?? '')
                            );

                        @endphp


                        @if($sisa > 0)

                            <option
                                value="{{ $item->id }}"
                                data-sisa="{{ $sisa }}"
                                data-total-sisa="{{ $isSpp ? $totalSisaSpp : $sisa }}"
                                data-nominal="{{ $item->nominal }}"
                                data-is-spp="{{ $isSpp ? '1' : '0' }}"
                                data-bulan="{{ $namaBulan }}"
                                data-tahun="{{ $item->tahun ?? '' }}"
                                data-siswa="{{ $namaSiswa }}"
                                data-nis="{{ $nis }}"
                                data-kategori="{{ $kategori }}"
                                data-tahun-ajaran="{{ $tahunAjaran }}"
                                data-search="{{ $searchText }}"
                                {{ old('tagihan_id') == $item->id ? 'selected' : '' }}
                            >

                                {{ $namaSiswa }}
                                -
                                {{ $kategori }}

                                @if($isSpp && $namaBulan)
                                    -
                                    {{ $namaBulan }}
                                    {{ $item->tahun }}
                                @endif

                            </option>

                        @endif

                    @endforeach

                </select>


                {{-- =================================================
                     TAGIHAN YANG DIPILIH
                ================================================== --}}
                <div
                    class="manual-selected-tagihan"
                    id="selectedTagihan"
                >

                    <strong id="selectedTagihanTitle">
                        Tagihan
                    </strong>

                    <span id="selectedTagihanDetail">
                        -
                    </span>

                </div>


                <small class="form-help">

                    Cari berdasarkan nama siswa, NIS, kategori,
                    tahun ajaran, atau bulan SPP.

                </small>

            </div>


            {{-- =================================================
                 INFORMASI TAGIHAN TERPILIH
            ================================================== --}}
            <div
                class="manual-info"
                id="infoTagihan"
                style="display: none;"
            >

                <div class="manual-info-icon">

                    <i data-lucide="calendar-days"></i>

                </div>

                <div>

                    <strong id="infoTagihanJudul">
                        Tagihan Terpilih
                    </strong>

                    <p id="infoTagihanText">
                        -
                    </p>

                </div>

            </div>


            {{-- =================================================
                 NOMINAL
            ================================================== --}}
            <div class="form-group">

                <label for="nominal">

                    <i data-lucide="wallet"></i>

                    Nominal Pembayaran

                    <span class="required-mark">
                        *
                    </span>

                </label>


                <div class="manual-nominal-wrapper">

                    <span class="manual-rupiah">
                        Rp
                    </span>

                    <input
                        type="number"
                        name="nominal"
                        id="nominal"
                        value="{{ old('nominal') }}"
                        min="1"
                        step="1"
                        placeholder="Masukkan nominal pembayaran"
                        required
                    >

                </div>


                <small
                    class="form-help"
                    id="nominalHelp"
                >
                    Pilih tagihan terlebih dahulu.
                </small>


                <div
                    class="manual-limit-info"
                    id="manualLimitInfo"
                    style="display: none;"
                >
                    Maksimal pembayaran:
                    <strong id="manualLimitValue">
                        Rp 0
                    </strong>
                </div>

            </div>


            {{-- =================================================
                 METODE PEMBAYARAN
            ================================================== --}}
            <div class="form-group">

                <label for="metode">

                    <i data-lucide="credit-card"></i>

                    Metode Pembayaran

                </label>


                <div class="manual-method-wrapper">

                    <input
                        type="text"
                        id="metode"
                        value="Cash"
                        readonly
                        class="input-readonly"
                    >

                    <span class="manual-method-icon">

                        <i data-lucide="banknote"></i>

                    </span>

                </div>


                <small class="form-help">

                    Pembayaran manual dicatat sebagai pembayaran cash.

                </small>

            </div>


            {{-- =================================================
                 TANGGAL PEMBAYARAN
            ================================================== --}}
            <div class="form-group">

                <label for="tanggal_kirim">

                    <i data-lucide="calendar-days"></i>

                    Tanggal Pembayaran

                    <span class="required-mark">
                        *
                    </span>

                </label>


                <input
                    type="date"
                    name="tanggal_kirim"
                    id="tanggal_kirim"
                    value="{{ old('tanggal_kirim', date('Y-m-d')) }}"
                    required
                >


                <small class="form-help">

                    Tentukan tanggal pembayaran diterima oleh admin.

                </small>

            </div>


            {{-- =================================================
                 INFORMASI PEMBAYARAN
            ================================================== --}}
            <div class="manual-info">

                <div class="manual-info-icon">

                    <i data-lucide="info"></i>

                </div>

                <div>

                    <strong>
                        Informasi Pembayaran Manual
                    </strong>

                    <p>

                        Pembayaran cash akan langsung dicatat sebagai
                        <strong>dibayar</strong>
                        dan tidak memerlukan proses persetujuan admin.

                        Untuk SPP, pilih bulan awal pembayaran.
                        Nominal dapat digunakan untuk melunasi bulan
                        tersebut dan bulan berikutnya secara berurutan.

                    </p>

                </div>

            </div>


            {{-- =================================================
                 ACTION
            ================================================== --}}
            <div class="form-actions">

                <a
                    href="{{ route('admin.pembayaran.index') }}"
                    class="btn-batal"
                >

                    <i data-lucide="x"></i>

                    Batal

                </a>


                <button
                    type="submit"
                    class="btn-simpan"
                >

                    <i data-lucide="save"></i>

                    Simpan Pembayaran

                </button>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     JAVASCRIPT
========================================================= --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }


    const selectTagihan =
        document.getElementById('tagihan_id');

    const searchInput =
        document.getElementById('manual_search');

    const searchResult =
        document.getElementById('manualSearchResult');

    const searchItems =
        document.getElementById('manualSearchItems');

    const searchClear =
        document.getElementById('manualSearchClear');

    const inputNominal =
        document.getElementById('nominal');

    const nominalHelp =
        document.getElementById('nominalHelp');

    const infoTagihan =
        document.getElementById('infoTagihan');

    const infoTagihanJudul =
        document.getElementById('infoTagihanJudul');

    const infoTagihanText =
        document.getElementById('infoTagihanText');

    const selectedTagihan =
        document.getElementById('selectedTagihan');

    const selectedTagihanTitle =
        document.getElementById('selectedTagihanTitle');

    const selectedTagihanDetail =
        document.getElementById('selectedTagihanDetail');

    const manualLimitInfo =
        document.getElementById('manualLimitInfo');

    const manualLimitValue =
        document.getElementById('manualLimitValue');


    function formatRupiah(angka) {

        return new Intl.NumberFormat('id-ID').format(
            Number(angka || 0)
        );

    }


    function getOptions() {

        return Array.from(
            selectTagihan.options
        ).filter(function (option) {

            return option.value !== '';

        });

    }


    function renderSearchResults(keyword = '') {

        const keywordLower =
            keyword
                .toLowerCase()
                .trim();


        const options =
            getOptions();


        const filtered =
            options.filter(function (option) {

                const searchText =
                    option.dataset.search || '';

                return searchText.includes(
                    keywordLower
                );

            });


        searchItems.innerHTML = '';


        const countText =
            searchResult.querySelector(
                '.manual-search-count'
            );


        if (keywordLower === '') {

            countText.textContent =
                'Pilih salah satu tagihan di bawah ini.';

        } else {

            countText.textContent =
                filtered.length +
                ' tagihan ditemukan.';

        }


        if (filtered.length === 0) {

            searchItems.innerHTML = `
                <div class="manual-search-empty">
                    Tidak ada siswa atau tagihan yang ditemukan.
                </div>
            `;

            return;

        }


        filtered.forEach(function (option) {

            const item =
                document.createElement('button');

            item.type = 'button';

            item.className =
                'manual-search-item';


            const siswa =
                option.dataset.siswa || '-';

            const nis =
                option.dataset.nis || '';

            const kategori =
                option.dataset.kategori || '-';

            const tahunAjaran =
                option.dataset.tahunAjaran || '-';

            const bulan =
                option.dataset.bulan || '';

            const tahun =
                option.dataset.tahun || '';

            const isSpp =
                option.dataset.isSpp === '1';

            const totalSisa =
                Number(
                    option.dataset.totalSisa || 0
                );


            let detail =
                kategori +
                ' • ' +
                tahunAjaran;


            if (nis) {

                detail =
                    'NIS ' +
                    nis +
                    ' • ' +
                    detail;

            }


            if (isSpp && bulan) {

                detail +=
                    ' • SPP ' +
                    bulan +
                    ' ' +
                    tahun;

            }


            let sisaText =
                'Sisa bulan ini: Rp ' +
                formatRupiah(
                    Number(
                        option.dataset.sisa || 0
                    )
                );


            if (isSpp) {

                sisaText =
                    'Sisa SPP mulai ' +
                    bulan +
                    ' ' +
                    tahun +
                    ': Rp ' +
                    formatRupiah(
                        totalSisa
                    );

            }


            item.innerHTML = `

                <span class="manual-search-item-name">
                    ${escapeHtml(siswa)}
                </span>

                <span class="manual-search-item-detail">
                    ${escapeHtml(detail)}
                </span>

                <span class="manual-search-item-sisa">
                    ${escapeHtml(sisaText)}
                </span>

            `;


            item.addEventListener(
                'click',
                function () {

                    selectTagihan.value =
                        option.value;

                    searchInput.value =
                        siswa +
                        ' - ' +
                        kategori +
                        (
                            isSpp && bulan
                                ? ' - ' +
                                  bulan +
                                  ' ' +
                                  tahun
                                : ''
                        );


                    searchResult.classList.remove(
                        'active'
                    );

                    searchClear.classList.add(
                        'active'
                    );


                    updateTagihanInfo();

                }
            );


            searchItems.appendChild(item);

        });

    }


    function escapeHtml(text) {

        const div =
            document.createElement('div');

        div.textContent =
            text ?? '';

        return div.innerHTML;

    }


    function openSearch() {

        renderSearchResults(
            searchInput.value
        );

        searchResult.classList.add(
            'active'
        );

    }


    function closeSearch() {

        searchResult.classList.remove(
            'active'
        );

    }


    function updateTagihanInfo() {

        const option =
            selectTagihan.options[
                selectTagihan.selectedIndex
            ];


        if (!option || !option.value) {

            inputNominal.removeAttribute(
                'max'
            );

            inputNominal.value = '';

            nominalHelp.textContent =
                'Pilih tagihan terlebih dahulu.';

            infoTagihan.style.display =
                'none';

            selectedTagihan.classList.remove(
                'active'
            );

            manualLimitInfo.style.display =
                'none';

            return;

        }


        const sisa =
            Number(
                option.dataset.sisa || 0
            );


        const totalSisa =
            Number(
                option.dataset.totalSisa || sisa
            );


        const nominal =
            Number(
                option.dataset.nominal || 0
            );


        const isSpp =
            option.dataset.isSpp === '1';


        const bulan =
            option.dataset.bulan || '';


        const tahun =
            option.dataset.tahun || '';


        const siswa =
            option.dataset.siswa || '-';


        const kategori =
            option.dataset.kategori || '-';


        /*
         * Untuk SPP:
         *
         * batas pembayaran =
         * total sisa dari bulan yang dipilih
         * sampai bulan terakhir.
         *
         * Contoh:
         *
         * Juli       112.500
         * Agustus    112.500
         * September  112.500
         *
         * Jika pilih Juli:
         *
         * max = 337.500
         */

        const batasPembayaran =
            isSpp
                ? totalSisa
                : sisa;


        inputNominal.max =
            Math.floor(batasPembayaran);


        /*
         * Jangan otomatis mengubah nominal
         * jika user sudah mengetik.
         *
         * Validasi akhir tetap dilakukan
         * sebelum submit.
         */


        if (isSpp) {

            infoTagihan.style.display =
                'flex';


            infoTagihanJudul.textContent =
                'SPP Mulai ' +
                bulan +
                ' ' +
                tahun;


            infoTagihanText.innerHTML =

                'Nominal bulan ini: ' +

                '<strong>Rp ' +

                formatRupiah(nominal) +

                '</strong>. ' +

                'Total sisa SPP mulai bulan ini: ' +

                '<strong>Rp ' +

                formatRupiah(totalSisa) +

                '</strong>.';


            nominalHelp.innerHTML =

                'Pembayaran dapat dialokasikan mulai ' +

                '<strong>' +

                bulan +

                ' ' +

                tahun +

                '</strong> ' +

                'ke bulan berikutnya.';


            manualLimitInfo.style.display =
                'block';


            manualLimitValue.textContent =
                'Rp ' +
                formatRupiah(
                    batasPembayaran
                );


            selectedTagihan.classList.add(
                'active'
            );


            selectedTagihanTitle.textContent =
                siswa +
                ' - SPP ' +
                bulan +
                ' ' +
                tahun;


            selectedTagihanDetail.textContent =
                'Total sisa mulai bulan ini: Rp ' +
                formatRupiah(
                    totalSisa
                );

        } else {

            infoTagihan.style.display =
                'flex';


            infoTagihanJudul.textContent =
                kategori;


            infoTagihanText.innerHTML =

                'Sisa tagihan yang dapat dibayar: ' +

                '<strong>Rp ' +

                formatRupiah(sisa) +

                '</strong>.';


            nominalHelp.innerHTML =

                'Maksimal pembayaran: ' +

                '<strong>Rp ' +

                formatRupiah(sisa) +

                '</strong>';


            manualLimitInfo.style.display =
                'block';


            manualLimitValue.textContent =
                'Rp ' +
                formatRupiah(sisa);


            selectedTagihan.classList.add(
                'active'
            );


            selectedTagihanTitle.textContent =
                siswa +
                ' - ' +
                kategori;


            selectedTagihanDetail.textContent =
                'Sisa tagihan: Rp ' +
                formatRupiah(
                    sisa
                );

        }

    }


    searchInput.addEventListener(
        'focus',
        function () {

            openSearch();

        }
    );


    searchInput.addEventListener(
        'input',
        function () {

            const value =
                searchInput.value;

            searchClear.classList.toggle(
                'active',
                value.length > 0
            );


            /*
             * Ketika user mengetik ulang,
             * pilihan sebelumnya dibatalkan
             * supaya tidak salah tagihan.
             */

            selectTagihan.value = '';

            updateTagihanInfo();

            openSearch();

        }
    );


    searchClear.addEventListener(
        'click',
        function () {

            searchInput.value = '';

            selectTagihan.value = '';

            searchClear.classList.remove(
                'active'
            );

            updateTagihanInfo();

            openSearch();

            searchInput.focus();

        }
    );


    inputNominal.addEventListener(
        'input',
        function () {

            const max =
                Number(
                    inputNominal.getAttribute(
                        'max'
                    ) || 0
                );


            const value =
                Number(
                    inputNominal.value || 0
                );


            if (
                max > 0 &&
                value > max
            ) {

                inputNominal.value =
                    max;

            }

        }
    );


    document.addEventListener(
        'click',
        function (event) {

            if (
                !event.target.closest(
                    '.manual-search-wrapper'
                )
            ) {

                closeSearch();

            }

        }
    );


    document
        .getElementById(
            'formManualPembayaran'
        )
        .addEventListener(
            'submit',
            function (event) {

                const option =
                    selectTagihan.options[
                        selectTagihan.selectedIndex
                    ];


                if (
                    !option ||
                    !option.value
                ) {

                    event.preventDefault();

                    alert(
                        'Silakan pilih siswa dan tagihan terlebih dahulu.'
                    );

                    searchInput.focus();

                    return;

                }


                const isSpp =
                    option.dataset.isSpp === '1';


                const sisa =
                    Number(
                        option.dataset.sisa || 0
                    );


                const totalSisa =
                    Number(
                        option.dataset.totalSisa ||
                        sisa
                    );


                const batasPembayaran =
                    isSpp
                        ? totalSisa
                        : sisa;


                const nominal =
                    Number(
                        inputNominal.value || 0
                    );


                if (nominal <= 0) {

                    event.preventDefault();

                    alert(
                        'Nominal pembayaran harus lebih dari 0.'
                    );

                    inputNominal.focus();

                    return;

                }


                if (
                    nominal >
                    batasPembayaran
                ) {

                    event.preventDefault();

                    alert(
                        'Nominal pembayaran tidak boleh melebihi total sisa tagihan yang dapat dibayar. ' +
                        'Maksimal Rp ' +
                        formatRupiah(
                            batasPembayaran
                        ) +
                        '.'
                    );

                    inputNominal.value =
                        batasPembayaran;

                    inputNominal.focus();

                    return;

                }

            }
        );


    /*
     * Tampilkan pilihan awal.
     */
    renderSearchResults();


    /*
     * Jika ada old('tagihan_id'),
     * tampilkan kembali data pilihannya.
     */
    const oldTagihanId =
        '{{ old('tagihan_id') }}';


    if (oldTagihanId) {

        const oldOption =
            Array.from(
                selectTagihan.options
            ).find(function (option) {

                return option.value ===
                    oldTagihanId;

            });


        if (oldOption) {

            selectTagihan.value =
                oldTagihanId;


            const siswa =
                oldOption.dataset.siswa || '';

            const kategori =
                oldOption.dataset.kategori || '';

            const bulan =
                oldOption.dataset.bulan || '';

            const tahun =
                oldOption.dataset.tahun || '';


            searchInput.value =
                siswa +
                ' - ' +
                kategori +
                (
                    bulan
                        ? ' - ' +
                          bulan +
                          ' ' +
                          tahun
                        : ''
                );


            searchClear.classList.add(
                'active'
            );


            updateTagihanInfo();

        }

    }

});

</script>

@endpush

@endsection