@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
        Import / Export
    </h2>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto p-6 space-y-6">
        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg border border-green-300">
                {{ session('success') }}
                @if(session('import_errors'))
                    <div class="mt-3 text-sm space-y-1">
                        @foreach(session('import_errors') as $error)
                            <div>⚠ {{ $error }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-100 text-red-800 rounded-lg border border-red-300">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Export Section -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-download text-amber-400 text-xl"></i>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Export Data</h3>
                </div>

                <form method="POST" action="{{ route('data.export') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">What to export:</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="types[]" value="transactions" class="w-5 h-5 text-amber-400 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Transactions</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="types[]" value="categories" class="w-5 h-5 text-amber-400 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Categories</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="types[]" value="investments" class="w-5 h-5 text-amber-400 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Investments</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Format:</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="format" value="xlsx" class="w-5 h-5 text-amber-400">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Excel (.xlsx)</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="format" value="csv" class="w-5 h-5 text-amber-400">
                                <span class="text-sm text-gray-700 dark:text-gray-300">CSV</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="format" value="pdf" class="w-5 h-5 text-amber-400">
                                <span class="text-sm text-gray-700 dark:text-gray-300">PDF Report</span>
                            </label>
                        </div>
                    </div>

                    <button class="w-full bg-amber-400 hover:bg-amber-500 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i>
                        Download
                    </button>
                </form>
            </div>

            <!-- Import Section -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fa-solid fa-upload text-indigo-400 text-xl"></i>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Import Data</h3>
                </div>

                <form method="POST" action="{{ route('data.import') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select file:</label>
                        <div class="relative">
                            <input type="file" name="file" accept=".xlsx,.csv,.xls" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white cursor-pointer file:mr-4 file:py-2 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-amber-400 file:text-white hover:file:bg-amber-500" required>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Supported: .xlsx, .csv, .xls</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <p class="text-xs text-blue-800 dark:text-blue-200">
                            <strong>Note:</strong> Import will merge data with existing records. Duplicate investments will update quantities and average prices.
                        </p>
                    </div>

                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-upload"></i>
                        Import
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Section -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📋 Data Format Reference</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Transactions</h4>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Title</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Amount</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Type</code> (income/expense)</li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Note</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Category</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Currency</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Date</code></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Categories</h4>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Name</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Monthly Budget</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Currency</code></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Investments</h4>
                    <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Type</code> (stock/crypto)</li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Name</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Symbol</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">External ID</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Quantity</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Avg Price</code></li>
                        <li><code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">Currency</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
