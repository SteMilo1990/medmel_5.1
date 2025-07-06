// ----- Global Attributes -----
let modernStaves = "";
let oldStaves = "";
let currentStyle = 0;
let zoom = 1;

// ----- Textarea Attributes -----
let nLine = 0; // Current line index in textarea

// ----- Default Shape Parameters -----
const default_pes = 1;
const default_clivis = 1;
const default_climacus = 1;
const default_porrectus = 1;
const default_plica = 1;
const default_scandicus = 1;
const default_torculus = 1;

// ----- Shape Parameters -----
let linesInLine = [4];  // Old staves parameter for number of lines in a system
let shapeGroupNote = []; // Group shape IDs
let shapeSingleNote = []; // Single note shape IDs
let stemSingleNote = []; // Stem direction for single notes
let connectGroupNote = []; // Connecting line in group ID
let melodicStructure = "";
let clickSingleNote = 0;
let custos = [];

// ----- Shape Parameters (Dynamic) -----
let punctus_virga_default = 1;
let pes_type = default_pes;
let united_clivis = default_clivis;
let climacus_type = default_climacus;
let porrectus_type = default_porrectus;
let plica_type = default_plica;
let scandicus_type = default_scandicus;
let torculus_type = default_torculus;

// ----- Text and Annotations -----
let texts = [""];
let annotations = [""];
let settings = {};
let selectedText = 0;
let selectedAnnotations = 0;
let parameterStorage = 1;
let manualMelodicStructureFirstUse = true;
let useManualMelodicStructure = 0;
let manualMelodicStructure = "";
let bFlatAlwaysInKeySignature = false;

// ----- Messine Notation -----
let messinNotation = false;

// ---- PARAMS -----
let textMode = 0;
let barMode = false;
let custosMode = false;
let bar = [[[]]];
let y1 = 0;
let y2 = 3;
let repetitionPattern = "";
let allowResize = true;
let fontOld = 17;
let notationType = 0;
let syncModernOld = true;


let selectedLine = 0;

// add numbering to textarea lines 
$(function() {
  if (localStorage.getItem("current_page") == "editor.html")
      $(".lined").linedtextarea(
      );
});

function getNotesFromText() {
    let notes = document.getElementById("music_input").value;
    notes = notes.replace(/[i-zI-Z]/g, "");
    notes.replace(/\n/g, "|");
    return notes;
}

function setURL(){
      // 1. Build an array of “key=value” strings instead of using Python’s "&".join
    const urlParametersList = [];

    // 2. Check if `loadedIdStaves` is non‐empty, then push “id=…” onto the array
    if (loadedIdStaves != null && loadedIdStaves !== "") {
      // Always encode URI components when interpolating into a query string
      urlParametersList.push(`id=${encodeURIComponent(loadedIdStaves)}`);
    }

    // 3. If you want to read the existing query (e.g. “?foo=bar”), use window.location.search
    const urlParams = new URLSearchParams(window.location.search);
    const urlNotationStyle = urlParams.get("notation");

    // 4. If notation=1, set the DOM element’s value
    // if (urlNotationStyle === "1") {
    //   document.getElementById("shapeSelection").value = 1;
    // }else{
    //   document.getElementById("shapeSelection").value = 0;
    // }

    // 5. Only push “notation=…” if you actually have a value
    if (urlNotationStyle != null) {
      urlParametersList.push(`notation=${encodeURIComponent(urlNotationStyle)}`);
    }

    // 6. Join everything with “&” and prepend “?”
    const newQueryString = urlParametersList.length > 0
      ? `?${urlParametersList.join("&")}`
      : "";

    // 7. Reconstruct the new URL (keep the same pathname, replace the query)
    const newUrl = window.location.pathname + newQueryString;

    // 8. Finally, replaceState with the new URL
    window.history.replaceState(null, "", newUrl);
}

function updateURLparam(param, value){
  // 1. Read the current querystring
  const params = new URLSearchParams(window.location.search);

  // 2. Change “notation” to whatever new value you need
  params.set(param, value);   // e.g. set notation=2
  // If you wanted to remove it instead:
  // params.delete("notation");

  // 3. Build a new querystring
  const newQueryString = params.toString(); // e.g. "foo=bar&notation=2&baz=qux"

  // 4. Replace the URL (keeping the same pathname)
  const newUrl = window.location.pathname + (newQueryString ? "?" + newQueryString : "");
  window.history.replaceState(null, "", newUrl);
} 

// ----- Controller UI Modern/Old Staves -----
function updateStaves(changingStyle = false) {
    setURL(); // updates URL or keeps URL as it should be
    
    messinNotation = false;
    
    // Get and clean music input
    let music_input = cleanNoteInput(document.getElementById("music_input").value);
    // Update music_input with clean one
    document.getElementById("music_input").value = music_input;

    // Get text and annotations
    updateText(changingStyle, currentStyle, syncModernOld);
    updateAnnotionation(changingStyle, currentStyle, syncModernOld);
  
    // Get metadata and settings
    const title = document.getElementById("title_input").value;
    const id_input = document.getElementById("id_input").value;
    const author = document.getElementById("author_input").value;
    const language = document.getElementById("language").value;
    const ms = document.getElementById("ms_input").value;
    const f_input = document.getElementById("f_input").value;
    const callingFrom = localStorage.getItem("current_page");
    
    let alphabetMelodicStructure = 0;
    try {
      alphabetMelodicStructure = document.getElementById("changeMelodicStructure").value;
    } catch {}
    
    // Modern stemless notation
    if (currentStyle == 0) {
        if (music_input != modernStaves && syncModernOld) {
            checkLineDeleted(music_input, modernStaves);
            checkNoteDeleted(music_input, modernStaves);  
        }
        
        modernStaves = music_input;
        music_input = cleanKeys(music_input, false);

        if (syncModernOld){
          syncDatasetShapes();
        }
        $.ajax({
            type : "POST",
            url  : "php/printStaves.php",
            data : {
              notes : music_input.replace(/\n/g, "|"), 
              text_string : texts[selectedText],
              title:title, 
              id:id_input, 
              author:author,
              language:language,
              ms:ms, 
              f_input:f_input, 
              annotations:annotations[selectedAnnotations], 
              text_mode:textMode, 
              callingFrom:callingFrom,
              alphabetMelodicStructure:alphabetMelodicStructure, 
              useManualMelodicStructure:useManualMelodicStructure,
              manualMelodicStructure:manualMelodicStructure, 
              bFlatAlwaysInKeySignature:bFlatAlwaysInKeySignature,
              repetitionPattern:repetitionPattern
            },
            success: function(res) {
                document.getElementById("stavesContainer").innerHTML = "";
                var div = document.createElement("div");
                div.innerHTML += res;
                document.getElementById("stavesContainer").appendChild(div);

                resizeStaves();
                if (music_input != "") highlightSelectedNote();
            }
        });
    }
    
    // Medieval notations
    else {
        if ((music_input != oldStaves && syncModernOld) || (music_input != oldStaves && !changingStyle)) {
            checkLineDeleted(music_input, oldStaves);
            checkNoteDeleted(music_input, oldStaves);
        }
        setTimeout(function() {
            oldStaves = music_input;
            
            syncDatasetShapes();
            // Messine notation
            if (notationType != 0 
              && (notationType== 1 
              || notationType == 2 
              || ms == "X" && language.toLowerCase() == "occitan" 
              || (ms == "U" && language.toLowerCase() == "french" && id_input !== "Linker 135.1")
              )
            ) {
                messinNotation = true;
                $.ajax({
                    type : "POST",
                    url  : "php/printClassicStavesMessinNotation.php",
                    data : {
                      notes : music_input, 
                      text_string : texts[selectedText], 
                      title:title, 
                      id:id_input, 
                      author:author,
                      ms:ms,
                      f_input:f_input, 
                      annotations:annotations[selectedAnnotations],
                      linesInLine:JSON.stringify(linesInLine),
                      shapeGroupNote:JSON.stringify(shapeGroupNote),
                      shapeSingleNote:JSON.stringify(shapeSingleNote),
                      stemSingleNote:JSON.stringify(stemSingleNote),
                      connectGroupNote:JSON.stringify(connectGroupNote),
                      bar:JSON.stringify(bar),
                      custos: JSON.stringify(custos),
                      pes: pes_type, clivis:united_clivis, climacus:climacus_type,
                      porrectus: porrectus_type, plica:plica_type, scandicus: scandicus_type,
                      repetitionPattern:repetitionPattern,
                      notationType:notationType
                    },
                    success: function(res) {
                        document.getElementById("stavesContainer").innerHTML = "";
                        var div = document.createElement("div");
                        div.innerHTML += res;
                        document.getElementById("stavesContainer").appendChild(div);
                        resizeStaves();
                        if (music_input != "") highlightSelectedNote();
                    }
                });
            }
            
            // Square notation
            else { 
                if (punctus_virga_default == 2) {
                  try {addStems(false);} catch {}
                }
                
                if (torculus_type == 2) {
                  try {setDefaultTorculus(2, false);}catch(err){console.log(err);}
                }
                $.ajax({
                    type : "POST",
                    url  : "php/printOldStaves.php",  
                    data : { 
                      notes : music_input.replace(/\n/g, "|"), 
                      text_string : texts[selectedText], 
                      title:title, 
                      id:id_input, 
                      author:author, 
                      ms:ms, 
                      f_input:f_input, 
                      annotations:annotations[selectedAnnotations],
                      linesInLine:JSON.stringify(linesInLine),
                      shapeGroupNote:JSON.stringify(shapeGroupNote),
                      shapeSingleNote:JSON.stringify(shapeSingleNote),
                      stemSingleNote:JSON.stringify(stemSingleNote),
                      connectGroupNote:JSON.stringify(connectGroupNote),
                      bar:JSON.stringify(bar),
                      custos: JSON.stringify(custos),
                      pes: pes_type, 
                      clivis:united_clivis, 
                      climacus:climacus_type,
                      porrectus: porrectus_type, 
                      plica:plica_type, 
                      scandicus: scandicus_type,
                      repetitionPattern:repetitionPattern
                    },
                    success: function(res) {
                        const staves_container = document.getElementById("stavesContainer");
                        staves_container.innerHTML = "";
                        const div = formatHTML(res);

                        staves_container.appendChild(div);
                        
                        resizeStaves();
                        
                        let page = localStorage.getItem("current_page");
                        if (music_input != "" && page != "viewer.html") highlightSelectedNote();
                    }
                });
            }
        }, 100);
    }
    setTimeout(() => autoscrollStaves(),  100);
}

