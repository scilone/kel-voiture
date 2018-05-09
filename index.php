<?php

session_start();

ini_set('max_execution_time', 0);
ini_set("memory_limit", "550M");

include 'job.php';

$sSiteName = 'Kel-voiture';
$aTrad = [
    'guide' => 'guide',
    'about' => 'a propos',
    'contact' => 'contact',
    'home' => 'accueil'
];

function createCheckbox($value, $name, $nameOption, $defaultValue) {
    $checked = '';

    if (is_array($defaultValue)) {
        foreach ($defaultValue as $val) {
            if ($value == $val) {
                $checked = 'checked';
                break;
            }
        }
    } elseif ($value == $defaultValue)
        $checked = 'checked';

    return "<label><input type=\"checkbox\" name=\"$nameOption\" value=\"$value\" $checked>$name</label>";
}

function createOption($value, $name, $defaultValue) {
    $selected = '';

    if ($value == $defaultValue)
        $selected = 'selected';

    return "<option value=\"$value\" $selected>$name</option>";
}


if (!empty($_POST)) {
    $aParams = paramsFormatter($_POST);
    $aResult = getForLive($aParams);

    $iNumberPages = $aResult['numberPage'];
    unset($aResult['numberPage']);

    if (is_array($aResult)) {
        $iTotalResult = 0;
        foreach ($aResult as $sBrand => $aCars) {
            foreach ($aCars as $value) {
                $iTotalResult++;
            }
            ksort($aResult[$sBrand]);
        }
        ksort($aResult);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title><?= $sSiteName ?> - Trouver votre voiture</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/landing-page.css?<?= filemtime('css/landing-page.css')?>" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>
        <div id="overlay" style="display: none;height: 100%;width: 100%;background: grey;position: fixed;z-index: 999;opacity: 0.5;"></div>
        <div style="z-index: 1000;position: fixed;top: 30%;width: 100%;left: 0%;display:none;" id="overlay-wait">
            <div class="container">
                <div class="jumbotron" style="text-align:center">
                    Nous recherchons les véhicules correspondant à vos attentes<br><br>
                    <div class="progress">
                        <div style="width: 0%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="80" role="progressbar" id="progressBarWaiting" class="progress-bar progress-bar-danger">
                            <span class="percentProgressBar">0%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="navbar navbar-default" role="navigation">
            <div class="container topnav">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand topnav" href="index.php" style="color: #c9302c;"><?= $sSiteName ?></a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#services"><?= ucfirst($aTrad['guide']) ?></a>
                        </li>
                        <li>
                            <a href="#about"><?= ucfirst($aTrad['about']) ?></a>
                        </li>
                        <li>
                            <a href="#contact"><?= ucfirst($aTrad['contact']) ?></a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container -->
        </nav>


        <!-- Header -->
        <a name="about"></a>
        <div id="main-div">
            <div class="container">
                <div class="row">
                    <div class="page-header">
                        <h3>Recherche</h3>
                    </div>
                    <div class="jumbotron">
                        <form class="" method="post" action="index.php#result">
                            <div class="form-group">
                                <label for="categorie">Categorie:</label><br/>
                                <div class="checkbox" id="categorie">
                                    <?php if (!isset($_POST['categories'])) $_POST['categories'] = null; ?>
                                    <?= createCheckbox('48', 'Sans permis&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('40', 'Citadine&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('45', 'Coupé&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('46', 'Cabriolet&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('41,42', 'Berline&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('43', 'Break&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('44', 'Monospace&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('47', '4x4/SUV/Crossover&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                    <?= createCheckbox('49', 'Collection&nbsp;&nbsp;', 'categories[]', $_POST['categories']) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <select id="marque" class="form-control input-sm" name="makesModelsCommercialNames">
                                    <option value="" selected>Marque</option>
                                    <?= createOption('ABARTH', 'ABARTH', $_POST['marque']) ?>
                                    <?= createOption('ALFA ROMEO', 'ALFA ROMEO', $_POST['marque']) ?>
                                    <?= createOption('ASTON MARTIN', 'ASTON MARTIN', $_POST['marque']) ?>
                                    <?= createOption('AUDI', 'AUDI', $_POST['marque']) ?>
                                    <?= createOption('AUSTIN', 'AUSTIN', $_POST['marque']) ?>
                                    <?= createOption('BENTLEY', 'BENTLEY', $_POST['marque']) ?>
                                    <?= createOption('BMW', 'BMW', $_POST['marque']) ?>
                                    <?= createOption('BUGATTI', 'BUGATTI', $_POST['marque']) ?>
                                    <?= createOption('CADILLAC', 'CADILLAC', $_POST['marque']) ?>
                                    <?= createOption('CHEVROLET', 'CHEVROLET', $_POST['marque']) ?>
                                    <?= createOption('CHRYSLER', 'CHRYSLER', $_POST['marque']) ?>
                                    <?= createOption('CITROEN', 'CITROEN', $_POST['marque']) ?>
                                    <?= createOption('DACIA', 'DACIA', $_POST['marque']) ?>
                                    <?= createOption('DAEWOO', 'DAEWOO', $_POST['marque']) ?>
                                    <?= createOption('DAIHATSU', 'DAIHATSU', $_POST['marque']) ?>
                                    <?= createOption('DODGE', 'DODGE', $_POST['marque']) ?>
                                    <?= createOption('FERRARI', 'FERRARI', $_POST['marque']) ?>
                                    <?= createOption('FIAT', 'FIAT', $_POST['marque']) ?>
                                    <?= createOption('FORD', 'FORD', $_POST['marque']) ?>
                                    <?= createOption('GMC', 'GMC', $_POST['marque']) ?>
                                    <?= createOption('HONDA', 'HONDA', $_POST['marque']) ?>
                                    <?= createOption('HUMMER', 'HUMMER', $_POST['marque']) ?>
                                    <?= createOption('HYUNDAI', 'HYUNDAI', $_POST['marque']) ?>
                                    <?= createOption('INFINITI', 'INFINITI', $_POST['marque']) ?>
                                    <?= createOption('JAGUAR', 'JAGUAR', $_POST['marque']) ?>
                                    <?= createOption('JEEP', 'JEEP', $_POST['marque']) ?>
                                    <?= createOption('KIA', 'KIA', $_POST['marque']) ?>
                                    <?= createOption('LAMBORGHINI', 'LAMBORGHINI', $_POST['marque']) ?>
                                    <?= createOption('LANCIA', 'LANCIA', $_POST['marque']) ?>
                                    <?= createOption('LAND ROVER', 'LAND ROVER', $_POST['marque']) ?>
                                    <?= createOption('LEXUS', 'LEXUS', $_POST['marque']) ?>
                                    <?= createOption('LOTUS', 'LOTUS', $_POST['marque']) ?>
                                    <?= createOption('MASERATI', 'MASERATI', $_POST['marque']) ?>
                                    <?= createOption('MAZDA', 'MAZDA', $_POST['marque']) ?>
                                    <?= createOption('MCLAREN', 'MCLAREN', $_POST['marque']) ?>
                                    <?= createOption('MERCEDES', 'MERCEDES', $_POST['marque']) ?>
                                    <?= createOption('MERCEDES_AMG', 'MERCEDES-AMG', $_POST['marque']) ?>
                                    <?= createOption('MG', 'MG', $_POST['marque']) ?>
                                    <?= createOption('MINI', 'MINI', $_POST['marque']) ?>
                                    <?= createOption('MITSUBISHI', 'MITSUBISHI', $_POST['marque']) ?>
                                    <?= createOption('NISSAN', 'NISSAN', $_POST['marque']) ?>
                                    <?= createOption('OPEL', 'OPEL', $_POST['marque']) ?>
                                    <?= createOption('PEUGEOT', 'PEUGEOT', $_POST['marque']) ?>
                                    <?= createOption('PIAGGIO', 'PIAGGIO', $_POST['marque']) ?>
                                    <?= createOption('PORSCHE', 'PORSCHE', $_POST['marque']) ?>
                                    <?= createOption('RENAULT', 'RENAULT', $_POST['marque']) ?>
                                    <?= createOption('ROLLS ROYCE', 'ROLLS ROYCE', $_POST['marque']) ?>
                                    <?= createOption('ROVER', 'ROVER', $_POST['marque']) ?>
                                    <?= createOption('SAAB', 'SAAB', $_POST['marque']) ?>
                                    <?= createOption('SEAT', 'SEAT', $_POST['marque']) ?>
                                    <?= createOption('SKODA', 'SKODA', $_POST['marque']) ?>
                                    <?= createOption('SMART', 'SMART', $_POST['marque']) ?>
                                    <?= createOption('SSANGYONG', 'SSANGYONG', $_POST['marque']) ?>
                                    <?= createOption('SUBARU', 'SUBARU', $_POST['marque']) ?>
                                    <?= createOption('SUZUKI', 'SUZUKI', $_POST['marque']) ?>
                                    <?= createOption('TESLA', 'TESLA', $_POST['marque']) ?>
                                    <?= createOption('TOYOTA', 'TOYOTA', $_POST['marque']) ?>
                                    <?= createOption('VOLKSWAGEN', 'VOLKSWAGEN', $_POST['marque']) ?>
                                    <?= createOption('VOLVO', 'VOLVO', $_POST['marque']) ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <select id="regions" name="regions" class="form-control input-sm">
                                    <option value="" selected>Region</option>
                                    <?= createOption('FR-ARA', 'Auvergne-Rhône-Alpes', $_POST['regions']) ?>
                                    <?= createOption('FR-BFC', 'Bourgogne-Franche-Comté', $_POST['regions']) ?>
                                    <?= createOption('FR-BRE', 'Bretagne', $_POST['regions']) ?>
                                    <?= createOption('FR-CVL', 'Centre-Val de Loire', $_POST['regions']) ?>
                                    <?= createOption('FR-GES', 'Grand Est', $_POST['regions']) ?>
                                    <?= createOption('FR-HDF', 'Hauts-de-France', $_POST['regions']) ?>
                                    <?= createOption('FR-IDF', 'Île-de-France', $_POST['regions']) ?>
                                    <?= createOption('FR-NOR', 'Normandie', $_POST['regions']) ?>
                                    <?= createOption('FR-NAQ', 'Nouvelle-Aquitaine', $_POST['regions']) ?>
                                    <?= createOption('FR-OCC', 'Occitanie', $_POST['regions']) ?>
                                    <?= createOption('FR-PDL', 'Pays de la Loire', $_POST['regions']) ?>
                                    <?= createOption('FR-PAC', 'Provence-Alpes-Côte d\'Azur', $_POST['regions']) ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control input-sm" id="carburant" name="energie">
                                    <option value="" selected>Carburant</option>
                                    <?= createOption('dies', 'Diesel', $_POST['energie']) ?>
                                    <?= createOption('ess', 'Essence', $_POST['energie']) ?>
                                    <?= createOption('elec', 'Electrique', $_POST['energie']) ?>
                                    <?= createOption('hyb', 'Hybrides', $_POST['energie']) ?>
                                    <?= createOption('gpl', 'Bicarburation essence / gpl', $_POST['energie']) ?>
                                    <?= createOption('eth', 'Bicarburation essence bioéthanol', $_POST['energie']) ?>
                                    <?= createOption('alt', 'Autres énergies alternatives', $_POST['energie']) ?>
                                </select>
                            </div>

                            <div class="form-group padding-none col-md-12">
                                <div class="col-lg-6 padding-none-sm padding-0-10-0-0">
                                    <div class="input-group">
                                        <input placeholder="Prix minimum"
                                               type="number" id="priceMin"
                                               name="priceMin" min="0"
                                               step="100"
                                               class="form-control input-sm"
                                               value="<?= $_POST['priceMin'] ?>">
                                        <span class="input-group-addon">€</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 padding-0-0-0-10 padding-none-sm">
                                    <div class="input-group">
                                        <input placeholder="Prix maximum"
                                               type="number"
                                               id="priceMax"
                                               name="priceMax"
                                               min="0" step="100"
                                               class="form-control input-sm"
                                               value="<?= $_POST['priceMax'] ?>">
                                        <span class="input-group-addon">€</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group padding-none col-md-12">
                                <div class="col-lg-6 padding-none-sm  padding-0-10-0-0">
                                    <div class="input-group">
                                        <input placeholder="Km minimum"
                                               type="number"
                                               id="mileageMin"
                                               name="mileageMin"
                                               step="1000"
                                               min="0"
                                               class="form-control input-sm"
                                               value="<?= $_POST['mileageMin'] ?>">
                                        <span class="input-group-addon">Km</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 padding-none-sm  padding-0-0-0-10">
                                    <div class="input-group">
                                        <input placeholder="Km maximum"
                                               type="number"
                                               name="mileageMax"
                                               id="mileageMax"
                                               step="1000"
                                               min="0"
                                               class="form-control input-sm"
                                               value="<?= $_POST['mileageMax'] ?>">
                                        <span class="input-group-addon">Km</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group padding-none col-md-12">
                                <div class="col-lg-6 padding-none-sm  padding-0-10-0-0">
                                    <select id="yearMin" name="yearMin" class="form-control input-sm">
                                        <option value="" selected>Année minimum</option>
                                        <?php
                                        for ($i = 0; $i < 21; $i++) {
                                            $selected = '';
                                            if ((date('Y') - $i) == $_POST['yearMin'])
                                                $selected = 'selected';
                                            echo '<option value="' . (date('Y') - $i) . '" ' . $selected . '>' . (date('Y') - $i) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 padding-none-sm  padding-0-0-0-10">
                                    <select id="yearMax" name="yearMax" class="form-control input-sm">
                                        <option value="" selected>Année maximum</option>
                                        <?php
                                        for ($i = 0; $i < 21; $i++) {
                                            $selected = '';
                                            if ((date('Y') - $i) == $_POST['yearMax'])
                                                $selected = 'selected';
                                            echo '<option value="' . (date('Y') - $i) . '" ' . $selected . '>' . (date('Y') - $i) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group padding-none col-md-12">
                                <div class="col-lg-4 padding-none-sm  padding-0-10-0-0">
                                    <select id="consumptionMax" name="consumptionMax" class="form-control input-sm">
                                        <option value="" selected>Consommation maximum</option>
                                        <?= createOption('4', '4L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('5', '5L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('6', '6L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('7', '7L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('8', '8L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('9', '9L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('10', '10L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('12', '12L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('15', '15L/100km', $_POST['consumptionMax']) ?>
                                        <?= createOption('17', '17L/100km', $_POST['consumptionMax']) ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 padding-none-sm  padding-0-10-0-0">
                                    <select id="firstHand" name="firstHand" class="form-control input-sm">
                                        <option value="" selected>Première main</option>
                                        <?= createOption('1', 'true', $_POST['firstHand']) ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 padding-none-sm  padding-0-0-0-10">
                                    <select class="form-control input-sm" id="gearbox" name="gearbox">
                                        <option value="" selected>Boite de vitesse</option>
                                        <?= createOption('MANUAL', 'Manuel', $_POST['transmission']) ?>
                                        <?= createOption('AUTO', 'Automatique', $_POST['transmission']) ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group padding-none col-md-12">
                                <div class="col-lg-6 padding-none-sm  padding-0-10-0-0">
                                    <select id="ratedHorsePowerMin" name="ratedHorsePowerMin" class="form-control input-sm">
                                        <option value="" selected>Puissance fiscal minimum</option>
                                        <?= createOption('4', '4 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('5', '5 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('6', '6 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('7', '7 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('8', '8 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('9', '9 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('10', '10 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('12', '12 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('15', '15 CV', $_POST['ratedHorsePowerMin']) ?>
                                        <?= createOption('20', '20 CV', $_POST['ratedHorsePowerMin']) ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 padding-none-sm  padding-0-0-0-10">
                                    <select id="ratedHorsePowerMax" name="ratedHorsePowerMax" class="form-control input-sm">
                                        <option value="" selected>Puissance fiscal maximum</option>
                                        <?= createOption('4', '4 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('5', '5 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('6', '6 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('7', '7 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('8', '8 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('9', '9 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('10', '10 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('12', '12 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('15', '15 CV', $_POST['ratedHorsePowerMax']) ?>
                                        <?= createOption('20', '20 CV', $_POST['ratedHorsePowerMax']) ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group padding-none col-md-12">
                                <div class="col-lg-6 padding-none-sm  padding-0-10-0-0">
                                    <select id="powerDINMin" name="powerDINMin" class="form-control input-sm">
                                        <option value="" selected>Puissance din minimum</option>
                                        <?= createOption('80', '80 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('90', '90 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('100', '100 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('110', '110 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('120', '120 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('130', '130 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('140', '140 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('150', '150 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('160', '160 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('170', '170 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('180', '180 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('190', '190 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('200', '200 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('220', '220 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('250', '250 ch', $_POST['powerDINMin']) ?>
                                        <?= createOption('-1', '+ de 250 ch', $_POST['powerDINMin']) ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 padding-none-sm  padding-0-0-0-10">
                                    <select id="powerDINMax" name="powerDINMax" class="form-control input-sm">
                                        <option value="" selected>Puissance din maximum</option>
                                        <?= createOption('80', '80 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('90', '90 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('100', '100 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('110', '110 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('120', '120 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('130', '130 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('140', '140 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('150', '150 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('160', '160 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('170', '170 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('180', '180 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('190', '190 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('200', '200 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('220', '220 ch', $_POST['powerDINMax']) ?>
                                        <?= createOption('250', '250 ch', $_POST['powerDINMax']) ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group padding-none col-md-12">
                                <select class="form-control input-sm" id="externalColors" name="externalColors">
                                    <option value="" selected>Couleur</option>
                                    <?= createOption('Argent', 'Argent', $_POST['externalColors']) ?>
                                    <?= createOption('Beige', 'Beige', $_POST['externalColors']) ?>
                                    <?= createOption('Blanc', 'Blanc', $_POST['externalColors']) ?>
                                    <?= createOption('Bleu', 'Bleu', $_POST['externalColors']) ?>
                                    <?= createOption('Bordeaux', 'Bordeaux', $_POST['externalColors']) ?>
                                    <?= createOption('Gris', 'Gris', $_POST['externalColors']) ?>
                                    <?= createOption('Ivoire', 'Ivoire', $_POST['externalColors']) ?>
                                    <?= createOption('Jaune', 'Jaune', $_POST['externalColors']) ?>
                                    <?= createOption('Marron', 'Marron', $_POST['externalColors']) ?>
                                    <?= createOption('Noir', 'Noir', $_POST['externalColors']) ?>
                                    <?= createOption('Or', 'Or', $_POST['externalColors']) ?>
                                    <?= createOption('Orange', 'Orange', $_POST['externalColors']) ?>
                                    <?= createOption('Rose', 'Rose', $_POST['externalColors']) ?>
                                    <?= createOption('Rouge', 'Rouge', $_POST['externalColors']) ?>
                                    <?= createOption('Vert', 'Vert', $_POST['externalColors']) ?>
                                    <?= createOption('Violet', 'Violet', $_POST['externalColors']) ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="form-control input-sm" id="doors" name="doors">
                                    <option value="" selected>Nombre de porte</option>
                                    <?= createOption('=2', '2', $_POST['doors']) ?>
                                    <?= createOption('=3', '3', $_POST['doors']) ?>
                                    <?= createOption('=4', '4', $_POST['doors']) ?>
                                    <?= createOption('=5', '5', $_POST['doors']) ?>
                                    <?= createOption('>=6', '6', $_POST['doors']) ?>
                                </select>
                            </div>

                            <!--div class="form-group">
                                <label for="length">Longueur maximum:</label>
                                <input type="number" id="length" name="length" class="form-control input-sm"  value="<?= $_POST['length'] ?>">
                            </div-->

                            <br/>
                            <button type="submit" class="btn btn-danger"> Valider </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if (empty($_POST['categories'])) {
            unset($_POST['categories']);
        }

        if (isset($aResult) && !empty($aResult)) {
            ?>
            <div id="main-div">
                <div class="container">
                    <div class="row">
                        <div class="page-header" id="result">
                            <h3>Résultats <small><?= $iTotalResult ?> modèles de véhicules trouvés</small></h3>
                        </div>
                        <?php
                        if ($iNumberPages == 30) {
                            ?>
                            <div class="alert alert-warning" role="alert">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                Votre recherche semble trop large, nous vous conseillons d'affiner votre recherche
                            </div>
                            <?php
                        }
                        ?>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <?php
                            $i = 0;
                            foreach ($aResult as $sBrand => $aInfo) {
                                $i++
                                ?>
                                <div class="panel panel-primary">
                                    <div class="panel-heading" role="tab" id="heading<?= $i ?>">
                                        <h4 class="panel-title marque-panel cursor-pointer">
                                            <span class="badge pull-right"><?= array_sum($aInfo) ?></span>
                                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $i ?>" aria-expanded="false" aria-controls="collapse<?= $i ?>">
                                                <?= $sBrand ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse<?= $i ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?= $i ?>">
                                        <div class="panel-body">
                                            <ul class="list-group">
                                                <?php
                                                foreach ($aInfo as $sModel => $sNumber) {
                                                    $uriParams = $aParams;
                                                    $uriParams['makesModelsCommercialNames'] .= ":$sModel";
                                                    $sModel = explode(':', $sModel)[1];
                                                    ?>
                                                    <li class="list-group-item">
                                                        <span class="badge"><?= $sNumber ?></span>
                                                        <a href="<?= getDomain() . getUri($uriParams) ?>" target="_blank" rel="noreferrer"><?= $sModel ?></a>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="alert alert-info" role="alert">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            Recherche original de vos critères <a href="<?= getDomain() . getUri($aParams) ?>" target="_blank" rel="noreferrer">ici</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } elseif (!empty($_POST)) {
            ?>
            <div class="container" id="result">
                <div class="row">
                    <div class="alert alert-warning" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        Aucun résultat pour votre recherche
                    </div>
                    <div class="alert alert-info" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        Recherche original de vos critères <a href="<?= getDomain() . getUri($aParams) ?>" target="_blank" rel="noreferrer">ici</a>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="list-inline">
                            <li>
                                <a href="#"><?= ucfirst($aTrad['home']) ?></a>
                            </li>
                            <li class="footer-menu-divider">&sdot;</li>
                            <li>
                                <a href="#services"><?= ucfirst($aTrad['guide']) ?></a>
                            </li>
                            <li class="footer-menu-divider">&sdot;</li>
                            <li>
                                <a href="#about"><?= ucfirst($aTrad['about']) ?></a>
                            </li>
                            <li class="footer-menu-divider">&sdot;</li>
                            <li>
                                <a href="#contact"><?= ucfirst($aTrad['contact']) ?></a>
                            </li>
                        </ul>
                        <p class="copyright text-muted small">Copyright &copy; <?= $sSiteName ?> <?= date('Y') ?>. All Rights Reserved powered with <a style="color: #c9302c;" target="_blank" rel="noreferrer" href="//www.lacentrale.fr">lacentrale</a></p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- jQuery -->
        <script src="js/jquery.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                // show waiting while search
                $('form').submit(function () {
                    $('#overlay').show();
                    $('#overlay-wait').show();

                    checkProgressBar();
                });

                var marquePanelClick = false;
                $(document).on('click', '.marque-panel', function () {
                    if (marquePanelClick == false) {
                        marquePanelClick = true;
                        $(this).find('a').click();
                    }
                    else
                        marquePanelClick = false;
                });
            });

            function checkProgressBar() {
                setTimeout(function () {
                    $.ajax({
                        url: "checkProgressBar.php?sessid=<?= session_id() ?>",
                        success: function (msg) {
                            if (parseInt(msg) === 0)
                                msg = 0;

                            $('#progressBarWaiting').css({'width': msg + '%'});
                            $('.percentProgressBar').html(msg + '%');

                            if (msg < 100)
                                checkProgressBar();
                        }
                    });
                }, 1000);
            }
        </script>
    </body>

</html>
