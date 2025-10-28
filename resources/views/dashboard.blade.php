<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
           {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <x-slot name="breadcrumb">
        Dashboard
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-5 lg:px-7">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p>
                        Anda Sudah Login sebagai
                        <span class="ml-1 font-extrabold text-primary-600 dark:text-primary-300">
                            {{ strtoupper(Auth::user()->getRoleName()) }}
                        </span>
                        dengan email
                        <span class="ml-1 font-extrabold text-primary-600 dark:text-primary-300">
                            {{ Auth::user()->email }}
                        </span>
                    </p>

                    {{-- ===== Tombol Aksi Berdasarkan Role Pengguna ===== --}}
                    <div class="mt-6 border-t dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Langkah Selanjutnya</h3>
                        @if (Auth::user()->hasRole('company'))
                        
                        @if (!Auth::user()->company)
                            <div class="p-4 rounded-lg bg-yellow-100 dark:bg-yellow-900 border border-yellow-300 dark:border-yellow-700">
                                <p class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                    Akun Anda hampir siap!
                                </p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-4">
                                    Harap lengkapi profil perusahaan Anda untuk dapat memposting lowongan pekerjaan.
                                </p>
                                {{-- Ini mengarah ke route 'create profile' yang kita buat di routes/web.php --}}
                                <a href="{{ route('company.profile.create') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                    Lengkapi Profil Perusahaan
                                </a>
                            </div>
                        @else
                            <a href="{{ route('company.job-postings.index') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                Kelola Lowongan Pekerjaan
                            </a>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Lihat, tambah, atau edit lowongan yang diposting oleh perusahaan Anda.</p>
                        @endif

                        @elseif (Auth::user()->hasRole('user'))
                            @if (!Auth::user()->user)
                                <div class="p-4 rounded-lg bg-yellow-100 dark:bg-yellow-900 border border-yellow-300 dark:border-yellow-700">
                                    <p class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                        Selamat datang, Pencari Kerja!
                                    </p>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-4">
                                        Harap lengkapi profil Anda untuk dapat melamar pekerjaan dengan mudah.
                                    </p>
                                    {{-- PERINGATAN: Anda perlu membuat route 'user.profile.create' --}}
                                    <a href="{{ route('user.profile.create') }}" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                        Lengkapi Profil Anda
                                    </a>
                                </div> {{-- Ganti 'user' jika nama role pencari kerja berbeda --}}
                            @else
                                <a href="{{ route('job-postings.index') }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                    Cari Lowongan Pekerjaan
                                </a>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Jelajahi semua lowongan pekerjaan yang tersedia dan temukan karir impian Anda.</p>
                            @endif

                        @elseif (Auth::user()->hasRole('admin'))
                             <a href="#" class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                                Buka Panel Admin
                            </a>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Akses panel administrasi untuk mengelola pengguna dan konten.</p>
                        @endif
                    </div>
                    {{-- ================================================= --}}

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
