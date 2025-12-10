<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Paket Langganan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Ups!</strong> Ada masalah.<br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.subscription_plans.update', $subscriptionPlan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nama Paket (plan_name) --}}
                        <div class="mb-4">
                            <label for="plan_name" class="block text-gray-700 text-sm font-bold mb-2">Nama Paket:</label>
                            <input type="text" name="plan_name" id="plan_name" value="{{ old('plan_name', $subscriptionPlan->plan_name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        {{-- Price --}}
                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp):</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $subscriptionPlan->price) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="0" required>
                        </div>

                        {{-- Duration --}}
                        <div class="mb-4">
                            <label for="duration_days" class="block text-gray-700 text-sm font-bold mb-2">Durasi (Hari):</label>
                            <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $subscriptionPlan->duration_days) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
                        </div>

                        {{-- Job Post Limit --}}
                        <div class="mb-4">
                            <label for="job_post_limit" class="block text-gray-700 text-sm font-bold mb-2">Batas Posting Lowongan:</label>
                            <input type="number" name="job_post_limit" id="job_post_limit" value="{{ old('job_post_limit', $subscriptionPlan->job_post_limit) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
                        </div>

                        {{-- Allow Verified Badge --}}
                        <div class="mb-4">
                            <label for="allow_verified_badge" class="block text-gray-700 text-sm font-bold mb-2">Dapat Verified Badge?</label>
                            <select name="allow_verified_badge" id="allow_verified_badge" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="0" {{ old('allow_verified_badge', $subscriptionPlan->allow_verified_badge) == '0' ? 'selected' : '' }}>Tidak</option>
                                <option value="1" {{ old('allow_verified_badge', $subscriptionPlan->allow_verified_badge) == '1' ? 'selected' : '' }}>Ya</option>
                            </select>
                        </div>

                        {{-- IS ACTIVE (STATUS AKTIF/NONAKTIF) --}}
                        <div class="mb-4">
                            <label for="is_active" class="block text-gray-700 text-sm font-bold mb-2">Status Paket:</label>
                            <select name="is_active" id="is_active" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                {{-- Jika nilai dari DB adalah 1 (true), maka pilih Aktif --}}
                                <option value="1" {{ old('is_active', $subscriptionPlan->is_active) == '1' ? 'selected' : '' }}>Aktif (Tersedia untuk dijual)</option>
                                {{-- Jika nilai dari DB adalah 0 (false), maka pilih Nonaktif --}}
                                <option value="0" {{ old('is_active', $subscriptionPlan->is_active) == '0' ? 'selected' : '' }}>Nonaktif (Tidak dijual)</option>
                            </select>
                        </div>
                        
                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('admin.subscription_plans.index') }}" class="text-gray-600 hover:underline">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Paket
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>