<?php
include  "master.php";
$currentYear = date('Y');
?>

    <!------------------------------ Page Header -------------------------------->
    <div class="box-header">
        <h3 class="pull-left"> View Report </h3>

    </div>
    <!------------------------------- Page Body --------------------------------->
    <div class="box-body mt15">
        <form action="step1.php" method="post" class="form-horizontal">

            <?php if (!empty($message)) { ?>
                <div class="alert alert-danger">
                    <?php echo $message?>
                </div>
            <?php } ?>

            <div class="form-group">
                <div class="col-sm-12">
                    <div class="col-sm-5">
                        <input placeholder="Chose Start Year" class="form-control" type="number" name="start_year" min="1956" max="<?php echo $currentYear; ?>" required>
                    </div>

                    <div class="col-sm-5">
                        <input placeholder="Chose End Year" class="form-control" type="number" name="end_year" min="1956" max="<?php echo $currentYear; ?>">
                    </div>

                    <div class="col-sm-2">
                        <input class="btn btn-green" type="submit" value="Go">
                    </div>
                </div>
            </div>
        </form>

        <br><br>
