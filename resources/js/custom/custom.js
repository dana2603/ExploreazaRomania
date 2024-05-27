/* Custom.js este fisierul Javascript care este prezent pe tot site-ul: backend (useri autentificati) si front end (pagina cautare) */

    //niste parametri din URL daca vrem sa facem un 'trigger' sau actiune ca sa aratam un popup/modal
    const urlSearchParams = new URLSearchParams(window.location.search);

    $('[data-toggle="tooltip"]').tooltip();

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    if(urlSearchParams.has('login')){
        $('#loginModal').modal('show');
        window.history.pushState({}, document.title, "/");
    }
    // dupa registrare setam URl-ul sa aiba ca si parametru 'login'. Astfel sti ca exact dupa registrare sa inchidem popup de registrare si sa deschidem popup de login
    if(urlSearchParams.has('register')){
        $('#registerModal').modal('show');
        window.history.pushState({}, document.title, "/");
    }
    // click pe registrare, deschidem popup de login
    $('.register-from-login').on('click', function(){
        $('#loginModal').modal('hide');
        $('#registerModal').modal('show');
    });
    // click pe registrare, arata pop-ul de registrare
    $('.login-from-register').on('click', function(){
        $('#registerModal').modal('hide');
        $('#loginModal').modal('show');
    });
    // in momentul cautari (click pe lupa de cautare)
    $('#search-properties').on('click', function(){
        // chemam functia search() de mai jos, si trimitem doi parametri: numarul 1 si boolean true
        search(1, true);
    });
    // cand paginam rezultatele si vrem sa vedem urmatoarea pagina a rezultatelor
    $('.results-container').on('click', '.page-link', function(){
        // chemam functia search() care practic face o cerere a urmatoarelor rezultate
        search($(this).data('page'));
    });
    // cand avem mai multe imagini pe o proprietate si vrem sa vedem urmatoarele imagini
    $('.results-container').on('click', '.carousel-control-next', function(){
        $('#carousel-'+$(this).data('id')).carousel('next');
    });
    // cand avem mai multe imagini pe o proprietate si vrem sa vedem urmatoarele imagini
    $('.results-container').on('click', '.carousel-control-prev', function(){
        $('#carousel-'+$(this).data('id')).carousel('prev');
    });
    // cand vrem sa vedem descrierea proprietati pe pagina de 'search'
    $('.results-container').on('click', '.info', function(){
        $('.quick-description').html('').html($(this).data('quick-description'));
        $('#quick-property-description').modal('show');
    });
    // cand vrem sa prenotam o proprietate si dam click pe butonul cu clasa 'bookRequestProperty'
    $('.results-container').on('click', '.bookRequestProperty', function(){
        // setam niste date ale proprietati in pop-ul care ne permite sa facem cererea de prenotare
        // aceste date trebuie setate, pentru ca apoi le vom accesa mai jos ca sa trimitem datele corecte prin axios la controller
        $(this).closest('.results-container').find('.request-from-date').html('').html('Data sosire: ' + $('#fromDate').val());
        $(this).closest('.results-container').find('.request-to-date').html('').html('Data plecare: ' + $('#toDate').val());
        $(this).closest('.results-container').find('.request-rooms').html('').html('Numar camere: ' + $('#rooms').val());
        $(this).closest('.results-container').find('.request-guests').html('').html('Numar turisti: ' + $('#guestsNumber').val());
        $(this).closest('.results-container').find('.request-guests').html('').html('Pret/noapte: ' + $(this).attr('data-price-per-night'));

        $(this).closest('.results-container').find('.quick-property-book-image').attr('src', $(this).data('first-image-src'));
        $(this).closest('.results-container').find('.quick-property-description').html('').html($(this).data('quick-description'));

        $(this).closest('.results-container').find('#send-book-request').attr("data-property-id", $(this).attr('data-property-id'));
        $(this).closest('.results-container').find('#send-book-request').attr("data-host-id", $(this).attr('data-host-id'));
        $(this).closest('.results-container').find('#send-book-request').attr("data-tourist-id", $(this).attr('data-tourist-id'));
        $(this).closest('.results-container').find('#send-book-request').attr("data-from-date", $('#fromDate').val());
        $(this).closest('.results-container').find('#send-book-request').attr("data-to-date", $('#toDate').val());
        $(this).closest('.results-container').find('#send-book-request').attr("data-price-per-night", $(this).attr('data-price-per-night'));

        // in funcie de cati turisti maxim poate primi proprietatea, generam exact numarul de campuri unde userul poate sa introduca numele di prenumele turistilor care vor fi gazduiti
        var touristDetailHtml = '';
        for (var index = 1; index <= $(this).data('max-tourists'); index++) {
            touristDetailHtml += '<div class="mb-3"><div class="row tourist-details">'+
            '<div class="col-6">'+
                '<label for="tourist-'+index+'-first-name" class="form-label">Nume turist '+index+': </label>'+
                '<input type="text" class="form-control" id="tourist-'+index+'-first-name" name="tourist-'+index+'-first-name">'+
                '<div class="invalid-feedback" id="tourist-'+index+'-first-name"></div>'+
            '</div>'+
                '<div class="col-6">'+
                    '<label for="tourist-'+index+'-last-name" class="form-label">Prenume turist '+index+':</label>'+
                    '<input type="text" class="form-control" id="tourist-'+index+'-last-name" name="tourist-'+index+'-last-name">'+
                    '<div class="invalid-feedback" id="tourist-'+index+'-last-name"></div>'+
                '</div>'+
            '</div></div>';
        };
        // attasam aceste campuri la pop-ul care se va deschide
        $(this).closest('.results-container').find('.booking-tourists-list').html('').html(touristDetailHtml);
        // deschidem pop-ul
        $('#quick-property-book').modal('show');
    });
    // cand vrem sa salvam o proprietate ca si favorita
    $('.results-container').on('click', '.favourite-property', function(){
        // setam noul status ca si favorit sau nu.
        $(this).attr('data-favourite', ($(this).attr('data-favourite') == 'hearted') ? 'nothearted' : 'hearted');
        // verificam daca trebuie sa aratam o inimioara plina sau goala (daca este sau nu favorita proprietatea)
        if($(this).attr('data-favourite') == 'hearted'){
            $(this).html('').html('<i class="fa-solid fa-heart"></i>');
        }
        else{
            $(this).html('').html('<i class="fa-regular fa-heart"></i>');
        }
        // executam cererea prin axios la ruta; /favourite care in baza rutei va accesa o functie intr-un controller specific
        axios.post("/favourite", {
            hostId: $(this).attr('data-host-id'),
            propertyId: $(this).attr('data-property-id')
        });
    });

    // setare calendar pentru sectiunea de cautare a proprietatilor
    $(".datePickerInput").datepicker({
        monthNames: ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'],
        monthNamesShort: ['Ian', 'Feb', 'Mar', 'Apr', 'Mai', 'Iun', 'Iul', 'Aug', 'Sept', 'Oct', 'Noi', 'Dec'],
        dayNames: ['Duminica', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata'],
        dayNamesMin: ['Du', 'Lu', 'Ma', 'Mi', 'Jo', 'Vi', 'Sa'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        firstDay: 1,
        minDate: new Date(),
        dateFormat: 'dd/mm/yy',
        onSelect: function(date) {
            if($(this).hasClass('search-input-from-date')){
                $(".search-input-to-date").datepicker("option", "defaultDate", date);
            }
        },
        beforeShow: function(input, inst) {
            inst.dpDiv.css({
                marginTop: '0px',
                marginLeft: '-52px'
            });
        }
    });

    // cand dam click pe butonul cu id-ul: send-enquiry atunci vom face o actiune
    // actiuna va fi de a colecta datele din formu si a le trimite la controller
    // axios post: e ruta unde datele vor fi trimise: va trebui sa adaugi o ruta in web.php care va accepta un pot si va trimite datele la un controller
    $("#send-enquiry").on('click', function(event){
        event.preventDefault();
        // deschide consola si ai sa vezi acest mesaj cand dai click pe SUBMIT pe pagina de contact
        axios.post("/contacteaza", {
            // asa colectam datele pentru toata casutele de input. trebuie sa le adaugi tu pe restu
            firstName: $("#firstName").val(),
            lastName: $("#lastName").val(),
            contactEmail: $("#contactEmail").val(),
            message: $("#message").val()
        })
        .then(function (response) {
        })
        .catch(function (errors) {
            displaySearchValidationErrorMessages(errors.response.data.errors);

        });
    });

    // aicea trimite cererea de prenotare
    $('.results-container').on('click', '#send-book-request', function(e){
        var bookRequestButton = $(this);
        bookRequestButton.addClass('disabled').html('<i class="fa-solid fa-spinner fa-spin"></i> Trimite cerere');
        e.preventDefault();
        var tourists = [];
        var touristDetailsContainer = bookRequestButton.closest('#quick-property-book').find('.booking-tourists-list').children();
        $.each(touristDetailsContainer, function (index, item) {
            index = index + 1;
            if($(item).find('#tourist-'+index+'-first-name').val() != ''){
                tourists.push({firstName: $(item).find('#tourist-'+index+'-first-name').val(), lastName: $(item).find('#tourist-'+index+'-last-name').val()});
            }
        });
        // facem o cerere la o ruta /book care va accesa o functie intr-un controller specific (vezi web.php)
        axios.post("/book", {
            // trimitem toate aceste date
            propertyId: $(this).attr('data-property-id'),
            hostId: $(this).attr('data-host-id'),
            touristId: $(this).attr('data-tourist-id'),
            fromDate: $(this).attr('data-from-date'),
            toDate: $(this).attr('data-to-date'),
            pricePerNight: $(this).attr('data-price-per-night'),
            message: bookRequestButton.closest('#quick-property-book').find('#message').val(),
            tourists: JSON.stringify(tourists)
        })
        .then(function (data) {
            // cand controllerul returneaza in obiectul data 'success' ..
            if(data.data.success){
                setTimeout(function(){
                    bookRequestButton.html('Trimite cerere');
                    $('.request-success-message').fadeIn();
                    setTimeout(function(){
                        // ascunde pop=ul de cerere
                        $('#quick-property-book').modal('hide');
                    }, 3000);
                }, 1000);
                // verifica care proprietate trebuie dezactivata pentru ca userul sa nu mai poate face inca o cerere de prenotare
                $.each($(".bookRequestProperty"), function (index, item) {
                    // dezactiveaza proprietatea care tocmai ce a fost prenotata
                    if($(this).attr('data-host-id') == bookRequestButton.attr('data-host-id') && $(this).attr('data-property-id') == bookRequestButton.attr('data-property-id')){
                        $(this).addClass('disabled');
                    }
                });
            }
        })
        .catch(function (error) {});
    });

    // functia pentru cautare
    function search(page, initialSearch){
        if(page == 1 && initialSearch){
            $('#results').html('<span class="loading"><i class="fa-solid fa-cog fa-spin"></i></span>').fadeIn();
        }
        // cerere axios la o ruta specifica si o functie specifica din controller.
        axios.post("/search?page=" + page, {
            // trimite aceste date colectate de pe pagina
            fromDate: $("#fromDate").val(),
            toDate: $("#toDate").val(),
            rooms: $("#rooms").val(),
            guestsNumber: $("#guestsNumber").val(),
        })
        .then(function (data) {
            // dupa terminarea cererei, verifica daca acesta a fost cu success sau a produs errori
            setTimeout(function(){
                // daca in obiectul data exista date returnate de controller: vezi in controller 'Return View::make' care practic returneaza toate datele care au fost trimise la view si procesate in view.
                if(data.data){
                    $('#results').html('').hide().append(data.data).fadeIn();
                }
                else{
                    $('#results').html('').html('<span class="no-results">Cautare nu a produs rezultate</span>').fadeIn();
                }
            }, ((page == 1, initialSearch) ? 1000 : 20));
        })
        .catch(function (error) {
            // cererea a produs errori
            setTimeout(function(){
                $('#results').html('').html('<span class="no-results">Cautare nu a produs rezultate</span>').fadeIn();
            }, 1000);
        });
    }

    // functie generala care itereaza toate erorile si le arata in sectiunea propriuzisa
    function displaySearchValidationErrorMessages(validationErrors){
        $.each(validationErrors, function (index, value) {
            var errorMesssages = "";
            $.each(value, function (index2, messageValue) {
                if (messageValue) errorMesssages += messageValue + "<br>";
            });
            $("#invalid-" + index).html(errorMesssages).slideDown();
            $("#" + index).addClass("border-danger");
        });
    }
