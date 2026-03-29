<div
    x-data="{
        show: false,
        title: '',
        message: '',
        confirmText: '{{ __('Confirm') }}',
        cancelText: '{{ __('Cancel') }}',
        confirmVariant: 'danger',
        onConfirm: null,

        open(detail) {
            this.title = detail.title || '{{ __('Confirm') }}';
            this.message = detail.message || '';
            this.confirmText = detail.confirmText || '{{ __('Confirm') }}';
            this.cancelText = detail.cancelText || '{{ __('Cancel') }}';
            this.confirmVariant = detail.variant || 'danger';
            this.onConfirm = detail.onConfirm;
            this.show = true;
        },

        confirm() {
            if (this.onConfirm) this.onConfirm();
            this.show = false;
        }
    }"
    x-on:confirm.window="open($event.detail)"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
    style="display: none;"
>
    {{-- Pozadí --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/40 dark:bg-black/80 backdrop-blur-md"
        @click="show = false"
    ></div>

    {{-- Obsah modalu --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
        class="relative w-full max-w-md transform overflow-hidden rounded-[2rem] bg-white dark:bg-[#18181b] p-8 text-left shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] transition-all border border-gray-100 dark:border-white/10"
    >
        <div class="flex flex-col items-center text-center">
            <div
                :class="{
                    'bg-red-500/10 text-red-500': confirmVariant === 'danger',
                    'bg-[#fbbf24]/10 text-[#fbbf24]': confirmVariant === 'warning',
                    'bg-blue-500/10 text-blue-500': confirmVariant === 'info'
                }"
                class="flex h-16 w-16 mb-6 items-center justify-center rounded-2xl"
            >
                <template x-if="confirmVariant === 'danger'">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </template>
                <template x-if="confirmVariant === 'warning' || confirmVariant === 'info'">
                    <i class="fa-solid fa-circle-question text-2xl"></i>
                </template>
            </div>
            
            <h3 class="text-xl font-bold t-primary leading-tight mb-3" x-text="title"></h3>
            <p class="text-sm t-secondary leading-relaxed px-2" x-text="message"></p>
        </div>

        <div class="mt-10 flex flex-col gap-3">
            <button
                type="button"
                :class="{
                    'bg-red-500 hover:bg-red-600 text-white shadow-lg shadow-red-500/25': confirmVariant === 'danger',
                    'bg-[#fbbf24] hover:bg-[#f59e0b] text-black shadow-lg shadow-[#fbbf24]/20': confirmVariant !== 'danger'
                }"
                class="w-full rounded-2xl py-4 text-sm font-bold transition transform hover:scale-[1.02] active:scale-[0.98]"
                @click="confirm()"
                x-text="confirmText"
            ></button>
            <button
                type="button"
                class="w-full py-4 text-sm font-bold t-muted hover:text-primary transition rounded-2xl"
                @click="show = false"
                x-text="cancelText"
            ></button>
        </div>
    </div>
</div>
