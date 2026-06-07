@extends('dashboard.layout.app',['title'=>'Manajemen User'])
@section('content')

<!-- Content -->
<div class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h1>
            <p class="text-gray-600">Kelola data pengguna dan akses sistem</p>
        </div>
        <div class="flex gap-3">
            <button id="btn-add-user" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                <i class="fas fa-plus"></i> Tambah Pengguna
            </button>
            <button id="btn-export" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                <i class="fas fa-download"></i> Ekspor Data
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pengguna</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="search-user" placeholder="Nama atau Email..." class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="filter-role" class="border border-gray-300 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua Role</option>
                        <option value="superadmin">Super Admin</option>
                        <option value="administrator">Administrator</option>
                        <option value="kepala_opd">Kepala OPD</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="filter-status" class="border border-gray-300 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">OPD</label>
                    <select id="filter-opd" class="border border-gray-300 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua OPD</option>
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <button id="apply-filter" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Table Users -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        <div class="table-container overflow-x-auto">
            <table id="users-table" class="min-w-full">
                <thead>
                    <tr class="bg-indigo-600">
                        <th class="rounded-tl-xl px-4 py-3 text-left text-white font-semibold text-sm">No</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-sm">Nama</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-sm">Email</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-sm">Role</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-sm">OPD</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-sm">Status</th>
                        <th class="px-4 py-3 text-left text-white font-semibold text-sm">Terakhir Login</th>
                        <th class="rounded-tr-xl px-4 py-3 text-left text-white font-semibold text-sm">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="flex justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                            </div>
                            <p class="mt-2 text-gray-500">Memuat data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center p-5 border-t border-gray-200">
            <div id="pagination-info" class="text-sm text-gray-600 mb-4 md:mb-0">
                Menampilkan 0 dari 0 entri
            </div>
            <div class="flex items-center gap-1 bg-white rounded-xl shadow-sm p-1">
                <button id="prev-page" class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left text-sm text-gray-600"></i>
                </button>
                <div id="pagination-numbers" class="flex items-center gap-1"></div>
                <button id="next-page" class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-right text-sm text-gray-600"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div id="user-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden" style="z-index: 1000;">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
            <h3 id="modal-title" class="text-lg font-semibold text-gray-800">Tambah Pengguna</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-5">
            <form id="user-form" onsubmit="return false;">
                @csrf
                <input type="hidden" id="user-id" name="user_id">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="nama" name="nama" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>

                <div class="mb-4" id="password-field">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter (untuk password baru)</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                    <select id="role" name="role" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Pilih Role</option>
                        <option value="superadmin">Super Admin</option>
                        <option value="administrator">Administrator</option>
                        <option value="kepala_opd">Kepala OPD</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">OPD</label>
                    <select id="opd_id" name="opd_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Pilih OPD</option>
                        @foreach($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak terkait OPD tertentu</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="flex justify-end gap-3 p-5 border-t border-gray-200 sticky bottom-0 bg-white">
            <button id="cancel-modal" class="px-4 py-2 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
            <button id="save-user" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden" style="z-index: 1000;">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
        <div class="p-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus</h3>
        </div>
        <div class="p-5">
            <p class="text-gray-600">Apakah Anda yakin ingin menghapus pengguna <strong id="delete-user-name"></strong>?</p>
            <p class="text-sm text-red-500 mt-2">Tindakan ini tidak dapat dibatalkan!</p>
        </div>
        <div class="flex justify-end gap-3 p-5 border-t border-gray-200">
            <button id="cancel-delete" class="px-4 py-2 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
            <button id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Hapus</button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden" style="z-index: 9999;">
    <div class="flex flex-col items-center">
        <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        <p class="mt-3 text-gray-600">Memuat data...</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Debug: Cek apakah jQuery sudah load
console.log('jQuery version:', $.fn.jquery);

// Variabel global
let currentPage = 1;
let totalPages = 1;
let currentDeleteId = null;
let isEditMode = false;

// CSRF Token setup untuk AJAX
const csrfToken = $('meta[name="csrf-token"]').attr('content');
console.log('CSRF Token:', csrfToken);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': csrfToken
    }
});

// Fungsi untuk memuat data users dari server
function loadUsers() {
    console.log('loadUsers dipanggil');
    showLoading();

    const search = $('#search-user').val();
    const role = $('#filter-role').val();
    const status = $('#filter-status').val();
    const opd_id = $('#filter-opd').val();

    console.log('Filter params:', {search, role, status, opd_id, page: currentPage});

    $.ajax({
        url: '/dashboard/users/data',
        method: 'GET',
        data: {
            page: currentPage,
            search: search,
            role: role,
            status: status,
            opd_id: opd_id
        },
        success: function(response) {
            console.log('Response success:', response);
            if (response.success) {
                renderTable(response.data);
                updatePagination(response.pagination);
            } else {
                showNotification('Gagal memuat data: ' + (response.message || 'Unknown error'), 'error');
            }
            hideLoading();
        },
        error: function(xhr) {
            console.error('Error detail:', xhr);
            let errorMsg = 'Terjadi kesalahan saat memuat data';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showNotification(errorMsg, 'error');
            hideLoading();

            // Tampilkan error di tabel
            $('#table-body').html(`
                <tr>
                    <td colspan="8" class="text-center py-8 text-red-500">
                        <i class="fas fa-exclamation-triangle text-3xl mb-2 block"></i>
                        Error: ${errorMsg}<br>
                        <small class="text-gray-500">Cek console untuk detail</small>
                    </td>
                </tr>
            `);
        }
    });
}

