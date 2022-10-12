  <!-- Content Wrapper. Contains page content -->
 <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/select2/css/select2.min.css">
  <div class="content-wrapper">

    <!-- Main content -->

    <section class="content">
      <div class="card card-default">
        <div class="card-header">
          <div class="d-inline-block">
              <center><h3 class="card-title">Guides Duty Allocations </h3>
                <a href="<?= base_url('admin/Duty/dutylist') ?>">
                <button type="button" style="margin-top:-20%;margin-left:460%;background-color: #d17628;" class="btn btn-success">Duty List</button>
              </a>
</center>
          </div>
        <div>
          
       </div>
        </div>
        <div class="card-body">
           <!-- For Messages -->
            <?php $this->load->view('admin/includes/_messages.php') ?>
            <?php echo form_open(base_url('admin/Duty/VerifiersDutyAssign'), 'class="form-horizontal"');  ?> 
               <form name="add_dimensions" id="add_dimensions">
                            
                    <div class="table-responsive">
                        <table class="table" border="0" id="dynamic_field">
                            <center><tr style="background-color: white;">
                                <td style="width:35%">
                                        <select class="form-control select2"  id="splace_id" name="placename[]" required="" onchange="checkStatus()">
                                            <option value="">---Select Place---</option>
                                            <?php 
                                            foreach ($placename as $key => $values) {?>
                                                <option value="<?php echo $values['id'];?>"> <?php echo $values['placename'];?></option>
                                            <?php } ?>
                                        </select>   
                                </td>
                                <td style="width:25%">
                                        <select class="form-control select2" id="sverifier_id"  name="verifiers_ids[]" required="" onchange="checkStatus()">
                                            <option value="">---Select Verifiers---</option>
                                            <?php 
                                            foreach ($verifiers as $key => $valuess) {?>
                                                <option value="<?php echo $valuess['admin_id'];?>"> <?php echo $valuess['fullname'];?></option>
                                            <?php } ?>
                                        </select>   
                                </td>
                                <td style="width:25%">
                                   <center>
                                    <input  type="date" id="duty_date" name="duty_date[]" placeholder="Please Select Dates" min="<?php echo date('Y-m-d') ;?>"  class="form-control name_list"  onchange="checkStatus()" /></center>
                                </td>
                                
                                
                                </center>
                            <td><button type="button" name="add2" id="add2" class="btn btn-success">Add More</button></td></tr>
                        </table>
                        <center>
                          <input type="submit" name="submit" value="Assign Duty" class="btn btn-primary pull-right">
                        <br>
                    </div>
                </form>

            <?php echo form_close( ); ?>
        </div>
          <!-- /.box-body -->
      </div>
    </section> 
  </div>



<script src="<?= base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>

<script>

        // Started Date Picker Validations 
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        today = yyyy + '-' + mm + '-' + dd;
        $('#date_picker').attr('min',today);

        // Ended Date Picker Validations
        // Started Select2 Functions

          $(function () {
          $('.select2').select2()
          });
    // Ended Select2 Functions

$(document).ready(function(){
    var i=1;
    $('#add2').click(function(){
        i++;

        $.ajax({
            url:"<?=base_url('admin/Duty/getData')?>",
            data:{id:i,'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
            method:"POST",
            success:function(data)
            {
                var new_data = JSON.parse(data);
                var html = '';
                html += '<tr id="row'+i+'" style="background-color:white;">';
                html +='<td style=""><select class="form-control select2"   name="placename[]" required><option value="">---Select Place---</option>';
                $.each(new_data.message.placename, function (key, val) {
                 html+='<option value="'+val.id+'">'+val.placename+'</option>';
                 });
                html += '</select></td>';
                html +='<td style=""><select class="form-control select2"  name="verifiers_ids[]" required><option value="">---Select Verifiers---</option>';
                $.each(new_data.message.verifiers, function (key, val) {
                 html+='<option value="'+val.admin_id+'">'+val.fullname+'</option>';
                 });
                html += '</select></td>';

                html +='<td style=""><input id="date_pickerss" type="date" name="duty_date[]" placeholder="Please Select Dates" value="" class="form-control name_list" min="<?php echo date('Y-m-d') ;?>">';
                html += '</td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>';
               $('#dynamic_field').append(html);
               $('.select2').select2();
            }   
        });
    });
    
    });
    $(document).on('click', '.btn_remove', function(){
        var button_id = $(this).attr("id"); 
        $('#row'+button_id+'').remove();
    });

    function checkStatus(){

     var place_id = $('#splace_id').val();
     var verifiers = $('#sverifier_id').val();
     var date = (String( $("#duty_date").val() ));


     if(place_id!='' && verifiers!='' && date!=''){

     $.ajax({
            url:"<?=base_url('admin/Duty/checkStatus')?>",
            data:{'place_id':place_id,'verifier_id':verifiers,'duty_date':date,'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
            method:"POST",
            success:function(data)
            {
                var new_data = JSON.parse(data);
              
            }   
        });

     }

    }
    
</script>

   