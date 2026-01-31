<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <title>{{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('landingpage/assets/images/logo-ponpes-icon.ico') }}">

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('landingpage/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="{{ asset('landingpage/assets/css/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('landingpage/assets/css/templatemo-tale-seo-agency.css') }}" />
    <link rel="stylesheet" href="{{ asset('landingpage/assets/css/owl.css') }}" />
    <link rel="stylesheet" href="{{ asset('landingpage/assets/css/animate.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <!--

TemplateMo 582 Tale SEO Agency

https://templatemo.com/tm-582-tale-seo-agency

-->
</head>

<body>
    <!-- ***** Preloader Start ***** -->
    {{-- <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div> --}}
    <!-- ***** Preloader End ***** -->

    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="index.html" class="logo">
                            <img src="{{ asset('landingpage/assets/images/logo_uiidalwaputih.png') }}" alt=""
                                style="width: 250px;" />
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li class="scroll-to-section">
                                <a href="#top" class="active">Home</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="#about">About</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="#syarat">Syarat</a>
                            </li>
                            <li class="scroll-to-section">
                                <a href="#alur">Alur</a>
                            </li>
                            <li class="scroll-to-section">
                                @if (\Auth::check())
                                    <a href="{{ route('home') }}">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}">Login</a>
                                @endif
                            </li>
                        </ul>
                        <a class="menu-trigger">
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <!--***** Hero Area Start ***** -->
    <div class="main-banner" id="top">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="caption header-text">
                        <h6>website Pendaftaran PKL</h6>
                        <div class="line-dec"></div>
                        <h4>
                            Selamat Datang di<em> Program PKL </em> <br />UII Dalwa<span></span>
                        </h4>
                        <hr>
                        @if (\Auth::check())
                            <a href="{{ route('home') }}" class="btn btn-info"><i class="fa fa-home" aria-hidden="true"></i> Dashboard!
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-warning"><i class="fa fa-sign-in" aria-hidden="true"></i> Masuk
                                disini!
                            </a>
                        @endif
                        <a href="#pedoman" class="btn btn-primary"><i class="fa fa-file" aria-hidden="true"></i> Pedoman PKL
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Hero Area End ***** -->

    <!-- ***** About Area Start ***** -->
    <div class="infos section" id="infos">
        <div class="container" id="about">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-content">
                        <div class="row">
                            <div class="col-lg-6">
                                <!-- <h1>hello</h1> -->
                                <div class="left-image">
                                    <img src="{{ asset('landingpage/assets/images/infos-bg.jpg') }}" alt="" />
                                </div>
                                <!-- <div class="section-heading">
                    <h2><em>Tujuan Program PKL UII Dalwa</em></h2>
                    <div class="line-dec"></div>

                    <ul>
                      <li>
                        <img <i class="ion-ios-location"></i>
                        <p>1. Mengembangkan Kompetensi Praktis</p>
                      </li>
                      <li>
                        <p>2. Menghubungkan Teori dengan Praktik</p>
                      </li>
                      <li>
                        <p>3. Membangun Etika dan Profesionalisme</p>
                      </li>
                      <li>
                        <p>4. Memperluas Jejaring dan Relasi Profesional</p>
                      </li>
                      <li>
                        <p>5. Meningkatkan Kemampuan Beradaptasi dan Inovasi</p>
                      </li>
                    </ul>

                  </div> -->
                            </div>

                            <div class="col-lg-6">
                                <div class="section-heading">
                                    <h2><em>About Us</em><span></span></h2>
                                    <div class="line-dec"></div>
                                    <p>
                                        Kami mengundang para mahasiswa untuk menjelajahi dan
                                        mengikuti Program Praktik Kerja Lapangan (PKL) di
                                        Universitas Islam Indonesia Dalwa. Dengan berfokus pada
                                        pengembangan keterampilan praktis dan pengalaman kerja
                                        nyata, program ini dirancang untuk membekali peserta
                                        dengan kompetensi yang relevan di bidang industri dan
                                        akademik. Temukan peluang, tingkatkan kemampuan, dan
                                        bersiaplah untuk masa depan yang cemerlang bersama kami!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** About Area End ***** -->

    <!-- ***** PedomanArea Start ***** -->
    <div class="projects section" id="pedoman">
        <div class="container" id="pedoman-pkl">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-heading">
                        <h2>Pedoman <em>untuk Setiap Prodi PKL</em> &amp;</h2>
                        <div class="line-dec"></div>
                        <p>
                            Setelah mahasiswa memenuhi syarat yang telah ditetapkan, maka
                            boleh melakukan pendaftaran, berikut alurnya
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="owl-features owl-carousel">
                        @foreach($pedoman as $item)
                            <div class="item">
                                <div class="down-content" style="
                                    height: 220px;
                                    ">
                                    <div class="row h-100">
                                        <div class="col-lg-8" style="
                                                    display: flex;
                                                    flex-direction: column;
                                                    justify-content: center;
                                                ">
                                            <h4>{{ $item->prodi->nama }}</h4>
                                            <small class="text-muted mt-2">{{ $item->keterangan }}</small>
                                        </div>
                                        <div class="col-lg-4" style="
                                                ">
                                            <button onclick="location.href='{{ \GoogleDrive::directDownload($item->file, \BulkData::dirGdrive['dokumen'], $item->keterangan . ' - ' . $item->prodi->nama) }}'" class="btn btn-primary h-100">
                                                Unduh {{ $item->keterangan }} <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Pedoman Area End ***** -->

    <!-- ***** Syarat Area Start ***** -->
    <div class="services section" id="services">
        <div id="syarat" class="container"
            style="
          background-image: url({{ asset('landingpage/assets/images/services.gif') }});
          background-repeat: no-repeat;
          background-size: cover;
          background-position: center center;
          width: 100%;
          height: 100%;
        ">
            <div class="row">
                <div class="col-lg-6 offset-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-heading">
                                <h2>Syarat <em>Pendaftaran PKL</em></h2>
                                <div class="line-dec"></div>
                                <p>
                                    Sebagai Mahasiswa yang ingin daftar pkl diharuskan memenuhi
                                    syarat sebagai berikut
                                </p>
                            </div>
                        </div>
                        <!-- <div class="col-lg-6 col-sm-6">
                <div class="service-item">
                  <div class="icon">
                    <img
                      src="{{ asset('landingpage/assets/images/services-01.jpg') }}"
                      alt="discover SEO"
                      class="templatemo-feature"
                    />
                  </div>
                  <h4>Discover More on Latest SEO Trends</h4>
                </div>
              </div> -->
                        <div class="col-lg-6 col-sm-6">
                            <div class="service-item">
                                <h4>Kelas Pondok Aliyah dan aktif di Pondok maupun Jamiah</h4>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-6">
                            <div class="service-item">
                                <h4>Mempunyai akhlak dan tidak melakukan pelanggaran</h4>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-6">
                            <div class="service-item">
                                <h4>Mempunyai Softskills yang bermanfaat</h4>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-6">
                            <div class="service-item">
                                <h4>Kelas Pondok Aliyah dan aktif di Pondok</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ***** Syarat Area End ***** -->

    <!-- ***** Alur Area Start ***** -->
    <div class="projects section" id="projects">
        <div class="container" id="alur">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-heading">
                        <h2>Alur <em>Pendaftaran PKL</em> &amp;</h2>
                        <div class="line-dec"></div>
                        <p>
                            Setelah mahasiswa memenuhi syarat yang telah ditetapkan, maka
                            boleh melakukan pendaftaran, berikut alurnya
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="owl-features owl-carousel">
                        <div class="item">
                            <img src="{{ asset('landingpage/assets/images/pembayaran.png') }}" alt="" />
                            <div class="down-content">
                                <h4>1. Pembayaran</h4>
                                <h8>
                                    mahasiswa melakukan pembayaran PKL(Praktek Kerja
                                    lapang)/PPL(Praktek Pengalaman Lapangan), sebesar
                                    Rp.2.500.000
                                </h8>
                                <!-- <a href="#"><i class="fa fa-link"></i></a> -->
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('landingpage/assets/images/konfirmasi-pembayaran (2).png') }}"
                                alt="" />
                            <div class="down-content">
                                <h4>2. Konfirmasi Pembayaran</h4>
                                <h8>
                                    Konfirmasi pembayaran dan menerima password untuk login
                                    website di nomor 0877 5445 2667 (S1 Banin), 0877 5934 7333
                                    (S1 Banat),
                                </h8>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('landingpage/assets/images/login.png') }}" alt="" />
                            <div class="down-content">
                                <h4>3. Login</h4>
                                <h8>
                                    Masuk ke website PKL bagi Mahasiswa yang telah mendapat
                                    username dan password
                                </h8>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('landingpage/assets/images/pendaftaran.png') }}" alt="" />
                            <div class="down-content">
                                <h4>4. Pengisian formulir</h4>
                                <h8>
                                    Bagi mahasiswa yang akan mengikuti PKL maupun PPL diwajibkan
                                    untuk mengisi dan melengkapi formulir
                                </h8>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('landingpage/assets/images/perivikasi.png') }}" alt="" />
                            <div class="down-content">
                                <h4>5. Verifikasi Pendaftaran</h4>
                                <h8>
                                    Staff yang bertugas melaksanakan verifikasi data mahasiswa
                                    dan pengelompokan POSKO
                                </h8>
                            </div>
                        </div>
                        <div class="item">
                            <img src="{{ asset('landingpage/assets/images/selesai.png') }}" alt="" />
                            <div class="down-content">
                                <h4>6. Selesai</h4>
                                <h8>
                                    Mahasiswa akan segera mengikuti PKL dan PPL setelah selesai
                                </h8>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ** *** Alur Area End ***** -->

    <!-- <div class="contact-us section" id="contact">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="contact-us-content">
              <div class="row">
                <div class="col-lg-4">
                  <div id="map">
                    <iframe
                      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12469.776493332698!2d-80.14036379941481!3d25.907788681148624!2m3!1f357.26927939317244!2f20.870722720054623!3f0!3m2!1i1024!2i768!4f35!3m3!1m2!1s0x88d9add4b4ac788f%3A0xe77469d09480fcdb!2sSunny%20Isles%20Beach!5e1!3m2!1sen!2sth!4v1642869952544!5m2!1sen!2sth"
                      width="100%"
                      height="670px"
                      frameborder="0"
                      style="border: 0; border-radius: 23px"
                      allowfullscreen=""
                    ></iframe>
                  </div>
                </div>
                <div class="col-lg-8">
                  <form id="contact-form" action="" method="post">
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="section-heading">
                          <h2>
                            <em>Contact Us</em> &amp; Get In <span>Touch</span>
                          </h2>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <fieldset>
                          <input
                            type="name"
                            name="name"
                            id="name"
                            placeholder="Your Name..."
                            autocomplete="on"
                            required
                          />
                        </fieldset>
                      </div>
                      <div class="col-lg-6">
                        <fieldset>
                          <input
                            type="surname"
                            name="surname"
                            id="surname"
                            placeholder="Your Surname..."
                            autocomplete="on"
                            required
                          />
                        </fieldset>
                      </div>
                      <div class="col-lg-6">
                        <fieldset>
                          <input
                            type="text"
                            name="email"
                            id="email"
                            pattern="[^ @]*@[^ @]*"
                            placeholder="Your E-mail..."
                            required=""
                          />
                        </fieldset>
                      </div>
                      <div class="col-lg-6">
                        <fieldset>
                          <input
                            type="subject"
                            name="subject"
                            id="subject"
                            placeholder="Subject..."
                            autocomplete="on"
                          />
                        </fieldset>
                      </div>
                      <div class="col-lg-12">
                        <fieldset>
                          <textarea name="message" id="message" placeholder="Your Message"></textarea>
                        </fieldset>
                      </div>
                      <div class="col-lg-12">
                        <fieldset>
                          <button
                            type="submit"
                            id="form-submit"
                            class="orange-button"
                          >
                            Send Message Now
                          </button>
                        </fieldset>
                      </div>
                    </div>
                  </form>
                  <div class="more-info">
                    <div class="row">
                      <div class="col-lg-4">
                        <div class="info-item">
                          <i class="fa fa-phone"></i>
                          <h4><a href="#">010-020-0340</a></h4>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="info-item">
                          <i class="fa fa-envelope"></i>
                          <h4><a href="#">info@company.com</a></h4>
                          <h4><a href="#">hello@company.com</a></h4>
                        </div>
                      </div>
                      <div class="col-lg-4">
                        <div class="info-item">
                          <i class="fa fa-map-marker"></i>
                          <h4>
                            <a href="#"
                              >Sunny Isles Beach, FL 33160, United States</a
                            >
                          </h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->
    <hr />
    <footer>
        <div class="container">
            <div class="col-lg-12">
                <p>
                    Copyright © 2024 <a href="#">UII DALWA </a>. All rights reserved.

                    <br />Design:
                    <a href="https://templatemo.com" target="_blank">Tim IT UII Dalwa</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('landingpage/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('landingpage/vendor/bootstrap/js/bootstrap.min.js') }}"></script>

    <script src="{{ asset('landingpage/assets/js/isotope.min.js') }}"></script>
    <script src="{{ asset('landingpage/assets/js/owl-carousel.js') }}"></script>
    <script src="{{ asset('landingpage/assets/js/tabs.js') }}"></script>
    <script src="{{ asset('landingpage/assets/js/popup.js') }}"></script>
    <script src="{{ asset('landingpage/assets/js/custom.js') }}"></script>
</body>

</html>
