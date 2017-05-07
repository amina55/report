<?php
session_start();
$message = '';
include "database_access.php";
if (!$connection) {
    $message = "Connection Failed.";
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $startYear = trim($_POST['start_year']);
        $endYear = trim($_POST['end_year']);

        if (!$startYear) {
            $message = 'Please choose Start Year for viewing report';
        } elseif ($startYear && !$endYear) {
            $query = " fil_year = '$startYear' ";
        } elseif ($startYear && $endYear) {
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
                $query = "select count(cino) as criminal_count from civil_t where " . $query;
                $statement = $connection->prepare($query);
                $statement->execute();
                $criminalReport = $statement->fetch();

                $query = $_SESSION['step1'] . " and filcase_type not in ($criminalCaseIds) ";
                $query = "select count(cino) as civil_count from civil_t where " . $query;
                $statement = $connection->prepare($query);
                $statement->execute();
                $civilReport = $statement->fetch();
            }
        }
    }
}

include  "search.php"; ?>

    <?php if (!empty($reports)) { ?>
    <div class="col-sm-12">
        <label class="col-sm-3 bold pull-left"> Total Records </label>
        <div class="col-sm-3 pull-left">
            <?php echo $reports['total_count']?>
        </div>

    </div><br><br><br><br>
    <?php }



    if (!empty($criminalReport)) { ?>
    <div class="col-sm-12">

        <label class="col-sm-3 bold pull-left"> Criminal Records </label>
        <div class="col-sm-3 pull-left">
            <?php echo ($criminalReport['criminal_count'] > 0) ? "<a href='step2.php?type=criminal'>".$criminalReport['criminal_count']."</a>" : $criminalReport['criminal_count']?>
        </div>
    </div><br><br>
    <?php }

    if (!empty($civilReport)) {
        $civilCount = $civilReport['civil_count']?>
    <div class="col-sm-12">

        <label class="col-sm-3 bold pull-left"> Civil Records </label>
        <div class="col-sm-3 pull-left">
            <?php echo ($civilCount > 0) ? "<a href='step2.php?type=civil'>".$civilCount."</a>" : $civilCount?>
        </div>
    </div><br><br>
    <?php } ?>




<?php
include "footer.php"; ?>