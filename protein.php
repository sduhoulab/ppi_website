<!DOCTYPE html>
<html lang="en">
<?php
require_once 'vendor/autoload.php';
use Archon\DataFrame;
$config = include( 'server_side/scripts/config.php' );
$uniprotId = $_GET['uniprotId'] ?? 'P15516';


$db = new PDO(
    "mysql:host={$config->dbHost};dbname={$config->dbName};charset={$config->dbCharset};port={$config->dbPort}",
    $config->dbUser,
    $config->dbPass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

$sql = "SELECT * FROM proteome_interface_residue WHERE uniprot_id = :uniprotId LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bindParam(':uniprotId', $uniprotId);
$stmt->execute();
$protein = $stmt->fetch();
if (!$protein) {
    die('Protein not found');
}
$sequence = htmlspecialchars($protein['sequence']);
$interface_residues_predicted = htmlspecialchars($protein['interface_residues_predicted']);
$sequence_PDB = htmlspecialchars($protein['sequence_PDB']);
$interface_residues_PDB = htmlspecialchars($protein['interface_residues_PDB']);
$sequence_AF2 = htmlspecialchars($protein['sequence_AF2']);
$interface_residues_AF2 = htmlspecialchars($protein['interface_residues_AF2']);


$sql = "SELECT * FROM protein_info WHERE uniprot_id = :uniprotId LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bindParam(':uniprotId', $uniprotId);
$stmt->execute();
$protein_info = $stmt->fetch();
$uniprot_id = htmlspecialchars($protein_info['uniprot_id'] ?? 'N/A');
$protein_name = htmlspecialchars($protein_info['protein_name'] ?? 'N/A');
$gene_name = htmlspecialchars($protein_info['gene_name'] ?? 'N/A');
$organism = htmlspecialchars($protein_info['organism'] ?? 'N/A');
$sequence_length = htmlspecialchars($protein_info['sequence_length'] ?? 'N/A');
$function = htmlspecialchars($protein_info['function'] ?? 'N/A');

$sql = "SELECT * FROM pair WHERE uniprot1 = :uniprotId1 or uniprot2 = :uniprotId2";
$stmt = $db->prepare($sql);
$stmt->bindParam(':uniprotId1', $uniprotId);
$stmt->bindParam(':uniprotId2', $uniprotId);
$stmt->execute();
$pairs = $stmt->fetchAll();

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
$filename = 'scores/'.$uniprotId.'.txt';
if (!file_exists($filename)) {
    $df_score = DataFrame::fromArray([]);
}else{
    $df_score = DataFrame::fromCSV($filename , ['sep'=>"\t"]);
}

$sql = "SELECT * FROM domain WHERE uniprot_id = :uniprotId";
$stmt = $db->prepare($sql);
$stmt->bindParam(':uniprotId', $uniprotId);
$stmt->execute();
$domains = $stmt->fetchAll();

?>
<head>
<?php include 'includes/head.php'; ?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pdbe-molstar@latest/build/pdbe-molstar.css"  />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pdbe-molstar@latest/build/pdbe-molstar-light.css"  />
<style>
/* nicer Bootstrap-friendly protein info card/table (updated: removed heavy black label) */
.protein-card { max-width:100%; }
.protein-info { width:100%; border-collapse:separate; border-spacing:0 10px; font-family: Inter, "Segoe UI", Roboto, Arial; }
.protein-info tbody { display: table-row-group; }
.protein-info tr { background: transparent; }

/* card-like rows with subtle border and shadow */
.protein-info .row-card {
  display: flex;
  width: 100%;
  align-items: center;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #e9eef3;
  background: #ffffff;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
}

/* label: light background, dark text, subtle accent on the left */
.protein-info .label {
  flex: 0 0 110px;
  background: linear-gradient(90deg, #f6f8fa, #f3f6f9);
  color: #0b1220;
  font-weight: 700;
  padding: 14px 18px;
  vertical-align: middle;
  white-space: nowrap;
  position: relative;
}
/* accent stripe */
.protein-info .label::before{
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 6px;
  background: linear-gradient(180deg,#0d6efd,#0a58ca); /* Bootstrap primary gradient */
}

/* value: clean white panel with comfortable padding */
.protein-info .value {
  flex: 1 1 auto;
  background: #ffffff;
  color: #111827;
  padding: 14px 18px;
  vertical-align: middle;
  line-height: 1.45;
}

/* responsive tweaks */
@media (max-width:767px){
  .protein-info .label{ flex-basis:140px; font-size: 14px; padding:12px 14px; }
  .protein-info .value{ padding:12px 14px; font-size: 14px; }
}

/* Mol* viewer container */
#molstar-view {
  position: relative;
  width: 100%;
  padding-top: 56.25%; /* 16:9 aspect ratio */
  overflow: hidden;
  border-radius: 8px;
  margin-top: 1.5rem;
  background: #f6f8fa;
}
#molstar-view iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

/* Sequence display styling */
.seq-label{ font-family: Inter, "Segoe UI", Roboto, Arial; margin-bottom:6px; }
.seq-line{ font-family: Menlo, Monaco, "Courier New", monospace; white-space:pre-wrap; word-break:break-all; font-size:14px; }
.seq-line .residue{ display:inline-block; padding:1px 4px; margin:0 1px 4px 0; border-radius:4px; }
.seq-line .residue.highlight{ background: linear-gradient(90deg,#fff7cc,#fff1b8); border:1px solid #f1c40f; color:#7a4900; font-weight:700; }
@media (max-width:767px){ .seq-line{ font-size:13px; } }

</style>
</head>

<body class="index-page">

 <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Protein Information</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li><a href="protome.php">Protome</a></li>
            <li class="current">Protein</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="protein-details section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>> <?= $uniprot_id ?></h2>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
          <div class="col-lg-6">

      
          <!-- Protein info table start-->

             <div class="protein-sidebar mb-4">

              <div class="protein-overview-card">
                  <div class="service-icon">
                    <i class="bi bi-body-text"></i>
                  </div>
                  <h3><?= $protein_name?></h3>
                  <div class="service-stats">
                    <div class="stat-item">
                      <span class="stat-number"><?= $uniprot_id?></span>
                      <span class="stat-label">UniProt ID</span>
                    </div>
                    <div class="stat-item">
                      <span class="stat-number"><?= $sequence_length?></span>
                      <span class="stat-label">Sequence Length</span>
                    </div>
                    <div class="stat-item">
                      <span class="stat-number"><?= $organism?></span>
                      <span class="stat-label">Organism</span>
                    </div>
                  </div>
                </div>

                <div class="quick-info-card">
                  <h4>Protein Information</h4>
                  <div class="info-grid">
                    <!-- <div class="info-row">
                      <span class="label">Organism:</span>
                      <span class="value"><?= $organism?></span>
                    </div> -->
                    <div class="info-row">
                      <span class="label">Function:</span>
                      <span class="value"></span>
                    </div>
                    <div class="info-row">
                      <span class="label"></span>
                      <span class="value"><?= $function?></span>
                    </div>
                  </div>
                </div>

            </div>
            <!-- Protein info table end-->
            
              
            <!-- Prediction score section start -->
            <div id="prediction_score" class="card protein-card shadow-sm mb-4">
              <div id="seqChart" style="width: 100%; height: 400px;" class="card-body"></div>
            </div>
            <div id="entropy_score" class="card protein-card shadow-sm mb-4">
              <div id="entropyScoreChart" style="width: 100%; height: 400px;" class="card-body"></div>
            </div>
            <!-- Prediction score section end -->

            <div id="seq_prediction">
              <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                  <h5 class="mb-2">Sequence Predictions</h5>

                  <div class="sequence-block mb-2">
                    <div class="seq-label"><strong>&gt;Our model</strong></div>
                    <div class="seq-line" id="seq-our-model"></div>
                  </div>

                  <div class="sequence-block mb-2">
                    <div class="seq-label"><strong>&gt;PDB</strong></div>
                    <div class="seq-line" id="seq-pdb"></div>
                  </div>

                  <div class="sequence-block mb-2">
                    <div class="seq-label"><strong>&gt;AlphaFold(Huri_low/Huri_high/HuMap_low/HuMap_high)</strong></div>
                    <div class="seq-line" id="seq-af"></div>
                  </div>

                  <div class="sequence-block mb-0">
                    <div class="seq-label"><strong>&gt;Domain</strong></div>
                    <canvas id="domainChart" style="width: 100%; height: 200px;"></canvas>
                  </div>

                </div>
              </div>
            </div>

            

        </div>
          
          
          <div class="col-lg-6 service-details">

            <div class="service-sidebar">
              <div class="quick-info-card" data-aos="fade-left">
                <div class="card-body mb-4">
                  <div id="molstar-view" class="container card"></div>
              </div>
                <table class="table table-striped table-hover mt-5">
                  <thead class="table-success">
                    <tr>
                      <th scope="col" colspan="2">Interaction partners from PDB</th>
                    </tr>
                  </thead>
                  <thead class="table-primary">
                    <tr>
                      <th scope="col">Protein A</th>
                      <th scope="col">Protein B</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                    <?php foreach ($pairs as $pair): ?>
                      <?php if ($pair['uniprot1'] != $uniprotId) continue; ?>
                      <tr>
                        <td><?= htmlspecialchars($pair['uniprot1']) ?></td>
                        <td><?= htmlspecialchars($pair['uniprot2']) ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tr>
                  </tbody>
                </table>

                <table class="table table-striped table-hover mt-5">
                  <thead class="table-success">
                    <tr>
                      <th scope="col" colspan="3">Putative interaction partners</th>
                    </tr>
                  </thead>
                  <thead class="table-primary">
                    <tr>
                      <th scope="col">Protein A</th>
                      <th scope="col">Protein B</th>
                      <th scope="col">pDockQ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                    <?php foreach ($pairs as $pair): ?>
                      <tr>
                        <td><?= htmlspecialchars($pair['uniprot1']) ?></td>
                        <td><?= htmlspecialchars($pair['uniprot2']) ?></td>
                        <td></td>
                      </tr>
                    <?php endforeach; ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>

    </section><!-- /Starter Section Section -->


  </main>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pdbe-molstar@latest/build/pdbe-molstar-plugin.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pdbe-molstar@latest/build/pdbe-molstar-component.js"></script>
  <!-- ECharts CDN and chart script -->
  <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var seq = "<?= implode(array_column($df_score['Amino_Acid']->toArray(),'Amino_Acid')) ?>";
      var xLabels = seq.split('');
      var yValues = [<?= implode(',',array_column($df_score['Prediction_score']->toArray(),'Prediction_score'))?>];

      var chartDom = document.getElementById('seqChart');
      var myChart = echarts.init(chartDom);
      var option = {
        title: { text: 'Sequence prediction scores', left: 'center', top: 8, textStyle: { fontSize: 14 } },
        tooltip: { trigger: 'axis' },
        xAxis: {
          type: 'category',
          data: xLabels,
          name: 'Residue',
          axisLabel: { interval: 0, rotate: 0 }
        },
        yAxis: {
          type: 'value',
          min: 0,
          max: 1,
          name: 'Score'
        },
        series: [{
          data: yValues,
          type: 'line',
          smooth: true,
          areaStyle: {},
          lineStyle: { width: 2 },
          symbol: 'circle',
          symbolSize: 6,
          markLine: {
                  data: [
                      {
                          yAxis: 0.5, // The y-axis value where the horizontal line will be drawn
                          lineStyle: {
                              color: 'red', // Color of the line
                              type: 'solid' // Line type (solid, dashed, dotted)
                          },
                          label: {
                              formatter: 'Cut Off' // Label for the line
                          },
                          symbol: 'none' // No symbol at the ends of the line
                      }
                  ]
              }
        }],
        grid: { left: '6%', right: '6%', bottom: '22%', containLabel: true },
        
      };
      myChart.setOption(option);

      // make chart responsive
      window.addEventListener('resize', function(){ myChart.resize(); });
    });
  </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
      var seq = "<?= implode(array_column($df_score['Amino_Acid']->toArray(),'Amino_Acid')) ?>";
      var xLabels = seq.split('');
      var yValues = [<?= implode(',',array_column($df_score['Entropy']->toArray(),'Entropy'))?>];

      var chartDom = document.getElementById('entropyScoreChart');
      var myChart = echarts.init(chartDom);
      var option = {
        title: { text: 'Entropy scores', left: 'center', top: 8, textStyle: { fontSize: 14 } },
        tooltip: { trigger: 'axis' },
        xAxis: {
          type: 'category',
          data: xLabels,
          name: 'Residue',
          axisLabel: { interval: 0, rotate: 0 }
        },
        yAxis: {
          type: 'value',
          min: 0,
          max: 1,
          name: 'Score'
        },
        series: [{
          data: yValues,
          type: 'line',
          smooth: true,
          areaStyle: {},
          lineStyle: { width: 2 },
          symbol: 'circle',
          symbolSize: 6,
          
        }],
        grid: { left: '6%', right: '6%', bottom: '22%', containLabel: true },
        
      };
      myChart.setOption(option);

      // make chart responsive
      window.addEventListener('resize', function(){ myChart.resize(); });
    });
  </script>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    // sequences (could be replaced with dynamic values later)
    var seqOur = "<?= $sequence?>";
    var seqPDB = "<?= $sequence_PDB?>";
    var seqAF  = "<?= $sequence_AF2?>";

    function renderSequence(containerId, seq, highlightIndex){
      var el = document.getElementById(containerId);
      if(!el) return;
      // clear
      el.innerHTML = '';
      for(var i=0;i<seq.length;i++){
        var span = document.createElement('span');
        span.className = 'residue';
        span.textContent = seq.charAt(i);
        interface_residue = seq.charAt(i)+(i+1);
        if((containerId==='seq-our-model' && "<?= $interface_residues_predicted ?>".includes(interface_residue))
        || (containerId==='seq-pdb' && "<?= $interface_residues_PDB ?>".includes(interface_residue))
        || (containerId==='seq-af' && "<?= $interface_residues_AF2 ?>".includes(interface_residue))){
          span.classList.add('highlight');
          span.setAttribute('title','Prediction 1 residue (position '+(i+1)+')');
        }
        el.appendChild(span);
      }
    }

    renderSequence('seq-our-model', seqOur);
    renderSequence('seq-pdb', seqPDB);
    renderSequence('seq-af', seqAF);
  });
