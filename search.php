<?php
include  "master.php";
$currentYear = date('Y');

$casesTypes = "'HCP', 'CRREV', 'CRREF', 'CRA', 'CRAA', 'PPCR', '561_A', '491_A', 'CPCR', 'LASCR', 'CRTA', 'BA', 'CPHCP', 'RPHCP', 'PERCR', 'ROBHC', '561', '491', 'LPACD'";
$criminal = "select case_type, type_name from case_type_t where type_name in ($casesTypes)";
$criminalCases = $connection->query($criminal);
$civil = "select case_type, type_name from case_type_t where type_name not in ($casesTypes)";
$civilCases = $connection->query($civil);
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
                            <option value="specific_status"> Case Status </option>
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

            <div id="specific_status" class="form-group">
                <div class="col-sm-12">
                    <div class="col-sm-4">
                        <select id="case_type_selector" name="case_type_selector" class="form-control">
                            <option value="civil">Civil</option>
                            <option value="criminal">Criminal</option>
                        </select>
                    </div>

                    <div class="col-sm-4">
                       <select id="civil_case_types" name="civil_case_types" class="form-control">
                           <?php foreach ($civilCases as $civilCase) {
                               echo "<option value='".$civilCase['case_type']."'>".$civilCase['type_name']."</option>";
                           }?>
                       </select>

                        <select id="criminal_case_types" name="criminal_case_types" class="form-control">
                            <?php foreach ($criminalCases as $criminalCase) {
                                echo "<option value='".$criminalCase['case_type']."'>".$criminalCase['type_name']."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <br><br>
                <div class="mt20 col-sm-12">
                    <div class="col-sm-4">
                        <input placeholder="Case Year" class="form-control" type="number" name="order_year" min="1898" max="<?php echo $currentYear; ?>">
                    </div>
                    <div class="col-sm-4">
                        <input placeholder="Case No." class="form-control" type="number" name="order_id" min="0">
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
            $('#specific_status').hide();
            $('#criminal_case_types').hide();

            $('#case_type_selector').change(function () {

                var value = $(this).val();
                if(value == 'civil') {
                    $('#criminal_case_types').hide();
                    $('#civil_case_types').show();

                } else if (value == 'criminal') {
                    $('#criminal_case_types').show();
                    $('#civil_case_types').hide();
                }
            });

            $('#selector').change(function () {
                var value = $(this).val();
                console.log(value);
                $('#full_data_submit').hide();
                $('#specific_year').hide();
                $('#show_range').hide();
                $('#specific_status').hide();

                if(value == 'year') {
                    $('#specific_year').show();
                } else if (value == 'range') {
                    $('#show_range').show();
                } else if (value == "full_data") {
                    $('#full_data_submit').show();
                } else if ( value == 'specific_status') {
                    $('#specific_status').show();
                }
            });
        </script>

        <br><br>
