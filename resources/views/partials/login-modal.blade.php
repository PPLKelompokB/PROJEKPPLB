<div id="loginModal"
    class="fixed inset-0 flex items-center justify-center z-50 opacity-0 pointer-events-none transition duration-300">

    <div id="modalBackdrop"
        class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300"
        onclick="closeModal()">
    </div>

    <div id="modalContent"
        class="relative bg-white rounded-2xl shadow-2xl w-96 p-6 text-center
               transform scale-90 opacity-0 transition duration-300">

        <h2 class="text-xl font-bold mb-4">OceanCare</h2>

        <div class="bg-gray-100 p-4 rounded mb-4">
            <p class="font-semibold text-gray-800">Login Required</p>
            <p class="text-sm text-gray-600">
                Please login first to access this feature.
            </p>
        </div>

        <div class="flex justify-center gap-3">
            <button onclick="closeModal()"
                class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded transition">
                Cancel
            </button>

            <button onclick="redirectToLogin()"
                class="bg-black hover:bg-gray-800 text-white px-4 py-2 rounded transition">
                Login
            </button>
        </div>
    </div>
</div>