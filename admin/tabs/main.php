<h3><?php _e('Übersicht aller Spenden','prbreakfast'); ?></h3>
<form method="post" action="">
  <input type="hidden" name="update_settings" />
  <?php wp_nonce_field( 'update_settings', 'prb_nonce_check' ); ?>



  <p>Hier kommt eventuell Spendenziel?<br />
  Kreisgrafik, wieviel errreicht wurde.<br />
  Pro Monat?</p>
  <center>
    <div class="circle" id="circle">
      <strong>100<i>%</i></strong>
      <span>Aktueller <br> Monat</span>
    </div>
    <div class="circle" id="circle">
      <strong>100<i>%</i></strong>
      <span>Spenden- <br />Ziel</span>
    </div>
  </center>

  <table class="wp-list-table widefat fixed striped posts">
    <thead>
      <tr>
        <th scope="col">Organisator</th>
        <th scope="col">Spende</th>
        <th scope="col">Ort</th>
        <th scope="col">E-Mail</th>
      </tr>
    </thead>
    <tbody>


      <?php

      $donations = $this->options['prb_donations'];
      if(!empty($donations)) {
          foreach($donations as $donation) {
            ?>
            <tr>
              <td><?php echo $donation['orga']?></td>
              <td><?php echo $donation['value']?></td>
              <td><?php echo $donation['city']?></td>
              <td><?php echo $donation['mail']?></td>
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
      </tr>
    </tfoot>
  </table>
</form>
