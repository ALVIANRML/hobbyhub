 const searchUser = document.getElementById('searchUser');
        const userTableBody = document.getElementById('userTableBody');
        const noUserResult = document.getElementById('noUserResult');
        const loadingUser = document.getElementById('loadingUser');
 
        const userPagination = document.getElementById('userPagination');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationButtons = document.getElementById('paginationButtons');
        const perPageSelect = document.getElementById('perPageSelect');
 
        const API_USERS = '/api/user-hobby/users';
        const API_HOBBIES = '/api/hobby/get-all';
        const API_ADD_USER = '/api/user-hobby/add-user';
        const API_UPDATE_USER = id => `/api/user-hobby/update-user/${encodeURIComponent(id)}`;
        const API_DELETE_USER = id => `/api/user-hobby/delete-user/${encodeURIComponent(id)}`;
 
        const btnAddUser = document.getElementById('btnAddUser');
        const editModal = document.getElementById('editModal');
        const editModalTitle = document.getElementById('editModalTitle');
        const editModalSubtitle = document.getElementById('editModalSubtitle');
        const editForm = document.getElementById('editForm');
        const editName = document.getElementById('editName');
        const editEmail = document.getElementById('editEmail');
        const editPhone = document.getElementById('editPhone');
        const editAddress = document.getElementById('editAddress');
        const editPassword = document.getElementById('editPassword');
        const editPasswordHint = document.getElementById('editPasswordHint');
        const editHobbies = document.getElementById('editHobbies');
        const editFormError = document.getElementById('editFormError');
        const editSave = document.getElementById('editSave');
 
        let allHobbies = [];
        let editingId = null;
        let isSaving = false;
        let modalMode = 'edit';
 
        let users = [];
        let filteredUsers = [];
        let currentPage = 1;
        let perPage = parseInt(perPageSelect.value, 10);
 
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
        function groupUsers(list) {
            const map = new Map();
 
            list.forEach(item => {
                const u = item.user;
                if (!u) return;
 
                if (!map.has(u.id)) {
                    map.set(u.id, {
                        id: u.id,
                        name: u.name ?? '',
                        email: u.email ?? '',
                        phone: u.phone ?? '-',
                        address: u.address ?? '',
                        hobbies: [],
                        hobbyIds: []
                    });
                }
 
                const hobbyName = item.hobby?.nama;
                const hobbyId = item.hobby?.id ?? item.id_hobbies;
                const entry = map.get(u.id);
 
                if (hobbyName && !entry.hobbies.includes(hobbyName)) {
                    entry.hobbies.push(hobbyName);
                }
 
                if (hobbyId !== undefined && hobbyId !== null && !entry.hobbyIds.includes(hobbyId)) {
                    entry.hobbyIds.push(hobbyId);
                }
            });
 
            return Array.from(map.values());
        }
        async function getUsers() {
            try {
                const token = sessionStorage.getItem('token');
 
                if (!token) {
                    window.location.href = '/login';
                    return;
                }
 
                const response = await fetch(API_USERS, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });
 
                if (response.status === 401) {
                    sessionStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
 
                const result = await response.json();
 
                if (!response.ok) {
                    throw new Error(result.message || 'Gagal mengambil data user');
                }
 
                const list = Array.isArray(result) ? result : (result.data ?? []);
 
                users = groupUsers(list);
 
                applyFilter();
 
            } catch (error) {
                loadingUser.style.display = '';
                loadingUser.innerHTML = `
                    <td colspan="5" class="text-center py-4 text-danger">
                        Gagal memuat data: ${escapeHtml(error.message)}
                    </td>
                `;
            }
        }
        function applyFilter(resetPage = true) {
            const keyword = searchUser.value.toLowerCase().trim();
 
            filteredUsers = users.filter(user =>
                user.name.toLowerCase().includes(keyword) ||
                user.email.toLowerCase().includes(keyword) ||
                String(user.phone).toLowerCase().includes(keyword) ||
                user.hobbies.some(h => h.toLowerCase().includes(keyword))
            );
 
            if (resetPage) currentPage = 1;
 
            renderUsers();
        }
 
        function renderUsers() {
 
            loadingUser.style.display = 'none';
 
            userTableBody.querySelectorAll('.user-row').forEach(row => row.remove());
 
            const total = filteredUsers.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
 
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
 
            if (total === 0) {
                noUserResult.style.display = 'table-row';
                userPagination.style.display = 'none';
                return;
            }
 
            noUserResult.style.display = 'none';
 
            const start = (currentPage - 1) * perPage;
            const end = Math.min(start + perPage, total);
            const pageData = filteredUsers.slice(start, end);
 
            pageData.forEach((user, index) => {
 
                const row = document.createElement('tr');
                row.classList.add('user-row');
 
                const hobbyHtml = user.hobbies.length ?
                    user.hobbies.map(h => `<span class="hobby-badge">${escapeHtml(h)}</span>`).join('') :
                    '<span class="text-muted">-</span>';
 
                row.innerHTML = `
                <td class="number">
                    ${String(start + index + 1).padStart(2, '0')}
                </td>
                <td>
                    <div class="user-info">
                        <div>
                            <div class="user-name">${escapeHtml(user.name)}</div>
                            <div class="user-email">${escapeHtml(user.email)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="phone">${escapeHtml(user.phone)}</span>
                </td>
                <td>
                    <div class="hobby-list">${hobbyHtml}</div>
                </td>
                <td>
                    <div class="action-buttons justify-content-end">
                        <button type="button" class="btn custom-action btn-edit"
                            onclick="editUser('${escapeHtml(user.id)}')">
                            Edit
                        </button>
                        <button type="button" class="btn custom-action btn-delete"
                            onclick="deleteUser('${escapeHtml(user.id)}', this)">
                            Delete
                        </button>
                    </div>
                </td>
            `;
 
                userTableBody.insertBefore(row, noUserResult);
            });
 
            paginationInfo.textContent =
                `Menampilkan ${start + 1}–${end} dari ${total} user`;
 
            renderPagination(totalPages);
            userPagination.style.display = '';
        }
 
        function getPageList(totalPages) {
            const pages = [];
            const delta = 1;
 
            for (let i = 1; i <= totalPages; i++) {
                if (
                    i === 1 ||
                    i === totalPages ||
                    (i >= currentPage - delta && i <= currentPage + delta)
                ) {
                    pages.push(i);
                } else if (pages[pages.length - 1] !== '...') {
                    pages.push('...');
                }
            }
 
            return pages;
        }
 
        function renderPagination(totalPages) {
 
            let html = `
            <button class="user-page-btn" data-page="${currentPage - 1}"
                ${currentPage === 1 ? 'disabled' : ''}>‹</button>
        `;
 
            getPageList(totalPages).forEach(page => {
                if (page === '...') {
                    html += `<span class="user-page-dots">…</span>`;
                } else {
                    html += `
                    <button class="user-page-btn ${page === currentPage ? 'active' : ''}"
                        data-page="${page}">${page}</button>
                `;
                }
            });
 
            html += `
            <button class="user-page-btn" data-page="${currentPage + 1}"
                ${currentPage === totalPages ? 'disabled' : ''}>›</button>
        `;
 
            paginationButtons.innerHTML = html;
        }
 
        paginationButtons.addEventListener('click', function(e) {
            const btn = e.target.closest('.user-page-btn');
 
            if (!btn || btn.disabled) return;
 
            currentPage = parseInt(btn.dataset.page, 10);
            renderUsers();
        });
 
        perPageSelect.addEventListener('change', function() {
            perPage = parseInt(this.value, 10);
            currentPage = 1;
            renderUsers();
        });
 
        searchUser.addEventListener('input', () => applyFilter(true));
 
        async function loadHobbies() {
            const token = sessionStorage.getItem('token');
            if (!token) return;
 
            try {
                const response = await fetch(API_HOBBIES, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });
 
                const result = await response.json();
 
                if (!response.ok) {
                    throw new Error(result.message || 'Gagal mengambil data hobby');
                }
 
                const list = Array.isArray(result) ? result : (result.data ?? []);
 
                allHobbies = list.map(h => ({
                    id: h.id,
                    nama: h.nama ?? h.name ?? ''
                }));
 
            } catch (error) {
                allHobbies = [];
            }
        }
 
        function openEditModal() {
            editModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            editName.focus();
        }
 
        function closeEditModal() {
            if (isSaving) return;
 
            editModal.style.display = 'none';
            document.body.style.overflow = '';
            editingId = null;
        }
 
        function clearEditErrors() {
            editForm.querySelectorAll('.field-error').forEach(el => el.textContent = '');
            editFormError.textContent = '';
            editFormError.style.display = 'none';
        }
        function showEditErrors(message, errors) {
            let shown = false;
 
            Object.entries(errors || {}).forEach(([field, msgs]) => {
                const key = field.split('.')[0];
                const el = editForm.querySelector(`[data-error="${key}"]`);
 
                if (el) {
                    if (!el.textContent) el.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                    shown = true;
                }
            });
 
            if (!shown) {
                editFormError.textContent = message || 'Terjadi kesalahan';
                editFormError.style.display = 'block';
            }
        }
 
        function renderHobbyOptions(selectedIds = []) {
            const selected = selectedIds.map(String);
 
            if (!allHobbies.length) {
                editHobbies.innerHTML =
                    '<div class="hobby-empty">Daftar hobby tidak tersedia (hobby tidak akan diubah).</div>';
                return;
            }
 
            editHobbies.innerHTML = allHobbies.map(h => `
                <label class="hobby-option">
                    <input type="checkbox" name="hobbies" value="${escapeHtml(h.id)}"
                        ${selected.includes(String(h.id)) ? 'checked' : ''}>
                    <span>${escapeHtml(h.nama)}</span>
                </label>
            `).join('');
        }
        function setModalMode(mode) {
            modalMode = mode;
            const isAdd = mode === 'add';
 
            editModalTitle.textContent = isAdd ? 'Tambah User' : 'Edit User';
            editModalSubtitle.textContent = isAdd ?
                'Isi data user baru dan pilih hobby-nya' :
                'Ubah data user dan hobby yang dimiliki';
            editPassword.placeholder = isAdd ? 'Masukkan password' : 'Kosongkan jika tidak diubah';
            editPasswordHint.textContent = isAdd ?
                'Password wajib diisi.' :
                'Kosongkan jika tidak ingin mengubah password.';
            editSave.textContent = isAdd ? 'Tambah' : 'Simpan';
        }
 
        async function addUser() {
            setModalMode('add');
            editingId = null;
 
            editName.value = '';
            editEmail.value = '';
            editPhone.value = '';
            editAddress.value = '';
            editPassword.value = '';
            clearEditErrors();
 
            editHobbies.innerHTML = '<div class="hobby-empty">Memuat hobby...</div>';
            openEditModal();
 
            if (!allHobbies.length) await loadHobbies();
 
            renderHobbyOptions([]);
        }
 
        async function editUser(id) {
            const user = users.find(u => String(u.id) === String(id));
            if (!user) return;
 
            setModalMode('edit');
            editingId = user.id;
            editName.value = user.name;
            editEmail.value = user.email;
            editPhone.value = user.phone === '-' ? '' : user.phone;
            editAddress.value = user.address;
            editPassword.value = '';
            clearEditErrors();
 
            editHobbies.innerHTML = '<div class="hobby-empty">Memuat hobby...</div>';
            openEditModal();
 
            if (!allHobbies.length) await loadHobbies();
 
            renderHobbyOptions(user.hobbyIds);
        }
 
        btnAddUser.addEventListener('click', addUser);
 
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
 
            const isAdd = modalMode === 'add';
 
            if (isSaving) return;
            if (!isAdd && editingId === null) return;
 
            clearEditErrors();
 
            const name = editName.value.trim();
            const email = editEmail.value.trim();
            const phone = editPhone.value.trim();
            const address = editAddress.value.trim();
            const password = editPassword.value;
 
            const clientErrors = {};
            if (!name) clientErrors.name = ['Nama wajib diisi.'];
            if (!email) clientErrors.email = ['Email wajib diisi.'];
            if (isAdd && !password) clientErrors.password = ['Password wajib diisi.'];
 
            if (Object.keys(clientErrors).length) {
                showEditErrors('', clientErrors);
                return;
            }
 
            const token = sessionStorage.getItem('token');
 
            if (!token) {
                window.location.href = '/login';
                return;
            }
 
            const payload = {
                name,
                email,
                phone,
                address
            };
            if (password) payload.password = password;
            let hobbyIds = null;
 
            if (allHobbies.length) {
                hobbyIds = Array.from(
                    editHobbies.querySelectorAll('input[name="hobbies"]:checked')
                ).map(input => Number(input.value));
 
                payload.hobbies = hobbyIds;
            }
 
            isSaving = true;
            editSave.disabled = true;
            editSave.textContent = 'Menyimpan...';
 
            try {
                const response = await fetch(isAdd ? API_ADD_USER : API_UPDATE_USER(editingId), {
                    method: isAdd ? 'POST' : 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(payload)
                });
 
                const result = await response.json().catch(() => ({}));
 
                if (response.status === 401) {
                    sessionStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
 
                if (!response.ok) {
                    const err = new Error(result.message || 'Gagal menyimpan data');
                    err.errors = result.errors;
                    throw err;
                }
                const savedId = editingId;
 
                isSaving = false;
                closeEditModal();
 
                if (isAdd) {
                    await getUsers();
                } else {
                    const user = users.find(u => String(u.id) === String(savedId));
 
                    if (user) {
                        user.name = name;
                        user.email = email;
                        user.phone = phone || '-';
                        user.address = address;
 
                        if (hobbyIds !== null) {
                            user.hobbyIds = hobbyIds;
                            user.hobbies = allHobbies
                                .filter(h => hobbyIds.includes(Number(h.id)))
                                .map(h => h.nama);
                        }
                    }
 
                    applyFilter(false);
                }
 
            } catch (error) {
                showEditErrors(error.message, error.errors);
            } finally {
                isSaving = false;
                editSave.disabled = false;
                editSave.textContent = modalMode === 'add' ? 'Tambah' : 'Simpan';
            }
        });
 
        document.getElementById('editClose').addEventListener('click', closeEditModal);
        document.getElementById('editCancel').addEventListener('click', closeEditModal);
 
        editModal.addEventListener('mousedown', function(e) {
            if (e.target === editModal) closeEditModal();
        });
 
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && editModal.style.display === 'flex') closeEditModal();
        });

        async function deleteUser(id, btn) {
            const user = users.find(u => String(u.id) === String(id));
            const name = user ? user.name : 'user ini';
 
            if (!confirm(`Hapus ${name}? Data yang dihapus tidak bisa dikembalikan.`)) return;
 
            const token = sessionStorage.getItem('token');
 
            if (!token) {
                window.location.href = '/login';
                return;
            }
 
            const originalText = btn ? btn.textContent : '';
 
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Menghapus...';
            }
 
            try {
                const response = await fetch(API_DELETE_USER(id), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                });
 
                const result = await response.json().catch(() => ({}));
 
                if (response.status === 401) {
                    sessionStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }
 
                if (!response.ok) {
                    throw new Error(result.message || 'Gagal menghapus user');
                }
                users = users.filter(u => String(u.id) !== String(id));
                applyFilter(false);
 
            } catch (error) {
                alert('Gagal menghapus: ' + error.message);
 
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }
        }
 
        getUsers();
        loadHobbies();