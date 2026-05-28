<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="" class="text-decoration-underline"><?= $breadcrumb ?></a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card border-2">
                    <div class="card-body">

                        <!-- Alert: Periode Tidak Ditemukan -->
                        <div class="row justify-content-center">
                            <div class="col-md-6 col-sm-10 col-12">

                                <!-- Icon + Pesan Utama -->
                                <div class="text-center py-5" 
                                     data-aos="fade-up" 
                                     data-aos-delay="100">

                                    <!-- Icon Animasi -->
                                    <div class="mb-4">
                                        <div class="d-inline-flex align-items-center justify-content-center 
                                                    bg-warning bg-opacity-10 rounded-circle"
                                             style="width: 80px; height: 80px; 
                                                    animation: pulse 2s ease-in-out infinite;">
                                            <i class="ri-calendar-line text-white" 
                                               style="font-size: 2.2rem;"></i>
                                        </div>
                                    </div>

                                    <!-- Judul -->
                                    <h5 class="fw-semibold text-dark mb-2"
                                        data-aos="fade-up" data-aos-delay="200">
                                        Periode Tidak Ditemukan
                                    </h5>

                                    <!-- Deskripsi -->
                                    <p class="text-muted mb-4"
                                       data-aos="fade-up" data-aos-delay="300">
                                        Data periode tidak tersedia.<br>
                                        Silahkan hubungi administrator untuk mendapatkan akses tersebut!
                                    </p>

                                </div>
                            </div>
                        </div>
                        <!-- End Alert -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Animasi Pulse (tambahkan di <style> atau file CSS kamu) -->
<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.08); }
}
</style>