<?php

namespace App\Http\Controllers;

use App\Models\Hobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HobbyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $hobbies = Hobby::orderBy('updated_at', 'asc')->get();

    return response()->json([
        'success' => true,
        'message' => 'Data ditemukan',
        'data' => $hobbies,
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string'
        ]);

        DB::beginTransaction();

        try{
            $hobby = Hobby::create([
                'nama' => $validated['nama'],
                'deskripsi' => $validated['deskripsi'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan',
            ], 200);
        } catch(\Exception $e){
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Data gagal ditambahkan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    $hobby = Hobby::find($id);

    if (!$hobby) {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan',
        ], 404);
    }

    $validated = $request->validate([
        'nama' => 'required|string|max:255',
        'deskripsi' => 'required|string',
    ]);

    DB::beginTransaction();

    try {
        // Update data
        $hobby->deskripsi = $validated['deskripsi'];

        // Simpan perubahan ke database
        $hobby->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Hobi berhasil diupdate',
            'data' => $hobby
        ], 200);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Hobi gagal diupdate',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hobby = Hobby::find($id);

        if(!$hobby){
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();

        try{
            $hobby->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus',
            ], 200);
        } catch(\Exception $e){
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus',
            ], 500);
        }
    }
}
