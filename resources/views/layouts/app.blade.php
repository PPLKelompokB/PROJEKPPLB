<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'OceanCare')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-[#fcfcfc] text-gray-800 antialiased">

    {{-- NAVBAR (opsional) --}}
    @hasSection('navbar')
        @yield('navbar')
    @else
        <x-layout.navbar />
    @endif

    {{-- CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER (opsional) --}}
    @hasSection('footer')
        @yield('footer')
    @else
        <x-layout.footer />
    @endif


    {{-- 🔔 GLOBAL NOTIFICATION SCRIPT --}}
    <script>
    let isOpen = false;

    function toggleNotif() {
        const dropdown = document.getElementById('notifDropdown');

        if (!isOpen) {
            dropdown.classList.remove('hidden');
            dropdown.classList.add('dropdown-enter');

            setTimeout(() => {
                dropdown.classList.add('dropdown-enter-active');
                dropdown.classList.remove('dropdown-enter');
            }, 10);

            fetchNotifications();
        } else {
            dropdown.classList.remove('dropdown-enter-active');
            dropdown.classList.add('dropdown-leave-active');

            setTimeout(() => {
                dropdown.classList.add('hidden');
                dropdown.classList.remove('dropdown-leave-active');
            }, 150);
        }

        isOpen = !isOpen;
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notifWrapper');
        const dropdown = document.getElementById('notifDropdown');

        if (!wrapper?.contains(e.target) && isOpen) {
            dropdown.classList.add('hidden');
            isOpen = false;
        }
    });

    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);

        if (diffDay > 0) {
            const hoursLeft = diffHour % 24;
            if (hoursLeft > 0) {
                return `${diffDay} day${diffDay > 1 ? 's' : ''} ${hoursLeft} hour${hoursLeft > 1 ? 's' : ''} ago`;
            }
            return `${diffDay} day${diffDay > 1 ? 's' : ''} ago`;
        }
        if (diffHour > 0) {
            return `${diffHour} hour${diffHour > 1 ? 's' : ''} ago`;
        }
        if (diffMin > 0) {
            return `${diffMin} minute${diffMin > 1 ? 's' : ''} ago`;
        }
        return `Just now`;
    }

    async function fetchNotifications() {
        try {
            const res = await fetch('/api/notifications');
            const data = await res.json();

            const list = document.getElementById('notifList');
            const badge = document.getElementById('notifBadge');

            if (!data.data || data.data.length === 0) {
                if (list) list.innerHTML = `<p class="p-4 text-sm text-gray-500 text-center py-8">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    No new notifications
                </p>`;
                if (badge) badge.classList.add('hidden');
                return;
            }

            let html = '';
            let unread = 0;

            data.data.forEach(n => {
                if (!n.is_read) unread++;

                let bgClass = !n.is_read ? 'bg-slate-50 relative' : 'bg-white hover:bg-slate-50 relative';
                let indicator = !n.is_read ? '<div class="absolute left-0 top-0 bottom-0 w-1 bg-[#dbeafe]"></div>' : '';

                html += `
                    <div class="p-4 border-b border-gray-100 ${bgClass} cursor-pointer transition-all flex flex-col"
                        onclick="handleNotifClick(${n.id}, '${n.action_url || ''}')">
                        ${indicator}
                        <p class="text-[15px] font-bold text-[#334155] leading-snug">${n.title}</p>
                        <p class="text-[14px] text-[#475569] mt-1 leading-snug">${n.message}</p>
                        <p class="text-[13px] font-bold text-[#64748b] mt-2">${timeAgo(n.created_at)}</p>
                        <div class="text-right mt-1">
                            <span class="text-[14px] ${n.action_url ? 'text-[#64748b] hover:text-[#334155]' : 'text-transparent'}">View full notification</span>
                        </div>
                    </div>
                `;
            });

            if (list) list.innerHTML = html;

            if (badge) {
                if (unread > 0) {
                    badge.innerText = unread;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

        } catch (err) {
            console.error(err);
        }
    }

    async function markAsRead(id) {
        await fetch(`/api/notifications/${id}/read`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        fetchNotifications();
    }

    function handleNotifClick(id, actionUrl) {
        fetch(`/api/notifications/${id}/read`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => {
            if (actionUrl && actionUrl !== '') {
                window.location.href = actionUrl;
            } else {
                fetchNotifications();
            }
        });
    }

    async function markAllAsRead() {
        await fetch(`/api/notifications/read-all`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        fetchNotifications();
    }

    // Panggil saat page load agar badge merah muncul jika ada notif baru
    document.addEventListener('DOMContentLoaded', () => {
        const badge = document.getElementById('notifBadge');
        if (badge) {
            fetchNotifications();
        }
    });
    </script>

    @stack('scripts')

</body>
</html>