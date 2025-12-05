@extends('layouts.app')

@section('content')

    <x-job-detail :jobPosting="$application->jobPosting" />

@endsection