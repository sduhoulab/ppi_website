<!DOCTYPE html>
<html lang="en">

<head>
<?php include 'includes/head.php'; ?>
</head>

<body class="index-page">

 <?php include 'includes/header.php'; ?>

  <main class="main">

  <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">
          <div class="col-lg-7">
            <div class="hero-content" data-aos="fade-right" data-aos-delay="200">
              <span class="subtitle">The First Proteome-Wide Atlas of Protein-Protein Interaction Interfaces and Pathogenic Mutations.</span>
              <h1>Protein-Protein Interaction</h1>
              <p>Protein–protein interactions (PPIs) are the physical contacts formed between two or more protein molecules, allowing them to work together to carry out essential biological functions. These interactions regulate nearly every cellular process—such as signaling, metabolism, immune response, and gene expression. Understanding PPIs helps researchers uncover how cells function, how diseases develop, and how new therapeutic strategies can be designed.</p>

              <div class="hero-buttons">
                <a href="#" class="btn-primary">Submit Prediction Task</a>
                <a href="#" class="btn-secondary">Explore Our Datasets</a>
              </div>

              <div class="trust-badges">
                <div class="badge-item">
                  <i class="bi bi-droplet"></i>
                  <div class="badge-text">
                    <span class="count">20,656</span>
                    <span class="label">Proteins</span>
                  </div>
                </div>
                <div class="badge-item">
                  <i class="bi bi-flask"></i>
                  <div class="badge-text">
                    <span class="count">9,854,603</span>
                    <span class="label">Residues</span>
                  </div>
                </div>
                <div class="badge-item">
                  <i class="bi bi-link-45deg"></i>
                  <div class="badge-text">
                    <span class="count">1,168,160</span>
                    <span class="label">Predicted Interface Sites</span>
                  </div>
                </div>
                <div class="badge-item">
                  <i class="bi bi-tags"></i>
                  <div class="badge-text">
                    <span class="count">XXX, XXX</span>
                    <span class="label">Annotated Pathogenic Variants</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5" data-aos="fade-left" data-aos-delay="300">
            <div class="hero-image">
              <img src="assets/img/Brief-introduction-of-Protein-Protein-Interaction-0.png" alt="Brief-introduction-of-Protein-Protein-Interaction-0" class="img-fluid">
              <div class="image-badge">
                <span>PPI_esm2_t33_650M_UR50D</span>
                <p>a deep-learning model used for predicting protein–protein interactions (PPIs). It is built on top of ESM-2, a family of protein language models developed by Meta AI.</p>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Hero Section -->

  </main>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>

 

</body>

</html>