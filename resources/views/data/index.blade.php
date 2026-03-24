@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Import / Export</h2>
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
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-[#fbbf24]/10">
                        <i class="fa-solid fa-download text-[#fbbf24]"></i>
                    </div>
                    <h3 class="text-lg font-bold t-primary">Export Data</h3>
                </div>
                <form method="POST" action="{{ route('data.export') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="label-dark mb-2">What to export:</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="types[]" value="transactions" class="w-4 h-4 rounded border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24] bg-gray-50 dark:bg-white/5">
                                <span class="text-sm t-secondary">Transactions</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="types[]" value="categories" class="w-4 h-4 rounded border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24] bg-gray-50 dark:bg-white/5">
                                <span class="text-sm t-secondary">Categories</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="types[]" value="investments" class="w-4 h-4 rounded border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24] bg-gray-50 dark:bg-white/5">
                                <span class="text-sm t-secondary">Investments</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="label-dark mb-2">Format:</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="format" value="xlsx" class="w-4 h-4 border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24]">
                                <span class="text-sm t-secondary">Excel (.xlsx)</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="format" value="csv" class="w-4 h-4 border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24]">
                                <span class="text-sm t-secondary">CSV</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="format" value="pdf" class="w-4 h-4 border-gray-300 dark:border-white/20 text-[#fbbf24] focus:ring-[#fbbf24]">
                                <span class="text-sm t-secondary">PDF Report</span>
                            </label>
                        </div>
                    </div>
                    <button class="btn-primary w-full text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i> Download
                    </button>
                </form>
            </div>

            <div class="card p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-[#8b5cf6]/10">
                            <i class="fa-solid fa-upload text-[#8b5cf6]"></i>
                        </div>
                        <h3 class="text-lg font-bold t-primary">Import Data</h3>
                    </div>
                    <a href="{{ route('data.template') }}" class="btn-secondary text-xs px-3 py-2">
                        <i class="fa-solid fa-file-csv mr-1"></i> Sample CSV
                    </a>
                </div>
                <form method="POST" action="{{ route('data.import') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label class="label-dark mb-2">Select file:</label>
                        <input type="file" name="file" accept=".xlsx,.csv,.xls" class="w-full px-4 py-3 border border-gray-300 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 t-secondary cursor-pointer file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#fbbf24] file:text-black hover:file:bg-[#f59e0b]" required>
                        <p class="text-xs t-muted mt-2">Supported: .xlsx, .csv, .xls</p>
                    </div>
                    <div class="bg-[#8b5cf6]/5 border border-[#8b5cf6]/10 rounded-xl p-4">
                        <p class="text-xs text-[#7c3aed] dark:text-[#a78bfa]">
                            <strong>Note:</strong> Import will merge data with existing records.
                        </p>
                    </div>
                    <button class="w-full bg-[#8b5cf6] hover:bg-[#7c3aed] text-white font-bold py-2.5 rounded-lg transition shadow-lg shadow-[#8b5cf6]/10 text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-upload"></i> Import
                    </button>
                </form>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-bold t-primary mb-4">Data Format Reference</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-semibold t-primary mb-2">Transactions</h4>
                    <ul class="text-xs t-secondary space-y-1.5">
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Title</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Amount</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Type</code> (income/expense)</li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Note</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Category</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Currency</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Date</code></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold t-primary mb-2">Categories</h4>
                    <ul class="text-xs t-secondary space-y-1.5">
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Name</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Monthly Budget</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Currency</code></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold t-primary mb-2">Investments</h4>
                    <ul class="text-xs t-secondary space-y-1.5">
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Type</code> (stock/crypto)</li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Name</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Symbol</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">External ID</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Quantity</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Avg Price</code></li>
                        <li><code class="bg-gray-100 dark:bg-white/5 px-2 py-1 rounded t-primary">Currency</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
