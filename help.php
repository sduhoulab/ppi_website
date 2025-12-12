<!DOCTYPE html>
<html lang="en">

<head>
<?php include 'includes/head.php'; ?>
</head>

<body class="index-page">

 <?php include 'includes/header.php'; ?>

  <main class="main">

  <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Help</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Home</a></li>
            <li class="current">Help</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Privacy Section -->
    <section id="privacy" class="privacy section">

      <div class="container" data-aos="fade-up">
        <!-- Header -->
        <!-- <div class="privacy-header" data-aos="fade-up">
          <div class="header-content">
            <div class="last-updated">Effective Date: February 27, 2025</div>
            <h1>Privacy Policy</h1>
            <p class="intro-text">This Privacy Policy describes how we collect, use, process, and disclose your information, including personal information, in conjunction with your access to and use of our services.</p>
          </div>
        </div> -->

        <!-- Main Content -->
        <div class="privacy-content" data-aos="fade-up">
          <!-- Introduction -->
          <div class="content-section">
            <h2>1. Introduction</h2>
            <p>
              We have developed a sequence-based protein-protein interaction (PPI) site prediction tool. Through systematic evaluation of multiple pre-trained models and hyperparameter optimization, we selected the best-performing model, which has been made publicly available on Hugging Face. Our predictor requires only the target protein sequence as input (no binding partner information needed), with benchmark tests demonstrating excellent prediction performance.
</p>
            <p>
             Using this predictor, we conducted a comprehensive analysis of 20,841 human protein sequences at the whole-proteome scale. All prediction results have been standardized and integrated into our web-accessible database. We have also incorporated variant data from three authoritative databases: ClinVar, gnomAD, and UK Biobank.
</p>
          </div>

          <!-- Information Collection -->
          <div class="content-section">
            <h2>2. Mutation</h2>
            <p>Variants were annotated using Variant Effect Predictor (VEP) to obtain corresponding amino acid change information, enabling assessment of their potential impact on PPI sites. The dataset includes these key fields:</p>

            
            <ul>
              <li>Core Information: POS (genomic position)、Uploaded_variation (variant ID)、REF/ALT (nucleotide change)</li>
              <li>Clinical Annotation: CLNREVSTAT (ClinVar-specific review status)、clin_sig (clinical significance)、Consequence (variant effect)</li>
              <li>Protein Impact: Protein (Swissprot)、Protein_position (amino acid position)、Amino_acids (residue change)</li>
              <li>Functional Predictions: SIFT/PolyPhen scores and prediction types</li>
              <li>PPI : interaction site prediction</li>
              <li>Structural Mapping (3Dmapper_result): 
                <ul>
                  <li>UniProt sequences were mapped to PDB structures through sequence alignment</li>
                  <li>Thresholds were set for sequence identity (Pident) and coverage</li>
                  <li>The PDB structure with highest sequence identity was selected</li>
                  <li>Mapped positions were classified as:Structure (structural region)、Interface (interaction interface)、Unmap (unmapped)、	Noncoding (non-coding region)</li>
                </ul>
              </li>
            </ul>
            Additionally, we integrated AlphaFold-predicted human protein interactome data (Huri/Humap), applying the same analysis pipeline with additional pDockQ score thresholds for interaction reliability assessment.
          </div>

            <div class="content-section">
              <h2>3. Proteome</h2>
              <p>We apply PPI site predictions for all 20,841 human proteins.</p>
            </div>

            <div class="content-section">
              <h2>4. Download</h2>
              <p>Bulk downloads available in CSV format.</p>
            </div> 


        </div>

        <!-- Contact Section -->
        <div class="privacy-contact" data-aos="fade-up">
          <h2>Contact Us</h2>
          <p>For technical support or inquiries, please contact us at::</p>
          <div class="contact-details">
            <p><strong>Email:</strong> houqingzhen@sdu.edu.cn</p>
            <p><strong>Address:</strong> The Center for Integrative Bioinformatics

National Institute of Health Data Science of China

Shandong University, Shandong, P. R. China.</p>
          </div>
        </div>

      </div>

    </section><!-- /Privacy Section -->

  </main>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>

 

</body>

</html>