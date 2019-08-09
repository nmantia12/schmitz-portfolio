 <?php if (defined('GTM_ID')) { ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= GTM_ID ?>');</script>
<!-- End Google Tag Manager -->
<?php
 } else if(defined('GA_TRACKING_ID')) { 
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= GA_TRACKING_ID ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?= GA_TRACKING_ID ?>');
</script>
<?php } else { ?>
<script>
    //REPLACE WITH REAL GTM IF THEY GIVE US A NEW ONE FOR LOCAL/QA
    var dataLayer = {
        push: function(object){
            console.table(object)
        }
    }
</script>
<?php } ?>