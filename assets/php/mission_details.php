<?php
require_once (__DIR__ . '/../templates/front_header.html');
?>
<main>
    <h1 class="text-center  mt-4">Détails de la mission</h1>
    <?php
    if (!isset ($_GET['missionId'])) {
        echo 'Veuillez cliquer sur une mission svp';
    } else {
        require_once (__DIR__.'/bdd_connexion.php');
        $statement = $pdo->prepare('SELECT * FROM missions WHERE id LIKE :id');
        $statement->bindParam('id', $_GET['missionId'], PDO::PARAM_INT);

        setlocale(LC_TIME, 'fr_FR', 'French');
        function dateToFrench($date, $format) :string
        {
            $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
            $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
            $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
            return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
        }

        if ($statement->execute()) {
            while ($mission = $statement->fetch()) {
                $missionArray = json_decode(json_encode($mission), true);

                $statusId = $missionArray['status'];
                $statementTwo = $pdo->prepare('SELECT * FROM mission_status WHERE id LIKE ?');
                $statementTwo->bindParam(1, $statusId, PDO::PARAM_INT);

                if ($statementTwo->execute()){
                    $statusName = $statementTwo->fetch();

                    $countryId = $missionArray['country'];
                    $statementThree = $pdo->prepare('SELECT * FROM countries WHERE id LIKE ?');
                    $statementThree->bindParam(1, $countryId, PDO::PARAM_INT);

                    if ($statementThree->execute()){
                        $countryName = $statementThree->fetch();

                        $specialityId = $missionArray['required_speciality'];
                        $statementFour = $pdo->prepare('SELECT * FROM specialities WHERE id LIKE ?');
                        $statementFour->bindParam(1, $specialityId, PDO::PARAM_INT);

                        if($statementFour->execute()){
                            $specialityName = $statementFour->fetch();

                            echo '
                            <div class="container-fluid">
                                <h3>Nom de la mission : '.$missionArray['title'].'</h3>
                                <div>
                                    <p class="mt-3">Nom de code : '.$missionArray['code_name'].'</p>
                                    <p>Pays d\'intervention : '.$countryName['french_name'].'</p>
                                    <p>Statut de la mission : '.$statusName['name'].'</p>
                                    <p>Spécialité requise : '.$specialityName['name'].'</p>
                                    <p>Date de début : '.dateToFrench($missionArray['start_date'],'l d F o').'</p>
                                    <p>Date de fin : '.dateToFrench($missionArray['end_date'], 'l d F o').'</p>
                                    <p>
                                    Description : <br>
                                    '.$missionArray['description'].'
                                    </p>
                                </div>
                            </div>
                            ';
                        }
                    }
                }
            }
        }
    }
    ?>
</main>
<?php
require_once (__DIR__ . '/../templates/front_footer.html');
?>