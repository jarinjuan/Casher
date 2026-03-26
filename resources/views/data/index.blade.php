@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Import / export') }}</h2>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        @if(session('success'))
            <div class="flash-success">
                {{ session('success') }}
                @if(session('import_errors'))
                    <div class="mt-3 text-sm space-y-1">
                        @foreach(session('import_errors') as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if($errors->any())
            <div class="flash-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6 flex flex-col">
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-[#fbbf24]/10">
                        <i class="fa-solid fa-download text-[#fbbf24]"></i>
                    </div>
                    <h3 class="text-lg font-bold t-primary">{{ __('Export data') }}</h3>
                </div>
                <form method="POST" action="{{ route('data.export') }}" class="flex-1 flex flex-col">
                    @csrf
                    <div class="flex-1 space-y-5 mb-5">
                        <div>
                            <label class="label-dark mb-2">{{ __('What to export:') }}</label>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="types[]" value="transactions" class="w-4 h-4 rounded border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24] bg-gray-50 dark:bg-white/5">
                                    <span class="text-sm t-secondary">{{ __('Transactions') }}</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="types[]" value="categories" class="w-4 h-4 rounded border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24] bg-gray-50 dark:bg-white/5">
                                    <span class="text-sm t-secondary">{{ __('Categories') }}</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="types[]" value="investments" class="w-4 h-4 rounded border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24] bg-gray-50 dark:bg-white/5">
                                    <span class="text-sm t-secondary">{{ __('Investments') }}</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="label-dark mb-2">{{ __('Format:') }}</label>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="format" value="xlsx" class="w-4 h-4 border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24]">
                                    <span class="text-sm t-secondary">{{ __('Excel (.xlsx)') }}</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="format" value="csv" class="w-4 h-4 border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24]">
                                    <span class="text-sm t-secondary">{{ __('CSV') }}</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="format" value="pdf" class="w-4 h-4 border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24]">
                                    <span class="text-sm t-secondary">{{ __('PDF report') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="btn-primary w-full text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i> {{ __('Download') }}
                    </button>
                </form>
            </div>

            @if(auth()->user()->canEdit(auth()->user()->current_team_id))
                <div class="card p-6 flex flex-col">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-[#8b5cf6]/10">
                                <i class="fa-solid fa-upload text-[#8b5cf6]"></i>
                            </div>
                            <h3 class="text-lg font-bold t-primary">{{ __('Import data') }}</h3>
                        </div>
                        <a href="{{ route('data.template') }}" class="btn-secondary text-xs px-3 py-2">
                            <i class="fa-solid fa-file-csv mr-1"></i> {{ __('Sample CSV') }}
                        </a>
                    </div>
                    <form method="POST" action="{{ route('data.import') }}" enctype="multipart/form-data" class="flex-1 flex flex-col">
                        @csrf
                        <div class="flex-1 space-y-5 mb-5">
                            <div>
                                <label class="label-dark mb-2">{{ __('Select file:') }}</label>
                                <input type="file" name="file" accept=".xlsx,.csv,.xls" class="w-full px-4 py-3 border border-gray-300 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 t-secondary cursor-pointer file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#fbbf24] file:text-black hover:file:bg-[#f59e0b]" required>
                                <p class="text-xs t-muted mt-2">{{ __('Supported: .xlsx, .csv, .xls') }}</p>
                            </div>
                            <div class="bg-[#8b5cf6]/5 border border-[#8b5cf6]/10 rounded-xl p-4">
                                <p class="text-xs text-[#7c3aed] dark:text-[#a78bfa]">
                                    <strong>{{ __('Note:') }}</strong> {{ __('Import will merge data with existing records.') }}
                                </p>
                            </div>
                        </div>
                        <button class="w-full bg-[#8b5cf6] hover:bg-[#7c3aed] text-white font-bold py-2.5 rounded-lg transition shadow-lg shadow-[#8b5cf6]/10 text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-upload"></i> {{ __('Import') }}
                        </button>
                    </form>
                </div>
            @endif
        </div>


    </div>
@endsection
