@extends('layouts.app')

@section('content')
    <x-job-detail :jobPosting="$jobPosting" 
        :matchedSkills="$matchedSkills" 
        :matchCount="$matchCount" 
        :isExpired="$isExpired" 
        :isUrgent="$isUrgent" 
        :hoursLeft="$hoursLeft" 
        :isCompanyOwner="$isCompanyOwner" 
        :hasApplied="$hasApplied"/>
@endsection