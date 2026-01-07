<?php
    $page_url = preg_replace('#^https?://#', 'www.', $page_url);
?>

<div class="InvitationContainer">

    <div id="inviteCard" class="invitationQrcode container invTemplate-4">

        <div class="col col-1" style="background-image:url('<?php echo esc_url( $bg_url ); ?>');" >

        </div>

        <div class="col col-2">
            <div class="topHeading">
                <h2><?php echo $groom_name; ?></h2>
                <h3>and</h3>
                <h2><?php echo $bride_name; ?></h2>
                <h5 class="weddingDate"><?php echo esc_html( $wedding_date ); ?></h5>
                <p>JOYFULL INVITE YOU TO THE CELEBRATION OF THEIR MARRIEAGE.</p>
            </div>
            <div class="qrCodeSection">
                <div class="col1"><img src="<?php echo esc_attr( $dataUri ); ?>" alt="QR Code" /></div>
                <div class="col2"><p>Another For more details, please scan the QR code and RSVP online with love by <strong><?php echo esc_html($rsvp_deadline)?></strong>.</p></div>
            </div>
            <div class="bottomSection">
                <p class="invitationUrl">WWW.BESTWISHES.COM/SERENITY</p>
             </div>
        </div>

    </div>

    <div class="overlayDownloadInvitation">
        <button id="downloadInviteBtn" class="btnInvitation">Download Your Invitation <img src="https://aswecelebrate.com/wp-content/uploads/2025/09/file-1.png"/></button>
    </div>
</div>



<style>

.InvitationContainer {
    position: relative;
    display: flex;
    justify-content: center;
}


.invTemplate-4 {
    display: flex;
    flex-direction: row;
    height: 600px;
    width: 100%;
    max-width: 600px;
    border: 1px solid #d3d3d3;
}

.invTemplate-4  .col {
    height: 100%;
    flex: 1 1 0%;
}

.invTemplate-4 .col-1 {
    background-size: cover;
    background-position: center;
}


.invTemplate-4 .col-2 {
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #fff;
}

.topHeading {
    text-align: center;
    margin-bottom: 10px;
}

.qrCodeSection {
    margin: 20px 0px;
    display: flex;
    align-items: center;
}

.qrCodeSection .col1 {
    flex: 0 0 40%;
}


.qrCodeSection p {
    font-size: 14px;
    text-align: left;
    line-height: 1.5em;
    font-family: 'futura';
    letter-spacing: 1px;
}

.qrCodeSection, .bottomSection {
    text-align: center;
}

.topHeading h2 {
    font-family: 'ArnoPro';
    font-size: 35px;
    margin: 0px;
    text-transform: uppercase;
    letter-spacing: 0.3rem;
}

.topHeading h3 {
    font-family: 'WhisperingSignature';
    font-size: 2rem;
    margin: 0px 0px -10px 0px;
}

.topHeading p {
    font-family: 'Futura';
    letter-spacing: 2px;
    font-style: italic;
    font-size: 13px;
    text-wrap-style: pretty;
}

.topHeading h5 {
    font-family: 'Futura';
    font-size: 25px;
    margin: 20px 0px;
}

.overlayDownloadInvitation {
  display: none;
  justify-content: center; /* Center horizontally */
  align-items: center; /* Center vertically */
  position: absolute; /* To make the overlay cover the whole viewport */
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.5);
}

.invitationUrl {
    font-family: 'Futura';
    letter-spacing:1px;
}

.btnInvitation {
    padding: 15px 20px 15px 30px;
    font-size: 18px;
    background-color: #333;
    color: white;
    border: none;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btnInvitation img {
  width: 30px;
  height: 30px;
}


.InvitationContainer:hover .overlayDownloadInvitation {
    display:flex;
}

</style>
