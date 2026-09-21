<?php
namespace App\Http\Controllers;

use App\Exports\TemplateSiswaExport;
use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    /**
     * Menampilkan seluruh data siswa.
     */
    public function index(Request $request)
    {
        $query = Siswa::with([
            'kelas',
            'orangTua',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')

                    ->orWhereHas('kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where(
                            'nama_kelas',
                            'like',
                            '%' . $search . '%'
                        );
                    })

                    ->orWhereHas('orangTua', function ($orangTuaQuery) use ($search) {
                        $orangTuaQuery->where(
                            'nama',
                            'like',
                            '%' . $search . '%'
                        );
                    });

            });
        }

        $siswa = $query
            ->latest()
            ->get();

        return view('admin.siswa.index', compact('siswa'));
    }

    /**
     * Menampilkan halaman tambah siswa.
     */
    public function create()
    {
        $kelas = Kelas::query()
            ->orderBy('nama_kelas')
            ->get();

        $orangTua = OrangTua::with('user')
            ->orderBy('nama')
            ->get();

        return view(
            'admin.siswa.create',
            compact(
                'kelas',
                'orangTua'
            )
        );
    }

    /**
     * Menyimpan data siswa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'           => [
                'required',
                'string',
                'max:50',
                'unique:siswa,nis',
            ],

            'nama'          => [
                'required',
                'string',
                'max:255',
            ],

            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],

            'alamat'        => [
                'nullable',
                'string',
            ],

            'kelas_id'      => [
                'required',
                'exists:kelas,id',
            ],

            'orang_tua_id'  => [
                'required',
                'exists:orang_tua,id',
            ],
        ], [
            'nis.required'           => 'NIS wajib diisi.',
            'nis.unique'             => 'NIS sudah terdaftar.',
            'nama.required'          => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid.',
            'kelas_id.required'      => 'Kelas wajib dipilih.',
            'kelas_id.exists'        => 'Kelas tidak ditemukan.',
            'orang_tua_id.required'  => 'Orang tua wajib dipilih.',
            'orang_tua_id.exists'    => 'Orang tua tidak ditemukan.',
        ]);

        Siswa::create($validated);

        return redirect()
            ->route('admin.siswa.index')
            ->with(
                'success',
                'Data siswa berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail siswa.
     */
    public function show(Siswa $siswa)
    {
        $siswa->load([
            'kelas',
            'orangTua',
            'tagihan',
            'pembayaran',
        ]);

        return view(
            'admin.siswa.show',
            compact('siswa')
        );
    }

    /**
     * Menampilkan halaman edit siswa.
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::query()
            ->orderBy('nama_kelas')
            ->get();

        $orangTua = OrangTua::with('user')
            ->orderBy('nama')
            ->get();

        return view(
            'admin.siswa.edit',
            compact(
                'siswa',
                'kelas',
                'orangTua'
            )
        );
    }

    /**
     * Memperbarui data siswa.
     */
    public function update(
        Request $request,
        Siswa $siswa
    ) {
        $validated = $request->validate([
            'nis'           => [
                'required',
                'string',
                'max:50',
                Rule::unique('siswa', 'nis')
                    ->ignore($siswa->id),
            ],

            'nama'          => [
                'required',
                'string',
                'max:255',
            ],

            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P']),
            ],

            'alamat'        => [
                'nullable',
                'string',
            ],

            'kelas_id'      => [
                'required',
                'exists:kelas,id',
            ],

            'orang_tua_id'  => [
                'required',
                'exists:orang_tua,id',
            ],
        ], [
            'nis.required'           => 'NIS wajib diisi.',
            'nis.unique'             => 'NIS sudah digunakan oleh siswa lain.',
            'nama.required'          => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid.',
            'kelas_id.required'      => 'Kelas wajib dipilih.',
            'kelas_id.exists'        => 'Kelas tidak ditemukan.',
            'orang_tua_id.required'  => 'Orang tua wajib dipilih.',
            'orang_tua_id.exists'    => 'Orang tua tidak ditemukan.',
        ]);

        $siswa->update($validated);

        return redirect()
            ->route('admin.siswa.index')
            ->with(
                'success',
                'Data siswa berhasil diperbarui.'
            );
    }

    /**
     * Menghapus data siswa.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()
            ->route('admin.siswa.index')
            ->with(
                'success',
                'Data siswa berhasil dihapus.'
            );
    }

    /**
     * Menampilkan halaman import siswa melalui Excel.
     */
    public function importForm()
    {
        return view('admin.siswa.import');
    }

    /**
     * Download template Excel siswa.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new TemplateSiswaExport(),
            'format-import-siswa.xlsx'
        );
    }

    /**
     * Import data siswa melalui Excel.
     *
     * Proses import menggunakan database transaction.
     * Jika terjadi kesalahan, seluruh proses import dibatalkan.
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:5120',
            ],
        ], [
            'file.required' => 'File Excel wajib dipilih.',

            'file.file'     => 'File yang dikirim tidak valid.',

            'file.mimes'    => 'File harus berformat XLSX, XLS, atau CSV.',

            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                Excel::import(
                    new SiswaImport(),
                    $request->file('file')
                );
            });

            return redirect()
                ->route('admin.siswa.index')
                ->with(
                    'success',
                    'Data siswa dan orang tua berhasil diimport.'
                );
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Import gagal: ' . $exception->getMessage()
                );
        }
    }
}
