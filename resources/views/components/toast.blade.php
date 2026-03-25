<div
    x-data="{
        messages: [],
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        },
        add(message, type = 'success', duration = 5000) {
            const id = Date.now();
            this.messages.push({ id, message, type });
            setTimeout(() => this.remove(id), duration);
        }
    }"
    x-on:toast.window="add($event.detail.message, $event.detail.type || 'success', $event.detail.duration)"
    x-init="
        @if(session('success')) add('{{ session('success') }}', 'success'); @endif
        @if(session('error')) add('{{ session('error') }}', 'error'); @endif
        @if(session('info')) add('{{ session('info') }}', 'info'); @endif
    "
    class="fixed top-6 right-6 z-[110] flex flex-col gap-3 w-full max-w-sm pointer-events-none"
>
    <template x-for="msg in messages" :key="msg.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-x-12 scale-90"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-x-0 outline-none"
            x-transition:leave-end="opacity-0 translate-x-12 scale-90"
            :class="{
                'bg-white dark:bg-[#18181b] border-emerald-500/20 shadow-emerald-500/5': msg.type === 'success',
                'bg-white dark:bg-[#18181b] border-red-500/20 shadow-red-500/5': msg.type === 'error',
                'bg-white dark:bg-[#18181b] border-[#8b5cf6]/20 shadow-[#8b5cf6]/5': msg.type === 'info'
            }"
            class="pointer-events-auto relative flex items-center justify-between gap-4 py-4 px-5 rounded-2xl border bg-white shadow-2xl backdrop-blur-xl border-gray-100 dark:border-white/10"
        >
            <div class="flex items-center gap-4">
                <div
                    :class="{
                        'bg-emerald-500/10 text-emerald-500': msg.type === 'success',
                        'bg-red-500/10 text-red-500': msg.type === 'error',
                        'bg-[#8b5cf6]/10 text-[#8b5cf6]': msg.type === 'info'
                    }"
                    class="h-10 w-10 shrink-0 flex items-center justify-center rounded-xl"
                >
                    <template x-if="msg.type === 'success'">
                        <i class="fa-solid fa-circle-check text-lg"></i>
                    </template>
                    <template x-if="msg.type === 'error'">
                        <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    </template>
                    <template x-if="msg.type === 'info'">
                        <i class="fa-solid fa-circle-info text-lg"></i>
                    </template>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-widest font-extrabold t-muted mb-0.5" x-text="msg.type === 'success' ? '{{ __('Success') }}' : (msg.type === 'error' ? '{{ __('Error') }}' : '{{ __('Info') }}')"></span>
                    <span class="text-sm font-bold t-primary leading-tight" x-text="msg.message"></span>
                </div>
            </div>
            <button @click="remove(msg.id)" class="text-gray-400 hover:text-primary transition p-2">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </template>
</div>
