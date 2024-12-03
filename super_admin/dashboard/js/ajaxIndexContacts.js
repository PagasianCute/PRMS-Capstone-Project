document.addEventListener("DOMContentLoaded", function() {

    const contactList = document.querySelector(".yourDivClass .contacts");

    
    var channel = pusher.subscribe(fclt_id);
    console.log('Heyy: ' + fclt_id);
    channel.bind('message', function(data) {
        console.log('Received message: ' + data);
        loadMessages();
        loadContacts();
    });


    loadContacts();

    function loadContacts() {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "server/index_contacts.php", true);
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    let data = xhr.response;
                    contactList.innerHTML = data;
                }
            }
        };
        xhr.send();
    }
    
});
