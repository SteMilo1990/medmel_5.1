<?php

function notesToIntervals($notes_input, $consider_plica = false){
  $notes_input = rtrim($notes_input);
  $notes_input = str_replace(" '","", $notes_input);
  $notes_input = str_replace("'","", $notes_input);
  $notes_input = str_replace(" &039;","", $notes_input);
  $notes_input = str_replace("&039;","", $notes_input);
  $notes_input = str_replace("_h","b", $notes_input); // considers editorial b-flats == to original b-flats
  $notes_input = str_replace("C1 ","", $notes_input);
  $notes_input = str_replace("C2 ","", $notes_input);
  $notes_input = str_replace("C3 ","", $notes_input);
  $notes_input = str_replace("C4 ","", $notes_input);
  $notes_input = str_replace("C5 ","", $notes_input);
  $notes_input = str_replace("C6 ","", $notes_input);
  $notes_input = str_replace("C1","", $notes_input);
  $notes_input = str_replace("C2","", $notes_input);
  $notes_input = str_replace("C3","", $notes_input);
  $notes_input = str_replace("C4","", $notes_input);
  $notes_input = str_replace("C5","", $notes_input);
  $notes_input = str_replace("C6","", $notes_input);
  $notes_input = str_replace("F1 ","", $notes_input);
  $notes_input = str_replace("F2 ","", $notes_input);
  $notes_input = str_replace("F3 ","", $notes_input);
  $notes_input = str_replace("F4 ","", $notes_input);
  $notes_input = str_replace("F5 ","", $notes_input);
  $notes_input = str_replace("F6 ","", $notes_input);
  $notes_input = str_replace("F1","", $notes_input);
  $notes_input = str_replace("F2","", $notes_input);
  $notes_input = str_replace("F3","", $notes_input);
  $notes_input = str_replace("F4","", $notes_input);
  $notes_input = str_replace("F5","", $notes_input);
  $notes_input = str_replace("F6","", $notes_input);
  $notes_input = str_replace("\n","|", $notes_input);
  $notes_input = str_replace("\n","|", $notes_input);

  if (!$consider_plica) {
    $notes_input = str_replace("(","", $notes_input);
    $notes_input = str_replace(")","", $notes_input);
  }
  
  $intervals_string = "";

  for ($i = 0; $i < strlen($notes_input)-1; $i++) {
    $interval_str = "";

    if ($notes_input[$i] == " "){
      // do nothing, but increase $i
    }
    elseif ($notes_input[$i] == "|"){
      //$intervals_string = $intervals_string."|";
    }
    elseif ($notes_input[$i] == "("){  
    }
    elseif ($notes_input[$i+1] == ")"){
      $intervals_string .= ")";
    }

    else {
      if ($notes_input[$i] == ")"){
        $first_note = note2interval($notes_input[$i-1]);
      }else{
        $first_note = note2interval($notes_input[$i]);
      }

      if ($notes_input[$i+1] == " "){
        $second_note = note2interval($notes_input[$i+2]);
        $intervals_string = $intervals_string." ";
      }elseif ($notes_input[$i+1] == "|"){
        $second_note = note2interval($notes_input[$i+2]);
        $intervals_string = $intervals_string."|";
      }elseif ($notes_input[$i+1] == "("){
        $second_note = note2interval($notes_input[$i+2]);
        $intervals_string = $intervals_string."(";
      }else{
      	if ($notes_input[$i+1] != ")")
        $second_note = note2interval($notes_input[$i+1]);
      }

      // reset interval
      $interval = $first_note - $second_note;

      if ($interval === 0){
        $interval = "=0";
      }
      elseif ($interval > 0){
        $interval = "+".$interval;
      }

      $interval_str = strval($interval);
    }

    $intervals_string = $intervals_string.$interval_str;
  }


return  $intervals_string;
$intervals_without_syl_separator = str_replace(" ","",$intervals_string);

}

// Auxiliary functions
function isNote($notes_input, $i) {
  $increment = 1;
  if ($notes_input[$i + $increment] == " ") {
    $increment = $increment + 1;
    isNote($notes_input, $i, $increment);
  }else{
    return $increment;
  }
}

function note2interval($note)	{
	if ($note === "J"){
			return 32;
		}elseif($note === "A"){
			return 30;
		}elseif ($note === "B"){
			return 29;
		}elseif ($note === "H"){
			return 28;
		}elseif ($note === "C"){
			return 27;
		}elseif ($note === "D"){
			return 25;
		}elseif ($note === "E"){
			return 23;
		}elseif ($note === "F"){
			return 22;
		}elseif ($note === "G"){
			return 20;
		}elseif ($note === "a"){
			return 18;
		}elseif ($note === "b"){
			return 17;
		}elseif ($note === "h"){
			return 16;
		}elseif ($note === "c"){
			return 15;
		}elseif ($note === "d"){
			return 13;
		}elseif ($note === "e"){
			return 11;
		}elseif ($note === "f"){
			return 10;
		}elseif ($note === "g"){
			return 8;
		}elseif ($note === "u") {
			return 6;
		}elseif ($note === "p") {
			return 5;
		}elseif ($note === "q") {
			return 4;
		}elseif ($note === "r") {
			return 2;
		}
	}

?>
