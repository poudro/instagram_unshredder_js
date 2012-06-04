<?php
session_start();

// simplistic protection to prevent outside use of loadimg.php
$_SESSION['id'] = uniqid();
$_SESSION['priv'] = uniqid();
$_SESSION['pub'] = md5($_SESSION['id'] . 'SALT_1' .  $_SESSION['priv'] . 'SALT_2') ;

?>
<!DOCTYPE html>
<html dir="ltr" lang="fr-FR">
<head>
  <!--
    
    / Peanut Butter and Jelly by
                             __
                            /\ \ 
     _____    ___   __  __  \_\ \  _ __  ___       ___    ___    ___ ___    
    /\ '__`\ / __`\/\ \/\ \ /'_` \/\`'__\ __`\    /'___\ / __`\/' __` __`\  
    \ \ \L\ \\ \L\ \ \ \_\ \\ \L\ \ \ \/\ \L\ \__/\ \__//\ \L\ \\ \/\ \/\ \ 
     \ \ ,__/ \____/\ \____/ \___,_\ \_\ \____/\_\ \____\ \____/ \_\ \_\ \_\
      \ \ \/ \/___/  \/___/ \/__,_ /\/_/\/___/\/_/\/____/\/___/ \/_/\/_/\/_/
       \ \_\ 
        \/_/ 
    
    / A web factory
    / Web problem solver
    / Développeur Web Freelance - Paris

    / visit us at http://poudro.com
    
  -->
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=9" />
  <title>Instagram Unshredder Challenge in javascript and html5 canvas // poudro.com</title>
  <script type="text/javascript" src="js/mootools-core-1.4.1-full-nocompat-yc.js"></script>
  <script type="text/javascript" src="js/mootools-more-1.4.0.1.js"></script>
  <script type="text/javascript" src="js/ic.js"></script>
  <style type="text/css">
    *{margin:0;padding:0;}
    body{font-family:arial;color:#333;width:100%;}
    #global{width:1308px;margin:10px auto;}
    #canvases{float:left;}
    canvas{border:1px solid #000;margin:5px;}
    #commands{clear:both;margin:10px 5px 0;border:1px solid;padding:10px;height:40px;}
    #commands span{float:left;margin:0 10px;line-height:30px;}
    #dounshred,.ll{cursor:pointer;padding:10px;border:1px solid;}
    #dounshred{float:left;font-weight:bold;}
    .ll{float:right;}
    #loadex{margin-left:50px;float:left;}
    select{padding:3px;margin-right:10px;}
    .clear{clear:both;}
    .rot{background:url(loader.gif) no-repeat center center;}
    a{color:#000;}
    .ll,#urlimg{padding:3px;margin:0 3px;}
    .ll{height:20px;line-height:20px;}
    #dounshred:hover,.ll:hover{color:#fff;background:#333;}
    .lll:hover{color:#333;background:#fff;}
    .lll{border:0;}
    #remarques{clear:both;margin:10px 5px 0;border:1px solid;padding:10px;width:800px;}
  </style>
</head>
<body>
<div id="global">
  <div id="about"><h1>Instagram Unshredder Challenge</h1><div>A html5 canvas/javascript version of my solution to the <a href="http://instagram-engineering.tumblr.com/post/12651721845/instagram-engineering-challenge-the-unshredder">Instagram Unshredder Challenge</a>. Click the "Do Unshred" button for the magic.</div></div>
  <div id="commands">
    <div id="dounshred">Do Unshred</div>
    <div id="loadex">
      <span>Choose another image : 
      <select onchange="loadNewImg(this.value)">
        <option selected="selected" value="examples/TokyoPanoramaShredded.png">default</option>
        <option value="examples/ex1.png">example 1</option>
        <option value="examples/ex2.png">example 2</option>
        <option value="examples/ex3.png">example 3</option>
        <option value="examples/ex4.png">example 4</option>
        <option value="examples/ex5.png">example 5</option>
        <option value="examples/ex6.png">example 6</option>
        <option value="examples/ex8.png">example 7</option>
        <option value="examples/ex9.png">example 8</option>
        <option value="examples/ex13.png">example 9</option>
        <option value="examples/ex10.png">example 10 (wrong border)</option>
        <option value="examples/ex11.png">example 11 (wrong border)</option>
        <option value="examples/ex12.png">example 12 (wrong border)</option>
        <option value="examples/fail2.png">example 13 (wrong border)</option>
        <option value="examples/ex7.png">example 14 (fails)</option>
        <option value="examples/fail1.png">example 15 (fails)</option>
        <option value="examples/ex14.png">example 16 (106 shreds)</option>
      </select>
      </span>
      <span><span>Load from url:</span> <input size=25 type="text" id="urlimg"/><div class="ll lll">#shreds : <input type="text" size="1" id="nshreds" value="20"/></div><div class="ll" id="loadfromurl">Load &amp; Shred</div>
      <input type="hidden" id="pub" value="<?php echo $_SESSION['pub']; ?>"/>
      <input type="hidden" id="tok" value="<?php echo $_SESSION['id']; ?>"/>
      </span>
    </div>
    <br class="clear"/>
  </div>
  <div id="canvases">
    <canvas id="source"></canvas>
    <canvas id="unshredded"></canvas>
  </div>
  <div id="remarques">
    (Notes:<br/> - Examples 3 and 16 have a lot of shreds, it might take a while to decode (Example 16 comes from a user, thanks for that whoever you are :).<br/> - There is a known flaw in my algorithm when the side borders are identical in the original image it might have a hard time setting things in the right position. See examples 10, 11, 12 and 13 in the drop down.<br/> - For instances where large portions of identical color or repeating patterns occur, it might have a hard time setting things straight. Although a human might have a hard time with some of these, but not always. See examples 14 and 15.<br/> - Please don't try to break things, also, the image loaded from the url is resized to max 640px wide and to a multiple of the shred count you have entered (with a minimum shred width of 6).<br/> - If the "Load" doesn't work, try reloading the page or changing the url.)<br/><br/>from <a href="http://www.poudro.com/blog/a-contribution-to-the-instagram-unshredder-challenge/">poudro.com</a>
  </div>
</div>
</body>
</html>
