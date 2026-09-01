<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected ApiService $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $viewerRole = session('user_role') ?? session('api_user.role');

        $users = User::query()
            // Hide developer account(s) from non-developer (shore admin) viewers, so
            // only a developer can see/manage the developer account. Developer sees all.
            ->when($viewerRole !== 'developer', fn($q) => $q->where(
                fn($sub) => $sub->whereNull('role')->orWhere('role', '!=', 'developer')
            ))
            ->when($search, fn($q) => $q->where(
                fn($sub) => $sub->where('username', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('vessel_name', 'ilike', "%{$search}%")
            ))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('users.index', compact('users', 'search'));
    }

    // ================= CREATE USER =================
    public function create()
    {
        return view('users.create');
    }

    // ================= STORE USER =================
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'address' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:150',
            'vessel_name' => 'nullable|string|max:255',
            'token_type' => 'nullable|string',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'age' => $request->age,
            'vessel_name' => $request->vessel_name,
            'token_type' => $request->token_type,
        ]);

        // Post-create verification ping: confirm the configured API is actually reachable,
        // since mobile apps read users through it, not through this portal's local database.
        $apiCheck = $this->api->getUser();
        if (!($apiCheck['success'] ?? false) && ($apiCheck['status'] ?? 0) !== 401) {
            return redirect()->route('users.index')->with('warning',
                'User created locally but the configured API is unreachable — check EXTERNAL_API_BASE_URL; mobile apps may not see this vessel user.'
            );
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    // ================= EDIT USER =================
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // ================= UPDATE USER =================
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'address' => 'nullable|string',
            'age' => 'nullable|integer|min:1|max:150',
            'vessel_name' => 'nullable|string|max:255',
            'token_type' => 'nullable|string',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'address' => $request->address,
            'age' => $request->age,
            'vessel_name' => $request->vessel_name,
            'token_type' => $request->token_type,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    // ================= UPDATE PASSWORD =================
    public function editPassword($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit-password', compact('user'));
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Same self/other distinction destroy() uses: the portal authenticates via session
        // (api_user/user_id), not Laravel's auth guard.
        $currentUserId = session('user_id') ?? session('api_user.id');
        $isSelfService = $currentUserId !== null && (int) $currentUserId === (int) $user->id;

        $rules = [
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        if ($isSelfService) {
            $rules['current_password'] = ['required', 'string'];
        }

        $request->validate($rules);

        if ($isSelfService && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Password updated successfully!');
    }

    // ================= DELETE USER =================
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself. The portal authenticates via session (api_user/user_id),
        // not Laravel's auth guard, so compare against the session-stored id instead of auth()->id().
        $currentUserId = session('user_id') ?? session('api_user.id');
        if ($currentUserId !== null && (int) $currentUserId === (int) $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function exportExcel(Request $request)
    {
        $users = User::all()->toArray();
        return Excel::download(new UsersExport($users), 'users-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $users = User::all()->toArray();
        $pdf = Pdf::loadView('exports.users-pdf', compact('users'))->setPaper('a4');
        return $pdf->download('users-' . now()->format('Y-m-d') . '.pdf');
    }
}