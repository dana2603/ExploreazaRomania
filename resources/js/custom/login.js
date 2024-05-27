// cand schimbam tipul de user, salveaza tip-ul de user intr-o casuta ascunsa a carei valoare o sa o trimitem la server printr-un POST care va fi preluat de RegisterController pe langa alte
$('#login').on('click', function(){
    $('.invalid-feedback').hide();
    $('.form-control, .form-check-input').removeClass('border-danger');
    $('#login').addClass('disabled').html('<i class="fa-solid fa-spinner fa-spin"></i> Login');
    // trimite cerere de login prin axios la controllerul de Login pentru verificare si validare
    axios.post("/login", {
        // colectam datele din formular
        email: $("#email").val(),
        password: $("#password").val(),
        userType: $("input[name='userType']:checked").attr('data-user-type') ?? null
    })
    .then(function (data) {
        setTimeout(function(){
            // daca respunsul din controller este success, window.location ne ajuta sa redirectionam user-ul spre pagina corect din backend (dashboard)
            // data.data.userType => va contine ce tip de user s-a logat
            // /host/dashboard sau /tourist/dashboard
            if(data.data.success){
                $('#login').removeClass('disabled').html('Login');
                window.location.replace("/" + data.data.userType + "/dashboard");
            }
        }, 1000);
    })
    .catch(function (error) {
        setTimeout(function(){
            // cheama functia pentru aratarea erorilor si trimite erorile la aceasta functie.
            displayValidationErrorMessages(error.response.data.errors);
            $('#login').removeClass('disabled').html('Login');
        }, 1000);
    });
});
// functie pentru aratarea erorilor de pe pagina
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



