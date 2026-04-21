<!DOCTYPE html>
<html lang="en">
<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Load all statistic CSV files
$csvFiles = [
    'all_AA_change' => 'statistic/all_AA_change_statistic.csv',
    'all_Amino_acids' => 'statistic/all_Amino_acids_statistic.csv',
    'all_Gene_symbol' => 'statistic/all_Gene_symbol_statistic.csv',
    'all_SWISSPROT' => 'statistic/all_SWISSPROT_statistic.csv',
    'ppi_AA_change' => 'statistic/ppi_AA_change_statistic.csv',
    'ppi_Amino_acids' => 'statistic/ppi_Amino_acids_statistic.csv',
    'ppi_Gene_symbol' => 'statistic/ppi_Gene_symbol_statistic.csv',
    'ppi_SWISSPROT' => 'statistic/ppi_SWISSPROT_statistic.csv',
    'non_ppi_AA_change' => 'statistic/non_ppi_AA_change_statistic.csv',
    'non_ppi_Amino_acids' => 'statistic/non_ppi_Amino_acids_statistic.csv',
    'non_ppi_Gene_symbol' => 'statistic/non_ppi_Gene_symbol_statistic.csv',
    'non_ppi_SWISSPROT' => 'statistic/non_ppi_SWISSPROT_statistic.csv'
];

$csvData = [];
foreach ($csvFiles as $key => $file) {
    if (file_exists($file)) {
        $csvData[$key] = array_map('str_getcsv', explode("\n", file_get_contents($file)));
        // Remove empty last line if exists
        if (empty($csvData[$key][count($csvData[$key])-1])) {
            array_pop($csvData[$key]);
        }
    }
}

// Helper function to get top N items from CSV data
function getTopItems($data, $n = 10) {
    if (count($data) <= 1) return [];
    $header = array_shift($data);
    // Sort by count (second column) descending
    usort($data, function($a, $b) {
        return (float)$b[1] <=> (float)$a[1];
    });
    $result = array_slice($data, 0, $n);
    array_unshift($result, $header);
    return $result;
}
?>
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
            <div class="last-updated">Release Date: February 27, 2025</div>
            <h1>Mutation Dataset</h1>
            <p class="intro-text">
              We have annotated genetic variants from ClinVar, UK Biobank, and gnomAD using ProteinIS to determine whether amino acid changes occur at protein-protein interaction (PPI) sites. Explore these annotations through our interactive query interface or download complete datasets from the Download section.
            </p>
          </div>
        </div>
      </div>
    </section>
    <!--End Privacy Section -->

    <section id="chart-section" class="starter-section section">
      <div class="container" data-aos="fade-up">
        <div class="row g-3 align-items-center">
          <div class="col-12">
            <h2 class="section-title">Mutation Statistics Overview</h2>
            <p class="section-description">Explore comprehensive statistics of genetic variants across different categories and data types.</p>
          </div>
        </div>

        <!-- Tabbed Chart Interface -->
        <div class="row g-3">
          <div class="col-12">
            <ul class="nav nav-tabs" id="chartTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-charts" type="button" role="tab" aria-controls="all-charts" aria-selected="true">
                  <div class="tab-header">
                    <div class="tab-title">All Variants</div>
                    <div class="tab-preview" id="all-preview"></div>
                  </div>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="ppi-tab" data-bs-toggle="tab" data-bs-target="#ppi-charts" type="button" role="tab" aria-controls="ppi-charts" aria-selected="false">
                  <div class="tab-header">
                    <div class="tab-title">PPI Variants</div>
                    <div class="tab-preview" id="ppi-preview"></div>
                  </div>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="non-ppi-tab" data-bs-toggle="tab" data-bs-target="#non-ppi-charts" type="button" role="tab" aria-controls="non-ppi-charts" aria-selected="false">
                  <div class="tab-header">
                    <div class="tab-title">Non-PPI Variants</div>
                    <div class="tab-preview" id="non-ppi-preview"></div>
                  </div>
                </button>
              </li>
            </ul>

            <div class="tab-content" id="chartTabsContent">
              <!-- All Variants Tab -->
              <div class="tab-pane fade show active" id="all-charts" role="tabpanel" aria-labelledby="all-tab">
                <div class="row g-4 mt-3">
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Nucleotide Substitutions (AA Changes)</h4>
                      <div id="all-aa-change-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Amino Acid Changes</h4>
                      <div id="all-amino-acids-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Gene Symbols</h4>
                      <div id="all-gene-symbol-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Swiss-Prot Proteins</h4>
                      <div id="all-swissprot-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PPI Variants Tab -->
              <div class="tab-pane fade" id="ppi-charts" role="tabpanel" aria-labelledby="ppi-tab">
                <div class="row g-4 mt-3">
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Nucleotide Substitutions (AA Changes)</h4>
                      <div id="ppi-aa-change-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Amino Acid Changes</h4>
                      <div id="ppi-amino-acids-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Gene Symbols</h4>
                      <div id="ppi-gene-symbol-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Swiss-Prot Proteins</h4>
                      <div id="ppi-swissprot-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Non-PPI Variants Tab -->
              <div class="tab-pane fade" id="non-ppi-charts" role="tabpanel" aria-labelledby="non-ppi-tab">
                <div class="row g-4 mt-3">
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Nucleotide Substitutions (AA Changes)</h4>
                      <div id="non_ppi-aa-change-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Amino Acid Changes</h4>
                      <div id="non_ppi-amino-acids-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Gene Symbols</h4>
                      <div id="non_ppi-gene-symbol-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-6">
                    <div class="chart-container">
                      <h4>Swiss-Prot Proteins</h4>
                      <div id="non_ppi-swissprot-chart" style="height: 400px;"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


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
          <!-- <div class="cols-12 col-md-12" id="snpClassDistBarChart" style="height:420px;"></div> -->
        <script>
