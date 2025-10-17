 <script src="js/jquery.js"></script>
 <script src="js/price-range.js"></script>
 <script src="js/jquery.scrollUp.min.js"></script>
 <script src="js/bootstrap.min.js"></script>
 <script src="js/jquery.prettyPhoto.js"></script>
 <script src="js/main.js"></script>
 
 <!--Select2-->
    <script src="select2/js/select2.full.min.js"></script>
    <script>
         $(function () {
         //Initialize Select2 Elements
         $(".select2").select2();
         
     })
    </script>
 
 <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({ pageLanguage: 'en' }, 'google_translate_element');
    }

	var flags = document.getElementsByClassName('lang-select'); 

    Array.prototype.forEach.call(flags, function(e){
      e.addEventListener('click', function(){
        var lang = e.getAttribute('data-lang'); 
        var languageSelect = document.querySelector("select.goog-te-combo");
        languageSelect.value = lang; 
        languageSelect.dispatchEvent(new Event("change"));
      }); 
    });
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script>
    $(document).ready(function(){
    $('.customer-logos').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 1500,
        arrows: false,
        dots: false,
        pauseOnHover: false,
        responsive: [{
            breakpoint: 768,
            settings: {
                slidesToShow: 4
            }
        }, {
            breakpoint: 520,
            settings: {
                slidesToShow: 3
            }
        }]
    });
    });
</script>
<script>
    jQuery(window).load(function () {
        var cookie = document.cookie;
        var position = cookie.indexOf("googtrans");
        var language = cookie.substring(position+10, position + 16);
        var act_lang = language.split('/');
        var flag = act_lang[act_lang.length - 1].length != 2 ? 'us' :  act_lang[act_lang.length - 1] == 'en' ? 'us' : act_lang[act_lang.length - 1] == 'sw' ? 'tz' : act_lang[act_lang.length - 1];
        $("#dropdownMenu1 span").replaceWith("<span class='flag-icon flag-icon-"+flag+"'></span>");
        $(this).prepend($("#dropdownMenu1").html());
    });
    
    $(".dropdown-menu li a").click(function(){
        $('#loader-cont').show();
        $("#dropdownMenu1 span").replaceWith($(this).find('.flag-icon'));
        $(this).prepend($("#dropdownMenu1").html());
        $('#loader-cont').hide();
    });
    
</script>

