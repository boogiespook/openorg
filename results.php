<!DOCTYPE html>
<html>
<?php
date_default_timezone_set("Europe/London");
## Connect to the Database 
include 'dbconnect.php';
connectDB();

# Retrieve the data
$hash = $_REQUEST['hash'];
$qq = "SELECT * FROM open_data WHERE hash='".mysqli_real_escape_string($db, $hash)."'";
#print $qq;
$res = mysqli_query($db, $qq);
$data_array = mysqli_fetch_assoc($res);

#global $data_array;
#var_dump($data_array);
if (empty($data_array)) {
print "<h2> Results not found </h2>";
exit;
}
$ops_arr = $dev_arr = $opsRound_arr = $devRound_arr = array();
$share = $data_array['share'];
$lob = $data_array['lob'];

function getRating($num) {
$roundedNum = round($num,0);
#print "Rounded: $roundedNum <br>";
switch ($roundedNum) {
	case "1":
		$rating = "Conventional";
		$ratingRank = "<b>Conventional</b>: ";
#		$ratingDescription = $ratingRank . "Governance practices are either non-existent or in the very early stages of development";
		break;
	case "2":
		$rating = "Modern";
		$ratingRank = "<b>Modern</b>: ";
#		$ratingDescription = $ratingRank . "Potential shortfalls in governance practices have been identified and initial steps have been taken to rectify them. There is significant room for improvement.";
		break;
	case "3":
		$rating = "Leading";
		$ratingRank = "<b>Leading</b>: ";
#		$ratingDescription = $ratingRank . "The minimum governance practices are in place. There is still room for improvement.";
		break;
}
return $rating;
}

$string = file_get_contents("questionsV2.json");
$json = json_decode($string, true);

$string2 = file_get_contents("comments.json");
$comments = json_decode($string2, true);
?>
<head>
    <script src="js/Chart.bundle.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/raphael-2.1.4.min.js"></script>
    <script src="js/justgage.js"></script>
    <title>Open Organization Maturity Assessment</title>
<link rel="stylesheet" type="text/css" href="https://overpass-30e2.kxcdn.com/overpass.css"/>
    <link href="http://static.jboss.org/css/rhbar.css" media="screen" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>    
	 <link rel="stylesheet" href="css/style.css">

  <script>
  $( function() {
    $( "#analysis-dialog" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "drop",
        duration: 1000
      },
      minWidth: 1000
    });
 
    $( "#analysis-opener" ).on( "click", function() {
      $( "#analysis-dialog" ).dialog( "open" );
    });
  } );
  </script>

  <script>
  $( function() {
    $( "#workshop-dialog" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "drop",
        duration: 1000
      },
      minWidth: 400
    });
 
    $("#workshop-opener" ).on( "click", function() {
      $("#workshop-dialog" ).dialog( "open" );
    });

  } );
  </script>

  <script>
  $( function() {
    $( "#priorities-dialog" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "drop",
        duration: 1000
      },
      minWidth: 800
    });
 
    $("#priorities-opener" ).on( "click", function() {
      $("#priorities-dialog" ).dialog( "open" );
    });

  } );
  </script>

  <script>
  $( function() {
    $( "#average-dialog-dev" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "drop",
        duration: 1000
      },
      minWidth: 1000
    });
 
    $( "#average-opener-dev" ).on( "click", function() {
      $( "#average-dialog-dev" ).dialog( "open" );
    });
  } );
  </script>

  <script>
  $( function() {
    $( "#average-dialog-dev-lob" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "drop",
        duration: 1000
      },
      minWidth: 1000
    });
 
    $( "#average-opener-dev-lob" ).on( "click", function() {
      $( "#average-dialog-dev-lob" ).dialog( "open" );
    });
  } );
  </script>

  <script>
  $( function() {
    $( "#average-dialog-ops-lob" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "drop",
        duration: 1000
      },
      minWidth: 1000
    });
 
    $( "#average-opener-ops-lob" ).on( "click", function() {
      $( "#average-dialog-ops-lob" ).dialog( "open" );
    });
  } );
  </script>

  <script>
  $( function() {
    $( "#average-dialog-ops" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "drop",
        duration: 1000
      },
      minWidth: 1000
    });
 
    $( "#average-opener-ops" ).on( "click", function() {
      $( "#average-dialog-ops" ).dialog( "open" );
    });
  } );
  </script>

 

<script>
$(document).ready(function() {
  $(function() {
    console.log('false');
    $( "#dialog" ).dialog({
        autoOpen: false,
        title: 'Email PDF'
    });
  });

  $("button").click(function(){
    console.log("click");
//        $(this).hide();
        $( "#dialog" ).dialog('open');
    });
}); 
</script>

</head>

<body>
  <script>
  $( function() {
    $( "#tabs" ).tabs();
  } );
  </script>
<?php  

#var_dump($data_array);

foreach( $data_array as $var => $value )
    {
    	if($var=='date') continue;
      if(substr($var[0],0,1) == "o") { $ops_arr[]=$value; $opsRound_arr[] = round($value);  };
      if(substr($var[0],0,1) == "d") { $dev_arr[]=$value; $devRound_arr[] = round($value);  };
    } 
     
 ?>
      <div id="wrapper">
      <header>

      <center>
      <h2>Open Organization Maturity Assessment for <?php echo $data_array['client']; 
		if ($data_array['project'] != "") {
			print " (" . $data_array['project'] . ")";		
		}      
      ?></h2>
      </center>
      </header>
      
