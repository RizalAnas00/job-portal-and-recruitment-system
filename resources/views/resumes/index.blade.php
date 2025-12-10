@extends('resumes.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{ $jobPosting }}</h1>
    <livewire:resume-list :jobPostingId="$jobPosting->id"/>
@endsection