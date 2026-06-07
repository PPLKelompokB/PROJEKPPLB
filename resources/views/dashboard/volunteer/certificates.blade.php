@extends('layouts.app')

@section('title', 'My Certificates - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10" id="certificates-container">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-medium text-gray-900 tracking-tight">My Certificates</h1>
            <p class="text-sm text-gray-600 mt-1">Recognition for your environmental impact and dedication</p>
        </div>
        <div class="bg-gray-100/80 border border-gray-200/50 rounded-xl px-5 py-3 flex items-center gap-3 shadow-sm shrink-0">
            <div class="bg-emerald-50 text-emerald-600 p-2 rounded-lg border border-emerald-100">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-900">{{ $certificatesEarned }} Certificates Earned</h4>
                <p class="text-[10px] text-gray-500 font-medium mt-0.5">Member since {{ $user->created_at->format('F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Events Completed -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition duration-200">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 uppercase tracking-wider">Active</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalEventsCompleted) }}</h3>
            <p class="text-xs font-semibold text-gray-500 mt-1">Events Completed</p>
        </div>

        <!-- Hours Volunteered -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition duration-200">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-sky-600 bg-sky-50 px-2 py-0.5 rounded border border-sky-100 uppercase tracking-wider">Impact</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalHours) }}</h3>
            <p class="text-xs font-semibold text-gray-500 mt-1">Hours Volunteered</p>
        </div>

        <!-- Beaches Cleaned -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition duration-200">
            <div class="flex justify-between items-start">
                <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-100 uppercase tracking-wider">Locations</span>
            </div>
            <h3 class="text-3xl font-semibold text-gray-900 mt-5">{{ number_format($totalBeaches) }}</h3>
            <p class="text-xs font-semibold text-gray-500 mt-1">Locations Cleaned</p>
        </div>
    </div>

    {{-- SEARCH & FILTERS --}}
    <form id="filterForm" action="{{ route('certificates.index') }}" method="GET" class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-8 gap-4">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Search bar -->
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Search certificates..." 
                    class="block w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition duration-150"
                >
            </div>

            <!-- Filter Year -->
            <select 
                name="year" 
                class="bg-white border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition duration-150"
            >
                <option value="All Years">All Years</option>
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>

            <!-- Filter Location -->
            <select 
                name="location" 
                class="bg-white border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition duration-150"
            >
                <option value="All Locations">All Locations</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc }}" {{ $selectedLocation == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
            </select>
        </div>

        <button 
            type="button" 
            onclick="downloadAllCertificates()"
            class="flex items-center justify-center gap-2 bg-[#1e293b] hover:bg-black text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm transition duration-150 shrink-0"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download All
        </button>
    </form>

    {{-- CERTIFICATE LIST --}}
    <div class="space-y-6">
        @forelse($attendances as $att)
            @php
                $event = $att->event;
                $cert = $event->certificates->first();
                $isGenerated = !is_null($cert);
                
                // Deduce tags from event title
                $tags = ['Volunteer Work'];
                if (stripos($event->title, 'clean') !== false) {
                    $tags = ['Beach Clean-Up', 'Community Service'];
                } elseif (stripos($event->title, 'restoration') !== false) {
                    $tags = ['Coastal Restoration', 'Habitat Protection'];
                }
            @endphp
            
            <div 
                id="event-card-{{ $event->id }}"
                class="bg-white border border-gray-200 rounded-2xl p-5 flex flex-col md:flex-row gap-6 items-stretch md:items-center shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden"
            >
                <div class="w-full md:w-56 h-36 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center shrink-0 overflow-hidden relative">
                    @if($isGenerated)
                        <img 
                            src="{{ asset($cert->file_path) }}" 
                            alt="Certificate Preview" 
                            class="w-full h-full object-contain p-1.5"
                        >
                    @else
                        <!-- Ungenerated state placeholder -->
                        <div class="text-center p-4 flex flex-col items-center justify-center h-full">
                            <div class="bg-gray-100 text-gray-400 p-3 rounded-full border border-dashed border-gray-300 mb-2">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Ready to Generate</span>
                        </div>
                    @endif
                </div>

                {{-- Certificate Info --}}
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="text-base font-bold text-gray-900 leading-snug">{{ $event->title }}</h3>
                            
                            @if($isGenerated)
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-full shrink-0">
                                    Not Generated
                                </span>
                            @endif
                        </div>

                        {{-- Metadata --}}
                        <div class="flex flex-wrap gap-x-5 gap-y-1.5 mt-3 text-xs text-gray-600 font-medium">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                </svg>
                                {{ $event->location }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $event->duration }} hours
                            </span>
                        </div>


                    </div>

                    {{-- Actions block --}}
                    <div class="mt-6 flex flex-wrap gap-2.5 border-t border-gray-100/70 pt-4">
                        @if($isGenerated)
                            <a 
                                href="{{ route('certificates.download', $cert->id) }}" 
                                class="download-cert-btn bg-[#1e293b] hover:bg-black text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition duration-150 flex items-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Download Certificate
                            </a>


                        @else
                            <button 
                                type="button"
                                onclick="generateCertificate({{ $event->id }}, this)"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition duration-150 flex items-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>Generate Certificate</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-semibold text-gray-900">No certificates found</h3>
                <p class="text-xs text-gray-500 mt-1">Try adjusting your search filters or check your event participation.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL PREVIEW --}}
