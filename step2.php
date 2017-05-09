<?php
session_start();
$purposeId = 0;
$purposeType = '';

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

    $purposeType = !empty($_GET['purpose']) ? $_GET['purpose'] : '';
    switch ($purposeType) {
        case 'admission' :
            $purposeId = 2;
            break;
        case 'orders' :
            $purposeId = 4;
            break;
        case 'hearing' :
            $purposeId = 8;
            break;
        default :
            $purposeId = 0;
    }


    $queryModify = (!empty($_GET['type']) && $_GET['type'] == 'civil') ? 'not' : '';
    $queryCondition = $_SESSION['step1']. " and filcase_type $queryModify in ($criminalCaseIds) ";
    $queryCondition .= ($purposeId) ? ' and purpose_today = '.$purposeId : '';
    $query = "select civil_t.filcase_type, case_type_t.type_name, count(civil_t.cino) as count";
    $query .= (!$purposeId) ? ", sum(case when purpose_today = 2 then 1 else 0 end) admission, sum(case when purpose_today = 4 then 1 else 0 end) orders, sum(case when purpose_today = 8 then 1 else 0 end) hearing ": '';
    $query .= " from civil_t INNER JOIN case_type_t ON civil_t.filcase_type = case_type_t.case_type  and " . $queryCondition . " group by civil_t.filcase_type, case_type_t.type_name";

    $statement = $connection->prepare($query);
    $statement->execute();
    $caseReports = $statement->fetchAll();
}

include "search.php";

if (!empty($caseReports)) { ?>


    <table class="table">
        <thead>
        <tr>
            <th>Case Type</th>
            <th>Total</th>

            <?php if(!$purposeId) { ?>
                <th>Admission</th>
                <th>Orders</th>
                <th>Hearing</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($caseReports as $caseReport) {
            $caseId = $caseReport['filcase_type'];
            $caseCount = $caseReport['count'];
            ?>

        <tr>
            <td><?php echo $caseReport['type_name']; ?></td>
            <td><?php echo ($caseCount > 0) ? "<a href='step3.php?case_id=".$caseId."&purpose=$purposeType'>" . $caseCount . "</a>" : $caseCount ?></td>
            <?php if(!$purposeId) { ?>
                <td><?php echo ($caseReport['admission']  > 0) ? "<a href='step3.php?case_id=".$caseId. "&purpose=admission'>" . $caseReport['admission']  . "</a>" : $caseReport['admission']  ?></td>
                <td><?php echo ($caseReport['orders']  > 0) ? "<a href='step3.php?case_id=".$caseId. "&purpose=orders'>" . $caseReport['orders']  . "</a>" : $caseReport['orders']  ?></td>
                <td><?php echo ($caseReport['hearing']  > 0) ? "<a href='step3.php?case_id=".$caseId. "&purpose=hearing'>" . $caseReport['hearing']  . "</a>" : $caseReport['hearing']  ?></td>
            <?php } ?>

        </tr>
        <?php } ?>
        </tbody>
    </table>

<?php }
include "footer.php"?>
