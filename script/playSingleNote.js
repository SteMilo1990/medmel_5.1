musicInputSelected = false;

function playSingleNote(letter){


    let text = letter;
    let strNotes = formatNotes(text);
    let notestxt = getNotes(strNotes);//notes to convert with extractFormattedNote
    let flatNotes = getFlatNotes(strNotes);
    let pattern = getDurationPattern(formatNotes(text, false));

    let pos = [];//position notes
    let notes = [];//notes to play

    pos = getNotePosition(text);

    notes = extractFormattedNotes(notestxt, flatNotes);

    // console.log("txt notes\n"+notestxt);
    //console.log("txt Pos\n"+pos);
     // console.log("notes\n"+notes);
     // console.log("flat : "+flatNotes);
    // console.log("pattern : "+pattern);
    //console.log("Octave\n"+octave);

    let speed = 1;
    let attack = 0;
    let deltaTime = 0;
    // piano.play(notes[index], octave[index], durationNote);
    for(let i = 0; i< notes.length; i = i+1){
        // console.log("thd : "+tmp);
        //console.log("t : "+i + " attack : "+(attack+deltaTime));
        if(i==1) attack = attack + (getDurationFromPattern(pattern[i], speed));

        let tmp = [];
        let t = 0;
        textarea = letter;
        //t = setTimeout(() => moveCursor(textarea, startPosition+pos[i]),  attack);
        tmp.push(t);
        //t = setTimeout(() => textarea.focus(),  attack);
        //tmp.push(t);
        t = setTimeout(() => playSample('Grand Piano', notes[i], getDurationFromPattern(pattern[i], speed)),  attack);
        tmp.push(t);
        t = setTimeout(() => resetUIbuttonPause(),  attack);
        tmp.push(t);
        t = setTimeout(() => schedulerPlay.splice(0,1),  attack);
        tmp.push(t);
        inqueueScheduler(tmp);
        attack = attack + getDurationFromPattern(pattern[i], speed);

        // playNote(notes[i], octave[i], durationNote);
    }
    let tmp = [];
    //add last note duration
    let t = setTimeout(() => resetUIbuttonPlay(),  attack);
    tmp.push(t);
    attack = attack + getDurationFromPattern(pattern[pattern.length-1], speed)+1350;
    t = setTimeout(() => stopMusic(),  attack);
    tmp.push(t);
    inqueueScheduler(tmp);
}


var sound_while_editing = true;

$(document).keyup(function(e) {

    if (sound_while_editing == true) {
        if(musicInputSelected == true){ // got  to set musicContainerSelected

        // LETTERS/NOTES
        // if shift AND capslock
        if (event.shiftKey && event.getModifierState("CapsLock")){
          if (e.which == 65){
            playSingleNote("a");
          }else if (e.which == 66){
            playSingleNote("b");
          }else if (e.which == 72){
            playSingleNote("h");
          }else if (e.which == 67){
            playSingleNote("c");
          }else if (e.which == 68){
            playSingleNote("d");
          }else if (e.which == 69){
            playSingleNote("e");
          }else if (e.which == 70){
            playSingleNote("f");
          }else if (e.which == 71){
            playSingleNote("g");
          }
        // if shift OR capslock
        }else if (event.shiftKey || event.getModifierState("CapsLock")){
           if (e.which == 65){
            playSingleNote("A");
          }else if (e.which == 66){
            playSingleNote("B");
          }else if (e.which == 72){
            playSingleNote("H");
          }else if (e.which == 67){
            playSingleNote("C");
          }else if (e.which == 68){
            playSingleNote("D");
          }else if (e.which == 69){
            playSingleNote("E");
          }else if (e.which == 70){
            playSingleNote("F");
          }else if (e.which == 71){
            playSingleNote("G");
          }

        } else if (event.getModifierState("Alt")){
          if (e.which == 71){
            playSingleNote("*G");
          }else if (e.which == 65){
            playSingleNote("+a");
          }else if (e.which == 66){
            playSingleNote("+b");
          }else if (e.which == 72){
            playSingleNote("+h");
          }

        }else{// if no shift nor capslock
          if (e.which == 65){
            playSingleNote("a");
          }else if (e.which == 66){
            playSingleNote("b");
          }else if (e.which == 72){
            playSingleNote("h");
          }else if (e.which == 67){
            playSingleNote("c");
          }else if (e.which == 68){
            playSingleNote("d");
          }else if (e.which == 69){
            playSingleNote("e");
          }else if (e.which == 70){
            playSingleNote("f");
          }else if (e.which == 71){
            playSingleNote("g");
          }

        }
      }
    }
});
