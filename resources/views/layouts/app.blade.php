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

                // Menyesuaikan style jika notifikasi berupa ACC/REJECT documentation event
                let icon = '';
                let bgClass = !n.is_read ? 'bg-blue-50/50' : 'hover:bg-gray-50';
                
                if (n.title && n.title.toLowerCase().includes('reject')) {
                    icon = `<div class="bg-red-100 text-red-600 p-2 rounded-full shrink-0"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>`;
                } else if (n.title && n.title.toLowerCase().includes('accept') || n.title && n.title.toLowerCase().includes('acc')) {
                    icon = `<div class="bg-green-100 text-green-600 p-2 rounded-full shrink-0"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>`;
                } else {
                    icon = `<div class="bg-blue-100 text-blue-600 p-2 rounded-full shrink-0"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>`;
                }

                html += `
                    <div class="p-4 border-b ${bgClass} cursor-pointer transition flex gap-3 items-start"
                        onclick="markAsRead(${n.id})">
                        ${icon}
                        <div>
                            <p class="text-sm font-semibold text-gray-800">${n.title}</p>
                            <p class="text-xs text-gray-600 mt-0.5">${n.message}</p>
                            ${!n.is_read ? '<span class="text-[10px] font-bold text-blue-600 mt-1 inline-block uppercase tracking-wider">New</span>' : ''}
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