<div id="content">       
    <div style="width:90%">
        <canvas id="canvas"></canvas>
    </div>
        <script>

    var customerName = '<?php echo $data_array['client'] ?>'
    var customerNameNoSpaces = customerName.replace(/\s+/, "");


function checkVal(inNo) {
	if (inNo == "0") {
		var outNo = "0.01";
	} else {
	   var outNo = inNo;	
	}
	return outNo
}

    var d1 = checkVal(<?php echo $data_array['d1'] ?>)
    var d2 = checkVal(<?php echo $data_array['d2'] ?>)
    var d3 = checkVal(<?php echo $data_array['d3'] ?>)
    var d4 = checkVal(<?php echo $data_array['d4'] ?>)
    var d5 = checkVal(<?php echo $data_array['d5'] ?>)
    
    var totalDev = d1 + d2 + d3 + d4 + d5

    var o1 = checkVal(<?php echo $data_array['o1'] ?>)
    var o2 = checkVal(<?php echo $data_array['o2'] ?>)
    var o3 = checkVal(<?php echo $data_array['o3'] ?>)
    var o4 = checkVal(<?php echo $data_array['o4'] ?>)
    var o5 = checkVal(<?php echo $data_array['o5'] ?>)

    var totalOps = o1 + o2 + o3 + o4 + o5

    var chartTitle = "Culture Assessment Chart - " + customerName
    var overall1 = (d1+o1)/2;
    var overall2 = (d2+o2)/2;
    var overall3 = (d3+o3)/2;
    var overall4 = (d4+o4)/2;
    var overall5 = (d5+o5)/2;
    
    var randomScalingFactor = function() {
        return Math.round(Math.random() * 4);
    };

    var color = Chart.helpers.color;
    var config = {
        type: 'radar',
        data: {
            labels: ["Transparency", "Inclusivity", "Adaptability", "Collaboration", "Community"],
            datasets: [{
                label: "Now",
                backgroundColor: color(window.chartColors.red).alpha(0.2).rgbString(),
                borderColor: window.chartColors.red,
                pointBackgroundColor: window.chartColors.red,
                data: [d1,d2,d3,d4,d5]
            }, {
                label: "Vision",
                backgroundColor: color(window.chartColors.blue).alpha(0.2).rgbString(),
                borderColor: window.chartColors.blue,
                pointBackgroundColor: window.chartColors.blue,
                data: [o1,o2,o3,o4,o5]

            }]
        },
        options: {
            legend: {
                position: 'bottom',
            },
            title: {
                display: true,
                text: chartTitle
            },
            scale: {
            
              ticks: {
                beginAtZero: true,
                max: 3,
                min: 0
              }
            },

    }
 }
    window.onload = function() {
        window.myRadar = new Chart(document.getElementById("canvas"), config);

var ctx = document.getElementById("myChartDev").getContext("2d");
var data = {
  labels: ["Transparency", "Inclusivity", "Adaptability", "Collaboration", "Community"],
  datasets: [{
    label: customerName,
    backgroundColor: 'green',
    data: [d1, d2, d3, d4, d5]
  }, {
    label: "Average",
    backgroundColor: "orange",
    data: <?php 
 #   $qq = "select avg(d1) as d1,avg(d2) as d2, avg(d3) as d3, avg(d4) as d4, avg(d5) as d5 from data;";    
    $qq = "select ROUND(avg(d1),2) as d1, ROUND(avg(d2),2) as d2, ROUND(avg(d3),2) as d3, ROUND(avg(d4),2) as d4, ROUND(avg(d5),2) as d5 from open_data where share ='on';";    
    $res = mysqli_query($GLOBALS["___mysqli_ston"], $qq);
    $row = mysqli_fetch_array($res);
     echo "[" . $row[0] . "," . $row[1] . "," . $row[2] . "," . $row[3] . "," . $row[4] . "]"; 
     ?>
  },
  ]
};

var myBarChart = new Chart(ctx, {
  type: 'bar',
  data: data,
  options: {
    barValueSpacing: 20,
    scales: {
      yAxes: [{
        ticks: {
          min: 0,
          max: 5
        }
      }]
    },

  }
});  		

var ctxLobDev = document.getElementById("myChartDevLob").getContext("2d");
var data = {
  labels: ["Transparency", "Inclusivity", "Adaptability", "Collaboration", "Community"],
  datasets: [{
    label: customerName,
    backgroundColor: 'green',
    data: [d1, d2, d3, d4, d5]
  }, {
    label: "Average",
    backgroundColor: "orange",
    data: <?php 
    $qq = "select ROUND(avg(d1),2) as d1, ROUND(avg(d2),2) as d2, ROUND(avg(d3),2) as d3, ROUND(avg(d4),2) as d4, ROUND(avg(d5),2) as d5 from open_data where share ='on' and lob = '" . $lob . "';";    
    $res = mysqli_query($GLOBALS["___mysqli_ston"], $qq);
    $row = mysqli_fetch_array($res);
     echo "[" . $row[0] . "," . $row[1] . "," . $row[2] . "," . $row[3] . "," . $row[4] . "]"; 
     ?>
  },
  ]
};

var myBarChart = new Chart(ctxLobDev, {
  type: 'bar',
  data: data,
  options: {
    barValueSpacing: 20,
    scales: {
      yAxes: [{
        ticks: {
          min: 0,
          max: 5
        }
      }]
    },

  }
});  	

var ctxLobOps = document.getElementById("myChartDevOps").getContext("2d");
var data = {
  labels: ["Transparency", "Inclusivity", "Adaptability", "Collaboration", "Community"],
  datasets: [{
    label: customerName,
    backgroundColor: 'green',
    data: [d1, d2, d3, d4, d5]
  }, {
    label: "Average",
    backgroundColor: "orange",
    data: <?php 
    $qq = "select ROUND(avg(o1),2) as o1, ROUND(avg(o2),2) as o2, ROUND(avg(o3),2) as o3, ROUND(avg(o4),2) as o4, ROUND(avg(o5),2) as o5 from open_data where share ='on' and lob = '" . $lob . "';";    
    $res = mysqli_query($GLOBALS["___mysqli_ston"], $qq);
    $row = mysqli_fetch_array($res);
     echo "[" . $row[0] . "," . $row[1] . "," . $row[2] . "," . $row[3] . "," . $row[4] . "]"; 
     ?>
  },
  ]
};

var myBarChart = new Chart(ctxLobOps, {
  type: 'bar',
  data: data,
  options: {
    barValueSpacing: 20,
    scales: {
      yAxes: [{
        ticks: {
          min: 0,
          max: 5
        }
      }]
    },

  }
});  	

var ctx2 = document.getElementById("myChartOps").getContext("2d");
var dataOps = {
            labels: ["Transparency", "Inclusivity", "Adaptability", "Collaboration", "Community"], 
  datasets: [{
    label: customerName,
    backgroundColor: 'green',
    data: [o1, o2, o3, o4, o5]
  }, {
    label: "Average",
    backgroundColor: "orange",
    data: <?php 
#    $qq = "select avg(o1) as d1,avg(o2) as d2, avg(o3) as d3, avg(o4) as d4, avg(o5) as d5 from data;";    
    $qq = "select ROUND(avg(o1),2) as o1, ROUND(avg(o2),2) as o2, ROUND(avg(o3),2) as o3, ROUND(avg(o4),2) as o4, ROUND(avg(o5),2) as o5 from open_data where share ='on';";    
    $res = mysqli_query($GLOBALS["___mysqli_ston"], $qq);
    $row = mysqli_fetch_array($res);    
     echo "[" . $row[0] . "," . $row[1] . "," . $row[2] . "," . $row[3] . "," . $row[4] . "]"; 
     ?>
  },
  ]
};

var myBarChart2 = new Chart(ctx2, {
  type: 'bar',
  data: dataOps,
  options: {
    barValueSpacing: 20,
    scales: {
      yAxes: [{
        ticks: {
          min: 0,
          max: 5
        }
      }]
    },

  }
});  				
  		
};
             


    var colorNames = Object.keys(window.chartColors);
    </script>