</script>
<script>
    function getJsonParam(paramName) {
        const paramString = new URL(window.location).searchParams.get(paramName);
        const param = JSON.parse(paramString);
        return param ?? undefined;
    }
    /** Load CSS with a different skin (theme) */
    function loadSkin(skin) {
        if (skin) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = `build/pdbe-molstar-${skin}.css`;
            document.getElementsByTagName('head')[0].appendChild(link);
        }
    }

    // Create plugin instance
    const viewerInstance = new PDBeMolstarPlugin();

    // Set default options (Checkout available options list in the documentation)
    const defaultOptions = {
      customData: {
          url: `pdb/<?= $uniprot_id ?>.pdb`,
          format: 'pdb'
      },
        // moleculeId: '1hda',
        // moleculeId: '<?= $uniprot_id ?>',
        // expanded: true,
        // loadMaps: true,
        bgColor: 'white',
        // hideControls: true,
        // domainAnnotation: true,
        // validationAnnotation: true,
        // symmetryAnnotation: true,
        // subscribeEvents: true,
        // loadingOverlay: true,
        // hideCanvasControls: ['expand', 'selection', 'animation', 'controlToggle', 'controlInfo']
        // loadMaps: true,
        // mapSettings: { defaultView: 'selection-box', 'em': { wireframe: true, opacity: 0.4 }, '2fo-fc': { wireframe: true, opacity: 0.4 } }
    };
    const options = { ...defaultOptions, ...getJsonParam('options') };

    const optionsDiv = document.getElementById('options');
    // optionsDiv.innerHTML = JSON.stringify(options, undefined, 2).replace(/"(\w+)":/g, '$1:');

    // Get element from HTML/Template to place the viewer 
    const viewerContainer = document.getElementById('molstar-view');

    // Call render method to display the 3D view
    viewerInstance.render(viewerContainer, options);

    const selectData = getJsonParam('select');
    if (selectData) {
        viewerInstance.events.loadComplete.subscribe(() => viewerInstance.visual.select(selectData));
    }

    loadSkin(getJsonParam('skin'));

    const size = getJsonParam('size');
    if (size) viewerContainer.setAttribute('style', `width: ${size[0]}px; height: ${size[1]}px;`);

    // document.addEventListener('PDB.molstar.mouseover', (e) => { 
    //   //do something on event
    //   console.log(e)
    // });

