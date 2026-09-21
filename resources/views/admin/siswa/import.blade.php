@extends('layouts.admin')

@section('title', 'Import Siswa')

@section('page-title', 'Import Siswa')

@push('styles')
    @vite('resources/css/admin/siswa.css')
@endpush

@section('content')

<div class="siswa-import-page">

    {{-- HEADER --}}

    <div class="siswa-import-header">

        <div class="siswa-import-header-content">

            <div class="siswa-import-header-icon">
                <i data-lucide="file-spreadsheet"></i>
            </div>

            <div>
                <h1>Import Data Siswa</h1>
                <p>
                    Tambahkan data siswa dan orang tua melalui Excel.
                </p>
            </div>

        </div>

        <a
            href="{{ route('admin.siswa.index') }}"
            class="siswa-import-back"
        >
            <i data-lucide="arrow-left"></i>
            <span>Kembali</span>
        </a>

    </div>


    {{-- CARD UTAMA --}}

    <div class="siswa-import-card">

        <div class="siswa-import-card-header">

            <div class="siswa-import-card-icon">
                <i data-lucide="file-up"></i>
            </div>

            <div>
                <h2>Import Excel</h2>

                <p>
                    Upload file Excel sesuai format yang tersedia.
                </p>
            </div>

        </div>


        {{-- INFORMASI --}}

        <div class="siswa-import-info">

            <div class="siswa-import-info-title">

                <i data-lucide="info"></i>

                <strong>
                    Petunjuk Pengisian
                </strong>

            </div>

            <ul>

                <li>
                    Unduh format Excel terlebih dahulu.
                </li>

                <li>
                    Nama kelas harus sama dengan data kelas di sistem.
                </li>

                <li>
                    Jenis kelamin hanya menggunakan L atau P.
                </li>

                <li>
                    Email orang tua digunakan untuk akun login.
                </li>

                <li>
                    Jika email orang tua sudah terdaftar, akun tersebut
                    akan digunakan kembali.
                </li>

                <li>
                    Password default jika dikosongkan adalah
                    <strong>orangtua123</strong>.
                </li>

                <li>
                    NIS yang sudah terdaftar tidak dapat diimport ulang.
                </li>

            </ul>

        </div>


        {{-- ALERT SUKSES --}}

        @if(session('success'))

            <div class="siswa-import-alert siswa-import-alert-success">

                <i data-lucide="check-circle"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- ALERT ERROR --}}

        @if(session('error'))

            <div class="siswa-import-alert siswa-import-alert-error">

                <i data-lucide="alert-circle"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>

        @endif


        {{-- VALIDATION ERROR --}}

        @if($errors->any())

            <div class="siswa-import-alert siswa-import-alert-error">

                <i data-lucide="alert-triangle"></i>

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


        {{-- DOWNLOAD TEMPLATE --}}

        <div class="siswa-import-download">

            <div class="siswa-import-download-icon">

                <i data-lucide="file-spreadsheet"></i>

            </div>

            <div class="siswa-import-download-content">

                <h3>
                    Format Excel Siswa
                </h3>

                <p>
                    Unduh template Excel untuk melihat kolom yang
                    harus diisi.
                </p>

            </div>

            <a
                href="{{ route('admin.siswa.import.template') }}"
                class="siswa-import-download-btn"
            >

                <i data-lucide="download"></i>

                <span>
                    Unduh Format
                </span>

            </a>

        </div>


        {{-- FORM IMPORT --}}

        <form
            action="{{ route('admin.siswa.import.excel') }}"
            method="POST"
            enctype="multipart/form-data"
            class="siswa-import-form"
        >

            @csrf

            <div class="siswa-import-form-group">

                <label for="file">

                    File Excel

                    <span>*</span>

                </label>

                <div class="siswa-import-file-wrapper">

                    <i data-lucide="file-up"></i>

                    <input
                        type="file"
                        id="file"
                        name="file"
                        accept=".xlsx,.xls,.csv"
                        required
                    >

                </div>

                <small>
                    Format yang didukung: XLSX, XLS, dan CSV.
                    Ukuran maksimal 5 MB.
                </small>

            </div>


            <div class="siswa-import-actions">

                <a
                    href="{{ route('admin.siswa.index') }}"
                    class="siswa-import-btn siswa-import-btn-cancel"
                >

                    <i data-lucide="x"></i>

                    <span>
                        Batal
                    </span>

                </a>

                <button
                    type="submit"
                    class="siswa-import-btn siswa-import-btn-submit"
                >

                    <i data-lucide="upload"></i>

                    <span>
                        Import Data
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush

@endsection