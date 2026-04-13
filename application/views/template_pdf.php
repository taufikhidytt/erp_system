<?php
  $this->db->select('
    s.NAME,s.LOGO_FILENAME,
    a.ADDRESS1,a.ADDRESS2,a.ADDRESS3,a.PHONE
  ');
  $this->db->from('setup s');
  $this->db->join('address a','s.ADDRESS_ID = a.ADDRESS_ID');
  $company = $this->db->get()->row_array();
  $company['logo'] = img_to_base64('./assets/logo/logo.png');
  date_default_timezone_set('Asia/Jakarta');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title)?$title:'' ?></title>
    <style>
        @page {
          margin: 110px 0cm 1cm 0cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #2d3748;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        /* Layout Utama */
        .container { padding: 0cm 1cm 1cm 1cm; }
        
        /* Header Table */
        .header-table {
          position: fixed;
          top: -110px;
          width: 100%;
          border-bottom: 1px solid #dde4de;
          background-color: #faf8f4;
          padding: 10px 30px;
        }

        .logo-box {
            width: 60px;
            height: 60px;
            background-color: #8a9e8c;
            text-align: center;
            border-radius: 10px;
        }

        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #2d3748;
        }

        .tagline {
            font-size: 8pt;
            color: #8a9e8c;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .contact-info {
            font-size: 8pt;
            color: #4a5568;
        }

        /* Title Section */
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            margin-top: 1rem;
        }

        .report-label {
            font-size: 8pt;
            color: #c4a49a;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 20pt;
        }

        /* Info Cards (Gunakan Table agar sejajar) */
        .info-container {
            width: 100%;
        }

        .info-card {
            width: 48%;
            background-color: #faf8f4;
            border: 1px solid #dde4de;
            padding: 15px;
            vertical-align: top;
        }

        .card-label {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 10px;
            display: block;
        }

        .info-row {
            width: 100%;
            font-size: 8pt;
            margin-bottom: 4px;
        }
        .info-row td{
            vertical-align: top !important;
        }

        .info-dots {
            border-bottom: 1px dotted #dde4de;
        }

        /* Table Items */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table thead {
          display: table-header-group;
        }

        .items-table th {
            background-color: #e8ede9;
            color: #4a5568;
            font-size: 8pt;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dde4de;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f0f0ec;
            vertical-align: top;
            font-size: 10px;
        }
        .items-table td {
            font-family: 'Courier New', Courier, monospace;
        }

        .item-desc {
            font-size: 8pt;
            color: #8a9e8c;
        }

        .total-row {
            background-color: #f0e8e3;
            font-weight: bold;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }

        .sig-title {
            font-size: 8pt;
            color: #8a9e8c;
            margin-bottom: 50px;
        }

        .sig-name {
            font-weight: bold;
            border-top: 1px solid #dde4de;
            padding-top: 5px;
            display: inline-block;
            width: 80%;
        }

        /* Page Numbering */
        footer {
            position: fixed;
            bottom: -60px; /* Atur posisi agar pas di area margin bawah */
            left: 0px;
            right: 0px;
            height: 50px;
            
            /* Styling bar seperti di gambar */
            background-color: #faf8f4; /* Warna cream background */
            border-top: 3px solid #8a9e8c; /* Garis hijau sage tebal di atas */
            padding: 10px 40px;
            font-family: 'Helvetica', sans-serif;
        }

        .footer-table {
            width: 100%;
            border: none;
        }

        .footer-table td {
            font-size: 8pt;
            color: #4a5568;
            vertical-align: middle;
            border: none;
        }

        .pi-doc {
            font-weight: bold;
            color: #97a999 !important;
            letter-spacing: 1px;
        }

        .pi-page-bold {
            font-size: 11pt;
            color: #8a9e8c;
            font-weight: bold;
        }
        .pagenum:before {
            content: counter(page);
        }
        .pagecount:before {
            content: counter(pages);
        }
    </style>
</head>
<body>

<table class="header-table">
  <tr>
      <?php if(isset($company['logo']) && $company['logo']){?>
      <td width="70">
          <div class="logo-box">
              <img src="<?= $company['logo'] ?>" style="width:100%" />
          </div>
      </td>
      <?php } ?>
      
      <td>
          <div class="company-name"><?= $company['NAME'] ?></div>
          <!-- <div class="tagline">Trusted Business Solutions</div> -->
          <div class="contact-info">
              <?= $company['ADDRESS1']?$company['ADDRESS1'].'</br>':'' ?>
              <?= $company['ADDRESS2']?$company['ADDRESS2'].'</br>':'' ?>
              <?= $company['ADDRESS3']?$company['ADDRESS3'].'</br>':'' ?>
              <?= $company['PHONE']?$company['PHONE']:'' ?>
          </div>
      </td>
  </tr>
</table>
<footer>
    <table class="footer-table">
        <tr>
            <td class="pi-doc">
                Dibuat oleh <?= $this->session->nama ?> pada <?= date('d-m-Y H:i:s') ?>
            </td>
        </tr>
    </table>
</footer>
<div class="container">
    <?php isset($dir_view)?$this->load->view($dir_view,$data):'' ?>
</div>
</body>
</html>