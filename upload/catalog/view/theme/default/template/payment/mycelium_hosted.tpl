<?php 

echo $header;
echo $content_top;
echo $column_left;
?>

<div class="container container-fluid" style="font-family: 'Roboto', 'Lucida Sans Unicode', Helvetica, Arial, Verdana, sans-serif; color:#21202c">
  <div class="col-md-12" style="background:#e7ecee; padding:20px; border-radius:5px">
    <div class="row">
      <div class="col-sm-2"></div>
      <div class="col-sm-2 text-center">
          <i id="expired_sign" class="fa fa-exclamation-triangle" style="font-size:15em; color:#a5b8c0; display:none" aria-hidden="true"></i>
          <i id="success_check" class="fa fa-check-square-o" style="font-size:15em; color:#a5b8c0; display:none" aria-hidden="true"></i>
          <a href="bitcoin:<?php echo $address; ?>?amount=<?php echo $bitcoin_amount; ?>" ><img id="qr-code" class="img-responsive" style="margin:auto" src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=bitcoin:<?php echo $address; ?>?amount=<?php echo $bitcoin_amount; ?>" style="border-radius:5px"/></a>
          <p class="text-center"><small>(click or tap to open in wallet)</small></p>
      </div>
      <div class="col-sm-6">
        <h5><?php echo date('d M Y, h:i', time()); ?> <?php if($expiration_time !== null){ ?>- Time remaining: ~ <span id="time_minutes"><?php echo intval($expiration_time / 60); ?></span>:<span id="time_seconds"><?php echo $expiration_time % 60; ?></span><?php } ?></h5>
        <p id="send_text"
           class="text-center"
           style="font-size: 1.5em;
                  line-height: 1.6em;
                  display: inline;
                  margin-bottom: 10px;
                  zoom: 1;
                  letter-spacing: normal;
                  word-spacing: normal;
                  vertical-align: top;
                  text-rendering: auto;">
          <?php echo $please_send; ?> <b style="font-weight:300; font-size:1.5em; padding:3px 3px 3px 3px; line-height: 26px "><?php echo $bitcoin_amount; ?> BTC</b><br/> <?php echo $to_address; ?> <br/>
          <span style="background:#fff; padding:5px; color:#888; font-size:0.9em"><?php echo $address; ?></span> <br/>
        </p>
        <p style="font-size: 1.5em;
                  line-height: 1.6em;
                  display: inline;
                  margin-bottom: 10px;
                  zoom: 1;
                  letter-spacing: normal;
                  word-spacing: normal;
                  vertical-align: top;
                  text-rendering: auto;
                  display:none" id="success_text">
          <?php echo $success_text; ?>
        </p>
        <p style="font-size: 1.5em;
                  line-height: 1.6em;
                  display: inline;
                  margin-bottom: 10px;
                  zoom: 1;
                  letter-spacing: normal;
                  word-spacing: normal;
                  vertical-align: top;
                  text-rendering: auto;
                  display:none" id="expired_text">
          <?php echo $expired_text; ?>
        </p>
        
      </div>
    </div>
    <div class="row">
      <div class="col-sm-2"></div>
      <div class="col-sm-8">
        <?php if($shifty_enabled){ ?>
        <div style="width:217px; margin:auto" id="shapeshift_container">
          <script>function shapeshift_click(a,e){e.preventDefault();var link=a.href;window.open(link,'1418115287605','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=0,left=0,top=0');return false;}</script> <a onclick="shapeshift_click(this, event);" href="https://shapeshift.io/shifty.html?destination=<?php echo $address; ?>&amp;output=BTC&amp;apiKey=563a6bdf5ba5881546cfe78a6bc68df1e718f06ab791ed4fa4fd19c1470c06d35b1230ede614f37977601ca965274906325102986a5c0d04938024e68809dc3b&amp;amount=<?php echo $bitcoin_amount; ?>"</a><img src="https://shapeshift.io/images/shifty/small_dark_altcoins.png" class="ss-button"></a>
        </div>
        <?php } ?>
        <div class="col-xs-12 text-center" style="display:none" id="return_button">
          <button class="btn btn-primary" onclick="window.location.href='<?php echo $return_url; ?>'"><?php echo $back_button; ?></button>
        </div>
        <div class="col-xs-12 text-center" style="display:none" id="expired_button">
          <button class="btn btn-primary" onclick="window.location.href='<?php echo $cancel_url; ?>'"><?php echo $back_button; ?></button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    
    socket();
    tick();
    function socket()
    {
      // connect to websocket
      var conn = new WebSocket('wss://gateway.gear.mycelium.com/gateways/<?php echo $gateway_id; ?>/orders/<?php echo $payment_id; ?>/websocket');
      
      
      conn.onclose = function() {
        setTimeout(socket, 2000);
      }
      
      conn.onmessage = function(e) {
        console.log(e);
        var data = $.parseJSON(e.data);
        
        if(
          data.status == 1 ||
          data.status == 2 || 
          data.status == 4
        )
        {
          $('#qr-code').fadeOut();
          $('#send_text').fadeOut();
          $('#shapeshift_container').fadeOut();
          
          $('#success_check').fadeIn();
          $('#success_text').fadeIn();
          $('#return_button').fadeIn();
        }
        else if(data.status == 5 || data.status == 6)
        {
          $('#qr-code').fadeOut();
          $('#send_text').fadeOut();
          $('#shapeshift_container').fadeOut();
          
          $('#expired_sign').fadeIn();
          $('#expired_text').fadeIn();
          $('#expired_button').fadeIn();
        }
      };
      
    }
    
    function formatTime(val)
    {
      if(val <= 9)
      {
        val = '0'+val;
      }
      
      return val;
    }
    
    function tick()
    {
      var minutes = parseInt($('#time_minutes').html());
      var seconds = parseInt($('#time_seconds').html());
      
      seconds--;
      if(seconds == 0 && minutes == 0)
      {
        $('#time_minutes').html(formatTime(minutes));
        $('#time_seconds').html(formatTime(seconds));
        return;
      } 
      
      if(seconds < 0)
      {
        seconds = 59;
        minutes--;
      }
      
      $('#time_minutes').html(formatTime(minutes));
      $('#time_seconds').html(formatTime(seconds));
      setTimeout(tick, 1000);
    }
    
  });
</script>


<?php

echo $column_right;
echo $content_bottom;
echo $footer;

?>