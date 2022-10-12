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
                    <?= "Booking Reports" ;?>
                  </h3>
              </div>
              <div class="d-inline-block float-right">
              </div>
            <div class="card-body">
                <?php echo form_open("/",'class="filterdata"') ?>    
                <div class="row">
                    <div class="col-md-3">
                        <center><span class=""><b> Placename</b></span></center>
                        <div class="form-group">
                            <select name="place_id" class="form-control select2" onchange="filter_data()" >
                                <option value="all"><?= "All" ?></option>
                                <?php foreach($placename as $datas):?>
                                    <option value="<?=$datas['id']?>"><?=$datas['placename']?></option>
                                <?php endforeach;?> 
                            </select>
                        </div>
                    </div>
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
                    <div class="col-md-3">
                         <center><span class=""><b>Status</b></span></center>
                        <div class="form-group">
                            <select name="type"  id="booking_status" class="form-control select2" onchange="filter_data()" >
                                <option value="all"><?= "All" ?></option>
                                 <option value="0"><?= "Onprocess" ?></option>
                                 <option value="1"><?= "Completed" ?></option>
                                 <option value="2"><?= "Cancelled" ?></option>
                                 <option value="4"><?= "Replaced" ?></option>
                            </select>
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
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fa fa-ticket"></i></span>
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Ticket Sales</b></span>
                <span class="info-box-number">
                  <p id="ticketSales"></p>
                </span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3" onclick="gotoTarget('placeslist')">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Earnings</b></span>
                <span class="info-box-number"><p id="earnings"></p></span>
              </div>
            </div>
          </div>
           <div class="col-12 col-sm-6 col-md-3" onclick="gotoTarget('bookingspage')">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-clock-o"></i></span>
              <div class="info-box-content">
                <span class="info-box-text" style="color: black;"><b>Bookings Hours</b></span>
                <span class="info-box-number"><p id="total_hrs"></p></span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-cloud-download"></i></span>
              <div class="info-box-content">
                <span class="info-box-text"><b>New Download</b></span>
                    <p id="total_downloads"></p>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
        <div class="card-body">
               <div class="js" id="midpageloader" >
                   <div id="preloader">
                    
                   </div></div>
                <div id="bookingData"></div>
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
$.post('<?=base_url('admin/Reports/getBookingData')?>',$('.filterdata').serialize(),function(data){
    var data = JSON.parse(data);
    var html ='';
    html +='<div class="datalist">';
    html +='<table id="example1" class="table table-bordered table-hover">';
    html +='<thead style="background-color: burlywood;">';
    html +='<tr>';
    html +='<th width="50">id</th>';
    html +='<th width="50">user id</th>';
    html +='<th>Booking Id</th>';
    html +='<th>Username</th>';
    html +='<th>Slot Id</th>';
    html +='<th>Placename</th>';
    html +='<th>From Date</th>';
    html +='<th>To Date</th>';
    html +='<th>Hours</th>';
    html +='<th>Cost</th>';
    html +='<th>Status</th>';
    html +='<th>Created On</th>';
    html+='</tr>';
    html+='</thead>';
    html+='<tbody>';
    
    if(data.bookingInfo.length!='0'){
    for (var i = 0; i < data.bookingInfo.length; i++) {
          html +='<tr>';
              html +='<td>'+data.bookingInfo[i]['id']+'</td>';
              html +='<td>'+data.bookingInfo[i]['userid']+'</td>';
              html +='<td>'+data.bookingInfo[i]['bookingId']+'</td>';
              html +='<td>'+data.bookingInfo[i]['username']+'</td>';
              html +='<td>'+data.bookingInfo[i]['slotinfo']+'</td>';
              html +='<td>'+data.bookingInfo[i]['place_address']+'</td>';
              html +='<td>'+data.bookingInfo[i]['from_date']+'</td>';
              html +='<td>'+data.bookingInfo[i]['to_date']+'</td>';
              html +='<td>'+data.bookingInfo[i]['booking_hrs']+'</td>';
              html +='<td>'+data.bookingInfo[i]['cost']+'</td>';
              html +='<td>'+data.bookingInfo[i]['status']+'</td>';
              html +='<td>'+data.bookingInfo[i]['created_date']+'</td>';
              html +='</tr>';
                            
            }
             html +="<button type='button' class='btn btn-info' onclick='tableToCSV()'>Export CSV</button><br><br>" ;
    html+='</tbody>';
    html+='</table>';
    html+='</div>';
    $("#stastics").show();
      $('#ticketSales').text(data.ticketSales);
      $('#earnings').text(data.earnings);
      $('#total_hrs').text(data.total_hrs);
      $('#total_downloads').text(data.total_downloads);
      

    }
    else{
         html+='</tbody>';
         html+='</table>';
         html+='</div>';
         html +="<center><h1>Data Not Available..!</h1></center>" ;
         $("#stastics").hide();
    }
    $("#bookingData").html(html);

    
                
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
            temp_link.download = "DutyData.csv";
            var url = window.URL.createObjectURL(CSVFile);
            temp_link.href = url;
            temp_link.style.display = "none";
            document.body.appendChild(temp_link);
            temp_link.click();
            document.body.removeChild(temp_link);
        }
    </script>
