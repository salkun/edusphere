<x-app-layout>
    <!-- Header Card Nilai -->
    <div class="mb-8 p-8 rounded-3xl bg-white border border-slate-200/60 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full uppercase tracking-wider">Hasil Belajar</span>
            </div>
            <h1 class="text-3xl font-black text-slate-800 mb-1">Rapor Siswa - Kelas {{ $classroom->name ?? '-' }}</h1>
            <p class="text-slate-500 font-medium">Lihat rangkuman nilai akademis, kehadiran, dan catatan perkembangan belajar Anda dari wali kelas.</p>
        </div>
        
        <!-- Wali Kelas Card -->
        <div class="flex items-center gap-4 bg-slate-50 p-5 rounded-2xl border border-slate-100 shrink-0">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg">
                {{ $classroom && $classroom->homeroomTeacher ? substr($classroom->homeroomTeacher->name, 3, 1) : 'W' }}
            </div>
            <div>
                <div class="text-xs text-slate-400 font-semibold tracking-wider uppercase">WALI KELAS</div>
                <div class="text-base font-bold text-slate-800">{{ $classroom->homeroomTeacher->name ?? 'Belum Ditentukan' }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Penanggung Jawab Rapor</div>
            </div>
        </div>
    </div>

    @if(!$reportCard)
        <div class="p-8 text-center bg-white rounded-3xl border border-slate-200/60 text-slate-500">
            Data nilai rapor Anda belum diterbitkan oleh wali kelas.
        </div>
    @else
        <div class="space-y-8 max-w-6xl mx-auto">
            
            <!-- FILTER SEMESTER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-slate-200/60 p-6 rounded-3xl shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-black text-slate-700">Pilih Semester:</span>
                    <form action="{{ route('grades') }}" method="GET" id="semesterForm">
                        <select name="semester" onchange="document.getElementById('semesterForm').submit()" 
                                class="text-sm font-bold text-slate-700 bg-slate-50 border-slate-200/80 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 py-2 px-4 pr-10">
                            @foreach($availableSemesters as $sem)
                                <option value="{{ $sem }}" {{ $sem == $selectedSemester ? 'selected' : '' }}>
                                    Semester {{ $sem }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @if($reportCard->created_at)
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">
                        Rapor Diterbitkan: {{ $reportCard->created_at->format('d M Y') }}
                    </div>
                @endif
            </div>

            <!-- BIODATA SISWA CARD -->
            <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm">
                <h2 class="text-lg font-black text-slate-800 mb-6 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                    Biodata Siswa
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Lengkap</div>
                        <div class="text-sm font-black text-slate-800">{{ $reportCard->student->name ?? '-' }}</div>
                    </div>
                    <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas</div>
                        <div class="text-sm font-black text-slate-800">{{ $classroom->name ?? '-' }}</div>
                    </div>
                    <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Semester</div>
                        <div class="text-sm font-black text-slate-800">{{ $reportCard->semester }}</div>
                    </div>
                    <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIS / NISN</div>
                        <div class="text-sm font-mono font-black text-slate-800">{{ $reportCard->nis ?? '-' }} / {{ $reportCard->nisn ?? '-' }}</div>
                    </div>
                    <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100 col-span-2 md:col-span-1">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Ajaran</div>
                        <div class="text-sm font-black text-slate-800">2025/2026</div>
                    </div>
                </div>
            </div>

            <!-- TABEL NILAI AKADEMIS -->
            <div class="bg-white border border-slate-200/60 rounded-3xl shadow-sm">
                <div class="p-8 border-b border-slate-100">
                    <h2 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                        Nilai Akademik & Capaian Kompetensi
                    </h2>
                </div>
                
                <div class="overflow-x-auto w-full max-w-full rounded-b-3xl">
                    <table class="w-full text-left border-collapse" style="min-width: 850px; width: 100%;">
                        <thead>
                            <tr class="bg-slate-50/75 border-b border-slate-100">
                                <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase text-center w-16 whitespace-nowrap">No</th>
                                <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase w-64 whitespace-nowrap">Mata Pelajaran</th>
                                <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase text-center w-32 whitespace-nowrap">Nilai Akhir</th>
                                <th class="py-4 px-6 text-xs font-bold text-slate-400 uppercase whitespace-nowrap">Capaian Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($reportCard->items as $index => $item)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <td class="py-5 px-6 text-sm font-bold text-slate-500 text-center whitespace-nowrap">{{ $index + 1 }}</td>
                                    <td class="py-5 px-6 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800">{{ $item->subject->name }}</div>
                                        <div class="text-xs text-slate-450 mt-0.5">Guru: {{ $item->subject->teacher->name ?? '-' }}</div>
                                    </td>
                                    <td class="py-5 px-6 text-center whitespace-nowrap">
                                        @php
                                            $gradeVal = floatval($item->final_grade);
                                            $formattedGrade = $gradeVal == intval($gradeVal) ? intval($gradeVal) : number_format($gradeVal, 1);
                                            
                                            // Conditional color badge for premium feel
                                            $badgeColor = match(true) {
                                                $gradeVal >= 90 => 'bg-emerald-50 text-emerald-700',
                                                $gradeVal >= 80 => 'bg-indigo-50 text-indigo-700',
                                                $gradeVal >= 70 => 'bg-amber-50 text-amber-700',
                                                default => 'bg-rose-50 text-rose-700'
                                            };
                                        @endphp
                                        <span class="inline-block px-3 py-1 text-sm font-black rounded-lg {{ $badgeColor }}">{{ $formattedGrade }}</span>
                                    </td>
                                    <td class="py-5 px-6 text-sm text-slate-650 leading-relaxed">{{ $item->competence }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- KETIDAKHADIRAN & CATATAN WALI KELAS -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Keterangan Ketidakhadiran (Absensi) -->
                <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm lg:col-span-1">
                    <h3 class="text-base font-black text-slate-800 mb-6 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                        Ketidakhadiran
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span class="text-xs font-bold text-slate-600">Sakit</span>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $reportCard->sick_days }} Hari</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2.5 border-b border-slate-50">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-xs font-bold text-slate-600">Izin</span>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $reportCard->excused_days }} Hari</span>
                        </div>

                        <div class="flex justify-between items-center py-2.5">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <span class="text-xs font-bold text-slate-600">Tanpa Keterangan</span>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $reportCard->unexcused_days }} Hari</span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Wali Kelas -->
                <div class="p-8 bg-white border border-slate-200/60 rounded-3xl shadow-sm lg:col-span-2">
                    <h3 class="text-base font-black text-slate-800 mb-6 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="w-2.5 h-5 bg-indigo-600 rounded-full"></span>
                        Catatan Wali Kelas
                    </h3>
                    
                    <div class="p-5 bg-slate-50 border border-slate-100 rounded-2xl">
                        <p class="text-sm text-slate-650 leading-relaxed italic">
                            "{{ $reportCard->homeroom_notes }}"
                        </p>
                    </div>
                </div>

            </div>

        </div>
    @endif
</x-app-layout>
