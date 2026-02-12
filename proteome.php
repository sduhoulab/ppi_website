<!DOCTYPE html>
<html lang="en">

<head>
<?php include 'includes/head.php'; ?>
  <!-- Datatable CSS File -->
  <link href="/assets/vendor/DataTables/datatables.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.5/css/dataTables.bootstrap5.css">
</head>

<body class="index-page">

 <?php include 'includes/header.php'; ?>

  <main class="main">

   <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Protome Datasets</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Protome</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

<!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

      <!-- Section Title -->
      <div class="container section-title">
        <h2>Protome Dataset Analysis</h2>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up">
        <!-- Amino acid frequency distribution chart -->
        <div id="aaChart" style="width:100%;height:420px;margin-top:18px;"></div>
        <!-- ECharts for bar plot -->
        <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
        <script>
          (function(){
            var labels = ['A','C','D','E','F','G','H','I','K','L','M','N','P','Q','R','S','T','V','W','Y'];
            // Fake frequency counts (values chosen to resemble the pasted image)
            var counts = [40425,18515,63158,93905,61238,41677,43197,50084,74062,120238,28203,50981,52057,65887,117051,62835,50943,56284,22131,67312];
            var total = counts.reduce(function(a,b){return a+b;},0);

            // color palette (one color per bar)
            var colors = [
              '#2f7ed8','#0d233a','#8bbc21','#910000','#1aadce','#492970','#f28f43',
              '#77a1e5','#c42525','#a6c96a','#2b908f','#f45b5b','#91e8e1','#7cb5ec',
              '#f7a35c','#90ed7d','#8085e9','#e4d354','#808080','#f15c80'
            ];

            var chartDom = document.getElementById('aaChart');
            var myChart = echarts.init(chartDom);

            var option = {
              title: {
                text: 'Amino Acid Frequency Distribution',
                left: 'center',
                top: 8,
                textStyle: { fontSize: 16 }
              },
              tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' },
                formatter: function(params){
                  var p = params[0];
                  var pct = (p.value / total * 100).toFixed(1);
                  return p.axisValue + '<br/>Count: ' + p.value.toLocaleString() + ' (' + pct + '%)';
                }
              },
              xAxis: {
                type: 'category',
                data: labels,
                name: 'Amino Acid',
                axisLabel: { interval: 0 }
              },
              yAxis: {
                type: 'value',
                name: 'Frequency Count',
                axisLabel: { formatter: function(v){ return v; } }
              },
              grid: { left: '6%', right: '6%', bottom: '10%', containLabel: true },
              series: [{
                type: 'bar',
                data: counts,
                barWidth: '60%',
                itemStyle: {
                  color: function(params){
                    return colors[params.dataIndex % colors.length];
                  }
                },
                label: {
                  show: true,
                  position: 'top',
                  distance: 6,
                  formatter: function(params){
                    var pct = (params.value / total * 100).toFixed(1);
                    return params.value.toLocaleString() + '\n(' + pct + '%)';
                  },
                  fontSize: 11
                }
              }]
            };

            myChart.setOption(option);
            window.addEventListener('resize', function(){ myChart.resize(); });
          })();
        </script>
  </div>
  <div class="container" data-aos="fade-left">
        <div>
          <table id="protome_table" >
          <thead>
            <tr>
              <th>ID</th>
              <th>Accession</th>
              <th>Protein Name</th>
              <th>Gene</th>
              <th>Sequence Length</th>
              <th>Interaction Residues Model</th>
              <th>Interaction Residues PDB</th>
              <th>Interaction Residues Alphafold</th>
            </tr>
          </thead>

        </table>
        </div>

          <!-- DataTables JS File -->
        <script src="/assets/vendor/DataTables/datatables.min.js"></script>
        <script>
     (function(){
        let table = new DataTable('#protome_table',{
          ajax: "server_side/scripts/protome_server_processing.php",
          processing: true,
          serverSide: true,
          scrollX: true,
          fixedHeader: true,
          
          columnDefs: [
              {
                  targets: 1,
                  render: function (data, type, row, meta) {
                      if (type === 'display') {
                          let link = 'protein.php?uniprotId=' + row[1];
                          return '<a href="' + link + '" class="link-primary">' + data + '</a>';
                      }
                      return data;
                  }
              },
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