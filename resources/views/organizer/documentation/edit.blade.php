@extends('layouts.app')

@section('title', 'Edit Documentation - OceanCare')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- Error / Success messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
                Edit Event Documentation
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Update the photo or description for this documentation.
            </p>
        </div>
        <a href="{{ route('documentation.index', ['event_id' => $documentation->event_id]) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition">
            Back
        </a>
    </div>

    {{-- Edit Documentation Form --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-8">
        <form action="{{ route('documentation.update', $documentation->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Photo (Upload new to replace)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition cursor-pointer relative overflow-hidden group h-64 flex flex-col justify-center items-center">
                        <img src="{{ Storage::url($documentation->file_path) }}" alt="Current Photo" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-30 transition">
                        <input type="file" name="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".jpg,.jpeg,.png" onchange="previewFile(this)">
                        
                        <div class="z-0 relative pointer-events-none bg-white/80 p-4 rounded-lg">
                            <svg class="mx-auto h-8 w-8 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-700 font-medium">Click or Drag to replace photo</p>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 10MB</p>
                        </div>
                    </div>
                    <p id="fileNameDisplay" class="mt-2 text-sm text-blue-600 font-medium hidden text-center"></p>
                </div>
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo Description</label>
                    <textarea name="note" rows="5" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-1 focus:ring-black focus:border-black outline-none transition flex-grow" placeholder="Describe what's happening in this photo...">{{ old('note', $documentation->note) }}</textarea>
                    
                    <button type="submit" class="mt-4 w-full bg-[#1a1c20] hover:bg-black text-white py-3 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewFile(input) {
    const file = input.files[0];
    const display = document.getElementById('fileNameDisplay');
    if (file) {
        display.textContent = "New file selected: " + file.name;
        display.classList.remove('hidden');
    } else {
        display.textContent = '';
        display.classList.add('hidden');
    }
}
</script>
@endpush
