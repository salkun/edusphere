<x-app-layout>
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Administrator</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1 font-sans">Kelola Kelas & Wali Kelas</h1>
            <p class="text-slate-500 font-medium font-sans">Daftarkan kelas belajar baru, tetapkan Wali Kelas, dan kelola mata pelajaran terjadwal kelas beserta daftar guru dan durasi belajarnya.</p>
        </div>
    </div>

    <!-- Alert Status -->
    @if (session('success'))
        <div class="mb-6 p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-2" role="alert">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-100 flex items-center gap-2" role="alert">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <!-- MAIN BODY -->
    <div x-data="{ 
        open: false, 
        openSchedule: false, 
        openStudents: false,
        isEdit: false, 
        actionUrl: '', 
        name: '', 
        studentSearchQuery: '', 
        homeroom_teacher_id: '', 
        activeClassId: '', 
        activeClassName: '', 
        activeClassSubjects: [], 
        activeClassStudents: [],
        deletedSubjectIds: [],
        addedStudentIds: [],
        removedStudentIds: [],
        selectedMasterId: '', 
        availableTeachers: [], 
        daysToAdd: [],
        startTimeToAdd: '07:00',
        durationToAdd: 90,
        teacherIdsToAdd: [],
        allStudentsList: {{ json_encode($allStudents->map(function($s) {
            return ['id' => $s->id, 'name' => $s->name, 'email' => $s->email];
        })) }},
        masterSubjectsList: {{ json_encode($masterSubjects->map(function($ms) {
            return [
                'id' => $ms->id,
                'name' => $ms->name,
                'teachers' => $ms->teachers->map(function($t) {
                    return ['id' => $t->id, 'name' => $t->name];
                })
            ];
        })) }},
        updateAvailableTeachers() {
            const selected = this.masterSubjectsList.find(ms => ms.id == this.selectedMasterId);
            this.availableTeachers = selected ? selected.teachers : [];
            this.teacherIdsToAdd = [];
        },
        getTeachersForSubjectName(name) {
            const match = this.masterSubjectsList.find(ms => ms.name === name);
            return match ? match.teachers : [];
        },
        getAvailableStudents() {
            return this.allStudentsList.filter(s => !this.activeClassStudents.some(ac => ac.id === s.id));
        },
        addSubjectToList() {
            const masterSub = this.masterSubjectsList.find(ms => ms.id == this.selectedMasterId);
            if (!masterSub) return;
            if (this.daysToAdd.length === 0) {
                alert('Silakan pilih minimal 1 hari belajar!');
                return;
            }

            this.activeClassSubjects.push({
                id: 'temp_' + Date.now(),
                name: masterSub.name,
                day: this.daysToAdd.join(','),
                days: [...this.daysToAdd],
                class_id: this.activeClassId,
                start_time: this.startTimeToAdd,
                duration: parseInt(this.durationToAdd) || 90,
                teacher_ids: [...this.teacherIdsToAdd],
                is_new: true
            });

            // Reset inputs
            this.selectedMasterId = '';
            this.availableTeachers = [];
            this.daysToAdd = [];
            this.teacherIdsToAdd = [];
            this.startTimeToAdd = '07:00';
            this.durationToAdd = 90;
        },
        removeSubject(item) {
            if (!item.is_new) {
                this.deletedSubjectIds.push(item.id);
            }
            this.activeClassSubjects = this.activeClassSubjects.filter(s => s.id !== item.id);
        }
    }" class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-black text-slate-800">Daftar Kelas Belajar</h2>
                <p class="text-xs text-slate-450 mt-0.5 font-medium font-sans">Semua kelas yang terdaftar dalam sistem akademik Edusphere.</p>
            </div>
            <button @click="open = true; isEdit = false; actionUrl = '{{ route('admin.classes.store') }}'; name = ''; homeroom_teacher_id = '';" 
                    class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-md transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kelas
            </button>
        </div>

        <div class="overflow-x-auto -mx-8">
            <table class="w-full text-left border-collapse min-w-[600px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold tracking-wider">
                        <th class="px-8 py-4 whitespace-nowrap">Nama Kelas</th>
                        <th class="px-6 py-4 whitespace-nowrap">Wali Kelas</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-center whitespace-nowrap">Jumlah Mapel</th>
                        <th class="px-8 py-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700 text-sm font-medium">
                    @forelse ($classes as $c)
                        <tr>
                            <td class="px-8 py-4 font-black text-slate-800 text-base whitespace-nowrap">
                                {{ $c->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($c->homeroomTeacher)
                                    <div class="flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span class="font-bold text-slate-800">{{ $c->homeroomTeacher->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic font-medium whitespace-nowrap">Belum ada Wali Kelas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-600 whitespace-nowrap">
                                <span class="bg-slate-100 text-slate-750 text-xs px-2.5 py-1 rounded-full whitespace-nowrap">{{ $c->students->count() }} Siswa</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-indigo-600 whitespace-nowrap">
                                {{ $c->subjects->count() }} Mapel
                            </td>
                            <td class="px-8 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2.5 whitespace-nowrap">
                                    <!-- Tombol Kelola Siswa -->
                                    <button @click="openStudents = true; activeClassId = '{{ $c->id }}'; activeClassName = '{{ $c->name }}'; studentSearchQuery = ''; addedStudentIds = []; removedStudentIds = []; activeClassStudents = {{ json_encode($c->students->map(function($student) {
                                        return ['id' => $student->id, 'name' => $student->name, 'email' => $student->email];
                                    })) }};" 
                                            class="text-blue-600 hover:text-blue-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 rounded-xl">
                                        Siswa
                                    </button>
                                    <!-- Tombol Atur Jadwal & Durasi -->
                                    <button @click="openSchedule = true; activeClassId = '{{ $c->id }}'; activeClassName = '{{ $c->name }}'; selectedMasterId = ''; availableTeachers = []; daysToAdd = []; teacherIdsToAdd = []; deletedSubjectIds = []; activeClassSubjects = {{ json_encode($c->subjects->map(function($sub) {
                                        return [
                                            'id' => $sub->id,
                                            'name' => $sub->name,
                                            'day' => $sub->day,
                                            'class_id' => $sub->class_id,
                                            'start_time' => \Carbon\Carbon::parse($sub->start_time)->format('H:i'),
                                            'end_time' => \Carbon\Carbon::parse($sub->end_time)->format('H:i'),
                                            'duration' => \Carbon\Carbon::parse($sub->end_time)->diffInMinutes(\Carbon\Carbon::parse($sub->start_time)),
                                            'teacher_ids' => $sub->teachers->pluck('id')->toArray()
                                        ];
                                    })) }};" 
                                            class="text-emerald-600 hover:text-emerald-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 rounded-xl">
                                        Jadwal & Mapel
                                    </button>
                                    <button @click="open = true; isEdit = true; actionUrl = '{{ route('admin.classes.update', $c->id) }}'; name = '{{ $c->name }}'; homeroom_teacher_id = '{{ $c->homeroom_teacher_id ?? '' }}';" 
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 rounded-xl">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.classes.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini? Semua mapel dan data murid di kelas ini akan terhapus.')">
                                        @csrf
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold transition-all px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 rounded-xl">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 font-bold text-slate-400 italic">Belum ada kelas terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODAL ALPINEJS: KELOLA KELAS -->
        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="open = false" 
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-md overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-black text-slate-800" x-text="isEdit ? 'Ubah Kelas' : 'Tambah Kelas'"></h3>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Nama Kelas</label>
                        <input type="text" name="name" x-model="name" required 
                               placeholder="Contoh: X RPL A"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5 tracking-wider">Wali Kelas</label>
                        <select name="homeroom_teacher_id" x-model="homeroom_teacher_id" 
                                class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-slate-800 bg-white">
                            <option value="">-- Tanpa Wali Kelas --</option>
                            @foreach ($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition-all">Simpan Kelas</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL ALPINEJS: KELOLA SISWA KELAS -->
        <div x-show="openStudents" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="openStudents = false" 
                 x-show="openStudents"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-xl overflow-hidden p-6 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Kelola Anggota Kelas</h3>
                        <p class="text-xs font-bold text-indigo-600" x-text="'Kelas: ' + activeClassName"></p>
                    </div>
                    <button type="button" @click="openStudents = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>

                <!-- Form Utama untuk Menyimpan Perubahan secara Batch -->
                <form :action="'/admin/classes/' + activeClassId + '/students/save'" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="added_student_ids" :value="JSON.stringify(addedStudentIds)">
                    <input type="hidden" name="removed_student_ids" :value="JSON.stringify(removedStudentIds)">

                    <!-- Bagian Tambah Anggota (Lokal Sementara) -->
                    <div class="space-y-3 bg-slate-50/50 p-4 border border-slate-150 rounded-2xl">
                        <div class="font-black text-slate-805 text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-1.5 h-3 bg-indigo-600 rounded-full"></span>
                            Tambahkan Siswa Baru ke Kelas Ini (Lokal)
                        </div>
                        
                        <div class="space-y-2">
                            <!-- Input Pencarian dengan Debounce 1 Detik -->
                            <input type="text" x-model.debounce.1000ms="studentSearchQuery" placeholder="Cari nama atau email siswa (delay 1s)..."
                                   class="w-full border border-slate-205 rounded-xl px-3 py-1.5 text-xs focus:ring-1 focus:ring-indigo-500 text-slate-850 bg-white font-bold">

                            <!-- List Hasil Pencarian Siswa -->
                            <div class="border border-slate-200 rounded-xl p-3 max-h-36 overflow-y-auto space-y-1.5 bg-white font-bold">
                                <template x-for="student in getAvailableStudents().filter(s => s.name.toLowerCase().includes(studentSearchQuery.toLowerCase()) || s.email.toLowerCase().includes(studentSearchQuery.toLowerCase()))" :key="student.id">
                                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 hover:bg-slate-50 p-1.5 rounded-lg transition-all">
                                        <div class="flex flex-col">
                                            <span class="text-slate-800" x-text="student.name"></span>
                                            <span class="text-[9px] text-slate-400 font-normal" x-text="student.email"></span>
                                        </div>
                                        <button type="button" 
                                                @click="
                                                    activeClassStudents.push(student);
                                                    if (!addedStudentIds.includes(student.id)) addedStudentIds.push(student.id);
                                                    removedStudentIds = removedStudentIds.filter(id => id !== student.id);
                                                "
                                                class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-2.5 py-1 rounded-lg text-[10px] font-black transition-all">
                                            + Tambah
                                        </button>
                                    </div>
                                </template>
                                <template x-if="getAvailableStudents().filter(s => s.name.toLowerCase().includes(studentSearchQuery.toLowerCase()) || s.email.toLowerCase().includes(studentSearchQuery.toLowerCase())).length === 0">
                                    <div class="text-[10px] text-slate-400 italic text-center py-4 font-medium">
                                        Ketik nama/email untuk mencari atau siswa tidak ditemukan
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Siswa Terdaftar -->
                    <div class="space-y-3">
                        <div class="font-black text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-1.5 h-3 bg-emerald-600 rounded-full"></span>
                            Daftar Siswa Terdaftar (<span x-text="activeClassStudents.length" class="text-indigo-600"></span> Siswa)
                        </div>

                        <div class="border border-slate-200 rounded-2xl overflow-hidden max-h-60 overflow-y-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-450 uppercase text-[9px] font-bold tracking-wider">
                                        <th class="px-4 py-2.5">Nama Siswa</th>
                                        <th class="px-4 py-2.5">Email</th>
                                        <th class="px-4 py-2.5 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs font-bold">
                                    <template x-for="s in activeClassStudents" :key="s.id">
                                        <tr>
                                            <td class="px-4 py-3 text-slate-800" x-text="s.name"></td>
                                            <td class="px-4 py-3 text-slate-500 font-normal" x-text="s.email"></td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end">
                                                    <button type="button" 
                                                            @click="
                                                                const sId = s.id;
                                                                activeClassStudents = activeClassStudents.filter(item => item.id !== sId);
                                                                if (!addedStudentIds.includes(sId)) {
                                                                    if (!removedStudentIds.includes(sId)) removedStudentIds.push(sId);
                                                                } else {
                                                                    addedStudentIds = addedStudentIds.filter(id => id !== sId);
                                                                }
                                                            "
                                                            class="text-rose-600 hover:text-rose-800 font-bold hover:bg-rose-50 px-2 py-1 rounded-lg transition-all">
                                                        Keluarkan
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="activeClassStudents.length === 0">
                                        <td colspan="3" class="text-center py-8 text-slate-400 italic font-medium">Belum ada siswa terdaftar di kelas ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="openStudents = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black transition-all shadow-md">Simpan & Selesai</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL ALPINEJS: JADWAL & DURASI KELAS (Unified Batch Editor) -->
        <div x-show="openSchedule" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="openSchedule = false" 
                 x-show="openSchedule"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="bg-white rounded-3xl border border-slate-200/60 shadow-xl w-full max-w-3xl overflow-hidden p-6 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Atur Jadwal & Mapel Kelas</h3>
                        <p class="text-xs font-bold text-indigo-600" x-text="'Kelas: ' + activeClassName"></p>
                    </div>
                    <button type="button" @click="openSchedule = false" class="text-slate-400 hover:text-slate-650 font-bold text-xl">&times;</button>
                </div>

                <!-- Form Tambah Mapel Baru ke Kelas (Simpan Sementara di Sisi Client) -->
                <div class="space-y-4 bg-slate-50/50 p-4 border border-slate-150 rounded-2xl">
                    <div class="font-black text-slate-805 text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-1.5 h-3 bg-indigo-600 rounded-full"></span>
                        Pasang Mapel Baru ke Kelas Ini (Lokal)
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Pilih Mapel Master -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 tracking-wider">Mata Pelajaran</label>
                            <select x-model="selectedMasterId" @change="updateAvailableTeachers"
                                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-indigo-500 text-slate-805 bg-white font-bold">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <template x-for="ms in masterSubjectsList" :key="ms.id">
                                    <option :value="ms.id" x-text="ms.name"></option>
                                </template>
                            </select>
                        </div>
                        
                        <!-- Pilih Guru Pengampu dari Mapel Master Terpilih -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 tracking-wider">Pilih Guru Pengampu</label>
                            <div class="border border-slate-200 rounded-xl p-2 max-h-24 overflow-y-auto space-y-1.5 bg-white">
                                <template x-if="availableTeachers.length === 0">
                                    <span class="text-[10px] text-slate-400 italic block py-1 font-medium">Pilih mata pelajaran terlebih dahulu untuk melihat daftar guru</span>
                                </template>
                                <template x-for="t in availableTeachers" :key="t.id">
                                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                                        <input type="checkbox" :value="t.id" x-model="teacherIdsToAdd"
                                               class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-4 h-4">
                                        <span x-text="t.name"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <!-- Pilih Hari Belajar (Bisa > 1) -->
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 tracking-wider">Pasang di Hari Belajar</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="d in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']">
                                    <label class="flex items-center gap-1.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-750 cursor-pointer transition-all">
                                        <input type="checkbox" :value="d" x-model="daysToAdd"
                                               class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-3.5 h-3.5">
                                        <span x-text="d"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Jam Mulai -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 tracking-wider">Jam Mulai</label>
                            <input type="text" x-model="startTimeToAdd" placeholder="07:00"
                                   class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono font-bold text-center focus:ring-1 focus:ring-indigo-500 text-slate-800 bg-white">
                        </div>

                        <!-- Durasi (Menit) -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 tracking-wider">Durasi (Menit)</label>
                            <input type="number" x-model="durationToAdd" min="1" placeholder="90"
                                   class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-center focus:ring-1 focus:ring-indigo-500 text-slate-800 bg-white">
                        </div>

                        <div>
                            <button type="button" @click="addSubjectToList" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl py-2 text-xs font-black transition-all shadow-md">
                                Tambah ke Daftar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Utama untuk Menyimpan Perubahan secara Batch -->
                <form :action="'/admin/classes/' + activeClassId + '/schedule/save'" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="schedules" :value="JSON.stringify(activeClassSubjects)">
                    <input type="hidden" name="deleted_ids" :value="JSON.stringify(deletedSubjectIds)">

                    <!-- Daftar Timetable yang Terdaftar di Kelas -->
                    <div class="space-y-4 max-h-[250px] overflow-y-auto pr-2 mt-4">
                        <div class="font-black text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-1.5 h-3 bg-emerald-600 rounded-full"></span>
                            Daftar Jadwal & Durasi Mapel Kelas (Sementara)
                        </div>

                        <template x-if="activeClassSubjects.length === 0">
                            <div class="text-center py-6 text-slate-400 italic text-xs">
                                Belum ada jadwal mata pelajaran terdaftar untuk kelas ini. Gunakan form di atas untuk menambahkannya.
                            </div>
                        </template>

                        <template x-if="activeClassSubjects.length > 0">
                            <div class="space-y-4">
                                <template x-for="dayName in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']">
                                    <div class="space-y-2 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                        <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider" x-text="dayName"></h4>
                                        <div class="space-y-3">
                                            <template x-for="sub in activeClassSubjects.filter(s => s.day.includes(dayName))">
                                                <div class="flex flex-col bg-slate-50 border border-slate-150 rounded-2xl p-4 gap-3.5 shadow-sm">
                                                    <!-- Header Jadwal: Nama Mapel & Tombol Hapus -->
                                                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                                                        <div>
                                                            <div class="font-black text-slate-805 text-sm flex items-center gap-2">
                                                                <span x-text="sub.name"></span>
                                                                <template x-if="sub.is_new">
                                                                    <span class="text-[9px] font-bold bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded border border-amber-100 uppercase tracking-wide">Baru</span>
                                                                </template>
                                                            </div>
                                                            <div class="text-[10px] text-slate-455 font-medium font-sans mt-0.5">
                                                                Hari Terdaftar: <span class="font-bold text-slate-700" x-text="sub.day"></span> &middot; Durasi: <span class="font-bold text-indigo-600" x-text="sub.duration"></span> Menit
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Tombol Hapus Sementara -->
                                                        <button type="button" @click="removeSubject(sub)" class="bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl p-2 transition-all shadow-sm shrink-0" title="Hapus dari Kelas">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Body: Editor Input Grid (Full Width) -->
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white border border-slate-100 p-3.5 rounded-xl shadow-sm">
                                                        <!-- Pilih Guru Pengampu (dari master teachers) -->
                                                        <div class="flex flex-col">
                                                            <span class="text-[9px] text-slate-400 font-bold mb-1 uppercase tracking-wider">Guru Pengampu</span>
                                                            <select multiple class="text-[9px] border border-slate-200 rounded-lg py-1 px-1.5 focus:ring-1 focus:ring-indigo-500 w-full bg-slate-50 text-slate-805 font-bold max-h-16 overflow-y-auto"
                                                                    @change="
                                                                        const selectedOptions = Array.from($el.selectedOptions).map(o => parseInt(o.value));
                                                                        sub.teacher_ids = selectedOptions;
                                                                    ">
                                                                <template x-for="t in getTeachersForSubjectName(sub.name)" :key="t.id">
                                                                    <option :value="t.id" :selected="sub.teacher_ids.includes(t.id)" x-text="t.name"></option>
                                                                </template>
                                                            </select>
                                                        </div>

                                                        <!-- Pilih Hari Belajar (Bisa lebih dari 1) -->
                                                        <div class="flex flex-col">
                                                            <span class="text-[9px] text-slate-400 font-bold mb-1 uppercase tracking-wider">Hari Belajar</span>
                                                            <div class="grid grid-cols-3 gap-1 w-full">
                                                                <template x-for="d in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']">
                                                                    <label class="flex items-center justify-center gap-1 bg-slate-50 border border-slate-200 rounded-md py-1 text-[9px] font-bold text-slate-700 cursor-pointer transition-all hover:bg-slate-100">
                                                                        <input type="checkbox" :value="d"
                                                                               :checked="sub.day.includes(d)"
                                                                               @change="
                                                                                   let currentDays = sub.day.split(',').filter(x => x);
                                                                                   if ($el.checked) {
                                                                                       if (!currentDays.includes(d)) currentDays.push(d);
                                                                                   } else {
                                                                                       currentDays = currentDays.filter(x => x !== d);
                                                                                   }
                                                                                   sub.day = currentDays.join(',');
                                                                                   sub.days = currentDays;
                                                                               "
                                                                               class="rounded text-indigo-600 border-slate-200 focus:ring-indigo-500 w-2.5 h-2.5">
                                                                        <span x-text="d"></span>
                                                                    </label>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <!-- Edit Jam & Durasi -->
                                                        <div class="flex flex-col">
                                                            <span class="text-[9px] text-slate-400 font-bold mb-1 uppercase tracking-wider">Jam & Durasi (Menit)</span>
                                                            <div class="flex items-center gap-1.5 w-full">
                                                                <input type="text" x-model="sub.start_time" required 
                                                                       placeholder="07:00"
                                                                       class="w-full flex-1 border border-slate-200 rounded-lg px-2 py-1 text-[9px] font-mono font-bold text-center focus:ring-1 focus:ring-indigo-500 text-slate-805 bg-white">
                                                                <span class="text-slate-450 font-bold text-xs">-</span>
                                                                <input type="number" x-model="sub.duration" required 
                                                                       placeholder="90"
                                                                       class="w-full flex-1 border border-slate-200 rounded-lg px-2 py-1 text-[9px] font-bold text-center focus:ring-1 focus:ring-indigo-500 text-slate-855 bg-white">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>

                                            <div x-show="activeClassSubjects.filter(s => s.day.includes(dayName)).length === 0" class="text-[10px] text-slate-400 italic pl-3">
                                                Tidak ada jadwal mapel di hari ini
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="openSchedule = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black transition-all shadow-md">Simpan & Selesai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
