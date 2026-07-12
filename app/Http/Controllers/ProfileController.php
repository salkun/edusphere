<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Mengambil kelas siswa
        $classroom = $user->classes()->first();
        
        // Mengambil data rapor terbaru yang menyimpan biodata lengkap
        $reportCard = \App\Models\ReportCard::where('student_id', $user->id)->first();
        
        return view('profile.edit', [
            'user' => $user,
            'classroom' => $classroom,
            'reportCard' => $reportCard,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user = $request->user();
        $reportCard = \App\Models\ReportCard::where('student_id', $user->id)->first();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // Buat folder jika belum ada
            if (!file_exists(public_path('images/avatars'))) {
                mkdir(public_path('images/avatars'), 0755, true);
            }
            
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/avatars'), $filename);
            
            if ($reportCard) {
                // Hapus berkas avatar lama jika ada
                if ($reportCard->avatar_path && file_exists(public_path($reportCard->avatar_path))) {
                    @unlink(public_path($reportCard->avatar_path));
                }
                $reportCard->update([
                    'avatar_path' => 'images/avatars/' . $filename
                ]);
            }
        }

        return redirect()->route('profile.edit')->with('status', 'avatar-updated');
    }
}
