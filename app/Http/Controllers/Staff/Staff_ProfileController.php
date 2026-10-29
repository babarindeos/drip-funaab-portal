<?php

namespace App\Http\Controllers\Staff;

use App\Http\Classes\OrganClass;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Signature;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Staff_ProfileController extends Controller
{
    /**
     * Photos and signatures are shown with asset('storage/...'), so they live on the public disk.
     * They are always saved under a name WE choose, with the extension of the file's real type
     * (never the name or extension the browser sent), so nothing uploaded here can run as code.
     */
    private const DISK = 'public';

    private const PHONE_RULE = 'regex:/^[0-9+()\-\s]{7,20}$/';

    private const PHOTO_RULES = ['image', 'mimes:jpg,jpeg,png', 'max:2048', 'dimensions:min_width=100,min_height=100'];

    private const MESSAGES = [
        'designation.required' => 'Enter your designation (for example Lecturer II or Senior Technologist).',
        'phone.required' => 'Enter your phone number.',
        'phone.regex' => 'Enter a valid phone number (digits, spaces, + ( ) and - only).',
        'avatar.image' => 'The photo must be a JPG or PNG image.',
        'avatar.mimes' => 'The photo must be a JPG or PNG image.',
        'avatar.max' => 'The photo is too large. The limit is 2 MB.',
        'avatar.dimensions' => 'The photo is too small. It must be at least 100 x 100 pixels.',
        'avatar.uploaded' => 'The photo could not be uploaded. Please try again.',
    ];

    // ---- first-time profile ---------------------------------------------------

    /** A new user lands here first (the dashboard sends them) to give their designation and phone. */
    public function create()
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        return view('staff.profile.create', compact('profile'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'designation' => ['required', 'string', 'max:255'],
            'phone' => ['required', self::PHONE_RULE],
            'avatar' => array_merge(['nullable'], self::PHOTO_RULES),
        ], self::MESSAGES);

        $profile = Profile::firstOrNew(['user_id' => Auth::id()]);
        $is_new = ! $profile->exists;

        $profile->designation = trim($data['designation']);
        $profile->phone = trim($data['phone']);
        $profile->bio = $profile->bio ?? '';
        $profile->avatar = $request->hasFile('avatar')
            ? $this->storePhoto($request->file('avatar'), $profile->avatar)
            : ($profile->avatar ?? '');
        $profile->save();

        return redirect()->route('staff.dashboard.index')
                         ->with('success', $is_new ? 'Your profile has been created. Welcome to the DRIP portal.' : 'Your profile has been saved.');
    }

    /** Old route kept so any bookmark still works. */
    public function upload_avatar(Request $request)
    {
        return redirect()->route('staff.profile.myprofile');
    }

    // ---- my profile ------------------------------------------------------------

    public function myprofile()
    {
        if (! $profile = $this->myProfileOrNull()) {
            return redirect()->route('staff.profile.create');
        }

        return view('staff.profile.myprofile', ['user' => Auth::user(), 'profile' => $profile]);
    }

    public function edit()
    {
        if (! $profile = $this->myProfileOrNull()) {
            return redirect()->route('staff.profile.create');
        }

        return view('staff.profile.edit', ['user' => Auth::user(), 'profile' => $profile]);
    }

    public function update(Request $request)
    {
        if (! $profile = $this->myProfileOrNull()) {
            return redirect()->route('staff.profile.create');
        }

        $data = $request->validate([
            'designation' => ['required', 'string', 'max:255'],
            'phone' => ['required', self::PHONE_RULE],
            'avatar' => array_merge(['nullable'], self::PHOTO_RULES),
        ], self::MESSAGES);

        $profile->designation = trim($data['designation']);
        $profile->phone = trim($data['phone']);

        if ($request->hasFile('avatar')) {
            $profile->avatar = $this->storePhoto($request->file('avatar'), $profile->avatar);
        }

        $profile->save();

        return redirect()->route('staff.profile.myprofile')->with('success', 'Your profile has been updated.');
    }

    /** The "Change photo" form on the profile page. */
    public function update_avatar(Request $request)
    {
        if (! $profile = $this->myProfileOrNull()) {
            return redirect()->route('staff.profile.create');
        }

        $request->validate(['photo' => array_merge(['required'], self::PHOTO_RULES)], [
            'photo.required' => 'Choose a photo to upload.',
            'photo.image' => 'The photo must be a JPG or PNG image.',
            'photo.mimes' => 'The photo must be a JPG or PNG image.',
            'photo.max' => 'The photo is too large. The limit is 2 MB.',
            'photo.dimensions' => 'The photo is too small. It must be at least 100 x 100 pixels.',
            'photo.uploaded' => 'The photo could not be uploaded. Please try again.',
        ]);

        $profile->avatar = $this->storePhoto($request->file('photo'), $profile->avatar);
        $profile->save();

        return redirect()->route('staff.profile.myprofile')->with('success', 'Your photo has been updated.');
    }

    // ---- password ----------------------------------------------------------------

    public function change_password()
    {
        if (! $profile = $this->myProfileOrNull()) {
            return redirect()->route('staff.profile.create');
        }

        return view('staff.profile.change_password', ['user' => Auth::user(), 'profile' => $profile]);
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Enter your current password.',
            'new_password.required' => 'Enter a new password.',
            'new_password.min' => 'Use at least 8 characters.',
            'new_password.confirmed' => 'The two new passwords do not match.',
            'new_password.different' => 'The new password must be different from the current one.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is not correct.']);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return redirect()->route('staff.profile.change_password')->with('success', 'Your password has been changed.');
    }

    // ---- signature (used by the document workflow) -------------------------------

    public function my_signature()
    {
        if (! $profile = $this->myProfileOrNull()) {
            return redirect()->route('staff.profile.create');
        }

        $signature = Signature::where('user_id', Auth::id())->first();

        return view('staff.profile.my_signature', ['user' => Auth::user(), 'profile' => $profile, 'signature' => $signature]);
    }

    public function upload_signature(Request $request)
    {
        return $this->saveSignature($request);
    }

    public function update_signature(Request $request)
    {
        return $this->saveSignature($request);
    }

    // ---- someone else's profile (linked from the document workflow) ---------------

    public function user_profile($fileno)
    {
        $staff = Staff::where('fileno', $fileno)->first();

        return $this->showPerson(optional($staff)->user, $staff);
    }

    public function email_user_profile($email)
    {
        $person = User::where('email', $email)->first();

        return $this->showPerson($person, optional($person)->staff);
    }

    // ------------------------------------------------------------------------------

    private function myProfileOrNull(): ?Profile
    {
        return Profile::where('user_id', Auth::id())->first();
    }

    /** Save an uploaded photo under our own name and remove the one it replaces. */
    private function storePhoto(UploadedFile $file, ?string $old): string
    {
        $path = $file->storeAs('avatars', Auth::id().'-'.Str::lower(Str::random(10)).'.'.$file->extension(), self::DISK);

        $this->deleteOld($old, 'avatars/', $path);

        return $path;
    }

    private function saveSignature(Request $request)
    {
        $request->validate([
            'signature' => ['required', 'image', 'mimes:png', 'max:1024'],
        ], [
            'signature.required' => 'Choose your signature image.',
            'signature.image' => 'The signature must be a PNG image.',
            'signature.mimes' => 'The signature must be a PNG image.',
            'signature.max' => 'The signature is too large. The limit is 1 MB.',
        ]);

        $signature = Signature::firstOrNew(['user_id' => Auth::id()]);
        $is_new = ! $signature->exists;

        $path = $request->file('signature')->storeAs('signatures', Auth::id().'-'.Str::lower(Str::random(10)).'.png', self::DISK);
        $this->deleteOld($signature->signature, 'signatures/', $path);

        $signature->signature = $path;
        $signature->save();

        return redirect()->route('staff.profile.my_signature')
                         ->with('success', $is_new ? 'Your signature has been uploaded.' : 'Your signature has been replaced.');
    }

    private function deleteOld(?string $old, string $folder, string $new): void
    {
        if ($old && $old !== $new && Str::startsWith($old, $folder)) {
            Storage::disk(self::DISK)->delete($old);
        }
    }

    private function showPerson(?User $person, ?Staff $staff)
    {
        $organ = null;

        if ($staff) {
            try {
                $organ = OrganClass::getOrganBySegment($staff);
            } catch (\Throwable $e) {
                $organ = null; // the office structure is optional
            }
        }

        return view('staff.profile.user_profile', [
            'person' => $person,
            'profile' => optional($person)->profile,
            'staff' => $staff,
            'organ' => $organ,
        ]);
    }
}
