<!-- Main Content -->
<div class="main-content min-h-screen">
    <!-- Header -->
    <header class="shadow-sm sticky top-0 z-30" style="background-color: #31326F;">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="lg:hidden text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-semibold text-white">Dashboard Administrator</h1>
            </div>

            <div class="flex items-center gap-4">
                @if(Auth::user()->role !== 'superadmin')
                <div class="hidden md:flex items-center relative">
                    <i class="fas fa-search absolute left-3 text-gray-300"></i>
                    <input type="text" placeholder="Cari..." class="rounded-full pl-10 pr-4 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 w-48 border border-gray-300">
                </div>
                @endif

                <div class="flex items-center gap-2">
                    <!-- Notification Button with Modal -->
                    <button id="notification-button" class="h-10 w-10 rounded-full bg-info bg-opacity-20 flex items-center justify-center text-white relative hover:bg-opacity-30 transition-colors">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">3</span>
                    </button>

                    <!-- Notification Modal -->
                    <div id="notification-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-96 overflow-hidden">
                            <div class="flex items-center justify-between p-4 border-b">
                                <h3 class="text-lg font-semibold text-gray-800">Notifikasi</h3>
                                <button id="close-notification" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="overflow-y-auto max-h-80">
                                <div class="p-4 space-y-3">
                                    <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mt-1">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Data OPD baru ditambahkan</p>
                                            <p class="text-xs text-gray-500">Dinas Kesehatan telah terdaftar</p>
                                            <p class="text-xs text-gray-400 mt-1">2 menit yang lalu</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 mt-1">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Proyek selesai</p>
                                            <p class="text-xs text-gray-500">Proyek infrastruktur telah selesai</p>
                                            <p class="text-xs text-gray-400 mt-1">1 jam yang lalu</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mt-1">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-800">Peringatan sistem</p>
                                            <p class="text-xs text-gray-500">Backup database diperlukan</p>
                                            <p class="text-xs text-gray-400 mt-1">5 jam yang lalu</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-t">
                                <button id="mark-all-read-btn" class="w-full py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    Tandai sudah dibaca
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Detail Modal -->
                    <div id="notif-detail-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300" id="notif-detail-content">
                            <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-file-invoice-dollar"></i> Detail Pengajuan RFK
                                </h3>
                                <button id="close-notif-detail" class="text-indigo-200 hover:text-white transition">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <div class="p-6">
                                <div id="notif-detail-body" class="space-y-4">
                                    <!-- Dynamic content -->
                                </div>
                            </div>
                            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                                <button id="close-notif-detail-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm transition shadow-sm">Tutup</button>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="user-menu-button" class="h-10 w-10 rounded-full bg-info bg-opacity-20 flex items-center justify-center text-white hover:bg-opacity-30 transition-colors">
                            <i class="fas fa-user"></i>
                        </button>
                        <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-50 hidden border border-gray-200">
                            <div class="py-2">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-circle w-5 text-gray-400 mr-3"></i>
                                    <span>Profil</span>
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog w-5 text-gray-400 mr-3"></i>
                                    <span>Pengaturan</span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form action ="{{ route('logout') }}" method="POST">
                                    @csrf
                                <button class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt w-5 text-red-400 mr-3"></i>
                                    <span>Logout</span>
                                </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <style>
        /* Dropdown Animation */
        #user-dropdown {
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        #user-dropdown.show {
            opacity: 1;
            transform: translateY(0);
            display: block;
        }

        /* Modal Animation */
        #notification-modal {
            opacity: 0;
            transition: all 0.3s ease;
        }

        #notification-modal.show {
            opacity: 1;
            display: flex;
        }

        /* Notification Items Hover */
        .bg-blue-50:hover, .bg-green-50:hover, .bg-yellow-50:hover {
            transform: translateX(2px);
            transition: transform 0.2s ease;
        }

        /* Smooth transitions for buttons */
        button {
            transition: all 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const userMenuButton = document.getElementById('user-menu-button');
            const userDropdown = document.getElementById('user-dropdown');
            const notificationButton = document.getElementById('notification-button');
            const notificationModal = document.getElementById('notification-modal');
            const closeNotification = document.getElementById('close-notification');
            const sidebarToggle = document.getElementById('sidebar-toggle');

            // Toggle User Dropdown
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isVisible = userDropdown.classList.contains('show');

                // Close all other dropdowns first
                closeAllDropdowns();

                // Toggle current dropdown
                if (!isVisible) {
                    userDropdown.classList.add('show');
                }
            });

            // Toggle Notification Modal
            notificationButton.addEventListener('click', function() {
                notificationModal.classList.add('show');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            });

            // Close Notification Modal
            closeNotification.addEventListener('click', function() {
                notificationModal.classList.remove('show');
                document.body.style.overflow = ''; // Re-enable scrolling
            });

            // Close modal when clicking outside
            notificationModal.addEventListener('click', function(e) {
                if (e.target === notificationModal) {
                    notificationModal.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.relative') && !e.target.closest('#user-menu-button')) {
                    closeAllDropdowns();
                }
            });

            // Close dropdowns when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAllDropdowns();
                    if (notificationModal.classList.contains('show')) {
                        notificationModal.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }
            });

            // Sidebar toggle functionality
            sidebarToggle.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                sidebar.classList.toggle('active');
            });

            // Function to close all dropdowns
            function closeAllDropdowns() {
                userDropdown.classList.remove('show');
            }

            // Prevent dropdown from closing when clicking inside
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Dynamic Notification Fetch
            async function loadNotifications() {
                try {
                    const response = await fetch('/dashboard/rfk/pending');
                    if (!response.ok) return;
                    const result = await response.json();

                    if (result.success && result.data) {
                        const notificationsList = document.querySelector('#notification-modal .p-4.space-y-3');
                        if (!notificationsList) return;

                        let readNotifs = JSON.parse(localStorage.getItem('read_notifs') || '[]');
                        let unreadData = result.data.filter(item => !readNotifs.includes(item.id));

                        let html = '';
                        unreadData.forEach(item => {
                            const programName = item.nama_program || 'Program Baru';
                            const staffName = item.user ? item.user.name : 'Staff';
                            const opdName = item.opd ? item.opd.nama_opd : 'OPD Tidak Diketahui';
                            const pagu = item.pagu || 0;
                            const pendingRealisasi = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0] : null;
                            const nilaiDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_keuangan : 0;
                            const fisikDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_fisik : 0;
                            const timeString = new Date(item.created_at).toLocaleString('id-ID');

                            html += `
                                <div class="flex flex-col gap-2 p-3 bg-blue-50 rounded-lg border border-blue-100 shadow-sm mb-3">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 mt-1 flex-shrink-0">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-800">${programName}</p>
                                            <p class="text-xs text-gray-600 font-medium">${opdName}</p>
                                            <p class="text-xs text-gray-500 mt-1"><i class="far fa-user mr-1"></i>Oleh: ${staffName} • <i class="far fa-clock mx-1"></i>${timeString}</p>
                                        </div>
                                    </div>
                                    <div class="bg-white p-2 rounded border border-gray-100 text-xs mt-1">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-gray-500">Pagu Master:</span>
                                            <span class="font-medium text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(pagu)}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Nilai Diajukan:</span>
                                            <span class="font-medium text-green-600">Rp ${new Intl.NumberFormat('id-ID').format(nilaiDiajukan)} (${fisikDiajukan}%)</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-2">
                                        <button onclick="showNotifDetail('${encodeURIComponent(JSON.stringify(item))}')" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded transition shadow-sm">
                                            <i class="fas fa-eye mr-1"></i> View Detail Lengkap
                                        </button>
                                    </div>
                                </div>
                            `;
                        });

                        if (unreadData.length === 0) {
                            html = '<div class="text-center py-4 text-gray-500 text-sm">Tidak ada notifikasi baru</div>';
                        }

                        notificationsList.innerHTML = html;

                        // Update badge
                        const badge = notificationButton.querySelector('span');
                        if (unreadData.length > 0) {
                            badge.textContent = unreadData.length;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }
                } catch (error) {
                    console.error("Error loading notifications:", error);
                }
            }

            // Show Notif Detail Modal
            window.showNotifDetail = function(encodedItem) {
                const item = JSON.parse(decodeURIComponent(encodedItem));
                const modal = document.getElementById('notif-detail-modal');
                const body = document.getElementById('notif-detail-body');

                const programName = item.nama_program || 'Program Baru';
                const staffName = item.user ? item.user.name : 'Staff';
                const opdName = item.opd ? item.opd.nama_opd : 'OPD Tidak Diketahui';
                const pagu = item.pagu || 0;
                const pendingRealisasi = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0] : null;
                const nilaiDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_keuangan : 0;
                const fisikDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_fisik : 0;

                body.innerHTML = `
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 font-medium">Program / Instansi OPD</p>
                        <p class="text-lg font-bold text-gray-800 leading-tight mt-1">${programName}</p>
                        <p class="text-sm text-indigo-600 font-medium mt-1"><i class="fas fa-building mr-1"></i> ${opdName}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 mb-1">Pagu Master</p>
                            <p class="font-bold text-gray-800">Rp ${new Intl.NumberFormat('id-ID').format(pagu)}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl border border-green-100 shadow-sm">
                            <p class="text-xs text-green-600 mb-1">Nilai Realisasi Diajukan</p>
                            <p class="font-bold text-green-700">Rp ${new Intl.NumberFormat('id-ID').format(nilaiDiajukan)} (${fisikDiajukan}%)</p>
                        </div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <p class="text-xs text-blue-600 mb-1 font-medium">Informasi Penginput</p>
                        <p class="text-sm text-blue-800"><i class="far fa-user mr-1"></i> ${staffName}</p>
                        <p class="text-xs text-blue-500 mt-1"><i class="far fa-clock mr-1"></i> ${new Date(item.created_at).toLocaleString('id-ID')}</p>
                    </div>
                `;

                modal.classList.remove('hidden');
                setTimeout(() => document.getElementById('notif-detail-content').classList.remove('scale-95'), 10);
            };

            // Close Notif Detail Modal
            const closeNotifDetail = () => {
                const modal = document.getElementById('notif-detail-modal');
                document.getElementById('notif-detail-content').classList.add('scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            };
            document.getElementById('close-notif-detail').addEventListener('click', closeNotifDetail);
            document.getElementById('close-notif-detail-btn').addEventListener('click', closeNotifDetail);

            // Mark as Read Logic
            document.getElementById('mark-all-read-btn').addEventListener('click', async function() {
                try {
                    const response = await fetch('/dashboard/rfk/pending');
                    if (response.ok) {
                        const result = await response.json();
                        if (result.success && result.data) {
                            const currentIds = result.data.map(item => item.id);
                            let readNotifs = JSON.parse(localStorage.getItem('read_notifs') || '[]');
                            readNotifs = [...new Set([...readNotifs, ...currentIds])];
                            localStorage.setItem('read_notifs', JSON.stringify(readNotifs));

                            // Reload and close
                            loadNotifications();
                            notificationModal.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    }
                } catch (error) {
                    console.error("Error marking read", error);
                }
            });

            // Initial load
            loadNotifications();

            // Poll every 30 seconds
            setInterval(loadNotifications, 30000);
        });
    </script>
