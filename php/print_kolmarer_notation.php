<?php

// 1 Note
if ($n_of_notes_in_note_group[$i] == 1){
  if ($single_note_shape == 0){
    $class = "uncinus";
    $shape = $uncinus_no_features;
  }else if ($single_note_shape == 1){
    $class = "virga";
    $shape = $virga;
  }else if ($single_note_shape == 2){
    $class = "uncinus";
    $shape = $uncinus_desc;
  }else if ($single_note_shape == 3){
    $class = "uncinus";
    $shape = $uncinus_asc;
  }else if ($single_note_shape == 4){
    $class = "uncinus";
    $shape = $uncinus_20050;
  }
}

//  2 NOTES
elseif ($n_of_notes_in_note_group[$i] == 2){
  // PES
  if ($next_nn != null && $this_nn < $next_nn){ 
    $class = "pes";
    $shape = $uncinus_no_features;
  }
  elseif ($previous_nn != null && $this_nn > $previous_nn){ 
    $class = "pes";
    $shape = $pes_shape;

    if ($ah_type == 1) {
      $shape = $pes_1_grade_type_2;
    }elseif ($ah_type == 2){
      $coeff_x += 6;
    }
  }
  
  // CLIVIS
  // 1st note
  elseif (($next_nn != null and $this_nn > $next_nn) 
  || ($previous_nn != null and $previous_nn > $this_nn)){
    $class = "clivis";

    if ($clivis_type == 0){
      if ($x == 0){
          if ($diff_this_next == 1) {
            $shape = $clivis_note1_deg1;
          }elseif ($diff_this_next == 2) {
            $shape = $clivis_note1_deg2;
          }
      } elseif ($x == 1){
          if ($diff_previous_this == 1) {
            $shape = $clivis_1_grade;
          }elseif ($diff_previous_this == 2) {
            $shape = $clivis_2_grade;
          }
      }
    }
    
    elseif ($clivis_type == 1) {
      if ($x == 0){
          if ($diff_this_next == 1) {
            $shape = $uncinus;
          }elseif ($diff_this_next == 2) {
            $shape = $uncinus;
          }else{
            $shape = $uncinus;
          }
      } elseif ($x == 1){
          if ($diff_previous_this == 1) {
            $shape = $clivis_1_grade;
          }elseif ($diff_previous_this == 2) {
            $shape = $clivis_2_grade;
          }
      }
    }
    
    elseif ($clivis_type == 2) {
      if ($x == 0){
          $shape = $virga;
      } elseif ($x == 1){
          $shape = $uncinus;
      }
    }
  }
  
  // nota ripetuta
  else {
    $class = "uncinus";

    if ($group_type == 0){
      $shape = $uncinus;
    }elseif ($group_type == 1){
      if ($x == 0){
        $shape = $virga_short_slightly_desc;
      }elseif($x == 1){
        $shape = $uncinus_short_slightly_desc; 
      }
    }elseif ($group_type == 2){
      if ($x == 0){
        $shape = $uncinus_short_slightly_desc;
      }elseif($x == 1){
        $shape = $uncinus; $coeff_x -= 7; 
      }    
    }elseif ($group_type == 3){
      if ($x == 0){
        $shape = $virga;
      }elseif($x == 1){
        $shape = $uncinus_short_commalike; 
      } 
    }elseif ($group_type == 4){
      if ($x == 0){
        $shape = $uncinus_short_slightly_desc;$coeff_x += 6;
      }elseif($x == 1){
        $shape = $uncinus_short_commalike; $coeff_x -= 10;
      } 
    }elseif ($group_type == 5){
      if ($x == 0){
        $shape = $uncinus_short_slightly_desc;
      }elseif($x == 1){
        $shape = $uncinus; $coeff_x -= 7; 
      } 
    }
  }
}

