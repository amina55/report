<?php
session_start();
$message = $query = '';
include "database_access.php";
if (!$connection) {
    $message = "Connection Failed.";
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $selector = trim($_POST['selector']);

        if(!$selector) {
            $message = "Please choose a option";
        } elseif ($selector == 'full_data') {
            $query = "true";
        } elseif ($selector == 'year') {
            $startYear = trim($_POST['specific_year']);
            if(!$startYear) {
                $message = "Choose a Specific Year";
            } else {
                $query = " fil_year = '$startYear'";
            }
        } elseif ($selector == 'range') {
            $startYear = trim($_POST['start_year']);
            $endYear = trim($_POST['end_year']);
           if ($startYear && $endYear) {
                if ($startYear > $endYear) {
                    $message = "Start Year should be less than End Year.";
                } else {
                    $str = '';
                    for ($i = $startYear; $i <= $endYear; $i++) {
                        $str .= " '$i',";
                    }
                    $str = rtrim($str, ',');
                    $query = " fil_year in ($str) ";
                }
           } else {
               $message = 'Please choose Start and End Year for viewing report';
           }
        } elseif ($selector == 'specific_status') {
            $caseType = trim($_POST['case_type_selector']);
            $caseTypeId = trim($_POST[$caseType.'_case_types']);
            $orderYear = trim($_POST['order_year']);
            $orderId = trim($_POST['order_id']);

            if(empty($caseType) || empty($caseTypeId) || empty($orderYear) || empty($orderId)) {
                $message = "Required Parameter is missing.";
            } else {
                $orderQuery = "select * from civil_t where fil_year = $orderYear and filcase_type = $caseTypeId and fil_no = $orderId";
                $statement = $connection->prepare($orderQuery);
                $statement->execute();
                $orderDetail = $statement->fetch();
                if(empty($orderDetail)) {
                    $message = "There is no record of this Order.";
                } else {
                    if(in_array($orderDetail['purpose_today'], [2,4,8])) {
                        header('Location:view-detail.php?id='.$orderDetail['cino']);
                    } else {
                        $message = "This Order is not in admission, orders and hearing category.";
                    }
                }
            }
        }

        if ($query) {
            $_SESSION['step1'] = $query;
            $query = "select count(cino) as total_count from civil_t where " . $query;
            $statement = $connection->prepare($query);
            $statement->execute();
            $reports = $statement->fetch();

            if($reports['total_count'] > 0 ) {
                $criminalCaseIds = !empty($_SESSION['criminal_case_ids']) ? $_SESSION['criminal_case_ids'] : '';
                if (empty($criminalCaseIds)) {
                    $casesTypes = "'HCP', 'CRREV', 'CRREF', 'CRA', 'CRAA', 'PPCR', '561_A', '491_A', 'CPCR', 'LASCR', 'CRTA', 'BA', 'CPHCP', 'RPHCP', 'PERCR', 'ROBHC', '561', '491', 'LPACD'";
                    $criminal = "select case_type, type_name from case_type_t where type_name in ($casesTypes)";
                    $criminalCases = $connection->query($criminal);
                    foreach ($criminalCases as $criminalCase) {
                        $criminalCaseIds .= $criminalCase['case_type'] . ",";
                    }
                    $criminalCaseIds = rtrim($criminalCaseIds, ',');
                    $_SESSION['criminal_case_ids'] = $criminalCaseIds;
                }

                $query = $_SESSION['step1'] . " and filcase_type in ($criminalCaseIds) ";
                $query = "select count(cino) as count, sum(case when purpose_today = 2 then 1 else 0 end) admission, ".
                    " sum(case when purpose_today = 4 then 1 else 0 end) orders, sum(case when purpose_today = 8 then 1 else 0 end) hearing ".
                    "from civil_t where " . $query;
                $statement = $connection->prepare($query);
                $statement->execute();
                $criminalReport = $statement->fetch();

                $query = $_SESSION['step1'] . " and filcase_type not in ($criminalCaseIds) ";
                $query = "select count(cino) as count, sum(case when purpose_today = 2 then 1 else 0 end) admission, ".
                    " sum(case when purpose_today = 4 then 1 else 0 end) orders, sum(case when purpose_today = 8 then 1 else 0 end) hearing ".
                    "from civil_t where " . $query;
                $statement = $connection->prepare($query);
                $statement->execute();
                $civilReport = $statement->fetch();

                $query = "select count(cino) as count, sum(case when purpose_today = 2 then 1 else 0 end) admission, ".
                    " sum(case when purpose_today = 4 then 1 else 0 end) orders, sum(case when purpose_today = 8 then 1 else 0 end) hearing ".
                    "from civil_t where " . $_SESSION['step1'];
                $statement = $connection->prepare($query);
                $statement->execute();
                $reports = $statement->fetch();
            }
        }
    }
}

