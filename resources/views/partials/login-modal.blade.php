<div id="loginModal"
    class="fixed inset-0 flex items-center justify-center z-50 opacity-0 pointer-events-none transition duration-300">

    <div id="modalBackdrop"
        class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"
        onclick="closeModal()">
    </div>

    <div id="modalContent"
        class="relative bg-white rounded-3xl shadow-2xl w-[22rem] overflow-hidden text-center
               transform scale-90 opacity-0 transition duration-300 flex flex-col">

        <!-- Logo Section -->
        <div class="pt-8 pb-6 flex flex-col items-center">
            <svg class="w-12 h-12 text-gray-800" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="none" stroke="currentColor" stroke-width="2"/>
                <path d="M6 10c1.5 0 2-1 3.5-1s2 1 3.5 1 2-1 3.5-1 2 1 3.5 1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
                <path d="M8 14c1.5 0 2-1 3.5-1s2 1 3.5 1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>
            </svg>
            <h2 class="text-xl font-bold text-gray-800 tracking-tight mt-2">OceanCare</h2>
        </div>

        <!-- Warning Card -->
        <div class="px-6 pb-6">
            <div class="bg-[#8b8e93] rounded-2xl p-6 text-[#1a1c20] shadow-inner flex flex-col items-center">
                <svg class="w-8 h-8 mb-3 opacity-90" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                
                <h3 class="font-bold text-lg mb-2">Login Required</h3>
                
                <p class="text-[13px] font-medium opacity-80 mb-6 leading-relaxed px-1">
                    Please login first if you want to see the content. Access to our ocean conservation resources requires authentication.
                </p>

                <button onclick="redirectToLogin()"
                    class="bg-[#6b7280] hover:bg-[#5b616e] text-white px-6 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    OK
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-[#5c6066] text-gray-300 text-xs py-3 flex items-center justify-center gap-2">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Secure authentication required
        </div>
    </div>
</div>