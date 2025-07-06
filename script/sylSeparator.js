function sylSeparator() {
  if (getLanguageUI() == "Latin") {
    sylSeparator_latin();
  }else{
    sylSeparator_romance();
  }
}
function sylSeparator_romance() {
  console.log("syl");
  let str = document.getElementById('text_input').value;

  // str = htmlspecialchars(text_string);
  // str = str.replace(/\r/g, "<br/>");
  str = str.replace(/açi/g, 'a-çi');
  str = str.replace(/agr/g, 'a-gr');
  str = str.replace(/aigue/g, 'ai-gue');
  str = str.replace(/aisa/g, 'ai-sa');
  str = str.replace(/ance/g, 'an-ce');
  str = str.replace(/anha/g, 'a-nha');
  str = str.replace(/apl/g, 'a-pl');
  str = str.replace(/aq/g, 'a-q');
  str = str.replace(/arti/g, 'ar-ti');
  str = str.replace(/arzi/g, 'ar-zi');
  str = str.replace(/assa/g, 'as-sa');
  str = str.replace(/asse/g, 'as-se');
  str = str.replace(/asso/g, 'as-so');
  str = str.replace(/atr/g, 'a-tr');
  str = str.replace(/aube/g, 'au-be');
  str = str.replace(/auça/g, 'au-ça');
  str = str.replace(/aucion/g, 'au-ci-on');
  str = str.replace(/auci/g, 'au-ci');
  str = str.replace(/aude/g, 'au-de');
  str = str.replace(/ausa/g, 'au-sa');
  str = str.replace(/ausion/g, 'au-si-on');
  str = str.replace(/ausi/g, 'au-si');
  str = str.replace(/auza/g, 'au-za');
  str = str.replace(/auze/g, 'au-ze');
  str = str.replace(/auzion/g, 'au-zi-on');
  str = str.replace(/auzi/g, 'au-zi');
  str = str.replace(/ayna/g, 'ay-na');
  str = str.replace(/aze/g, 'a-ze');
  str = str.replace(/ceme/g, 'ce-me');
  str = str.replace(/efr/g, 'e-fr');
  str = str.replace(/egr/g, 'e-gr');
  str = str.replace(/eia/g, 'e-ia');
  str = str.replace(/enca/g, 'en-ca');
  str = str.replace(/ence/g, 'en-ce');
  str = str.replace(/ente/g, 'en-te');
  str = str.replace(/etr/g, 'e-tr');
  str = str.replace(/eya/g, 'e-ya');
  str = str.replace(/igr/g, 'e-gr');
  str = str.replace(/ilha/g, 'i-lha');
  str = str.replace(/itr/g, 'i-tr');
  str = str.replace(/ive/g, 'i-ve');
  str = str.replace(/lça/g, 'l-ça');
  str = str.replace(/lço/g, 'l-ço');
  str = str.replace(/lla/g, 'l-la');
  str = str.replace(/lr/g, 'l-r');
  str = str.replace(/ltr/g, 'l-tr');
  str = str.replace(/mbr/g, 'm-br');
  str = str.replace(/nbr/g, 'n-br');
  str = str.replace(/nça/g, 'n-ça');
  str = str.replace(/ncl/g, 'n-cl');
  str = str.replace(/nqa/g, 'n-qa');
  str = str.replace(/nqua/g, 'n-qua');
  str = str.replace(/ns'ai/g, "ns' ai");
  str = str.replace(/nsa/g, 'n-sa');
  str = str.replace(/nta/g, 'n-ta');
  str = str.replace(/nti/g, 'n-ti');
  str = str.replace(/ntr/g, 'n-tr');
  str = str.replace(/nv/g, 'n-v');
  str = str.replace(/nza/g, 'n-za');
  str = str.replace(/oba/g, 'o-ba');
  str = str.replace(/obl/g, 'ob-l');
  str = str.replace(/ogr/g, 'o-gr');
  str = str.replace(/oise/g, 'oi-se');
  str = str.replace(/onme/g, 'on-me');
  str = str.replace(/ossa/g, 'os-sa');
  str = str.replace(/osse/g, 'os-se');
  str = str.replace(/ossi/g, 'os-si');
  str = str.replace(/osso/g, 'os-so');
  str = str.replace(/otr/g, 'o-tr');
  str = str.replace(/oubl/g, 'ou-bl');
  str = str.replace(/ouco/g, 'ou-co');
  str = str.replace(/ousa/g, 'ou-sa');
  str = str.replace(/rr/g, 'r-r');
  str = str.replace(/scl/g, 's-cl');
  str = str.replace(/sia/g, 'si-a');
  str = str.replace(/sinho/g, 'si-nho');
  str = str.replace(/sire/g, 'si-re');
  str = str.replace(/spi/g, 's-pi');
  str = str.replace(/ugr/g, 'u-gr');
  str = str.replace(/utr/g, 'u-tr');
  str = str.replace(/yve/g, 'y-ve');
  str = str.replace(/rdi/g, 'r-di');
  str = str.replace(/bsc/g, 'b-sc');
  str = str.replace(/edou/g, 'e-dou');
  str = str.replace(/ebr/g, 'e-br');
  str = str.replace(/espe/g, 'es-pe');
  str = str.replace(/upta/g, 'up-ta');
  str = str.replace(/mbl/g, 'm-bl');
  str = str.replace(/sco/g, 's-co');
  str = str.replace(/espe/g, 'es-pe');
  str = str.replace(/oeza/g, 'o-e-za');
  str = str.replace(/ayta/g, 'ay-ta');
  str = str.replace(/espe/g, 'es-pe');
  str = str.replace(/nde/g, 'n-de');
  str = str.replace(/lv/g, 'l-v');
  str = str.replace(/arca/g, 'ar-ca');
  str = str.replace(/oia/g, 'o-ia');
  str = str.replace(/oie/g, 'o-ie');
  str = str.replace(/lv/g, 'l-v');
  str = str.replace(/eigno/g, 'ei-gno');
  str = str.replace(/sti/g, 's-ti');
  str = str.replace(/aea/g, 'a-da');
  str = str.replace(/aia/g, 'a-ia');
  str = str.replace(/aqua/g, 'a-qua');
  str = str.replace(/aua/g, 'a-ua');
  str = str.replace(/abr/g, 'a-br');
  str = str.replace(/osaphat/g, 'o-sa-phat');
  str = str.replace(/aike/g, 'ai-ke');
  str = str.replace(/onno/g, 'on-no');
  str = str.replace(/Era/g, 'E-ra');
  str = str.replace(/rça/g, 'r-ça');
  str = str.replace(/rçe/g, 'r-çe');
  str = str.replace(/rçi/g, 'r-çi');
  str = str.replace(/rço/g, 'r-ço');
  str = str.replace(/rçu/g, 'r-çu');
  str = str.replace(/aicho/g, 'ai-cho');
  str = str.replace(/sm/g, 's-m');
  str = str.replace(/aie/g, 'a-ie');
  str = str.replace(/eie/g, 'e-ie');
  str = str.replace(/aye/g, 'a-ye');
  str = str.replace(/aya/g, 'a-ya');
  str = str.replace(/ee/g, 'e-e');
  str = str.replace(/ncha/g, 'n-cha');
  str = str.replace(/nche/g, 'n-che');
  str = str.replace(/nchi/g, 'n-chi');
  str = str.replace(/ncho/g, 'n-cho');
  str = str.replace(/mna/g, 'm-na');
  str = str.replace(/mne/g, 'm-ne');
  str = str.replace(/mni/g, 'm-ni');
  str = str.replace(/mno/g, 'm-no');
  str = str.replace(/mnu/g, 'm-nu');
  str = str.replace(/nma/g, 'n-ma');
  str = str.replace(/nme/g, 'n-me');
  str = str.replace(/nmi/g, 'n-mi');
  str = str.replace(/nmo/g, 'n-mo');
  str = str.replace(/nmu/g, 'n-mu');
  str = str.replace(/oio/g, 'o-io');
  str = str.replace(/’/g, '*');
  str = str.replace(/ç/g, '%');
  str = str.replace(/sie/g, 'si-e');
  str = str.replace(/rie/g, 'ri-e');
  str = str.replace(/ria/g, 'ri-a');
  str = str.replace(/acl/g, 'a-cl');
  str = str.replace(/ecl/g, 'e-cl');
  str = str.replace(/icl/g, 'i-cl');
  str = str.replace(/ocl/g, 'o-cl');
  str = str.replace(/ucl/g, 'u-cl');
  str = str.replace(/rtr/g, 'r-tr');
  str = str.replace(/spl/g, 's-pl');
  str = str.replace(/eiu/g, 'e-iu');
  str = str.replace(/mia/g, 'mi-a');
  str = str.replace(/dia/g, 'di-a');
  str = str.replace(/lia/g, 'li-a');
  str = str.replace(/ayre/g, 'ay-re');
  str = str.replace(/aire/g, 'ai-re');
  str = str.replace(/via/g, 'vi-a');
  str = str.replace(/eay/g, 'e-ay');
  str = str.replace(/pch/g, 'p-ch');
  str = str.replace(/aa/g, 'a-a');
  str = str.replace(/ee/g, 'e-e');
  str = str.replace(/ii/g, 'i-i');
  str = str.replace(/oo/g, 'o-o');
  str = str.replace(/uu/g, 'u-u');
  str = str.replace(/ae/g, 'a-e');
  str = str.replace(/ao/g, 'a-o');
  str = str.replace(/ea/g, 'e-a');
  str = str.replace(/eo/g, 'e-o');
  str = str.replace(/ae/g, 'a-e');
  str = str.replace(/oa/g, 'o-a');
  str = str.replace(/oe/g, 'o-e');
  str = str.replace(/mpn/g, 'm-pn');
  str = str.replace(/mn/g, 'm-n');
  str = str.replace(/zia/g, 'zi-a');
  str = str.replace(/sg/g, 's-g');
  str = str.replace(/nzo/g, 'n-zo');

  let change;
  let change2;
  let change3;
  let changed;
  let changed2;
  let changed3;
  let l;
  let l1;
  let l2;
  let l3;
  let l4;
  let z;
  let lminus1;
  let lminus2;
  let lminus3;
  let addedCharacters = 0;

  // vcv
  for (z = 0; z < str.length; z++) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];

    if (l2 !== 'ç'
    && l !== ' '
    && l1 !== ' '
    && l2 !== ' '
   && (l === 'a'
    || l === 'e'
    || l === 'i'
    || l === 'o'
    || l === 'u'
    || l === 'y'
    || l === 'Y'
    || l === 'A'
    || l === 'E'
    || l === 'I'
    || l === 'O'
    || l === 'U')
   && (
     l1 === 'b'
    || l1 === 'c'
    || l1 === 'd'
    || l1 === 'f'
    || l1 === 'g'
    || l1 === 'h'
    || l1 === 'j'
    || l1 === 'k'
    || l1 === 'l'
    || l1 === 'm'
    || l1 === 'n'
    || l1 === 'p'
    || l1 === 'q'
    || l1 === 'r'
    || l1 === 's'
    || l1 === 't'
    || l1 === 'v'
    || l1 === 'w'
    || l1 === 'x'
    || l1 === '%' // matches ç
    || l1 === 'z')
   && (
     l2 === 'a'
    || l2 === 'e'
    || l2 === 'i'
    || l2 === 'o'
    || l2 === 'u')
    ) {
      change = l + l1 + l2;
      changed = l+"-"+l1+l2;
      str = str.replace(change, changed);
      addedCharacters = addedCharacters +1;
    }
  }

  // vc2v
  addedCharacters = 0;

  for (z = 0; z < str.length; z ++) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u' || l === 'A' || l === 'E' || l === 'I' || l === 'O' || l === 'U')
    && (l2 === l1)
    && (l1 === 'b' || l1 === 'c' || l1 === 'd' || l1 === 'f' || l1 === 'g' || l1 === 'h' || l1 === 'j' || l1 === 'k' || l1 == 'l' || l1 === 'm' || l1 === 'n' || l1 === 'p' || l1 === 'q' || l1 === 'r' || l1 === 's' || l1 === 't' || l1 === 'v' || l1 === 'z')
    && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change2 = l + l1 + l2 + l3;
      changed2 = l + l1+"-"+l2+l3;
      str = str.replace(/change2/g, changed2);
      addedCharacters = addedCharacters +1;
 
    }
  }


  // v[sc]v
  // v[rç]v
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && (l1 === 's' && l2 === 'c')
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;

    }
  }


  // v[r/l]cv
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && (l1 === 'r' || l1 === 'l')
       && (l2 === 'b'
        || l2 === 'c'
        || l2 === 'd'
        || l2 === 'f'
        || l2 === 'g'
        || l2 === 'h'
        || l2 === 'j'
        || l2 === 'k'
        || l2 === 'l'
        || l2 === 'm'
        || l2 === 'n'
        || l2 === 'p'
        || l2 === 'q'
        || l2 === 'r'
        || l2 === 's'
        || l2 === 't'
        || l2 === 'v'
        || l2 === 'w'
        || l2 === 'x'
        || l2 === 'z'
       )
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // [ai/au]cv
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if (l === 'a' && (l1 === 'i' || l1 === 'u')
       && (l2 === 'b'
        || l2 === 'c'
        || l2 === 'd'
        || l2 === 'f'
        || l2 === 'g'
        || l2 === 'h'
        || l2 === 'j'
        || l2 === 'k'
        || l2 === 'l'
        || l2 === 'm'
        || l2 === 'n'
        || l2 === 'p'
        || l2 === 'q'
        || l2 === 'r'
        || l2 === 's'
        || l2 === 't'
        || l2 === 'v'
        || l2 === 'w'
        || l2 === 'x'
        || l2 === 'z'
       )
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[sn]v
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l1 === 'u')
       && l1 === 's'
       && (l2 === 'n' || l2 === 'p' || l2 === 't' || l2 === 'b')
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[str] > v[s-tr]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && l1 === 's'
       && l2 === 't'
       && (l3 === 'r' || l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // [a/i]-[gn/nh]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    l4 = str[z + 4 + addedCharacters];
    if ((l === 'a' || l === 'e')
       && l1 === 'i'
       && (l2 === 'g' || l2 === 'n')
       && (l3 === 'n' || l3 === 'h')
       && (l4 === 'a' || l4 === 'e' || l4 === 'i' || l4 === 'o' || l4 === 'u')) {
      change = l + l1 + l2 + l3 + l4;
      changed = `${l + l1}-${l2}${l3}${l4}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if (
      (l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
     && (
       (l1 === 'g' && l2 === 'n')
        || (l1 === 'n' || l2 === 'h')
     )
      && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')
    ) {
      change = l + l1 + l2 + l3;
      changed = `${l}-${l1}${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // [n-p/b/t/v/f]v/[l] / [m-p/b/t/v]v/[l]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    if ((l === 'n' || l === 'm') && (l1 === 'p' || l1 === 'b' || l1 === 'f' || l1 === 'v' || l1 === 't' || l1 === 'd' || l1 === 'c' || l1 === 'g' || l1 === 's' || l1 === 'q')
            && (l2 === 'a' || l2 === 'e' || l2 === 'i' || l2 === 'o' || l2 === 'u' || l2 === 'l')) {
      change3 = l + l1 + l2;
      changed3 = `${l}-${l1}${l2}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[p/b/f/v/t/d][r]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && (l1 === 'p'
        || l1 === 'b'
        || l1 === 'f'
        || l1 === 'v'
        || l1 === 't'
        || l1 === 'd'
       )
       && l2 === 'r') {
      change3 = l + l1 + l2;
      changed3 = `${l + l1}-${l2}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[ch]v
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && l1 === 'c'
       && l2 === 'h'
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l}-${l1}${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // sinalpha
  // if (n_notes[i] = n_syl[i]) {
  //   synalepha = "no";}
  //   else {
  //     synalepha = "yes";
  //   };
  // if (synalepha === "no"){}else{

  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    lminus1 = str[z - 1 + addedCharacters];
    lminus2 = str[z - 2 + addedCharacters];
    lminus3 = str[z - 3 + addedCharacters];

    if ((lminus2 !== ' ' && lminus3 !== ' ' && lminus1 !== 'a' && lminus1 !== '0')
      && (l === 'a' || l === 'e' || l === 'i' || l === 'o')
       && l1 === ' '
       && (l2 === 'a' || l2 === 'e' || l2 === 'i' || l2 === 'o' || l2 === 'u')
    ) {
      change3 = lminus3 + lminus2 + lminus1 + l + l1 + l2;
      changed3 = `${lminus3 + lminus2 + lminus1 + l}_${l2}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }
  // }


  str = str.replace(/si-eu/g, 'sieu');
  str = str.replace(/_io/g, ' io');
  str = str.replace(/_ia/g, ' ia');
  str = str.replace(/_ie/g, ' ie');
  str = str.replace(/_iu/g, ' iu');
  str = str.replace(/\*/g, '’');
  const textString = str.replace(/%/g, 'ç');
  setTimeout(()=>{
    document.getElementById('text_input').value = textString;
    console.log(document.getElementById('text_input').value);
    updateStaves(false);
  }, 10);
}

function sylSeparator_latin() {
  let str = document.getElementById('text_input').value;

  // str = htmlspecialchars(text_string);
  // str = str.replace(/\r/g, "<br/>");
  str = str.replace(/açi/g, 'a-çi');
  str = str.replace(/agr/g, 'a-gr');
  str = str.replace(/aigue/g, 'ai-gue');
  str = str.replace(/aisa/g, 'ai-sa');
  str = str.replace(/ance/g, 'an-ce');
  str = str.replace(/anha/g, 'a-nha');
  str = str.replace(/apl/g, 'a-pl');
  str = str.replace(/aq/g, 'a-q');
  str = str.replace(/arti/g, 'ar-ti');
  str = str.replace(/arzi/g, 'ar-zi');
  str = str.replace(/assa/g, 'as-sa');
  str = str.replace(/asse/g, 'as-se');
  str = str.replace(/asso/g, 'as-so');
  str = str.replace(/atr/g, 'a-tr');
  str = str.replace(/aube/g, 'au-be');
  str = str.replace(/auça/g, 'au-ça');
  str = str.replace(/aucion/g, 'au-ci-on');
  str = str.replace(/auci/g, 'au-ci');
  str = str.replace(/aude/g, 'au-de');
  str = str.replace(/ausa/g, 'au-sa');
  str = str.replace(/ausion/g, 'au-si-on');
  str = str.replace(/ausi/g, 'au-si');
  str = str.replace(/auza/g, 'au-za');
  str = str.replace(/auze/g, 'au-ze');
  str = str.replace(/auzion/g, 'au-zi-on');
  str = str.replace(/auzi/g, 'au-zi');
  str = str.replace(/ayna/g, 'ay-na');
  str = str.replace(/aze/g, 'a-ze');
  str = str.replace(/ceme/g, 'ce-me');
  str = str.replace(/efr/g, 'e-fr');
  str = str.replace(/egr/g, 'e-gr');
  str = str.replace(/eia/g, 'e-ia');
  str = str.replace(/enca/g, 'en-ca');
  str = str.replace(/ence/g, 'en-ce');
  str = str.replace(/ente/g, 'en-te');
  str = str.replace(/etr/g, 'e-tr');
  str = str.replace(/eya/g, 'e-ya');
  str = str.replace(/igr/g, 'e-gr');
  str = str.replace(/ilha/g, 'i-lha');
  str = str.replace(/itr/g, 'i-tr');
  str = str.replace(/ive/g, 'i-ve');
  str = str.replace(/lça/g, 'l-ça');
  str = str.replace(/lço/g, 'l-ço');
  str = str.replace(/lla/g, 'l-la');
  str = str.replace(/lr/g, 'l-r');
  str = str.replace(/ltr/g, 'l-tr');
  str = str.replace(/mbr/g, 'm-br');
  str = str.replace(/nbr/g, 'n-br');
  str = str.replace(/nça/g, 'n-ça');
  str = str.replace(/ncl/g, 'n-cl');
  str = str.replace(/nqa/g, 'n-qa');
  str = str.replace(/nqua/g, 'n-qua');
  str = str.replace(/ns'ai/g, "ns' ai");
  str = str.replace(/nsa/g, 'n-sa');
  str = str.replace(/nta/g, 'n-ta');
  str = str.replace(/nti/g, 'n-ti');
  str = str.replace(/ntr/g, 'n-tr');
  str = str.replace(/nv/g, 'n-v');
  str = str.replace(/nza/g, 'n-za');
  str = str.replace(/oba/g, 'o-ba');
  str = str.replace(/obl/g, 'ob-l');
  str = str.replace(/ogr/g, 'o-gr');
  str = str.replace(/oise/g, 'oi-se');
  str = str.replace(/onme/g, 'on-me');
  str = str.replace(/ossa/g, 'os-sa');
  str = str.replace(/osse/g, 'os-se');
  str = str.replace(/ossi/g, 'os-si');
  str = str.replace(/osso/g, 'os-so');
  str = str.replace(/otr/g, 'o-tr');
  str = str.replace(/oubl/g, 'ou-bl');
  str = str.replace(/ouco/g, 'ou-co');
  str = str.replace(/ousa/g, 'ou-sa');
  str = str.replace(/rr/g, 'r-r');
  str = str.replace(/scl/g, 's-cl');
  str = str.replace(/sia/g, 'si-a');
  str = str.replace(/sinho/g, 'si-nho');
  str = str.replace(/sire/g, 'si-re');
  str = str.replace(/spi/g, 's-pi');
  str = str.replace(/ugr/g, 'u-gr');
  str = str.replace(/utr/g, 'u-tr');
  str = str.replace(/yve/g, 'y-ve');
  str = str.replace(/rdi/g, 'r-di');
  str = str.replace(/bsc/g, 'b-sc');
  str = str.replace(/edou/g, 'e-dou');
  str = str.replace(/ebr/g, 'e-br');
  str = str.replace(/espe/g, 'es-pe');
  str = str.replace(/upta/g, 'up-ta');
  str = str.replace(/mbl/g, 'm-bl');
  str = str.replace(/sco/g, 's-co');
  str = str.replace(/espe/g, 'es-pe');
  str = str.replace(/oeza/g, 'o-e-za');
  str = str.replace(/ayta/g, 'ay-ta');
  str = str.replace(/espe/g, 'es-pe');
  str = str.replace(/nde/g, 'n-de');
  str = str.replace(/lv/g, 'l-v');
  str = str.replace(/arca/g, 'ar-ca');
  str = str.replace(/oia/g, 'o-ia');
  str = str.replace(/oie/g, 'o-ie');
  str = str.replace(/lv/g, 'l-v');
  str = str.replace(/eigno/g, 'ei-gno');
  str = str.replace(/sti/g, 's-ti');
  str = str.replace(/aea/g, 'a-da');
  str = str.replace(/aia/g, 'a-ia');
  str = str.replace(/aqua/g, 'a-qua');
  str = str.replace(/aua/g, 'a-ua');
  str = str.replace(/abr/g, 'a-br');
  str = str.replace(/osaphat/g, 'o-sa-phat');
  str = str.replace(/aike/g, 'ai-ke');
  str = str.replace(/onno/g, 'on-no');
  str = str.replace(/Era/g, 'E-ra');
  str = str.replace(/rça/g, 'r-ça');
  str = str.replace(/rçe/g, 'r-çe');
  str = str.replace(/rçi/g, 'r-çi');
  str = str.replace(/rço/g, 'r-ço');
  str = str.replace(/rçu/g, 'r-çu');
  str = str.replace(/aicho/g, 'ai-cho');
  str = str.replace(/sm/g, 's-m');
  str = str.replace(/aie/g, 'a-ie');
  str = str.replace(/eie/g, 'e-ie');
  str = str.replace(/aye/g, 'a-ye');
  str = str.replace(/aya/g, 'a-ya');
  str = str.replace(/ee/g, 'e-e');
  str = str.replace(/ncha/g, 'n-cha');
  str = str.replace(/nche/g, 'n-che');
  str = str.replace(/nchi/g, 'n-chi');
  str = str.replace(/ncho/g, 'n-cho');
  str = str.replace(/mna/g, 'm-na');
  str = str.replace(/mne/g, 'm-ne');
  str = str.replace(/mni/g, 'm-ni');
  str = str.replace(/mno/g, 'm-no');
  str = str.replace(/mnu/g, 'm-nu');
  str = str.replace(/nma/g, 'n-ma');
  str = str.replace(/nme/g, 'n-me');
  str = str.replace(/nmi/g, 'n-mi');
  str = str.replace(/nmo/g, 'n-mo');
  str = str.replace(/nmu/g, 'n-mu');
  str = str.replace(/oio/g, 'o-io');
  str = str.replace(/’/g, '*');
  str = str.replace(/ç/g, '%');
  str = str.replace(/sie/g, 'si-e');
  str = str.replace(/rie/g, 'ri-e');
  str = str.replace(/ria/g, 'ri-a');
  str = str.replace(/acl/g, 'a-cl');
  str = str.replace(/ecl/g, 'e-cl');
  str = str.replace(/icl/g, 'i-cl');
  str = str.replace(/ocl/g, 'o-cl');
  str = str.replace(/ucl/g, 'u-cl');
  str = str.replace(/rtr/g, 'r-tr');
  str = str.replace(/spl/g, 's-pl');
  str = str.replace(/eiu/g, 'e-iu');
  str = str.replace(/mia/g, 'mi-a');
  str = str.replace(/dia/g, 'di-a');
  str = str.replace(/lia/g, 'li-a');
  str = str.replace(/ayre/g, 'ay-re');
  str = str.replace(/aire/g, 'ai-re');
  str = str.replace(/via/g, 'vi-a');
  str = str.replace(/eay/g, 'e-ay');
  str = str.replace(/pch/g, 'p-ch');
  str = str.replace(/aa/g, 'a-a');
  str = str.replace(/ee/g, 'e-e');
  str = str.replace(/ii/g, 'i-i');
  str = str.replace(/oo/g, 'o-o');
  str = str.replace(/uu/g, 'u-u');
  str = str.replace(/ae/g, 'a-e');
  str = str.replace(/ao/g, 'a-o');
  str = str.replace(/ea/g, 'e-a');
  str = str.replace(/eo/g, 'e-o');
  str = str.replace(/ae/g, 'a-e');
  str = str.replace(/oa/g, 'o-a');
  str = str.replace(/oe/g, 'o-e');
  str = str.replace(/mpn/g, 'm-pn');
  str = str.replace(/mn/g, 'm-n');
  str = str.replace(/zia/g, 'zi-a');
  str = str.replace(/sg/g, 's-g');
  str = str.replace(/nzo/g, 'n-zo');

  let change;
  let change2;
  let change3;
  let changed;
  let changed2;
  let changed3;
  let l;
  let l1;
  let l2;
  let l3;
  let l4;
  let z;
  let lminus1;
  let lminus2;
  let lminus3;
  let addedCharacters = 0;

  // vcv
  for (z = 0; z < str.length; z++) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];

    if (l2 !== 'ç'
    && l !== ' '
    && l1 !== ' '
    && l2 !== ' '
   && (l === 'a'
    || l === 'e'
    || l === 'i'
    || l === 'o'
    || l === 'u'
    || l === 'y'
    || l === 'Y'
    || l === 'A'
    || l === 'E'
    || l === 'I'
    || l === 'O'
    || l === 'U')
   && (
     l1 === 'b'
    || l1 === 'c'
    || l1 === 'd'
    || l1 === 'f'
    || l1 === 'g'
    || l1 === 'h'
    || l1 === 'j'
    || l1 === 'k'
    || l1 === 'l'
    || l1 === 'm'
    || l1 === 'n'
    || l1 === 'p'
    || l1 === 'q'
    || l1 === 'r'
    || l1 === 's'
    || l1 === 't'
    || l1 === 'v'
    || l1 === 'w'
    || l1 === 'x'
    || l1 === '%' // matches ç
    || l1 === 'z')
   && (
     l2 === 'a'
    || l2 === 'e'
    || l2 === 'i'
    || l2 === 'o'
    || l2 === 'u')
    ) {
      change = l + l1 + l2;
      changed = l+"-"+l1+l2;
      str = str.replace(change, changed);
      addedCharacters = addedCharacters +1;
    }
  }

  // vc2v
  addedCharacters = 0;

  for (z = 0; z < str.length; z ++) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u' || l === 'A' || l === 'E' || l === 'I' || l === 'O' || l === 'U')
    && (l2 === l1)
    && (l1 === 'b' || l1 === 'c' || l1 === 'd' || l1 === 'f' || l1 === 'g' || l1 === 'h' || l1 === 'j' || l1 === 'k' || l1 == 'l' || l1 === 'm' || l1 === 'n' || l1 === 'p' || l1 === 'q' || l1 === 'r' || l1 === 's' || l1 === 't' || l1 === 'v' || l1 === 'z')
    && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change2 = l + l1 + l2 + l3;
      changed2 = l + l1+"-"+l2+l3;
      str = str.replace(/change2/g, changed2);
      addedCharacters = addedCharacters +1;
 
    }
  }


  // v[sc]v
  // v[rç]v
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && (l1 === 's' && l2 === 'c')
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;

    }
  }


  // v[r/l]cv
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && (l1 === 'r' || l1 === 'l')
       && (l2 === 'b'
        || l2 === 'c'
        || l2 === 'd'
        || l2 === 'f'
        || l2 === 'g'
        || l2 === 'h'
        || l2 === 'j'
        || l2 === 'k'
        || l2 === 'l'
        || l2 === 'm'
        || l2 === 'n'
        || l2 === 'p'
        || l2 === 'q'
        || l2 === 'r'
        || l2 === 's'
        || l2 === 't'
        || l2 === 'v'
        || l2 === 'w'
        || l2 === 'x'
        || l2 === 'z'
       )
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // [ai/au]cv
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if (l === 'a' && (l1 === 'i' || l1 === 'u')
       && (l2 === 'b'
        || l2 === 'c'
        || l2 === 'd'
        || l2 === 'f'
        || l2 === 'g'
        || l2 === 'h'
        || l2 === 'j'
        || l2 === 'k'
        || l2 === 'l'
        || l2 === 'm'
        || l2 === 'n'
        || l2 === 'p'
        || l2 === 'q'
        || l2 === 'r'
        || l2 === 's'
        || l2 === 't'
        || l2 === 'v'
        || l2 === 'w'
        || l2 === 'x'
        || l2 === 'z'
       )
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[sn]v
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l1 === 'u')
       && l1 === 's'
       && (l2 === 'n' || l2 === 'p' || l2 === 't' || l2 === 'b')
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[str] > v[s-tr]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && l1 === 's'
       && l2 === 't'
       && (l3 === 'r' || l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l + l1}-${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // [a/i]-[gn/nh]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    l4 = str[z + 4 + addedCharacters];
    if ((l === 'a' || l === 'e')
       && l1 === 'i'
       && (l2 === 'g' || l2 === 'n')
       && (l3 === 'n' || l3 === 'h')
       && (l4 === 'a' || l4 === 'e' || l4 === 'i' || l4 === 'o' || l4 === 'u')) {
      change = l + l1 + l2 + l3 + l4;
      changed = `${l + l1}-${l2}${l3}${l4}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if (
      (l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
     && (
       (l1 === 'g' && l2 === 'n')
        || (l1 === 'n' || l2 === 'h')
     )
      && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')
    ) {
      change = l + l1 + l2 + l3;
      changed = `${l}-${l1}${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // [n-p/b/t/v/f]v/[l] / [m-p/b/t/v]v/[l]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    if ((l === 'n' || l === 'm') && (l1 === 'p' || l1 === 'b' || l1 === 'f' || l1 === 'v' || l1 === 't' || l1 === 'd' || l1 === 'c' || l1 === 'g' || l1 === 's' || l1 === 'q')
            && (l2 === 'a' || l2 === 'e' || l2 === 'i' || l2 === 'o' || l2 === 'u' || l2 === 'l')) {
      change3 = l + l1 + l2;
      changed3 = `${l}-${l1}${l2}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[p/b/f/v/t/d][r]
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && (l1 === 'p'
        || l1 === 'b'
        || l1 === 'f'
        || l1 === 'v'
        || l1 === 't'
        || l1 === 'd'
       )
       && l2 === 'r') {
      change3 = l + l1 + l2;
      changed3 = `${l + l1}-${l2}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // v[ch]v
  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    if ((l === 'a' || l === 'e' || l === 'i' || l === 'o' || l === 'u')
       && l1 === 'c'
       && l2 === 'h'
       && (l3 === 'a' || l3 === 'e' || l3 === 'i' || l3 === 'o' || l3 === 'u')) {
      change3 = l + l1 + l2 + l3;
      changed3 = `${l}-${l1}${l2}${l3}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }

  // sinalpha
  // if (n_notes[i] = n_syl[i]) {
  //   synalepha = "no";}
  //   else {
  //     synalepha = "yes";
  //   };
  // if (synalepha === "no"){}else{

  for (z = 0; z < str.length; z += 1) {
    l = str[z + addedCharacters];
    l1 = str[z + 1 + addedCharacters];
    l2 = str[z + 2 + addedCharacters];
    l3 = str[z + 3 + addedCharacters];
    lminus1 = str[z - 1 + addedCharacters];
    lminus2 = str[z - 2 + addedCharacters];
    lminus3 = str[z - 3 + addedCharacters];

    if ((lminus2 !== ' ' && lminus3 !== ' ' && lminus1 !== 'a' && lminus1 !== '0')
      && (l === 'a' || l === 'e' || l === 'i' || l === 'o')
       && l1 === ' '
       && (l2 === 'a' || l2 === 'e' || l2 === 'i' || l2 === 'o' || l2 === 'u')
    ) {
      change3 = lminus3 + lminus2 + lminus1 + l + l1 + l2;
      changed3 = `${lminus3 + lminus2 + lminus1 + l}_${l2}`;
      str = str.replace(change3, changed3);
      addedCharacters = addedCharacters +1;
    }
  }
  // }


  str = str.replace(/si-eu/g, 'sieu');
  str = str.replace(/_io/g, ' io');
  str = str.replace(/_ia/g, ' ia');
  str = str.replace(/_ie/g, ' ie');
  str = str.replace(/_iu/g, ' iu');
  str = str.replace(/\*/g, '’');
  const textString = str.replace(/%/g, 'ç');

  document.getElementById('text_input').value = textString;
  console.log(textString);
  updateStaves(false);
}
