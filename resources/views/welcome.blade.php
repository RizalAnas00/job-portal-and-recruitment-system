<!-- resources/views/landing.blade.php -->
@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-600 to-indigo-700 text-white pt-40 pb-20">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-6">Temukan Pekerjaan Impianmu</h1>
            <p class="text-lg md:text-xl text-blue-100 mb-8">Jelajahi ribuan lowongan pekerjaan dari perusahaan terpercaya di seluruh Indonesia.</p>

            <!-- Search Bar -->
            <form action="#" method="GET" class="max-w-2xl mx-auto flex bg-white rounded-full overflow-hidden shadow-lg">
                <input type="text" name="query" placeholder="Cari pekerjaan, posisi, atau perusahaan..."
                    class="flex-1 px-6 py-3 text-gray-700 focus:outline-none" />
                <button type="submit"
                    class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 font-semibold transition">
                    Cari
                </button>
            </form>
        </div>

        <!-- Decorative Shapes -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
    </section>

    <!-- Category Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Jelajahi Berdasarkan Kategori</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach (['Teknologi', 'Desain', 'Marketing', 'Keuangan', 'Pendidikan', 'Kesehatan', 'Manufaktur', 'Lainnya'] as $category)
                    <a href="#"
                        class="p-6 bg-white dark:bg-gray-700 rounded-2xl shadow hover:shadow-lg hover:-translate-y-1 transition text-center">
                        <span class="block text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $category }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Jobs Section -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">Lowongan Terbaru</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @for ($i = 1; $i <= 3; $i++)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">Frontend Developer</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">PT Digital Inovasi Nusantara · Jakarta</p>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Bergabung dengan tim kreatif kami dan bantu kembangkan aplikasi inovatif.</p>
                        <a href="#" class="text-primary-600 dark:text-primary-400 font-semibold hover:underline">Lihat Detail →</a>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-br from-indigo-600 to-blue-600 text-white text-center">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-4">Perusahaan Anda Sedang Mencari Talenta?</h2>
            <p class="text-lg text-blue-100 mb-8">Pasang lowongan dan temukan kandidat terbaik dalam hitungan menit.</p>
            <a href="#" class="bg-white text-indigo-700 font-semibold px-6 py-3 rounded-full shadow hover:bg-gray-100 transition">
                Pasang Lowongan Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 text-center py-6 text-sm">
        © {{ date('Y') }} JobFinder. All rights reserved.
    </footer>
</div>

<!-- Tailwind blob animation -->
<style>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(30px, -20px) scale(1.1); }
}
.animate-blob { animation: blob 8s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
</style>
@endsection
