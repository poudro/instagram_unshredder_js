
var log = function()
{
  try
  {
    if (typeof console.log == "function")
      console.log(arguments);
  } catch(e){}
}


/* An unshredder */

var srcCvs, unshCvs, srcCt, unshCt, srcData, alreadyShredded = false;

var getGCD = function(x,y) 
{
  var w;
  while (y != 0) 
  {
    w = x % y;
    x = y;
    y = w;
  }
  return x;
}

var getPosInData = function(x,y,d)
{
  return (y * d.width*4) + x*4;
}

var getPixelValue = function(x,y,d)
{
  var s = getPosInData(x,y,d), p = [d.data[s],d.data[s+1],d.data[s+2]];
  return p;
}

var dist = function(p1, p2)
{
  return Math.sqrt(Math.pow(p1[0]-p2[0],2)+Math.pow(p1[1]-p2[1],2)+Math.pow(p1[2]-p2[2],2));
}


var getSummedAdjacentePix = function(d)
{
  var y = d.height, x = d.width-1, s = [];
  
  while (x--)
    s[x] = 0;
    
  while(y--)
  {
    x = d.width-1;
    while (x--)
      s[x] += dist(getPixelValue(x, y, d), getPixelValue(x+1, y, d));
  }
  
  return s;
}


var sortWithKey = function(a,b)
{
  return (a[1]-b[1]);
}

var getSecondDiffSorted = function(s)
{
  var x = s.length-2, dd = [];
  while(x--)
    dd.push([x+1, -(s[x+2]-2*s[x+1]+s[x])]);
  
  dd.sort(sortWithKey);
  
  return dd;
}



var getShredWidth = function()
{
  var s = getSummedAdjacentePix(srcData),
      ddd = getSecondDiffSorted(s),
      dddL = ddd.length,
      dddLL = ddd.length - 1,
      x = dddL, y,
      limit = 0;
 
  while (x--)
  {
    if (ddd[dddLL][1]/ddd[x][1]>5)
      break;
    ++limit;
  }
  --limit;

  var diffs = [];
  x = limit;
  while(x--)
  {
    y = limit;
    while(y--)
      if (x!=y)
        diffs.push(Math.abs(ddd[x][0]-ddd[y][0]));
  }
  
  var n = new Hash(), g;
  x = diffs.length;
  while(x--)
  {
    y = diffs.length;
    while(y--)
    {
      if (x != y)
      {
        g = Math.abs(getGCD(diffs[x],diffs[y]));
        if (g > 5 && srcData.width%g == 0)
        {
          if (n[g])
            ++n[g];
          else
            n[g] = 1;
        }
      }
    }
  }

  var k = n.getKeys();
  var v = n.getValues();

  n = [];
  x = k.length;
  while(x--)
    n.push([parseInt(k[x],10),v[x]]);
  n.sort(sortWithKey);

  return n[n.length-1][0];
}


var loadShredsFromSrcData = function(shredWidth)
{
  var shreds = [];
  x = srcData.width/shredWidth;
  
  while(x--)
  {
    if (x<0)
      break;
    shreds[x] = srcCt.getImageData(x*shredWidth, 0, shredWidth, srcData.height);
  }
  
  return shreds;
}




var startUnshred = function()
{
  if (alreadyShredded)
    return;
  alreadyShredded = true;
  
  unshCvs.addClass('rot');
  if (typeof _gaq != "undefined")
    _gaq.push(['_trackEvent', 'unshredder','startUnshred']);

  setTimeout(doUnshred, 50);
}



