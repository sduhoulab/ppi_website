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
              <span class="subtitle">THE FIRST PROTEOME-WIDE ATLAS OF PROTEIN-PROTEIN INTERACTION INTERFACES AND HUMAN GENETIC MUTATIONS.</span>
              <h1>Proteome-wide Interaction Variants Database</h1>
              <p>IntVar is an integrative resource for exploring the proteome-scale intersection of protein-protein interaction (PPI) interfaces and human genetic diversity. Leveraging the deep learning-based predictor ProteinIS alongside high-resolution structural data, the platform offers multi-level annotations of PPI sites. By mapping genetic variants onto interfaces, IntVar enables intuitive visualization of their positional relationships, providing open data resources to characterize how genetic diversity impacts the human interactome.</p>

              <div class="hero-buttons dropdown">
                <a href="predictor.php" class="btn-primary">Submit Prediction Task</a>
                <button class="btn-secondary dropdown-toggle" type="button" id="exploreDropdown" data-bs-toggle="dropdown" aria-expanded="false">Explore Our Datasets</button>
                <ul class="dropdown-menu" aria-labelledby="exploreDropdown">
                   <li><a class="dropdown-item" href="proteome.php">Explore human proteome</a></li>
                   <li><a class="dropdown-item" href="mutation.php">Explore genetic mutations</a></li>
                </ul>
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
                    <span class="count">11,444,263</span>
                    <span class="label">Residues</span>
                  </div>
                </div>
                <div class="badge-item">
                  <i class="bi bi-link-45deg"></i>
                  <div class="badge-text">
                    <span class="count">1,208,361</span>
                    <span class="label">Predicted Interface Sites</span>
                  </div>
                </div>
                <div class="badge-item">
                  <i class="bi bi-tags"></i>
                  <div class="badge-text">
                    <span class="count">7,687,530</span>
                    <span class="label">Annotated Variants</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5" data-aos="fade-left" data-aos-delay="300">
            <div class="hero-image">
              <img src="assets/img/database.png" alt="database" class="img-fluid">              
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