<?php print '<br>
<a target=_blank href=resultsOpen.php?hash=' . $hash . '><p class=centeredDiv>Detailed Version</p></a>'; 
?>    
</div>

<div id="rightcol">


<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Overview</a></li>
    <li><a href="#tabs-4">Comments</a></li>  
    <li><a href="#tabs-5">Comparisons</a></li>  </ul>
      <div id="tabs-1">
  <table class="bordered">
    <thead>
    <tr>
        <th>Area</th>        
        <th>Now</th>
        <th>Vision</th>
    </tr>
    </thead>
	<tbody> 

<?php
$areas = array(
	1 => "Transparency",
	2 => "Inclusivity",
	3 => "Adaptability",
	4 => "Collaboration",
	5 => "Community"
);

function printGauge($areaName,$num,$chartName,$arr) {
print '<tr><td><b>' . $areaName . '</b></td><td><div id="' . $chartName . 'Dev" style="width:100px; height:80px"></div><p class="' . strtolower(getRating($arr["d$num"])) . '">' . getRating($arr["d$num"]) . '</p></td>
<td><div id="' . $chartName . 'Ops" style="width:100px; height:80px"></div><p class=" ' . strtolower(getRating($arr["o$num"])) . '">' . getRating($arr["o$num"]) . '</p></td></tr>';
}

printGauge("Transparency","1","automation",$data_array);
printGauge("Inclusivity","2","wow",$data_array);
printGauge("Adaptability","3","arch",$data_array);
printGauge("Collaboration","4","vision",$data_array);
printGauge("Community","5","env",$data_array);

function getActions($areaName,$type,$num,$comments){
## For example getActions("Automation","operations",1)
$actionField = round($num) . "-action";
print $comments[$areaName][$type][$actionField];

## TODO: Need to get ops and dev together with <td> etc
}

?>

</tbody>
</table>	

	<!-- End of Tab1 Div -->    
      </div>
      
