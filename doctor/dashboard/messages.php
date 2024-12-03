<?php
include_once 'header.php';
include_once 'includes/messages_functions.inc.php';
$fclt_id = $_SESSION['fcltid'];
?>


<link rel="stylesheet" href="css/chat.css?v=<?php echo time();?>">

<div class="messages-feed">
    <div class="containers row gx-0">
        <div class="contacts p-4 col-lg-4 col-md-5 col-sm-6">
        <h2>Contacts</h2>
        <div class="col-12">
            <div class="input-group mb-3">
                <input type="text" id="search-input" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="button-search">
                <button class="btn btn-primary" type="button" id="button-search"><i class="fi fi-rr-search"></i></button>
            </div>
        </div>
        <div class="contacts-list">
                <!-- All Contacts (Facilities) Will Display Here -->
        </div> 
        </div>  
        <div class="messages col-lg-8 col-md-7 col-sm-6" id="messages">
            <nav class="messages-header">
                
            </nav>
        <div id="message-container" class="message-container">
                <!-- All Messages Will Display Here -->
                    <div class="receiver" id="sender-messages">
                        <div class = "users-head-logo shadow" data-toggle="tooltip" data-placement="top" title="Jezmahboi">
                            <img src="../../assets/doctor.jpg" alt="">
                        </div>
                        <div class="message-content shadow" data-toggle="tooltip" data-placement="top" title="10:45 PM">
                            <p>Hello Jezmahboi</p>
                        </div>
                    </div>
        </div>
            <div class="input-container">
            <form action="#" id="message-form" class="type-area" autocomplete="off">
                <input type="hidden" value="<?php echo $users_id = $_SESSION["usersid"]?>" id="users_id" name="users_id">
                <input type="hidden" value="<?php echo $fclt_id = $_SESSION['fcltid']?>" id="sender_id" name="sender_id">
                <input type="hidden" value="" name="receiver_id" id="receiver_id">
                <input type="text" id="message-input" class="input-field" name="message" placeholder="Type your message...">
                <button id="send-button" class="btn">Send</button>
                <button id="sending-button" class="btn d-none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending..
                </button>
            </form>
            </div>
        </div>
    </div>
</div>

<script>
    var fclt_id = "<?php echo $fclt_id ?>";
</script>

<script src="js/ajaxMessages.js"></script>
<script src="js/MessageUI.js"></script>

<?php
include_once 'footer.php';
?>
