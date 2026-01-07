<?php

$page_url = preg_replace('#^https?://#', 'www.', $page_url);


?>

<div id="inviteCard" class="invitationQrcode container invTemplate-3">

    <img class="overlayLeaves leave-1" src="https://localhost/bestwishes/wp-content/uploads/2025/09/leaves.png"/>

    <div class="col-1" style="background-image:url('<?php echo esc_url( $bg_url ); ?>');" ></div>

    <div class="col-2">
        <div class="topHeading">
            <h3>OUR</h3>
            <h5>wedding</h5>
            <h3>WEBSITE</h3>
        </div>
        <div class="qrCodeSection">
            <img src="<?php echo esc_attr( $dataUri ); ?>" alt="QR Code" />
        </div>
        <div class="bottomSection">
            <p>For more information on our wedding & to rsvp online. please scan this QR CODE to visit our website.</p>
            <p class="invitationUrl"><?php echo $page_url; ?></p>
        </div>
    </div>

    <img class="overlayLeaves leave-2" src="https://localhost/bestwishes/wp-content/uploads/2025/09/leaves.png"/>


</div>



<style>
.invTemplate-3 {
    background-image: url(https://localhost/bestwishes/wp-content/uploads/2025/09/TILE.jpg);
    height: 850px;
    width: 850px;
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    position:relative;
    overflow:hidden;
}

.invTemplate-3 .col-1 {
    background-size: cover;
    background-position: center;
    height: 500px;
    width: 370px;
    border-radius: 20px;
    position: relative;
    top: -50px;
    left: 30px;
    transform: rotate(-5deg);
    box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.5);
}

.invTemplate-3 .col-2 {
    background-color: #fff;
    height: 500px;
    width: 370px;
    border-radius: 20px;
    padding: 30px;
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    transform: rotate(5deg);
    position: relative;
    top: 50px;
    left: -30px;
    text-align: center;
    box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.2), inset -1px -1px 1px rgba(0, 0, 0, 0.2);
}

.invTemplate-3 img {
    width: 200px;
}

.invTemplate-3 .topHeading {
    display: flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
}

.invTemplate-3 p.invitationUrl {
    text-transform: uppercase;
}

img.overlayLeaves {
    position: absolute;
}

img.overlayLeaves.leave-1 {
    top: -10px;
    right: -50px;
    width: 300px;
}

img.overlayLeaves.leave-2 {
    bottom: -30px;
    left: -20px;
    transform: rotate(170deg);
    width: 300px;
}
</style>