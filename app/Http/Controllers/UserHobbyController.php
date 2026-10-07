<?php

namespace App\Http\Controllers;

// use App\Models\User;

use App\Models\User;
use App\Models\UserHobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserHobbyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $users = UserHobby::with('user', 'hobby')
        ->orderBy('updated_at', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $users
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request){
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',

            'hobbies' => 'required|array',
            'hobbies.*' => 'exists:hobbies,id',
            ]);

            DB::beginTransaction();

            try{
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                ]);


                foreach($validated['hobbies'] as $hobbyId){
                    UserHobby::create([
                        'id_users' => $user->id,
                        'id_hobbies' => $hobbyId,
                    ]);
                }
                DB::commit();
               $user->load('userHobbies.hobby');

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil ditambahkan',
                    'data' => $user,
                ], 200);
                } catch(\Exception $e){
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Data gagal dibuat',
                        'error' => $e->getMessage(),
                    ], 500);
                }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $userHobby = UserHobby::with(['user', 'hobby'])
        ->where('id', $id)
        ->first();

        if(!$userHobby){
            return response()->json([
                'success' => false,
                'message' => 'Data user Tidak Ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $userHobby
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
            $user = User::find($id);

            if(!$user){
                return response()->json([
                    'success' => false,
                    'message' => 'user tidak ditemukan',
                ], 404); 
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id),
                    ],
                'password' => 'nullable|string|min:6',
                'phone' => 'required|string|max:20',
                'address'=>'required|string',

                'hobbies' => 'required|array',
                'hobbies.*' => 'exists:hobbies,id',
            ]);

            DB::beginTransaction();

            try{

                $user->name = $validated['name'];
                $user->email = $validated['email'];
                $user->phone = $validated['phone'];
                $user->address = $validated['address'];

                if (!empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                    }

                $user->save();

                UserHobby::where('id_users', $user->id)->delete();

                foreach($validated['hobbies'] as $hobbyId){
                    UserHobby::create([
                        'id_users' => $user->id,
                        'id_hobbies' => $hobbyId,
                    ]);
                }

                DB::commit();

                $user->load('userHobbies.hobby');

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil di update',
                ], 200);
                } catch(\Exception $e){
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Data gagal di update',
                        'error' => $e->getMessage(),
                    ], 500);
            }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json([
                'success' => false,
                'message'=> 'Data user tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();

        try{
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di hapus',
            ], 200);
        }
        catch(\Exception $e){
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus',
            ],500);
        }
    }
}