</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const canvas = document.getElementById("domainChart");
  const ctx = canvas.getContext("2d");

  const padding = 10;
  const width = canvas.width - padding * 2;

  const domainData = <?= json_encode($domains) ?>;

  const lineYs = {'PFAM':20,'SMART':60,'CDD':100,'PROSITE':140};
  const colors = {'PFAM':'#4F81BD','SMART':'#C0504D','CDD':'#9BBB59','PROSITE':'#8064A2'};

  // Example ranges (min/max normalized 0–100)
  const seriesA = [
    { min: 30, max: 60, color: "#F8D56A" },
    { min: 62, max: 90, color: "#F8D56A" }
  ];

  const seriesB = [
    { min: 20, max: 85, color: "#E8A6AF" }
  ];

  function drawLine(y) {
    ctx.strokeStyle = "#000000";
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(padding, y+padding);
    ctx.lineTo(canvas.width - padding, y+padding);
    ctx.stroke();
  }

  function drawBlock(y, min, max, color, height = 24) {
    const xStart = padding + (min / 100) * width;
    const xEnd = padding + (max / 100) * width;
    const blockWidth = xEnd - xStart;
    ctx.fillStyle = color;
    ctx.fillRect(xStart, y - height / 2+padding, blockWidth, height);
  }

  domainData.forEach(domain => {
    const lineY = lineYs[domain.database.toUpperCase()] || 180;
    const color = colors[domain.database.toUpperCase()] || '#000000';
    drawLine(lineY);
    drawBlock(lineY, domain.start_location / domain.sequence_length * 100, domain.end_location / domain.sequence_length * 100, color, 20);
    // Draw domain label
    ctx.fillStyle = "#000000";
    ctx.font = "12px Arial";
    ctx.fillText(domain.database.toUpperCase(), padding, lineY - 10+padding);
  });
  
});

</script>

</body>

</html>