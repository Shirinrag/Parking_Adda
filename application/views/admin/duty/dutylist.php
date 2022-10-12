<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.css"> 

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <section class="content">

         <!-- For Messages -->

        <?php $this->load->view('admin/includes/_messages.php') ?>

        <div class="card">

            <div class="card-body">

                <div class="d-inline-block">

                  <h3 class="card-title">

                    <i class="fa fa-list"></i>

                    <?= "Allocated Duty" ;?>

                  </h3>
                  <a href="<?= base_url('admin/Duty/duty_allocation') ?>">
                <button type="button" style="margin-top:-20%;margin-left:613%;background-color: #d17628;" class="btn btn-success">Add New Duty</button>
              </a>

              </div>

              <div class="d-inline-block float-right">

                

              </div>

            </div>

            <div class="card-body">

                <?php echo form_open("/",'class="filterdata"') ?>    

                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="type"  id="verifiers" class="form-control select2" onchange="filter_data()" >
                                <option value=""><?= "Select Verifier" ?></option>
                                <option value="0"><?= "All" ?></option>
                                <?php foreach($verifiers as $data):?>
                                    <option value="<?=$data['admin_id']?>"><?=$data['fullname']?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="place_id" class="form-control select2" onchange="filter_data()" >
                                <option value=""><?= "Select Placename" ?></option>
                                <?php foreach($placename as $datas):?>
                                    <option value="<?=$datas['id']?>"><?=$datas['placename']?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="date" name="date" id="duty_date" class="form-control"  placeholder="" onchange="filter_data()" />
                        </div>
                    </div>



                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="button" name="add2" id="add2" class="btn btn-success" onclick="resetme()">Reset</button>
                        </div>
                    </div>


                    <div class="col-md-1">
                        <div class="form-group">
                            <button type="button" onclick="return confirm('Are you sure to download the verifiers duty data.?')?tableToCSV(event):'';"  class="btn btn-warning" style="margin-left: -103px;">Export Data</button>
                        </div>
                    </div>





                </div>
                <?php echo form_close(); ?> 
            </div> 
        </div>
    </section>





    <!-- Main content -->

    <section class="content mt10">
    	<div class="card">
    		<div class="card-body">
               <!-- Load Admin list (json request)-->
               <div class="data_container"></div>
           </div>
       </div>
    </section>
    <!-- /.content -->

</div>










<!-- DataTables -->

<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.bootstrap4.js"></script>

<script>
  $(function () {
    $('.select2').select2();
    $('#example1').DataTable();

  });

//------------------------------------------------------------------

function filter_data(){
$('.data_container').html('<div class="text-center"><img src="<?=base_url('assets/dist/img')?>/loading.png"/></div>');
$.post('<?=base_url('admin/Duty/filterdata')?>',$('.filterdata').serialize(),function(){
	$('.data_container').load('<?=base_url('admin/duty/list_data')?>');
});
}

//------------------------------------------------------------------
function load_records()
{
$('.data_container').html('<div class="text-center"><img src="<?=base_url('assets/dist/img')?>/loading.png"/></div>');
$('.data_container').load('<?=base_url('admin/duty/list_data')?>');
}
load_records();

function resetme(){
$("select").val(null).trigger("change");
$("#duty_date").val(null);
filter_data();

}
</script>



<!-- Script Started For Datatable -->


    <script type="text/javascript">
        function tableToCSV() {
            // Variable to store the final csv data
            var csv_data = [];
            // Get each row data
            var rows = document.getElementsByTagName('tr');
            for (var i = 0; i < rows.length; i++) {
 
                // Get each column data
                var cols = rows[i].querySelectorAll('td,th');
 
                // Stores each csv row data
                var csvrow = [];
                for (var j = 0; j < cols.length; j++) {
 
                    // Get the text data of each cell
                    // of a row and push it to csvrow
                    csvrow.push(cols[j].innerHTML);
                }
 
                // Combine each column value with comma
                csv_data.push(csvrow.join(","));
            }
 
            // Combine each row data with new line character
            csv_data = csv_data.join('\n');
 
            // Call this function to download csv file 
            downloadCSVFile(csv_data);
 
        }
 
        function downloadCSVFile(csv_data) {
 
            // Create CSV file object and feed
            // our csv_data into it
            CSVFile = new Blob([csv_data], {
                type: "text/csv"
            });
 
            // Create to temporary link to initiate
            // download process
            var temp_link = document.createElement('a');
 
            // Download csv file
            temp_link.download = "DutyData.csv";
            var url = window.URL.createObjectURL(CSVFile);
            temp_link.href = url;
 
            // This link should not be displayed
            temp_link.style.display = "none";
            document.body.appendChild(temp_link);
 
            // Automatically click the link to
            // trigger download
            temp_link.click();
            document.body.removeChild(temp_link);
        }
    </script>




