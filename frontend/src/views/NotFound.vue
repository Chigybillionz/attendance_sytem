<template>
  <div class="min-h-screen bg-blue-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">About Our Attendance System</h1>
        <p class="text-xl text-gray-600">
          Simplifying attendance tracking for modern organizations
        </p>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <div class="relative py-8">
          <!-- Mission Section (Icon on Right) -->
          <div class="relative flex items-start gap-8 py-8 mb-12">
            <!-- Vertical line for Mission (on the right side) -->
            <div class="absolute right-[64px] top-0 h-[calc(100%+6rem)] w-0.5 bg-blue-600"></div>

            <!-- Content -->
            <div class="flex-1 pt-8 text-right pr-8">
              <h2 class="text-4xl font-bold text-blue-600 mb-6">Our Mission</h2>
              <p class="text-gray-900 text-lg leading-relaxed">
                To help in securing organizations assets using best practice standards and
                frameworks.
              </p>
            </div>

            <!-- Icon circle -->
            <div class="relative z-10 flex-shrink-0">
              <div
                class="w-32 h-32 rounded-full bg-blue-100 flex items-center justify-center shadow-lg"
              >
                <img src="/mission.png" alt="Mission Icon" class="w-20 h-20" />
              </div>
            </div>
          </div>

          <!-- Vision Section (Icon on Left) -->
          <div class="relative flex items-start gap-8 py-8">
            <!-- Vertical line for Vision (on the left side) -->
            <div class="absolute left-[64px] top-0 bottom-0 w-0.5 bg-blue-600"></div>

            <!-- Icon circle -->
            <div class="relative z-10 flex-shrink-0">
              <div
                class="w-32 h-32 rounded-full bg-blue-100 flex items-center justify-center shadow-lg"
              >
                <img src="/vison.png" alt="Vision Icon" class="w-20 h-20" />
              </div>
            </div>

            <!-- Content -->
            <div class="flex-1 pt-8 pl-8">
              <h2 class="text-4xl font-bold text-blue-600 mb-6">Our Vision</h2>
              <p class="text-gray-900 text-lg leading-relaxed">
                To be the most respected Information Technology assurance and GRC firm of African
                Origin
              </p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-900 mb-4 mt-12">Key Features</h2>
        <div class="grid md:grid-cols-2 gap-6 mb-6">
          <div class="border-l-4 border-blue-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-blue-600 pb-1 w-fit">
              Instant Visibility
            </h3>
            <p class="text-gray-600">
              Managers can monitor attendance as it happens, allowing quick responses to issues like
              tardiness or unexpected absences.
            </p>
          </div>

          <div class="border-l-4 border-blue-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-blue-600 pb-1 w-fit">
              Location Verification
            </h3>
            <p class="text-gray-600">
              Integrates GPS, geofencing, or Wi-Fi to confirm employees are at approved work sites,
              reducing time theft (e.g., buddy punching).
            </p>
          </div>

          <div class="border-l-4 border-blue-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-blue-600 pb-1 w-fit">
              Proactive Management
            </h3>
            <p class="text-gray-600">
              Provides alerts for approaching overtime or low staffing, helping optimize labor costs
              and scheduling in real time.
            </p>
          </div>

          <div class="border-l-4 border-green-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-green-500 pb-1 w-fit">
              Comprehensive Reports
            </h3>
            <p class="text-gray-600">
              Generate detailed reports for better decision-making and compliance.
            </p>
          </div>

          <div class="border-l-4 border-purple-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-purple-500 pb-1 w-fit">
              User-Friendly Interface
            </h3>
            <p class="text-gray-600">
              Intuitive design that requires minimal training to get started.
            </p>
          </div>

          <div class="border-l-4 border-orange-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-orange-500 pb-1 w-fit">
              Secure & Reliable
            </h3>
            <p class="text-gray-600">
              Enterprise-grade security to protect your sensitive attendance data.
            </p>
          </div>
        </div>
      </div>

      <!-- Contact/Support Section -->
    </div>
  </div>
</template>