(function(){
                // SNP classes (ordered, top = largest)
                // Use all_AA_change_statistic.csv data
                <?php $snpData = $csvData['all_AA_change'] ?? []; ?>
                
    const snpClasses = [
        <?php
        $classes = [];
        for ($i = 1; $i < count($snpData); $i++) {
            $classes[] = '"' . $snpData[$i][0] . '"';
        }
        echo implode(", ", $classes)   ;
    ?>
    ];
    // Values taken from your chart
    const values = [
        <?php
        $vals = [];
        for ($i = 1; $i < count($snpData); $i++) {
            $vals[] = $snpData[$i][1];
        }
        echo implode(", ", $vals);
    ?>
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
          <!-- <div id="aaChangesChart" class="cols-12 col-md-12" style="height:420px;"></div> -->
        <script>
          (function(){
                // Label list (ordered top → bottom)
                // Use all_Amino_acids_statistic.csv data
                <?php $aminioAcidData = getTopItems($csvData['all_Amino_acids'] ?? [], 10); ?>
    const aaChanges = [
        <?php
        $changes = [];
        for ($i = 1; $i < count($aminioAcidData); $i++) {
            $changes[] = '"' . $aminioAcidData[$i][0] . '"';
        }
        echo implode(", ", $changes);
    ?>
    ];

    // Values from image
    const values = [
<?php
        $vals = [];
        for ($i = 1; $i < count($aminioAcidData); $i++) {
            $vals[] = $aminioAcidData[$i][1];
        }
        echo implode(", ", $vals);
    ?>
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
  <!-- <div id="snpClassDistChart" class="cols-12 col-md-12" style="height:420px;"></div> -->
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
              <!--//CHROM,POS,REF,ALT,Consequence,Gene,Feature,Gene_symbol,SWISSPROT,Protein_position,Amino_acids,canonical,clin_sig,SIFT_type,SIFT_score,Polyphen_type,Polyphen_score,3Dmapper_result,PPI-->
                <th>CHROM</th>
                <th>POS</th>
                <th>REF</th>
                <th>ALT</th>
                <th>Consequence</th>
                <th>Gene</th>
                <th>Feature</th>
                <th>Gene_symbol</th>
                <th>SWISSPROT</th>
                <th>Protein_position</th>
                <th>Amino_acids</th>
                <th>canonical</th>
                <th>clin_sig</th>
                <th>SIFT_type</th>
                <th>SIFT_score</th>
                <th>Polyphen_type</th>
                <th>Polyphen_score</th>
                <th>3Dmapper_result</th>
                <th>PPI</th>
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
          ],
          fixedColumns: {
            left: 2,
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

  <!-- Chart Initialization Scripts -->
  <script>
  // Helper function to create chart data from CSV
  function createChartData(csvData, maxItems = 15) {
    if (!csvData || csvData.length <= 1) return { labels: [], values: [] };

    const data = csvData.slice(1); // Skip header
    // Sort by count (second column) descending
    data.sort((a, b) => parseFloat(b[1]) - parseFloat(a[1]));
    const topData = data.slice(0, maxItems);

    return {
      labels: topData.map(row => row[0]),
      values: topData.map(row => parseFloat(row[1])),
      percentages: topData.map(row => parseFloat(row[2]))
    };
  }

  // Helper function to create ECharts option
  function createBarChartOption(title, data, colorPalette) {
    return {
      title: {
        text: title,
        left: 'center',
        top: 10,
        textStyle: { fontSize: 16 }
      },
      tooltip: {
        trigger: 'axis',
        axisPointer: { type: 'shadow' }
      },
      grid: {
        left: '15%',
        right: '10%',
        top: 60,
        bottom: 40
      },
      xAxis: {
        type: 'value',
        name: 'Count',
        nameLocation: 'middle',
        nameGap: 30
      },
      yAxis: {
        type: 'category',
        data: data.labels,
        name: 'Categories',
        nameGap: 20
      },
      series: [{
        type: 'bar',
        data: data.values.map((v, i) => ({
          value: v,
          itemStyle: { color: colorPalette[i % colorPalette.length] }
        })),
        label: {
          show: true,
          position: 'right',
          formatter: '{c}'
        },
        barWidth: 20
      }]
    };
  }

  // Color palettes
  const colorPalettes = {
    aa_change: ['#5DAE95', '#F4A261', '#E9C46A', '#E76F51', '#5DAE95', '#F4A261', '#F4A261', '#5DAE95', '#E76F51', '#E9C46A', '#E76F51', '#E9C46A'],
    amino_acids: ['#5DAE95', '#8BB68A', '#B7C27B', '#E1C870', '#F2B66A', '#E68A54', '#E07A47', '#D7684A', '#D45E47', '#CC5240'],
    genes: ['#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd', '#8c564b', '#e377c2', '#7f7f7f', '#bcbd22', '#17becf'],
    proteins: ['#4e79a7', '#f28e2c', '#e15759', '#76b7b2', '#59a14f', '#edc949', '#af7aa1', '#ff9da7', '#9c755f', '#bab0ab']
  };

  // Initialize charts when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    // Chart data from PHP
    const chartData = {
      all: {
        aa_change: <?php echo json_encode($csvData['all_AA_change'] ?? []); ?>,
        amino_acids: <?php echo json_encode($csvData['all_Amino_acids'] ?? []); ?>,
        gene_symbol: <?php echo json_encode($csvData['all_Gene_symbol'] ?? []); ?>,
        swissprot: <?php echo json_encode($csvData['all_SWISSPROT'] ?? []); ?>
      },
      ppi: {
        aa_change: <?php echo json_encode($csvData['ppi_AA_change'] ?? []); ?>,
        amino_acids: <?php echo json_encode($csvData['ppi_Amino_acids'] ?? []); ?>,
        gene_symbol: <?php echo json_encode($csvData['ppi_Gene_symbol'] ?? []); ?>,
        swissprot: <?php echo json_encode($csvData['ppi_SWISSPROT'] ?? []); ?>
      },
      'non_ppi': {
        aa_change: <?php echo json_encode($csvData['non_ppi_AA_change'] ?? []); ?>,
        amino_acids: <?php echo json_encode($csvData['non_ppi_Amino_acids'] ?? []); ?>,
        gene_symbol: <?php echo json_encode($csvData['non_ppi_Gene_symbol'] ?? []); ?>,
        swissprot: <?php echo json_encode($csvData['non_ppi_SWISSPROT'] ?? []); ?>
      }
    };

    // Initialize all charts
    const categories = ['all', 'ppi', 'non_ppi'];
    const dataTypes = ['aa_change', 'amino_acids', 'gene_symbol', 'swissprot'];
    const chartIds = {
      aa_change: ['aa-change-chart', 'amino-acids-chart', 'gene-symbol-chart', 'swissprot-chart'],
      amino_acids: ['aa-change-chart', 'amino-acids-chart', 'gene-symbol-chart', 'swissprot-chart'],
      gene_symbol: ['aa-change-chart', 'amino-acids-chart', 'gene-symbol-chart', 'swissprot-chart'],
      swissprot: ['aa-change-chart', 'amino-acids-chart', 'gene-symbol-chart', 'swissprot-chart']
    };

    categories.forEach(category => {
      dataTypes.forEach((dataType, index) => {
        const chartId = `${category}-${chartIds[dataType][index]}`;
        const element = document.getElementById(chartId);
        if (element && chartData[category][dataType]) {
          const data = createChartData(chartData[category][dataType]);

          const title = dataType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
          const option = createBarChartOption(`Top ${title}`, data, colorPalettes[dataType] || colorPalettes.genes);

          const chart = echarts.init(element);
          chart.setOption(option);
        }
      });
    });

    // Create thumbnail charts for tab headers
    function createThumbnailChart(containerId, data, color) {
      const element = document.getElementById(containerId);
      if (!element || !data) return;

      const thumbnailData = createChartData(data, 3); // Only top 3 for thumbnail
      const option = {
        grid: { left: 5, right: 5, top: 5, bottom: 5 },
        xAxis: { show: false, type: 'value' },
        yAxis: { show: false, type: 'category', data: thumbnailData.labels },
        series: [{
          type: 'bar',
          data: thumbnailData.values.map(v => ({ value: v, itemStyle: { color: color } })),
          barWidth: 8
        }]
      };

      const chart = echarts.init(element, null, { width: 80, height: 40 });
      chart.setOption(option);
    }

    // Create thumbnails for each tab
    createThumbnailChart('all-preview', chartData.all.aa_change, '#007bff');
    createThumbnailChart('ppi-preview', chartData.ppi.aa_change, '#28a745');
    createThumbnailChart('non-ppi-preview', chartData['non_ppi'].aa_change, '#dc3545');

    // Handle tab changes for lazy loading
    const tabButtons = document.querySelectorAll('#chartTabs button[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
      button.addEventListener('shown.bs.tab', function(event) {
        // Resize charts in the active tab to ensure proper display
        const targetId = event.target.getAttribute('data-bs-target').substring(1);
        const tabPane = document.getElementById(targetId);
        if (tabPane) {
          const charts = tabPane.querySelectorAll('[id$="-chart"]');
          charts.forEach(chartElement => {
            const chart = echarts.getInstanceByDom(chartElement);
            if (chart) {
              chart.resize();
            }
          });
        }
      });
    });
  });
  </script>

</body>

</html>