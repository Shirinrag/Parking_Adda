<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 
<div class="content-wrapper">
    <section class="content">
        <?php $this->load->view('admin/includes/_messages.php') ?>
        <div class="card">
            <div class="card-body">
                <div class="d-inline-block">
                  <h3 class="card-title">
                    <i class="fa fa-list"></i>
                    <?= "Summary Reports" ;?>
                  </h3>
              </div>
              <div class="d-inline-block float-right">
              </div>
            <div class="card-body">
                <?php echo form_open("/",'class="filterdata"') ?>    
                <div class="row">
                    <div class="col-md-2">
                        <center><span class=""><b>From Date</b></span></center>
                        <div class="form-group">
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?= date('Y-m-01') ?>"  placeholder="" onchange="filter_data()" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <center><span class=""><b>To Date</b></span></center>
                        <div class="form-group">
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?= date('Y-m-d') ?>"  placeholder="" onchange="filter_data()" />
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?> 
            </div> 
        </div>
    </section>
    <section class="content mt10">
      <div class="card">
        <div class="container-fluid">
          <div id="stastics" style="display: none">
        <div class="row">
          <div class="col-12 col-sm-6 col-md-6">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fa fa-ticket"></i></span>
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Total Transactions</b></span>
                <span class="info-box-number">
                  <p id="total_txn"></p>
                </span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-6" onclick="gotoTarget('placeslist')">
            <div class="info-box mb-6">
              <span class="info-box-icon bg-success elevation-1"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Earnings</b></span>
                <span class="info-box-number"><p id="txn_amount"></p></span>
              </div>
            </div>
          </div>
           
       
        </div>
        </div>
      </div>
        <div class="card-body">
               <div class="js" id="midpageloader" style="display: none;"><div id="preloader"></div></div>
                <div id="summaryData"></div>
        </div>

           </div>
       </div>
    </section>
</div>
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>
<script>
  $(function () {
    $('.select2').select2();
    $('#example1').DataTable();
    filter_data();


  });

function filter_data(){
$('#midpageloader').show();  
$.post('<?=base_url('admin/Reports/getSummaryData')?>',$('.filterdata').serialize(),function(data){
    var data = JSON.parse(data);
    var html ='';
    html +='<div class="datalist">';
    html +='<table id="example1" class="table table-bordered table-hover">';
    html +='<thead style="background-color: burlywood;">';
    html +='<tr>';
    html +='<th>Sr</th>';
    html +='<th>Date</th>';
    html +='<th>App Downloads</th>';
    html +='<th>Booking Count</th>';
    html +='<th>Total Booking Hour</th>';
    html +='<th>Ticket Sales</th>';
    html +='<th>Wallet Recharge Count</th>';
    html +='<th>Wallet Recharge Amount</th>';
    html+='</tr>';
    html+='</thead>';
    html+='<tbody>';
    
    if(data.summary.length!='0'){
    for (var i = 0; i < data.summary.length; i++) {
          html +='<tr>';
              html +='<td>'+data.summary[i]['id']+'</td>';
              html +='<td>'+data.summary[i]['date']+'</td>';
              html +='<td>'+data.summary[i]['downloads']+'</td>';
              html +='<td>'+data.summary[i]['booking_count']+'</td>';
              html +='<td>'+data.summary[i]['total_booking_hrs']+'</td>';
              html +='<td>'+data.summary[i]['amount']+'</td>';
              html +='<td>'+data.summary[i]['wallet_recharge_count']+'</td>';
              html +='<td>'+data.summary[i]['wallet_recharge_amount']+'</td>';
              html +='</tr>';
                            
            }
             html +="<button type='button' class='btn btn-info' onclick='tableToCSV()'>Export CSV</button><br><br>" ;
    html+='</tbody>';
    html+='</table>';
    html+='</div>';
    $("#stastics").hide();
      // $('#total_txn').text(data.total_txn);
      // $('#txn_amount').text(data.txn_amount);
    }
    else{
         html+='</tbody>';
         html+='</table>';
         html+='</div>';
         html +="<center><h1>Data Not Available..!</h1></center>" ;
         $("#stastics").hide();
    }
    $("#summaryData").html(html);
});
$('#midpageloader').hide();  
}



</script>
<script type="text/javascript">
        function tableToCSV() {
            var csv_data = [];
            var rows = document.getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
 
                var cols = rows[i].querySelectorAll('td,th');
                var csvrow = [];
                for (var j = 0; j < cols.length; j++) {
                    csvrow.push(cols[j].innerHTML);
                }
                csv_data.push(csvrow.join(","));
            }
            csv_data = csv_data.join('\n');
            downloadCSVFile(csv_data);
 
         }
 
        function downloadCSVFile(csv_data) {
            CSVFile = new Blob([csv_data], {
                type: "text/csv"
            });
            var temp_link = document.createElement('a');
            temp_link.download = "SummaryData.csv";
            var url = window.URL.createObjectURL(CSVFile);
            temp_link.href = url;
            temp_link.style.display = "none";
            document.body.appendChild(temp_link);
            temp_link.click();
            document.body.removeChild(temp_link);
        }
    </script>