<!--       <div id="tabs-2">
  <table class="bordered">
    <thead>
    <tr>
        <th>Area</th>        
        <th>Action</th>
    </tr>
    </thead>
	<tbody> 
	<tr>
		<td>Transparency</td>
		<td><?php #getActions("Transparency","development",$data_array['d1'],$comments); ?>
		</td>

	</tr>
	<tr>
		<td>Inclusivity</td>
		<td><?php #getActions("Inclusivity","development",$data_array['d2'],$comments); ?>
		</td>

	</tr>
	<tr>
		<td>Adaptability</td>
		<td><?php #getActions("Adaptability","development",$data_array['d3'],$comments); ?>
		</td>

	</tr>
	<tr>
		<td>Collaboration</td>
		<td><?php #getActions("Collaboration","development",$data_array['d4'],$comments); ?>
		</td>

	</tr>
	<tr>
		<td>Community</td>
		<td><?php #getActions("Community","development",$data_array['d5'],$comments); ?>
		</td>

	</tr>
	</tbody>
	</table>
      </div> -->


      <div id="tabs-4">
<h4>Notes and Comments</h4>
<?php
if ($data_array['comments'] != "") {
print "<p>" . $data_array['comments'] . "</p>";
}


## Fudge here as can't seem to get it in a loop ... dodgy code alert!
if ($data_array['comments_transparency'] != "") {
print "<h4>Transparency</h4>";
print "<p>" . $data_array['comments_transparency'] . "</p>";
}

if ($data_array['comments_inclusivity'] != "") {
print "<h4>Inclusivity</h4>";
print "<p>" . $data_array['comments_inclusivity'] . "</p>";
}

if ($data_array['comments_adaptability'] != "") {
print "<h4>Adaptability</h4>";
print "<p>" . $data_array['comments_adaptability'] . "</p>";
}

if ($data_array['comments_collaboration'] != "") {
print "<h4>Collaboration</h4>";
print "<p>" . $data_array['comments_collaboration'] . "</p>";
}

if ($data_array['comments_community'] != "") {
print "<h4>Community</h4>";
print "<p>" . $data_array['comments_community'] . "</p>";
}

?>      
	<!-- End of Tab4 Div -->    
      </div>

      <div id="tabs-5">

 <?php
if ($share == "on") {
print '<br>
<div id="average-dialog-dev" title="Average (Now)">
<canvas id="myChartDev" width="400" height="200"></canvas>
</div>
<button id="average-opener-dev" class="ui-button ui-widget ui-corner-all">Average (Now)</button>

<div id="average-dialog-ops" title="Average (Vision)">
<canvas id="myChartOps" width="400" height="200"></canvas>
</div>
<button id="average-opener-ops" class="ui-button ui-widget ui-corner-all">Average (Vision)</button>
<br><br>
<div id="average-dialog-dev-lob" title="Average for ' . $lob . ' (Now)">
<canvas id="myChartDevLob" width="400" height="200"></canvas>
</div>
<button id="average-opener-dev-lob" class="ui-button ui-widget ui-corner-all">Average (Now) for ' . $lob . '</button>

<div id="average-dialog-ops-lob" title="Average for ' . $lob . ' (Vision)">
<canvas id="myChartDevOps" width="400" height="200"></canvas>
</div>
<button id="average-opener-ops-lob" class="ui-button ui-widget ui-corner-all">Average (Vision) for ' . $lob . '</button>
<br><br>
';
} else {
print "<h4>Comparisons not available for this customer</h4>";
}
?>     
	<!-- End of Tab5 Div -->    
      </div>


</div>

                  
<?php



# Create data arrays
#$string = file_get_contents("questions.json");
#$string = file_get_contents("questions-new.json");


#$automation_dev_array = $json['development']['automation'];
#$automation_ops_array = $json['operations']['automation'];
#$methodology_dev_array = $json['development']['methodology'];
#$methodology_ops_array = $json['operations']['methodology'];
#$architecture_dev_array = $json['development']['automation'];
#$architecture_ops_array = $json['operations']['architecture'];
#$strategy_dev_array = $json['development']['strategy'];
#$strategy_ops_array = $json['operations']['strategy'];
#$environment_dev_array = $json['development']['environment'];
#$environment_ops_array = $json['operations']['environment'];
#
#
#$totalDev = $totalOps = 0;
#
#$workshops = array();
#$workshopLinks = array(
#	"AdaptiveSOE" => "<a target=_blank href='https://mojo.redhat.com/community/consulting-customer-training/consulting-services-solutions/projects/consulting-solution-adaptive-soe'>Adaptive SOE</a>",
#	"InnovationLabs" => "<a target=_blank href='https://mojo.redhat.com/groups/na-emerging-technology-practice/projects/red-hat-open-innovation-labs'>Open Innovation Lab</a>",
#	"ContainerPlatforms" => "<a target=_blank href='https://mojo.redhat.com/community/consulting-customer-training/consulting-services-solutions/projects/consulting-solution-modernize-app-delivery-with-container-platforms'>Container Platforms</a>",
#	"AgileDevelopment" => "<a target=_blank href='https://mojo.redhat.com/docs/DOC-965558'>Agile Development</a>",
#	"OpenSCAP" => "<a target=_blank href='https://access.redhat.com/documentation/en-US/Red_Hat_Enterprise_Linux/7/html/Security_Guide/chap-Compliance_and_Vulnerability_Scanning.html'>Compliance and Vulnerability Scanning Guide</a>",
#	"RHCE" => "<a target=_blank href='https://www.redhat.com/en/services/certification/rhce'>Red Hat Certification (RHCE)</a> for Operations team",
#	"OSEP" => "<a target=_blank href='https://mojo.redhat.com/groups/osep-community-of-practice'>Open Source Enablement</a>",
#	"BusinessInfluence" => "<a target=_blank href='#'>Strategy and Business Influence</a>",
#	"AnsibleAutomation" => "<a target=_blank href='https://mojo.redhat.com/community/consulting-customer-training/consulting-services-solutions/projects/consulting-solution-accelerate-it-automation-with-ansible'>Ansible Automation</a>",
#	"CloudInfrastructure" => "<a target=_blank href='https://mojo.redhat.com/docs/DOC-1097461'>Cloud Infrastructure</a>",
#	"CloudManagement" => "<a target=_blank href='https://mojo.redhat.com/docs/DOC-1097463'>Cloud Management</a>",
#	"BusinessAutomation" => "<a target=_blank href='https://mojo.redhat.com/docs/DOC-1041221'>Business Automation</a>",
#	"WalledGarden" => "<a target=_blank href='#'>Walled Garden Presentation</a>",
#	"DevOpsReview" => "<a target=_blank href='#'>Review of DevOps Skills</a>",
#	"Microservices" => "<a target=_blank href='#'>Microservices : Design and Architecture</a>",
#);


