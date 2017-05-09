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
                    <label class="col-sm-2 mt5" for="selector">Select a Option <em class="required-asterik">*</em></label>

                    <div class="col-sm-4">
                        <select id="selector" name="selector" class="form-control" required>
                            <option value=""> -- Select a Option -- </option>
                            <option value="full_data"> Full Data </option>
                            <option value="year"> Specific Year </option>
                            <option value="range"> Range </option>
                        </select>
                    </div>

                    <div id="full_data_submit" class="col-sm-2">
                        <input class="btn btn-green" type="submit" value="Go">
                    </div>
                </div>
            </div>

            <div id="show_range" class="form-group">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <input placeholder="Choose Start Year" class="form-control" type="number" name="start_year" min="1898" max="<?php echo $currentYear; ?>">
                    </div>

                    <div class="col-sm-4">
                        <input placeholder="Choose End Year" class="form-control" type="number" name="end_year" min="1898" max="<?php echo $currentYear; ?>">
                    </div>

                    <div class="col-sm-2">
                        <input class="btn btn-green" type="submit" value="Go">
                    </div>
                </div>
            </div>

            <div id="specific_year" class="form-group">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <input placeholder="Choose a Year" class="form-control" type="number" name="specific_year" min="1898" max="<?php echo $currentYear; ?>">
                    </div>

                    <div class="col-sm-2">
                        <input class="btn btn-green" type="submit" value="Go">
                    </div>
                </div>
            </div>
        </form>

        <script>

            $('#full_data_submit').hide();
            $('#specific_year').hide();
            $('#show_range').hide();

            $('#selector').change(function () {
                var value = $(this).val();
                console.log(value);
                $('#full_data_submit').hide();
                $('#specific_year').hide();
                $('#show_range').hide();
                if(value == 'year') {
                    $('#specific_year').show();
                } else if (value == 'range') {
                    $('#show_range').show();
                } else if (value == "full_data") {
                    $('#full_data_submit').show();
                }
            });
        </script>

        <br><br>
