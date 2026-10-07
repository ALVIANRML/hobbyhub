        const searchHobby = document.getElementById('searchHobby');
        const hobbyTableBody = document.getElementById('hobbyTableBody');
        const noHobbyResult = document.getElementById('noHobbyResult');
        const loadingHobby = document.getElementById('loadingHobby');
 
        const hobbyPagination = document.getElementById('hobbyPagination');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationButtons = document.getElementById('paginationButtons');
        const perPageSelect = document.getElementById('perPageSelect');
 
        const API_GET_ALL = '/api/hobby/get-all';
        const API_ADD_HOBBY = '/api/hobby/add-hobby';
        const API_UPDATE_HOBBY = id => `/api/hobby/update-hobby/${encodeURIComponent(id)}`;
        const API_DELETE_HOBBY = id => `/api/hobby/delete-hobby/${encodeURIComponent(id)}`;
 
        const btnAddHobby = document.getElementById('btnAddHobby');
        const editModal = document.getElementById('editHobbyModal');
        const editModalTitle = document.getElementById('editHobbyTitle');
        const editModalSubtitle = document.getElementById('editHobbySubtitle');
        const editForm = document.getElementById('editHobbyForm');
        const editNama = document.getElementById('editNama');
        const editDeskripsi = document.getElementById('editDeskripsi');
        const editError = document.getElementById('editHobbyError');
        const editSave = document.getElementById('editHobbySave');
 
        let hobbies = []; 
        let filteredHobbies = []; 
        let currentPage = 1;
        let perPage = parseInt(perPageSelect.value, 10);
 
        let editingHobbyId = null;
        let isSaving = false;
        let modalMode = 'edit'; 
 
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
 
        function normalizeHobby(h) {
            return {
                id: h.id,
                name: h.name ?? h.nama ?? '',
                description: h.description ?? h.deskripsi ?? '',
            };
        }
 
        function getToken() {
            const token = sessionStorage.getItem('token');
 
            if (!token) {
                window.location.href = '/login';
                return null;
            }
 
            return token;
        }
        async function getHobbies() {
            try {
                const token = getToken();
                if (!token) return;
 
                const response = await fetch(API_GET_ALL, {
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
                const list = Array.isArray(result) ? result : (result.data ?? []);
 
                hobbies = list.map(normalizeHobby);
                applyFilter();
 
            } catch (error) {
                console.error('ERROR:', error);
                loadingHobby.style.display = '';
                loadingHobby.innerHTML =
                    '<td colspan="4" class="text-center py-4 text-danger">Gagal memuat data.</td>';
            }
        }
        function applyFilter(resetPage = true) {
            const keyword = searchHobby.value.toLowerCase().trim();
 
            filteredHobbies = hobbies.filter(hobby =>
                hobby.name.toLowerCase().includes(keyword) ||
                hobby.description.toLowerCase().includes(keyword)
            );
 
            if (resetPage) currentPage = 1;
 
            renderHobbies();
        }
 
        function renderHobbies() {
 
            loadingHobby.style.display = 'none';
 
            hobbyTableBody.querySelectorAll('.hobby-row').forEach(row => row.remove());
 
            const total = filteredHobbies.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
 
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
 
            if (total === 0) {
                noHobbyResult.style.display = 'table-row';
                hobbyPagination.style.display = 'none';
                return;
            }
 
            noHobbyResult.style.display = 'none';
 
            const start = (currentPage - 1) * perPage;
            const end = Math.min(start + perPage, total);
            const pageData = filteredHobbies.slice(start, end);
 
            pageData.forEach((hobby, index) => {
 
                const row = document.createElement('tr');
                row.classList.add('hobby-row');
 
                row.innerHTML = `
                <td class="hobby-number">
                    ${String(start + index + 1).padStart(2, '0')}
                </td>
                <td>
                    <div class="hobby-info">
                       
                        <div class="hobby-name">${escapeHtml(hobby.name)}</div>
                    </div>
                </td>
                <td>
                    <div class="hobby-description">
                        ${escapeHtml(hobby.description) || '-'}
                    </div>
                </td>
 
                <td>
                    <div class="hobby-action-buttons">
                        <button type="button" class="btn hobby-btn hobby-btn-edit"
                            onclick="editHobby('${escapeHtml(hobby.id)}')">
                            Edit
                        </button>
                        <button type="button" class="btn hobby-btn hobby-btn-delete"
                            onclick="deleteHobby('${escapeHtml(hobby.id)}', this)">
                            Delete
                        </button>
                    </div>
                </td>
            `;
 
                hobbyTableBody.insertBefore(row, noHobbyResult);
            });
 
            paginationInfo.textContent =
                `Menampilkan ${start + 1}–${end} dari ${total} hobby`;
 
            renderPagination(totalPages);
            hobbyPagination.style.display = '';
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
            <button class="hobby-page-btn" data-page="${currentPage - 1}"
                ${currentPage === 1 ? 'disabled' : ''}>‹</button>
        `;
 
            getPageList(totalPages).forEach(page => {
                if (page === '...') {
                    html += `<span class="hobby-page-dots">…</span>`;
                } else {
                    html += `
                    <button class="hobby-page-btn ${page === currentPage ? 'active' : ''}"
                        data-page="${page}">${page}</button>
                `;
                }
            });
 
            html += `
            <button class="hobby-page-btn" data-page="${currentPage + 1}"
                ${currentPage === totalPages ? 'disabled' : ''}>›</button>
        `;
 
            paginationButtons.innerHTML = html;
        }
 
        paginationButtons.addEventListener('click', function(e) {
            const btn = e.target.closest('.hobby-page-btn');
 
            if (!btn || btn.disabled) return;
 
            currentPage = parseInt(btn.dataset.page, 10);
            renderHobbies();
        });
        perPageSelect.addEventListener('change', function() {
            perPage = parseInt(this.value, 10);
            currentPage = 1;
            renderHobbies();
        });
 
        searchHobby.addEventListener('input', () => applyFilter(true));

        function openEditModal() {
            editModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            editNama.focus();
        }
 
        function closeEditModal() {
            if (isSaving) return;
 
            editModal.style.display = 'none';
            document.body.style.overflow = '';
            editingHobbyId = null;
        }
 
        function clearEditErrors() {
            editForm.querySelectorAll('.hobby-field-error').forEach(el => el.textContent = '');
            editError.textContent = '';
            editError.style.display = 'none';
        }
 
        function showEditErrors(message, errors) {
            let shown = false;
 
            Object.entries(errors || {}).forEach(([field, msgs]) => {
                const el = editForm.querySelector(`[data-error="${field}"]`);
 
                if (el) {
                    if (!el.textContent) el.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                    shown = true;
                }
            });
 
            if (!shown) {
                editError.textContent = message || 'Terjadi kesalahan';
                editError.style.display = 'block';
            }
        }
 
        function setModalMode(mode) {
            modalMode = mode;
            const isAdd = mode === 'add';
 
            editModalTitle.textContent = isAdd ? 'Tambah Hobby' : 'Edit Hobby';
            editModalSubtitle.textContent = isAdd ?
                'Isi nama dan deskripsi hobby baru' :
                'Ubah nama dan deskripsi hobby';
            editSave.textContent = isAdd ? 'Tambah' : 'Simpan';
        }
 
        function addHobby() {
            setModalMode('add');
            editingHobbyId = null;
 
            editNama.value = '';
            editDeskripsi.value = '';
            clearEditErrors();
 
            openEditModal();
        }
 
        function editHobby(id) {
            const hobby = hobbies.find(h => String(h.id) === String(id));
            if (!hobby) return;
 
            setModalMode('edit');
            editingHobbyId = hobby.id;
            editNama.value = hobby.name;
            editDeskripsi.value = hobby.description;
            clearEditErrors();
 
            openEditModal();
        }
 
        btnAddHobby.addEventListener('click', addHobby);
 
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
 
            const isAdd = modalMode === 'add';
 
            if (isSaving) return;
            if (!isAdd && editingHobbyId === null) return;
 
            clearEditErrors();
 
            const nama = editNama.value.trim();
            const deskripsi = editDeskripsi.value.trim();
 
            if (!nama) {
                showEditErrors('', {
                    nama: ['Nama hobby wajib diisi.']
                });
                return;
            }
 
            const token = getToken();
            if (!token) return;
 
            isSaving = true;
            editSave.disabled = true;
            editSave.textContent = 'Menyimpan...';
 
            try {
                const response = await fetch(isAdd ? API_ADD_HOBBY : API_UPDATE_HOBBY(editingHobbyId), {
                    method: isAdd ? 'POST' : 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        nama,
                        deskripsi
                    })
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
 
                const savedId = editingHobbyId;
 
                isSaving = false;
                closeEditModal();
 
                if (isAdd) {
                    await getHobbies();
                } else {
                    const hobby = hobbies.find(h => String(h.id) === String(savedId));
 
                    if (hobby) {
                        hobby.name = nama;
                        hobby.description = deskripsi;
                    }
 
                    applyFilter(false);
                }
 
            } catch (error) {
                console.error('SAVE ERROR:', error);
                showEditErrors(error.message, error.errors);
            } finally {
                isSaving = false;
                editSave.disabled = false;
                editSave.textContent = modalMode === 'add' ? 'Tambah' : 'Simpan';
            }
        });
 
        document.getElementById('editHobbyClose').addEventListener('click', closeEditModal);
        document.getElementById('editHobbyCancel').addEventListener('click', closeEditModal);
 
        editModal.addEventListener('mousedown', function(e) {
            if (e.target === editModal) closeEditModal();
        });
 
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && editModal.style.display === 'flex') closeEditModal();
        });
 
 
        async function deleteHobby(id, btn) {
            const hobby = hobbies.find(h => String(h.id) === String(id));
            const name = hobby ? hobby.name : 'hobby ini';
 
            if (!confirm(`Hapus hobby "${name}"? Data yang dihapus tidak bisa dikembalikan.`)) return;
 
            const token = getToken();
            if (!token) return;
 
            const originalText = btn ? btn.textContent : '';
 
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Menghapus...';
            }
 
            try {
                const response = await fetch(API_DELETE_HOBBY(id), {
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
                    throw new Error(result.message || 'Gagal menghapus hobby');
                }
 
                hobbies = hobbies.filter(h => String(h.id) !== String(id));
                applyFilter(false);
 
            } catch (error) {
                console.error('DELETE ERROR:', error);
                alert('Gagal menghapus: ' + error.message);
 
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }
        }
 
        getHobbies();