#$areas = array(
#	0 => "Automation",
#	1 => "Methodology",
#	2 => "Architecture",
#	3 => "Strategy",
#	4 => "Environment"
#);




#$areaWeighting = array(
#	0 => "1",
#	1 => "2",
#	2 => "4",
#	3 => "8",
#	4 => "16"
#);

#$analysis = $recommendations = $weighting = $oWeight = $dWeight = $tWeights = array();

#for ($ii = 0; $ii < 5; $ii++) {
#	$lcArea=strtolower($areas[$ii]);
#	$lcDev=$lcArea."_dev_array";
#	$lcOps=$lcArea."_ops_array";
#	$o = $ops_arr[$ii];
#	$o = round($ops_arr[$ii]);
#	$weighting['Operations_'. $areas[$ii]] = $ops_arr[$ii]+1 * $areaWeighting[$ii];
#	$weighting['Development_'. $areas[$ii]] = $dev_arr[$ii]+1 * $areaWeighting[$ii];
#	$oWeight[$areas[$ii]] = $ops_arr[$ii] * $areaWeighting[$ii];
#	$dWeight[$areas[$ii]] = $dev_arr[$ii] * $areaWeighting[$ii];
#	$d = $dev_arr[$ii];
#	$d = round($dev_arr[$ii]);
#	$totalDev += $d;
#	$totalOps += $o;
#
#echo "    <tr>
#        <td>$areas[$ii] </td>
#        <td><b>$dev_arr[$ii]</b> " . ${$lcDev}[$d]['question'] . " </td>
#        <td><b>$ops_arr[$ii]</b> " . ${$lcOps}[$o]['question'] . " </td>
#    </tr>";        
#}    
  

## Assess Dev vs Ops

#if ($totalDev > $totalOps) {
#	$moreMature = "Dev";
#	$lessMature = "Ops";
#	$top = $totalDev;
#	$bottom = $totalOps;
#	} else {
#	$moreMature = "Ops";
#	$lessMature = "Dev";
#	$top = $totalOps;
#	$bottom = $totalDev;
#}

#function assessVals($var) {
#switch (true) {
#case ($var <= 1):
#   $rating = "average";
#   break;
#case ($var > 1 && $var < 3):
#   $rating = "good";
#   break;
#case ($var > 3):
#   $rating = "very good";
#   break;
#}	
#return $rating;
#}

#function assessOverallVals($var) {
#switch (true) {
#case ($var < 3):
#   $rating = "low";
#   break;
#case ($var <= 4):
#   $rating = "average";
#   break;
#case ($var > 4 && $var < 7):
#   $rating = "good";
#   break;
#case ($var > 7):
#   $rating = "very good";
#   break;
#}	
#return $rating;
#}

#$ratio = ($top / $bottom);
#switch (true) {
#    case ($ratio < "1.3"):
#    	$word = "slightly more";
#    	break;
#    case ($ratio > "1.32" && $ratio < "2"):
#    	$word = "considerably more";
#    	break;
#    case ($ratio > "2"):
#    	$word = "extremely more";
#    	break;
#}

#array_push($analysis, "Overall, the $moreMature team are $word mature than the $lessMature team.");
#array_push($recommendations, "Re-balance the maturity levels between teams");

