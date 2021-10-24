<?php
// ltrim each line
// use decomp to check answer
class Comb {
  
  public $current = "";
  function __construct($a)
  { 
    echo strlen($a)."<br>";
    $this->compress($a);
  }
  
  public function adiff($ic, &$hex, &$temp, $front = "")
  {
    // leave a 1 at the end
    if (strlen($ic) <= 1)
      return $ic;
    $front = ($front == "")?$ic:$front;
    
    $h = strlen($ic);
    $trim = substr($ic,0,1);
    $oc = ltrim($ic,"$trim");
    $f = $h-strlen($oc);

    while ($f >= 3)
    {
      $temp .= '0';
      $f -= 3;
    }
    $temp .= '0';
    $temp .= str_repeat('1',$f+1);

    if (strlen($oc) <= 3)
    {
      $temp = $temp;
      $blank = $this->decomp($blank,$temp,0);
      $rev = ($blank).$oc;
      echo substr($rev,3)."*<br>";
      
      while (strlen($temp) > 0)
      {
        $temp2 = substr($temp,0,8%(strlen($temp)+1));
        if (0 == bindec($temp2)%256)
          $hex .= chr(63);
        else
          $hex .= chr(bindec($temp2)%256);
        $temp = substr($temp,8%(strlen($temp)+1));
        $temp = (strlen($temp) > 8)?$temp:"";
      }
    }
    return ($oc);
  }

  public function decomp($total, &$temp,$zo)
  {
    $temp = substr($temp,1);
    while (substr($temp,0,1) == "0")
    {
      $temp = substr($temp,1);
      $total .= str_repeat("$zo",3);//.$total;
    }

    $temp = substr($temp,1);
      
    while (substr($temp,0,1) == "1")
    {
      $temp = substr($temp,1);
      $total = ("$total$zo");
    }

    $zo = ($zo ^ 1);
   
    if (strlen($total) >= 128)
      return $total;
    return $this->decomp($total,$temp,$zo);
  }

  public function fd($g, &$z = 0)
  {
    $t = 0;
    while ($g != "")
    {
      $cb = 8 - strlen(decbin(ord($g[0])));
      $t .= decbin(ord($g[0]));
      $t = str_repeat('0',$cb).$t;
      $g = substr($g,1);
    }
    $hex = "";
    echo "".$t."<br>";
    $z = 0;
    do
    {
      $t = $this->adiff($t, $hex, $z);
    } while (strlen($t) > 3);
    $hex .= chr(bindec($t));
    return $hex;
  }

  public function compress($d)
  {
    $x = "";
    $c = 0;
   // echo strlen($d) . "<br>";
    ob_flush();

    //while ($c < 2)
    {
      $z = 0;
      for (; $d!="" ;)
      {
    // 8 chars at a time
        $x.= $this->fd(substr($d,0,16%(strlen($d)+1)), $z);
        //if (strlen($d) > 16)
        $d = substr($d,16%(strlen($d)+1));
        $d = (strlen($d) > 16)?$d:"";
      }
      $z = bindec($z);
      while ($z > 0)
      {
        $x.=chr($z%256);
        $z >>= 8;
      }
  // reset sources
      $hex = "";
      $d = $x;
      $x = "";
      $c++;
      //echo strlen($d)."<br>";
    }
    file_put_contents('out.xiv',$d);
    $s = "";
    //echo $this->decomp($s,$d,0);
  }
}
$a=file_get_contents("groovy.mp3");
$a = substr($a, 0, 10000);
$timea = date_create();
$x = new Comb($a);
$v = date_diff(date_create(),$timea);
echo $v->m.":". $v->s." ".$v->f;
echo "<br>".filesize('out.xiv');

?>