@extends('layouts.master_dashboard_layout')
@section('main_content')

<div class="flex items-center space-x-4 py-5 lg:py-6">
    <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
        Add a Scorm Package
    </h2>
    <div class="hidden h-full py-1 sm:flex">
        <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
    </div>
    <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
        <li class="flex items-center space-x-2">
            <a class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent" href="{{ route('admin.scorm.index', ['prefix' => 'admin'])}}">All Scorms</a>
            <svg x-ignore="" xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </li>
        <li>Help</li>
    </ul>
</div>

<div class="grid grid-cols-8 gap-4 sm:gap-5 lg:gap-6">
    <div class="col-span-12 sm:col-span-8">

        <div class="card p-4 sm:p-5">

            <div class="container">
                <h1>Upload SCORM Package</h1>
                <form action="{{ route('admin.scorm.store', ['prefix' => 'admin']) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Package Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="file">SCORM Package (ZIP)</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>

        </div>

    </div>


</div>

@endsection