## Assess ops automation
#if ($ops_arr[0]  < 2) {
#array_push($analysis, "The Ops team would benefit from better use of automation techniques such as puppet/ansible.");
#array_push($recommendations,"SOE/CII Workshop</a>");
#array_push($workshops,$workshopLinks['AdaptiveSOE']);
#array_push($workshops,$workshopLinks['AnsibleAutomation']);
#}
#
#if ($ops_arr[0]  >= 2) {
#	$automationAnalysis = "The Ops team provide good use of automation";
#	$automationRecommendation = "None";
#	if ($dev_arr[0] < 2) {
#		$automationAnalysis .= " although less automation is used by the Dev team";
#		$automationRecommendation = "Increase automation in the Dev team";
#		array_push($workshops,$workshopLinks['BusinessAutomation']);		
#	}
#	if ($dev_arr[0] > 2) {
#		$automationAnalysis .= " which is similar to the Dev team";
#		$automationRecommendation = "None";
#	}
#array_push($analysis, $automationAnalysis);
#array_push($recommendations,$automationRecommendation);
#}

## Additional Automation stuff
#if ($ops_arr[0] < 1) {
#array_push($analysis,"No automated patch or release management.");
#array_push($recommendations,"Consider using automation tools such as puppet and/or ansible.");
#}

## Dev Automation
#$devAutomationAnalysis = $devAutomationRecommendations = "";
#switch($dev_arr[0]) {
#	case 0:
#		$devAutomationAnalysis .= "No control over which tools are used by developers";
#		$devAutomationRecommendations .= "Provide a list of support development tools (aka 'Walled Garden')";
#		array_push($workshops,$workshopLinks['WalledGarden']);
#		break;
#	case 1:
#		$devAutomationAnalysis .= "All deployments involve manual intervention";
#		$devAutomationRecommendations .= "Invest in CI/CD technologies";
#		break;
#	case 2:
#		$devAutomationAnalysis .= "Good use of automation in pre-production environments";
#		$devAutomationRecommendations .= "Enable CI/CD pipelines to production environments";
#		break;
#}

#if ($devAutomationAnalysis != "") {
#array_push($analysis,$devAutomationAnalysis);
#array_push($recommendations,$devAutomationRecommendations);
#}

#if ($dev_arr[0] < 1) {
#array_push($analysis,"No automation within the Dev team");
#array_push($recommendations,"Consider using CI/CD tooling.");
#array_push($workshops,$workshopLinks['BusinessAutomation']);
#}

## Assess strategy
#$opsStrategy = $ops_arr[3];
#$devStrategy = $dev_arr[3];
#$overallStrategy = $opsStrategy + $devStrategy;
#$strategyAnalysis = "The overall strategy awareness is " . assessOverallVals($overallStrategy);
#$strategyRecommendations = "";
#if($opsStrategy > $devStrategy) {
#	$strategyAnalysis .= " although the Operations team are more mature than the Development team.";
#	$strategyRecommendations .= "Strategy and Business Influence Workshop";
#	array_push($workshops,"Strategy and Business Influence Workshop");
#	array_push($workshops,"Business Influence Mapping");	
#} elseif ($opsStrategy < $devStrategy) {
#	$strategyAnalysis .= " although the Development team are more mature than the Operations team.";
#	$strategyRecommendations .= "Strategy and Business Influence Workshop";
#} else {
#	$strategyAnalysis .= " although both teams have the same level of maturity";
#	$strategyRecommendations .= "";
#}

#if ($overallStrategy <= 2) {
#	$strategyRecommendations .= "Open Innovation Lab & Strategy and Business Influence Workshop";
#	array_push($workshops,$workshopLinks['InnovationLabs']);	
#	array_push($workshops,$workshopLinks['BusinessInfluence']);	
#	array_push($workshops,"Business Influence Mapping");	
#}

#array_push($recommendations,$strategyRecommendations);
#array_push($analysis,$strategyAnalysis);


## Assess methodology
#$opsMethods = $ops_arr[1];
#$devMethods = $dev_arr[1];
#$methodRecommendations = "";
#$overallMethods = $opsMethods + $devMethods;
#$methodsAnalysis = "The overall methodology score is " . assessOverallVals($overallMethods);
#if($opsMethods > $devMethods) {
#	$methodsAnalysis .= " although the Operations team have more mature methodology than the Development team.";
#	$methodRecommendations .= "Container Platforms & Agile Development";
#   array_push($workshops,$workshopLinks['ContainerPlatforms']);	
#   array_push($workshops,$workshopLinks['AgileDevelopment']);	
#     
#} elseif ($opsMethods < $devMethods) {
#	$methodsAnalysis .= " although the Development team are more mature than the Operations team.";
#	$methodRecommendations .= "Standard Operating Environment Workshop";
#   array_push($workshops,$workshopLinks['AdaptiveSOE']);	
#} else {
#	$methodsAnalysis .= " and both teams have the same level of maturity.";
#	$methodRecommendations .= "";
#}

#if ($devMethods < 2) {
#   array_push($workshops,$workshopLinks['ContainerPlatforms']);	
#   array_push($workshops,$workshopLinks['AgileDevelopment']);	
#   $methodsAnalysis .= " The Dev team could be improved through the use of more agile methodologies";
#	$methodRecommendations .= "  Container Platforms and Agile Development coaching";
#}
#
#if ($overallMethods <= 2) {
#	$methodRecommendations .= "Open Innovation Lab";
#   array_push($workshops,$workshopLinks['InnovationLabs']);	
#
#}
#array_push($recommendations,$methodRecommendations);
#array_push($analysis,$methodsAnalysis);