<div 
    id="previewModal" 
    class="fixed inset-0 z-50 overflow-y-auto hidden" 
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
>
    <!-- Dark glass overlay -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div 
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
            aria-hidden="true"
            onclick="closePreviewModal()"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900" id="modal-title">Certificate Preview</h3>
                <button 
                    onclick="closePreviewModal()"
                    class="text-gray-400 hover:text-gray-600 transition p-1"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content (Rendering the certificate SVG inline) -->
            <div class="p-6 bg-gray-50 flex items-center justify-center">
                <div id="modalCertImgContainer" class="w-full max-w-[calc(65vh*1.414)] aspect-[1120/792] bg-white border border-gray-200 rounded-xl shadow-inner p-2 md:p-4 flex items-center justify-center overflow-hidden">
                    <!-- SVG will be injected here dynamically -->
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-3">
                <button 
                    onclick="closePreviewModal()"
                    class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-xs font-semibold transition"
                >
                    Close
                </button>
                <a 
                    id="modalPrintBtn"
                    href="" 
                    target="_blank"
                    class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print / Save PDF
                </a>
                <a 
                    id="modalDownloadBtn"
                    href="" 
                    class="bg-[#1e293b] hover:bg-black text-white px-4 py-2 rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download SVG
                </a>
            </div>
        </div>
    </div>
</div>

{{-- FLOATING TOASTS (Alerts) --}}
<div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

