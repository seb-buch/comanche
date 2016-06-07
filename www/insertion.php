<!-- TODO
  - fonction updatenb:
      Ajouter la gestion de l'air par lipide pour déterminer le nombre de lipides par feuillet 
  - fonction pdbctrl
  - fonction topctrl
  - fonction pdbidctrl
-->
<?php
if (!(isset($_SESSION['username']))) {
  die("You must be logged in!");
  }
$connect = mysql_connect("localhost","root","comanche");
if (!$connect) {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("comanche_db", $connect);
$sql = "SELECT * FROM forcefield";
$result = mysql_query($sql);
$forcefield = array();
$lipidname = array();
$lipidcommon = array();
while ($rows = mysql_fetch_assoc($result)) {
  $ff = $rows['forcefield'];
  if ((!in_array($ff, $forcefield))) {
    $forcefield[] = $ff;
    }
  $lipidname[$ff][$rows['name']] = $rows['reverse'];
  $lipidcommon[$ff][$rows['common']] = $rows['reverse'];
  }
$sql = "SELECT membrane,nblipids FROM membranes";
$result = mysql_query($sql);
$membrane = array();
$membranelipids = array();
$membrane[] = "Custom";
while ($rows = mysql_fetch_assoc($result)) {
  $mb = $rows['membrane'];
  $nb = $rows['nblipids'];
  if ((!in_array($mb, $membrane))) {
    $membrane[] = $mb;
    $membranelipids[$mb] = array();
    }
  if ((!in_array($nb, array_keys($membranelipids[$mb])))) {
    $membranelipids[$mb][$nb] = array();
    $membranelipids[$mb][$nb]['upper'] = array();
    $membranelipids[$mb][$nb]['lower'] = array();
    }
  }
$sql = "select * from membranelipids;";
$result = mysql_query($sql);
while ($rows = mysql_fetch_assoc($result)) {
  $mb = $rows['membrane'];
  $lp = $rows['name'];
  $nl = $rows['number'];
  $po = $rows['position'];
  $nb = $rows['nblipids'];
  if ((!in_array($lp, array_keys($membranelipids[$mb][$nb][$po])))) {
    $membranelipids[$mb][$nb][$po][$lp] = $nl; 
    }
  }
?>

<script type="text/javascript"> 
  function updatelipids(){
    var e, i, memb, lipid, lipids, nbkeys, text, nblip ;
    var jArray= <?php echo json_encode($membranelipids ); ?>;
    e = document.getElementById("MembraneComposition");
    memb = e.options[e.selectedIndex].text;
    if (memb != "Custom") { 
      nbkeys = Object.keys(jArray[memb]);
      lipids = Object.keys(jArray[memb][nbkeys[0]]['upper']); 
      lipid = "";
      for ( i = 0; i < lipids.length; i++) {
        lipid = lipid + jArray[memb][nbkeys[0]]['upper'][lipids[i]] + "\t" + lipids[i] + "\n"; 
        }
      document.getElementById('LipidsUp').value = lipid.slice(0,-1); 
      lipids = Object.keys(jArray[memb][nbkeys[0]]['lower']); 
      lipid = "";
      for ( i = 0; i < lipids.length; i++) {
        lipid = lipid + jArray[memb][nbkeys[0]]['lower'][lipids[i]] + "\t" + lipids[i] + "\n"; 
        }
      document.getElementById('LipidsDown').value = lipid.trim(); 
      }
    else {
      document.getElementById('LipidsUp').value = ""; 
      document.getElementById('LipidsDown').value = ""; 
      }
    updatenb();
    }
  function checklipids() {
    var ff, lipid, nb, lipidsup, lipidsdown, lipids, text, regex, lipidsre;
    var name = <?php echo json_encode($lipidname); ?>;
    var common = <?php echo json_encode($lipidcommon); ?>;
    var mamiss = [];
    var ffmiss = [];
    var remiss = [];
    var error = [];
    ff = document.getElementById('forcefield').value;
    malipidname = Object.keys(name['Martini']); 
    malipidcommon = Object.keys(common['Martini']); 
    fflipidname = Object.keys(name[ff]); 
    fflipidcommon = Object.keys(common[ff]); 
    lipidsup = document.getElementById('LipidsUp').value.split("\n"); 
    lipidsdown = document.getElementById('LipidsDown').value.split("\n"); 
    lipids = lipidsup.concat(document.getElementById('LipidsDown').value.split("\n"));
    regex = /(\s+)/g;
    if (document.getElementById('LipidsUp').value != "" && document.getElementById('LipidsDown').value != "") {
      for ( i = 0; i < lipids.length; i++) {
        lipidsre = lipids[i].replace(regex, ' ');
        nb = lipidsre.split(" ")[0];
        lipid = lipidsre.split(" ")[1];
        if ( ! isValidInteger(nb) ) {
          error.push(nb)  
          }
        if (malipidname.indexOf(lipid) == -1 && malipidcommon.indexOf(lipid) == -1) {
          if (mamiss.indexOf(lipid) == -1) {
            mamiss.push(lipid); 
            }
          }
        if (ff != "Martini") {
          if (fflipidname.indexOf(lipid) == -1 && fflipidcommon.indexOf(lipid) == -1) {
            if (ffmiss.indexOf(lipid) == -1) {
              ffmiss.push(lipid); 
              }
            else if (fflipidname.indexOf(lipid) >= 0) {
              if (name[ff][lipid] == 0) {
                remiss.push(lipid);
                }
              }
            else if (fflipidcommon.indexOf(lipid) >= 0) {
              if (common[ff][lipid] == 0) {
                remiss.push(lipid);
                }
              }
            }
          }
        }
      text = "";
      if (mamiss.length >= 1) {
        text = text + "<strong>Missing Martini topologies: </strong>" + mamiss.join(", ") + "<br>"; 
        }
      if (ffmiss.length >= 1) {
        text = text + "<strong>Missing "+ ff +" topologies: </strong>" + ffmiss.join(", ") + "<br>"; 
        }
      if (remiss.length >= 1) {
        text = text + "<strong>Missing "+ ff +" reverse mapping: </strong>" + remiss.join(", ") + "<br>"; 
        }
      if (error.length >= 1) {
        text = text + "<strong>These values are not numbers: </strong>" + error.join(", ") + "<br>"; 
        }
      document.getElementById('checklipids').innerHTML = text ;
      }
    else {
      document.getElementById('checklipids').innerHTML = "<strong>There is no lipids</strong>";
      }
    }
  function updatenb() {
    var lipidsup, lipidsdown, nblipids, nball, nball2, nblipidsup, nblipidsdown, regex, i, nb, lipid, lipidsre, zero, line;
    var lipidsupraw, lipidsdownraw;
    var lipidsuparr = [];
    var lipidsdownarr = [];  
    var jArray= <?php echo json_encode($membranelipids ); ?>;
    nblipids = parseFloat(document.getElementById('Nblipids').value);
    zero = "False";
    e = document.getElementById("MembraneComposition");
    memb = e.options[e.selectedIndex].text;
    if (memb == "Custom") { 
      regex = /(\s+)/g;
      lipidsupraw = document.getElementById('LipidsUp').value.split("\n"); 
      nbup = 0;
      for ( i = 0; i < lipidsupraw.length; i++) {
        lipidsre = lipidsupraw[i].replace(regex, ' ');
        lipidsuparr[lipidsre.split(" ")[1]] = lipidsre.split(" ")[0]; 
        nbup = nbup + parseFloat(lipidsre.split(" ")[0]);
        }
      lipidsup = getSortedKeys(lipidsuparr);
      lipidsdownraw = document.getElementById('LipidsDown').value.split("\n"); 
      nbdown = 0;
      for ( i = 0; i < lipidsdownraw.length; i++) {
        lipidsre = lipidsdownraw[i].replace(regex, ' ');
        lipidsdownarr[lipidsre.split(" ")[1]] = lipidsre.split(" ")[0]; 
        nbdown = nbdown + parseFloat(lipidsre.split(" ")[0]);
        }
      lipidsdown = getSortedKeys(lipidsdownarr);
      nblipidsup = Math.round(nblipids*nbup/(nbup+nbdown));
      nblipidsdown = nblipids - nblipidsup;
      nball = 0;
      text = "";
      for ( i = 0; i < lipidsup.length; i++) {
        nb = parseFloat(lipidsuparr[lipidsup[i]]);
        nb = Math.round(nb * nblipidsup / nbup);
        if ( i == lipidsup.length -1) {
          nb = nblipidsup - nball;
          }
        text = nb.toString() + "\t" + lipidsup[i] + "\n" + text;
        nball = nball + parseFloat(nb);
        if (nb == 0) {
          zero = "True";
          }
        }
      document.getElementById('LipidsUp').value = text.trim();
      nball = 0;
      text = "";
      for ( i = 0; i < lipidsdown.length; i++) {
        nb = parseFloat(lipidsdownarr[lipidsdown[i]]);
        nb = Math.round(nb * nblipidsdown / nbdown);
        if ( i == lipidsdown.length -1) {
          nb = nblipidsdown - nball;
          }
        text = nb.toString() + "\t" + lipidsdown[i] + "\n" + text;
        nball = nball + parseFloat(nb);
        if (nb == 0) {
          zero = "True";
          }
        }
      document.getElementById('LipidsDown').value = text.trim();
      }
    else {
      nbkeys = Object.keys(jArray[memb]);
      lipidsup =  getSortedKeys(jArray[memb][nbkeys[0]]['upper']); 
      lipidsdown = getSortedKeys(jArray[memb][nbkeys[0]]['lower']); 
      nbkeysup = 0;
      for ( i = 0; i < lipidsup.length; i++) {
        nbkeysup = nbkeysup + parseFloat(jArray[memb][nbkeys[0]]['upper'][lipidsup[i]]); 
        }
      nbkeysdown = 0;
      for ( i = 0; i < lipidsdown.length; i++) {
        nbkeysdown = nbkeysdown + parseFloat(jArray[memb][nbkeys[0]]['lower'][lipidsdown[i]]); 
        }
      nblipidsup = Math.round(nblipids*nbkeysup/(nbkeysup+nbkeysdown));
      nblipidsdown = nblipids - nblipidsup;
      nball = 0;
      lipid = "";
      for ( i = 0; i < lipidsup.length; i++) {
        nb = Math.round(parseFloat(jArray[memb][nbkeys[0]]['upper'][lipidsup[i]])*nblipidsup/nbkeysup); 
        if ( i == lipidsup.length -1) {
          nb = nblipidsup - nball;
          }
        lipid = nb.toString() + "\t" + lipidsup[i] + "\n" + lipid; 
        nball = nball + parseFloat(nb);
        if (nb == 0) {
          zero = "True";
          }
        }
      document.getElementById('LipidsUp').value = lipid.slice(0,-1); 
      nball = 0;
      lipid = "";
      for ( i = 0; i < lipidsdown.length; i++) {
        nb = Math.round(parseFloat(jArray[memb][nbkeys[0]]['lower'][lipidsdown[i]])*nblipidsdown/nbkeysdown); 
        if ( i == lipidsdown.length -1) {
          nb = nblipidsdown - nball;
          }
        lipid = nb.toString() + "\t" + lipidsdown[i] + "\n" + lipid; 
        nball = nball + parseFloat(nb);
        if (nb == 0) {
          zero = "True";
          }
        }
      document.getElementById('LipidsDown').value = lipid.trim(); 
      }
    if (zero == "True") {
      document.getElementById('checklipidsnb').innerHTML = "<strong>Some lipids have 0 as number</strong>";
      }
    else {
      document.getElementById('checklipidsnb').innerHTML = "";
      }
    checklipids();
    }
  function isValidInteger(str) {
    return /^-?\d+$/.test(str) && String(Number(str)) === str;
    }
  function LipidsChange() {
    document.getElementById("MembraneComposition").selectedIndex = 0;
    }
  function getSortedKeys(obj) {
    var keys = []; 
    for(var key in obj) keys.push(key);
    return keys.sort(function(a,b){return obj[a]-obj[b]});
    }
  function addstruct() {
    var cell, element, ielement, i, j, test;
    var table = document.getElementById("StructTable");
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var names = ["pdbcode","","pdbfile","uploadpdb","topfile","uploadtop","nbstruct","delstruct"];
    var types = ["text","","text","div","text","div","text","button"];
    var index; /* = String(rowCount);*/
    index = 1;
    test = document.getElementById(names[0] + index); 
    while (test) { 
      index++ ;
      test = document.getElementById(names[0] + index);
      } 
    row.id = "StructRow" + index;
    for ( i=0 ; i<8 ; i++ ) {
      cell = row.insertCell(i);
      if (i == 2 || i == 4) {
        cell.style.textAlign = "right";
        }
      else {
        cell.style.textAlign = "center";
        }
      if (i != 1) {
        if (types[i] != "div") {
          element = document.createElement("input");
          element.type = types[i];
          element.id = names[i] + index;
          cell.appendChild(element);
          }
        else {
          element = document.createElement("div");
          element.innerHTML = '<span>...</span>'; 
          element.className = "fileUpload";
          cell.appendChild(element);
          ielement = document.createElement("input");
          ielement.type = "file";
          ielement.id = names[i] + index;
          ielement.className = "upload";
          element.appendChild(ielement);
          }
        }
      }
    document.getElementById(names[3] + index).onchange = function() { document.getElementById(names[2] + index).value = document.getElementById(names[3] + index).value ; document.getElementById(names[0]+index).disabled = true;};
    document.getElementById(names[5] + index).onchange = function() { document.getElementById(names[4] + index).value = document.getElementById(names[5] + index).value ; document.getElementById(names[0]+index).disabled = true;};
    document.getElementById(names[2] + index).disabled = true ; 
    document.getElementById(names[4] + index).disabled = true ; 
    document.getElementById(names[0] + index).size = "4" ; 
    document.getElementById(names[0] + index).maxlength = "4" ; 
    document.getElementById(names[6] + index).size = "4" ; 
    document.getElementById(names[6] + index).maxlength = "4" ;
    element = document.getElementById(names[7] + index); 
    element.value = "Delete" ; 
    /*element.setAttribute("onclick", "delstruct('" + String(parseInt(index)) + "')");*/
    element.setAttribute("onclick", "delstruct('" + row.id + "')");
    element = document.getElementById(names[0] + index);
    element.setAttribute("onkeyup", "PDBIDChange("+index+")");
    }
  function addseq() {
    var cell, element, test;
    var table = document.getElementById("SeqTable");
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var names = ["Ncap","seq","Ccap","ss","nbseq","delseq"];
    var types = ["select-one","text","select-one","select-one","text","button"];
    var create = ["select","input","select","select","input","input"];
    var index; 
    index = 1;
    test = document.getElementById(names[0] + index); 
    while (test) { 
      index++ ;
      test = document.getElementById(names[0] + index);
      } 
    row.id = "SeqRow" + index;
    for ( i=0 ; i<6 ; i++ ) {
      cell = row.insertCell(i);
      cell.style.textAlign = "center";
      element = document.createElement(create[i]);
      element.type = types[i];
      element.id = names[i] + index;
      cell.appendChild(element);
      }
    var element = document.createElement("option");
    element.text = "NH3+";   
    element.value = "NH3+";   
    document.getElementById(names[0] + index).options.add(element); 
    var element = document.createElement("option");
    element.text = "NH2";   
    element.value = "NH2";   
    document.getElementById(names[0] + index).options.add(element); 
    var element = document.createElement("option");
    element.text = "COO-";   
    element.value = "COO-";   
    document.getElementById(names[2] + index).options.add(element); 
    var element = document.createElement("option");
    element.text = "COOH";   
    element.value = "COOH";   
    document.getElementById(names[2] + index).options.add(element); 
    var element = document.createElement("option");
    element.text = "Helix";   
    element.value = "Helix";   
    document.getElementById(names[3] + index).options.add(element); 
    var element = document.createElement("option");
    element.text = "Beta";   
    element.value = "Beta";   
    document.getElementById(names[3] + index).options.add(element); 
    document.getElementById(names[1] + index).size = "50" ; 
    document.getElementById(names[1] + index).maxlength = "30" ; 
    document.getElementById(names[4] + index).size = "4" ; 
    document.getElementById(names[4] + index).maxlength = "4" ;
    element = document.getElementById(names[5] + index); 
    element.value = "Delete" ; 
    element.setAttribute("onclick", "delseq('" + row.id + "')");
    }
  function delstruct(element) {
    var table = document.getElementById("StructTable");
    var index = table.rows.namedItem(element).rowIndex;
    table.deleteRow(index);
    }
  function delseq(element) {
    var table = document.getElementById("SeqTable");
    var index = table.rows.namedItem(element).rowIndex;
    table.deleteRow(index);
    }
  function PDBIDChange(index) {
    var temp = document.getElementById("pdbcode"+index).value ;
    if (temp == "") {
      document.getElementById("uploadpdb"+index).disabled = false;
      document.getElementById("uploadtop"+index).disabled = false;
      }
    else {
      document.getElementById("uploadpdb"+index).disabled = true;
      document.getElementById("uploadtop"+index).disabled = true;
      }
    }
  function LipidsUpSize() {
    alert("Test");
    }
</script>    

<form action='index.php?page=insertion' method='POST' submit=true>
<h1>System options</h1>
<table class=center border="0" cellspacing="2" cellpadding="5">
  <tr>
    <td align=center>Name: <input type="text" name="seqname" size="10" maxlength="10" value='<?php echo $seqname ?>' /><br /></td>
    <td align=center>Forcefield</td>
    <td align=center>
      <select name="forcefield" id="forcefield" onchange="return checklipids()">
        <?php 
          $i = 0;
          foreach ($forcefield as $value) {
            $i = $i + 1;
            if ($i == 1) {
              echo "<option value='$value'selected>$value</option>";
              }
            else {
              echo "<option value='$value'>$value</option>";
              }
            }
        ?>
      </select>
    </td>
  </tr>
  <tr>
    <td align=center>Membranes</td>
    <td align=center>Temperature</td>
    <td align=center>Nb lipids</td>
  </tr>
  <tr>
    <td align=center>
      <select name="MembraneComposition" id="MembraneComposition" onchange="return updatelipids()">
        <?php 
          $i = 0;
          foreach ($membrane as $value) {
            $i = $i + 1;
            if ($i == 1) {
              echo "<option value='$value'selected>$value</option>";
              }
            else {
              echo "<option value='$value'>$value</option>";
              }
            }
        ?>
      </select>
    </td>
    <td align=center>
      <input type="text" name="TEMP" size="3" maxlength="3" value='<?php if (isset($_POST['TEMP'])){echo $TEMP;}else{echo "297";} ?>' /></td>
    </td>
    <td align=center>
      <select name="Nblipids" id="Nblipids" onchange="return updatenb()">
        <option value="  128"selected>128</option>
        <option value="  256">256</option>
        <option value="  512">512</option>
        <option value=" 1024">1024</option>
        <option value=" 2048">2048</option>
        <option value=" 4096">4096</option>
        <option value=" 8192">8192</option>
        <option value="16384">16384</option>
        <option value="32768">32768</option>
      </select>
    </td>
  <tr/>
</table>
<br />
<h1>Lipids</h1>
<table  class=center border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td align=center>Lipids Up</td>
    <td align=center>Lipids Down</td>
  <tr/>
  <tr>
    <td align=center>
      <textarea class=textarea id="LipidsUp" cols="40" rows="7" onkeyup="LipidsChange()" onresize="LipidsUpSize();" ></textarea>
    </td>
    <td align=center>
      <textarea class=textarea id="LipidsDown" cols="40" rows="7" onkeyup="LipidsChange()"></textarea>
    </td>
  </tr>
</table>
<p id="checklipidsnb"></p>
<p id="checklipids"></p>
<br />
<h1>Interacting molecules</h1>
<table class=center style="width:725px" id="StructTable" border="0" cellspacing="0" cellpadding="0">
  <tr id=StructRow0>
    <td align=center>PDB ID</td>
    <td></td>
    <td align=center colspan = "2">PDB Structure</td>
    <td align=center colspan = "2">Topology</td>
    <td align=center>Nb prot<td>
  </tr>
  <tr id=StructRow1>  
    <td align=center><input type="text" name="pdbcode1" id="pdbcode1"  size="4" maxlength="4" autocomplete="off" onkeyup="PDBIDChange('1')"/></td>
    <td align=center>Or</td>                            
    <td align=right><input type="text" name="pdbfile1" id="pdbfile1"  size="20" maxlength="20" autocomplete="off" disabled/></td>
    <td align=center>
      <div class="fileUpload">
        <span>...</span>
        <input class="upload" type="file" id="uploadpdb1" onchange="getElementById('pdbfile1').value = getElementById('uploadpdb1').value; getElementById('pdbcode1').disabled = true;"/>
      </div>
    </td>
    <td align=right><input type="text" name="topfile1" id="topfile1"  size="20" maxlength="20" autocomplete="off" disabled/></td>
    <td align=center>
      <div class="fileUpload">
        <span>...</span>
        <input class="upload" type="file" id="uploadtop1" onchange="getElementById('topfile1').value = getElementById('uploadtop1').value; getElementById('pdbcode1').disabled = true;"/>
      </div>
    </td>
    <td align=center><input type="text" name="nbstruct1" id="nbstruct1" size="4" maxlength="4" autocomplete="off" /></td>
    <td align=center><input type='button' name="Delete" value='Delete' id="delstruct1" onclick="delstruct('StructRow1');"></td>   	
  </tr>
</table>
<table  class=center border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td align=center><input type='button' name="Add" value='Add' id="AddStruct" onclick="addstruct();"></td>   	
  </tr>
</table>

<table class=center id="SeqTable" border="0" cellspacing="2" cellpadding="0">
  <tr id="SeqRow0">
    <td align=center>N-cap</td>
    <td align=center>Sequence</td>
    <td align=center>C-cap</td>
    <td align=center>Structure</td>
    <td align=center>Nb prot<td>
  </tr>
  <tr id="SeqRow1">
    <td align=center>
      <select name="Ncap" id="Ncap1">
        <option value="NH3+">NH3+</option>
        <option value="NH2">NH2</option>
      </select>
    </td>
    <td><input type="text" name="sequence" id="seq1" size="50" maxlength="30" autocomplete="off" /></td>
    <td align=center>
      <select name="Ccap" id="Ccap1">
        <option value="COO-">COO-</option>
        <option value="COOH">COOH</option>
      </select>
    </td>
    <td align=center>
      <select name="Structure" id="ss1">
        <option value="Helix"selected>Helix</option>
        <option value="Beta">Beta</option>
      </select>
    </td>
    <td align=center><input type="text" name="nbseq" id="nbseq1" size="4" maxlength="4" autocomplete="off" /></td>
    <td align=center><input type='submit' name="Delete" value='Delete' id="delseq1" onclick="delseq('SeqRow1');"></td>   	
  </tr>
</table>
<table class=center border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td align=center><input type='button' name="Add" value='Add' id="AddSeq" onclick="addseq();"></td>   	
  </tr>
</table>
<table class=center border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td><input type="hidden" name="key" value="<?php echo $_SESSION['key'] ?>" /></td>
    <td><input type='submit' name="submit" value='Submit'  ></td>   	
  </tr>
</table>
</form>

<?php
$t=date("h:i:s a", time());
$tbl_name = "jobs";
$submit = $_POST['submit'];
#$connect = mysql_connect("localhost","root","comanche");
$username = $_SESSION['username'];
$submit = $_POST['submit'];
$delete = $_POST['delete'];
$sequence = strtoupper(strip_tags($_POST['sequence']));
$seqname = strip_tags($_POST['seqname']);
$date = date("Y-m-d");
$email = $_SESSION['email'];
$dir = '/srv/samba/share/';

if (ereg('[^A-Za-z0-9,.]', $seqname)) {
  echo "<h5>'Job Name' contains characters other than: <br> A-Z , a-z , 0-9 , . </h5>";
  }
else {
  if (ereg('[^A,R,N,D,C,E,Q,G,H,I,L,K,M,F,P,S,T,W,Y,V]', $sequence)) {
    echo "<h5>Sequence contains characters other than <br>(A,R,N,D,C,E,Q,G,H,I,L,K,M,F,P,S,T,W,Y,V)</h5>";
    }
  else {
    echo "";    
    if ($seqname&&$sequence) {
      echo "";
      if (strlen($sequence)>30||strlen($sequence)<2) {
        echo "<h5>Sequence must contain minimum two amino acids! </h5>";    
        }
      else {
        if(isset($_POST['submit']) && ($_POST['key']) == $_SESSION['key']) {
          //open database
          #mysql_select_db("comanche_db");
          //select database
          $queryreg = mysql_query("INSERT INTO jobs VALUES ('', '$seqname', '$username', '$Ncap', '$sequence', '$Ccap', '$date', '$t', 'pending')");
          $lastid = mysql_insert_id();
          // create files section
          $text_id_path = "$dir$lastid$ext";
          $peplook_web = "$dir$lastid$ext1";
          $mail ="$dir$lastid$ext2";
          //echo $text_id_path; create peplook files; file <jobnumber>.plk
          if ($submit) {
            $h = fopen($text_id_path, 'a');
            echo "<h5> $text_id_path </h5>";
            fwrite($h, "$sequence Sequence\r\n");
            fwrite($h, "$Ccap C-Cap\r\n");
            fwrite($h, "$Ncap N-Cap\r\n");
            fwrite($h, "$env_bolean $environment\r\n");
            fwrite($h, "$ph pH\r\n");
            fwrite($h, "PDB+TXT Output\r\n");
            fwrite($h, "$lastid Number\r\n");
            fwrite($h, "$seqname Job_Name\r\n");
            fclose($h);
            // file <jobnumber>.pelook_WEB
            $h1 = fopen($peplook_web, 'a');
            fwrite($h1, "G:\\$lastid\\$lastid$ext\r\n");
            fwrite($h1, "G:\\$lastid\r\n");
            fclose($h1);
            // file <jobnumber>.mail
            $h2 = fopen($mail, 'a');
            fwrite($h2, "$email");
            fclose($h2);
            }
          // delete if void <Job numner> So avoid '.plk' and '.mail' and '.peplook.web' hiden file 
          unlink("$dir$ext");  
          unlink("$dir$ext1");
          unlink("$dir$ext2");
          $test=`/bin/bash /home/comanche/Job_run.sh`;
          echo system(`$test`);
          }
        } 
      }
    else {
      echo "<h5>Amino acids must be entered as one letter codes in Sequence field.</h5>";
      }	
    }
    $key=($_SESSION['key'] = mt_rand(1, 10000));
  }

$connect = mysql_connect("localhost","root","comanche");
if (!$connect)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("comanche_db", $connect);
$sql = ("SELECT * FROM `jobs` WHERE username='$username' and status='pending' ORDER BY `jobs`.`id` DESC LIMIT 0, 100");
$result = mysql_query($sql);
// Check if delete button active, start this
if ( ! empty($_POST['delete'])) {
  foreach ($_POST['need_delete'] as $id => $value) {
    system("rm -rf $dir/$id");
    // delete Job if not run yet			
    unlink("$dir$id$ext");
    unlink("$dir$id$ext1");
    unlink("$dir$id$ext2");
    //delete dir if not run yet ( not functional )
    unlink("$dir$id/$id$ext");
    unlink("$dir$id/$id$ext1");
    $sql = 'DELETE FROM `'.$tbl_name.'` WHERE `id`='.(int)$id;
    mysql_query($sql);
    }
  header('location: index.php?page=insertion');
  }
$sql_nodelete = ("SELECT * FROM `jobs` WHERE username='$username' and not status='pending' ORDER BY `jobs`.`id` DESC LIMIT 0, 100");
$nodelete = mysql_query($sql_nodelete);
mysql_close();
?>

<br /><br />
<table class=center border="1" cellspacing="2" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#FFFFFF">#</td>
    <td align="center" bgcolor="#FFFFFF"><strong>Id</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Username</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Job Name</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>N-cap</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Sequence</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>C-cap</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Date</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Time</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Status</strong></td>
  </tr>
  <?php while ($rows = mysql_fetch_array($result)): ?>
    <tr>
      <td align="center" ><input name="need_delete[<? echo $rows['id']; ?>]" type="checkbox" id="checkbox[<? echo $rows['id']; ?>]" value="<? echo $rows['id']; ?>"></td>
      <td ><?php echo $rows['id']; ?></td>
      <td ><?php echo htmlspecialchars($rows['username']); ?></td>
      <td ><?php echo htmlspecialchars($rows['seqname']); ?></td>
      <td ><?php echo htmlspecialchars($rows['Ncap']); ?></td>
      <td ><?php echo htmlspecialchars($rows['sequence']); ?></td>		
      <td ><?php echo htmlspecialchars($rows['Ccap']); ?></td>
      <td ><?php echo htmlspecialchars($rows['date']); ?></td>
      <td ><?php echo htmlspecialchars($rows['time']); ?></td>
      <td ><?php echo htmlspecialchars($rows['status']); ?></td>
    </tr>
  <?php endwhile; ?>
  <tr>
    <td colspan="13" align="center" ><input name="delete" type="submit" id="delete" value="Delete" submit=true></td>
  </tr>
</table>

<!-- 
<table  border="1" cellspacing="2" cellpadding="0">
  <tr>
    <td align="center" bgcolor="#FFFFFF"><strong>Id</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Username</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Job Name</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>N-cap</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Sequence</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>C-cap</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Environment</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>pH</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Date</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Time</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Status</strong></td>
    <td align="center" bgcolor="#FFFFFF"><strong>Node</strong></td>
  </tr>
  <?php while ($rows = mysql_fetch_array($nodelete)): ?>
    <tr>
      <td ><? echo $rows['id']; ?></td>
      <td ><? echo htmlspecialchars($rows['username']); ?></td>
      <td ><? echo htmlspecialchars($rows['seqname']); ?></td>
      <td ><? echo htmlspecialchars($rows['Ncap']); ?></td>
      <td ><? echo htmlspecialchars($rows['sequence']); ?></td>		
      <td ><? echo htmlspecialchars($rows['Ccap']); ?></td>
      <td ><? echo htmlspecialchars($rows['environment']); ?></td>
      <td ><? echo htmlspecialchars($rows['ph']); ?></td>
      <td ><? echo htmlspecialchars($rows['date']); ?></td>
      <td ><? echo htmlspecialchars($rows['time']); ?></td>
      <td ><? echo htmlspecialchars($rows['status']); ?></td>
      <td ><? echo htmlspecialchars($rows['node']); ?></td>
    </tr>
  <?php endwhile; ?>
</table>
-->

