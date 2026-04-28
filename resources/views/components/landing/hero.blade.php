<section class="bg-gray-700 text-white text-center py-20">
    <h1 class="text-4xl font-bold mb-4">
        Protect Our Ocean, Join Beach Clean-Up Activities
    </h1>

    <p class="mb-6">
        Connect with environmental organizations and make a difference
    </p>

    @auth
        <a href="/events"
            class="bg-white text-black px-6 py-3 rounded">
            Join as Volunteer
        </a>
    @else
        <button onclick="openModal()"
            class="bg-white text-black px-6 py-3 rounded">
            Join as Volunteer
        </button>
    @endauth
</section>