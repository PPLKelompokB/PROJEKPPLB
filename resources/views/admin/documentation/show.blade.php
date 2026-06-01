@extends('layouts.app')

@section('title', 'Review Documentation - OceanCare')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header Section -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">Event Documentation</h1>
            <p class="text-gray-500 text-sm">Manage photos from beach clean-up events.</p>
        </div>
        
        <div class="flex items-center gap-4 w-full md:w-auto">
            <button onclick="openModal('rejected')" class="flex-1 md:flex-none px-8 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors {{ $event->documentations->where('status', 'pending')->count() == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $event->documentations->where('status', 'pending')->count() == 0 ? 'disabled' : '' }}>
                Reject All Pending
            </button>
            <button onclick="openModal('approved')" class="flex-1 md:flex-none inline-flex items-center justify-center px-8 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-[#1a1c20] hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors gap-2 {{ $event->documentations->where('status', 'pending')->count() == 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $event->documentations->where('status', 'pending')->count() == 0 ? 'disabled' : '' }}>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Approve All Pending
            </button>
        </div>
    </div>

    <!-- Uploaded Documentation Section -->
    <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Uploaded Documentation</h2>
        <p class="text-sm text-gray-600 mb-6">Review Documentation</p>
        
        @if($event->documentations->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($event->documentations as $doc)
            <div class="border border-gray-200 border-dashed rounded-lg p-4 bg-gray-50/50">
                <p class="text-[10px] font-bold text-gray-400 mb-2 tracking-wider uppercase">DOCUMENTATION</p>
                <div class="flex items-center gap-3 bg-white p-3 rounded border border-gray-100 shadow-sm">
                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm text-gray-800 font-medium truncate">{{ basename($doc->file_path) }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-sm text-gray-500 italic">No documentation files uploaded for this event.</div>
        @endif
    </div>

    <!-- Event Documentation Images Section -->
    <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Event Documentation</h2>
        
        @if($event->documentations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($event->documentations as $doc)
            <div class="border border-gray-200 rounded-xl overflow-hidden flex flex-col">
                <!-- Image Preview -->
                <div class="h-48 bg-[#d1d5db] relative flex items-center justify-center p-4">
                    @if(Storage::disk('public')->exists($doc->file_path))
                        <img src="{{ asset('storage/' . $doc->file_path) }}" alt="Documentation" class="w-full h-full object-cover absolute inset-0">
                    @endif
                    <!-- Placeholder text matching mockup -->
                    <div class="relative z-10 text-white text-center text-sm font-medium px-4">{{ Str::limit($doc->note ?? 'Event documentation image', 50) }}</div>
                    <div class="absolute inset-0 bg-black/10"></div>
                </div>
                
                <!-- Details -->
                <div class="p-5 flex-1 flex flex-col">
                    <p class="text-sm text-gray-700 leading-relaxed mb-3 flex-1">
                        {{ $doc->note ?? 'No description provided.' }}
                    </p>
                    <div class="flex justify-between items-center mb-5">
                        <p class="text-[11px] text-gray-500">
                            Uploaded: {{ \Carbon\Carbon::parse($doc->created_at)->format('M d, Y') }}
                        </p>
                        @if($doc->status == 'approved')
                            <span class="bg-black text-white px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide capitalize">Approved</span>
                        @elseif($doc->status == 'rejected')
                            <span class="bg-gray-100 text-gray-500 border border-gray-200 px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide capitalize">Rejected</span>
                        @else
                            <span class="bg-gray-50 text-gray-600 border border-gray-200 px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide capitalize">Pending</span>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-[#f3f4f6] hover:bg-gray-200 text-gray-700 text-[12px] font-medium rounded-md border border-transparent transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </a>
                        @if($doc->status == 'pending')
                        <button type="button" onclick="openModal('rejected', {{ $doc->id }})" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white hover:bg-red-50 text-red-600 text-[12px] font-medium rounded-md border border-red-200 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reject
                        </button>
                        <button type="button" onclick="openModal('approved', {{ $doc->id }})" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-black hover:bg-gray-800 text-white text-[12px] font-medium rounded-md border border-transparent transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Approve
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-sm text-gray-500 italic">No documentation to display.</div>
        @endif
    </div>
</div>

<div id="confirmModal" class="hidden fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 text-center mb-6">Are you sure you want to approve this documentation?</h3>
        <div class="flex gap-4">
            <button onclick="closeModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
            <button id="confirmModalBtn" onclick="executeAction()" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 transition-colors">Approve!</button>
        </div>
    </div>
</div>

<script>
    let currentDocId = null;
    let currentStatus = null;

    function openModal(status, id = null) {
        currentStatus = status;
        currentDocId = id;
        document.getElementById('modalTitle').innerText = 'Are you sure you want to ' + status + ' ' + (id ? 'this' : 'these') + ' documentation' + (id ? '?' : 's?');
        document.getElementById('confirmModalBtn').innerText = status === 'approved' ? 'Approve!' : 'Reject!';
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    function executeAction() {
        const confirmModalBtn = document.getElementById('confirmModalBtn');
        const originalText = confirmModalBtn.innerHTML;
        confirmModalBtn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        confirmModalBtn.disabled = true;

        if (currentDocId) {
            submitSingle(currentDocId, currentStatus);
        } else {
            submitAll(currentStatus);
        }
    }

    function submitSingle(id, status) {
        fetch('/documentation/' + id + '/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        }).then(response => {
            if(response.ok) {
                location.reload();
            } else {
                response.json().then(data => {
                    alert(data.message || 'An error occurred.');
                    closeModal();
                });
            }
        }).catch(err => {
            console.error(err);
            alert('An error occurred.');
            closeModal();
        });
    }

    function submitAll(status) {
        const buttons = document.querySelectorAll('button[onclick*="openModal"]');
        const pendingIds = [];
        
        buttons.forEach(btn => {
            const match = btn.getAttribute('onclick').match(/openModal\('[^']+',\s*(\d+)/);
            if (match && match[1]) {
                const id = match[1];
                if (!pendingIds.includes(id)) {
                    pendingIds.push(id);
                }
            }
        });

        if (pendingIds.length === 0) {
            alert('No pending documentations found.');
            closeModal();
            return;
        }

        let completed = 0;
        let hasError = false;

        pendingIds.forEach(id => {
            fetch('/documentation/' + id + '/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            }).then(response => {
                if(!response.ok) hasError = true;
            }).catch(() => {
                hasError = true;
            }).finally(() => {
                completed++;
                if (completed === pendingIds.length) {
                    if(hasError) {
                        alert('Some documentations could not be updated.');
                    }
                    location.reload();
                }
            });
        });
    }
</script>
@endsection
