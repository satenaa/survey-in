<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use Dompdf\Dompdf;
use App\Models\User;
use App\Models\Fasos;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\DataSurvey;
use App\Models\JenisFasos;
use App\Models\LampiranFoto;
use Illuminate\Http\Request;
use App\Models\DetailSurveys;
use App\Models\JenisLampiran;
use App\Models\RiwayatSurvey;
use App\Exports\DataSurveyExport;
use App\Http\Controllers\Controller;
use App\Models\JenisKonstruksiJalan;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\AssignOp\Mod;
use App\Models\JenisKonstruksiSaluran;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;
use Intervention\Image\Facades\Image;

// jika erorr menggunakan alert
// jalankan composer install
class AdminController extends Controller
{
    public function beranda()
    {

        $data = [
            'title' => 'Beranda',
            'active' => 'beranda',
            // 'profile' => User::where('role', 'admin')->get(['nama_lengkap', 'gender', 'alamat', 'nomor_telepon', 'email', 'role', 'avatar']),
            'kabupaten' => Kabupaten::get(['id', 'nama']),
        ];
        return view('admin.beranda', $data);
    }
    public function profile()
    {
        $data = [
            'title' => 'Profil',
            'active' => 'profile',
            'data' => auth()->user()
        ];
        return view('admin.profile', $data);
    }
    public function profileEdit()
    {
        $data = [
            'active' => 'Profile - Edit',
            'title' => 'Halaman Profil',
            'data' => auth()->user()
        ];
        return view('admin.edit-profile', $data);
    }
    public function profileUpdate(Request $request)
    {
        // ddd($request);
        $validateData = $request->validate([
            'nama_lengkap' => ['required'],
            'email' => ['required'],
            'tanggal_lahir' => ['required'],
            'gender' => ['required'],
            'nomor_telepon' => ['required'],
            'alamat' => ['required'],
            'avatar' => 'image|file|max:2048'
        ]);

        if ($request->file('avatar')) {
            if ($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $image = $request->file('avatar');
            $name['imgname'] = auth()->user()->nama_lengkap . '_' . uniqid() . '.' . $image->guessExtension();
            Image::make($image)->resize(115, 115)->save(public_path('storage/avatar-images/') . $name['imgname']);
            $image_path = "avatar-images/" . $name['imgname'];
            $validateData['avatar'] = $image_path;
        }
        try {
            User::where('id', $request->id)
                ->update($validateData);
            return redirect('/profile')
                ->with('success', 'Profil admin telah berhasil di edit')
                ->with('confirm', 'Ok');
        } catch (\Exception $e) {
            return redirect()->back()->withInput();
        }
    }
    public function surveyor()
    {
        return view('admin.surveyor', [
            'active' => 'surveyor',
            'title' => 'Surveyor',
            'surveyors' => User::where('role', 'surveyor')->get()
        ]);
    }

    public function surveyortambah()
    {
        return view('admin.surveyor.tambah', [
            'active' => 'surveyor',
            'title' => 'Surveyor - Tambah Surveyor',
            'kabupaten' => Kabupaten::all('id', 'nama')
        ]);
    }
    public function tambahSurveyor(Request $request)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'max:255'],
            'nomor_telepon' => ['required', 'numeric', 'unique:users'],
            'area' => ['required'],
            'email' => ['required', 'email:dns', 'unique:users'],
        ]);

        try {
            User::create([
                "nama_lengkap" => $request->nama_lengkap,
                "nomor_telepon" => $request->nomor_telepon,
                "email" => $request->email,
                "kabupaten_id" => $request->area,
                "password" => Hash::make('password')

            ]);
            return redirect('/surveyor')
                ->with('success', 'Akun telah berhasil ditambahkan !')
                ->with('confirm', 'Ok');
        } catch (\Exception $e) {
            return redirect()->back()->withInput();
        }
    }

    public function surveyorProfile($id)
    {
        $data = User::with(['detailSurvey.kecamatan', 'kabupaten'])->where('id', $id)->where('role', 'surveyor')->get();
        $selesai = 0;
        $target = 0;
        $weekly_target = 0;
        $weekly_selesai = 0;
        foreach ($data[0]->detailSurvey as $hasil) {
            $selesai = $selesai + $hasil->selesai;
            $target = $target + $hasil->target;
            $date1 = Carbon::now();
            $date2 = Carbon::createFromFormat('Y-m-d', $hasil->tanggal_selesai);
            if ($date1->gt($date2)) {
                $weekly_target = $hasil->target;
                $weekly_selesai = $hasil->selesai;
            }
        }

        $detail = [
            'active' => 'surveyor',
            'title' => 'Surveyor - Profil', [0],
            'profile_surveyor' => $data[0],
            'selesai' => $selesai,
            'target' => $target,
            'weekly_target' => $weekly_target,
            'weekly_selesai' => $weekly_selesai,
            'detailSurvey' => $data[0]->detailSurvey,
            'area' => $data[0]->kabupaten,
            'kabupaten' => Kabupaten::get()
        ];
        // dd($detail);
        return view('admin.surveyor.surveyor-profile', $detail);
    }
    public function updateSurveyor(Request $request)
    {
        if ($request->target == '1') {
            $request->validate([
                'nama_lengkap' => ['required'],
                'area' => ['required'],
                'nomor_telepon' => ['required'],
                'email' => ['required'],
            ]);
            try {
                User::where('id', $request->id)
                    ->update([
                        'nama_lengkap' => $request->nama_lengkap,
                        'kabupaten_id' => $request->area,
                        'nomor_telepon' => $request->nomor_telepon,
                        'email' => $request->email,
                    ]);
                return redirect('/surveyor/profile/' . $request->id)
                    ->with('success', 'Akun surveyor telah berhasil di edit')
                    ->with('confirm', 'OK');
            } catch (\Exception $e) {
                return redirect()->back()->withInput();
            }
        } elseif ($request->target == '2') {
            $request->validate([
                'password' => ['required', 'confirmed'],
                'password_confirmation' => ['required'],
            ]);
            try {
                User::where('id', $request->id)
                    ->update([
                        'password' => Hash::make($request->password)
                    ]);
                return redirect('/surveyor/profile/' . $request->id)
                    ->with('success', 'Password surveyor telah berhasil diubah')
                    ->with('confirm', 'OK');
            } catch (\Exception $e) {
                return redirect()->back()->withInput();
            }
        }
    }
    public function getSurveyor($action, $id)
    {
        if ($action == 'profile') {
            $data = [
                'active' => 'surveyor',
                'title' => 'Surveyor - Edit Profil Surveyor',
                'kabupaten' => Kabupaten::all(),
                'profile' => User::where('id', $id)
                    ->where('role', 'surveyor')
                    ->get()[0]
            ];
            return view('admin.surveyor.edit-profile', $data);
        } elseif ($action == 'password') {
            $data = [
                'active' => 'surveyor',
                'title' => 'Surveyor - Edit Password',
                'profile' => User::where('id', $id)
                    ->where('role', 'surveyor')
                    ->get(['avatar', 'nama_lengkap', 'role', 'id'])[0]
            ];
            return view('admin.surveyor.edit-password', $data);
        }
    }
    public function surveyorTarget($id)
    {
        $user = User::with('kabupaten.kecamatan')->find($id);
        $detail = DetailSurveys::where('user_id', $id)
            ->whereDate('tanggal_selesai', '>=', Carbon::now())
            ->get();
        if (count($detail) != 0) {
            $date1 = Carbon::now();
            $date2 = Carbon::createFromFormat('Y-m-d', $detail[0]->tanggal_selesai);
            $result = $date1->gt($date2);
        }

        $data = [
            'active' => 'surveyor',
            'title' => 'Surveyor - Tambah Target Surveyor', [0],
            'profile_surveyor' => $user,
            'kecamatans' => $user->kabupaten->kecamatan
        ];
        if (count($detail) == 0) {
            return view('admin.surveyor.add-surveyor-target', $data);
        } else if ($date1->gte($date2)) {
            return view('admin.surveyor.add-surveyor-target', $data);
        } else {
            $surveyor = User::with(['detailSurvey' => function ($query) {
                $query->whereDate('tanggal_selesai', '>=', Carbon::now());
            }])->where('id', $id)->get();
            $data = [
                'active' => 'surveyor',
                'title' => 'Surveyor - Edit Target Surveyor',
                'profile_surveyor' => $surveyor[0],
                'detail_survey' => $surveyor[0]->detailSurvey[0],
                'kecamatans' => $user->kabupaten->kecamatan
            ];
            return view('admin.surveyor.edit-surveyor-target', $data);
        }
    }
    public function addSurveyorTarget(Request $request)
    {
        $request->validate([
            'kecamatan' => ['required'],
            'tanggal_mulai' => ['required'],
            'target' => ['required'],
            'kategori' => ['required'],
        ]);
        $tanggal_selesai =  Carbon::createFromFormat('Y-m-d', $request->tanggal_mulai);
        $tanggal_selesai = $tanggal_selesai->addDays($request->kategori - 1);
        try {
            DetailSurveys::create([
                'user_id' => $request->id,
                'kecamatan_id' => $request->kecamatan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'target' => $request->target
            ]);
            return redirect('/surveyor')
                ->with('success', 'Berhasil menambahkan target surveyor')
                ->with('confirm', 'Ok');
        } catch (\Exception $e) {
            return redirect()->back()->withInput();
        }
    }
    public function editSurveyorTarget(Request $request)
    {
        $request->validate([
            'kecamatan' => ['required'],
            'tanggal_selesai' => ['required'],
            'target' => ['required'],
            'tanggal_selesai' => ['required']
        ]);
        try {
            DetailSurveys::where('id', $request->id)
                ->update([
                    'kecamatan_id' => $request->kecamatan,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_selesai' => $request->tanggal_selesai,
                    'target' => $request->target,
                ]);
            return redirect('/surveyor')
                ->with('success', 'Berhasil mengubah target surveyor')
                ->with('confirm', 'Ok');
        } catch (\Exception $e) {
            return redirect()->back()->withInput();
        }
    }
    public function destroySuyveyor(Request $request)
    {
        try {
            User::destroy($request->id);
            return redirect()->back()
                ->with('success', 'Berhasil Menghapus')->with('confirm', 'OK');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Mengapus Akun Surveyor');
        }
    }

    // Halaman Pengaturan Admin
    public function pengaturan()
    {
        return view('admin.pengaturan', [
            'active' => 'pengaturan',
            'title' => 'Pengaturan',
        ]);
    }
    public function editDataSurvey()
    {
        return view('admin.pengaturan.edit-data-survey', [
            'active' => 'pengaturan',
            'title' => 'Pengaturan-Edit Data',
            'jalan' => JenisKonstruksiJalan::all(),
            'saluran' => JenisKonstruksiSaluran::all(),
            'sosial' => JenisFasos::all(),
            'lampiran' => JenisLampiran::all(),
        ]);
    }
    public function createData($model, Request $data)
    {
        switch ($model) {
            case 'jalan':
                $data->validate([
                    'jalan' => ['required', 'unique:jenis_konstruksi_jalans,jenis']
                ]);
                JenisKonstruksiJalan::create([
                    "jenis" => $data->jalan,
                ]);
                return redirect()->back()
                    ->with('tsuccess', 'Data berhasil di data');
                break;
            case 'saluran':
                $data->validate([
                    'saluran' => ['required', 'unique:jenis_konstruksi_salurans,jenis']
                ]);

                JenisKonstruksiSaluran::create([
                    "jenis" => $data->saluran,
                ]);
                return redirect()->back()
                    ->with('tsuccess', 'Data berhasil di tambahkan');
                break;
            case 'fasos':
                $data->validate([
                    'fasos' => ['required', 'unique:jenis_fasos,jenis']
                ]);

                JenisFasos::create([
                    "jenis" => $data->fasos,
                ]);
                return redirect()->back()
                    ->with('tsuccess', 'Data berhasil di tambahkan');
                break;
            case 'lampiran':
                $data->validate([
                    'lampiran' => ['required', 'unique:jenis_lampirans,jenis']
                ]);
                JenisLampiran::create([
                    "jenis" => $data->lampiran,
                ]);
                return redirect()->back()
                    ->with('tsuccess', 'Data berhasil di tambahkan');
                break;
            default:
                return redirect('/pengaturan/edit-data-survey');
        };

        return redirect('/pengaturan/edit-data-survey')->withInput();
    }
    public function editData(Request $request)
    {
        switch ($request->model) {
            case 'jalan':
                JenisKonstruksiJalan::where('id', $request->id)->update([
                    'jenis' => $request->jenis
                ]);
                return redirect()->back()
                    ->with('success', 'Berhasil diubah')
                    ->with('confirm', 'Ok');
                break;
            case 'saluran':
                JenisKonstruksiSaluran::where('id', $request->id)->update([
                    'jenis' => $request->jenis
                ]);
                return redirect()->back()
                    ->with('success', 'Berhasil diubah')
                    ->with('confirm', 'Ok');
                break;
            case 'fasos':
                JenisFasos::where('id', $request->id)->update([
                    'jenis' => $request->jenis
                ]);
                return redirect()->back()
                    ->with('success', 'Berhasil diubah')
                    ->with('confirm', 'Ok');
                break;
            case 'lampiran':
                JenisLampiran::where('id', $request->id)->update([
                    'jenis' => $request->jenis
                ]);
                return redirect()->back()
                    ->with('success', 'Berhasil diubah')
                    ->with('confirm', 'Ok');
                break;
            default:
                return redirect()->back()->with('error', 'Gagal Mengubah');
        };
    }
    public function destroy(Request $request)
    {
        switch ($request->model) {
            case 'jalan':
                JenisKonstruksiJalan::destroy($request->id);
                break;
            case 'saluran':
                JenisKonstruksiSaluran::destroy($request->id);
                break;
            case 'fasos':
                JenisFasos::destroy($request->id);
                break;
            case 'lampiran':
                JenisLampiran::destroy($request->id);
                break;
            default:
                return redirect()->back()->with('terror', 'Data gagal di hapus');;
        };

        return redirect('/pengaturan/edit-data-survey')->with('tsuccess', 'Data berhasil di hapus');
    }
    public function ubahPassword(Request $request)
    {
        return view('admin.pengaturan.ubah-password', [
            'active' => 'pengaturan',
            'title' => 'Pengaturan - Ubah Password', [0],
        ]);
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'kata_sandi_lama' => ['required', 'min:8'],
            'kata_sandi_baru' => ['required', 'min:8', 'confirmed'],
            'kata_sandi_baru_confirmation' => ['required', 'min:8']
        ]);

        $admin = User::where('role', 'admin')->where('id', auth()->user()->id)->get()[0];
        $currentPassword = $admin->password;
        $kata_sandi_lama = request('kata_sandi_lama');

        if (Hash::check($kata_sandi_lama, $currentPassword)) {
            $admin->update([
                'password' => Hash::make($request->kata_sandi_baru)
            ]);
            return redirect('/pengaturan')
                ->with('success', 'Password anda berhasil diubah')->with('confirm', 'Ok');
        } else {
            return back()->withErrors(['kata_sandi_lama' => 'Kata sandi tidak cocok!']);
        }
    }
    public function detailDataSurvei($id)
    {
        $data = DataSurvey::with(['user', 'konstruksiJalan', 'konstruksiSaluran', 'kecamatan', 'fasosTable.jenisFasos', 'lampiranFoto.jenisLampiran'])->where('id', $id)->get();
        return view('admin.data-survei.detail-data-survei', [
            'title' => 'Data Survey',
            'data' => $data[0],
        ]);
    }
    public function destroyDataSurvei(Request $request)
    {
        try {
            DataSurvey::destroy($request->id);
            return redirect()->back()
                ->with('success', 'Berhasil Menghapus Data Survey')->with('confirm', 'Ok');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Menghapus Data Survey');
        }
    }
}