var doUnshred = function()
{
  var shredWidth = getShredWidth(),
      numShreds = srcData.width/shredWidth,
      x, y;

  log(shredWidth, numShreds);
  var shreds = loadShredsFromSrcData(shredWidth);

  var n = new Hash(), nr = new Hash(), s1 = shreds.length, s2, l ,r, tt1, tt2;
  while(s1--)
  {
    s2 = shreds.length;
    
    tt1 = [];
    tt2 = [];
    
    
    while(s2--)
      if (s1 != s2)
      {
        l = 0;
        r = 0;
        y = shreds[s1].height;
        while(y--)
        {
          l += dist(getPixelValue(shreds[s1].width-1, y, shreds[s1]), getPixelValue(0, y, shreds[s2]));
          r += dist(getPixelValue(shreds[s2].width-1, y, shreds[s2]), getPixelValue(0, y, shreds[s1])); 
        }
        
        tt1.push([s2,l]);
        tt2.push([s2,r]);
      }
    
    tt1.sort(sortWithKey);
    tt2.sort(sortWithKey);

    n[s1] = tt1.concat();
    nr[s1] = tt2.concat();
  }


  var lb = 0, k = n.getKeys(), x, xL = k.length, f;
  for (x=0; x!=xL; ++x)
  {
    f = nr[x][0][0];
    r = n[f][0][0];
    if (x != r)
      lb = x;
  }

  f = lb;
  var xL = k.length, used = [], ff;
  unshCvs.removeClass('rot');
  for (x=0; x!=xL; ++x)
  {
    unshCt.putImageData(shreds[f], x*shredWidth, 0);

    used.push(f);
    ff = f;
    y = 0;
    if (used.length < xL)
      while(used.contains(ff))
      {
        ff = n[f][y][0];
        ++y;
      }
    f = ff;
    if (x!=0)
    {
      srcCt.beginPath();
      srcCt.strokeStyle = "#f00";
      srcCt.moveTo(x*shredWidth, 0);
      srcCt.lineTo(x*shredWidth, srcCvs.height);
      srcCt.closePath();
      srcCt.stroke();  
    }
  }

}



var srcLoaded = function(im)
{
  alreadyShredded = false;
  
  var w = im.get('width'), h = im.get('height');
  srcCvs.removeClass('rot');
  srcCvs.width = unshCvs.width = w;
  srcCvs.height = unshCvs.height = h;
  
  srcCt.drawImage(im, 0, 0, w, h);
  
  srcData = srcCt.getImageData(0, 0, w, h);
}

var loadNewImg = function(src)
{
  srcCt.clearRect(0,0,srcCvs.width,srcCvs.height);
  unshCt.clearRect(0,0,unshCvs.width,unshCvs.height);
  
  srcCvs.addClass('rot');

  if (typeof _gaq != "undefined")
    _gaq.push(['_trackEvent', 'unshredder','example',src]);
  
  var iniImg = new Asset.image(src, {onLoad:srcLoaded,onError:function(e){log(e)}});
}

var loadAndShred = function(alreadyShredded)
{
  srcCt.clearRect(0,0,srcCvs.width,srcCvs.height);
  unshCt.clearRect(0,0,unshCvs.width,unshCvs.height);
  
  srcCvs.addClass('rot');

  var src = $('urlimg').get('value'),
      tok = $('tok').get('value'),
      pub = $('pub').get('value'),
      nshreds = parseInt($('nshreds').get('value'));
  
  if (640./nshreds<6)
  {
    nshreds = 20;
    $('nshreds').set('value',20);
  }
  
  if (typeof _gaq != "undefined")
    _gaq.push(['_trackEvent', 'unshredder','load',src]);
    
  var url = 'loadimg.php?tok='+tok+'&pub='+pub+'&url='+src+'&nshreds='+nshreds+'&_r='+Math.floor(100000*Math.random());

  var iniImg = new Asset.image(url, {onLoad:srcLoaded,onError:function(e){log(e)}});  
}





var init = function()
{
  srcCvs = $('source');
  if (typeof srcCvs.getContext == "undefined")
  {
    new Element('div',{'html': 'You browser is too old and does not support canvas. Please consider getting one of these : <a target="_blank" href="http://getfirefox.com/">firefox</a> or <a target="_blank" href="http://www.google.com/chrome/">chrome</a>','style':"border:1px solid #f00;padding:10px;margin:10px 5px;"}).inject($("commands"),'after');
    return;
  }
  srcCt = srcCvs.getContext('2d');
  
  unshCvs = $('unshredded');
  unshCt = unshCvs.getContext('2d');
  $('dounshred').addEvent('click', startUnshred);
  $('loadfromurl').addEvent('click', loadAndShred);

  loadNewImg('examples/TokyoPanoramaShredded.png');
}


window.addEvent('domready', init);
