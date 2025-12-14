<!DOCTYPE html>
<html lang="en">

<head>
<?php include 'includes/head.php'; ?>
<style>
#predictResult { margin-top: 12px; font-family: Inter, "Segoe UI", Roboto, Arial; }
.pr-info { margin-bottom: 8px; color: #333; font-size: 14px; }
.residues { display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
.res-cell { width:34px; text-align:center; border-radius:6px; padding:6px 4px; background:#f5f7fa; border:1px solid #e1e6ee; box-shadow:0 1px 0 rgba(0,0,0,0.02); cursor:default; }
.res-letter { font-weight:600; font-size:14px; color:#222; line-height:1; }
.res-pred { margin-top:4px; font-size:12px; color:#666; }
.res-cell.positive { background:linear-gradient(180deg,#ffecec,#ffd6d6); border-color:#ff9b9b; box-shadow:0 2px 6px rgba(255,110,110,0.12); }
.res-cell.positive .res-letter { color:#900; }
.res-legend { margin-top:10px; font-size:13px; color:#444; }
.legend-bullet { display:inline-block; width:12px; height:12px; margin-right:6px; vertical-align:middle; border-radius:3px; }
.legend-pos { background:#ff9b9b; border:1px solid #e06e6e; }
.legend-neg { background:#f5f7fa; border:1px solid #e1e6ee; }

</style>
<style>
.nightingale-viewer-container  td {
    padding: 5px;
  }
.nightingale-viewer-container  td:first-child {
    background-color: white;
    font: 0.8em sans-serif;
    white-space: nowrap;
  }
.nightingale-viewer-container  td:nth-child(2) {
    background-color: aliceblue;
  }
.nightingale-viewer-container  tr:nth-child(-n + 3) > td {
    background-color: transparent;
  }
</style>
<script type="importmap">
  {
    "imports": {
      "@nightingale-elements/": "https://cdn.jsdelivr.net/npm/@nightingale-elements/"
    }
  }
</script>
<script type="module">
  import "@nightingale-elements/nightingale-sequence@latest";
  import "@nightingale-elements/nightingale-track@latest";
  import "@nightingale-elements/nightingale-manager@latest";
  import "@nightingale-elements/nightingale-navigation@latest";
  import "@nightingale-elements/nightingale-colored-sequence@latest";
  import "@nightingale-elements/nightingale-linegraph-track@latest";
  
  const accession = "P15516";
  
  // Load feature and variation data from Proteins API
  const featuresData = await (
    await fetch("https://www.ebi.ac.uk/proteins/api/features/" + accession)
  ).json();
  
  customElements.whenDefined("nightingale-sequence").then(() => {
    const seq = document.querySelector("#sequence");
    seq.data = featuresData.sequence;
  });
  
  customElements.whenDefined("nightingale-colored-sequence").then(() => {
    const coloredSeq = document.querySelector("#colored-sequence");
    coloredSeq.data = featuresData.sequence;
  }); 
  
  
</script>
</head>

<body class="index-page">

 <?php include 'includes/header.php'; ?>

  <main class="main">
    <!-- Privacy Section -->
    <section id="privacy" class="privacy section">

      <div class="container" data-aos="fade-up">
        <!-- Header -->
        <div class="privacy-header" data-aos="fade-up">
          <div class="header-content">
            <div class="last-updated">Released Date: Novermber 01, 2025</div>
            <h1>Article title</h1>
            <p class="intro-text">
              Powered by protein language models, ProteinIS predicts interaction sites for individual proteins using only sequence information—no partner protein data required. Our platform also offers extensive mutation annotations and human proteome-wide predictions. In this website, you can submit your protein sequences for analysis or explore our comprehensive data resources.    
          </p>
          </div>
        </div>
      </div>

    </section><!-- /Privacy Section -->

       <!-- Quote Section -->
    <section id="quote" class="quote section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row justify-content-center">
          <div class="col-lg-12">
            <div class="quote-form-container">
              <div class="row g-0">
                <div class="col-lg-5">
                  <div class="quote-info">
                    <div class="quote-content">
                      <h3>Submit Your Prediction Task</h3>
                      <p>Input the protein sequence, then submit the prediction task.</p>

                      <div class="quote-form-wrapper">
                        <form action="server_side/scripts/predictor_api.php" method="post" class="php-email-form" data-aos="fade-left" data-aos-delay="200">
                      
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="predictor">Select Predictor</label>
                                <select id="predictor" name="predictor" class="form-control" required="">
                                  <option value="ESM">ESM</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="cutoff">Cut Off</label>
                                <input type="text"  name="cutoff" class="form-control" id="cutoff" placeholder="Cut Off. Default 0.5." value="0.5" required="">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="uniprotId">Uniprot ID</label>
                            <input type="text" name="uniprotId" class="form-control" id="uniprotId" placeholder="Uniprot ID">
                          </div>
                        
                            <div class="form-group">
                              <label for="proteinSeq">Protein Sequence (FASTA format)</label>
                              <textarea class="form-control" id="proteinSeq" name="proteinSeq" rows="5" placeholder="> P15516 MKFFVFALILALMLSMTGADSHAKRHHGYKRKFHEKHHSHRGYRSNYLYDN;">> P15516 MKFFVFALILALMLSMTGADSHAKRHHGYKRKFHEKHHSHRGYRSNYLYDN;</textarea>
                            </div>

                            <div class="loading">Loading</div>
                            <div class="error-message"></div>
                            <div class="sent-message">Your prediction task has been submitted successfully.</div>

                            <div class="text-center">
                              <button id="submitBtn" type="submit">Submit Prediction Task</button>
                            </div>
                          </form>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-7">
                  <div class="quote-form-wrapper">
                    <div class="quote-content">
                      <div class="form-header">
                          <h4>Prediction Result</h4>
                          <p>Your prediction results are shown below. </p>
                        </div>
                      <div id="seqChart" style="width:100%;height:200px;"></div>
                      <div id="predictResult"></div>
                      <div id="interfaceResidueDiv" class="mt-3">

                      <!-- nightingale viewer start-->
                       <div class="nightingale-viewer-container p-1 mt-3 mb-3 border">
<nightingale-manager>
  <table>
    <tbody>
      <tr>
        <td></td>
        <td>
          <nightingale-navigation
            id="navigation"
            min-width="51"
            height="40"
            length="51"
            display-start="1"
            display-end="51"
            margin-color="white"
          ></nightingale-navigation>
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          <nightingale-sequence
            id="sequence"
            min-width="400"
            height="40"
            length="51"
            display-start="1"
            display-end="51"
            margin-color="white"
            highlight-event="onmouseover"
          ></nightingale-sequence>
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          <nightingale-colored-sequence
            id="colored-sequence"
            min-width="400"
            height="15"
            length="51"
            display-start="1"
            display-end="51"
            scale="hydrophobicity-scale"
            margin-color="white"
            highlight-event="onmouseover"
          >
          </nightingale-colored-sequence>
        </td>
      </tr>
      <tr>
        <td>Prediction Interface Residues</td>
        <td>
          <nightingale-track
            id="binding"
            min-width="400"
            height="15"
            length="51"
            display-start="1"
            display-end="51"
            margin-color="aliceblue"
            highlight-event="onmouseover"
          ></nightingale-track>
        </td>
      </tr>
      <tr>
        <td>Prediction Results</td>
        <td>
          <nightingale-linegraph-track
            id="prediction-track"
            min-width="400"
            length="51"
            height="50"
            display-start="1"
            display-end="51"
            margin-color="aliceblue"
          ></nightingale-linegraph-track>
        </td>
      </tr>
    </tbody>
  </table>
</nightingale-manager>
 </div>
<!-- nightingale viewer end-->


                      <div class="form-group">
                            <h4>Interface Residue</h4>
                            <textarea class="form-control" id="interfaceResidue" name="interfaceResidue" rows="3" placeholder="" readonly></textarea>
                        </div>
                      </div>
                      <div class="text-center quote php-email-form mt-3">
                              <button id="downloadBtn" type="submit"><i class="bi bi-cloud-download-fill"></i> Download Prediction Result</button>
                      </div>
                    </div>
                  </div>
                 </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Quote Section -->

  </main>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/scripts.php'; ?>
<!-- ECharts CDN and chart script -->
 <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>

jQuery(function ($) {
    var $inputs = $('input[name=uniprotId], input[name=proteinSeq]');
    $inputs.on('input', function () {
        // Set the required property of the other input to false if this input is not empty.
        $inputs.not(this).prop('required', !$(this).val().length);
    });

    $('#submitBtn').on('click', function(e){
      e.preventDefault();
      var form = $(this).closest('form');
      var formData = form.serialize();

      $('.loading').show();
      $('.error-message').hide();
      $('.sent-message').hide();

      $.post(form.attr('action'), formData)
        .done(function(response){
          $('.loading').hide();
          response = JSON.parse(response);
          if (response.status !== 'success') {
              $('.error-message').show().text(response.message || 'An error occurred while processing your request.');
              return;
          }
          $('.sent-message').show();
          // Here you can handle the response and update the prediction result
          // For demo, we just log it
         
          uniprotId = response.data.uniprotId;
          seq = response.data.proteinSeq;
          yValues = response.data.predictResult[0];

          updatePredictionResult(uniprotId, seq, yValues);

          customElements.whenDefined("nightingale-sequence").then(() => {
            const seqEl = document.querySelector("#sequence");
            seqEl.data = seq;
            seqEl.length = seq.length;
            seqEl.displayEnd = seq.length;
          });

          customElements.whenDefined("nightingale-colored-sequence").then(() => {
            const coloredSeqEl = document.querySelector("#colored-sequence");
            coloredSeqEl.data = seq;
            coloredSeqEl.length = seq.length;
            coloredSeqEl.displayEnd = seq.length;
          });

          const site = document.querySelector("#site");
          site.data = features.filter(({ type }) => type === "SITE");
          
        })
        .fail(function(){
          $('.loading').hide();
          $('.error-message').show().text('There was an error submitting your task. Please try again.');
        });
    });

    var chartDom = document.getElementById('seqChart');
    var myChart = echarts.init(chartDom);
    var uniprotId = "P15516";
    var seq = "MKFFVFALILALMLSMTGADSHAKRHHGYKRKFHEKHHSHRGYRSNYLYDN";
    var xLabels = seq.split('');
    var yValues = [0.08,0.13,0.08,0.06,0.04,0.04,0.02,0.04,0.03,0.04,
                            0.02,0.04,0.04,0.04,0.05,0.07,0.06,0.06,0.08,0.25,
                            0.25,0.36,0.25,0.44,0.48,0.49,0.49,0.35,0.57,0.48,
                            0.58,0.50,0.52,0.55,0.51,0.50,0.54,0.52,0.44,0.56,
                            0.60,0.44,0.62,0.66,0.56,0.58,0.64,0.65,0.67,0.67,
                            0.62];

    var option = {
          title: { text: 'Sequence prediction scores', left: 'center', top: 8, textStyle: { fontSize: 14 } },
          tooltip: { trigger: 'axis' },
          xAxis: {
            type: 'category',
            data: xLabels,
            name: 'Residue',
            nameLocation: 'middle',
            nameGap: 30,
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
                  symbol: 'circle',
                  data: [
                      {
                          yAxis: 0.5, // The y-axis value where the horizontal line will be drawn
                          lineStyle: {
                              color: 'red', // Color of the line
                              type: 'solid' // Line type (solid, dashed, dotted)
                          },
                          label: {
                              formatter: 'Cut Off', // Label for the line
                              position: 'start',
                          },
                      }
                  ]
              }
          }],
          grid: { left: '6%', right: '6%', bottom: '22%', containLabel: true }
      };
 
      myChart.setOption(option);

      window.addEventListener('resize', function(){ myChart.resize(); });

      var renderPrediction = function(uniprotId, seq, scores) {
        var cutoffEl = document.getElementById('cutoff');
        var cutoff = 0.5;
        if (cutoffEl) {
          var v = parseFloat(cutoffEl.value);
          if (!isNaN(v)) cutoff = v;
        }
        var container = document.getElementById('predictResult');
        container.innerHTML = '';

        var info = document.createElement('div');
        info.className = 'pr-info';
        var positives = scores.reduce(function(acc, s){ return acc + (s >= cutoff ? 1 : 0); }, 0);
        info.textContent = 'Cutoff: ' + cutoff.toFixed(3) + '  |  Length: ' + seq.length + '  |  Positives: ' + positives;
        container.appendChild(info);

        var residuesDiv = document.createElement('div');
        residuesDiv.className = 'residues';
        seq.split('').forEach(function(res, i){
          var score = scores[i];
          var pred = score >= cutoff ? 1 : 0;
          var cell = document.createElement('div');
          cell.className = 'res-cell' + (pred ? ' positive' : '');
          cell.title = 'Pos ' + (i+1) + ' • ' + res + ' • score: ' + score;
          cell.innerHTML = '<div class="res-letter">' + res + '</div>' +
                           '<div class="res-pred">' + pred + '</div>';
          residuesDiv.appendChild(cell);
        });
        container.appendChild(residuesDiv);

        var legend = document.createElement('div');
        legend.className = 'res-legend';
        legend.innerHTML = '<span class="legend-bullet legend-pos"></span> predicted 1 &nbsp;&nbsp; <span class="legend-bullet legend-neg"></span> predicted 0';
        container.appendChild(legend);

        const mask = scores.map(s => s >= cutoff ? '1' : '0').join('');
        let result = [];

        for (let i = 0; i < mask.length; i++) {
            if (mask[i] === '1') {
                const position = i + 1;     // 1-based index
                const letter = seq[i];      // corresponding amino acid
                result.push(position + letter);
            }
        }

        var interfaceResidueEl = document.getElementById('interfaceResidue');
        if (interfaceResidueEl) {
          interfaceResidueEl.value = "> "+ uniprotId + " " +result.join(', ');
        }
      };

      var updatePredictionResult = function(uniprotId, newSeq, newScores){
        seq = newSeq;
        xLabels = seq.split('');
        yValues = newScores;
        // update chart
        myChart.setOption({ xAxis: { data: xLabels }, series: [{ data: yValues }] });
        renderPrediction(uniprotId, seq, yValues);
      };

       renderPrediction(uniprotId, seq, yValues);

       $('#downloadBtn').on('click', function(e){
          e.preventDefault();
          var interfaceResidueEl = document.getElementById('interfaceResidue');
          if (interfaceResidueEl) {
            var content = interfaceResidueEl.value;
            var blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'interface_residues.txt';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          }
       });

      customElements.whenDefined("nightingale-track").then(() => {
        // Nightingale expects start rather than the API's begin


        const binding = document.querySelector("#binding");
        binding.length = yValues.length;
        binding.displayEnd = yValues.length;
        binding.data = yValues
                                .map((value, index) => ({ value, index }))
                                .filter(item => item.value >= 0.5)
                                .map(item => ({
                                  type: "BINDING",
                                  category: "DOMAINS_AND_SITES",
                                  begin: String(item.index + 1),
                                  start:String(item.index + 1),
                                  end: String(item.index + 1),
                                  fill:'#ff9b9b',
                                }));
                            
        
    

      });

      customElements.whenDefined("nightingale-linegraph-track").then(()=>{
          const predictionTrack = document.querySelector("#prediction-track");
          predictionTrack.data = [
            {
                name: 'Prediction',
                range: [0, 1],
                color: 'red',
                lineCurve:'curveLinear',
                values: yValues.map((value, index) => ({ position:index+1,
                  value:value }))
            },
          ];
          console.log(predictionTrack.data);
      });
});
</script>

</body>

</html>