// Render tabel
function renderTable(users) {
    const tbody = $('#table-body');
    console.log('Render table dengan users:', users);

    if (!users || users.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="8" class="text-center py-8 text-gray-500">
                    <i class="fas fa-users text-3xl mb-2 block"></i>
                    Tidak ada data pengguna
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    users.forEach((user, index) => {
        const startIndex = (currentPage - 1) * 10;
        const roleBadge = getRoleBadge(user.role);
        const statusBadge = getStatusBadge(user.status);
        const lastLogin = user.last_login ? formatDate(user.last_login) : '-';
        const opdName = (user.opd && user.opd.nama_opd) ? user.opd.nama_opd : '-';

        html += `
            <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                <td class="px-4 py-3 text-center">${startIndex + index + 1}</td>
                <td class="px-4 py-3 font-medium">${escapeHtml(user.name)}</td>
                <td class="px-4 py-3">${escapeHtml(user.email)}</td>
                <td class="px-4 py-3">${roleBadge}</td>
                <td class="px-4 py-3">${escapeHtml(opdName)}</td>
                <td class="px-4 py-3">${statusBadge}</td>
                <td class="px-4 py-3 text-sm text-gray-500">${lastLogin}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <button onclick="editUser(${user.id})" class="text-indigo-600 hover:text-indigo-800 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="confirmDelete(${user.id}, '${escapeHtml(user.name)}')" class="text-red-600 hover:text-red-800 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.html(html);
}

// Update pagination
function updatePagination(pagination) {
    if (!pagination) return;

    totalPages = pagination.last_page;
    currentPage = pagination.current_page;

    const startItem = pagination.from || 0;
    const endItem = pagination.to || 0;
    $('#pagination-info').html(`Menampilkan ${startItem} sampai ${endItem} dari ${pagination.total} entri`);

    // Generate pagination buttons
    let pagesHtml = '';
    const maxVisible = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);

    if (endPage - startPage + 1 < maxVisible) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }

    for (let i = startPage; i <= endPage; i++) {
        pagesHtml += `<button onclick="goToPage(${i})" class="h-9 w-9 flex items-center justify-center rounded-lg ${i === currentPage ? 'bg-indigo-600 text-white' : 'hover:bg-gray-100'}">${i}</button>`;
    }

    $('#pagination-numbers').html(pagesHtml);
    $('#prev-page').prop('disabled', currentPage === 1);
    $('#next-page').prop('disabled', currentPage === totalPages);
}

// Go to page
function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadUsers();
}

// Get role badge
function getRoleBadge(role) {
    const badges = {
        superadmin: '<span class="bg-purple-100 text-purple-800 text-xs px-2.5 py-1 rounded-full font-medium">Super Admin</span>',
        administrator: '<span class="bg-indigo-100 text-indigo-800 text-xs px-2.5 py-1 rounded-full font-medium">Administrator</span>',
        kepala_opd: '<span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-medium">Kepala OPD</span>',
        staff: '<span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-medium">Staff</span>'
    };
    return badges[role] || '<span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded-full">-</span>';
}

// Get status badge
function getStatusBadge(status) {
    if (status == 1) {
        return '<span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">Aktif</span>';
    }
    return '<span class="bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-medium">Non-Aktif</span>';
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = $('<div>')
        .addClass(`fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} transition-all transform translate-x-full`)
        .html(`
            <div class="flex items-center gap-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `);

    $('body').append(notification);

    setTimeout(() => {
        notification.removeClass('translate-x-full').addClass('translate-x-0');
    }, 100);

    setTimeout(() => {
        notification.removeClass('translate-x-0').addClass('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Loading handlers
function showLoading() {
    $('#loading-overlay').removeClass('hidden');
}

function hideLoading() {
    $('#loading-overlay').addClass('hidden');
}

// Reset form modal
function resetForm() {
    $('#user-id').val('');
    $('#nama').val('');
    $('#email').val('');
    $('#password').val('');
    $('#role').val('');
    $('#opd_id').val('');
    $('#status').val('1');
    $('#modal-title').text('Tambah Pengguna');
    $('#password-field').show();
    isEditMode = false;
}

// Open add modal
function openAddModal() {
    console.log('Open add modal');
    resetForm();
    $('#user-modal').removeClass('hidden');
}

// Edit user
function editUser(id) {
    console.log('Edit user:', id);
    showLoading();

    $.ajax({
        url: `/dashboard/users/${id}/edit`,
        method: 'GET',
        success: function(response) {
            console.log('Edit response:', response);
            if (response.success) {
                const user = response.data;
                isEditMode = true;
                $('#modal-title').text('Edit Pengguna');
                $('#user-id').val(user.id);
                $('#nama').val(user.name);
                $('#email').val(user.email);
                $('#role').val(user.role);
                $('#opd_id').val(user.opd_id || '');
                $('#status').val(user.status);
                $('#password-field').hide();
                $('#user-modal').removeClass('hidden');
            } else {
                showNotification('Gagal memuat data user', 'error');
            }
            hideLoading();
        },
        error: function(xhr) {
            console.error('Error edit:', xhr);
            showNotification('Terjadi kesalahan', 'error');
            hideLoading();
        }
    });
}

// Save user
function saveUser() {
    console.log('Save user dipanggil');
    const id = $('#user-id').val();
    const formData = {
        name: $('#nama').val().trim(),
        email: $('#email').val().trim(),
        password: $('#password').val(),
        role: $('#role').val(),
        opd_id: $('#opd_id').val() || null,
        status: $('#status').val()
    };

    console.log('Form data:', formData);

    // Validasi
    if (!formData.name || !formData.email || !formData.role) {
        showNotification('Mohon isi semua field yang wajib diisi!', 'error');
        return;
    }

    if (!isEditMode && (!formData.password || formData.password.length < 6)) {
        showNotification('Password harus diisi minimal 6 karakter!', 'error');
        return;
    }

    showLoading();

    const url = isEditMode ? `/dashboard/users/${id}` : '/dashboard/users';
    const method = isEditMode ? 'PUT' : 'POST';

    console.log('URL:', url, 'Method:', method);

    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function(response) {
            console.log('Save response:', response);
            if (response.success) {
                $('#user-modal').addClass('hidden');
                loadUsers();
                showNotification(isEditMode ? 'Pengguna berhasil diupdate!' : 'Pengguna berhasil ditambahkan!', 'success');
            } else {
                showNotification(response.message || 'Gagal menyimpan data', 'error');
            }
            hideLoading();
        },
        error: function(xhr) {
            console.error('Save error:', xhr);
            let errorMsg = 'Terjadi kesalahan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showNotification(errorMsg, 'error');
            hideLoading();
        }
    });
}

// Confirm delete
function confirmDelete(id, name) {
    currentDeleteId = id;
    $('#delete-user-name').text(name);
    $('#delete-modal').removeClass('hidden');
}

// Delete user
function deleteUser() {
    if (!currentDeleteId) return;
    console.log('Delete user:', currentDeleteId);
    showLoading();

    $.ajax({
        url: `/dashboard/users/${currentDeleteId}`,
        method: 'DELETE',
        success: function(response) {
            console.log('Delete response:', response);
            if (response.success) {
                $('#delete-modal').addClass('hidden');
                loadUsers();
                showNotification('Pengguna berhasil dihapus!', 'success');
                currentDeleteId = null;
            } else {
                showNotification(response.message || 'Gagal menghapus data', 'error');
            }
            hideLoading();
        },
        error: function(xhr) {
            console.error('Delete error:', xhr);
            showNotification('Terjadi kesalahan saat menghapus data', 'error');
            hideLoading();
        }
    });
}

// Apply filter
function applyFilter() {
    console.log('Apply filter');
    currentPage = 1;
    loadUsers();
}

// Event listeners
$(document).ready(function() {
    console.log('Document ready - Memulai inisialisasi');

    // Load initial data
    loadUsers();

    // Button event listeners
    $('#btn-add-user').on('click', openAddModal);
    $('#apply-filter').on('click', applyFilter);
    $('#save-user').on('click', saveUser);
    $('#close-modal, #cancel-modal').on('click', () => $('#user-modal').addClass('hidden'));
    $('#cancel-delete').on('click', () => $('#delete-modal').addClass('hidden'));
    $('#confirm-delete').on('click', deleteUser);

    $('#prev-page').on('click', () => {
        if (currentPage > 1) goToPage(currentPage - 1);
    });

    $('#next-page').on('click', () => {
        if (currentPage < totalPages) goToPage(currentPage + 1);
    });

    // Search on enter
    $('#search-user').on('keypress', function(e) {
        if (e.which === 13) applyFilter();
    });

    // Export data
    $('#btn-export').on('click', function() {
        const params = $.param({
            search: $('#search-user').val(),
            role: $('#filter-role').val(),
            status: $('#filter-status').val(),
            opd_id: $('#filter-opd').val()
        });
        window.location.href = '/dashboard/users/export?' + params;
    });

    console.log('Inisialisasi selesai');
});
</script>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
.animate-spin {
    animation: spin 1s linear infinite;
}
.table-container {
    overflow-x: auto;
}
#users-table {
    width: 100%;
    border-collapse: collapse;
}
#users-table th {
    background-color: #4f46e5;
    color: white;
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
}
#users-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
}
#users-table tr:hover {
    background-color: #f9fafb;
}
button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

@endsection
