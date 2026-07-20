@php
    $user = auth()->user();
    $name = $user->nama ?? 'User';
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $w) {
        if (!empty($w)) $initials .= mb_strtoupper(mb_substr($w, 0, 1));
        if (mb_strlen($initials) >= 2) break;
    }
    if (mb_strlen($initials) === 1) $initials .= mb_strtoupper(mb_substr($words[0] ?? '', 1, 1));
    $role = $user->role ?? '';
    $roleLabel = $role === 'admin_gudang' ? 'Admin Gudang' : ucfirst($role);
@endphp

<div class="flex items-center gap-4">
    <!-- Notification Bell -->
    <div class="relative" id="notificationWrapper">
        <button type="button" id="notificationBtn" class="relative p-2 rounded-xl hover:bg-slate-50 transition-all">
            <i class="fas fa-bell text-slate-600 text-lg"></i>
            <span id="notificationBadge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">0</span>
        </button>

        <!-- Notification Dropdown -->
        <div id="notificationDropdown" class="hidden absolute right-0 top-full mt-2 w-64 sm:w-80 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
            <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-slate-800">Notifikasi Stok</p>
                    <button id="clearNotifications" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Hapus Semua</button>
                </div>
            </div>
            <div id="notificationList" class="max-h-96 overflow-y-auto">
                <div class="px-4 py-8 text-center text-slate-400 italic text-sm">
                    Tidak ada notifikasi
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Dropdown -->
    <div class="relative" id="profileDropdownWrapper">
    <button type="button" id="profileDropdownBtn" class="flex items-center gap-3 hover:bg-slate-50 rounded-xl px-2 py-1.5 transition-all">
        <div class="hidden sm:block text-right">
            <p class="text-sm font-bold text-slate-900 leading-tight">{{ $name }}</p>
            <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">{{ $roleLabel }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-200 text-sm">
            {{ $initials }}
        </div>
    </button>

        <div id="profileDropdown" class="hidden absolute right-0 top-full mt-2 w-56 max-w-[calc(100vw-1rem)] bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
        <div class="px-4 py-3 border-b border-slate-100">
            <p class="text-sm font-bold text-slate-800 truncate">{{ $name }}</p>
            <p class="text-xs text-slate-400 truncate">{{ $user->email ?? '-' }}</p>
        </div>
        <div class="py-1">
            <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                <i class="fas fa-cog text-slate-400 w-4 text-center"></i>
                <span>Pengaturan</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-100">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                    <i class="fas fa-sign-out-alt w-4 text-center"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Profile Dropdown
    (function() {
        const btn = document.getElementById('profileDropdownBtn');
        const dropdown = document.getElementById('profileDropdown');
        if (!btn || !dropdown) return;

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    })();

    // Notification System
    (function() {
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationList = document.getElementById('notificationList');
        const clearBtn = document.getElementById('clearNotifications');
        
        let notifications = JSON.parse(localStorage.getItem('lowStockNotifications') || '[]');

        // Toggle notification dropdown
        if (notificationBtn && notificationDropdown) {
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
                // Close profile dropdown if open
                const profileDropdown = document.getElementById('profileDropdown');
                if (profileDropdown) profileDropdown.classList.add('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });
        }

        // Render notifications
        function renderNotifications() {
            if (notifications.length === 0) {
                notificationList.innerHTML = '<div class="px-4 py-8 text-center text-slate-400 italic text-sm">Tidak ada notifikasi</div>';
                notificationBadge.classList.add('hidden');
            } else {
                notificationBadge.classList.remove('hidden');
                notificationBadge.textContent = notifications.length > 9 ? '9+' : notifications.length;
                
                notificationList.innerHTML = notifications.map((notif, index) => {
                    const isManajer = window.location.pathname.startsWith('/manajer');
                    const baseUrl = isManajer ? '/manajer/barang' : '/admin/barang';

                    const targetUrl = `${baseUrl}?search=${encodeURIComponent(notif.barang_nama)}`;
                    
                    return `
                            <div class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors cursor-pointer" 
                                 onclick="event.preventDefault(); fetch('/api/low-stock-items/'+${notif.barang_id}, {method:'DELETE'}).then(() => window.location.href='${targetUrl}')">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate">${notif.barang_nama}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Stok tersisa: ${notif.stok} unit</p>
                                        <p class="text-[10px] text-slate-400 mt-1">${formatTime(notif.timestamp)}</p>
                                    </div>
                                    <button onclick="event.stopPropagation(); removeNotification(${index})" class="text-slate-400 hover:text-red-500 transition-colors">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('');
            }
            localStorage.setItem('lowStockNotifications', JSON.stringify(notifications));
        }

        // Add new notification
        window.addNotification = function(data) {
            notifications.unshift(data);
            // Keep only last 20 notifications
            if (notifications.length > 20) notifications = notifications.slice(0, 20);
            renderNotifications();
            
            // Show popup alert
            showStockAlertPopup(data);
        };

        // Remove single notification
        window.removeNotification = function(index) {
            const notif = notifications[index];
            if (notif && notif.barang_id) {
                fetch('/api/low-stock-items/' + notif.barang_id, { method: 'DELETE' })
                    .catch(err => console.error('Dismiss error:', err));
            }
            notifications.splice(index, 1);
            renderNotifications();
        };

        // Clear all notifications
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                fetch('/api/low-stock-items', { method: 'DELETE' })
                    .catch(err => console.error('Dismiss all error:', err));
                notifications = [];
                renderNotifications();
            });
        }

        // Format timestamp
        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = (now - date) / 1000; // seconds

            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        // Show popup alert for low stock - STACK DOWNWARD
        let activePopups = [];
        
        function showStockAlertPopup(data) {
            const popup = document.createElement('div');
            const topPosition = 20 + (activePopups.length * 90); // Stack 90px apart
            popup.className = 'fixed right-4 z-50 bg-white rounded-2xl shadow-2xl border-l-4 border-red-500 p-4 max-w-sm animate-slide-in';
            popup.style.top = topPosition + 'px';
            popup.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-800">Peringatan Stok Rendah!</p>
                        <p class="text-xs text-slate-600 mt-1">${data.message}</p>
                        <button class="mt-2 text-xs text-blue-600 hover:text-blue-700 font-medium">Tutup</button>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);
            activePopups.push(popup);
            
            // Close button
            popup.querySelector('button').addEventListener('click', () => {
                removePopup(popup);
            });

            // Auto remove after 8 seconds
            setTimeout(() => {
                removePopup(popup);
            }, 8000);
        }
        
        function removePopup(popup) {
            if (popup.parentElement) {
                popup.style.opacity = '0';
                popup.style.transform = 'translateX(400px)';
                popup.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    popup.remove();
                    activePopups = activePopups.filter(p => p !== popup);
                    // Reposition remaining popups
                    activePopups.forEach((p, index) => {
                        p.style.top = (20 + (index * 90)) + 'px';
                    });
                }, 300);
            }
        }

        // Initial render
        renderNotifications();
        
        // Load existing low stock items from API
        function loadLowStockItems() {
            fetch('/api/low-stock-items')
                .then(response => response.json())
                .then(data => {
                    // Hapus notifikasi localStorage yang sudah tidak ada di DB
                    const apiBarangIds = new Set(data.map(n => n.barang_id));
                    const beforeCount = notifications.length;
                    notifications = notifications.filter(n => apiBarangIds.has(n.barang_id));

                    if (data.length > 0) {
                        // Merge with existing notifications, avoid duplicates
                        const existingIds = new Set(notifications.map(n => n.barang_id));
                        const newNotifications = data.filter(n => !existingIds.has(n.barang_id));

                        if (newNotifications.length > 0) {
                            notifications = [...newNotifications, ...notifications];
                            // Keep only last 20 notifications
                            if (notifications.length > 20) notifications = notifications.slice(0, 20);

                            // Show popup for new notifications
                            newNotifications.forEach(notif => {
                                showStockAlertPopup(notif);
                            });
                        }
                    }

                    // Re-render jika ada perubahan
                    if (notifications.length !== beforeCount || data.length > 0) {
                        renderNotifications();
                    }
                })
                .catch(error => console.error('Error loading low stock items:', error));
        }
        
        loadLowStockItems();
        
        // Poll for new notifications every 2 seconds
        setInterval(loadLowStockItems, 2000);
    })();
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease;
    }
</style>
