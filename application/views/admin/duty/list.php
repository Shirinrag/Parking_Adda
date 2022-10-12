<div class="datalist">

    <table id="example1" class="table table-bordered table-hover">
        <thead style="background-color: burlywood;">
            <tr>
                <th width="50"><?= trans('id') ?></th>
                <th>Verifier Name</th>
                <th>Contact</th>
                <th>Placename</th>
                <th>Duty Date</th>
                <th>Allocation Date</th>
                <th width="120"><?= trans('action') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($info as $keys => $row): ?>
            <tr>
            	<td><?= $keys+1; ?></td>
                <td><?= $row['fullname'] ?></td>
                <td><?= $row['mobile_no']?></td> 
                <td><?= $row['placename']?></td>
                <td><?= date("d-m-Y", strtotime($row['duty_date'])); ?></td>
                <td><?= date("d-m-Y h:s A", strtotime($row['onCreated'])); ?></td>
                <td>
                    <a href="<?= base_url("admin/duty/deallocateVerifier/".$row['duty_id']); ?>" onclick="return confirm('are you sure to delete?')" class="fa fa-trash"></i></a>
                </td>
            </tr>

            <?php endforeach;?>

        </tbody>

    </table>

</div>



