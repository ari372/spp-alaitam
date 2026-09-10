@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('page-title', 'Profil Admin')

@section('content')

@vite('resources/css/admin/profil.css')

<div class="profil-container">

    <div class="profil-header">

        <h2>Profil Admin</h2>

        <p>
            Informasi akun administrator sistem pembayaran SPP
        </p>

    </div>

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    <div class="profil-card">

        <div class="profil-top">

            <div class="profil-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div class="profil-identitas">

                <h3>
                    {{ Auth::user()->name }}
                </h3>

                <p>
                    Administrator Sistem Pembayaran SPP
                </p>

                <span class="profil-role">
                    Administrator
                </span>

            </div>

        </div>

        <h3 class="profil-section-title">
            Informasi Akun
        </h3>

        <div class="profil-info">

            <div class="profil-info-item">
                <span class="profil-info-label">
                    Nama Lengkap
                </span>

                <span class="profil-info-value">
                    {{ Auth::user()->name }}
                </span>
            </div>

            <div class="profil-info-item">
                <span class="profil-info-label">
                    Email
                </span>

                <span class="profil-info-value">
                    {{ Auth::user()->email }}
                </span>
            </div>

            <div class="profil-info-item">
                <span class="profil-info-label">
                    Role
                </span>

                <span class="profil-info-value">
                    Administrator
                </span>
            </div>

            <div class="profil-info-item">
                <span class="profil-info-label">
                    Status Akun
                </span>

                <span class="profil-info-value">
                    Aktif
                </span>
            </div>

        </div>

        <div class="profil-actions">

            <a
                href="{{ route('admin.profil.edit') }}"
                class="btn-edit-profil"
            >
                Edit Profil
            </a>

        </div>

    </div>

</div>

@endsection