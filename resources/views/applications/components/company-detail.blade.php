@props(['application'])

<div class="space-y-5">

    {{-- STATUS UPDATE --}}
    <div class="rounded-lg border bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 p-4">
        <div class="flex justify-between items-center mb-3">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 font-semibold">Status Lamaran:</p>
                <span class="text-lg font-bold
                    @if($application->status === 'rejected') text-red-500
                    @elseif(in_array($application->status, ['reviewed'])) text-yellow-500
                    @elseif(in_array($application->status, ['interviewing','interview_scheduled'])) text-primary-500
                    @elseif(in_array($application->status, ['accepted','hired','offered'])) text-green-500
                    @else text-gray-400
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                </span>
            </div>

            <form method="POST" action="{{ route('company.applications.update', $application) }}" class="flex items-center">
                @csrf
                @method('PATCH')

                <x-select-field-one
                    name="status"
                    :options="[
                        'pending' => 'Sedang Diproses',
                        'reviewed' => 'Ditinjau',
                        'interview_scheduled' => 'Wawancara Dijadwalkan',
                        'interviewing' => 'Sedang Wawancara',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                    ]"
                    :selected="$application->status"
                    class="text-sm min-w-52"
                />

                <button type="submit"
                    class="ml-2 px-3 py-3 text-xs rounded-md bg-primary-600 text-white hover:bg-primary-700">
                    Update
                </button>
            </form>
        </div>

        <p class="text-xs text-gray-500">
            Dilamar pada: <span class="font-semibold">{{ $application->created_at->format('d F Y H:i') }}</span>
        </p>
    </div>


    {{-- PELAMAR INFO --}}
    <div class="rounded-lg border border-gray-300 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">

        <div class="flex gap-4 items-start">

            {{-- Foto --}}
            <img src="{{ asset($application->jobSeeker->profile_picture_path) ?? asset('default-user.png') }}"
                 class="w-20 h-20 rounded-lg object-cover border dark:border-gray-600">

            <div class="flex-1">
                <h3 class="text-xl font-semibold">
                    {{ $application->jobSeeker->full_name }}
                </h3>

                <p class="text-gray-600 dark:text-gray-400">
                    {{ $application->jobSeeker->user->email }}
                </p>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $application->jobSeeker->bio ?? 'No bio provided.' }}
                </p>

                {{-- Resume View --}}
                <a href="{{ route('user.resume.view', $application->jobSeeker->user_id) }}"
                    class="inline-block mt-3 px-3 py-1 rounded-md shadow text-sm 
                           bg-primary-600 text-white hover:bg-primary-700">
                    📄 Lihat Resume
                </a>
            </div>
        </div>

        {{-- Skills --}}
        <div class="mt-4">
            <span class="font-semibold text-sm">Skill:</span>
            <div class="flex flex-wrap gap-2 mt-1">
                @forelse($application->jobSeeker->skills as $skill)
                    <span class="px-2 py-1 border text-xs rounded-md bg-gray-100 dark:bg-gray-700 
                                 dark:border-gray-600">
                        {{ $skill->skill_name }}
                    </span>
                @empty
                    <span class="text-sm text-gray-500">Tidak ada skill</span>
                @endforelse
            </div>
        </div>

        {{-- Cover Letter --}}
        @if($application->cover_letter)
            <div class="mt-6">
                <span class="font-semibold">Cover Letter:</span>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 border rounded-md mt-1 dark:border-gray-600">
                    {!! nl2br(e($application->cover_letter)) !!}
                </div>
            </div>
        @endif

    </div>
</div>
