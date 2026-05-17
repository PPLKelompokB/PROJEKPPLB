@extends('layouts.app')

@section('title', 'Manage Event Documentation - OceanCare')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-1">Event Documentation</h1>
        <p class="text-gray-500 text-sm">Upload and manage photos from your beach clean-up events.</p>
    </div>

    <!-- Select Event Section -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8">
        <label for="event_select" class="block text-sm font-medium text-gray-900 mb-3">Select Event</label>
        <div class="relative max-w-lg">
            <select id="event_select" onchange="window.location.href=this.value" class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-black focus:border-black sm:text-sm rounded-lg appearance-none bg-white border border-gray-300">
                @foreach($completedEvents as $ce)
                    <option value="{{ route('organizer.documentation.show', $ce->id) }}" {{ $event->id === $ce->id ? 'selected' : '' }}>
                        {{ $ce->title }} | {{ \Carbon\Carbon::parse($ce->event_date)->format('M d, Y') }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="mb-8 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-8 bg-red-50 text-red-600 p-4 rounded-lg text-sm font-medium border border-red-100">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Upload Documentation Section -->
    <div class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Upload Documentation</h2>
        
        <form action="{{ route('organizer.documentation.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left: File Upload -->
                <div>
                    <label class="block text-xs text-gray-600 mb-3 font-medium">Upload Photo</label>
                    <div class="mt-1 flex justify-center px-6 pt-10 pb-10 border-2 border-gray-200 border-dashed rounded-xl bg-white hover:bg-gray-50 transition-colors relative" id="dropZoneDoc" onclick="document.getElementById('file-upload').click()">
                        <div class="space-y-2 text-center" id="uploadPlaceholderDoc">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <button type="button" class="relative cursor-pointer bg-white rounded-md font-medium text-black hover:text-gray-800 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-black px-3 py-1.5 border border-gray-300 shadow-sm">
                                    <span>Choose Files</span>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Drag and drop your photos here, or</p>
                            <p class="text-[11px] text-gray-400 mt-4 uppercase tracking-wide">PNG, JPG up to 10MB</p>
                        </div>
                        
                        <div class="flex flex-col items-center">
                            <img id="imagePreviewDoc" class="hidden max-h-48 rounded shadow-sm">
                            <p id="fileNameDoc" class="hidden text-sm text-gray-600 mt-3 font-medium"></p>
                        </div>
                        
                        <input id="file-upload" name="file" type="file" class="hidden" required accept="image/png, image/jpeg, image/jpg">
                    </div>
                </div>

                <!-- Right: Description -->
                <div class="flex flex-col">
                    <label for="note" class="block text-xs text-gray-600 mb-3 font-medium">Photo Description</label>
                    <textarea id="note" name="note" rows="5" class="shadow-sm focus:ring-black focus:border-black block w-full sm:text-sm border-gray-300 rounded-xl p-4 flex-1 resize-none" placeholder="Describe what's happening in this photo..."></textarea>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-[#1a1c20] hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Upload Documentation
                        </button>
                    </div>
                </div>
            </div>
        </form>
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
                    <div class="relative z-10 text-white text-center text-sm font-medium px-4 drop-shadow-sm">{{ Str::limit($doc->note ?? 'Event documentation image', 50) }}</div>
                    <div class="absolute inset-0 bg-black/10"></div>
                    
                    <!-- Status Badge -->
                    @if($doc->status === 'approved')
                        <div class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-20 uppercase tracking-wider">Approved</div>
                    @elseif($doc->status === 'rejected')
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-20 uppercase tracking-wider">Rejected</div>
                    @else
                        <div class="absolute top-2 right-2 bg-yellow-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm z-20 uppercase tracking-wider">Pending</div>
                    @endif
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
                        <button type="button" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#f9fafb] hover:bg-gray-100 text-gray-700 text-[13px] font-medium rounded-md border border-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <form action="{{ route('organizer.documentation.destroy', $doc->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this documentation?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#f9fafb] hover:bg-red-50 hover:text-red-600 text-gray-700 text-[13px] font-medium rounded-md border border-gray-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-sm text-gray-500 italic">No documentation uploaded yet.</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('file-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            let placeholder = document.getElementById('uploadPlaceholderDoc');
            let preview = document.getElementById('imagePreviewDoc');
            let fileName = document.getElementById('fileNameDoc');
            
            placeholder.classList.add('hidden');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            
            fileName.textContent = file.name;
            fileName.classList.remove('hidden');
        }
    });
</script>
@endpush
