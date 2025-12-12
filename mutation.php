<!DOCTYPE html>
<html lang="en">

<head>
<?php include 'includes/head.php'; ?>
  <!-- Datatable CSS File -->
  <link href="/assets/vendor/DataTables/datatables.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.5/css/dataTables.bootstrap5.css">
  <!-- ECharts JS File -->
   <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
</head>

<body class="index-page">

 <?php include 'includes/header.php'; ?>

  <main class="main">

   <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Mutation Datasets</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Mutation</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Privacy Section -->
    <section id="privacy" class="privacy section">

      <div class="container" data-aos="fade-up">
        <!-- Header -->
        <div class="privacy-header" data-aos="fade-up">
          <div class="header-content">
            <div class="last-updated">Released Date: February 27, 2025</div>
            <h1>Mutation Dataset</h1>
            <p class="intro-text">
              We have annotated genetic variants from ClinVar, UK Biobank, and gnomAD using ProteinIS to determine whether amino acid changes occur at protein-protein interaction (PPI) sites. Explore these annotations through our interactive query interface or download complete datasets from the Download section.
            </p>
          </div>
        </div>
      </div>
    </section>
    <!--End Privacy Section -->


<!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

      <!-- Section Title -->
      <!-- <div class="container section-title">
        <h2>Mutation Dataset Analysis</h2>
      </div> -->
      <!-- End Section Title -->

      <div class="container" data-aos="fade-up">
        <div class="row g-3 align-items-center">
<!-- ECharts for bar plot -->
          <div class="cols-12 col-md-12" id="snpClassDistBarChart" style="height:420px;"></div>
        <script>
(function(){
                // SNP classes (ordered, top = largest)
    const snpClasses = [
        "G>A",
        "C>T",
        "T>C",
        "A>G",
        "G>C",
        "C>G",
        "C>A",
        "G>T",
        "A>C",
        "T>G",
        "A>T",
        "T>A"
    ];

    // Values taken from your chart
    const values = [
        158000,
        158000,
        94233,
        94178,
        53947,
        53707,
        42666,
        42569,
        29090,
        29023,
        24483,
        24216
    ];

    // Color palette similar to your image
    const colors = [
        "#5DAE95",
        "#F4A261",
        "#E9C46A",
        "#E76F51",
        "#5DAE95",
        "#F4A261",
        "#F4A261",
        "#5DAE95",
        "#E76F51",
        "#E9C46A",
        "#E76F51",
        "#E9C46A"
    ];

    const option = {
        title: {
            text: "Distribution of SNP Class",
            left: "center",
            top: 10,
            textStyle: { fontSize: 22 }
        },
        tooltip: {
            trigger: "axis",
            axisPointer: { type: "shadow" }
        },
        grid: {
            left: 150,
            right: 40,
            top: 80,
            bottom: 40
        },
        xAxis: {
            type: "value",
            name: "Numbers of missense variation",
            nameLocation: "middle",
            nameGap: 40
        },
        yAxis: {
            type: "category",
            data: snpClasses,
            name: "SNP class",
            nameGap: 20
        },
        series: [{
            type: "bar",
            data: values.map((v, i) => ({
                value: v,
                itemStyle: { color: colors[i] }
            })),
            label: {
                show: true,
                position: "right",
                formatter: "{c}"
            },
            barWidth: 22
        }]
    };

    const chart = echarts.init(document.getElementById("snpClassDistBarChart"));
    chart.setOption(option);
          })();
        </script>
<!-- END ECharts for bar plot -->

          <!-- Amino Acid Frequency Distribution Chart -->
          <div id="aaChangesChart" class="cols-12 col-md-12" style="height:420px;"></div>
        <script>
          (function(){
                // Label list (ordered top → bottom)
    const aaChanges = [
        "R/Q",
        "R/H",
        "R/C",
        "R/W",
        "E/K",
        "R/G",
        "Y/C",
        "R/L",
        "D/N",
        "N/S"
    ];

    // Values from image
    const values = [
        41782,
        37588,
        35674,
        27043,
        22088,
        19056,
        18622,
        16031,
        15861,
        15107
    ];

    // Color palette similar to the figure
    const colors = [
        "#5DAE95",
        "#8BB68A",
        "#B7C27B",
        "#E1C870",
        "#F2B66A",
        "#E68A54",
        "#E07A47",
        "#D7684A",
        "#D45E47",
        "#CC5240"
    ];

    const option = {
        title: {
            text: "Top 10 Most Frequent Amino Acids Changes",
            left: "center",
            top: 10,
            textStyle: { fontSize: 22 }
        },
        tooltip: {
            trigger: "axis",
            axisPointer: { type: "shadow" }
        },
        grid: {
            left: 150,
            right: 40,
            top: 80,
            bottom: 50
        },
        xAxis: {
            type: "value",
            name: "Number of missense variants",
            nameGap: 35,
            nameLocation: "middle"
        },
        yAxis: {
            type: "category",
            data: aaChanges,
            name: "Amino acids changes",
            nameGap: 25
        },
        series: [{
            type: "bar",
            barWidth: 24,
            data: values.map((v, i) => ({
                value: v,
                itemStyle: { color: colors[i] }
            })),
            label: {
                show: true,
                position: "right",
                formatter: "{c}"
            }
        }]
    };

    const chart = echarts.init(document.getElementById("aaChangesChart"));
    chart.setOption(option);
          })();
        </script>
