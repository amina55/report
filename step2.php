<?php
session_start();
include "database_access.php";
if (!$connection) {
    $message = "Connection Failed.";
} else {

    $criminalCaseIds = !empty($_SESSION['criminal_case_ids']) ? $_SESSION['criminal_case_ids'] : '';
    if(empty($criminalCaseIds)) {
        $casesTypes = "'HCP', 'CRREV', 'CRREF', 'CRA', 'CRAA', 'PPCR', '561_A', '491_A', 'CPCR', 'LASCR', 'CRTA', 'BA', 'CPHCP', 'RPHCP', 'PERCR', 'ROBHC', '561', '491', 'LPACD'";
        $criminal = "select case_type, type_name from case_type_t where type_name in ($casesTypes)";
        $criminalCases = $connection->query($criminal);
        foreach ($criminalCases as $criminalCase) {
            $criminalCaseIds .= $criminalCase['case_type'].",";
        }
        $_SESSION['criminal_case_ids'] = $criminalCaseIds;
    }

     $queryModify = (!empty($_GET['type']) && $_GET['type'] == 'civil') ? 'not' : '';
    $query = $_SESSION['step1']. " and filcase_type $queryModify in ($criminalCaseIds) ";
    $query = "select civil_t.filcase_type, case_type_t.type_name, count(civil_t.cino) as case_count from civil_t ".
        "INNER JOIN case_type_t ON civil_t.filcase_type = case_type_t.case_type  and " . $query . " group by civil_t.filcase_type, case_type_t.type_name";
    $statement = $connection->prepare($query);
    $statement->execute();
    $caseReports = $statement->fetchAll();


}
include "search.php";

if (!empty($caseReports)) {

    foreach ($caseReports as $caseReport) {
        $caseCount = $caseReport['case_count'];
        $caseId = $caseReport['filcase_type']; ?>

        <div class="col-sm-12">
            <label class="col-sm-3 bold pull-left"> <?php echo $caseReport['type_name']?> </label>
            <div class="col-sm-3 pull-left">
                <?php echo ($caseCount > 0) ? "<a href='step3.php?case_id=".$caseId. "'>" . $caseCount . "</a>" : $caseCount ?>
            </div>
        </div><br><br>

        <?php
    }
}
include "footer.php"?>
