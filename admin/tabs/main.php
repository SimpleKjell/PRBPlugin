<h3><?php _e('Übersicht aller Spenden','prbreakfast'); ?></h3>
<form method="post" action="">
  <input type="hidden" name="update_settings" />
  <?php wp_nonce_field( 'update_settings', 'prb_nonce_check' ); ?>


  <?php
  $resp = $this->getResponsiblePerson();

  $donations = $this->options['prb_donations'];
  //$donations = array_reverse($donations);


  //var_dump($donations);

  // Zeige nur die Donations aus dem Bundesland an.
  if($resp != 'Admin') {
    foreach($donations as $key => $val) {
      if($val['resp'] != $resp) {
        unset($donations[$key]);
      }
    }
  }
  //var_dump($donations);
  // Nach dem Speichern nimm die Post Variablen
  //if(!empty($_POST)) {
  //  $donations = $_POST['prb_donations'];
  //  $donations = array_reverse($donations);
  //}






  $monthDonations = array();
  $donationAmount = array();


  if(!empty($donations)) {
    foreach($donations as $donation) {
      $monthDonations[$donation['month']] += $donation['value'];
      $donationAmount[$donation['month']] += 1;
    }

    // durchschnittsValue pro Monat
    foreach($monthDonations as $month => $val) {
      $durchschnittsValue[$month] = $val / $donationAmount[$month];
    }
    $gesamtDurchschnittsValue = 0;

    // Durchschnittswert über alle Monate
    foreach($durchschnittsValue as $durchschnitt) {
      $gesamtDurchschnittsValue += $durchschnitt;
    }
    $gesamtDurchschnitt = $gesamtDurchschnittsValue / count($durchschnittsValue);

    foreach($donations as $donation) {
      $donationsPerMont[$donation['month']] = $monthDonations[$donation['month']];
    }

  }






  ?>



  <center>
    <h3 class="barGraphHeading"><span class="prevYear"><i class="fa fa-chevron-left" aria-hidden="true"></i></span> Spendenübersicht <?php echo ($resp == 'Admin') ? '' : $resp;?> <span class="year">2016</span> <span class="nextYear"><i class="fa fa-chevron-right" aria-hidden="true"></i></span></h3>
    <div class="barGraphStatistics">
      <?php

      if(!empty($donations)) {

        // Vorab sollte das Jahr getrennt werden
        $months = '';
        $dntValues = '';
        foreach($donationsPerMont as $monthDonation => $value) {

          preg_match_all('!\d+!', $monthDonation, $matches);
          $var = implode(' ', $matches[0]);

          if(!isset($year[$var])) {
              $pureMonth = trim(preg_replace('/[0-9]+/', '', $monthDonation));
              $year[$var] = array($pureMonth => $value);
          } else {
            $pureMonth = trim(preg_replace('/[0-9]+/', '', $monthDonation));
            $year[$var] = array_merge($year[$var], array($pureMonth => $value));
          }

          $months .= $monthDonation .',';
          $dntValues .= $value. ',';

        }
        $length = strlen($months);
        $months = substr($months, 0, $length -1);
        $length = strlen($dntValues);
        $dntValues = substr($dntValues, 0, $length -1);




        // Für jedes Jahr sind nun mit Monat => Value alle Daten erfasst
        // Nun aufbereiten fürs js
        $dntValues = '';
        foreach($year as $year => $val) {


        $dntValues .= (array_key_exists('Jan',$val)) ? $val['Jan'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Feb',$val)) ? $val['Feb'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Mar',$val)) ? $val['Mar'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Apr',$val)) ? $val['Apr'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('May',$val)) ? $val['May'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Jun',$val)) ? $val['Jun'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Jul',$val)) ? $val['Jul'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Aug',$val)) ? $val['Aug'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Sep',$val)) ? $val['Sep'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Oct',$val)) ? $val['Oct'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Nov',$val)) ? $val['Nov'] : '0';
        $dntValues .= ',';
        $dntValues .= (array_key_exists('Dec',$val)) ? $val['Dec'] : '0';

        ?>
        <div class="hidden" id="graphData<?php echo $year;?>" data-graphValues="<?php echo $dntValues;?>" ></div>
        <?php

        $dntValues = '';

        }
      }


      ?>
      <script type="text/javascript" src="http://www.chartjs.org/assets/Chart.js"></script>


    </div>
    <?php
    if(!empty($donations)) {
      ?>
      <div style="padding: 1cm 4cm 1cm 4cm;">
        <div class="canvas_container">
          <canvas id="canvas" width="900" height="300"></canvas>
        </div>

        <script>
          PRBAdmin.prototype.barGraph();
        </script>
      </div>
      <?php
    }
    ?>



  </center>


  <!-- data Table -->
  <!-- TODO Nach Bundesland anzeigen -->

  <table id="donationTableMain" class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
        <?php if($resp == 'Admin') {
          ?>
            <th scope="col">Bundesland <span class="headerSortDown"><i class="fa fa-arrow-up" aria-hidden="true"></i></span><span class="headerSortUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></span></th>
          <?php
        }
        ?>

        <th scope="col">Organisator <span class="headerSortDown"><i class="fa fa-arrow-up" aria-hidden="true"></i></span><span class="headerSortUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></span></th>
        <th scope="col">Spende <span class="headerSortDown"><i class="fa fa-arrow-up" aria-hidden="true"></i></span><span class="headerSortUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></span></th>
        <th scope="col">Ort <span class="headerSortDown"><i class="fa fa-arrow-up" aria-hidden="true"></i></span><span class="headerSortUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></span></th>
        <th scope="col">E-Mail <span class="headerSortDown"><i class="fa fa-arrow-up" aria-hidden="true"></i></span><span class="headerSortUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></span></th>
        <th scope="col">Monat/Jahr <span class="headerSortDown"><i class="fa fa-arrow-up" aria-hidden="true"></i></span><span class="headerSortUp"><i class="fa fa-arrow-down" aria-hidden="true"></i></span></th>
        <th scope="col"> </th>
      </tr>
    </thead>
    <tbody>


      <?php


      if(!empty($donations)) {

        $page = $_GET['paginierung'];
        $amountDonations = count($donations);

        $anzahlZuZeigenderSpenden = 9;
        $allPages = ceil($amountDonations / $anzahlZuZeigenderSpenden);





        if(empty($page)) {
          $page = 1;
        }


        if($amountDonations > $anzahlZuZeigenderSpenden) {
          $aktuellerIndex = $anzahlZuZeigenderSpenden * $page;

          $startIndexValue = $aktuellerIndex - $anzahlZuZeigenderSpenden;

          $indexNeeded = '';

          $i = 1;
          for($startIndexValue; $startIndexValue < $aktuellerIndex; $startIndexValue++) {

            $indexNeeded .=  $aktuellerIndex-$i . ',';
            $i++;
          }
          $length = strlen($indexNeeded);
          $indexNeeded = substr($indexNeeded, 0, $length-1);



          $indexNeeded = explode(',',$indexNeeded);


          foreach($donations as $key => $val) {
            if(!in_array($key,$indexNeeded)) {
              unset($donations[$key]);
            }
          }


        }


        $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
        $path = $uri_parts[0];
        ?>
        <div class="tablenav">
          <div class="tablenav-pages">
            <span class="pagination-links">
              <?php
              if($page == "1") {
                ?>
                <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <?php
              } else {
                $prevPage = $page -1;
                if($page != "2") {
                  ?>

                  <a class="first-page" href="<?php echo $path . '?page=pr-breakfast&paginierung=1'?>"><span class="screen-reader-text">Erste Seite</span><span aria-hidden="true">«</span></a>
                  <?php
                } else {
                  ?>
                  <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
                  <?php
                }
                ?>
                <a class="prev-page" href="<?php echo $path . '?page=pr-breakfast&paginierung='.$prevPage?>"><span class="screen-reader-text">Vorherige Seite</span><span aria-hidden="true">‹</span></a>
                <?php
              }
              $nextPage = $page+1;
              ?>
              <span><?php echo $page;?> von <?php echo $allPages;?></span>
              <?php
              if($page == $allPages) {
                ?>
                <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
                <?php
              } else {
                ?>
                <a class="next-page" href="<?php echo $path . '?page=pr-breakfast&paginierung='.$nextPage?>"><span class="screen-reader-text">Nächste Seite</span><span aria-hidden="true">›</span></a>
                <?php
                if($page == $allPages-1) {
                  ?>
                  <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
                  <?php
                } else {
                  ?>
                  <a class="last-page" href="<?php echo $path . '?page=pr-breakfast&paginierung='.$allPages?>"><span class="screen-reader-text">Letzte Seite</span><span aria-hidden="true">»</span></a>
                  <?php
                }
              }
              ?>
            </span>
          </div>
        </div>
        <?php


          foreach($donations as $key => $donation) {
            ?>
            <tr>
              <?php if($resp == 'Admin') {
                ?>
                <td>
                  <?php echo $donation['resp']?>
                  <span class="editInput"><input type="hidden" name="prb_donations[<?php echo $key;?>][resp]" value="<?php echo $donation['resp']; ?>" /></span>
                </td>
                <?php
              } else {
                ?>
                <input type="hidden" name="prb_donations[<?php echo $key;?>][resp]" value="<?php echo $donation['resp']; ?>" />
                <?php
              }
              ?>
              <td>
                <span class="text"><?php echo $donation['orga']?></span>
                <span class="editInput"><input type="text" name="prb_donations[<?php echo $key;?>][orga]" value="<?php echo $donation['orga']; ?>" /></span>
              </td>
              <td>
                <span class="text"><?php echo $donation['value']?></span>
                <span class="editInput"><input type="number" name="prb_donations[<?php echo $key;?>][value]" value="<?php echo $donation['value']; ?>" /></span>
              </td>
              <td>
                <span class="text"><?php echo $donation['city'];?></span>
                <span class="editInput"><input type="text" name="prb_donations[<?php echo $key;?>][city]" value="<?php echo $donation['city']; ?>" /></span>
              </td>
              <td>
                  <span class="text"><?php echo $donation['mail'];?></span>
                  <span class="editInput"><input type="email" name="prb_donations[<?php echo $key;?>][mail]" value="<?php echo $donation['mail']; ?>" /></span>
              </td>
              <td>
                <?php echo $donation['month']?>
                <span class="editInput"><input type="hidden" name="prb_donations[<?php echo $key;?>][month]" value="<?php echo $donation['month']; ?>" /></span>
              </td>
              <td>
                <a class="edit" href=""><i class="fa fa-pencil-square-o" aria-hidden="true"></i> bearbeiten</a>
                <a class="close" href=""><i class="fa fa-times" aria-hidden="true"></i> abbrechen</a>
                <?php $nonce = wp_create_nonce('delete_donation_nonce'); ?>
                <a class="delete" data-nonce="<?php echo $nonce; ?>" data-rowId="<?php echo $key;?>" href="">/ <i class="fa fa-times" aria-hidden="true"></i> löschen</a>
              </td>
            </tr>
            <?php
          }
      }

      ?>
    </tbody>



    <tfoot>
      <tr>
        <?php if($resp == 'Admin') {
          ?>
          <th scope="col">Bundesland</th>
          <?php
        }
        ?>


        <th scope="col">Organisator</th>
        <th scope="col">Spende</th>
        <th scope="col">Ort</th>
        <th scope="col">E-Mail</th>
        <th scope="col">Monat/Jahr</th>
        <th scope="col"> </th>
      </tr>
    </tfoot>
  </table>
  <?php
  if(!empty($donations)) {
    ?>
    <div class="tablenav">
      <div class="tablenav-pages">
        <span class="pagination-links">
          <?php
          if($page == "1") {
            ?>
            <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
            <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
            <?php
          } else {
            $prevPage = $page -1;
            if($page != "2") {
              ?>

              <a class="first-page" href="<?php echo $path . '?page=pr-breakfast&paginierung=1'?>"><span class="screen-reader-text">Erste Seite</span><span aria-hidden="true">«</span></a>
              <?php
            } else {
              ?>
              <span class="tablenav-pages-navspan" aria-hidden="true">«</span>
              <?php
            }
            ?>
            <a class="prev-page" href="<?php echo $path . '?page=pr-breakfast&paginierung='.$prevPage?>"><span class="screen-reader-text">Vorherige Seite</span><span aria-hidden="true">‹</span></a>
            <?php
          }
          $nextPage = $page+1;
          ?>
          <span><?php echo $page;?> von <?php echo $allPages;?></span>
          <?php
          if($page == $allPages) {
            ?>
            <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
            <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
            <?php
          } else {
            ?>
            <a class="next-page" href="<?php echo $path . '?page=pr-breakfast&paginierung='.$nextPage?>"><span class="screen-reader-text">Nächste Seite</span><span aria-hidden="true">›</span></a>
            <?php
            if($page == $allPages-1) {
              ?>
              <span class="tablenav-pages-navspan" aria-hidden="true">»</span>
              <?php
            } else {
              ?>
              <a class="last-page" href="<?php echo $path . '?page=pr-breakfast&paginierung='.$allPages?>"><span class="screen-reader-text">Letzte Seite</span><span aria-hidden="true">»</span></a>
              <?php
            }
          }
          ?>
        </span>
      </div>
    </div>
    <?php
  }
  ?>



  <p class="submit editDonationSaveButton">
  	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Änderungen speichern','prbreakfast'); ?>"  />
  </p>
</form>
