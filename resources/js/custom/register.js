// cand schimbam tipul de user, salveaza tip-ul de user intr-o casuta ascunsa a carei valoare o sa o trimitem la server printr-un POST care va fi preluat de RegisterController pe langa alte
$('.register-user-type').on('click', function(){
    $('#registerType').val($(this).attr('data-user-type'));
});
// cand user-ul da click pe butonul de registrare
$('#registerUser').on('click', function(){
    $('.form-control, .form-check-input').removeClass('border-danger');
    $('.invalid-feedback').hide();
    $('#registerUser').addClass('disabled').html('<i class="fa-solid fa-spinner fa-spin"></i> Inregistreaza-te');
    // trimite printr-un post datele colectate din formular
    // /register este o ruta definita in web.php, care acceseaza o functie intr-un controller.
    axios.post("/register", {
        registerUserType: $("#registerType").val(),
        touristUserName: $("#touristUserName").val(),
        touristFirstName: $("#touristFirstName").val(),
        touristLastName: $("#touristLastName").val(),
        touristEmail: $("#touristEmail").val(),
        touristPassword: $("#touristPassword").val(),
        touristPasswordConfirm: $("#touristPasswordConfirm").val(),
        touristAcceptTC: $("#touristAcceptTC").is(":checked"),
        hostFirstName: $("#hostFirstName").val(),
        hostLastName: $("#hostLastName").val(),
        hostPhoneNumber: $("#hostPhoneNumber").val(),
        hostWebsite: $("#hostWebsite").val(),
        hostEmail: $("#hostEmail").val(),
        hostPassword: $("#hostPassword").val(),
        hostPasswordConfirm: $("#hostPasswordConfirm").val(),
        hostAcceptTC: $("#hostAcceptTC").val(),
    })
    .then(function (response) {
        // daca registrarea este cu success
        setTimeout(function(){
            if(response.data.success){
                $('#registerUser').removeClass('disabled').html('<i class="fa-solid fa-check"></i> Succes');
            }
        }, 1000);
        setTimeout(function(){
            // ascunde pop-ul de registrare si aratal pe cel de login
            $('#registerModal').modal('hide');
            setTimeout(function(){
                $('#loginModal').modal('show');
            }, 600);
        }, 1800);
    })
    .catch(function (error) {
        setTimeout(function(){
            $('#registerUser').removeClass('disabled').html('Inregistreaza-te');
            displayValidationErrorMessages(error.response.data.errors);
        }, 1000);
    });
});
// cand user-ul introduce un nou caracter in una din campurile cu ID: touristEmail sau hostEmail,
// la fiecare 1000 milisecunde, trimite o cerere axios la o ruta din backend, unde un controller verifica daca acest email exista deja in baza de date
$("#touristUserName, #touristEmail, #hostEmail").on("keyup", function() {
    $(".form-control, .form-check-input").removeClass("border-danger");
    $(".invalid-feedback").hide();
    setTimeout(function(){
        axios.post("/checkTouristUniqueData", {
            registerUserType: $("#registerType").val(),
            touristUserName: $("#touristUserName").val(),
            touristEmail: $("#touristEmail").val(),
            hostEmail: $("#hostEmail").val(),
        })
        .then(function (response) {})
        .catch(function (error) {
            // daca email-ul exista deja in baza de date, arata erorile.
            displayValidationErrorMessages(error.response.data.errors);
        });
    }, 1000);
});

function displayValidationErrorMessages(validationErrors){
    $.each(validationErrors, function (index, value) {
        var errorMesssages = "";
        $.each(value, function (index2, messageValue) {
            if (messageValue) errorMesssages += messageValue + "<br>";
        });
        $("#invalid-" + index).html(errorMesssages).slideDown();
        $("#" + index).addClass("border-danger");
    });
}



