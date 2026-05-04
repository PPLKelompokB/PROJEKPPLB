<section class="bg-[#4a4a4a] text-white text-center py-32 px-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl md:text-5xl font-semibold mb-6 tracking-tight text-gray-100 leading-tight">
            Protect Our Ocean, Join Beach Clean-Up Activities
        </h1>

        <p class="text-lg text-gray-300 mb-10 max-w-2xl mx-auto font-light">
            Connect with environmental organizations and make a difference in marine ecosystem protection
        </p>

        @auth
            <a href="/events"
                class="inline-block bg-[#9ca3af] hover:bg-gray-400 text-black px-8 py-3 rounded-md font-medium transition duration-200">
                Join as Volunteer
            </a>
        @else
            <button onclick="openModal()"
                class="inline-block bg-[#9ca3af] hover:bg-gray-400 text-black px-8 py-3 rounded-md font-medium transition duration-200">
                Join as Volunteer
            </button>
        @endauth
    </div>
</section>