// 3 NOTES
elseif ($n_of_notes_in_note_group[$i] == 3){
  if ($nn0 > $nn1 and $nn1 > $nn2){
    $class = "climacus";
    if ($plica == 1 and $x == 2){
      $shape = $plica_disc_after_clivis;
    }elseif($plica_next == 1){
      $shape = $clivis_shape;
      
    }elseif ($climacus_type == 0){
      if ($x == 0){$shape = $virga;}
      elseif ($x == 1){$shape = $uncinus_short_tilted; $coeff_x += 2;}
      elseif ($x == 2){$shape = $uncinus_short_tilted;}
      
    }elseif ($climacus_type == 3){
      if ($x == 0){$shape = $uncinus;}
      elseif ($x == 1){
        if ($diff_previous_this == 1){$shape = $clivis_1_grade;}
        elseif ($diff_previous_this == 2){$shape = $clivis_2_grade;}
      }
      elseif ($x == 2){$shape = $uncinus;}
      
    }elseif ($climacus_type == 4){
      if ($x == 0){$shape = $uncinus;}
      elseif ($x == 1){
        if ($diff_previous_this == 1){$shape = $clivis_1_grade;}
        elseif ($diff_previous_this == 2){$shape = $clivis_2_grade;}
      }
      elseif ($x == 2){
        if ($diff_previous_this == 1){$shape = $clivis_1_grade;}
        elseif ($diff_previous_this == 2){$shape = $clivis_2_grade;}
      }
    }elseif ($climacus_type == 5){
      if ($x == 0){
        if ($diff_this_next == 1){$shape = $clivis_note1_deg1;}
        elseif ($diff_this_next == 2){$shape = $clivis_note1_deg2;}
      }
      elseif ($x == 1){
        if ($diff_previous_this == 1){$shape = $clivis_1_grade;}
        elseif ($diff_previous_this == 2){$shape = $clivis_2_grade;}
      }
      elseif ($x == 2){
        if ($diff_previous_this == 1){$shape = $clivis_1_grade;}
        elseif ($diff_previous_this == 2){$shape = $clivis_2_grade;}
      }
      
    }else{
      $shape = $uncinus;
    }
  }
  elseif ($nn0 < $nn1 and $nn1 < $nn2){
    $class = "scandicus";

    if ($scandicus_type == 0){
      if ($x == 0) {
        $shape = $uncinus;
      } elseif ($x == 1) {
        $shape = $uncinus_short;
      } elseif ($x == 2) {
        $shape = $virga; $coeff_x += 2;
      }
    }	elseif ($scandicus_type == 1){
      if ($x == 0 or $x == 1){
        $shape = $uncinus;
      }	elseif ($x == 2) {
        $shape = $pes_shape;
      }

    }	elseif ($scandicus_type == 2){
        if ($x == 0 or $x == 2) {$shape = $uncinus;}
        elseif ($x == 1) {$shape = $pes_shape;}
      } elseif ($scandicus_type == 3){
        if ($x == 0) {$shape = $uncinus;}
        elseif ($x == 1) {$shape = $pes_shape;}
        elseif ($x == 2) {$shape = $pes_of_scandicus_x2;}
      }

    }	elseif ($nn0 > $nn1 and $nn1 < $nn2) {
      $class = "porrectus";

      if ($x == 0){$shape = $clivis_note1_deg1;}

      elseif ($x == 1){
        if ($diff_previous_this == 1){$shape = $porrectus_note2_grade1;}
        elseif($diff_previous_this == 2){$shape = $clivis_of_porrectus_2deg_path;}

      }	elseif ($x == 2){
        if ($porrectus_type == 0){
          if ($diff_previous_this == -1){$shape = $pes_of_porrectus_note3_deg1;}
          elseif($diff_previous_this == -2){$shape = $pes_of_porrectus_note3_deg2;}
        }elseif ($porrectus_type == 1){
          if ($diff_previous_this == -1){$shape = $pes_of_porrectus2_1deg_path;}
          elseif($diff_previous_this == -2){$shape = $pes_of_porrectus2_2deg_path;}
        }
      }

    }	elseif ($nn0 < $nn1 and $nn1 > $nn2) {
      $class = "torculus";

      if ($x == 0){
        $shape = $uncinus;

      } elseif ($x == 1) {
        if ($diff_previous_this == -1) {$shape = $virga;}
        elseif ($diff_previous_this == -2) {$shape = $pes_2_grades;}

      } elseif ($x == 2) {
        if ($torculus_type == 0) {
          if ($diff_previous_this == 1) {$shape = $clivis_1_grade;}
          elseif ($diff_previous_this == 2) {$shape = $clivis_2_grade; $coeff_x =+5;}
        }else{
          $shape = $uncinus;
        }
      }
    
  }
  // *aaa*
  elseif ($nn0 == $nn1 && $nn0 == $nn2){
    if ($x == 0){
      $shape = $tripunctis_n1;
    }elseif ($x == 1){
      $shape = $tripunctis_n2;
    }elseif ($x == 2){
      $shape = $uncinus_short_commalike;
    }  
  }
  // *aah*
  elseif ($nn0 == $nn1 && $nn1 < $nn2){
      
      if ($x == 0) {
        $shape = $uncinus_short;
      } elseif ($x == 1) {
        $shape = $uncinus;
      } elseif ($x == 2) {
        $shape = $pes_shape;
      }

  }  
  // *aaG*
  elseif ($nn0 == $nn1 && $nn1 > $nn2) {
      if ($x == 0) {
        $shape = $uncinus_short_slightly_desc;$coeff_x+=5;
      } elseif ($x == 1) {
        $shape = $uncinus_short; $coeff_x-=7;
      } elseif ($x == 2) {
        $shape = $clivis_shape;$coeff_x-=3;
      }
  } 
  // *aGG*
  elseif ($nn0 < $nn1 && $nn1 == $nn2) {
      if ($aGG_type == 0){
        if ($x == 0) {
          $shape = $uncinus;
        } elseif ($x == 1) {
          $shape = $pes_shape;
        } elseif ($x == 2) {
          $shape = $punctum_double;
        }
      }elseif ($aGG_type == 1){
        if ($x == 0) {
          $shape = $uncinus;
        } elseif ($x == 1) {
          $shape = $pes_shape;
        } elseif ($x == 2) {
          $shape = $punctum_double_long;
        }
      }elseif ($aGG_type == 2){
        if ($x == 0) {
          $shape = $uncinus;
        } elseif ($x == 1) {
          $shape = $pes_shape;
        } elseif ($x == 2) {
          $shape = $tripunctis_n2;
        }
      }
      
  }

  // all other configurations
  else {
    $shape = $uncinus;
  }


  // COMPLEX CASES

  //  *aGGF*
  } elseif ($n_of_notes_in_note_group[$i] == 4 && $x > 0 && $nn0 > $nn1 && $nn1 == $nn2 && $nn2 > $nn3){

    if($this_nn < $previous_nn && ($x == 1 or $x == 3)){
      $class = "clivis";
      $shape = $clivis_shape;
      
    }else{
      $shape = $uncinus;
    }

    if ($x > 1 && $aGGF_type == 1){
      $coeff_x = -3;
    }else{
      $coeff_x = 0;
    }
  }

  // // pes for *ahha*
  // elseif ($n_of_notes_in_note_group[$i] == 4 && $x==1 && $previous_nn < $this_nn && $nn1 == $nn2
  //     // && $ahha_type != 1
  // ){
  //   if($this_nn > $previous_nn){
  //     $shape = $pes_shape;
  //   }else{
  //     $shape = $uncinus;
  //   }
  // }
  // 
  // // clivis for *ahha*
  // elseif ($n_of_notes_in_note_group[$i] == 4 && $x==3 && $nn0 < $nn1 && $nn1 == $nn2 && $nn2 > $nn3){
  //     $shape = $clivis_shape;
  // }
  // 
  // // pes for *aGGa*
  // elseif ($n_of_notes_in_note_group[$i] == 4 && ($x == 3 or $x == 1) && $nn0 > $nn1 && $nn1 == $nn2 && $nn2 < $nn3){
  //   if($this_nn > $previous_nn){
  //     $shape = $pes_shape;
  //   }else{
  //     $shape = $clivis_shape;
  //   }
  // }
  // 
  // // pes for *aGFG*
  // elseif ($n_of_notes_in_note_group[$i] == 4 && $x == 3 && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 < $nn3 && $aGFG_type == 1){
  //     $shape = $pes_shape;
  // }
  // elseif ($n_of_notes_in_note_group[$i] == 4 && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 < $nn3 && ($aGFG_type == 2 || $aGFG_type == 3)){
  //     if ($x == 0 || $x == 1)
  //       $shape = $uncinus;
  //     elseif ($x == 2)
  //       $shape = $clivis_of_porrectus_1deg_path;
  //     elseif ($x == 3) {
  //       if ($aGFG_type == 2)
  //         $shape = $pes_of_porrectus_1deg_path;
  //       elseif ($aGFG_type == 3)
  //         $shape = $pes_of_porrectus2_1deg_path;
  //     }
  // }
  // 
  // // ahc(h)
  // elseif ($n_of_notes_in_note_group[$i] == 4 && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 > $nn3 && $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][3] == 1 && ($ahchPlica_type == 1 || $ahchPlica_type == 2)){
  //   if ($x == 0) {$shape = $uncinus;}
  //   if ($ahchPlica_type == 1) {
  //     if ($x == 1) {$shape = $uncinus;}
  //     elseif ($x == 2) {$shape = $uncinus;}
  //   }
  //   elseif ($ahchPlica_type == 2) {
  //     if ($x == 1) {$shape = $pes_shape;}
  //     elseif ($x == 2) {$shape = $pes_of_scandicus_x2;}
  //   }
  // }
  // 
  // // pes and clivis for *ahch* (w/ scandicus)
  // elseif ($n_of_notes_in_note_group[$i] == 4 && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 > $nn3 && ($ahch_type == 1 || $ahch_type == 2)){
  //   if ($ahch_type == 1){
  //     if ($x == 0) {$shape = $uncinus;}
  //     elseif ($x == 1) {$shape = $pes_shape;}
  //     elseif ($x == 2) {$shape = $pes_shape;}
  //     elseif ($x == 3) {$shape = $uncinus;}
  //   }elseif($ahch_type == 2) {
  //     if ($x == 0) {$shape = $uncinus;}
  //     elseif ($x == 1) {$shape = $pes_shape;$coeff=2;$coeff_x=-1.3;}
  //     elseif ($x == 2) {$shape = $pes_of_torculus_1deg_path;$coeff=0;$coeff_x=0.3;}
  //     elseif ($x == 3) {$shape = $clivis_of_torculus_1deg_path;}
  //   }
  // }
  // 
  //   // check if torculus in 4-notes neume
  //   elseif ($n_of_notes_in_note_group[$i] == 4 && ($x == 2 or $x == 3) && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 > $nn3){
  //   if ($x == 2){
  //     if ($diff_previous_this == -1){$shape = $pes_of_torculus_1deg_path;}
  //     elseif ($diff_previous_this == -2){$shape = $pes_of_porrectus_2deg_path;}
  //   }elseif($x == 3){
  //     if ($diff_previous_this == 1){$shape = $clivis_of_torculus_1deg_path;}
  //     elseif ($diff_previous_this == 2){$shape = $clivis_of_torculus_2deg_path;}
  //   }
  // }
  // 
  // // *aaGF*
  // elseif($n_of_notes_in_note_group[$i] == 4 && $x > 0 && $nn0 == $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $aaGF_type == 1){
  //   $coeff_x = -1;
  //   if ($x == 2){
  //     $shape = $clivis_shape;
  //   }elseif ($x == 3){
  //     $shape = $clivis_shape;
  //   }
  // }
  // 
  // // *aGah*
  // elseif($n_of_notes_in_note_group[$i] == 4 && $nn0 > $nn1 && $nn1 < $nn2 && $nn2 < $nn3 && $aGah_type == 0){
  //   if ($x == 0){
  //     $shape = $uncinus;
  //   }elseif ($x == 1){
  //     $shape = $clivis_of_porrectus_1deg_path;
  //   }elseif ($x == 2){
  //     $shape = $pes_of_porrectus_1deg_path;
  //   }elseif ($x == 3){
  //     $shape = $pes_shape; 
  //     $coeff_x = 0.3; 
  //     $coeff = -0.7;
  //   }
  // }elseif($n_of_notes_in_note_group[$i] == 4 && $nn0 > $nn1 && $nn1 < $nn2 && $nn2 < $nn3 && $aGah_type == 1){
  //   if ($x == 0){
  //     $shape = $uncinus;
  //   }elseif ($x == 1){
  //     $shape = $clivis_shape;
  //   }elseif ($x == 2){
  //     $shape = $uncinus;
  //   }elseif ($x == 3){
  //     $shape = $pes_1_grade; $coeff_x = 0.3; $coeff = -0.7;
  //   }
  // }
  // 
  // *ahaG* type 0 // Kolmarer
  elseif ($n_of_notes_in_note_group[$i] == 4 && $x > 0 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $ahaG_type == 0){
    if ($x == 0) {$shape = $uncinus;}
    elseif ($x == 1) {$shape = $pes_1_grade;}
    elseif ($x == 2) {$shape = $uncinus_short_tilted;}
    elseif ($x == 3) {$shape = $uncinus_short_tilted;}
  }
  // *aEacecah* // kolmarer
  elseif ($n_of_notes_in_note_group[$i] == 8 && 
    $nn0 > $nn1 && // a>E
    $nn1 < $nn2 && // E<a
    $nn2 < $nn3 && // a<c
    $nn3 < $nn4 && // c<e
    $nn4 > $nn5 && // e>c
    $nn5 > $nn6 && // c>a
    $nn6 < $nn7    // a<h
  ){
    if ($x == 0){$shape = $pes_deg2_short;}
    elseif ($x == 1){$shape = $clivis_deg4;}
    elseif ($x == 2){$shape = $pes_deg4;}
    elseif ($x == 3){$shape = $pes_deg2_short;}
    elseif ($x == 4){$shape = $pes_deg2_short;}
    elseif ($x == 5){$shape = $uncinus_vertical;}
    elseif ($x == 6){$shape = $uncinus;}
    elseif ($x == 7){$shape = $virga;}
  }
  // *ahdfeed* // kolmarer
  elseif ($n_of_notes_in_note_group[$i] == 7 && 
    $nn0 < $nn1 && // ah
    $nn1 < $nn2 && // hd
    $nn2 < $nn3 && // df
    $nn3 > $nn4 && // fe
    $nn4 == $nn5 && // ee
    $nn5 > $nn6    // ed
  ){
    if ($x == 0){$shape = $uncinus_short;}
    elseif ($x == 1){$shape = $uncinus_short;}
    elseif ($x == 2){$shape = $pes_of_porrectus_note3_deg2;}
    elseif ($x == 3){$shape = $pes_of_porrectus_note3_deg2;}
    elseif ($x == 4){$shape = $clivis_1_grade;}
    elseif ($x == 5){$shape = $uncinus; $coeff_x -= 4;}
    elseif ($x == 6){$shape = $clivis_1_grade;}
  }
  
  // // *ahaG type 1*.
  // elseif ($n_of_notes_in_note_group[$i] == 4 && $x > 0 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $ahaG_type == 1){
  //   if ($x == 1) {$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 2) {$shape = $clivis_of_torculus_1deg_path;}
  //   elseif ($x == 3) {
  //     if ($diff_previous_this == 1) {$shape = $clivis_1_grade_preceeded_by_clivis; $coeff_x = -4;}
  //     elseif ($diff_previous_this == 2) {$shape = $clivis_of_torculus_ahaG_2deg_path;} // non funziona
  //     elseif ($diff_previous_this == 3) {$shape = $clivis_of_torculus_ahaG_3deg_path;} // non funziona
  //     elseif($diff_previous_this == 4){$class= "clivis_of_torculus_ahaG_4deg_path";
  //       $shape = $clivis_of_torculus_ahaG_4deg_path;}
  //   }
  // }
  // // *ahaG* type 3
  // elseif($n_of_notes_in_note_group[$i] == 4 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $ahaG_type == 3){
  //   if ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $uncinus;}
  //   elseif ($x == 2) {$shape = $clivis_shape;}
  //   elseif ($x == 3) {$shape = $clivis_1_grade_preceeded_by_clivis;}
  // }
  // 
  // // *ahaG* default and type 2
  // elseif($n_of_notes_in_note_group[$i] == 4 && $x > 0 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 > $nn3){
  //     if ($x == 1 and $ahaG_type != 2) {$shape = $pes_shape;}
  //     elseif ($x > 1) {$shape = $uncinus;}
  // }
  // 
  // elseif($n_of_notes_in_note_group[$i] == 4 && $x > 0 && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 > $nn3){
  //     if ($x == 1 and $aGFE_type == 1) {$shape = $clivis_shape;}
  //     elseif ($x > 1) {$shape = $uncinus;}
  // }
  // 
  // // *aGaG*
  // elseif($n_of_notes_in_note_group[$i] == 4 && $nn0 > $nn1 && $nn1 < $nn2 && $nn2 > $nn3 && $aGaG_type == 1){
  //     if ($x == 0) {$shape = $uncinus;}
  //     if ($x == 1) {$shape = $clivis_of_porrectus_1deg_path;}
  //     if ($x == 2) {$shape = $pes_of_porrectus_flexus;}
  //     if ($x == 3) {$shape = $clivis_of_torculus_1deg_path;}
  // }
  // 
  // // *ahcch*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 == $nn3 && $nn3 > $nn4){
  //   $class = "ahcch";
  //   if     ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $pes_shape;}
  //   elseif ($x == 2) {$shape = $pes_shape;}
  //   elseif ($x == 3) {$shape = $uncinus;}
  //   elseif ($x == 4) {$shape = $clivis_shape;}
  // }
  // 
  // // $aGFGF
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 < $nn3 && $nn3 > $nn4 && $aGFGF_type == 1){
  //   $class = "aGFGF";
  //   if     ($x == 4) {$shape = $clivis_shape;}
  //   else  {$shape = $uncinus;}
  // }
  // 
  // // *ahaaG*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 == $nn3 && $nn3 > $nn4){
  //   $class = "ahaaG";
  //   if     ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 2) {$shape = $clivis_of_torculus_1deg_path;}
  //   elseif ($x == 3) {$shape = $uncinus; $coeff_x = -5;}
  //   elseif ($x == 4) {$shape = $clivis_shape;}
  // }
  // 
  // // *aGaha*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 > $nn1 && $nn1 < $nn2 && $nn2 < $nn3 && $nn3 > $nn4){
  //   $class = "aGaha";
  //   if     ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $clivis_shape;}
  //   elseif ($x == 2) {$shape = $uncinus;}
  //   elseif ($x == 3) {$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 4) {$shape = $clivis_of_torculus_1deg_path;}
  // }
  // 
  // // *ahcha*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && (
  //   $ahcha_type == 1 || $ahcha_type == 2 || 
  //   $ahcha_type == 3 || $ahcha_type == 4 || 
  //   $ahcha_type == 5 || $ahcha_type == 6)) {
  //   $class = "ahcha";
  // 
  //   if ($ahcha_type == 1 || $ahcha_type == 3 || $ahcha_type == 5){
  //     if     ($x == 0) {$shape = $uncinus;}
  //     elseif ($x == 1) {
  //       if ($ahcha_type == 5){
  //         $shape = $pes_shape;
  //       }else{
  //         $shape = $uncinus;
  //       }
  //     }
  //     elseif ($x == 2) {$shape = $pes_of_torculus_1deg_path;}
  //     elseif ($x == 3) {$shape = $clivis_of_torculus_1deg_path;}
  //     elseif ($x == 4) {
  //       if ($ahcha_type == 3){
  //         $shape = $uncinus;
  //       }
  //       elseif ($nn3 - $nn4 == 2){
  //         $shape = $clivis_2_grade; $coeff_x = -5;
  //       }
  //       else {
  //         $shape = $clivis_1_grade_preceeded_by_clivis; $coeff_x = -3;
  //       }
  //     }
  //   } elseif ($ahcha_type == 2){
  //       if     ($x == 0) {$shape = $uncinus;}
  //       elseif ($x == 1) {$shape = $pes_shape;}
  //       elseif ($x == 2) {$shape = $pes_shape;}
  //       elseif ($x == 3) {$shape = $uncinus;}
  //       elseif ($x == 4) {$shape = $uncinus;}
  // 
  //   } elseif ($ahcha_type == 4){
  //       if     ($x == 0) {$shape = $uncinus;}
  //       elseif ($x == 1) {$shape = $pes_shape;}
  //       elseif ($x == 2) {$shape = $pes_shape;}
  //       elseif ($x == 3) {$shape = $uncinus;}
  //       elseif ($x == 4) {$shape = $clivis_note2_deg1;}
  //   }elseif ($ahcha_type == 6){
  //       if   ($x == 2) {$shape = $pes_shape;}
  //       else {$shape = $uncinus;}
  //   }
  // }
  // 
  // // *ahaGa*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $nn3 < $nn4 && $ahaGa_type == 1 && $x == 4){
  //   $class = "ahaGa";
  //   $shape = $pes_shape;
  // }
  // 
  // // *ahaGF*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && $x == 1){
  //   $class = "ahaGF";
  //   $shape = $pes_shape;
  // }
  // 
  // // *aGFEF*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $nn3 < $nn4 && $aGFEF_type == 1 && $x == 4){
  //   $class = "aGFEF";
  //   $shape = $pes_shape;
  // }
  // 
  // // *aaGFG*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 == $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $nn3 < $nn4 && $aaGFG_type == 1 && $x == 4){
  //   $class = "aaGFG";
  //   $shape = $pes_shape;
  // }
  // 
  // // aaGF(G)
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 == $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][4] == 1){
  //   if ($x == 0) {$shape = $uncinus;}
  //   if ($x == 1) {$shape = $uncinus; $coeff_x = -0.5;}
  //   if ($x == 2) {$shape = $clivis_shape;}
  //   if ($x == 3) {$shape = $clivis_shape;}
  //   if ($x == 4) {$shape = $plica_asc_path_curved; $coeff_x = -6;}
  // }
  // 
  // // ahcdc
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 < $nn3 && $nn3 > $nn4 && $ahcdc_type == 1){
  //   if ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $uncinus;}
  //   elseif ($x == 2) {$shape = $pes_shape; $coeff=2;$coeff_x=-1;}
  //   elseif ($x == 3) {$shape = $pes_of_torculus_1deg_path;$coeff=0.3;$coeff_x=-1;}
  //   elseif ($x == 4) {$shape = $clivis_of_torculus_1deg_path;}
  // }
  // 
  // // *ahhaaG*
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 < $nn1 && $nn1 == $nn2 && $nn2 > $nn3 && $nn3 == $nn4 && $nn4 > $nn5){
  //   if ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 2) {$shape = $uncinus; $coeff_x = -0.5;}
  //   elseif ($x == 3) {$shape = $clivis_shape;}
  //   elseif ($x == 4) {$shape = $uncinus;}
  //   elseif ($x == 5) {$shape = $clivis_shape;}
  // }
  // 
  // // aGahaF
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 > $nn1 && $nn1 < $nn2 && $nn2 < $nn3 && $nn3 > $nn4 && $nn4 > $nn5){
  //   if ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $clivis_shape;}
  // elseif ($x == 2) {$shape = $uncinus; $coeff_x = 2;}
  //   elseif ($x == 3) {$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 4) {$shape = $clivis_of_torculus_1deg_path;}
  //   elseif ($x == 5) {$shape = $clivis_shape;$coeff_x = -4;}
  // }
  // // aahcha
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 == $nn1 && $nn1 < $nn2 && $nn2 < $nn3 && $nn3 > $nn4 && $nn4 > $nn5){
  //   if ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $uncinus; $coeff_x = -1;}
  //   elseif ($x == 2) {$shape = $pes_shape;}
  //   elseif ($x == 3) {$shape = $pes_of_scandicus_x2;}
  //   elseif ($x == 4) {$shape = $uncinus;}
  //   elseif ($x == 5) {$shape = $uncinus;}
  // }
  // 
  // // aGFEFG
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $nn3 < $nn4 && $nn4 < $nn5){
  //   if ($x == 5) {$shape = $pes_shape;}
  //   else{$shape = $uncinus;}
  // }
  // 
  // // aGFEFG
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && $nn4 > $nn5){
  //   if ($x == 1) {$shape = $pes_shape;}
  //   else{$shape = $uncinus;}
  // }
  // // aba-aG
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 < $nn1 && $nn1 > $nn2 && $nn2 == $nn4 &&  $nn4 > $nn5){
  //   if ($x == 1) {$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 2) {$shape = $clivis_of_torculus_1deg_path;}
  //   elseif ($x == 5) {$shape = $clivis_shape;}
  //   else{$shape = $uncinus;}
  // }
  // 
  // 
  // // *ahchGG*
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && $nn4 == $nn5){
  //   if ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1) {$shape = $uncinus;}
  //   elseif ($x == 2) {$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 3) {$shape = $clivis_of_torculus_1deg_path;}
  //   elseif ($x == 4) {$shape = $clivis_of_torculus_ahaG_2deg_path;}
  //   elseif ($x == 5) {$shape = $uncinus; $coeff_x = -5;}
  // }
  // 
  // // *aGGFED*
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 > $nn1 && $nn1 = $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && $nn4 > $nn5){
  //   if ($x == 1) {$shape = $clivis_shape;}
  //   else {$shape = $uncinus;}
  // }
  // 
  // // *aGGFE*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && ($x == 1 or $x == 3 or $x == 4) && $nn0 > $nn1 && $nn1 == $nn2 && $nn2 > $nn3 && $nn3 > $nn4){
  //     if ($x == 1){$shape = $clivis_shape;}
  //   if ($aGGFE_type == 1){
  //     if ($x == 3) {
  //       $shape = $clivis_shape;
  //     }elseif ($x == 4){
  //       $shape = $clivis_shape;
  //     }
  //   }else{} // this always prints uncini, so it works
  // }
  // // *aGFGa*
  // elseif ($n_of_notes_in_note_group[$i] == 5 && ($x == 3 or $x == 4) && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 < $nn3 && $nn3 < $nn4 && $aGFGa_type == 0){
  //   $shape = $pes_shape;
  // }
  // elseif ($n_of_notes_in_note_group[$i] == 5 && $x == 4 && $nn0 > $nn1 && $nn1 > $nn2 && $nn2 < $nn3 && $nn3 < $nn4 && $aGFGa_type == 1){
  //   $shape = $pes_shape;
  // }
  // // *abcbaG*
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 < $nn1 && $nn1 < $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && $nn4 > $nn5){
  //   if ($x == 2) {$shape = $pes_shape;}
  //   else {$shape = $uncinus;}
  // }
  // 
  // // *aFGFEF*
  // elseif ($n_of_notes_in_note_group[$i] == 6 && $nn0 > $nn1 && $nn1 < $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && $nn4 < $nn5){
  //   if ($x == 1) {$shape = $clivis_of_porrectus_2deg_path;}
  //   elseif ($x == 2) {$shape = $pes_of_porrectus_1deg_path;}
  //   elseif ($x == 5) {$shape = $pes_shape;}
  //   else {$shape = $uncinus;}
  // }
  // 
  // // *aGahchG*
  // elseif ($n_of_notes_in_note_group[$i] == 7
  //   && $nn0 > $nn1
  //   && $nn1 < $nn2
  //   && $nn2 < $nn3
  //   && $nn3 < $nn4
  //   && $nn4 > $nn5
  //   && $nn5 > $nn6) {
  //   if ($x == 0) {$shape = $uncinus;}
  //   elseif ($x == 1){$shape = $clivis_shape;}
  //   elseif ($x == 2){$shape = $uncinus; }
  //   elseif ($x == 3){$shape = $pes_for_aGahchG;}
  //   elseif ($x == 4){$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 5){$shape = $clivis_of_torculus_1deg_path;}
  //   elseif ($x == 6){$shape = $clivis_of_torculus_ahaG_2deg_path;}
  // }
  // 
  // // *aGFGaGF*
  // elseif ($n_of_notes_in_note_group[$i] == 7
  //   && $nn0 > $nn1
  //   && $nn1 > $nn2
  //   && $nn2 < $nn3
  //   && $nn3 < $nn4
  //   && $nn4 > $nn5
  //   && $nn5 > $nn6) {
  //   if 	   ($x == 0){$shape = $uncinus;}
  //   elseif ($x == 1){$shape = $clivis_shape;}
  //   elseif ($x == 2){$shape = $clivis_shape; }
  //   elseif ($x == 3){$shape = $uncinus;}
  //   elseif ($x == 4){$shape = $uncinus;}
  //   elseif ($x == 5){$shape = $clivis_shape;}
  //   elseif ($x == 6){$shape = $clivis_shape; }
  // }
  // 
  // /*aGabaaG*/
  // elseif ($n_of_notes_in_note_group[$i] == 7
  //   && $nn0 > $nn1
  //   && $nn1 < $nn2
  //   && $nn2 < $nn3
  //   && $nn3 > $nn4
  //   && $nn4 == $nn5
  //   && $nn5 > $nn6) {
  //   if 	   ($x == 0){$shape = $uncinus;}
  //   elseif ($x == 1){$shape = $clivis_shape;}
  //   elseif ($x == 2){$shape = $uncinus; }
  //   elseif ($x == 3){$shape = $pes_of_torculus_1deg_path;}
  //   elseif ($x == 4){$shape = $clivis_of_torculus_1deg_path;}
  //   elseif ($x == 5){$shape = $uncinus; $coeff_x = -3;}
  //   elseif ($x == 6){$shape = $clivis_shape; }
  // }
  // 
  // // *aGGFEDCB*
  // elseif ($n_of_notes_in_note_group[$i] == 8 && $x > 0 && $x < 6 && $nn0 > $nn1 && $nn1 == $nn2 && $nn2 > $nn3 && $nn3 > $nn4 && $nn4 > $nn5) {
  //   if ($x == 1){$shape = $clivis_shape;}
  //   elseif ($x == 2){$shape = $uncinus; $coeff_x = -2;}
  //   elseif ($x == 3){$shape = $clivis_shape;}
  //   elseif ($x == 4){$shape = $clivis_shape; $coeff_x = -4;}
  //   elseif ($x == 5){$shape = $uncinus; $coeff_x = -8;}
  // 
  // }
  // 
  // // *aGGFGaGF*
  // elseif ($n_of_notes_in_note_group[$i] == 8 && $nn0 > $nn1 && $nn1 == $nn2 && $nn2 > $nn3 && $nn3 < $nn4 && $nn4 < $nn5 && $nn5 > $nn6 && $nn6 > $nn7) {
  //   if ($x == 1 or $x == 6 or $x == 7){$shape = $clivis_shape;}
  //   elseif ($x == 2){$shape = $uncinus;}
  //   elseif ($x == 3){$shape = $clivis_shape;}
  //   elseif ($x == 4){$shape = $clivis_shape;$coeff_x = 5;}
  //   elseif ($x == 5){$shape = $uncinus;$coeff_x = 4;}
  // }
  // 
  // // *aGGFFEDCB*
  // elseif ($n_of_notes_in_note_group[$i] == 9 && $x > 0 && $x < 6) {
  //   if ($x == 1){$shape = $clivis_shape;}
  //   elseif ($x == 2){$shape = $uncinus; $coeff_x = -2;}
  //   elseif ($x == 3 or $x == 5){$shape = $clivis_shape;}
  //   elseif ($x == 4){$shape = $uncinus; $coeff_x = -4;}
  // }
  // 
  // // *aGahaGF*
  // elseif ($n_of_notes_in_note_group[$i] == 7 && $nn0 > $nn1 && $nn1 < $nn2 && $nn2 < $nn3 && $nn3 > $nn4 && $nn4 > $nn5 && $nn5 > $nn6 && $x > 0 && $x < 4){
  //   if ($x == 1){
  //     $shape = $clivis_shape;
  //   }elseif($x == 3){
  //     $shape = $pes_shape;
  //   }elseif ($x == 2){
  //     $shape = $uncinus;
  //     $coeff_x = 3;
  //   }
  // }
  // 
  else {
    $class = "uncinus";
    $shape = $uncinus;
  }
  // 
  // // 3 notes
  // 
  // // PLICA - modifies previous calculations
  // if ($Plica_notes_groups_ar[$line][$i-$n_of_keys_in_line][$x] == 1){
  //   $plica_diff = $this_nn - $previous_nn;
  // 
  //   if ($this_nn < $previous_nn){
  //     $class = "plica";
  // 
  //     if ($shape == $plica_disc_after_clivis){
  //       $shape = $plica_disc_after_clivis;
  //     }else{
  //         if ($diff_previous_this == 1){
  //           $shape = $plica_disc_path;
  // 
  //         }else if ($diff_previous_this == 2){
  //           $shape = $plica_disc_2deg_path;
  //         }
  //       }
  //   }else{
  //     if ($shape == $plica_asc_path_curved) {
  //       $shape = $plica_asc_path_curved;
  //     }else{
  //       $shape = $plica_asc_path;
  //     }
  //   }
  // }


  /*****
  * Printing note
  ******/
  $x_adjustment = -23 + $coeff_x;
  $npos[$x] = $npos[$x] -3;
  
  echo "<g class='note$noteCount'";
  echo " transform='matrix(0.2, 0, 0, 0.2, ";
  echo (7 * $x + $x_adjustment) . ", ";
  echo $npos[$x];
  echo ")'>";
  echo "<path id='note.".$line.".".$i.".".$x."'";
  echo " d='".$shape."'></path></g>";
