<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
    $page_url = preg_replace('#^https?://#', 'www.', $page_url);

?>

<div class="momentCaptureContainer">

    <div id="inviteMomentCapturedCard" class="invitationQrcode container invTemplate-5">

        <div class="row row-1">
            <div class="momentCapturedtopHeading">
                <h2>Capture</h2>
                <h3>The love</h3>
            </div>
        </div>
        <div class="row row-2">
            <div class="qrMomentCapturedCodeSection col-1">
                <img src="<?php echo esc_attr( $dataUri ); ?>" alt="QR Code" />
            </div>
            <div class="col-2">
                <p>We want to see our day through your eyes!</p>
            </div>
        </div>
        <div class="row row-3">
            <div class="bottomSection">
                <p>Upload your photos to our online album so we don't miss out of any of the fun</p>
            </div>
            <div class="groomandbride">
                <p><?php echo $groom_name; ?></p>
                <img src="https://aswecelebrate.com/wp-content/uploads/2025/10/heart-sketch.png">
                <p><?php echo $bride_name; ?></p>
            </div>
        </div>


    </div>

    <img src="" alt="" class="inviteMomentCapturedCardResult">

    <div class="overlayDownloadInvitation">
        <button id="downloadCapturedBtn" class="btnInvitation">Download Your Invitation <img src="https://aswecelebrate.com/wp-content/uploads/2025/09/file-1.png"/></button>
    </div>



</div>

<style>


.momentCaptureContainer {
    position: relative!important;
}

.momentCapturedtopHeading h2 {
    font-family: 'WhisperingSignature', cursive;
    font-size: 3.2rem
}

.momentCapturedtopHeading h3 {
    font-family: 'Futura';
    font-size: 3rem;
}


#inviteMomentCapturedCard {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 600px;
    width: 70%;
    max-width: 450px;
    margin: 0 auto;
    background: #fff;
    padding: 50px;
    border: 1px solid #D3D3D3;
}

.momentCapturedtopHeading {
    text-align: center;
}


#inviteMomentCapturedCard .row-2 {
    display: flex;
    flex-direction: row;
    gap: 10px;
    align-items: flex-start;
}

/* 40% width column */
.row-2 .col-1 {
  flex: 0 0 40%;
}

/* 60% width column */
.row-2 .col-2 {
  flex: 0 0 60%;
}

.row-2 .col-2 p {
    font-family: 'Futura';
    text-transform: uppercase;
    font-size: 1.4rem;
    text-align: left;
    line-height: 1.5em;
    letter-spacing: 5px;
}


.qrMomentCapturedCodeSection {
    text-align: center;
}



#inviteMomentCapturedCard .bottomSection p {
    font-family: 'Futura';
    text-transform: none;
    font-size: 16px;
    font-style: normal;
    font-weight: 500;
    word-spacing: 1px;
    letter-spacing: 0.5px;
}


.groomandbride {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0px 20px;
}


.groomandbride p {
    font-family: 'QuickLetter';
    text-align: center;
    font-size: 2.2rem;
    margin-bottom: 0px;
    line-height: 0.8em;
}

.groomandbride img {
    width: 30px;
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


.momentCaptureContainer:hover .overlayDownloadInvitation {
    display:flex;
}



</style>