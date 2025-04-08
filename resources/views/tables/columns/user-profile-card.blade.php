@php
    $user = $getRecord();
@endphp
<head>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<div class="bg-white p-8 rounded-2xl shadow-sm w-full max-w-4xl mx-auto">
     
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
        {{-- Full Name --}}
        <div class="">
            <label class="block mb-1 font-medium text-gray-700 pt-2">Full Name</label>
            <div class="relative">
                <input type="text" value="{{ $user->first_name }} {{ $user->last_name }}"
                       class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:ring focus:ring-purple-100"
                       disabled>
                <x-heroicon-o-user class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" />
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700  pt-2">Email Address</label>
            <div class="relative">
                <input type="email" value="{{ $user->email }}"
                       class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:ring focus:ring-purple-100"
                       disabled>
                <x-heroicon-o-envelope class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" />
            </div>
        </div>

        {{-- Phone Number --}}
        <div>
            <label class="block mb-2 font-medium text-gray-700">Phone Number</label>
            <div class="relative">
                <input type="text" value="{{ $user->phone_number }}"
                       class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:ring focus:ring-purple-100"
                       disabled>
                <x-heroicon-o-phone class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" />
            </div>
        </div>

        {{-- Address --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Address</label>
            <div class="relative">
                <input type="text" value="{{ $user->address ?? 'N/A' }}"
                       class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 focus:ring focus:ring-purple-100"
                       disabled>
                <x-heroicon-o-map class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" />
            </div>
        </div>
    </div>

    {{-- Change Avatar --}}
    <div class="mt-10">
        <label class="block mb-2 font-medium text-gray-700 mt-2">Change Avatar</label>
        <div class="flex items-center gap-6">
            {{-- Avatar Image --}}
            <img src="{{ $user->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name) }}"
                 alt="avatar"
                 class="w-16 h-16 rounded-full border border-gray-300">

            {{-- Upload Box (placeholder for now) --}}
            <div class="border border-dashed border-gray-300 rounded-xl px-6 py-4 text-center w-full max-w-md">
                <div class="text-purple-600 font-semibold cursor-pointer">
                    Click here
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    to upload your file or edit your profile.
                </div>
                <!-- <div class="text-xs text-gray-400 mt-2">
                    Supported Format: SVG, JPG, PNG (10mb each)
                </div> -->
            </div>
        </div>
    </div>
</div>
