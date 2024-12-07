<?php
include 'db_connect.php'; 
$d1 = (isset($_GET['d1']) ? date("Y-m-d",strtotime($_GET['d1'])) : date("Y-m-d"));
$d2 = (isset($_GET['d2']) ? date("Y-m-d",strtotime($_GET['d2'])) : date("Y-m-d"));
$data = $d1 == $d2 ? $d1 : $d1. ' - ' . $d2;
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="" class="control-label">Date From</label>
							<input type="date" class="form-control" name="d1" id="d1" value="<?php echo date("Y-m-d",strtotime($d1)) ?>">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="" class="control-label">Date To</label>
							<input type="date" class="form-control" name="d2" id="d2" value="<?php echo date("Y-m-d",strtotime($d2)) ?>">
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="" class="control-label">&nbsp;</label>
							<button class="btn-block btn-primary btn-sm" type="button" id="filter">Filter</button>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="" class="control-label">&nbsp;</label>
							<button class="btn-block btn-primary btn-sm" type="button" id="print"><i class="fa fa-print"></i> Print</button>
						</div>
					</div>
				</div>
				<hr>
				<div class="row" id="print-data">
					<div style="width:100%">
						<p class="text-center">
							<large><b>Habit Laba Laundry Report</b></large>
						</p>
						<p class="text-center">
							<large><b><?php echo $data ?></b></large>
						</p>
					</div>

					<table class='table table-bordered'>
						<thead>
							<tr>
								<th>Date</th>
								<th>Customer Name</th>
								<th>Total Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$total = 0;
								$qry = $conn->query("SELECT * FROM laundry_list WHERE pay_status = 1 AND date(date_created) BETWEEN '$d1' AND '$d2' ORDER BY date_created DESC");
								while($row=$qry->fetch_assoc()):
									$total += $row['total_amount'];
							?>
							<tr>
								<td><?php echo date("M d, Y",strtotime($row['date_created'])) ?></td>
								<td><?php echo ucwords($row['customer_name']) ?></td>
								<td class='text-right'><?php echo number_format($row['total_amount'],2) ?></td>
							</tr>
							<?php endwhile; ?>
						</tbody>
						<tfoot>
							<tr>
								<td class="text-right" colspan="2">Total</td>
								<td class="text-right"><?php echo number_format($total,2) ?></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	#print-data p {
		display: none;
	}
</style>

<noscript>
	<style>
		#div {
			width: 100%;
		}
		table {
			border-collapse: collapse;
			width: 100% !important;
		}
		tr, th, td {
			border: 1px solid #ddd;
		}
		.text-right {
			text-align: right;
		}
		p {
			margin: unset;
		}
		#div p {
			display: block;
		}
		p.text-center {
			text-align: -webkit-center;
		}

		/* Eye-catching styles for print */
		@media print {
			#div {
				width: 100%;
			}

			#header {
				position: relative;
				background-color: #a3c8f1; /* Baby blue background */
				padding: 20px;
				text-align: center;
				border-bottom: 2px solid #000;
				color: white;
				font-size: 24px;
			}

			/* Center the logo inside the header */
			#header img {
				width: 50px;
				display: block;
				margin: 0 auto;
			}

			.table th, .table td {
				padding: 15px;
				text-align: center;
				font-size: 14px;
			}

			.table th {
				background-color: #f1f1f1;
				font-weight: bold;
			}

			.table {
				width: 100%;
				margin-top: 20px;
				border-collapse: collapse;
			}

			/* Styling the footer row */
			.table tfoot tr td {
				font-weight: bold;
				font-size: 16px;
			}

			/* Adding additional spacing to content */
			hr {
				border: 1px solid #a3c8f1;
				width: 100%;
			}
		}
	</style>
</noscript>

<script>
	$('#filter').click(function(){
		location.replace('index.php?page=reports&d1='+$('#d1').val()+'&d2='+$('#d2').val())
	})
	$('#print').click(function(){
		var newWin = window.open('','_blank','height=500,width=600');
		var _html = $('#print-data').clone();
		var ns = $('noscript').clone();
		
		// Adding the header and logo before the content
		var header = '<div id="header"><img src="image/logo.jpg" alt="Logo"></div>';
		
		newWin.document.write(ns.html());
		newWin.document.write(header + _html.html());
		newWin.document.close();
		newWin.print();
		setTimeout(function(){
			newWin.close();
		},1500);
	})
</script>