## Additional Methodology stuff
#if ($opsMethods <2) {
#array_push($analysis,"No automated security compliance in use.");
#array_push($recommendations,"Consider using tools such as OpenSCAP");
#array_push($workshops,$workshopLinks['OpenSCAP']);
#}
#
# Assess Environment
#$opsEnvironment = $ops_arr[4];
#$devEnvironment = $dev_arr[4];
#$resourceRecommendations = "";
#$overallEnvironment = $opsEnvironment + $devEnvironment;
#$EnvironmentAnalysis = "The overall skills rating for Environment is " . assessOverallVals($overallEnvironment);
#if($opsEnvironment > $devEnvironment) {
#	$EnvironmentAnalysis .= " although the Operations team are more mature than the Development team.";
#	$resourceRecommendations .= "Agile Development Workshop";
#} elseif ($opsEnvironment < $devEnvironment) {
#	$EnvironmentAnalysis .= " although the Development team are more mature than the Operations team.";
#	$resourceRecommendations .= $workshopLinks['RHCE'];
#} else {
#	$EnvironmentAnalysis .= " and both teams have the same level of maturity.";
#	$resourceRecommendations .= "";
#	}
#
#if ($overallEnvironment <= 2) {
#	$resourceRecommendations .= "Increase overall skills through an Open Innovation Lab</a>";
#}
#array_push($analysis,$EnvironmentAnalysis);
#array_push($recommendations,$resourceRecommendations);
#
#if ($devEnvironment < 2) {
#array_push($analysis,"Lack of DevOps Skills");
#array_push($recommendations,"Review current DevOps Skills");
#array_push($workshops,$workshopLinks['DevOpsReview']);
#}

## Assess architecture
#$opsArchs = $ops_arr[2];
#$devArchs = $dev_arr[2];
#$ArchRecommendations = "";
#$overallArchs = $opsArchs + $devArchs;
#$ArchsAnalysis = "The overall rating for architecture is " . assessOverallVals($overallArchs);
#if($opsArchs > $devArchs) {
#	$ArchsAnalysis .= " although the Operations team have a higher architecture rating than the Development team.";
#	$ArchRecommendations .= "Container Platforms <br> Agile Development.";
#   array_push($workshops,$workshopLinks['ContainerPlatforms']);	
#   array_push($workshops,$workshopLinks['AgileDevelopment']);	
#     
#} elseif ($opsArchs < $devArchs) {
#	$ArchsAnalysis .= " although the Development team are more mature than the Operations team.";
#	$ArchRecommendations .= "Increase infrastructure management and cloud awareness.";
#   array_push($workshops,$workshopLinks['CloudInfrastructure']);	
#   array_push($workshops,$workshopLinks['CloudManagement']);	
#} else {
#	$ArchsAnalysis .= " and both teams have the same level of maturity.";
#	$ArchRecommendations .= "";
#}

#if ($devArchs < 2) {
#   array_push($workshops,$workshopLinks['ContainerPlatforms']);	
#   array_push($workshops,$workshopLinks['AgileDevelopment']);	
#   array_push($workshops,$workshopLinks['Microservices']);	
#   $ArchsAnalysis .= " The Dev team could be improved through the use of more agile based architectures and microservices";
#	$ArchRecommendations .= " Increase use of microservices";
#}

#array_push($recommendations,$ArchRecommendations);
#array_push($analysis,$ArchsAnalysis);


## Look for OSEP opportunities

#if ($devStrategy < 3 && $opsStrategy < 3) {
#array_push($analysis,"Increase methodology and strategy through increased use of Open Source software");
#array_push($recommendations,"OSEP Workshop");
#array_push($workshops,$workshopLinks['OSEP']);	
#}

?>
</tbody>
</table>
<div id="analysis-dialog" title="Analysis of Results">
                   <table class="bordered">
    <thead>
    <tr>
        <th>ID</th>
        <th>Analysis</th>
        <th>Recommendations</th>
    </tr>
    </thead>
    <tbody>
<?php
#$i=1;
#foreach ($analysis as $key => $answer) {
#echo "<tr><td>$i</td><td>$answer</td><td>$recommendations[$key]</td></tr>";
#$i++;
#}
?>
    </tbody>
    </table>

<!-- <br></div>
<br> 
<button id="analysis-opener" class="ui-button ui-widget ui-corner-all">Open Analysis Dialog</button>

<div id="priorities-dialog" title="Priority Areas">
    <table class="bordered">
    <thead>
    <tr>
    	  <th>Timescale</th>
        <th>Development Team</th>        
        <th>Operations Team</th>        
    </tr>
    </thead>
<tbody>
 --><?php
## Create an array with all workshops by Dev/Ops breakdown
#$allWorkshops = array(
#	"Development" => array (
#	  	"Automation" => "CI/CD To Production",
#	  	"Methodology" => "Container Platforms",
#	  	"Architecture" => "Microservices",
#	  	"Strategy" => "Business Influence Mapping",
#	  	"Environment" => "Agile Development Workshop",
#	),
#	"Operations" => array (
#	  	"Automation" => "Adaptive SOE and increased automation",
#	  	"Methodology" => "Innovation Labs (Ops Focus)",
#	  	"Architecture" => "Application Lifecycle Management",
#	  	"Strategy" => "Open Source Strategy",
#	  	"Environment" => "RH Training / GLS organization review of skills",
#	)
#);

