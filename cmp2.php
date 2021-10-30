<?php
// ltrim each line
// use decomp to check answer
class Comb {
  
  public $current = "";
  public $bytes = 0;
  
  public function adiff($ic, &$hex, &$temp, $const)
  {
    // leave a 1 at the end
    $assertion = 0; // This will count the flipping of bits, 1010101 for example
    start:
    if (strlen($ic) < 1)
      return $ic;
    $h = strlen($ic);
    $trim = substr($ic,0,1);
    $oc = ltrim($ic,"$trim");
    $f = $h-strlen($oc);

    $f--;
    if ($assertion > 0 && $f == 0) { // Once the flipping bits are reached
      $assertion++; // Increment counter once for each flipping bit
      $ic = $oc; // Make sure to recount the one taken off by the flipping bit
      goto start;

    }
    else if ($assertion == 0 && $f == 0) { // Start a new order of flipping bits
      $assertion++; // Increment for the first flipping bit
      $ic = $oc; // Reset the $ic variable because of the flipping bit
      goto start;
    }
    else if ($assertion > 0) // if we have already seenthe flipping bits
    {
      $temp .= "1"; // Make a assertion that we have been using the flipping bit technique
      $f = $assertion; // turn $assertion over to $f which is the count of flipped off and on again bits
      $ic = $oc; // Questionable line here. Have we flipped them enough?
      $assertion = 0; // reset $assertion to count again next time. Kinda pointless with it not by reference
    }
    else // If there were no assertions, and there were no flipping bits to handle, than we carry on with "0"
      $temp .= "0"; // This tells the file that we are going straight thru with a series of bits.
    
    $temp .= str_repeat("0",2-strlen(decbin($f%4))) . decbin($f%4);
    $f -= $f%4;
    $f >>= 2;
    $temp .= ($f == 0) ? "1" : "0";
    while ($f >= 1)
    {
      $temp .= decbin($f <= 1); // are we at zero?
      $f -= 1;
    }
    
    if (strlen($oc) < 1)
    {
      // Here down to while : test decompress
      $temp1 = ($temp);
      $blank = "";
      $blank = $this->decomp($blank,$temp1);
      $rev = ($blank);
      echo $const."\n\r";
      echo strrev($rev).$oc."++++\n\r";

      // Significant while
      while (strlen($temp) > 0)
      {
        $temp2 = substr($temp,0,8%(strlen($temp)+1));
        $hex .= chr(bindec($temp2));
        $temp = (strlen($temp) >= 8)?substr($temp,8%(strlen($temp)+1)):"";
      }
      return $oc;
    }
    
    return $this->adiff($oc, $hex, $temp, $const);
  }

  public function decomp($total, &$temp, $bit = "0")
  {
    // HERE IS THE CODE FOR MAKING THE DECOMPRESSION
    // $f is ALWAYS AT LEAST 1
    // +1
    if (strlen($temp) < 1)
      return $total;
    // This is the actual bit were using (line 24)
    $SPMB = substr($temp,0,1); // Remove the front bit
    {
      $temp = substr($temp,1); // line 39/44 ($SPMB) 
      $number = bindec(substr($temp,0,2)); // line 46
      
      $temp = substr($temp, 2); // line 46
      
      $temp_reps = strlen(ltrim($temp,"0"));
      if (substr($temp,0,1) == "1") {
        
        $temp = substr($temp,1);
      } 
      else if (substr($temp,0,1) == "0")
      { 
        $number <<= 2;
        $number += (strlen($temp) - $temp_reps); // Take the difference
        $temp = substr($temp,1);
        return $this->decomp(($total), $temp, (bindec($bit) ^ 1));
      }
      while ($number > 1)
      {
        $total .= "$bit"; // Take as causal. Maybe needs to be last
        $bit = ($bit ^ bindec($SPMB)); // Keep flipping the flipping bits
        $number--; // Decrement for each flipped bit
      }
      $total .= "$bit"; // Just one more bit
      return $this->decomp(($total), $temp, (bindec($bit) ^ 1));
    }

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
    //echo "".$t."\n\r";
    $z = 0;
    $r = $t;
    do
    {
      $t = $this->adiff($t, $hex, $z, $r);
    } while (strlen($t) > 8);
    $hex .= chr(bindec($t)%256);
    return $hex;
  }

  public function compress($d)
  {
    $x = "";
    $c = 0;
    
    while ($c < 1)
    {
      $z = 0;
      for (; $d!="" ;)
      {
    // 8 chars at a time
        $x.= $this->fd(substr($d,0,4%(strlen($d)+1)), $z);
        $d = substr($d,4%(strlen($d)+1));
        $d = (strlen($d) > 4)?$d:"";
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
    }
    $s = "";
    return $d;
  }
}

$timea = date_create();
$a = 0;
$output = "temp__";
$zipfile = "out.xiv";
$filename = "pic.png";
$enw = fopen($filename,"r");
$size = filesize($filename);
echo "Input Size: $size\r\n";
    
    $out = null;
    $out = fopen("temp__","w") or die("\n\rCannot open file:  $output\n\r");

    //$a=file_get_contents("enwik9");
    $f = 0;
    $input = 0;
    $x = new Comb();
    $m = 0;

    while ($x->bytes < $size-1)
    {
      $v = date_diff(date_create(),$timea);
      echo round($input/($x->bytes+1)*100,2) . "%   ::    ". round($x->bytes/(filesize($filename)+1)*100 ,2). "%      :: ($input / $x->bytes)      :: " .$v->i.":". ($v->s + $v->f)."\r";
      $enw9 = fread($enw, 1000000%(filesize($filename)));
      $x->output = $x->compress($enw9);
      $x->bytes += strlen($enw9)%(filesize($filename));
      if (FALSE == fwrite($out, $x->output))
      {
          echo "ERROR: CANNOT CONTINUE";
          exit();
      }
      $input += strlen($x->output);
    }
    fclose($out);
    fclose($enw);
    //unlink("tmp");
    $a++;
    echo round($input/$x->bytes*100,2) . "%   ::    ". round($x->bytes/(filesize($filename)+1)*100 ,2). "%      :: ($input / $x->bytes)      :: " .$v->i.":". ($v->s + $v->f)."\r";
      
    rename("$output","tmp");
    $enw = fopen("tmp","r");
    $size = filesize("tmp");
    $filename = "$output";
    $output = "tmp";
    echo "\n\r";

rename("tmp", $zipfile);
echo "Output size: ".filesize($zipfile)."\r\n";

?>
