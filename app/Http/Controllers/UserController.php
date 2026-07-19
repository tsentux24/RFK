<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $opds = Opd::orderBy('nama_opd', 'asc')->get();
        return view('dashboard.datauser', compact('opds'));
    }

    public function getData(Request $request)
    {
        try {
            Log::info('getData dipanggil dengan params: ', $request->all());

            $query = User::with('opd');

            // Filter pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter role
            if ($request->filled('role') && $request->role !== '') {
                $query->where('role', $request->role);
            }

            // Filter status
            if ($request->filled('status') && $request->status !== '') {
                $query->where('status', (int)$request->status);
            }

            // Filter OPD
            if ($request->filled('opd_id') && $request->opd_id !== '') {
                $query->where('opd_id', $request->opd_id);
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(10);

            Log::info('Jumlah user ditemukan: ' . $users->total());

            \Carbon\Carbon::setLocale('id');
            $items = collect($users->items())->map(function($user) {
                $userArray = $user->toArray();
                $userArray['last_login_human'] = $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : null;
                return $userArray;
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::with('opd')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            Log::info('Store user dipanggil dengan data: ', $request->all());

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role' => 'required|in:superadmin,administrator,staff,kepala_opd',
                'opd_id' => 'nullable|exists:opds,id',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'opd_id' => $request->opd_id,
                'status' => (int)$request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Error store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:superadmin,administrator,staff,kepala_opd',
                'opd_id' => 'nullable|exists:opds,id',
                'status' => 'required|in:0,1',
                'password' => 'nullable|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'opd_id' => $request->opd_id,
                'status' => (int)$request->status
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = User::with('opd');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('status')) {
                $query->where('status', (int)$request->status);
            }

            if ($request->filled('opd_id')) {
                $query->where('opd_id', $request->opd_id);
            }

            $users = $query->get();

            return response()->json([
                'success' => true,
                'data' => $users,
                'total' => $users->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ekspor: ' . $e->getMessage()
            ], 500);
        }
    }
}
