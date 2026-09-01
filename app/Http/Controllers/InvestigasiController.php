<?php

namespace App\Http\Controllers;

use App\Models\Investigasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvestigasiController extends Controller
{
    /**
     * Menampilkan semua data Pra Registrasi beserta relationship.
     */
    public function index()
    {
        return view('pra-registrasi.index');
    }

    /**
     * Menyediakan data JSON untuk halaman Pra Registrasi.
     */
    public function data(Request $request)
    {
        $perPage = 10;
        $search = trim((string) $request->input('search', ''));

        $query = Investigasi::with([
            'asuransi',
            'jenisClaim',
            'investigator',
        ])->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('no_case', 'like', "%{$search}%")
                    ->orWhere('number_case', 'like', "%{$search}%")
                    ->orWhere('no_polis', 'like', "%{$search}%")
                    ->orWhere('nm_tertanggung', 'like', "%{$search}%")
                    ->orWhere('nm_pemegang_polis', 'like', "%{$search}%")
                    ->orWhere('diagnosa_utama', 'like', "%{$search}%")
                    ->orWhere('rumah_sakit', 'like', "%{$search}%");
            });
        }

        $investigasis = $query->paginate($perPage)->appends(['search' => $search]);

        return response()->json([
            'success' => true,
            'data' => $investigasis->items(),
            'pagination' => [
                'current_page' => $investigasis->currentPage(),
                'last_page' => $investigasis->lastPage(),
                'per_page' => $investigasis->perPage(),
                'total' => $investigasis->total(),
                'prev_page_url' => $investigasis->previousPageUrl(),
                'next_page_url' => $investigasis->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan data Pra Registrasi baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $asuransiId = $request->input('asuransi_id');
        $numberCase = $this->generateNumberCase();

        $investigasi = new Investigasi();

        $investigasi->no_case = $this->generateNoCase($asuransiId);
        $investigasi->number_case = $numberCase;
        $investigasi->tgl_registrasi = $request->input('tgl_registrasi');
        $investigasi->tgl_kirim_laporan = $request->input('tgl_kirim_laporan');
        $investigasi->asuransi_id = $request->input('asuransi_id');
        $investigasi->no_polis = $request->input('no_polis');
        $investigasi->nm_tertanggung = $request->input('nm_tertanggung');
        $investigasi->nm_pemegang_polis = $request->input('nm_pemegang_polis');
        $investigasi->nm_agen = $request->input('nm_agen');
        $investigasi->uang_pertanggungan = $request->input('uang_pertanggungan');
        $investigasi->alamat_provinsi = $request->input('alamat_provinsi');
        $investigasi->alamat_kabupaten = $request->input('alamat_kabupaten');
        $investigasi->alamat_kecamatan = $request->input('alamat_kecamatan');
        $investigasi->alamat_tertanggung = $request->input('alamat_tertanggung');
        $investigasi->tgl_spaj = $request->input('tgl_spaj');
        $investigasi->tgl_efektif_polis = $request->input('tgl_efektif_polis');
        $investigasi->tgl_joint = $request->input('tgl_joint');
        $investigasi->usia_polis = $request->input('usia_polis');
        $investigasi->pekerjaan = $request->input('pekerjaan');
        $investigasi->matauang = $request->input('matauang');
        $investigasi->premi = $request->input('premi');
        $investigasi->total_premi = $request->input('total_premi');
        $investigasi->jml_klaim = $request->input('jml_klaim');
        $investigasi->tempat_meninggal = $request->input('tempat_meninggal');
        $investigasi->tgl_meninggal = $request->input('tgl_meninggal');
        $investigasi->diagnosa_utama = $request->input('diagnosa_utama');
        $investigasi->tgl_dirawat_dr = $request->input('tgl_dirawat_dr');
        $investigasi->tgl_dirawat_smp = $request->input('tgl_dirawat_smp');
        $investigasi->jenisclaim_id = $request->input('jenisclaim_id');
        $investigasi->rumah_sakit = $request->input('rumah_sakit');
        $investigasi->area_investigasi = $request->input('area_investigasi');
        $investigasi->provinsi = $request->input('provinsi');
        $investigasi->kepemilikan_asuransi_lain = $request->input('kepemilikan_asuransi_lain');
        $investigasi->investigasi_fee = $request->input('investigasi_fee');
        $investigasi->investigator_id = $request->input('investigator_id');
        $investigasi->informasi_lain = $request->input('informasi_lain');
        $investigasi->tgl_kirim_dokumen = $request->input('tgl_kirim_dokumen');
        $investigasi->tambahan_waktu = $request->input('tambahan_waktu');
        $investigasi->pengaju_klaim = $request->input('pengaju_klaim');
        $investigasi->kronologi_singkat = $request->input('kronologi_singkat');
        $investigasi->metode_investigasi = $request->input('metode_investigasi');
        $investigasi->agen_terlibat = $request->input('agen_terlibat');
        $investigasi->nama_peserta = $request->input('nama_peserta');
        $investigasi->nomor_peserta = $request->input('nomor_peserta');
        $investigasi->tgl_mulai = $request->input('tgl_mulai');
        $investigasi->tgl_pengajuan = $request->input('tgl_pengajuan');
        $investigasi->tgl_selesai = $request->input('tgl_selesai');
        $investigasi->tgl_klaim = $request->input('tgl_klaim');
        $investigasi->plan = $request->input('plan');
        $investigasi->status = 0;
        $investigasi->status_sent_client = 0;
        $investigasi->user_id = Auth::id();

        $investigasi->save();

        return response()->json([
            'success' => true,
            'message' => 'Data Pra Registrasi berhasil dibuat sebagai Draft.',
            'data' => $investigasi->fresh()->load([
                'asuransi',
                'jenisClaim',
                'investigator',
            ]),
        ], 201);
    }

    /**
     * Menampilkan detail satu data Pra Registrasi.
     */
    public function show(Investigasi $investigasi)
    {
        $investigasi->load([
            'asuransi',
            'jenisClaim',
            'investigator',
        ]);

        return response()->json([
            'success' => true,
            'data' => $investigasi,
        ]);
    }

    /**
     * Memperbarui data Pra Registrasi.
     *
     * Hanya Draft yang boleh diedit.
     */
    public function update(Request $request, Investigasi $investigasi)
    {
        if ((int) $investigasi->status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data hanya dapat diedit ketika masih berstatus Draft.',
            ], 422);
        }

        $validated = $request->validate($this->rules());

        // Status workflow tidak boleh diubah lewat endpoint update biasa.
        unset(
            $validated['status'],
            $validated['status_sent_client'],
            $validated['user_id_approve']
        );

        $investigasi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Pra Registrasi berhasil diperbarui.',
            'data' => $investigasi->fresh()->load([
                'asuransi',
                'jenisClaim',
                'investigator',
            ]),
        ]);
    }

    /**
     * Menghapus data Pra Registrasi.
     *
     * Hanya Draft yang boleh dihapus.
     */
    public function destroy(Investigasi $investigasi)
    {
        if ((int) $investigasi->status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data yang sudah diajukan tidak dapat dihapus.',
            ], 422);
        }

        $investigasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Pra Registrasi berhasil dihapus.',
        ]);
    }

    /**
     * Mengajukan Draft.
     *
     * Draft / diajukan tetap memakai status 0,
     * karena status 1 dipakai untuk case yang sudah approve / done.
     */
    public function submit(Investigasi $investigasi)
    {
        if ((int) $investigasi->status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya data Draft yang dapat diajukan.',
            ], 422);
        }

        // Status 1 dipakai untuk case yang sudah di-approve / done.
        // Oleh karena itu, proses pengajuan tidak mengisi status 1.
        $investigasi->update([
            'status' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diajukan.',
            'data' => $investigasi->fresh(),
        ]);
    }

    /**
     * Menyetujui data yang sudah diajukan.
     *
     * Draft / diajukan (0) -> Approve / Done (1)
     */
    public function approve(Investigasi $investigasi)
    {
        if ((int) $investigasi->status !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data hanya dapat disetujui ketika masih dalam status Draft / diajukan.',
            ], 422);
        }

        $investigasi->update([
            'status' => 1,
            'user_id_approve' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disetujui / done.',
            'data' => $investigasi->fresh(),
        ]);
    }

    /**
     * Menandai data sudah dikirim ke client.
     */
    public function sendClient(Investigasi $investigasi)
    {
        if ((int) $investigasi->status !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus sudah di-approve / done sebelum dikirim ke client.',
            ], 422);
        }

        if ((int) $investigasi->status_sent_client === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Data sudah pernah dikirim ke client.',
            ], 422);
        }

        $investigasi->update([
            'status_sent_client' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditandai sebagai sudah dikirim ke client.',
            'data' => $investigasi->fresh(),
        ]);
    }

    /**
     * Menyelesaikan proses.
     *
     * Approve / Done + sudah dikirim -> Selesai (2)
     */
    public function complete(Investigasi $investigasi)
    {
        if ((int) $investigasi->status !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Data belum berada pada tahap approve / done.',
            ], 422);
        }

        if ((int) $investigasi->status_sent_client !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Data harus dikirim ke client terlebih dahulu.',
            ], 422);
        }

        $investigasi->update([
            'status' => 2,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proses Pra Registrasi telah selesai.',
            'data' => $investigasi->fresh(),
        ]);
    }

    /**
     * Generate nomor case otomatis berdasarkan data terakhir.
     *
     * Mengikuti logic InvestigasiControllerraise.php: number_case adalah
     * counter global (MAX dari seluruh data investigasis), tidak dipisah
     * per asuransi.
     */
    public function generateNumberCase(): int
    {
        $numbercase = DB::table('investigasis')
            ->select('investigasis.number_case')
            ->orderBy('number_case', 'desc')
            ->first();

        if (empty($numbercase)) {
            return 1;
        }

        return (int) $numbercase->number_case + 1;
    }

    /**
     * Generate no_case otomatis.
     *
     * Mengikuti logic generateNo() di registrasi_blade.php (raise):
     * prefix diambil dari kd_perusahaan asuransi, diikuti bulan.tahun,
     * TANPA nomor urut ditempel di belakang.
     * Format: {KD_PERUSAHAAN}/{BULAN}.{TAHUN}/
     */
    public function generateNoCase($asuransiId = null): string
    {
        $prefix = 'ASURANSI';

        if ($asuransiId !== null && $asuransiId !== '') {
            $asuransi = \App\Models\Asuransi::find((int) $asuransiId);

            if ($asuransi && $asuransi->kd_perusahaan) {
                $prefix = trim((string) $asuransi->kd_perusahaan);
            }
        }

        $month = date('n');
        $year = date('Y');

        return sprintf('%s/%s.%s/', $prefix, $month, $year);
    }

    public function nextNumberCase(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'number_case' => $this->generateNumberCase(),
            ],
        ]);
    }

    /**
     * Validation rules untuk store dan update.
     */
    private function rules(): array
    {
        return [
            // Identitas case
            'no_case' => 'nullable|string|max:255',
            'number_case' => 'nullable|integer',
            'tgl_registrasi' => 'nullable|date',
            'tgl_kirim_laporan' => 'nullable|date',
            'alamat_provinsi' => 'nullable|string|max:255',
            'alamat_kabupaten' => 'nullable|string|max:255',
            'alamat_kecamatan' => 'nullable|string|max:255',
            'usia_polis' => 'nullable|integer',

            // Polis
            'no_polis' => 'nullable|string|max:255',
            'nm_tertanggung' => 'nullable|string|max:255',
            'nm_pemegang_polis' => 'nullable|string|max:255',
            'nm_agen' => 'nullable|string|max:255',

            // Nilai
            'uang_pertanggungan' => 'nullable|numeric',
            'premi' => 'nullable|numeric',
            'total_premi' => 'nullable|numeric',
            'jml_klaim' => 'nullable|numeric',
            'investigasi_fee' => 'nullable|numeric',

            // Tanggal polis
            'tgl_spaj' => 'nullable|date',
            'tgl_efektif_polis' => 'nullable|date',
            'tgl_joint' => 'nullable|date',
            'tgl_kirim_dokumen' => 'nullable|date',
            'tambahan_waktu' => 'nullable|integer',
            'tgl_mulai' => 'nullable|date',
            'tgl_pengajuan' => 'nullable|date',
            'tgl_selesai' => 'nullable|date',
            'tgl_klaim' => 'nullable|date',

            // Informasi meninggal / klaim
            'tgl_meninggal' => 'nullable|date',
            'tgl_dirawat_dr' => 'nullable|date',
            'tgl_dirawat_smp' => 'nullable|date',

            // Referensi
            'asuransi_id' => 'nullable|integer|exists:asuransis,id',
            'jenisclaim_id' => 'nullable|integer|exists:jenis_claims,id',
            'investigator_id' => 'nullable|integer|exists:investigators,id',
            'matauang' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kepemilikan_asuransi_lain' => 'nullable|string|max:255',

            // Informasi tambahan
            'tempat_meninggal' => 'nullable|string|max:255',
            'diagnosa_utama' => 'nullable|string|max:255',
            'rumah_sakit' => 'nullable|string|max:255',
            'area_investigasi' => 'nullable|string|max:255',
            'alamat_tertanggung' => 'nullable|string',
            'pekerjaan' => 'nullable|string|max:255',
            'informasi_lain' => 'nullable|string',
            'pengaju_klaim' => 'nullable|string|max:255',
            'kronologi_singkat' => 'nullable|string',
            'metode_investigasi' => 'nullable|string|max:255',
            'agen_terlibat' => 'nullable|string|max:255',
            'plan' => 'nullable|string|max:255',
            'nama_peserta' => 'nullable|string|max:255',
            'nomor_peserta' => 'nullable|string|max:255',
        ];
    }
}