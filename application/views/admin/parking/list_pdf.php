<?php

$html = '
		<h3>Parking List</h3>
		<table border="1" style="width:100%">
			<thead>
				<tr class="headerrow">
				<th >id</th>
                <th>Parking Name</th>
                <th>Parking Address</th>
                <th>No. Of Slot</th>
                <th>Place Status</th>
                <th>Status</th>
                <th>Action</th>
				</tr>
			</thead>
			<tbody>';
            $i=1;
			foreach($all_parking as $row):
			$html .= '		
				<tr class="oddrow">
					<td>'.$i++.'</td>
					<td>'.$row['placename'].'</td>
					<td>'.$row['mobile_no'].'</td>
					<td>'.$row['created_at'].'</td>
				</tr>';
			endforeach;

			$html .=	'</tbody>
			</table>			
		 ';
				
				
			   
		$mpdf = new mPDF('c');

		$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Codeglamour - Users List");
		$mpdf->SetAuthor("Codeglamour");
		$mpdf->watermark_font = 'Codeglamour';
		$mpdf->watermarkTextAlpha = 0.1;
		$mpdf->SetDisplayMode('fullpage');		 
		 

		$mpdf->WriteHTML($html);

		$filename = 'users_list1';

		ob_clean();
		$mpdf->Output($filename . '.pdf', 'D');			

		exit;