<!-- END Amino Acid Frequency Distribution Chart -->

  <!-- SNP Class Distribution Chart -->
  <div id="snpClassDistChart" class="cols-12 col-md-12" style="height:420px;"></div>
  <script>
    (function(){
    // Data sources (x-axis)
    const dataSources = [
        "clinvar_PPipeline",
        "clinvar_non_PPipeline",
        "gnomad_PPipeline",
        "gnomad_non_PPipeline",
        "ukb_PPipeline",
        "ukb_non_PPipeline"
    ];

    // SNP classes and percentages from the image
    const snpClasses = [
        "T>A", "A>T", "T>G", "A>C", "C>A",
        "G>T", "G>C", "C>G", "A>G", "T>C",
        "G>A", "C>T"
    ];

    // Matrix of values (rows = snp classes, columns = data sources)
    const values = [
        [3.6, 3.4, 3.7, 3.5, 3.1, 3.0],  
        [3.6, 3.5, 3.7, 3.5, 3.2, 3.0],  
        [4.9, 5.8, 5.5, 6.1, 4.8, 5.6],  
        [5.0, 5.8, 5.5, 6.1, 4.7, 5.6],  
        [6.6, 7.1, 6.9, 7.3, 6.0, 6.6],  
        [6.7, 7.2, 6.8, 7.3, 5.9, 6.6],  
        [11.5, 10.4, 11.8, 10.7, 11.2, 10.0],  
        [11.8, 10.3, 11.8, 10.6, 11.1, 10.0],  
        [20.4, 20.5, 19.1, 19.6, 22.4, 22.3],  
        [20.4, 20.5, 19.1, 19.8, 22.2, 22.3],  
        [15.3, 15.8, 16.2, 16.7, 15.8, 16.3],  
        [15.5, 16.0, 16.2, 16.7, 15.9, 16.3]
    ];

    // Colors (similar to the plot)
    const colors = [
        "#204b9b", "#3464b5", "#4988c8", "#71b0d9",
        "#a4d7a8", "#d7efb1", "#f8f3a4", "#f4cd7a",
        "#ef9c63", "#e57654", "#d95d4a", "#c94940"
    ];

    // Build ECharts series
    const series = snpClasses.map((cls, i) => ({
        name: cls,
        type: "bar",
        stack: "total",
        emphasis: { focus: "series" },
        label: {
            show: true,
            position: "inside",
            formatter: p => p.value + "%"
        },
        itemStyle: { color: colors[i] },
        data: values[i]
    }));

    // Chart configuration
    const option = {
        title: {
            text: "Distribution of SNP Classes (Ordered by Percentage)",
            left: "center"
        },
        tooltip: {
            trigger: "axis",
            axisPointer: { type: "shadow" }
        },
        legend: {
            type: "scroll",
            right: 20,
            top: 50,
            orient: "vertical"
        },
        xAxis: {
            type: "category",
            data: dataSources
        },
        yAxis: {
            type: "value",
            name: "Percentage (%)",
            max: 100
        },
        series: series
    };

    // Render chart
    const chart = echarts.init(document.getElementById("snpClassDistChart"));
    chart.setOption(option);
    })();
</script>
<!-- End SNP Class Distribution Chart -->
</div>
        <!-- Amino acid frequency distribution chart -->
        <!-- <div id="aaChart" style="width:100%;height:420px;margin-top:18px;"></div> -->
        
  </div>
  <div class="container" data-aos="fade-left">
        <div>
          <table id="mutation_table" >
          <thead>
            <tr>
              <th>ID</th>
              <th>Chromosome</th>
              <th>Position</th>
              <th>Uploaded Variation</th>
              <th>Reference Allele</th>
              <th>Alternate Allele</th>
              <th>Clinical Review Status</th>
              <th>Clinical Significance</th>
              <th>Consequence</th>
              <th>Existing Variation</th>
              <th>SWISSPROT</th>
              <th>TREMBL</th>
              <th>Protein Position</th>
              <th>Amino Acids</th>
              <th>Canonical</th>
              <th>SIFT Type</th>
              <th>SIFT Score</th>
              <th>Polyphen Type</th>
              <th>Polyphen Score</th>
              <th>PPI</th>
              <th>3Dmapper Result</th>
              <th>Gene</th>
              <th>Feature</th>
              <th>Gene Symbol</th>
            </tr>
          </thead>
        </table>
        </div>

          <!-- DataTables JS File -->
        <script src="/assets/vendor/DataTables/datatables.min.js"></script>
        <script>
     (function(){
        let table = new DataTable('#mutation_table',{
          ajax: "server_side/scripts/mutation_server_processing.php",
          processing: true,
          serverSide: true,
          scrollX: true,
          fixedHeader: true,
          
          columnDefs: [
              
              {
                  targets: [0],
                  visible: false
              },
              
              
          ],
          fixedColumns: {
            left: 1,
            right: 0
          },
      });
     })();
        </script>
      </div>

    </section><!-- /Starter Section Section -->

  </main>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>

</body>

</html>