function setNotationFromURL() {
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const notation = urlParams.get('notation');
  
  if (notation != "" && notation != undefined) {
    setTimeout(function() {
      setStavesStyle(notation, true);
    }, 100);
  }
}

function cleanNoteInput(notes){
  notes = notes.replace(/[^a-h1-8#_%*+\[\]<>()}\-\s?'\/]/gi, "");
  notes = notes.replace("\n ", "\n");
  
  notes = notes
    .replace(/\++/g, "+")
    .replace(/\*+/g, "*")
    .replace(/#+/g, "#")
    .replace(/%+/g, "%")
    .replace(/_+/g, "_")
    .replace(/\[+/g, "[")
    .replace(/\]+/g, "]")
    .replace(/\(+/g, "(")
    .replace(/\)+/g, ")")
    // collapse two or more slashes → "//"
    .replace(/\/{2,}/g, "//");
  
  notes = notes.replace(/\b([fc])([1-9])\b/gi, (_, letter, digit) => {
    return letter.toUpperCase() + digit;
  });

  notes = notes.replace(/' /g, '\'');
  
  return notes;
}

function updateText(changingStyle, currentStyle, syncModernOld){
  // If modern and medieval notation are not synced, update currentText at every edit, unless we are switching between notations
  // Clean text
  const currentText = document.getElementById('text_input').value.replace("\"","");
  document.getElementById('text_input').value = currentText;
  // Update correct text to correct global variable
  if (!changingStyle && !syncModernOld){
    if (currentStyle == 0) {
      texts[0] = currentText;
    } else {
      texts[1] = currentText;
    }
  }
  // in case text is not split, just update the variable. Don't know if this is actually necessary
  else if (syncModernOld){
    texts[selectedText] = currentText;
  }
}


function updateAnnotionation() {
  const currentAnnotations = document.getElementById('annotationsBox').value.replace("\"","");
  annotations[selectedAnnotations] = currentAnnotations;
}
// ----- Autoscroll staves -----
function autoscrollStaves() {

    let dimLine = 142;
    if (currentStyle == 1){
      if (notationType == 0){ // square notation
        dimLine = 122; // it shoudl really check the exact height of each stave. THis is for 1 line of text, 4 staves, no notes below the last line.
      }else{ // notation style == 1 | 2 == messine | kolmarer 
        dimLine = 155;
      }
    }
    let stanza = "";
    let rowText = 1;

    if (textMode > 0) {// this should be reviewed I think.
        stanza = texts[selectedText].replace(/\n\s*\n/g, '$').split('$');
        rowText = stanza.length - 1;
    }

    dimLine += rowText * 20;
    if (currentStyle == 1) dimLine = 155; // Old style view

    let payload = 0, dimMusic = 0;
    try {
        payload = document.getElementById("divPayloadStaves").offsetHeight;
        dimMusic = document.getElementById("staves").offsetHeight;
    } catch {}

    let deltaHeight = payload - dimMusic;
    let textarea = document.getElementById("music_input");

    let nLine = (textarea === document.activeElement)
        ? textarea.value.substr(0, textarea.selectionStart).split("\n").length - 1
        : document.getElementById("boxStavesContainer").scrollTop / dimLine;

    nLine = Math.max(0, Math.floor(nLine)); // Ensure non-negative integer

    const boxContainer = document.getElementById("boxStavesContainer");
    const page = localStorage.getItem("current_page");

    if (nLine < 1) {
        boxContainer.scrollTop = 0;
    } else if (textarea === document.activeElement || page == "viewer.html") {
        boxContainer.scrollTop = ((dimLine * zoom) * (nLine - 1)) + deltaHeight;
    } else {
      // This prevents stave from mooving when a shape is clicked, but WILL destroy something else
      // boxContainer.scrollTop = dimLine * nLine;
    }
}

// THIS VERSION SCROLLS WELL BUT PUTS THE SELECTED LINE ON TOP OF THE VIEW. IT MOVES TOO MUCH
// function autoscrollStaves() {
//     const page = localStorage.getItem("current_page");
// 
//     const boxContainer = document.getElementById("boxStavesContainer"); 
//     const boxTop = boxContainer.getBoundingClientRect().top;
//     const lines = Array.from(document.querySelectorAll(".stave-line"));
//     const lineOffsets = lines.map(line =>
//       line.getBoundingClientRect().top - boxTop + boxContainer.scrollTop
//     );
// 
//     let stanza = "";
//     let rowText = 1;
// 
//     if (textMode > 0) {// this should be reviewed I think.
//         stanza = texts[selectedText].replace(/\n\s*\n/g, '$').split('$');
//         rowText = stanza.length - 1;
//     }
// 
//     let textarea = document.getElementById("music_input");
//     let nLine;
// 
//     if (textarea === document.activeElement) {
//         console.log("activeEl")
//         // Textarea is focused — determine current line based on cursor position
//         const cursorPosition = textarea.selectionStart;
//         const textBeforeCursor = textarea.value.substring(0, cursorPosition);
//         const linesBeforeCursor = textBeforeCursor.split("\n");
//         nLine = linesBeforeCursor.length - 1;
//     } else {
//         // Textarea is not focused — use fallback value (e.g., selectedLine)
//         nLine = selectedLine;
//     }    
// 
//     const selectedLineOffset = lineOffsets[nLine];
// 
//     if (nLine < 1) {
//         boxContainer.scrollTop = 0;
//         console.log("Pass 1");
//     } else if (textarea === document.activeElement || page == "viewer.html") {
//         boxContainer.scrollTop = selectedLineOffset;
//         console.log(`Pass 2, L: ${nLine}; SL: ${selectedLine}; OS: ${selectedLineOffset}`);
//     } else {
//       console.log("pass 3");
//       // This prevents stave from mooving when a shape is clicked, but WILL destroy something else
//       // boxContainer.scrollTop = dimLine * nLine;
//     }
// }

// ZoomIn/ZoomOut staves
function resizeStaves() {
    if (!allowResize) {
      // keep the same zoom
      let stavesElement = document.getElementById('staves')
      stavesElement.style.transform = `scale(${zoom})`;
      stavesElement.style.transformOrigin = "top left";
      stavesElement.style.width = `${100 / zoom}%`;
      return
    }

    if (currentStyle == 0) {

      try {
        const largestLineWidth = document.getElementById('staves').getElementsByTagName("svg")[0].width.baseVal.value;
        var width = document.getElementById('stavesBox').offsetWidth;
        let stavesElement = document.getElementById('staves')
        if (largestLineWidth > width-160) {
            zoom = (width-160)/(largestLineWidth);
            zoom = Math.round((zoom + Number.EPSILON) * 100) / 100;

            stavesElement.style.transform = `scale(${zoom})`;
            stavesElement.style.transformOrigin = "top left";
            stavesElement.style.width = `${100 / zoom}%`;

        } else {
            zoom = 1;
            document.getElementById('staves').style.zoom = 1;
            document.getElementById('staves').style.width = "100%";
            document.getElementById('staves').style.transform = `scale(1)`;
        }
      }catch(err){console.log(err);}

    }
    else if (currentStyle == 1) {
        var largestLineWidth = 100;
        try{largestLineWidth  = document.getElementById("line_0").width.baseVal.value;}catch{} // needed
        var width = document.getElementById('stavesBox').offsetWidth;

        let stavesElement = document.getElementById('staves')

        if (largestLineWidth > width-75) {
            zoom = (width-75)/(largestLineWidth);
            stavesElement.style.transform = `scale(${zoom})`;
            stavesElement.style.transformOrigin = "top left";
            stavesElement.style.width = `${100 / zoom}%`;
        }else{
          zoom = 1;
          document.getElementById('staves').style.zoom = 1;
          document.getElementById('staves').style.width = "100%";
          document.getElementById('staves').style.transform = `scale(1)`;            }
    }
    
}

function formatHTML(html) {
    const div = document.createElement('div');
    div.innerHTML = html.trim();
    return beautifyHTML(div, 0); // Return the beautified node, not the innerHTML
}

function beautifyHTML(node, level) {
    const indentBefore = '  '.repeat(level);
    const indentAfter = '  '.repeat(Math.max(level - 1, 0));
    let textNode;

    for (let i = 0; i < node.children.length; i++) {
        textNode = document.createTextNode(`\n${indentBefore}`);
        node.insertBefore(textNode, node.children[i]);
        beautifyHTML(node.children[i], level + 1);
        if (i === node.children.length - 1) {
            textNode = document.createTextNode(`\n${indentAfter}`);
            node.appendChild(textNode);
        }
    }
    return node; // Return the node itself
} 

//-----Format text when user switch old to modern or modern to old--------------
function setStavesStyle(value) {
    currentStyle = value;

    // updateURLparam("notation", value);

    const currentPage = localStorage.getItem("current_page");
    
    if (syncModernOld && currentPage !== "viewer.html") {
      // console.log("CH1 - syncModernOld in editor")
      
      // Switching to Modern Notation
      if (currentStyle == 0) {
          // console.log("CH1.1 - syncModernOld in editor MODERN");
          
          // Case 1: both modern and old are populated
          if (oldStaves != "" && modernStaves != "") {
              // store oldStaves, transform it to modernStaves and clean it
              oldStaves = document.getElementById("music_input").value;
              modernStaves = oldStaves.replace(/  +/g, ' ');
              modernStaves = modernStaves.replace(/\' \n/g, " '\n");
              modernStaves = modernStaves.replace(/(?<!-)\s'/g, "'");
              modernStaves = modernStaves.replace(/\n /g, "\n");
              modernStaves = modernStaves.replace(/\n/g, ".");
              modernStaves = modernStaves.replace(/\'\./g, ".'");
              modernStaves = modernStaves.replace(/\'/g, "\n");
              modernStaves = modernStaves.replace(/\./g, " '");
              modernStaves = modernStaves.replace(/- '/g, "-'");

          }
          // Case 2: Only oldStaves is populated -> populate modernStaves
          // just change the input to modern setting
          else if (modernStaves == "") {
              modernStaves = document.getElementById("music_input").value;
              modernStaves = modernStaves.replace(/ \n/g, "\n");
              modernStaves = modernStaves.replace(/\'/g, "");
              modernStaves = modernStaves.replace(/\n/g, " '\n");
              modernStaves = modernStaves.replace(/  +/g, ' ');
          }
          
          // Update the text area with modernStaves
          document.getElementById("music_input").value = modernStaves;
          
          setupMedievalNotationIcons("none");
      }
      
      // Switching to Medieval notation (synced)
      else {
          // console.log("CH1.2 - syncModernOld in editor MEDIEVAL")
                    
          // save modernStaves and transform it into oldStave
          if (modernStaves != "" && oldStaves!= "") {
              modernStaves = document.getElementById("music_input").value;
              oldStaves = modernStaves.replace(/  +/g, ' ');
              oldStaves = oldStaves.replace(/\' \n/g, " '\n");
              oldStaves = oldStaves.replace(/(?<!-)\s'/g, "'");
              oldStaves = oldStaves.replace(/\n /g, "\n");
              oldStaves = oldStaves.replace(/\n/g, ".");
              oldStaves = oldStaves.replace(/\'\./g, ".'");
              oldStaves = oldStaves.replace(/\'/g, "\n");
              oldStaves = oldStaves.replace(/\./g, " '");
          }
          else if (oldStaves == "") {
              oldStaves = document.getElementById("music_input").value;
              oldStaves = oldStaves.replace(/ \n/g, "\n");
              oldStaves = oldStaves.replace(/\'/g, "");
              oldStaves = oldStaves.replace(/\n/g, " '\n");
              oldStaves = oldStaves.replace(/  +/g, ' ');
          }
          document.getElementById("music_input").value = oldStaves;
          
          setupMedievalNotationIcons("block");
      }
    }
    // UNNECESSARY... the nes "else" does every thing is needed
    // else if (currentPage == "viewer.html"){
    //   console.log("CH3 - viewer");
    // 
    //   // Switching to Modern notation  
    //   if (currentStyle == 0) {
    //     console.log("CH3.1 - viewer -> Modern");
    // 
    //     // reinstate modernStaves
    //     document.getElementById("music_input").value = modernStaves;
    // 
    //     // set Text
    //     selectedText = 0;
    //     document.getElementById("text_input").value = texts[selectedText];
    //   }
    //   else if (currentStyle == 1) {
    //     console.log("CH3.2 - viewer -> medieval");
    // 
    //     document.getElementById("music_input").value = oldStaves;
    // 
    //     // set Text
    //     if (!syncModernOld) {
    //       selectedText = 1;
    //       if (texts[selectedText] == undefined) {
    //         texts[selectedText] = "";
    //       }
    //       document.getElementById("text_input").value = texts[selectedText];
    //     }
    //   }
    //   updateStaves(true);
    // }
    // MODERN AND OLD INDEPENDENT - NOT VIEWER
    else {
      // console.log("CH2 - NOT syncModernOld in editor")
      
      // Switching to Modern notation  
      if (currentStyle == 0) {
        // console.log("CH2.1 - NOT syncModernOld MODERN");
        
        // reinstate modernStaves
        document.getElementById("music_input").value = modernStaves;
        
        // set Text
        selectedText = 0;
        document.getElementById("text_input").value = texts[selectedText];
        
        setupMedievalNotationIcons("none");
      }
      
      else if (currentStyle == 1) {
        //console.log("CH2.1 - NOT syncModernOld in editor MEDIEVAL");
        
        document.getElementById("music_input").value = oldStaves;
        
        // set Text
        if (!syncModernOld) {
          selectedText = 1;
          if (texts[selectedText] == undefined) {
            texts[selectedText] = "";
          }
          document.getElementById("text_input").value = texts[selectedText];
        }
        
        setupMedievalNotationIcons("block");
      }
    }

    updateStaves(true);
}

function setupMedievalNotationIcons(action) {
  // Show or hide icons for medieval notation editor
  try {
    document.getElementById("btnBarMode").style.display = action;
    document.getElementById("btnCustosMode").style.display = action;
    document.getElementById("btnDefaultShapes").style.display = action;
  } catch {}
}

// ----- setup data for the current visual editor session -----
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

function setupStavesUI(
    mStaves, oStaves, titleInput, idInput, authorInput,
    languageInput, msInput, fInput, textInput,
    annotationsStaves, setts, styleSelected, namePublisher
) {
    // Validate styleSelected (default to 0 if invalid)
    styleSelected = [0, 1].includes(Number(styleSelected)) ? Number(styleSelected) : 0;

    // Clone data
    modernStaves = deepClone(mStaves);
    oldStaves = deepClone(oStaves);
    texts = deepClone(textInput);
    annotations = deepClone(annotationsStaves);
    settings = deepClone(setts);
    selectedText = 0;
    selectedAnnotations = 0;

    // Populate UI elements
    document.getElementById("title_input").value = titleInput;
    document.getElementById("id_input").value = idInput;
    document.getElementById("author_input").value = authorInput;
    document.getElementById("language").value = languageInput;
    document.getElementById("ms_input").value = msInput;
    document.getElementById("f_input").value = fInput;
    document.getElementById("music_input").value = loadedModernStyle === 0 ? oStaves : mStaves;
    
    // Change Browser's Window Title
    document.getElementsByTagName("title")[0].innerHTML = idInput;
    
    // Handle text and annotation inputs safely
    try {
        document.getElementById("text_input").value = texts[selectedText] || "";
        document.getElementById("annotationsBox").value = annotations[selectedAnnotations] || "";
    } catch (error) {
        console.error("Error setting text_input or annotationsBox:", error);
    }

    // Set shape selection and visibility
    document.getElementById("shapeSelection").value = loadedModernStyle === 0 ? 1 : 0;
    currentStyle = loadedModernStyle === 0 ? 1 : 0;
    document.getElementById("checkOldVisible").disabled = loadedModernStyle === 0;

    // Viewer or Editor UI adjustments
    const currentPage = localStorage.getItem("current_page");
    if (currentPage === "viewer.html") {
        document.getElementById("credit-editor").innerHTML = "Ed. " + namePublisher;
        document.getElementById("shapeSelection").disabled = loadedModernStyle === 0 || loadedOldStyle === 0;
    } else if (currentPage === "editor.html") {
        document.getElementById("shapeSelection").disabled = false;
    }

    // Update stave display
    updateStaves();
}

// 0:lines_in_line 1: shape_group_note
// 2: shape_single_note 3: stem_single_note 4: connect_group_note
// 5: pes_type 6: united_clivis 7: climacus_type 8: porrectus_type 9: plica_type
// 10: scandicus_type 11: melodic_structure
function setupStavesParametersUI(lil, sgn, ssn, stemsn, cgn, ps, uc, ct, pt, plicat, st, melodics, bg) {
    linesInLine = JSON.parse(JSON.stringify(lil));
    shapeGroupNote = JSON.parse(JSON.stringify(sgn));
    shapeSingleNote = JSON.parse(JSON.stringify(ssn));
    stemSingleNote = JSON.parse(JSON.stringify(stemsn));
    connectGroupNote = JSON.parse(JSON.stringify(cgn));
    try{bar = JSON.parse(JSON.stringify(bg));}catch{bar = [];};
    pes_type = ps;
    united_clivis = uc;
    climacus_type = ct;
    porrectus_type = pt;
    plica_type = plicat;
    scandicus_type = st;
    melodicStructure = JSON.parse(JSON.stringify(melodics));
}

// ----- return the data contained in the visual editor -----
function getTitleUI() {return document.getElementById('title_input').value;}
function getIdUI() {return document.getElementById('id_input').value;}
function getAuthorUI() {return document.getElementById('author_input').value;}
function getLanguageUI() { return document.getElementById('language').value;}
function getMsUI() {return document.getElementById('ms_input').value;}
function getFUI() {return document.getElementById('f_input').value;}
function getModernStavesUI() {return modernStaves;}
function getOldStavesUI() {return oldStaves;}
function getTextsUI() {return texts;}
function getAnnotationsUI() {return annotations;}
function getSettingsUI() {return getAdvancedParameters();}
// ----- Parameters -----
function getLinesInLineUI() {return linesInLine;}
function getShapeGroupNoteUI() {return shapeGroupNote;}
function getShapeSingleNoteUI() {return shapeSingleNote;}
function getStemSingleNoteUI() {return stemSingleNote;}
function getConnectGroupNoteUI() {return connectGroupNote;}
function getBarsGroupNoteUI() {return bar};
    //default parameters shape
function getPesUI() {return pes_type;}
function getClivisUI() {return united_clivis;}
function getClimacusUI() {return climacus_type;}
function getPorrectusUI() {return porrectus_type;}
function getPlicaUI() {return plica_type;}
function getScandicusUI() {return scandicus_type;}
function getMelodicStructureUI() {
    getMelodicStructure();
    return melodicStructure;
}

// ----- Setup line for stave -----
function menuLineNumberRemove() {
    let child = $('#menuLineNumber');
    child.remove();
}

function showLineNumber(id_element, line, event) {
    containerNoteMenuRemove();
    menuLineNumberRemove();

    const currentValue = id_element.getAttribute("data-lines");
    const x = event.clientX + 5;
    const y = event.clientY;

    // Create menu container
    const div = document.createElement("div");
    div.className = "menuStyleNote";
    div.id = "menuLineNumber";
    div.style.position = "absolute";
    div.style.left = `${x}px`;
    div.style.top = `${y}px`;
    div.style.animation = "fading 0.8s";

    // Title
    const title = document.createElement("div");
    title.className = "divMenuLineNumber";
    title.textContent = "Lines in staff";
    title.style.marginTop = "10px";

    // Number input
    const linesNumber = document.createElement("input");
    linesNumber.type = "number";
    linesNumber.value = currentValue;
    linesNumber.style.width = "100px";
    linesNumber.min = "1";
    linesNumber.max = "8";
    linesNumber.id = "linesNumer";
    linesNumber.addEventListener("change", () => setupLinesForStave(line, 0));

    // Apply button
    const apply = document.createElement("input");
    apply.type = "button";
    apply.value = "Apply";
    apply.style.margin = "15px 0 0 20px";
    apply.addEventListener("click", () => setupLinesForStave(line, 1));

    // Append elements if `music_input` has a value
    if (document.getElementById("music_input").value !== "") {
        const divButton = document.createElement("div");
        divButton.className = "divMenuLineNumber";
        divButton.appendChild(linesNumber);
        divButton.appendChild(apply);

        div.appendChild(title);
        div.appendChild(divButton);
        document.body.appendChild(div);
    }
}

function setupLinesForStave(line, close) {
    let linesNumber = document.getElementById("linesNumer").value;
    linesInLine[line] = parseInt(linesNumber);
    updateStaves();
    if (close == 1) menuLineNumberRemove();
}

//----- Controller click event on note -----
function extractNoteFromClick(nGroup, nNote, line) {
    selectedLine = line;
    clickSingleNote = 1;
    // Remove previus menu
    menuLineNumberRemove();
    containerNoteMenuRemove();
    
    let values = extractGroup(nGroup);
    // Check that a singe plica has't been clicked: in this case the plica menu should open
    if (/^[a-zA-Z]\([a-zA-Z]\)$/.test(values[1])) {
        clickSingleNote = 0;
        extractGroupFromClick(nGroup, line);
        return 
    }
    
    let groupLine = line;
    let indexGroup = values[0];
    let groupNote = values[1];
    let groupPos = extractGroupPosition(indexGroup, line);
    if (groupPos == -1) return 0;

    // ----- mouse position ----
    let x = event.clientX+5;
    let y = event.clientY;
    let div = document.createElement("div");
    div.setAttribute("id", "containerNoteMenu");
    div.setAttribute("style", "visibility:hidden");
    $(div).css({position:"absolute", left:x,top:y});
    document.body.appendChild(div);
    if (notationType == 2) {
      $(containerNoteMenu).load("./Menu/menuSingleNote_notationType2.html");
      setTimeout(function() {
        setupButtonsSingleNote(indexGroup, groupNote, nNote, line, groupPos);
      }, 300);
    }else{
      $(containerNoteMenu).load("./Menu/menuSingleNote.html");
      setTimeout(function() {
        setupButtonsSingleNote(indexGroup, groupNote, nNote, line, groupPos);
      }, 300);
    }

    setTimeout(function() {
        div.style.visibility = "visible";
    }, 300);
    //alert("Click Nota - N group : "+indexGroup+ " - Group : "+groupNote+" - length stringa : "+groupNote.length + " - Nota :  "+groupNote[nNote]+" - nNota : "+nNote);
}

// ----- Controller click event on group -----
function containerNoteMenuRemove() {
    let container = $('#containerNoteMenu');
    container.remove();
}

function extractGroupFromClick(nGroup, line) {
    selectedLine = line;
    
    if (clickSingleNote) {
        clickSingleNote = 0;
        return 0;
    }
    let values = extractGroup(nGroup);
    let groupLine = line;
    let indexGroup = values[0];
    let groupNote = values[1].replace("_","");
    groupNote = groupNote.replace("/","");
    if (indexGroup == -1) return 0;
    let groupPos = extractGroupPosition(indexGroup, line);
    if (groupPos == -1) return 0;

    // Remove previus menù
    menuLineNumberRemove();
    containerNoteMenuRemove();

    // mouse position 
    let x = event.clientX+5;
    let y = event.clientY;
  
    let div = document.createElement("div");
    div.setAttribute("id", "containerNoteMenu");
    div.setAttribute("style", "visibility:hidden");
    $(div).css({position:"absolute", left:x,top:y});
    document.body.appendChild(div);
  
    if (custosMode == true) {
      $(div).css({position:"absolute", left:(x+25),top:y});
      $(containerNoteMenu).load("./Menu/menuCustos.html");
      createCustosStructure();
      setTimeout(function() {
          setupButtonsCustos(groupLine);
      }, 150);
      setTimeout(function() {
          div.style.visibility = "visible";
      }, 200);
      return 0;
    }
  
    if (barMode == true || event.getModifierState("Shift") == true) {
      if (y > 356) {y = 356}
      if ((window.innerWidth-x-45) < 230) {x = x-320}
      $(div).css({position:"absolute", left:(x+45),top:y});
      $(containerNoteMenu).load("./Menu/menuBar.html");
      createBarStructure();
      setTimeout(function() {
          setupButtonsBar(indexGroup, groupNote, groupLine, groupPos);
      }, 150);
      setTimeout(function() {
          div.style.visibility = "visible";
      }, 200);
      return 0;
    }
    // Two-note group
    if (groupNote.length == 2) {
        if (y > 605) y = 605;
        if ((window.innerWidth - x - 45) < 230) x = x - 320;
        $(div).css({position:"absolute", left:(x), top:y});
        $(containerNoteMenu).load("./Menu/menuGroup2.html");
        
        try {
          setTimeout(function() {
            setupButtons2(indexGroup, groupNote, groupLine, groupPos);
          }, 150);
        } catch {
          setTimeout(function() {
            setupButtons2(indexGroup, groupNote, groupLine, groupPos);
          }, 150);
        }
        setTimeout(function() {
            div.style.visibility = "visible";
        }, 200);
    }
    
    // Plica group
    else if (groupNote.charAt(1) == '(' && groupNote.length < 5 && messinNotation == false) {
        $(containerNoteMenu).load("./Menu/menuPlica.html");
        setTimeout(function() {
            setupButtonsPlica(indexGroup, groupNote, groupLine, groupPos);
        }, 100);
        setTimeout(function() {
            div.style.visibility = "visible";
        }, 150);
    }
    
    // Three-note group
    else if (groupNote.length == 3) {
        if ((window.innerWidth-x-45) < 230) {x = x-320}
        if (y > 605) {y = 605;}
        $(div).css({position:"absolute", left:(x),top:y});
        $(containerNoteMenu).load("./Menu/menuGroup3.html");
        setTimeout(function() {
            setupButtons3(indexGroup, groupNote, groupLine, groupPos);
        }, 200);
        setTimeout(function() {
            div.style.visibility = "visible";
        }, 300);
    }
    
    // Five-note group - Messine notation - with liquescence
    else if ((groupNote.length == 5 || groupNote.length == 6) && messinNotation == true && groupNote.charAt(3) == "(") {
        $(containerNoteMenu).load("./Menu/menuGroup4ahch-plica.html");
        setTimeout(function() {
            setupButtons4ahch(indexGroup, groupNote, groupLine, groupPos);
        }, 100);
        setTimeout(function() {
            div.style.visibility = "visible";
        }, 150);
    }
    
    // Four-note group - Messine 
    else if (groupNote.length == 4 && messinNotation == true) {
        $(containerNoteMenu).load("./Menu/menuGroup4.html");
        setTimeout(function() {
            setupButtons4(indexGroup, groupNote, groupLine, groupPos);
        }, 100);
        setTimeout(function() {
            div.style.visibility = "visible";
        }, 150);
    }
    
    // Five-note group - Messine
    else if (groupNote.length == 5 && messinNotation == true) {
        $(containerNoteMenu).load("./Menu/menuGroup5Messine.html");
        setTimeout(function() {
            setupButtons5(indexGroup, groupNote, groupLine, groupPos);
        }, 150);
        setTimeout(function() {
            div.style.visibility = "visible";
        }, 200);
    }

    // Single note, 
    else if (groupNote.length == 1 && (messinNotation == false || notationType == 2)) {
        extractNoteFromClick(nGroup, 0, groupLine, groupPos);
        clickSingleNote = 0;
    }

    // Check if menu is empty and clean page from empty element
    setTimeout(function() {
        if (div.innerHTML == "") div.remove();
    }, 300);

}


//---Extract group from oldStaves by id, and return formatted id for Shapes array---------
function extractGroup(nGroup) {
    let oldFormattedNote = oldStaves.replace(/\'/g, " ");
    oldFormattedNote = oldFormattedNote.replace(/\n/g, " ");
    oldFormattedNote = oldFormattedNote.replace(/  +/g, ' ');
    oldFormattedNote = oldFormattedNote.replace(/[\[\]<>]/g, '');

    oldFormattedNote = cleanKeys(oldFormattedNote);
    
    oldFormattedNote = oldFormattedNote.replace(/  +/g, ' ');
    oldFormattedNote = removeSpaceAtStart(oldFormattedNote);
    oldFormattedNote = oldFormattedNote.split(' ');
    let groupNote = oldFormattedNote;
    let selectedGroup = groupNote[nGroup];
    //console.log("Gruppo : "+selectedGroup+" - id: "+nGroup);
    if (groupNote[nGroup].length == 0 || groupNote.length ==0 ) {
        //alert("gruppo non valido");
        return [-1,[]];
    }
    selectedGroup = selectedGroup.replace(/\+a/g, 'u');
    selectedGroup = selectedGroup.replace(/\+b/g, 'p');
    selectedGroup = selectedGroup.replace(/\+h/g, 'q');
    selectedGroup = selectedGroup.replace(/\+c/g, 'r');
    selectedGroup = selectedGroup.replace(/\+d/g, 's');
    selectedGroup = selectedGroup.replace(/\*G/g, 'J');
    return [nGroup, selectedGroup];
}
// nGroup, group number from sequence groups in printOldStaves
function extractGroupPosition(nGroup, line) {

    let oldFormattedNote = cleanKeys(oldStaves);
    oldFormattedNote = oldFormattedNote.replace(/_ ?/gi, '');

    oldFormattedNote = removeSpaceAtStart(oldFormattedNote);
    //remove (') from notes
    oldFormattedNote = oldFormattedNote.replace(/\'/g, ' ');
    //remove multiple space from notes
    oldFormattedNote = oldFormattedNote.replace(/  +/g, ' ');
    //remove space at line start
    oldFormattedNote = oldFormattedNote.replace(/\n |\n/g, "\n");
    //split note groups into array
    oldFormattedNote = oldFormattedNote.split("\n");
    for(let i = 0; i < oldFormattedNote.length; i = i+1)
        oldFormattedNote[i] = removeSpaceAtLast(oldFormattedNote[i]);

    let lineSplit = [];
    let i = 0;
    while(oldFormattedNote.length> i) {
        lineSplit[i] = oldFormattedNote[i].split(' ');
        i=i+1
    }

    let groupPos = -1; //index for shapes array
    i = 0;
    let count = 0;

    while(i < lineSplit.length && (count+lineSplit[i].length) <= nGroup ) {
        //console.log("riga "+i+" : "+(count+lineSplit[i].length));
        if (lineSplit[i].length == 1 && lineSplit[i][0] == '') {} // skip line without text

        else
            count = count + lineSplit[i].length;
        i = i+1;
    }

    groupPos = nGroup - count;

    if (groupPos == -1 || lineSplit.length == 0) {
        return -1;
    }
    return groupPos;
}
function removeSpaceAtLast(str) {
    while(String(str).substring(String(str).length-1,String(str).length) == " " && str.length >0) {

            str = String(str).substring(0,String(str).length-1);
    }
    return str;
}
function removeSpaceAtStart(str) {
    while(String(str).substring(0,1) == " " && str.length >0) {

            str = String(str).substring(1,String(str).length);
    }
    return str;
}

//--------Concrete Shapes dataset--------
function syncDatasetShapes() {
    let notes = "";
    
    // Modern notation
    if (currentStyle == 0) {
        notes = cleanKeys(modernStaves);
        notes.replace("%", "");
        notes = removeSpaceAtStart(notes);
        //remove (\n) from notes
        notes = notes.replace(/\n/g, ' ');
        //remove multiple space from notes
        notes = notes.replace(/  +/g, ' ');
        //remove space at line start
        notes = notes.replace(/\' |\'/g, "\'");
        //split note groups into array
        notes = notes.split("\'");
        for(let i = 0; i < notes.length; i = i+1)
            notes[i] = removeSpaceAtLast(notes[i]);
    }
    // Medieval notation
    else if (currentStyle == 1) { 
        notes = cleanKeys(oldStaves, false);
        notes.replace("%", "");
        notes = removeSpaceAtStart(notes);
        //remove (') from notes
        notes = notes.replace(/\'/g, ' ');
        //remove multiple space from notes
        notes = notes.replace(/  +/g, ' ');
        //remove space at line start
        notes = notes.replace(/\n |\n/g, "\n");
        //split note groups into array
        notes = notes.split("\n");
        for (let i = 0; i < notes.length; i = i+1)
            notes[i] = removeSpaceAtLast(notes[i]);
    }
    
    // Line
    for (let i = 0; i < notes.length; i = i+1) {
        try{
            //setup shapeGroupNote
            if (shapeGroupNote[i] == null) shapeGroupNote[i] = [];
            //setup shapeSingleNote
            if (shapeSingleNote[i] == null) shapeSingleNote[i] = [];
            //setup stemSingleNote
            if (stemSingleNote[i] == null) stemSingleNote[i] = [];
            //setup shapeGroupNote
            if (connectGroupNote[i] == null) connectGroupNote[i] = [];
            //setup bar array
            // This covers both null *and* undefined:
            if (bar[i] == null) bar[i] = [];

            // Now bar[i] is definitely an array. Next:
            if (bar[i][0] == null) bar[i][0] = [];
            if (bar[i][1] == null) bar[i][1] = [];
            
            groupSplit = notes[i].split(" ");

            for (let j = 0; j < groupSplit.length; j = j+1) { //for every group in line

                //setup shapeSingleNote
                if (shapeSingleNote[i][j] == null) shapeSingleNote[i][j] = [];
                //setup stemSingleNote
                if (stemSingleNote[i][j] == null) stemSingleNote[i][j] = []; 
                //setup shapeGroupNote
                if (connectGroupNote[i][j] == null) connectGroupNote[i][j] = [];
                //setup bar
                if (bar[i][j] == null) bar[i][j] = [];

                // Loop through notes
                for (let x = 0; x < groupSplit[j].length; x = x+1) {
                    //setup shapeGroupNote
                    if (x+1 < groupSplit[j].length)
                        if (connectGroupNote[i][j][x+1] == null) connectGroupNote[i][j][x+1] = 1;

                    if (groupSplit[j].length > 1 && groupSplit[j].length < 4) {}
                    else {
                        // // //setup shapeSingleNote
                        // if (shapeSingleNote[i][j][x] == null) shapeSingleNote[i][j][x] = null;
                        // //setup stemSingleNote
                        // if (stemSingleNote[i][j][x] == null) stemSingleNote[i][j][x] = null;
                    }

                }
            }
        }catch (error){
          console.log(error);
        }
    }
}


function resetController() {
    //----Attributes
    modernStaves = "";
    oldStaves = "";
    currentStyle = 0;
    zoom = 1;
    textMode = 0;
    //---textarea attributes
    nLine = 0;//current line index in textarea

    //----Shapes parameters--------
    linesInLine = [];  //old staves parameter for number of lines in line
    shapeGroupNote = []; //group shape id
    shapeSingleNote = []; //single note shape id
    stemSingleNote = []; //stem direction single note id
    connectGroupNote = []; //connecting line in group id
    bar = [];
    custos = [];
    melodicStructure = "";
    clickSingleNote = 0;
    //------default Shape Parameters------------------------------
    pes_type = default_pes;
    united_clivis = default_clivis;
    climacus_type = default_climacus;
    porrectus_type = default_porrectus;
    plica_type = default_plica;
    scandicus_type = default_scandicus;
    //---------Controller text and annotations--------------------
    texts = [""];
    annotations = [""];
    selectedText = 0;
    selectedAnnotations = 0;
    //------------------------------------------------------------
}


function highlightSelectedNote() {
    var cursorPosition = $('#music_input').prop("selectionStart");
    var inputValue = $('#music_input').val();
  
    let spartito = document.getElementById("music_input").value;
    spartito = spartito.substring(0,cursorPosition);
    if (spartito.match(/[_#%\+\/\(\)\*\\\s\n\[\]'<>]/g) !== null) {
       var specialSigns = spartito.match(/[_#%\+\/\(\)\*\\\s\n\[\]'<>]/g).length;
    }else{
        var specialSigns = 0;
    }
    if (spartito.match(/[FCG][1-9] ?|[bh]} ?/gmi) !== null) {
      var keys = spartito.match(/[FCG][1-9] ?|[bh]} ?/gmi).length*2;  
    }else{
        keys = 0;
    }

    let count = specialSigns + keys;
    var classOfSelecterElement = "note"+(cursorPosition-count);
    

    if (document.getElementsByClassName(classOfSelecterElement) != undefined) {
        let selectedElements = document.getElementsByClassName(classOfSelecterElement);
        for (var i = 0; i < selectedElements.length; i++) {
          if (cursorPosition > 0 && inputValue[cursorPosition - 1] === ' ') {
              let parent = selectedElements[i].parentNode;
              try { // in modern notation thee are not rect (yet)
                parent.getElementsByTagName("rect")[0].classList.add("selectedSyl");
              }catch(err){
                // console.log(err); // gets triggered regularly
              }
          } else {
              selectedElements[i].classList.add('selectedNote');            
          }
        }
    }
}

function toggleElementsVisibility(elements) {
    elements.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle("hidden");
    });
}

function getAdvancedParameters() {
  let parMelodicStructure = document.getElementById("checkMelodicStructure").checked;
  let parLineNumber = document.getElementById("checkLineNumber").checked;
  let parAnnotation = document.getElementById("checkAnnotation").checked;
  let parMsBreak = document.getElementById("checkAnnotation").checked;
  let parCarryBFlat = document.getElementById("checkCarryBFlat").checked;
  let parCarryBFlatBrackets = document.getElementById("checkCarriedBFlatBrackets").checked;
  let parBFlatAlwaysInKeySignature = document.getElementById("checkBFlatAlwaysInKeySignature").checked;
  let parOctave = document.getElementById("checkOctaveClef").checked;
  let parPlica = document.getElementById("checkPlica").checked;
  let parAlphabet = document.getElementById("changeMelodicStructure").value;
  let parTextFontModern = document.getElementById("changeTextFontModern").value;
  let parFontSizeModern = document.getElementById("nFontSizeModern").value;
  let parTextFontOld = document.getElementById("changeTextFontOld").value;
  let parFontSizeOld = document.getElementById("nFontSizeOld").value;
  // let parAllowResize = document.getElementById("allowResizeCB").value;
  let parLink = document.getElementById("urlInput").value;
  parLink = parLink == "undefined" ? "" : parLink;
  let parIIIFManifest = document.getElementById("manifestInput").value;
  let parIIIFid = document.getElementById("iiifIdInput").value;
  let parCustos = JSON.stringify(custos);
  let parUseManualMelodicStructure = 0;
  if (!document.getElementById("checkAutomaticMelodicStructure").checked) {
    parUseManualMelodicStructure = 1;
  }
  let parManualMelodicStructure = document.getElementById("manualMelodicStructure").value;
  let parRepetitionPattern = repetitionPattern;
  let parZoomFacsimile = zoomMeasure;
  let parFacsimileX = 0;
  let parFacsimileY = 0;
  try {
    parFacsimileX = facsimileX;
    parFacsimileY = facsimileY;
  }catch(err){console.log(err);}
  let parNotationType = document.getElementById("changeNotationType").value;
  let parSyncTranscriptions = syncModernOld;
  
  advancedParameters = {
    MelodicStructure: parMelodicStructure,
    LineNumber: parLineNumber,
    Annotations: parAnnotation,
    MsLineBreaks: parMsBreak,
    CarryBFlat: parCarryBFlat,
    CarriedBFlatBrackets: parCarryBFlatBrackets,
    BFlatAlwaysInKeySignature: parBFlatAlwaysInKeySignature,
    OctaveClef: parOctave,
    Plica: parPlica,
    Alphabetic: parAlphabet,
    TextFontModern: parTextFontModern,
    FontSizeModern: parFontSizeModern,
    TextFontOld: parTextFontOld,
    FontSizeOld: parFontSizeOld,
    Link: parLink,
    IIIFManifest: parIIIFManifest,
    IIIFid: parIIIFid,
    ZoomFacsimile: parZoomFacsimile,
    facsimileX: parFacsimileX,
    facsimileY: parFacsimileY,
    custos: parCustos,
    UseManualMelodicStructure: parUseManualMelodicStructure,
    ManualMelodicStructure: parManualMelodicStructure,
    RepetitionPattern: parRepetitionPattern,
    NotationType: parNotationType,
    AllowResize: true, // you had this in some examples
    SyncModernOld: parSyncTranscriptions // you had this in some examples
  };
  
  return advancedParameters;
}

function createBarStructure() {
// if there is no bar structure in the database, it needs to be created
  if (bar == null) {

    let notes = document.getElementById("music_input").value;

    notes = cleanKeys(notes)

    notes = notes.replace(/  +/g, ' ');
    notes = notes.replace(/\' |\'/g, "\'");
    let lines = notes.split("\n");
    bar = [];
    for (let i=0; i < lines.length; i=i+1) {
      bar[i] = [];
      for (let j=0; j < lines[i].split(" ").length; j=j+1) {
        bar[i][j] = [];
      }
    }
  }
}

function createCustosStructure() {
// if there is no bar structure in the database, it needs to be created
  if (custos == null) {
    let notes = document.getElementById("music_input").value;
    let lines = notes.split("\n");
    custos = [];
    for (let i=0; i < lines.length; i=i+1) {
      custos[i] = null;
    }
  }
}

// Function to download data to a file
// Works with any file. Filename needs extension, or it just appears as txt
function download(data, filename, type) {
    var file = new Blob([data], {type: type});
    if (window.navigator.msSaveOrOpenBlob) // IE10+
        window.navigator.msSaveOrOpenBlob(file, filename);
    else { // Others
        var a = document.createElement("a"),
                url = URL.createObjectURL(file);
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);  
        }, 0); 
    }
}


function cleanKeys(arr, bh=true) {
  if (bh) {
    arr = cleanBH(arr);
  }
  arr = arr.replace(/\[[CFG][1-9]\] ?/gmi, "");
  return arr.replace(/[CFG][1-9] ?|[1-9] ?/gmi, "");
}

function cleanBH(arr) {
  arr = arr.replace(/\[[bh]}\] ?/gmi, "");
  return arr.replace(/[bh]} ?/gmi, "");
}

function openLineOptionsMenu(line_n){
  // for now custos is the only line option
  selectedLine = line_n;
  if (custosMode == true) {
    extractGroupFromClick(0, line_n);
  }
}

function getImageFileName(){
  let fileName = "";
  try {
    if (localStorage.getItem("current_page") == "compareTool.html"){
      let fileName = idsToCompare.join("_");
    }else{
      let id = document.getElementById("id_input").value;
      let ms = document.getElementById("ms_input").value;
      let f = document.getElementById("f_input").value;
      fileName = `${id}_${ms}_${f}`;
      if (fileName === "__") {
          fileName = "medmel_export";
      }
    }
  }catch{
    fileName = "medmel_export";
  }
  return fileName;
}

// EXPORT PNG
function exportStavesAsPNG(){
  if (localStorage.getItem("current_page") == "compareTool.html")  {
    exportStavesAsPNGcompare();
  }else{
    exportStavesAsPNGEditorViewer();
  }
}
function exportStavesAsPNGcompare() {
    const fileName = getImageFileName();

    const stavesContainer = document.getElementById("stavesContainer");
    stavesContainer.style.height = "auto";
    stavesContainer.style.maxHeight = "none";
    stavesContainer.style.overflow = "visible";
    const margin = 20;

    const staves = document.getElementById("staves");
    staves.style.zoom = "1"; // Rimuove il ridimensionamento temporaneamente
    
    
    html2canvas(stavesContainer, {
        scale: window.devicePixelRatio * 3, // High resolution
        useCORS: true,
        scrollX: 0,
        scrollY: 0,
        width: stavesContainer.scrollWidth + 2 * margin, // Add margin to width
        height: stavesContainer.scrollHeight + 2 * margin, // Add margin to height
        x: -margin, // Shift capture to the left
        y: -margin // Shift capture upward
    }).then(canvas => {
        const link = document.createElement("a");
        link.href = canvas.toDataURL("image/png");
        link.download = fileName + ".png";
        link.click();
    });
}
function exportStavesAsPNGEditorViewer() {
    try {
        document.getElementsByClassName("selectedNote")[0]?.classList.remove("selectedNote");
    } catch {}

    const fileName = getImageFileName();

    const stavesContainer = document.getElementById("stavesContainer");
    stavesContainer.style.height = "auto";
    stavesContainer.style.maxHeight = "none";
    stavesContainer.style.overflow = "visible";
    const margin = 20;

    const staves = document.getElementById("staves");
    staves.style.zoom = "1"; // Rimuove il ridimensionamento temporaneamente
    
    const titles = document.getElementsByClassName("title");
    const authors = document.getElementsByClassName("author");
    const id_mss_fs = document.getElementsByClassName("id_mss_fs");

    for (let i = 0; i < titles.length; i++) {
        // Create a wrapper for title, author, and id_mss_fs
        const wrapper = document.createElement("div");
        wrapper.style.display = "flex";
        wrapper.style.flexDirection = "column";
        wrapper.style.alignItems = "center"; // Centers text
        wrapper.style.justifyContent = "center";
        wrapper.style.width = `${stavesContainer.scrollWidth}px`; // Match width of the score
        wrapper.style.marginBottom = "20px"; // Space before the score

        // Move elements inside the wrapper
        titles[i].parentNode.insertBefore(wrapper, titles[i]); 
        wrapper.appendChild(titles[i]);
        wrapper.appendChild(authors[i]);
        wrapper.appendChild(id_mss_fs[i]);
    }
    
    html2canvas(stavesContainer, {
        scale: window.devicePixelRatio * 3, // High resolution
        useCORS: true,
        scrollX: 0,
        scrollY: 0,
        width: stavesContainer.scrollWidth + 2 * margin, // Add margin to width
        height: stavesContainer.scrollHeight + 2 * margin, // Add margin to height
        x: -margin, // Shift capture to the left
        y: -margin // Shift capture upward
    }).then(canvas => {
        const link = document.createElement("a");
        link.href = canvas.toDataURL("image/png");
        link.download = fileName + ".png";
        link.click();
    });
}

// Load html2canvas after everything is already loaded
window.addEventListener("DOMContentLoaded", function() {
  const script = document.createElement("script");
  script.src = "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js";
  script.onload = function() {
    try{
      document.getElementById("exportToPNG").disabled = false;
    }catch{
    }
    // Initialize Mirador here if necessary
  };
  document.body.appendChild(script);
});

window.onload = () => {
    setTimeout(() => {
      const div = document.getElementById("stavesContainer");

        // console.log(div.scrollHeight); // Dovrebbe ora essere corretto
    }, 500);
};



function resizeColumns(rightColumn) {
  var rightColClass = "col-md-" + rightColumnWidth;
  var leftColumnClass = "col-md-" + leftColumnsWidth;
  
  for (let i = 1; i <= 12; i++) {
    document.getElementById('stavesBox').classList.remove("col-md-"+i);
  }
  
  document.getElementById("stavesBox").classList.remove(rightColClass);
  
  for (let i = 1; i <= 12; i++) {
    document.getElementById('containerSideBar').classList.remove("col-md-"+i);
  }

  document.getElementById("containerSideBar").classList.remove(leftColumnClass);
  
  rightColumnWidth = rightColumn;
  leftColumnsWidth = 12 - rightColumnWidth;
  rightColClass = "col-md-" + rightColumnWidth;
  leftColumnClass = "col-md-" + leftColumnsWidth;
  
  leftColumnWidth = 100 - rightColumn;
  document.getElementById("stavesBox").classList.add(rightColClass);
  document.getElementById("containerSideBar").classList.add(leftColumnClass);

  if (allowResize) {
    resizeStaves();
  }
}

var rightColumnWidth = 6;
var leftColumnsWidth = 6;

setTimeout(function (){
dragElementBar(document.getElementById("resizeBar"));
}, 200);

function dragElementBar(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  try{
    elmnt.onmousedown = dragMouseDownBar;
  }catch(err){/*console.log(err); // muted: it is triggered all the times*/}

  function dragMouseDownBar(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    // pos4 = e.clientY;
    document.onmouseup = closeDragElementBar;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDragBar;
  }

  function elementDragBar(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos3 = e.clientX;
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";

    let aTwelwthOfWindow = window.innerWidth/12;
    //console.log(b);
    let colMeasure = Math.floor(pos3/aTwelwthOfWindow);
    if (colMeasure > 10) {colMeasure=10;}
    if (colMeasure < 1) {colMeasure=1;}
    resizeColumns(colMeasure)
    elmnt.style.left = (colMeasure-pos3/aTwelwthOfWindow) + "px";

  }

  function closeDragElementBar() {
    // stop moving when mouse button is released:
    document.onmouseup = null;
    document.onmousemove = null;
  }
}