## Sort the arrays to get them in ascending order of priority
#asort($oWeight);
#asort($dWeight);
#
#$top3Dev = $top3Ops = array();
#
#foreach ($oWeight as $key => $value) {
#	array_push($top3Ops,$key);
#}
#
#foreach ($dWeight as $key => $value) {
#	array_push($top3Dev,$key);
#}
#
#$timeScales = array("Short Term","Medium Term","Long Term");
#for ($i=0; $i < 3; $i++) {
#echo "<tr><td>$timeScales[$i]</td><td><b>$top3Dev[$i]</b><br>" . $allWorkshops['Development'][$top3Dev[$i]] . "</td><td><b>$top3Ops[$i]</b><br>" . $allWorkshops['Operations'][$top3Ops[$i]] . "</td></tr>";
#}
?>
<!-- </tbody>
</table>
</div>
<button id="priorities-opener" class="ui-button ui-widget ui-corner-all">Top 3 Action Areas</button>

<div id="workshop-dialog" title="Recommended Workshops">

    <table class="bordered">
    <thead>
    <tr>
    	  <th>ID</th>
        <th>Workshops</th>        
    </tr>
    </thead>
<tbody>
 -->
 <?php
#$i=1;
#foreach (array_unique($workshops) as $workshop) {
#echo "<tr><td>$i</td><td>$workshop</td></tr>";
#$i++;
#}
?>
<!-- </tbody>
</table>
</div>
<button id="workshop-opener" class="ui-button ui-widget ui-corner-all">Workshop Links</button>
<br>
 -->

<?php
// Temporary hack, pending refactoring of resultsOpen.php to fetch the results
// from the DB and not reproducing the entire code
$url_parts = array();
foreach ($data_array as $key => $val) {
	$url_parts[] = $key . '=' .urlencode($val);
}
$query_string = implode('&', $url_parts);
?>
<a href="resultsOpen.php?hash=<?php echo $hash ?>" target="_blank"><input type="button" value="Printable Version" class="ui-button ui-widget ui-corner-all"></a>


</div>
<!-- end of main content div -->
<!-- end of wrapper div -->


</div>

<script type="text/javascript" >
// Get the DIV responses
function saveHTMLDivs(divName,dataType,customer) {
 var htmlObj = document.getElementById(divName);
 var htmlRaw = htmlObj.innerHTML;
 						$.ajax({ 
				   	 	type: "POST", 
    						url: "htmlSave.php",
    						data: "data="+htmlRaw+"&customer="+customer+"&dataType="+dataType,
						});
}

//saveHTMLDivs("analysis-dialog","analysis",customerNameNoSpaces);
//saveHTMLDivs("priorities-dialog","priorities",customerNameNoSpaces);

</script>
<?php
## Put all the relevant parts together in one doc ready for PDF
#$name = preg_replace('/\s+/', '', $data_array['client']);

?>
<script id="jsbin-javascript">
$(document).ready(function(){
  
  var mc = {
    '0-25'     : 'red',
    '26-50'    : 'orange',
    '51-100'   : 'green'
  };
  
function between(x, min, max) {
  return x >= min && x <= max;
}
  

  
  var dc;
  var first; 
  var second;
  var th;
  
  $('p').each(function(index){
    
    th = $(this);
    
    dc = parseInt($(this).attr('data-color'),10);
    
    
      $.each(mc, function(name, value){
        
        
        first = parseInt(name.split('-')[0],10);
        second = parseInt(name.split('-')[1],10);

        
        if( between(dc, first, second) ){
          th.addClass(value);
        }
      });
    
  });
});
</script>
<script>
  var g = new JustGage({
    id: "automationDev",
    value: <?php print $data_array['d1'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
<script>
  var g = new JustGage({
    id: "automationOps",
    value: <?php print $data_array['o1'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
<script>
  var g = new JustGage({
    id: "wowOps",
    value: <?php print $data_array['o2'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
<script>
  var g = new JustGage({
    id: "wowDev",
    value: <?php print $data_array['d2'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>


<script>
  var g = new JustGage({
    id: "archOps",
    value: <?php print $data_array['o3'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
<script>
  var g = new JustGage({
    id: "archDev",
    value: <?php print $data_array['d3'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
         customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>

<script>
  var g = new JustGage({
    id: "visionDev",
    value: <?php print $data_array['d4'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
<script>
  var g = new JustGage({
    id: "visionOps",
    value: <?php print $data_array['o4'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
         customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
<script>
  var g = new JustGage({
    id: "envOps",
    value: <?php print $data_array['o5'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
<script>
  var g = new JustGage({
    id: "envDev",
    value: <?php print $data_array['d5'] . "\n"; ?>,
    min: 0,
    max: 3,
    decimals: 2,
        humanFriendly : true,
        gaugeWidthScale: 1.3,
        customSectors: [{
          color: "#ff0000",
          lo: 0,
          hi: 1
        },
        {
          color: "#ffbf00",
          lo: 1.1,
          hi: 2
        }, {
          color: "#00ff00",
          lo: 2.1,
          hi: 3
        }],
        counter: true    
  });
</script>
</body>
</html>
