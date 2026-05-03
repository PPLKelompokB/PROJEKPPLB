@extends('layouts.app')

@section('title', 'OceanCare')

@section('content')

<x-landing.hero />

<x-landing.stats 
    :volunteers="$totalVolunteers" 
    :events="$totalEvents" 
/>

<x-landing.events-section :events="$events" />

<x-landing.mission />

{{-- Modal dipisah --}}
@include('partials.login-modal')

@endsection


@push('scripts')
<script>
const modal = document.getElementById('loginModal');
const modalContent = document.getElementById('modalContent');

function openModal() {
    modal.classList.remove('opacity-0', 'pointer-events-none');

    setTimeout(() => {
        modalContent.classList.remove('scale-90', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeModal() {
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-90', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('opacity-0', 'pointer-events-none');
    }, 200);
}

function redirectToLogin() {
    window.location.href = "/login";
}

document.addEventListener('keydown', function (e) {
    if (e.key === "Escape") {
        closeModal();
    }
});
</script>
@endpush