<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profil-page-utama</title>
    <link rel="stylesheet" href="/fontawesome5/css/all.css">
    <link rel="stylesheet" href="/css/profil-utama.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        <!-- sidebar -->
        <div class="sidebar" id="mySidebar">
            <div class="side">
                <div class="span" id="mySpan">
                    <input type="checkbox" />
                    <!-- <span></span>
                <span></span>
                <span></span> -->
                </div>
                <div class="logo">
                    <div class="img">
                        <h2>Survei</h2>
                    </div>
                    <p>Aplikasi Survei Gang dan Perumahan di Kota Pontianak</p>
                </div>
                <ul class="menu">
                    <li><a href=""><span class="icon b"></span>Beranda</a></li>
                    <li><a href=""><span class="icon a"></span>Profile</a></li>
                    <li><a href=""><span class="icon c"></span>Surveyor</a></li>
                    <li><a href=""><span class="icon d"></span>Data Survei</a></li>
                    <li><a href=""><span class="icon e"></span>Pengaturan</a></li>
                    <li><a href="/"><span class="icon f"></span>Keluar</a></li>
                </ul>
            </div>
        </div>

        <!-- sidebar end -->


        <!-- Main Content -->
        <div class="main-content">
            {{-- @dd($profile) --}}
            <!-- Header -->

            <!-- Content -->
            <div class="content">
                <!-- ===================== -->
                <div class="biodata">
                    @if (session()->has('success'))
                        <div class="alert alert-success col-lg-8" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <table class="bio">
                        @foreach ($surveyors as $surveyor)
                            <tr>
                                <td class="right-bio">{{ $surveyor->nama_lengkap }}
                                    <form action="/surveyor/hapus/{{ $surveyor->id }}" method="POST">
                                        @method('delete')
                                        @csrf
                                        <button class="btn btn-primary float-end"
                                            onclick="return confirm('Anda yakin ingin menghapus akun?')">Hapus</button>
                                    </form>
                                    <a href="/surveyor/profile/{{ $surveyor->id }}"
                                        class="btn btn-warning text-light float-end ms-1">Edit</a>
                                    <a href="/surveyor/profile/{{ $surveyor->id }}"
                                        class="btn btn-danger float-end ms-1">Profil</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

            </div>

            <!-- Footer -->
            <div class="footer">
                <hr>
                <p>&copy; 2021 Website Survei</p>
            </div>
        </div>

        <div class="modal-container" id="modal_container">
            <div class="modal">

                <p>Anda yakin ingin keluar<br>dari aplikasi ini ?</p>
                <button id="close">Keluar</button>
                <button id="cancel">Batal</button>
            </div>
        </div>
    </div>
    <!-- Main Content End -->
    <script src="/js/script.js"></script>
    <script src="/js/modal.js"></script>
</body>

</html>
=======
@extends('/admin/main')
@section('main-content')
    <div class="content">
        <h2 class="p-3 text-center shadow mb-5 bg-light">Daftar Surveyor</h2>
        <div class="biodata">
            <table class="bio">
                @foreach ($surveyors as $surveyor)
                <tr>
                    <td class="right-bio">{{ $surveyor->nama_lengkap }}
                        <a href="/surveyor/{{ $surveyor->id }}" class="btn btn-danger float-end ms-1">Hapus</a>
                        <a href="/surveyor/edit/{{ $surveyor->id }}"
                            class="btn btn-warning text-light float-end ms-1">Edit</a>
                        <a href="/surveyor/{{ $surveyor->id }}" class="btn btn-primary float-end">Profil</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
>>>>>>> 8718b11fcc9576ecbc0b67a8752b8b33e21500e3
