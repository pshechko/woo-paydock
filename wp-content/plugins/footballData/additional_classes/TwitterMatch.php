<?php
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

function wtitter_load_styles() {
    $adr = plugins_url('', __FILE__) . "/twitter_styles.css";
    $urlstyle = str_replace("additional_classes", "styles", $adr);
}

//add_action('wp_enqueue_scripts', 'wtitter_load_styles');
//mb_internal_encoding("UTF-8");
/*
  function register_my_custom_menu_pages() {      // REGISTER MENU ITEMS
  $adr = plugins_url('', __FILE__) . "/settings_ico.png";
  $adr = str_replace("additional_classes", "icons", $adr);
  add_menu_page('Settings', 'Twitter settings', 'manage_options', 'Settings', 'Settings', $adr, 130);
  }
 * 
 */

add_action('admin_menu', 'register_my_custom_menu_pages');


add_shortcode('twitter', 'twitter');

function twitter($atts) {
    ?>
    <style>


        .twitter_wrapper{
            display: table;
            border-collapse:collapse;
            border-spacing:0px;
            background-color: transparent;
            width: 100%;
            //display:none!important;
        }
        .oddpost{
            display: table-row;
            background-color: transparent;
            border-bottom: #c9c9c9 solid 1px;
        }
        .evenpost{
            display: table-row;
            background-color: transparent;
            border-bottom: #c9c9c9 solid 1px;
        }

        .pretwitmin{
            padding-left: 10px;
            font-weight: bold;
            display: table-cell;
            width: 7%!important;
        }

        .pretwitact{
            padding-right: 10px;
            padding-left: 10px;
            font-weight: bold;
            display: table-cell;
            text-align: center;
        }

        .twit{
            display: inline;
            display: table-cell;
        }

        .widefat{
            width: 300px!important;
        }

    </style>

    <input type="hidden" id="ht" value=<?php echo "'" . plugins_url('twitter.php', __FILE__) . "'"; ?> />
    <input type="hidden" id="ht2" value=<?php echo "'" . get_option('cacheTime') . "'"; ?> />
    <input type="hidden" id="ht3" value=<?php
    echo "'";
    if ($atts['count']) {
        echo "count=" . $atts['count'];
    }
    if ($atts['hashtag']) {
        if ($atts['count']) {
            echo"&";
        }
        echo "hashtag=" . $atts['hashtag'];
    }
    echo"'";
    ?> />

    <script>

        function showHint()
        {
            //console.log("in func");
            var xmlhttp;

            if (window.XMLHttpRequest)
            {// ��� ��� IE7+, Firefox, Chrome, Opera, Safari
                console.log("XMLHttpRequest");
                xmlhttp = new XMLHttpRequest();
            }
            else
            {// ��� ��� IE6, IE5
                console.log("Microsoft.XMLHTTP");
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
                {
                    document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
                }
            }
            var adr = document.getElementById("ht").value;
            console.log(adr);
            var atts = document.getElementById("ht3").value;
            if (atts != "")
                adr += "?" + atts;
            xmlhttp.open("GET", adr, true);

            console.log("xmlhttp.open(\"GET\", " + adr + ", true);");
            xmlhttp.send();
        }

        showHint();
        myVar = setInterval(showHint, (document.getElementById("ht2").value) * 1000 * 60);

    </script>

    <style>

        .twitter_wrapper{
            display: table;
            border-collapse:collapse;
            border-spacing:0px;
            //background-color: #FFFAFA;
            width: 100%;
            //display:none!important;
        }

        .tst{
            color:black;    
            -webkit-animation: myfirst 25s; /* Chrome, Safari, Opera */
            animation: myfirst 25s;
        }

        /* Chrome, Safari, Opera */
        @-webkit-keyframes myfirst {
            from {background: Bisque  ;}
            to {background: none;}
        }

        /* Standard syntax */
        @keyframes myfirst {
            from {background: Bisque  ;}
            to {background: none;}
        }

    </style>


    <div class="twitter_wrapper" id="txtHint">
        <div style="text-align: center;">
            <i style="font-size: 52px;" class="fa fa-spinner fa-spin"></i>
        </div>
    </div>

    <?php
}
