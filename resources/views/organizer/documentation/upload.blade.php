@extends('layouts.app')

@section('title', 'Upload Dokumentasi')

@section('content')
<div class="max-w-3xl mx-auto mt-10 px-4">

    <h1 class="text-2xl font-bold mb-6">Upload Dokumentasi Kegiatan</h1>

    {{-- Success --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow">

        <form action="{{ route('documentation.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Event ID --}}
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            {{-- Upload File --}}
            <div class="mb-4">
                <label class="block font-medium mb-1">Upload File</label>
                <input type="file" name="file"
                    class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required>
                <p class="text-sm text-gray-500 mt-1">JPG / PNG (Max 2MB)</p>
            </div>

            {{-- Preview --}}
            <div class="mb-4">
                <img id="preview" class="hidden max-h-48 rounded shadow" />
            </div>

            {{-- Note --}}
            <div class="mb-4">
                <label class="block font-medium mb-1">Catatan (Note)</label>
                <textarea name="note"
                    class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    rows="3"
                    placeholder="Tambahkan catatan (opsional)"></textarea>
            </div>

            {{-- Button --}}
            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Upload
                </button>

                <a href="{{ route('events.detail', $event->id) }}"
                   class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 transition">
                    Kembali
                </a>
            </div>

        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('input[name="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    }

    if (file && file.size > 2 * 1024 * 1024) {
        alert('File maksimal 2MB');
        e.target.value = '';
        preview.classList.add('hidden');
    }
});
</script>
@endpush