include  "search.php"; ?>

    <?php if (!empty($reports)) { ?>

    <table class="table">
        <thead>
        <tr>
            <th>Type</th>
            <th>Total</th>
            <th>Admission</th>
            <th>Orders</th>
            <th>Hearing</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($criminalReport)) { ?>

        <tr>
            <td>Criminal</td>
            <td><?php echo ($criminalReport['count'] > 0) ? "<a href='step2.php?type=criminal'>".$criminalReport['count']."</a>" : $criminalReport['count']?></td>
            <td><?php echo ($criminalReport['admission'] > 0) ? "<a href='step2.php?type=criminal&purpose=admission'>".$criminalReport['admission']."</a>" : $criminalReport['admission']?></td>
            <td><?php echo ($criminalReport['orders'] > 0) ? "<a href='step2.php?type=criminal&purpose=orders'>".$criminalReport['orders']."</a>" : $criminalReport['orders']?></td>
            <td><?php echo ($criminalReport['hearing'] > 0) ? "<a href='step2.php?type=criminal&purpose=hearing'>".$criminalReport['hearing']."</a>" : $criminalReport['hearing']?></td>
        </tr>

        <?php }
        if (!empty($civilReport)) { ?>

        <tr>
            <td>Civil</td>
            <td><?php echo ($civilReport['count'] > 0) ? "<a href='step2.php?type=civil'>".$civilReport['count']."</a>" : $civilReport['count']?></td>
            <td><?php echo ($civilReport['admission'] > 0) ? "<a href='step2.php?type=civil&purpose=admission'>".$civilReport['admission']."</a>" : $civilReport['admission']?></td>
            <td><?php echo ($civilReport['orders'] > 0) ? "<a href='step2.php?type=civil&purpose=orders'>".$civilReport['orders']."</a>" : $civilReport['orders']?></td>
            <td><?php echo ($civilReport['hearing'] > 0) ? "<a href='step2.php?type=civil&purpose=hearing'>".$civilReport['hearing']."</a>" : $civilReport['hearing']?></td>
        </tr>

        <?php }
        if (!empty($reports)) { ?>

        <tr>
            <td>Total</td>
            <td><?php echo  $reports['count']?></td>
            <td><?php echo  $reports['admission']?></td>
            <td><?php echo  $reports['orders']?></td>
            <td><?php echo  $reports['hearing']?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <br><br>

    <div class="col-sm-12">
        <div class="col-sm-6">
            <h3>Criminal Graph</h3>
            <div id="criminalDiv" style="width: 480px; height: 380px;"></div>
        </div>
        <div class="col-sm-6">
            <h3>Civil Graph</h3>
            <div id="civilDiv" style="width: 480px; height: 380px;"></div>
        </div>
    </div>




    <script>
        var data = [{
            values: [ <?php echo $criminalReport['admission']?>, <?php echo $criminalReport['orders']?>, <?php echo $criminalReport['hearing']?>, <?php echo $criminalReport['count'] - ($criminalReport['admission']+$criminalReport['orders']+ $criminalReport['hearing']) ?>],
            labels: ['Admission', 'Orders', 'Hearing', 'Others'],
            type: 'pie'
        }];
        var data2 = [{
            values: [ <?php echo $civilReport['admission']?>, <?php echo $civilReport['orders']?>, <?php echo $civilReport['hearing']?>, <?php echo $civilReport['count'] - ($civilReport['admission']+$civilReport['orders']+ $civilReport['hearing']) ?>],
            labels: ['Admission', 'Orders', 'Hearing', 'Others'],
            type: 'pie'
        }];
        console.log(data);
        console.log(data2);
        var layout = {
            height: 380,
            width: 480
        };
        Plotly.newPlot('criminalDiv', data, layout);
        Plotly.newPlot('civilDiv', data2, layout);
    </script>
    <br><br>
<?php }


if(!empty($orderDetail)) {

}

include "footer.php"; ?>