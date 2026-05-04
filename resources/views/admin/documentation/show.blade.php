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
            <button onclick="submitAll('rejected')" class="flex-1 md:flex-none px-10 py-2.5 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors">
                Reject
            </button>
            <button onclick="submitAll('approved')" class="flex-1 md:flex-none inline-flex items-center justify-center px-10 py-2.5 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-[#1a1c20] hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Approve
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
                    <p class="text-[11px] text-gray-500 mb-5">
                        Uploaded: {{ \Carbon\Carbon::parse($doc->created_at)->format('M d, Y') }}
                    </p>
                    
                    <!-- Actions -->
                    <div class="flex gap-3">
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#f3f4f6] hover:bg-gray-200 text-gray-700 text-[13px] font-medium rounded-md border border-transparent transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </a>
                        <button type="button" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#f3f4f6] hover:bg-red-50 hover:text-red-600 text-gray-700 text-[13px] font-medium rounded-md border border-transparent transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
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

<script>
    function submitAll(status) {
        if (!confirm('Are you sure you want to ' + status + ' all documentations for this event?')) {
            return;
        }
        
        const documentations = @json($event->documentations->pluck('id'));
        
        if (documentations.length === 0) {
            alert('No documentation to ' + status);
            return;
        }
        
        let promises = documentations.map(id => {
            return fetch('/documentation/' + id + '/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            });
        });

        Promise.all(promises).then(() => {
            alert('Successfully ' + status + ' documentations.');
            window.location.href = "{{ route('admin.documentation.index') }}";
        }).catch(err => {
            console.error(err);
            alert('An error occurred.');
        });
    }
</script>
@endsection
