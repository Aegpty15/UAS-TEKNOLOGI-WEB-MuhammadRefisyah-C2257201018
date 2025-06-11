<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Muhammad Refisyah">

    <title>Aplikasi Pencatatan Pemakaian Uang Bulanan</title>

    <link href="<?php echo base_url()?>aset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="<?php echo base_url()?>aset/css/sb-admin-2.min.css" rel="stylesheet">

    <link href="<?php echo base_url()?>aset/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('/') ?>">
                <div class="sidebar-brand-icon rotate-n-15">
                </div>
    <div class="sidebar-brand-text mx-3">Pemakaian Uang Bulanan</sup></div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('/')?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
				</a>
            </li>

            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('duit/daftar')?>">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Pemakaian Uang</span>
				</a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="sidebar-heading"></div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Laporan Keuangan</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="<?php echo base_url('laporan/tampil')?>" target="_blank">Tampilkan Keuangan</a>
                        <a class="collapse-item" href="<?php echo base_url('laporan/pdf')?>" target="_blank">Cetak Keuangan</a>    
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">

					<?php echo $this->renderSection('konten') ?>
					
                </div>

            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; 2025 - <a href="https://www.instagram.com/chelseavanmeijr?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank">Muhammad Refisyah</a></span>
                    </div>
                </div>
            </footer>

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <script src="<?php echo base_url()?>aset/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url()?>aset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="<?php echo base_url()?>aset/vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="<?php echo base_url()?>aset/js/sb-admin-2.min.js"></script>

    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url()?>aset/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script src="<?php echo base_url()?>aset/js/demo/datatables-demo.js"></script>

</body>

</html>