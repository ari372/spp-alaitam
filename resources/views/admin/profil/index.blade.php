@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('page-title', 'Profil Admin')

@push('styles')
    @vite('resources/css/admin/profil.css')
@endpush

@section('content')

<div class="profil-page">

    {{-- HEADER --}}
    <div class="profil-header">

        <div>
            <h2>
                Profil Admin
            </h2>

            <p>
                Informasi akun administrator sistem pembayaran SPP
            </p>
        </div>

        <a
            href="{{ route('admin.profil.edit') }}"
            class="btn-edit-profil"
        >
            <i data-lucide="pencil"></i>
            Edit Profil
        </a>

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="profil-alert profil-alert-success">

            <i data-lucide="circle-check"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- PROFIL CARD --}}
    <div class="profil-card">

        {{-- IDENTITAS --}}
        <div class="profil-identitas">

            <div class="profil-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div class="profil-identitas-content">

                <h3>
                    {{ Auth::user()->name }}
                </h3>

                <p>
                    Administrator Sistem Pembayaran SPP
                </p>

                <span class="profil-role">
                    <i data-lucide="shield-check"></i>
                    Administrator
                </span>

            </div>

        </div>


        {{-- INFORMASI AKUN --}}
        <div class="profil-section">

            <div class="profil-section-header">

                <div class="profil-section-icon">
                    <i data-lucide="user-round"></i>
                </div>

                <div>
                    <h3>
                        Informasi Akun
                    </h3>

                    <p>
                        Informasi akun administrator
                    </p>
                </div>

            </div>


            <div class="profil-info">

                {{-- NAMA --}}
                <div class="profil-info-item">

                    <div class="profil-info-label">
                        <i data-lucide="user"></i>
                        Nama Lengkap
                    </div>

                    <div class="profil-info-value">
                        {{ Auth::user()->name }}
                    </div>

                </div>


                {{-- EMAIL --}}
                <div class="profil-info-item">

                    <div class="profil-info-label">
                        <i data-lucide="mail"></i>
                        Email
                    </div>

                    <div class="profil-info-value">
                        {{ Auth::user()->email }}
                    </div>

                </div>


                {{-- ROLE --}}
                <div class="profil-info-item">

                    <div class="profil-info-label">
                        <i data-lucide="shield"></i>
                        Role
                    </div>

                    <div class="profil-info-value">

                        <span class="profil-role-badge">
                            Administrator
                        </span>

                    </div>

                </div>


                {{-- STATUS --}}
                <div class="profil-info-item">

                    <div class="profil-info-label">
                        <i data-lucide="circle-check"></i>
                        Status Akun
                    </div>

                    <div class="profil-info-value">

                        <span class="profil-status">
                            Aktif
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

    });
</script>

@endsection