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

        $users = User::query()
            ->when($search, fn($q) => $q->where('username', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")
                ->orWhere('vessel_name', 'ilike', "%{$search}%"))
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

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

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
        
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
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