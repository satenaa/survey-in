@extends('admin.main')
@section('title', 'Surveyor')
@section('main-content')
    @include('admin.header')
    <!-- content -->
    <div class="content d-flex flex-column" id="surveyor-profile">
        <div class="surveyor-hl ms-0 ms-sm-5">
            <h1>Profil Surveyor</h1>
            <p class="mb-5">Dibawah ini adalah informasi lengkap <br> dari surveyor</p>

            <!-- avatar -->
            <div class="surveyor">
                @if ($profile_surveyor->avatar)
                <img src="{{ asset('storage/' . $profile_surveyor->avatar) }} " class="profile-img rounded-circle">
                @else
                <img src="/img/profile.png" class="profile-img rounded-circle">
                @endif
            </div>
            <div class="profile-status mt-3 d-flex flex-column">
                <h3>{{ ucwords($profile_surveyor->nama_lengkap) }}</h3>
                <p>{{ ucwords($profile_surveyor->role) }}</p>
            </div>
        </div>

        <div class="data-surveyor p-0 p-sm-5">
            <!-- Riwayat -->
            <div class="riwayat d-flex justify-content-end mb-2" data-bs-toggle="modal" data-bs-target="#riwayatModal">
                <span data-bs-toggle="modal" data-bs-target="#surveyModal" class="riwayat-survey me-2 btn btn-outline-primary" >Riwayat Survey</span>
                <span data-bs-toggle="modal" data-bs-target="#riwayatModal" class="riwayat-target me-1 btn btn-outline-primary ">Riwayat Target</span> 
            </div>
            <input type="hidden" value="{{ $profile_surveyor->id }}" id="data-id">
            <!-- Modal Riwayat Target -->
            <div class="modal fade mt-0" id="riwayatModal" tabindex="-1" aria-labelledby="riwayatModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="riwayatModalLabel">Riwayat Target</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-tabel">
                                <table class="table" id="tabel-riwayat">
                                    <thead class="judul-tabel border-bottom-1">
                                        <tr>
                                            <th class="p-0" scope="col">Surveyor</th>
                                            <th class="p-0" scope="col">Kecamatan</th>
                                            <th class="p-0" scope="col">Jenis Target</th>
                                            <th class="p-0" scope="col">Tanggal Mulai</th>
                                            <th class="p-0" scope="col">Tanggal Selesai</th>
                                            <th class="p-0" scope="col">Hasil Target</th>
                                            <th class="p-0" scope="col">Perhitungan Target</th>
                                        </tr>
                                    </thead>
                                    <tbody class="isi-tabel">
                                        @foreach ($detailSurvey as $item)
                                            <tr>
                                                <td>{{ $profile_surveyor->nama_lengkap }}</td>
                                                <td>{{ $item->kecamatan->nama }}</td>
                                                <td>
                                                    @php
                                                        $selisih=\Carbon\Carbon::createFromTimestamp(strtotime($item->tanggal_mulai))->diff(\Carbon\Carbon::createFromTimestamp(strtotime($item->tanggal_selesai)))->days;
                                                        $selisih= $selisih+1;
                                                    @endphp
                                                    @if ($selisih !=6 && $selisih !=7)
                                                        {{ $selisih }} hari
                                                    @else
                                                        Per-minggu
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('j F Y') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('j F Y') }}
                                                </td>

                                                <td>{{ $item->selesai }} dari {{ $item->target }} Gang dan Perumahan
                                                </td>
                                                <td
                                                    class="{{ $item->selesai - $item->target < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                                    @if ($item->selesai - $item->target > 0)
                                                        + {{ $item->selesai - $item->target }} Gang dan Perumahan
                                                    @elseif ($item->selesai - $item->target == 0)
                                                        Survey Sukses
                                                    @elseif($item->selesai - $item->target < 0)
                                                        {{$item->selesai - $item->target }}
                                                        Gang dan Perumahan
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Riwayat Target -->

            <!-- Modal Riwayat Survey -->
            <div class="modal fade mt-0" id="surveyModal" tabindex="-1" aria-labelledby="riwayatModalLabelSurvey" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="riwayatModalLabel">Riwayat Survey {{ $profile_surveyor->nama_lengkap }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-tabel">
                                <div class="pilih w-100 d-flex flex-column container-fluid">
                                    {{-- <h1 class="dasur-content w-100 text-center mt-4">
                                        Pencarian Hasil Survey
                                    </h1>
                                    <p class="dasur-content w-100 text-center mb-4">
                                        Temukan hasil Survey Gang dan Perumahan <br> di Kecamatan <span class="text-kec"></span>
                                    </p> --}}
                                        <div class="row justify-content-around my-3 col-12 d-flex flex-column flex-sm-row">
                                            <div class="col-sm-5 col-12">
                                                <div class="input-group mb-3">
                                                    <label class="input-group-text fw-bold" for="kabupaten">Kabupaten/Kota</label>
                                                    <select class="form-select" id="kabupaten" name="kabupaten">
                                                        <option selected>Pilih kota/kabupaten</option>
                                                        @foreach ($kabupaten as $item)
                                                            <option value="{{ $item->id }}">
                                                                {{ $item->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-5 col-12">
                                                <div class="input-group mb-3">
                                                    <label class="input-group-text fw-bold" for="kecamatan">Kecamatan</label>
                                                    <select class="form-select" id="kecamatan" name="kecamatan">
                                                        <option value="" selected> Pilih Kecamatan</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div class="form-dasur ps-4 pe-4 mb-4 mt-4">
                                    <table class="table table-hover bg-white shadow-sm table-responsive flex-column" id="dasur-table" style="width: 100%;">
                                        <thead>
                                            <tr style="vertical-align: middle">
                                                <th scope="col" style="width: 30%;">Nama Gang dan Perumahan</th>
                                                <th scope="col" style="width: 25%;">Lokasi</th>
                                                <th scope="col" style="width: 25%;">koordinat Depan</th>
                                                <th scope="col" style="width: 20%;">Aktivitas</th>
                                            </tr>
                                        </thead>
                                        <tbody id="data" class="data">
                                            <script type="module" src="/js/data-survei-single.js"></script>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Riwayat Survey -->


            <!--  Data Profil Surveyor -->
            <div class="biodata p-0 p-sm-3">
                <table class="bio">
                    <tr>
                        <td class="left-bio">Nama Lengkap</td>
                        <td style="width: 3%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">{{ ucwords($profile_surveyor->nama_lengkap) }}</td>
                    </tr>
                    <tr>
                        <td class="left-bio">Wilayah Survey</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">Kabupaten {{ ucwords($area->nama) }}</td>
                    </tr>
                    <tr>
                        <td class="left-bio">Email</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">{{ $profile_surveyor->email }}</td>
                    </tr>
                    <tr>
                        <td class="left-bio">No. Handphone</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">{{ $profile_surveyor->nomor_telepon }}</td>
                    </tr>
                    <tr class="w-100">
                        <td class="left-bio">Alamat</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio text-wrap align-top">
                            {{ $profile_surveyor->alamat }}</td>
                    </tr>
                    <tr>
                        <td class="left-bio">Jenis Kelamin</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">{{ ucwords($profile_surveyor->gender) }}</td>
                    </tr>
                    <tr>
                        <td class="left-bio">Tanggal Lahir</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">
                            {{                             $profile_surveyor->tanggal_lahir === null ? $profile_surveyor->tanggal_lahir : \Carbon\Carbon::parse($profile_surveyor->tanggal_lahir)->format('j F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="left-bio">Target Mingguan</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">{{ $weekly_selesai }} dari
                            {{ $weekly_target }} Gang dan Perumahan</td>
                    </tr>
                    <tr>
                        <td class="left-bio">Target Tercapai</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio align-top">{{ $selesai }} dari
                            {{ $target }} Gang dan Perumahan </td>
                    </tr>
                    <tr id="tr-akhir">
                        <td class="left-bio">Perhitungan Target</td>
                        <td style="width: 1%;" class="pe-1 ps-1 align-top">:</td>
                        <td class="right-bio {{ $selesai - $target < 0 ? 'text-danger' : 'text-success' }} fw-bold align-top">
                            @if ($selesai - $target > 0)
                                + {{ $selesai - $target }} Gang dan Perumahan
                            @elseif ($selesai - $target == 0)
                                Survey Komplit
                            @elseif($selesai - $target < 0) {{ $selesai - $target }} Gang dan Perumahan
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Btn Ubah Password -->
        <div class="ubah-password d-flex justify-content-evenly mt-5">
            <a href="/surveyor/edit/profile/{{ $profile_surveyor->id }}" class="btn btn-primary ps-2 pe-2 ps-sm-5 pe-sm-5 mb-5 border-0 h-auto" style="border-radius: .5em; background: #3f4fc8;">Edit
                Profile</a>
            <a href="/surveyor/edit/password/{{ $profile_surveyor->id }}" class="btn btn-primary ps-2 pe-2 ps-sm-5 pe-sm-5 mb-5 border-0 h-auto" style="border-radius: .5em; background: #3f4fc8;">Edit
                Password</a>
        </div>
        <script>
            $(window).ready(function() {
                $("#dasur-table").click(function(e) {
                    let btn = e.target;
                    if (btn.classList.contains('btn-hapus')) {
                        $('#hapus-id').attr('value', btn.value);
                    }
                })
            });
        </script>
        <script src="/js/jquery.dataTables.min.js"></script>
    </div>

@endsection
