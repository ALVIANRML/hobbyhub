@extends('layout.app')

@section('title', 'Hobby')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/hobby.css') }}">
@endsection
@section('js')
    <script src="{{ asset('js/hobby.js') }}"></script>
@endsection
@section('content')

    <div class="page-content">

        <div class="page-title">
            <h1>Hobby</h1>
        </div>

        <div class="hobby-table-card">

            <div class="hobby-table-header">

                <div class="hobby-title">
                    <h5>Daftar Hobby</h5>
                    <p>Kelola daftar hobby yang tersedia di HobbyHub</p>
                </div>

                <div class="hobby-header-actions">

                    <div class="hobby-search">

                        <span class="hobby-search-icon">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text" id="searchHobby" placeholder="Cari hobby..." autocomplete="off">

                    </div>

                    <button type="button" class="btn-add-user" id="btnAddHobby">
                        <span class="btn-add-icon">+</span>
                        <span>Tambah Hobby</span>
                    </button>

                </div>

            </div>


            <div class="table-responsive">

                <table class="table hobby-table align-middle">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Hobby</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Aksi</th>
                        </tr>

                    </thead>


                    <tbody id="hobbyTableBody">

                        <tr id="loadingHobby">
                            <td colspan="4" class="text-center py-4 text-muted">
                                Memuat data...
                            </td>
                        </tr>

                        <tr id="noHobbyResult" class="no-result">
                            <td colspan="4">
                                Tidak ada hobby yang ditemukan.
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="hobby-pagination" id="hobbyPagination" style="display:none;">

                <div class="hobby-pagination-info" id="paginationInfo"></div>

                <div class="hobby-pagination-controls">

                    <select id="perPageSelect" class="hobby-per-page">
                        <option value="5">5 / halaman</option>
                        <option value="10" selected>10 / halaman</option>
                        <option value="25">25 / halaman</option>
                        <option value="50">50 / halaman</option>
                    </select>

                    <div class="hobby-page-buttons" id="paginationButtons"></div>

                </div>

            </div>

        </div>

    </div>

    <div class="hobby-modal-backdrop" id="editHobbyModal">
        <div class="hobby-modal" role="dialog" aria-modal="true" aria-labelledby="editHobbyTitle">
            <form id="editHobbyForm" novalidate>

                <div class="hobby-modal-header">
                    <div>
                        <h5 id="editHobbyTitle">Edit Hobby</h5>
                        <p id="editHobbySubtitle">Ubah nama dan deskripsi hobby</p>
                    </div>
                    <button type="button" class="hobby-modal-close" id="editHobbyClose" aria-label="Tutup">&times;</button>
                </div>

                <div class="hobby-modal-body">

                    <div class="hobby-form-alert" id="editHobbyError"></div>

                    <div class="hobby-form-group">
                        <label class="hobby-form-label" for="editNama">Nama Hobby</label>
                        <input type="text" id="editNama" autocomplete="off">
                        <div class="hobby-field-error" data-error="nama"></div>
                    </div>

                    <div class="hobby-form-group">
                        <label class="hobby-form-label" for="editDeskripsi">Deskripsi</label>
                        <textarea id="editDeskripsi" rows="4" placeholder="Deskripsi hobby (opsional)"></textarea>
                        <div class="hobby-field-error" data-error="deskripsi"></div>
                    </div>

                </div>

                <div class="hobby-modal-footer">
                    <button type="button" class="hobby-modal-cancel" id="editHobbyCancel">Batal</button>
                    <button type="submit" class="hobby-modal-save" id="editHobbySave">Simpan</button>
                </div>

            </form>
        </div>
    </div>

@endsection
