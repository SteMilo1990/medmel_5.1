var schedulerPlay = [];


function stopMusic(){
    if(audioContext.state === 'running'){
        stop();
        cleanScheduler();
        //updateStaves();
    }
}

function playMusic(){

    let textarea = document.getElementById("music_input");
    let startPosition = textarea.selectionEnd;
    let checkEnd = textarea.value.substring(textarea.selectionEnd, textarea.value.length);
    if(startPosition == textarea.value.length || !checkEnd.replace(/\s/g, '').length){
        startPosition = 0;
        textarea.selectionEnd = 0;
        textarea.selectionStart = 0;
    }

    let text = textarea.value.substring(textarea.selectionEnd, textarea.value.length);
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

    let speed = document.getElementById("speed").value;
    let attack = 0;
    let deltaTime = 0;
    // piano.play(notes[index], octave[index], durationNote);
    for(let i = 0; i< notes.length; i = i+1){
        // console.log("thd : "+tmp);
        //console.log("t : "+i + " attack : "+(attack+deltaTime));
        if(i==1) attack = attack + (getDurationFromPattern(pattern[i], speed));

        let tmp = [];
        let t = 0;
        t = setTimeout(() => moveCursor(textarea, startPosition+pos[i]),  attack);
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

function getDurationFromPattern(id, s){
    let time = 180*(2-s);
    if(id == 1) time = (180*(2-s));
    if(id== 2) time = (400*(2-s));
    if(id == 4) time = (779*(2-s));
    return time
}
function cleanScheduler(){
    for(let i = 0; i< schedulerPlay.length; i = i+1){
        for(let j = 0; j< schedulerPlay[i].length; j = j+1){
            clearTimeout(schedulerPlay[i][j]);
            // console.log("clean : "+schedulerPlay[i][j]+ " - pos : ["+i+"]["+j+"]");
        }
    }
    schedulerPlay = [];
}
function inqueueScheduler(job){
    schedulerPlay.push(job);
}


function moveCursor(txt, i){
    try{
        let prevHight = document.getElementsByClassName("selectedNote");
        if(prevHight != null && String(prevHight) != 'undefined')
            prevHight[0].classList.remove('selectedNote');
    }catch{}
    txt.selectionEnd = i+1;
    txt.selectionStart = i+1;
    txt.blur();
    txt.focus();
    highlightSelectedNote();
    autoscrollStaves();
}

function formatNotes(str, countSlash=true){
    let strNotes = String(str);

    //Inserimento separatrore
    strNotes = strNotes.replace(/\n/g, '|');

    //rimuove le chiavi
    strNotes = strNotes.replace(/[CFG][1-9] ?|[bh]} ?/gmi, "");

    //codifica casi speciali
    strNotes = strNotes.replace(/\+a/g, 'u');
    strNotes = strNotes.replace(/\+b/g, 'p');
    strNotes = strNotes.replace(/\+h/g, 'q');
    strNotes = strNotes.replace(/\+c/g, 'r');
    strNotes = strNotes.replace(/\+d/g, 's');
    strNotes = strNotes.replace(/\*G/g, 'J');

    if (currentStyle == 1) {
      strNotes = strNotes.replace(/\|+/g, ' ');
      strNotes = strNotes.replace(/\'+/g, '|');
    }
    //rimuove tutto ciò che non fa parte dell'espressione regolare
    strNotes = strNotes.replace(/[^a-hA-H| |_|\|u|p|q|r|s|J|/|-]+/g, '');
    //rimuove spazi multipli
    strNotes = strNotes.replace(/  +/g, ' ');

    //rimuove lo spazio tra i separatori
    strNotes = strNotes.replace(/ \|/g, '\|');
    strNotes = strNotes.replace(/\| /g, '\|');

    strNotes = strNotes.replace(/\r/g, '');
    strNotes = strNotes.replace(/\n/g, '');
    strNotes = strNotes.replace(/-/g, '');
    if (countSlash == false){
      strNotes = strNotes.replace(/\n/g, '');
    }
    return strNotes.trim();
}
function getFlatNotes(notes){
    //rimuove tutto ciò che non fa parte dell'espressione regolare
    let cleanNotes = notes.replace(/[^a-hA-H|u|p|q|s|J|/|_/-]+/g, '');
    cleanNotes = cleanNotes.replace(/\|/g, '');
    let flat = cleanNotes.replace(/_a|_b|_c|_d|_e|_f|_g|_h|_J|_A|_B|_C|_D|_E|_F|_G|_H|_u|_p|_q|_r|s/g, '1');
    // Change all other notes to value "0"
    flat = flat.replace(/a|b|c|d|e|f|g|h|J|A|B|C|D|E|F|G|H|u|p|q|r|s/g, '0');
    return flat;
}
function getNotes(notes){
    //rimuove tutto ciò che non fa parte dell'espressione regolare
    let cleanNote = notes.replace(/[^a-hA-H|u|p|q|r|s|J|-]+/g, '');
    cleanNote = cleanNote.replace(/\|/g, '');
    //alert("getNote: "+cleanNote);
    return cleanNote;
}
function getDurationPattern(notes){
    //rimuove tutto ciò che non fa parte dell'espressione regolare

    let cleanNote = notes.replace(/[^a-hA-H|u|p|q|r|s|J|'|\ |-|\/]+/g, '');

    cleanNote = cleanNote.replace(/_/g, '');
    let pattern = cleanNote.replace(/J|A|B|H|C|D|E|F|G|a|b|h|c|d|e|f|g|u|p|q|r|s/g, '0');
    pattern = pattern.replace(/0\|/g, '4');
    pattern = pattern.replace(/0\//g, '4');
    pattern = pattern.replace(/0 /g, '2 ');
    pattern = pattern.replace(/0\/ /g, '4 ');
    pattern = pattern.replace(/ 02 /g, ' 11 ');
    pattern = pattern.replace(/ 02 /g, ' 11 '); // necessary for cases like 00 00 00, which without this repetition become 11 02 11
    pattern = pattern.replace(/0/g, '1');
    pattern = pattern.replace(/ /g, '');
    pattern = pattern.replace(/.$/, '8');
    pattern = pattern.split('');
    return pattern;
}

function getNotePosition(text){
    let pos = [];
    //rimuove le chiavi
    let tmpNote = text;
    //tmpNote = tmpNote.replace(/C1 |C2 |C3 |C4 |C5 |C6 |C7 |C8 |C9 |C0 |b} |h} /g, '00 ');
    //tmpNote = tmpNote.replace(/C1|C2|C3|C4|C5|C6|C7|C8|C9|C0|b}|h}/g, '00');
    //tmpNote = tmpNote.replace(/F1 |F2 |F3 |F4 |F5 |F6 |F7 |F8 |F9 |F0 /g, '00 ');
    //tmpNote = tmpNote.replace(/F1|F2|F3|F4|F5|F6|F7|F8|F9|F0/g, '00');
    //tmpNote = tmpNote.replace(/G1 |G2 |G3 |G4 |G5 |G6 |G7 |G8 |G9 |G0 /g, '00 ');
    //tmpNote = tmpNote.replace(/G1|G2|G3|G4|G5|G6|G7|G8|G9|G0/g, '00');
    //tmpNote = tmpNote.replace(/1|2|3|4|5|6|7|8|9|0/g, '0');
    tmpNote = tmpNote.replace(/[CFGAD][1-9] |[bh]} /gi, "00 ");
    tmpNote = tmpNote.replace(/[CFGAD][1-9]|[bh]}/gi, "00");
    tmpNote = tmpNote.replace(/[1-9]/g, "0");    

    for (let i = 0; i < tmpNote.length; i = i+1){
        let match = /a|b|c|d|e|f|g|h|A|B|C|D|E|F|G|H/.exec(tmpNote.charAt(i));
        if (match) {
            pos.push(i);
        }
    }

    return pos;
}

function extractFormattedNotes(note, flat){

    let notes = [];

    for (let i = 0; i < note.length; i = i+1){

        notes.push(getNoteValuePlayer(note[i], flat[i]));
    }
    return notes;

}

function getNoteValuePlayer(char, flat){
    let note = char;
    let octave = 0 ;
    try{
        octave = document.getElementById("octave").value;
        octave = parseInt(octave);
    }catch{}
    if (note == "J") {
      note = "G"+octave;
    }else if(note == "A") {
      note = "A"+octave;
    }else if(note == "B") {
      note = "Bb"+octave;
    } else if(note == "H" && flat == "1") {
      note = "Bb"+octave;
    }else if(note == "H") {
      note = "B"+octave;
    }else if(note == "C") {
      note = "C"+(octave+1);
    }else if(note == "D") {
      note = "D"+(octave+1);
    }else if(note == "E") {
      note = "E"+(octave+1);
    }else if(note == "F") {
      note = "F"+(octave+1);
    }else if(note == "G") {
      note = "G"+(octave+1);
    }else if(note == "a") {
      note = "A"+(octave+1);
    }else if(note == "b") {
      note = "Bb"+(octave+1);
    }else if(note == "h" && flat == "1") {
      note = "Bb"+(octave+1);
    }else if(note == "h") {
      note = "B"+(octave+1);
    }else if(note == "c") {
      note = "C"+(octave+2);
    }else if(note == "d") {
      note = "D"+(octave+2);
    }else if(note == "e") {
      note = "E"+(octave+2);
    }else if(note == "f") {
      note = "F"+(octave+2);
    }else if(note == "g" && flat == 1) {
      note = "F#"+(octave+2);
    }else if(note == "g") {
      note = "G"+(octave+2);
    }else if(note == "u") {
      note = "A"+(octave+2);
    }else if(note == "p") {
      note = "Bb"+(octave+2);
    }else if(note == "q") {
      note = "B"+(octave+2);
    } else if(note == "r") {
      note = "C"+(octave+3);
    } else if(note == "s") {
      note = "D"+(octave+3);
    }
    return note;

}
//***************PLAYER STREAM***************************************************

const SAMPLE_LIBRARY = {
  'Grand Piano': [
    { note: 'A',  octave: 0, file: 'Samples/Grand Piano/A0.mp3' },
    { note: 'A',  octave: 1, file: 'Samples/Grand Piano/A1.mp3' },
    { note: 'A',  octave: 2, file: 'Samples/Grand Piano/A2.mp3' },
    { note: 'A',  octave: 3, file: 'Samples/Grand Piano/A3.mp3' },
    { note: 'A',  octave: 4, file: 'Samples/Grand Piano/A4.mp3' },
    { note: 'A',  octave: 5, file: 'Samples/Grand Piano/A5.mp3' },
    { note: 'A#',  octave: 0, file: 'Samples/Grand Piano/As0.mp3' },
    { note: 'A#',  octave: 1, file: 'Samples/Grand Piano/As1.mp3' },
    { note: 'A#',  octave: 2, file: 'Samples/Grand Piano/As2.mp3' },
    { note: 'A#',  octave: 3, file: 'Samples/Grand Piano/As3.mp3' },
    { note: 'A#',  octave: 4, file: 'Samples/Grand Piano/As4.mp3' },
    { note: 'A#',  octave: 5, file: 'Samples/Grand Piano/As5.mp3' },
    { note: 'B',  octave: 0, file: 'Samples/Grand Piano/B0.mp3' },
    { note: 'B',  octave: 1, file: 'Samples/Grand Piano/B1.mp3' },
    { note: 'B',  octave: 2, file: 'Samples/Grand Piano/B2.mp3' },
    { note: 'B',  octave: 3, file: 'Samples/Grand Piano/B3.mp3' },
    { note: 'B',  octave: 4, file: 'Samples/Grand Piano/B4.mp3' },
    { note: 'B',  octave: 5, file: 'Samples/Grand Piano/B5.mp3' },
    { note: 'C',  octave: 3, file: 'Samples/Grand Piano/C3.mp3' },
    { note: 'C',  octave: 4, file: 'Samples/Grand Piano/C4.mp3' },
    { note: 'C',  octave: 5, file: 'Samples/Grand Piano/C5.mp3' },
    { note: 'C#',  octave: 0, file: 'Samples/Grand Piano/Cs0.mp3' },
    { note: 'C#',  octave: 1, file: 'Samples/Grand Piano/Cs1.mp3' },
    { note: 'C#',  octave: 2, file: 'Samples/Grand Piano/Cs2.mp3' },
    { note: 'C#',  octave: 3, file: 'Samples/Grand Piano/Cs3.mp3' },
    { note: 'C#',  octave: 4, file: 'Samples/Grand Piano/Cs4.mp3' },
    { note: 'C#',  octave: 5, file: 'Samples/Grand Piano/Cs5.mp3' },
    { note: 'D',  octave: 0, file: 'Samples/Grand Piano/D0.mp3' },
    { note: 'D',  octave: 1, file: 'Samples/Grand Piano/D1.mp3' },
    { note: 'D',  octave: 2, file: 'Samples/Grand Piano/D2.mp3' },
    { note: 'D',  octave: 3, file: 'Samples/Grand Piano/D3.mp3' },
    { note: 'D',  octave: 4, file: 'Samples/Grand Piano/D4.mp3' },
    { note: 'D',  octave: 5, file: 'Samples/Grand Piano/D5.mp3' },
    { note: 'D#',  octave: 0, file: 'Samples/Grand Piano/Ds0.mp3' },
    { note: 'D#',  octave: 1, file: 'Samples/Grand Piano/Ds1.mp3' },
    { note: 'D#',  octave: 2, file: 'Samples/Grand Piano/Ds2.mp3' },
    { note: 'D#',  octave: 3, file: 'Samples/Grand Piano/Ds3.mp3' },
    { note: 'D#',  octave: 4, file: 'Samples/Grand Piano/Ds4.mp3' },
    { note: 'D#',  octave: 5, file: 'Samples/Grand Piano/Ds5.mp3' },
    { note: 'E',  octave: 0, file: 'Samples/Grand Piano/E0.mp3' },
    { note: 'E',  octave: 1, file: 'Samples/Grand Piano/E1.mp3' },
    { note: 'E',  octave: 2, file: 'Samples/Grand Piano/E2.mp3' },
    { note: 'E',  octave: 3, file: 'Samples/Grand Piano/E3.mp3' },
    { note: 'E',  octave: 4, file: 'Samples/Grand Piano/E4.mp3' },
    { note: 'E',  octave: 5, file: 'Samples/Grand Piano/E5.mp3' },
    { note: 'F',  octave: 0, file: 'Samples/Grand Piano/F0.mp3' },
    { note: 'F',  octave: 1, file: 'Samples/Grand Piano/F1.mp3' },
    { note: 'F',  octave: 2, file: 'Samples/Grand Piano/F2.mp3' },
    { note: 'F',  octave: 3, file: 'Samples/Grand Piano/F3.mp3' },
    { note: 'F',  octave: 4, file: 'Samples/Grand Piano/F4.mp3' },
    { note: 'F',  octave: 5, file: 'Samples/Grand Piano/F5.mp3' },
    { note: 'F#',  octave: 0, file: 'Samples/Grand Piano/Fs0.mp3' },
    { note: 'F#',  octave: 1, file: 'Samples/Grand Piano/Fs1.mp3' },
    { note: 'F#',  octave: 2, file: 'Samples/Grand Piano/Fs2.mp3' },
    { note: 'F#',  octave: 3, file: 'Samples/Grand Piano/Fs3.mp3' },
    { note: 'F#',  octave: 4, file: 'Samples/Grand Piano/Fs4.mp3' },
    { note: 'F#',  octave: 5, file: 'Samples/Grand Piano/Fs5.mp3' },
    { note: 'G',  octave: 0, file: 'Samples/Grand Piano/G0.mp3' },
    { note: 'G',  octave: 1, file: 'Samples/Grand Piano/G1.mp3' },
    { note: 'G',  octave: 2, file: 'Samples/Grand Piano/G2.mp3' },
    { note: 'G',  octave: 3, file: 'Samples/Grand Piano/G3.mp3' },
    { note: 'G',  octave: 4, file: 'Samples/Grand Piano/G4.mp3' },
    { note: 'G',  octave: 5, file: 'Samples/Grand Piano/G5.mp3' },
    { note: 'G#',  octave: 0, file: 'Samples/Grand Piano/Gs0.mp3' },
    { note: 'G#',  octave: 1, file: 'Samples/Grand Piano/Gs1.mp3' },
    { note: 'G#',  octave: 2, file: 'Samples/Grand Piano/Gs2.mp3' },
    { note: 'G#',  octave: 3, file: 'Samples/Grand Piano/Gs3.mp3' },
    { note: 'G#',  octave: 4, file: 'Samples/Grand Piano/Gs4.mp3' },
    { note: 'G#',  octave: 5, file: 'Samples/Grand Piano/Gs5.mp3' }
  ]
};
const OCTAVE = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];

var audioContext = new AudioContext();

function stop(){
    if(audioContext.state === 'running'){
        audioContext.close();
    }

}

function fetchSample(path, noteAndOctaveKey) {
    return getSampleFromDatabase(noteAndOctaveKey).then((cachedArrayBuffer) => {
        if (cachedArrayBuffer) {
            // Convert cached ArrayBuffer back to AudioBuffer
            return audioContext.decodeAudioData(cachedArrayBuffer.slice(0)); // Slicing to ensure it's a fresh copy
        } else {
            // Otherwise fetch from server, decode, and cache as ArrayBuffer
            return fetch(path)
                .then(response => response.arrayBuffer())
                .then(arrayBuffer => {
                    // Make a copy of the ArrayBuffer before decoding
                    const arrayBufferCopy = arrayBuffer.slice(0);
                    
                    // Cache the ArrayBuffer in IndexedDB
                    addSampleToDatabase(noteAndOctaveKey, arrayBufferCopy);
                    
                    // Decode the ArrayBuffer to AudioBuffer for playback
                    return audioContext.decodeAudioData(arrayBuffer);
                });
        }
    });
}

function noteValue(note, octave) {
  return octave * 12 + OCTAVE.indexOf(note);
}

function getNoteDistance(note1, octave1, note2, octave2) {
  return noteValue(note1, octave1) - noteValue(note2, octave2);
}

function getNearestSample(sampleBank, note, octave) {
  let sortedBank = sampleBank.slice().sort((sampleA, sampleB) => {
    let distanceToA =
      Math.abs(getNoteDistance(note, octave, sampleA.note, sampleA.octave));
    let distanceToB =
      Math.abs(getNoteDistance(note, octave, sampleB.note, sampleB.octave));
    return distanceToA - distanceToB;
  });
  return sortedBank[0];
}

function flatToSharp(note) {
  switch (note) {
    case 'Bb': return 'A#';
    case 'Db': return 'C#';
    case 'Eb': return 'D#';
    case 'Gb': return 'F#';
    case 'Ab': return 'G#';
    default:   return note;
  }
}

function getSample(instrument, noteAndOctave) {
    let [, requestedNote, requestedOctave] = /^(\w[b\#]?)(\d)$/.exec(noteAndOctave);
    requestedOctave = parseInt(requestedOctave, 10);
    requestedNote = flatToSharp(requestedNote);
    let sampleBank = SAMPLE_LIBRARY[instrument];
    let sample = getNearestSample(sampleBank, requestedNote, requestedOctave);
    let distance = getNoteDistance(requestedNote, requestedOctave, sample.note, sample.octave);

    // Create a unique key for this note and octave combination
    let noteAndOctaveKey = `${requestedNote}${requestedOctave}`;

    // Fetch the sample (from IndexedDB or server)
    return fetchSample(sample.file, noteAndOctaveKey).then(audioBuffer => ({
        audioBuffer: audioBuffer,
        distance: distance
    }));
}


function playSample(instrument, note, duration) {
    checkAudioContext();
    getSample(instrument, note).then(({audioBuffer, distance}) => {
        let playbackRate = Math.pow(2, distance / 12);
        let frameCount = audioContext.sampleRate *duration;
        let bufferSource = audioContext.createBufferSource();
        audioBuffer.length = frameCount*(duration/1000);
        bufferSource.buffer = audioBuffer;
        bufferSource.playbackRate.value = playbackRate;
        bufferSource.connect(audioContext.destination);
        bufferSource.start();
    });
}
function checkAudioContext(){
    if(audioContext.state === 'closed'){
        audioContext = new AudioContext();
    }
}

function preloadPianoSamples() {
    const sampleBank = SAMPLE_LIBRARY['Grand Piano'];
    sampleBank.forEach(sample => {
        const noteAndOctaveKey = `${sample.note}${sample.octave}`;
        fetchSample(sample.file, noteAndOctaveKey); // This will store them in IndexedDB
    });
    console.log('Piano samples preloading started...');
}

window.onload = function() {
    preloadPianoSamples();
};

//******************************************************************************
