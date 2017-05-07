<?php
include "database_access.php";
try {
    if (!$connection) {
        $message = "Connection Failed.";
    } else {
        $id = $_GET['id'];
        $query = "select * from civil_t where cino = '$id'";
        $statement = $connection->prepare($query);
        $statement->execute();
        $record = $statement->fetch();
    }
    include "master.php";
} catch (Exception $ex) {
    $message = "Error : ".$ex->getMessage();
}

?>

    <!------------------------------ Page Header -------------------------------->
    <div class="box-header">
        <h3 class="pull-left"> Case Detail</h3>
        </a>
    </div>
    <!------------------------------- Page Body --------------------------------->
    <div class="box-body">
        <div class="mt15">

            <?php if (!empty($message)) { ?>
                <div class="alert alert-danger">
                    <?php echo $message?>
                </div>
            <?php } ?>

            <?php  if(!empty($record)) { ?>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Case No.
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo $record['case_no'] ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Fill No.
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo $record['fil_no'] ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Fill Year
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo $record['fil_year'] ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Petitioner name
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo $record['pet_name'] ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Petitioner Advocate
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo  $record['pet_adv']?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Respondent name
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo $record['res_name'] ?>
                    </label>
                </div>

                <div class="form-group col-sm-12">
                    <label class="col-sm-4 col-xs-12 control-label text-right">
                        Respondent Advocate
                    </label>
                    <label class="col-sm-8 col-xs-12 text-left bold">
                        <?php echo $record['res_adv'] ?>
                    </label>
                </div>
            <?php }?>
        </div>
    </div>
<?php include "footer.php" ?>