<h3><?php _e('Übersicht aller Spenden','prbreakfast'); ?></h3>
<form method="post" action="">
  <input type="hidden" name="update_settings" />
  <?php wp_nonce_field( 'update_settings', 'prb_nonce_check' ); ?>


  <?php

  $donations = $this->options['prb_donations'];



  // Nach dem Speichern nimm die Post Variablen
  if(!empty($_POST)) {
    $donations = $_POST['prb_donations'];
    $donations = array_reverse($donations);
  }

  $donationMonth = date('M Y');

  $monthDonations = array();
  $donationAmount = array();


  if(!empty($donations)) {
    foreach($donations as $donation) {
      $monthDonations[$donation['month']] += $donation['value'];
      $donationAmount[$donation['month']] += 1;
    }
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




  ?>
  <p>Hier kommt eventuell Spendenziel?<br />
  Kreisgrafik, wieviel errreicht wurde.<br />
  Pro Monat?</p>


  <center>
    <?php
    if(!empty($monthDonations)) {

      // Letzte Donations immer als erstes
      $monthDonationsReverse = array_reverse($monthDonations);

      $i = 0;
      foreach($monthDonationsReverse as $monthName => $val) {

        // Prozent ausrechnen aus dem Durchschnittswert
        //$percentage = round($val * 100 / $gesamtDurchschnitt);
        $percentage = round($durchschnittsValue[$monthName] * 100 / $gesamtDurchschnitt);

        if($percentage > 100) {
          $percentage = 100;
        }
        $percentage = $percentage / 100;

        ?>
          <div data-value="<?php echo $percentage;?>" data-fill="{&quot;color&quot;: &quot;#EA4E92&quot;}" data-amount="<?php echo $val;?>" class="circle">
            <strong></strong>
            <span>Monat <br><?php echo $monthName;?></span>
          </div>
        <?php
        if($i > 5) {
          break;
        }
        $i++;
      }
    }

    ?>
    <p>
      In jedem Monat werden im Durchschnitt <?php echo $gesamtDurchschnitt;?>€ Spenden gesammelt.
    </p>
    <p>
      Ein Durschnittswert der Spenden wird in jedem Monat neu berechnet.
      In den ersten Monaten können die Statistiken vorerst abweichen.
    </p>
  </center>


  <table id="donationTableMain" class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
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

        // Letzte Donations immer als erstes
        $donationReverse = array_reverse($donations);

          foreach($donationReverse as $key => $donation) {
            ?>
            <tr>
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
        <th scope="col">Organisator</th>
        <th scope="col">Spende</th>
        <th scope="col">Ort</th>
        <th scope="col">E-Mail</th>
        <th scope="col">Monat/Jahr</th>
        <th scope="col"> </th>
      </tr>
    </tfoot>
  </table>
  <p class="submit editDonationSaveButton">
  	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Änderungen speichern','prbreakfast'); ?>"  />
  </p>
</form>
