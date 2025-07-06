function checkLineDeleted(newNotes, oldNotes){
    let currentLines = "";
    let oldLines = "";
    try{
        currentLines = newNotes.replace(/  +/g, ' ');
        oldLines = oldNotes.replace(/  +/g, ' ');
    }catch{}

    if (currentStyle == 0){//modern
        currentLines = currentLines.replace(/\n/g,' ');
        oldLines = oldLines.replace(/\n/g,' ');

        currentLines = currentLines.split("\'");
        oldLines = oldLines.split("\'");
    }
    else if (currentStyle == 1){//old
        currentLines = currentLines.replace(/\'/g,' ');
        oldLines = oldLines.replace(/\'/g,' ');

        currentLines = currentLines.split("\n");
        oldLines = oldLines.split("\n");

    }
    //remove spaces at start for each row
    currentLines = removeFirstSpaces(currentLines);
    oldLines = removeFirstSpaces(oldLines);

    //-----index contain the index of the first line without match-----
    let textarea =  document.getElementById("music_input");
    index = textarea.value.substr(0, textarea.selectionStart).split("\n").length-1;

    // log("linesInLine: "+linesInLine);
    if (currentLines.length - oldLines.length >= 1){//one or more lines added
        let linesAdded = currentLines.length - oldLines.length;
        try {//if user press enter at line start position and line contain text
            if(linesAdded == 1 && currentLines[index] == oldLines[index-1])
                index = index -1;
        } catch {}
        if(linesAdded < 1) return 0;

        index = index - (linesAdded-1);
        for (let i = 0; i<linesAdded; i = i +1){
            linesInLine.splice(i+index, 0, 4);
            custos.splice(i+index, 0, null);
          }
    }
    else if(oldLines.length - currentLines.length  >= 1 ){//one or more lines deleted
        let linesDeleted = oldLines.length - currentLines.length;
        if(linesDeleted < 1) return 0;
        index = index +1;
        if(index<0) index = 0;
        linesInLine.splice(index, linesDeleted);
        custos.splice(index, linesDeleted);

    }
    if(currentLines.length!= null && linesInLine.length < currentLines.length){
        while(linesInLine.length < currentLines.length){
            linesInLine.push(4);
            custos.push(null);
        }

    }
    else if(currentLines == ""){
      linesInLine = [4];
      custos = [null];
    }

}

/*************************
**************************/
// aumentare numero note in gruppo : OK
// staccare e riattaccare gruppi : funziona solo nella prima riga
// andare a capo : not OK. Toccare solo le parti interessate
/*************************
**************************/


function checkNoteDeleted(newNotes, oldNotes){
  log("checkNoteDeleted");
  log(`${oldNotes} -> ${newNotes}`);
    let currentLines = "";
    let oldLines = "";
    try{
        currentLines = newNotes.replace(/  +/g, ' ');
        oldLines = oldNotes.replace(/  +/g, ' ');
    }catch{}
    
    currentLines = cleanKeys(currentLines, false);
    currentLines = removeSpaceAtStart(currentLines);
    
    oldLines = cleanKeys(oldLines, false);
    oldLines = removeSpaceAtStart(oldLines);

    if (currentStyle == 0){
        //remove (\n) from notes
        currentLines = currentLines.replace(/\n/g, ' ');
        oldLines = oldLines.replace(/\n/g, ' ');
        //remove multiple space from notes
        currentLines = currentLines.replace(/  +/g, ' ');
        oldLines = oldLines.replace(/  +/g, ' ');
        //remove space at line start
        currentLines = currentLines.replace(/\' |\'/g, "\'");
        oldLines = oldLines.replace(/\' |\'/g, "\'");
        //split note groups into array
        currentLines = currentLines.split("\'");
        oldLines = oldLines.split("\'");

    }
    else if (currentStyle == 1){
        log("C1 - currStyle1");
        //remove (') from notes
        currentLines = currentLines.replace(/\'/g, ' ');
        oldLines = oldLines.replace(/\'/g, ' ');
        //remove multiple space from notes
        currentLines = currentLines.replace(/  +/g, ' ');
        oldLines = oldLines.replace(/  +/g, ' ');
        //remove space at line start
        currentLines = currentLines.replace(/\n |\n/g, "\n");
        oldLines = oldLines.replace(/\n |\n/g, "\n");
        //split note groups into array
        currentLines = currentLines.split("\n");
        oldLines = oldLines.split("\n");
    }
    //remove spaces at last for each line
    currentLines = removeLastSpace(currentLines);
    oldLines = removeLastSpace(oldLines);

    //index contains the index of the first line without match
    let textarea =  document.getElementById("music_input");
    index = textarea.value.substr(0, textarea.selectionStart).split("\n").length-1;

    if (currentLines.length - oldLines.length == 1 ){//new line
      log("C2 - new line");

        //alert(index);
        //check if new line is last line without chars
        if(index == oldLines.length && (currentLines[index] == " " || currentLines[index] == "")){
            //alert("last");
            return 0;
        }
        index = index - 1;
        //check if new line contains a substring from index line in old
        let start = currentLines[index].length;
        if(index < currentLines.length-1)
            if(currentLines[index+1].substring(0, 1) != " ") start = start+1;

        if (currentLines[index] != " " && currentLines[index] != ""){
            let strCompair = "";
            try{
                strCompair = oldLines[index].substring(start, oldLines[index].length);
            }catch{start = -1;}
            if (strCompair != "" && strCompair != " " && strCompair == currentLines[index+1]){
                log("C3 - ?");
                //fill new line with shapes parameter from old line
                //alert("trovata (index : "+index+") : compair : "+strCompair+" - new line : "+currentLines[index+1]);
                let compairSplit = strCompair.split(" ");
                let numberGroup = compairSplit.length;

                let oldSplit = currentLines[index].split(" ");
                let oldIndex = oldSplit.length;
                // alert("oldIndex"+oldIndex+" - groups to copy: "+numberGroup); // QUESTO non funziona bene secondo me: funziona se vai a capo qui (aa |aaa) ma nn se vai a capo qui (a|a aa)
                //log("oldIndex : "+oldIndex );
                let tmp_group = [];
                let tmp_note = [];
                let tmp_stem = [];
                let tmp_conn = [];
                let tmp_bar = [];

                //----Setup value previous line
                let tmp_group1 = [];
                let tmp_note1 = [];
                let tmp_stem1 = [];
                let tmp_conn1 = [];
                let tmp_bar1 = [];
                for (let i = 0; i < oldIndex; i= i+1){
                    //alert(i+" - shape: "+shapeSingleNote[index][i]);
                    tmp_group1[i] = shapeGroupNote[index][i];
                    tmp_note1[i] = shapeSingleNote[index][i];
                    tmp_stem1[i] = stemSingleNote[index][i];
                    tmp_conn1[i] = connectGroupNote[index][i];
                    tmp_bar1[i] = bar[index][i];
                    //log("I. tmp_bar1: "+tmp_bar1);
                    // tmp_bar1 = bar[index][i][1];
                }
                tmp_group[index] = tmp_group1;
                tmp_note[index] = tmp_note1;
                tmp_stem[index] = tmp_stem1;
                tmp_conn[index] = tmp_conn1;
                tmp_bar[index] = tmp_bar1;
                //log("II. tmp_bar: "+tmp_bar + " index:" + index);

                //--setup value new line------------
                tmp_group1 = [];
                tmp_note1 = [];
                tmp_stem1 = [];
                tmp_conn1 = [];
                tmp_bar1 = [[],[]];
                for (let i = 0; i <numberGroup; i= i+1){
                    //alert(i+" - shape: "+shapeSingleNote[index][i]);
                    tmp_group1[i] = shapeGroupNote[index][i+oldIndex];
                    tmp_note1[i] = shapeSingleNote[index][i+oldIndex];
                    tmp_stem1[i] = stemSingleNote[index][i+oldIndex];
                    tmp_conn1[i] = connectGroupNote[index][i+oldIndex];
                    tmp_bar1[i] = bar[index][i+oldIndex];
                }
                tmp_group[index+1] = tmp_group1;
                tmp_note[index+1] = tmp_note1;
                tmp_stem[index+1] = tmp_stem1;
                tmp_conn[index+1] = tmp_conn1;
                tmp_bar[index+1] = tmp_bar1;

                //----copy the others lines in dataset
                for (let i = 0; i < index; i= i+1){
                    tmp_group[i] = shapeGroupNote[i];
                    tmp_note[i] = shapeSingleNote[i];
                    tmp_stem[i] = stemSingleNote[i];
                    tmp_conn[i] = connectGroupNote[i];
                    tmp_bar[i] = bar[i];
                }
                for (let i = index+2; i < shapeGroupNote.length+1; i= i+1){
                    tmp_group[i] = shapeGroupNote[i-1];
                    tmp_note[i] = shapeSingleNote[i-1];
                    tmp_stem[i] = stemSingleNote[i-1];
                    tmp_conn[i] = connectGroupNote[i-1];
                    tmp_bar[i] = bar[i-1];
                }
                shapeGroupNote = tmp_group;
                shapeSingleNote = tmp_note;
                stemSingleNote = tmp_stem;
                connectGroupNote = tmp_conn;
                bar = tmp_bar;
            }
            else {
              log("C4 - ?");
              // check if number of groups is the same when lines is added (e.g. if group has been split in two lines aa|aa)
              let nOfGroupsInCurrentLine0 = currentLines[index].split(" ").length;
              let nOfGroupsInCurrentLine1 = currentLines[index+1].split(" ").length;
              let nOfGroupsInOldLine = oldLines[index].split(" ").length;
              let diffOfnOfGroups = nOfGroupsInCurrentLine0 + nOfGroupsInCurrentLine1 - nOfGroupsInOldLine;
              if (diffOfnOfGroups > 0) {
                log("C5 - More groups (group has been split in two lines) ");

                let tmp_group = [];
                let tmp_note = [];
                let tmp_stem = [];
                let tmp_conn = [];
                let tmp_bar = [];
                // create empty slots for every line (plus one for the new line)
                for (let i = 0; i < bar.length+1; i=i+1){
                      tmp_bar[i] = [];
                      tmp_note[i] = [];
                      tmp_stem[i] = [];
                      tmp_conn[i] = [];
                      tmp_group[i] = [];
                }
                // copy all that was before and untouched
                for(let i = 0; i < index; i= i+1){
                    tmp_bar[i] = bar[i];
                    tmp_note[i] = shapeSingleNote[i];
                    tmp_stem[i] = stemSingleNote[i];
                    tmp_conn[i] = connectGroupNote[i];
                    tmp_group[i] = shapeGroupNote[i];
                }
                // give first part of line its values
                for (let j = 0; j < nOfGroupsInCurrentLine0-1; j=j+1){
                    tmp_bar[index][j] = bar[index][j];
                    tmp_note[index][j] = shapeSingleNote[index][j] ;
                    tmp_stem[index][j] = stemSingleNote[index][j];
                    tmp_conn[index][j] = connectGroupNote[index][j];
                    tmp_group[index][j] = shapeGroupNote[index][j];

                }
                // give second part of line its values
                for (let j = 0; j < nOfGroupsInCurrentLine1; j=j+1){
                    tmp_bar[index+1][j] = bar[index][j+nOfGroupsInCurrentLine0-1];
                    if (j > 0){
                      tmp_group[index+1][j] = shapeGroupNote[index][j+nOfGroupsInCurrentLine0-1]; // non dare un valore al nuovo gruppo che si è formato
                      tmp_note[index+1][j] = shapeSingleNote[index][j+nOfGroupsInCurrentLine0-1];
                      tmp_stem[index+1][j] = stemSingleNote[index][j+nOfGroupsInCurrentLine0-1];
                      tmp_conn[index+1][j] = connectGroupNote[index][j+nOfGroupsInCurrentLine0-1];
                    }
                }
                //copy all the rest untouched
                for (let i = index+2; i < bar.length+1; i=i+1){
                      tmp_bar[i] = bar[i-1];
                      tmp_note[i] = shapeSingleNote[i-1];
                      tmp_stem[i] = stemSingleNote[i-1];
                      tmp_conn[i] = connectGroupNote[i-1];
                      tmp_group[i] = shapeGroupNote[i-1];
                }

                shapeGroupNote = tmp_group;
                shapeSingleNote = tmp_note;
                stemSingleNote = tmp_stem;
                connectGroupNote = tmp_conn;
                bar = tmp_bar;

              }


                //--New line detected translate array positions - index = new line position
                // let tmp_group = [];
                // let tmp_note = [];
                // let tmp_stem = [];
                // let tmp_conn = [];
                //  let tmp_bar = [[],[]];
                //   for (let i = 0; i < index; i= i+1){
                    //tmp_group[i] = shapeGroupNote[i];
                    // tmp_note[i] = shapeSingleNote[i];
                    // tmp_stem[i] = stemSingleNote[i];
                    // tmp_conn[i] = connectGroupNote[i];
                //    tmp_bar[i] = bar[i];
                // }
                // for(let i = index+1; i < shapeGroupNote.length+1; i= i+1){
                  //  tmp_group[i] = shapeGroupNote[i-1];
                    // tmp_note[i] = shapeSingleNote[i-1];
                    // tmp_stem[i] = stemSingleNote[i-1];
                    // tmp_conn[i] = connectGroupNote[i-1];
                    //tmp_bar[i] = bar[i-1];
                // }
                // shapeGroupNote = tmp_group;
                // shapeSingleNote = tmp_note;
                // stemSingleNote = tmp_stem;
                // connectGroupNote = tmp_conn;
                // bar = tmp_bar;
            }
        }
        else {
          log("C6 - New line ...?");
            //--New line detected translate array positions - index = new line position
            
            let tmp_group = [];
            let tmp_note = [];
            let tmp_stem = [];
            let tmp_conn = [];
            let tmp_bar = [];
            for (let i = 0; i < index; i= i+1){
                tmp_group[i] = shapeGroupNote[i];
                tmp_note[i] = shapeSingleNote[i];
                tmp_stem[i] = stemSingleNote[i];
                tmp_conn[i] = connectGroupNote[i];
                tmp_bar[i] = bar[i];
            }
            for (let i = index+1; i < shapeGroupNote.length+1; i++){
                tmp_group[i] = shapeGroupNote[i-1];
                tmp_note[i] = shapeSingleNote[i-1];
                tmp_stem[i] = stemSingleNote[i-1];
                tmp_conn[i] = connectGroupNote[i-1];
                tmp_bar[i] = bar[i-1];
            }
            shapeGroupNote = tmp_group;
            shapeSingleNote = tmp_note;
            stemSingleNote = tmp_stem;
            connectGroupNote = tmp_conn;
            bar = tmp_bar;
        }
        //alert(index+" - start : "+start);
    }
    else if (currentLines.length == oldLines.length ) { //same line - note deleted or added
        log("C7 - same line note deleted or added");
        let currentLineSplit = String(currentLines[index]).split(" ");
        let oldLinesSplit = String(oldLines[index]).split(" ");

        //find the index of the modified group
       let indexGroup = 0;
       while(indexGroup < oldLinesSplit.length && indexGroup < currentLineSplit.length && currentLineSplit[indexGroup] == oldLinesSplit[indexGroup]){
           indexGroup = indexGroup +1;
       }

        if (currentLineSplit.length < oldLinesSplit.length){//group deleted
          log("C8 - group deleted");

            let tmp_group = [];
            let tmp_note = [];
            let tmp_stem = [];
            let tmp_conn = [];
            let tmp_bar = [];
            for (let i = 0; i < indexGroup; i++){
                tmp_group[i] = shapeGroupNote[index][i];
                tmp_note[i] = shapeSingleNote[index][i];
                tmp_stem[i] = stemSingleNote[index][i];
                tmp_conn[i] = connectGroupNote[index][i];
                tmp_bar[i] = bar[index][i];
            }
            for (let i = indexGroup; i < shapeGroupNote[index].length-1; i++){
                tmp_group[i] = shapeGroupNote[index][i+1];
                tmp_note[i] = shapeSingleNote[index][i+1];
                tmp_stem[i] = stemSingleNote[index][i+1];
                tmp_conn[i] = connectGroupNote[index][i+1];
                tmp_bar[i] = bar[index][i+1];
            }
            shapeGroupNote[index] = tmp_group;
            shapeSingleNote[index] = tmp_note;
            stemSingleNote[index] = tmp_stem;
            connectGroupNote[index] = tmp_conn;
            bar[index] = tmp_bar;
            //alert("Gruppo eliminato - "+indexGroup+" - "+oldLinesSplit[indexGroup]);
        }
        else if (currentLineSplit.length == oldLinesSplit.length){ //same group number, note inside group deleted or added
            log("C8 - same group number, note inside group deleted or added");
            if(typeof oldLinesSplit === "undefined" || typeof currentLineSplit[indexGroup] === "undefined" ) return;

            let tmp_group = [];
            let tmp_note = [];
            let tmp_stem = [];
            let tmp_conn = [];
            //  let tmp_bar = []; // DON'T uncomment
            for (let i = 0; i < shapeGroupNote[index].length; i= i+1){
                if (i != indexGroup){
                    tmp_group[i] = shapeGroupNote[index][i];
                    tmp_note[i] = shapeSingleNote[index][i];
                    tmp_stem[i] = stemSingleNote[index][i];
                    tmp_conn[i] = connectGroupNote[index][i];
                //    tmp_bar[i] = bar[index][i]; // DON'T uncomment
                }

            }
            shapeGroupNote[index] = tmp_group;
            shapeSingleNote[index] = tmp_note;
            stemSingleNote[index] = tmp_stem;
            connectGroupNote[index] = tmp_conn;
          //  bar[index] = tmp_bar; // don't uncomment COMMENTATO PERCHé ELIMINAVA LE BARRE QUANDO SI AGGIUNGONO NOTE A UN GRUPPO
            //alert("Gruppo modificato - "+indexGroup+" - Da : "+oldLinesSplit[indexGroup]+" - A : "+currentLineSplit[indexGroup]);
        }
        else if (currentLineSplit.length > oldLinesSplit.length){// group added
            log("group added");
            let tmp_group = [];
            let tmp_note = [];
            let tmp_stem = [];
            let tmp_conn = [];
            let tmp_bar = [];
            for (let i = 0; i < indexGroup; i= i+1){
            // ogni volta che un gruppo è aggiunto, per ogni gruppo crea un temp
                tmp_group[i] = shapeGroupNote[index][i];
                tmp_note[i] = shapeSingleNote[index][i];
                tmp_stem[i] = stemSingleNote[index][i];
                tmp_conn[i] = connectGroupNote[index][i];
                tmp_bar[i] = bar[index][i];
            }
            for (let i = indexGroup+1; i < shapeGroupNote[index].length+1; i=i+1){
                tmp_group[i] = shapeGroupNote[index][i-1];
                tmp_note[i] = shapeSingleNote[index][i-1];
                tmp_stem[i] = stemSingleNote[index][i-1];
                tmp_conn[i] = connectGroupNote[index][i-1];
            }

            for (let i = indexGroup+1; i < bar[index].length+1; i=i+1){
                tmp_bar[i] = bar[index][i-1];
            }

            shapeGroupNote[index] = tmp_group;
            shapeSingleNote[index] = tmp_note;
            stemSingleNote[index] = tmp_stem;
            connectGroupNote[index] = tmp_conn;
            bar[index] = tmp_bar;
            // alert("Gruppo Aggiunto - "+indexGroup+" - "+currentLineSplit[indexGroup]+ " - tmp :" );
            log(shapeGroupNote)
            log(shapeSingleNote)
        }
    }
    else if (oldLines.length - currentLines.length  == 1 ){ // line deleted
        log("C10 - line deleted");

        let start = oldLines[index].length;
        if (index < oldLines.length-1)
            if (oldLines[index+1].substring(0, 1) != " ") start = start+1;


        if (oldLines[index] != " " && oldLines[index] != ""){

            let strCompair = "";
            try{
                strCompair = currentLines[index].substring(start, currentLines[index].length);
            }catch{ start = -1;}


            //check if new line contains a substring from index line in old
            let nOfGroupsInOldLine0 = oldLines[index].split(" ").length;
            let nOfGroupsInOldLine1 = oldLines[index+1].split(" ").length;
            let nOfGroupsInCurrentLine = currentLines[index].split(" ").length;
            // log(nOfGroupsInOldLine0 + nOfGroupsInOldLine1 - nOfGroupsInCurrentLine);
            if (nOfGroupsInOldLine0 + nOfGroupsInOldLine1 - nOfGroupsInCurrentLine > 0) {
              // log("line deleted, merging two  groups");
              let compairSplit = strCompair.split(" ");
              let numberGroup = compairSplit.length;

              let oldSplit = oldLines[index].split(" ");
              let oldIndex = oldSplit.length;
              //alert("oldIndex"+oldIndex+" - groups to copy: "+numberGroup);
              let tmp_group = [];
              let tmp_note = [];
              let tmp_stem = [];
              let tmp_conn = [];
              let tmp_bar = [];
              // create empty slots for every line (plus one for the new line)
              for (let i = 0; i < bar.length; i=i+1){
                tmp_group[i] = [];
                tmp_note[i] = [];
                tmp_stem[i] = [];
                tmp_conn[i] = [];
                tmp_bar[i] = [];
              }
              //copy all that was before and untouched
              for(let i = 0; i < index; i= i+1){
                tmp_group[i] = shapeGroupNote[i];
                tmp_note[i] = shapeSingleNote[i];
                tmp_stem[i] = stemSingleNote[i];
                tmp_conn[i] = connectGroupNote[i];
                tmp_bar[i] = bar[i];
              }
              // give first part of line its values
              let j = 0;

              for (j = 0; j < nOfGroupsInOldLine0-1; j=j+1){
                tmp_group[index][j] = shapeGroupNote[index][j];
                tmp_note[index][j] = shapeSingleNote[index][j];
                tmp_stem[index][j] = stemSingleNote[index][j];
                tmp_conn[index][j] = connectGroupNote[index][j];
                tmp_bar[index][j] = bar[index][j];
              }
              tmp_bar[index][j] = bar[index+1][0];
              j=j+1; // non dare un valore al nuovo gruppo che si è formato

              //   give second part of line its values
              for (let y = 1; y < nOfGroupsInOldLine1; y=y+1){
                tmp_group[index][j] = shapeGroupNote[index+1][y];
                tmp_note[index][j] = shapeSingleNote[index+1][y];
                tmp_stem[index][j] = stemSingleNote[index+1][y];
                tmp_conn[index][j] = connectGroupNote[index+1][y];
                tmp_bar[index][j] = bar[index+1][y];
                j=j+1;
              }
              //copy all the rest untouched
              for (let i = index+2; i < bar.length-1; i=i+1){
                    tmp_group[i] = shapeGroupNote[i+1];
                    tmp_note[i] = shapeSingleNote[i+1];
                    tmp_stem[i] = stemSingleNote[i+1];
                    tmp_conn[i] = connectGroupNote[i+1];
                    tmp_bar[i] = bar[i+1];

              }
              shapeGroupNote = tmp_group;
              shapeSingleNote = tmp_note;
              stemSingleNote = tmp_stem;
              connectGroupNote = tmp_conn;
              bar = tmp_bar;
              return;

            }
            else if(strCompair != "" && strCompair != " " && strCompair == oldLines[index+1]){
                log("line deleted, but preserving the same groups");

                //alert("trovata (index : "+index+") : compair : "+strCompair+" - new line : "+currentLines[index]);
                let compairSplit = strCompair.split(" ");
                let numberGroup = compairSplit.length;

                let oldSplit = oldLines[index].split(" ");
                let oldIndex = oldSplit.length;
                //alert("oldIndex"+oldIndex+" - groups to copy: "+numberGroup);
                let tmp_group = [];
                let tmp_note = [];
                let tmp_stem = [];
                let tmp_conn = [];
                let tmp_bar = [];

                for(let i = 0; i <oldIndex; i= i+1){
                    //alert(i+" - shape: "+shapeSingleNote[index][i]);
                    tmp_group[i] = shapeGroupNote[index][i];
                    tmp_note[i] = shapeSingleNote[index][i];
                    tmp_stem[i] = stemSingleNote[index][i];
                    tmp_conn[i] = connectGroupNote[index][i];
                    tmp_bar[i] = bar[index][i];
                }
                for(let i = 0; i < numberGroup; i = i+1){
                    let newIndex = i+oldIndex;
                    //alert(newIndex+" - shape: "+shapeSingleNote[index+1][i]);
                    tmp_group[newIndex] = shapeGroupNote[index+1][i];
                    tmp_note[newIndex] = shapeSingleNote[index+1][i];
                    tmp_stem[newIndex] = stemSingleNote[index+1][i];
                    tmp_conn[newIndex] = connectGroupNote[index+1][i];
                    tmp_bar[newIndex] = bar[index+1][i];
                  }
                  shapeGroupNote[index+1] = tmp_group;
                  shapeSingleNote[index+1] = tmp_note;
                  stemSingleNote[index+1] = tmp_stem;
                  connectGroupNote[index+1] = tmp_conn;
                  bar[index+1] = tmp_bar;
              }



        //alert(index);
        //------remove line from shapes dataset---------------
        let tmp_group = [];
        let tmp_note = [];
        let tmp_stem = [];
        let tmp_conn = [];
        let tmp_bar = [];

        for (let i = 0; i <index; i= i+1){
            tmp_group[i] = shapeGroupNote[i];
            tmp_note[i] = shapeSingleNote[i];
            tmp_stem[i] = stemSingleNote[i];
            tmp_conn[i] = connectGroupNote[i];
            tmp_bar[i] = bar[i];
        }
        let indexStart =  index;
        if(indexStart == shapeGroupNote.length-1 && shapeGroupNote.length-1 >= 0)
            indexStart = index-1;
        for (let i = indexStart; i < shapeGroupNote.length-1; i = i+1){
            tmp_group[i] = shapeGroupNote[i+1];
            tmp_note[i] = shapeSingleNote[i+1];
            tmp_stem[i] = stemSingleNote[i+1];
            tmp_conn[i] = connectGroupNote[i+1];
            tmp_bar[i] = bar[i+1];
        }
        shapeGroupNote = tmp_group;
        shapeSingleNote = tmp_note;
        stemSingleNote = tmp_stem;
        connectGroupNote = tmp_conn;
        bar = tmp_bar;
      }
        //-------------------------------------------------------
        //alert(index+" - start : "+start);
    }
    else if(currentLines.length - oldLines.length > 1){//pasted multiple lines
        log("C11 - Pasted multiple lines!");
        let linesAdded =  currentLines.length - oldLines.length;
        index = index - (linesAdded-1);
        //alert(linesAdded);
        //--New line detected translate array positions - index = new line position
        let tmp_group = [];
        let tmp_note = [];
        let tmp_stem = [];
        let tmp_conn = [];
        let tmp_bar = [];
        for(let i = 0; i < index; i= i+1){
            tmp_group[i] = shapeGroupNote[i];
            tmp_note[i] = shapeSingleNote[i];
            tmp_stem[i] = stemSingleNote[i];
            tmp_conn[i] = connectGroupNote[i];
            tmp_bar[i] = bar[i];
        }
        for(let i = index+1; i < shapeGroupNote.length+1; i= i+1){
            tmp_group[i] = shapeGroupNote[i-linesAdded];
            tmp_note[i] = shapeSingleNote[i-linesAdded];
            tmp_stem[i] = stemSingleNote[i-linesAdded];
            tmp_conn[i] = connectGroupNote[i-linesAdded];
            tmp_bar[i] = bar[i-linesAdded];
        }
        shapeGroupNote = tmp_group;
        shapeSingleNote = tmp_note;
        stemSingleNote = tmp_stem;
        connectGroupNote = tmp_conn;
        bar = tmp_bar;
    }
    else if(oldLines.length - currentLines.length  > 1 ){ // multiple lines deleted
        log("C12 - deleted multiple lines");
        let linesDeleted =  oldLines.length - currentLines.length;

        //------remove lines from shapes dataset---------------
        let tmp_group = [];
        let tmp_note = [];
        let tmp_stem = [];
        let tmp_conn = [];
        let tmp_bar = [];

        for(let i = 0; i <index; i= i+1){
            tmp_group[i] = shapeGroupNote[i];
            tmp_note[i] = shapeSingleNote[i];
            tmp_stem[i] = stemSingleNote[i];
            tmp_conn[i] = connectGroupNote[i];
            tmp_bar[i] = bar[i];
        }
        let indexStart =  index;
        if(indexStart == shapeGroupNote.length-1 && shapeGroupNote.length-1 >= 0)
            indexStart = index-1;
        for(let i = indexStart; i < shapeGroupNote.length-1; i = i+1){
            tmp_group[i] = shapeGroupNote[i+linesDeleted];
            tmp_note[i] = shapeSingleNote[i+linesDeleted];
            tmp_stem[i] = stemSingleNote[i+linesDeleted];
            tmp_conn[i] = connectGroupNote[i+linesDeleted];
            tmp_bar[i] = bar[i+linesDeleted];
        }
        shapeGroupNote = tmp_group;
        shapeSingleNote = tmp_note;
        stemSingleNote = tmp_stem;
        connectGroupNote = tmp_conn;
        bar = tmp_bar;

    }else{
      log("C13 - don't know what's what");
    }
}

function removeLastSpace(data){
    if(typeof data == 'undefined' || data == []) return [];
    for(let i = 0; i<data.length; i=i+1){

        if(data[i] == ""){}
        else {
            while(String(data[i]).substring(String(data[i]).length-1,String(data[i]).length) == " "){

                    data[i] = String(data[i]).substring(0,String(data[i]).length-1);
            }
        }
    }
    return data;
}

function removeFirstSpaces(data){
    if(typeof data == 'undefined' || data == []) return [];
    for(let i = 0; i<data.length; i=i+1){

        if(data[i] == ""){}
        else {
            while(String(data[i]).substring(0,1) == " "){

                    data[i] = String(data[i]).substring(1,String(data[i]).length);
            }
        }
    }
    return data;
}

function removeBlankLinesAtStart(data){

    let tmp = data;
    let i = 0; //blank lines deleted
    // log(tmp);
    try{
        while(!tmp[0].replace(/  +/g, '').length){ //if line is empty
            tmp.splice(0,1);
            i = i + 1;
        }

    }catch{}

    return [tmp, i];
}

const debugMode = true;

function log(msg) {
    if (debugMode) console.log(msg);
}