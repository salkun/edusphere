<?php

use App\Http\Controllers\ProfileController;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Material;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Menghitung ucapan selamat berdasarkan waktu Purwakarta (WIB / Asia/Jakarta)
    $hour = \Carbon\Carbon::now('Asia/Jakarta')->hour;
    if ($hour >= 4 && $hour < 11) {
        $greeting = 'Selamat pagi';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Selamat siang';
    } elseif ($hour >= 15 && $hour < 19) {
        $greeting = 'Selamat sore';
    } else {
        $greeting = 'Selamat malam';
    }
    
    // Mengambil kelas pertama tempat siswa terdaftar
    $classroom = $user->classes()->first();
    
    $subjects = collect();
    $upcomingAssignments = collect();
    $recentMaterials = collect();
    $completedMaterialIds = [];
    $totalClassMaterialsCount = 0;
    $completedClassMaterialsCount = 0;
    $globalProgress = 0;
    
    if ($classroom) {
        // Ambil ID materi yang sudah diselesaikan oleh siswa ini
        $completedMaterialIds = $user->completedMaterials()->pluck('material_id')->toArray();

        // Mengambil semua mata pelajaran di kelas tersebut beserta gurunya dan materinya
        $subjects = Subject::with(['teacher', 'materials'])
            ->where('class_id', $classroom->id)
            ->get();

        // Hitung progres belajar per mata pelajaran
        foreach ($subjects as $subject) {
            $totalMaterials = $subject->materials->count();
            $completedMaterials = $subject->materials->whereIn('id', $completedMaterialIds)->count();
            
            // Set property dinamis untuk view
            $subject->total_materials = $totalMaterials;
            $subject->completed_materials = $completedMaterials;
            $subject->progress = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;
            
            // Akumulasi progres global
            $totalClassMaterialsCount += $totalMaterials;
            $completedClassMaterialsCount += $completedMaterials;
        }
            
        $subjectIds = $subjects->pluck('id');
        
        // Mengambil tugas mendatang (belum melewati deadline)
        $upcomingAssignments = Assignment::with('subject.teacher')
            ->whereIn('subject_id', $subjectIds)
            ->where('deadline', '>', now())
            ->orderBy('deadline', 'asc')
            ->get();
            
        // Mengambil materi terbaru
        $recentMaterials = Material::with('subject.teacher')
            ->whereIn('subject_id', $subjectIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung progres global
        $globalProgress = $totalClassMaterialsCount > 0 
            ? round(($completedClassMaterialsCount / $totalClassMaterialsCount) * 100) 
            : 0;
    }
    
    return view('dashboard', compact(
        'classroom', 
        'subjects', 
        'upcomingAssignments', 
        'recentMaterials',
        'completedMaterialIds',
        'totalClassMaterialsCount',
        'completedClassMaterialsCount',
        'globalProgress',
        'greeting'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/materials/{material}/toggle', function (\App\Models\Material $material) {
    $user = auth()->user();
    if ($user->completedMaterials()->where('material_id', $material->id)->exists()) {
        $user->completedMaterials()->detach($material->id);
    } else {
        $user->completedMaterials()->attach($material->id);
    }
    return back();
})->middleware('auth')->name('materials.toggle');

Route::get('/my-class', function () {
    $user = auth()->user();
    
    // Mengambil kelas beserta wali kelas dan mata pelajaran terjadwal
    $classroom = $user->classes()->with(['homeroomTeacher', 'subjects.teacher'])->first();
    
    $subjects = collect();
    if ($classroom) {
        // Urutkan mata pelajaran berdasarkan hari dan jam mulai
        $dayOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
        $subjects = $classroom->subjects->sortBy(function ($subject) use ($dayOrder) {
            return [$dayOrder[$subject->day] ?? 9, $subject->start_time];
        });
    }
    
    return view('my-class', compact('classroom', 'subjects'));
})->middleware(['auth', 'verified'])->name('my-class');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
