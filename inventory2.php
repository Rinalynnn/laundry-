<?php include 'db_connect1.php' ?>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4><b>Inventory</b></h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <th class="text-center">#</th>
                                <th class="text-center">Supply Name</th>
                                <th class="text-center">Stock Available</th>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $supply = $conn->query("SELECT * FROM supply_list ORDER BY name ASC");
                                while($row=$supply->fetch_assoc()):
                                    $sup_arr[$row['id']] = $row['name'];
                                $inn = $conn->query("SELECT SUM(qty) as inn FROM inventory WHERE stock_type = 1 AND supply_id = ".$row['id']);
                                $inn = $inn && $inn->num_rows > 0 ? $inn->fetch_array()['inn'] : 0;
                                $out = $conn->query("SELECT SUM(qty) as `out` FROM inventory WHERE stock_type = 2 AND supply_id = ".$row['id']);
                                $out = $out && $out->num_rows > 0 ? $out->fetch_array()['out'] : 0;
                                $available = $inn - $out;
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td class=""><?php echo $row['name'] ?></td>
                                    <td class="text-right"><?php echo $available ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4><b>Supply In/Out List</b></h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <th class="text-center">Date</th>
                                <th class="text-center">Supply Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Type</th>
                            </thead>
                            <tbody>
                                <?php 
                                $inventory = $conn->query("SELECT * FROM inventory ORDER BY id DESC");
                                while($row=$inventory->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo date("Y-m-d", strtotime($row['date_created'])) ?></td>
                                    <td class=""><?php echo $sup_arr[$row['supply_id']] ?></td>
                                    <td class="text-right"><?php echo $row['qty'] ?></td>
                                    <?php if($row['stock_type'] == 1): ?>
                                    <td class="text-center"><span class="badge badge-primary"> IN </span></td>
                                    <?php else: ?>
                                    <td class="text-center"><span class="badge badge-secondary"> Used </span></td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('table').dataTable();
</script>
