@extends('layout.app')

@section('title', 'users')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endsection
@section('js')
    <script src="{{ asset('js/user.js') }}" defer></script>
@endsection

@section('content')



    <div class="page-content">

        <div class="page-title">
            <h1>Pengguna</h1>
        </div>

        <div class="user-table-card">

            <div class="user-table-header">

                <div>
                    <h5>Daftar User</h5>
                    <p>Kelola data user dan hobby yang dimiliki</p>
                </div>

                <div class="user-header-actions">

                    <div class="user-search">
                        <span class="user-search-icon">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text" id="searchUser" placeholder="Cari user..." autocomplete="off">
                    </div>

                    <button type="button" class="btn-add-user" id="btnAddUser">
                        <span class="btn-add-icon">+</span>
                        <span>Tambah User</span>
                    </button>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table custom-table align-middle">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Hobby</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="userTableBody">

                        <tr id="loadingUser">
                            <td colspan="5" class="text-center py-4 text-muted">
                                Memuat data...
                            </td>
                        </tr>

                        <tr id="noUserResult" class="no-user-result">
                            <td colspan="5">
                                Tidak ada user yang ditemukan.
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="user-pagination" id="userPagination" style="display:none;">

                <div class="user-pagination-info" id="paginationInfo"></div>

                <div class="user-pagination-controls">

                    <select id="perPageSelect" class="user-per-page">
                        <option value="5">5 / halaman</option>
                        <option value="10" selected>10 / halaman</option>
                        <option value="25">25 / halaman</option>
                        <option value="50">50 / halaman</option>
                    </select>

                    <div class="user-page-buttons" id="paginationButtons"></div>

                </div>

            </div>

        </div>

    </div>

    <div class="user-modal-backdrop" id="editModal">
        <div class="user-modal" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
            <form id="editForm" novalidate>

                <div class="user-modal-header">
                    <div>
                        <h5 id="editModalTitle">Edit User</h5>
                        <p id="editModalSubtitle">Ubah data user dan hobby yang dimiliki</p>
                    </div>
                    <button type="button" class="user-modal-close" id="editClose" aria-label="Tutup">&times;</button>
                </div>

                <div class="user-modal-body">

                    <div class="user-form-alert" id="editFormError"></div>

                    <div class="user-form-group">
                        <label class="user-form-label" for="editName">Nama</label>
                        <input type="text" id="editName" autocomplete="off">
                        <div class="field-error" data-error="name"></div>
                    </div>

                    <div class="user-form-group">
                        <label class="user-form-label" for="editEmail">Email</label>
                        <input type="email" id="editEmail" autocomplete="off">
                        <div class="field-error" data-error="email"></div>
                    </div>

                    <div class="user-form-group">
                        <label class="user-form-label" for="editPhone">Nomor Telepon</label>
                        <input type="tel" id="editPhone" autocomplete="off" inputmode="tel" placeholder="08123456789">
                        <div class="field-error" data-error="phone"></div>
                    </div>

                    <div class="user-form-group">
                        <label class="user-form-label" for="editAddress">Alamat</label>
                        <textarea id="editAddress" rows="3" autocomplete="off" placeholder="Alamat lengkap"></textarea>
                        <div class="field-error" data-error="address"></div>
                    </div>

                    <div class="user-form-group">
                        <label class="user-form-label" for="editPassword">Password</label>
                        <input type="password" id="editPassword" autocomplete="new-password"
                            placeholder="Kosongkan jika tidak diubah">
                        <div class="user-form-hint" id="editPasswordHint">Kosongkan jika tidak ingin mengubah password.
                        </div>
                        <div class="field-error" data-error="password"></div>
                    </div>

                    <div class="user-form-group" style="margin-bottom:0;">
                        <label class="user-form-label">Hobby</label>
                        <div class="hobby-options" id="editHobbies"></div>
                        <div class="field-error" data-error="hobbies"></div>
                    </div>

                </div>

                <div class="user-modal-footer">
                    <button type="button" class="btn-modal-cancel" id="editCancel">Batal</button>
                    <button type="submit" class="btn-modal-save" id="editSave">Simpan</button>
                </div>

            </form>
        </div>
    </div>

@endsection
