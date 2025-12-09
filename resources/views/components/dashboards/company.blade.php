<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-xl mt-6">
    <div class="p-6 text-gray-900 dark:text-gray-100">

        {{-- Quick Actions --}}
        <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Manajemen Perusahaan</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('company.job-postings.index') }}"
               class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                Kelola Lowongan
            </a>
            <a href="{{ route('company.job-postings.create') }}"
               class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition duration-300">
                Tambah Lowongan Baru
            </a>
        </div>

        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            Lihat, buat, dan edit lowongan perusahaan Anda.
        </p>
    </div>
</div>

{{-- Statistik Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
    @foreach ([
        ['Total Lowongan', $jobPostingsCount ?? '-', 'primary'],
        ['Total Pelamar', $totalApplicantsCount ?? '-', 'green'],
        ['Diterima', $hiredCandidatesCount ?? '-', 'primary'],
        ['Lowongan Aktif', $activeJobPostingsCount ?? '-', 'orange'],
    ] as [$title, $value, $color])
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
            <p class="text-gray-500 text-sm">{{ $title }}</p>
            <h3 class="text-3xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400 mt-2">{{ $value }}</h3>
        </div>
    @endforeach
</div>

{{-- ... Bagian Header & Card Statistik (Kode Anda sebelumnya) ... --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
    
    {{-- Chart Section (Lebar 2/3) --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Tren Pendaftar (30 Hari Terakhir)</h3>
        <div class="relative h-80 w-full">
            <canvas id="companyChart"></canvas>
        </div>
    </div>

    {{-- Tabel Pelamar Terbaru (Lebar 1/3) --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md overflow-hidden">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Pelamar Terbaru</h3>
        <div class="overflow-y-auto max-h-80">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-3 py-2">Nama</th>
                        <th scope="col" class="px-3 py-2">Posisi</th>
                        <th scope="col" class="px-3 py-2">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobSeekerApplyAt as $applicant)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                {{ $applicant->first_name }} {{ $applicant->last_name }}
                            </td>
                            <td class="px-3 py-2">
                                {{ $applicant->position_name }}
                            </td>
                            <td class="px-3 py-2">
                                {{ \Carbon\Carbon::parse($applicant->created_at)->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center">Belum ada pelamar baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Persiapan Data Chart dari Controller
    // Kita gunakan map untuk memisahkan tanggal dan jumlah
    @php
        // Ubah format tanggal jadi lebih cantik (misal: 10 Oct)
        $labels = $chartData->map(fn($item) => \Carbon\Carbon::parse($item->date)->format('d M'));
        $values = $chartData->map(fn($item) => $item->aggregate);
    @endphp

    const ctx = document.getElementById('companyChart').getContext('2d');

    new Chart(ctx, {
        type: 'line', // Bisa diganti 'bar' jika ingin diagram batang
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Jumlah Pelamar',
                data: @json($values),
                backgroundColor: 'rgba(59, 130, 246, 0.2)', // Biru transparan
                borderColor: 'rgba(59, 130, 246, 1)',       // Biru solid
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: 'rgba(59, 130, 246, 1)',
                tension: 0.3, // Membuat garis sedikit melengkung
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1 // Agar sumbu Y tidak menampilkan desimal (0.5 orang)
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush