function showSearchTooltip() {
  let tooltip =  document.getElementById("searchTooltip");

  if (tooltip.style.display == "none"){
    document.getElementById("searchTooltip").style.display = "block";
    highlightTourElement(0);

  }else{
    hideInfo();
  }
}

function hideInfo() {
  document.getElementById("searchTooltip").style.display = "none";
  document.getElementsByClassName("tour-highlight")[0].classList.remove("tour-highlight");
}

var tooltipNumber = 0;
function nextTooltip() {
  let id = "info" + tooltipNumber;
  if (tooltipNumber < 8) {
    document.getElementById(id).style.display = "none";

    tooltipNumber += 1;
    id = "info" + tooltipNumber;
    document.getElementById(id).style.display = "block";

    document.getElementById("prevTooltip").disabled = false;
    if (tooltipNumber == 7){
      document.getElementById("nextTooltip").disabled = true;
    }else{
      document.getElementById("nextTooltip").disabled = false;
    }
    setArrowDirection();
    highlightTourElement(tooltipNumber)
  }
}

function previousTooltip() {
  let id = "info" + tooltipNumber;
  if (tooltipNumber > -1) {
    document.getElementById(id).style.display = "none";

    tooltipNumber -= 1;
    id = "info" + tooltipNumber;
    document.getElementById(id).style.display = "block";

    document.getElementById("nextTooltip").disabled = false;
    if (tooltipNumber == 0){
      document.getElementById("prevTooltip").disabled = true;
    }
  }
  setArrowDirection();
  highlightTourElement(tooltipNumber)
}

function setArrowDirection(){
  let cl = document.getElementById("searchTooltip").classList;
  cl.remove("pointingLeft");
  cl.remove("pointingUp");
  cl.remove("pointingDown");
  
  if ([1, 2, 3, 4].includes(tooltipNumber)) {
    cl.add("pointingLeft");
  } else if ([5,6,7].includes(tooltipNumber)) {
    cl.add("pointingDown");
  }
  else{
    cl.add("pointingUp");
  }
}

function highlightTourElement(tooltipNumber) {
  var id = "";
  if (tooltipNumber == 0) {
    id = "svgSearchWrap";
  }else if (tooltipNumber == 1) {
    id = "musicStringInput";
  }else if (tooltipNumber == 2) {
    id = "newSylBtn";
  }else if (tooltipNumber == 3) {
    id = "deleteBtn";
  }else if (tooltipNumber == 4) {
    id = "optionsBox";
  }else if (tooltipNumber == 5) {
    id = "matchAccuracyContainer";
  }else if (tooltipNumber == 6) {
    id = "checkboxesContainer";
  }else if (tooltipNumber == 7) {
    id = "positionOptionBox";
  }
  
  var th = document.getElementsByClassName("tour-highlight");
  for (var i = 0; i < th.length; i++) {
    th[i].classList.remove("tour-highlight");
  }
  document.getElementById(id).classList.add("tour-highlight");
}

$("body").click(function(e) {
    //ignore the element of Info
    if(e.target.id == "searchTooltip"){}
    else{
      hideInfo();
    }
});
