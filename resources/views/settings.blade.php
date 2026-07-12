<x-app-layout>
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Kustomisasi</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Pengaturan Tampilan</h1>
            <p class="text-slate-500 font-medium">Sesuaikan aksen warna favorit Anda untuk menghiasi antarmuka Edusphere.</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto space-y-8" 
         x-data="{ 
            accent: localStorage.getItem('accent') || 'indigo',
            setAccent(newAccent) {
                this.accent = newAccent;
                localStorage.setItem('accent', newAccent);
                ['accent-emerald', 'accent-rose', 'accent-violet', 'accent-amber'].forEach(c => {
                    document.documentElement.classList.remove(c);
                });
                if (newAccent !== 'indigo') {
                    document.documentElement.classList.add('accent-' + newAccent);
                }
            }
         }">

        <!-- AKSEN WARNA CARD -->
        <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
            <h2 class="text-lg font-black text-slate-800 mb-2 flex items-center gap-2">
                <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                Aksen Warna Sistem
            </h2>
            <p class="text-xs text-slate-450 font-medium mb-8">Pilih warna aksen utama yang akan menghiasi tombol, menu aktif, dan status di Edusphere.</p>

            <div class="flex flex-wrap items-center justify-center gap-8 py-4">
                <!-- Indigo Option -->
                <button @click="setAccent('indigo')" 
                        class="group flex flex-col items-center gap-2.5 focus:outline-none">
                    <div class="w-14 h-14 rounded-full transition-all duration-200 flex items-center justify-center shadow-sm"
                         :style="accent === 'indigo' ? 'background-color: rgb(79, 70, 229); border: 4px solid white; box-shadow: 0 0 0 2px rgb(79, 70, 229); transform: scale(1.05);' : 'background-color: rgb(79, 70, 229); border: 4px solid transparent; box-shadow: none;'"
                         style="cursor: pointer;">
                        <template x-if="accent === 'indigo'">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Indigo</span>
                </button>

                <!-- Emerald Option -->
                <button @click="setAccent('emerald')" 
                        class="group flex flex-col items-center gap-2.5 focus:outline-none">
                    <div class="w-14 h-14 rounded-full transition-all duration-200 flex items-center justify-center shadow-sm"
                         :style="accent === 'emerald' ? 'background-color: rgb(16, 185, 129); border: 4px solid white; box-shadow: 0 0 0 2px rgb(16, 185, 129); transform: scale(1.05);' : 'background-color: rgb(16, 185, 129); border: 4px solid transparent; box-shadow: none;'"
                         style="cursor: pointer;">
                        <template x-if="accent === 'emerald'">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Emerald</span>
                </button>

                <!-- Violet Option -->
                <button @click="setAccent('violet')" 
                        class="group flex flex-col items-center gap-2.5 focus:outline-none">
                    <div class="w-14 h-14 rounded-full transition-all duration-200 flex items-center justify-center shadow-sm"
                         :style="accent === 'violet' ? 'background-color: rgb(147, 51, 234); border: 4px solid white; box-shadow: 0 0 0 2px rgb(147, 51, 234); transform: scale(1.05);' : 'background-color: rgb(147, 51, 234); border: 4px solid transparent; box-shadow: none;'"
                         style="cursor: pointer;">
                        <template x-if="accent === 'violet'">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Violet</span>
                </button>

                <!-- Rose Option -->
                <button @click="setAccent('rose')" 
                        class="group flex flex-col items-center gap-2.5 focus:outline-none">
                    <div class="w-14 h-14 rounded-full transition-all duration-200 flex items-center justify-center shadow-sm"
                         :style="accent === 'rose' ? 'background-color: rgb(225, 29, 72); border: 4px solid white; box-shadow: 0 0 0 2px rgb(225, 29, 72); transform: scale(1.05);' : 'background-color: rgb(225, 29, 72); border: 4px solid transparent; box-shadow: none;'"
                         style="cursor: pointer;">
                        <template x-if="accent === 'rose'">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Rose</span>
                </button>

                <!-- Amber Option -->
                <button @click="setAccent('amber')" 
                        class="group flex flex-col items-center gap-2.5 focus:outline-none">
                    <div class="w-14 h-14 rounded-full transition-all duration-200 flex items-center justify-center shadow-sm"
                         :style="accent === 'amber' ? 'background-color: rgb(217, 119, 6); border: 4px solid white; box-shadow: 0 0 0 2px rgb(217, 119, 6); transform: scale(1.05);' : 'background-color: rgb(217, 119, 6); border: 4px solid transparent; box-shadow: none;'"
                         style="cursor: pointer;">
                        <template x-if="accent === 'amber'">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </template>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Amber</span>
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