@push('scripts')
<script>
    // Submit filters on change
    const filterForm = document.getElementById('filterForm');
    const selects = filterForm.querySelectorAll('select');
    selects.forEach(select => {
        select.addEventListener('change', () => filterForm.submit());
    });

    // Auto submit search with delay
    let searchTimeout;
    const searchInput = filterForm.querySelector('input[name="search"]');
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 600);
    });

    // Download All function
    function downloadAllCertificates() {
        const links = document.querySelectorAll('.download-cert-btn');
        if (links.length === 0) {
            showToast('info', 'Tidak ada sertifikat yang tersedia untuk diunduh.');
            return;
        }
        showToast('info', 'Memulai pengunduhan massal (mohon izinkan pop-up jika diminta)...');
        links.forEach((link, idx) => {
            setTimeout(() => {
                const a = document.createElement('a');
                a.href = link.href;
                a.download = '';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }, idx * 600);
        });
    }

    // Modal preview controls
    async function openPreviewModal(certId, filePath) {
        const modal = document.getElementById('previewModal');
        const imgContainer = document.getElementById('modalCertImgContainer');
        const printBtn = document.getElementById('modalPrintBtn');
        const downloadBtn = document.getElementById('modalDownloadBtn');

        printBtn.href = `/certificates/${certId}/preview`;
        downloadBtn.href = `/certificates/${certId}/download`;

        // Render loader
        imgContainer.innerHTML = `
            <div class="flex justify-center items-center py-12">
                <svg class="animate-spin h-8 w-8 text-sky-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        `;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Lock background scroll

        try {
            const response = await fetch(filePath + '?t=' + new Date().getTime());
            if (!response.ok) throw new Error('Network response was not ok');
            const svgText = await response.text();
            
            // Clean up xml metadata header if present
            const cleanSvg = svgText.replace(/<\?xml.*\?>/g, '');
            imgContainer.innerHTML = cleanSvg;

            // Make the inline SVG scale responsively inside the container
            const svgElement = imgContainer.querySelector('svg');
            if (svgElement) {
                svgElement.removeAttribute('width');
                svgElement.removeAttribute('height');
                svgElement.style.width = '100%';
                svgElement.style.height = '100%';
                svgElement.style.display = 'block';
            }
        } catch (err) {
            console.error('Failed to load SVG preview', err);
            imgContainer.innerHTML = '<p class="text-red-500 font-semibold py-8">Gagal memuat pratinjau sertifikat.</p>';
        }
    }

    function closePreviewModal() {
        const modal = document.getElementById('previewModal');
        const imgContainer = document.getElementById('modalCertImgContainer');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scroll
        // Clear container content to free memory and prevent previous certificate flashing
        imgContainer.innerHTML = '';
    }

    // Share Certificate copy link
    function shareCertificate(url, btnElement) {
        navigator.clipboard.writeText(url).then(() => {
            const span = btnElement.querySelector('span');
            const originalText = span.innerText;
            span.innerText = 'Copied!';
            btnElement.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-200');
            
            showToast('success', 'Tautan sertifikat berhasil disalin ke clipboard!');

            setTimeout(() => {
                span.innerText = originalText;
                btnElement.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-200');
            }, 2000);
        }).catch(err => {
            console.error('Sharing failed', err);
            showToast('error', 'Gagal menyalin tautan.');
        });
    }

    // Generate Certificate via AJAX
    async function generateCertificate(eventId, buttonElement) {
        // Show loading state
        const originalContent = buttonElement.innerHTML;
        buttonElement.disabled = true;
        buttonElement.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Generating...
        `;
        buttonElement.classList.add('opacity-75', 'cursor-not-allowed');

        try {
            const res = await fetch(`/certificates/${eventId}/generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showToast('success', 'Sertifikat digital berhasil dibuat!');
                
                // Reload container/page to show the updated certificate
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('error', data.message || 'Gagal membuat sertifikat.');
                buttonElement.disabled = false;
                buttonElement.innerHTML = originalContent;
                buttonElement.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        } catch (err) {
            console.error(err);
            showToast('error', 'Koneksi bermasalah. Silakan coba lagi.');
            buttonElement.disabled = false;
            buttonElement.innerHTML = originalContent;
            buttonElement.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }

    // Toast Alert notification helper
    function showToast(type, message) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        
        let bgClass = 'bg-[#1e293b] text-white';
        let icon = '';

        if (type === 'success') {
            bgClass = 'bg-emerald-600 text-white';
            icon = '<svg class="w-4 h-4 inline mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else if (type === 'error') {
            bgClass = 'bg-red-600 text-white';
            icon = '<svg class="w-4 h-4 inline mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else if (type === 'info') {
            bgClass = 'bg-sky-600 text-white';
            icon = '<svg class="w-4 h-4 inline mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        }

        toast.className = `${bgClass} flex items-center px-4 py-3 rounded-xl shadow-lg border border-white/10 transition-all duration-300 transform translate-y-2 opacity-0 text-xs font-semibold`;
        toast.innerHTML = `${icon}<span>${message}</span>`;
        
        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        }, 10);

        // Auto remove
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => {
                container.removeChild(toast);
            }, 300);
        }, 3500);
    }
</script>
@endpush
@endsection
