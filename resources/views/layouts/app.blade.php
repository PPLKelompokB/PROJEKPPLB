<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'OceanCare')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

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
                list.innerHTML = `<p class="p-4 text-sm text-gray-500">No notifications</p>`;
                badge.classList.add('hidden');
                return;
            }

            let html = '';
            let unread = 0;

            data.data.forEach(n => {
                if (!n.is_read) unread++;

                html += `
                    <div class="p-4 border-b hover:bg-gray-50 cursor-pointer"
                        onclick="markAsRead(${n.id})">
                        <p class="font-medium">${n.title}</p>
                        <p class="text-sm text-gray-600">${n.message}</p>
                        ${!n.is_read ? '<span class="text-xs text-blue-500">NEW</span>' : ''}
                    </div>
                `;
            });

            list.innerHTML = html;

            if (unread > 0) {
                badge.innerText = unread;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
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
    </script>

    @stack('scripts')

</body>
</html>