<?php 

    include("../config.php");
    $url = urldecode($_GET['url']);

?>
<meta property="og:url"           content="<?php echo $url?>" />
<meta property="og:type"          content="website" />
<meta property="og:title"         content="<?php echo $pageName?>" />



<script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<script>
    window.twttr = (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0],
        t = window.twttr || {};
    if (d.getElementById(id)) return t;
    js = d.createElement(s);
    js.id = id;
    js.src = "https://platform.twitter.com/widgets.js";
    fjs.parentNode.insertBefore(js, fjs);

    t._e = [];
    t.ready = function(f) {
        t._e.push(f);
    };

    return t;
    }(document, "script", "twitter-wjs"));
</script>

<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<style>
    #sharerTable{
        width: auto;
        margin-right: 0px;
        margin-left: auto;
        table-layout:fixed;
    }
    #sharerTable td{
        padding-left:5px;
        padding-right:5px;
    }
</style>

<table id="sharerTable">
    <tr>
        <td>
            <a class="twitter-share-button" href="https://twitter.com/intent/tweet?text=<?php echo $pageName?>">
                Tweet
            </a>
        </td>
        <td>
            <div id="fb-root"></div>
            <div class="fb-share-button" data-href="<?php echo $url?>" data-layout="button_count"></div>
        </td>
        <td>
            <div class="g-plus" data-action="share" data-href="<?php echo $url?>"></div>
        </td>
    </tr>
</table>

