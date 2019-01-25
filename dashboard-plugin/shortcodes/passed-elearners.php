<?php

echo do_shortcode("[wp-datatable id='testTable' fat='1']
dom: 'Bfrtip',
paging: false,
responsive: true,
search: true,
buttons: [ 'copy', 'excel', 'pdf', 'csv' ]
[/wp-datatable]");
?>

<form method="post" action="">
    <select name='elearning' id='elearning'>
        <option value=''>Select a Course</option>
    <?php

    $courses_query = $wpdb->get_results("SELECT *  FROM `wp_posts` WHERE `post_type` = 'course' ORDER BY `post_title` ASC");

    foreach ($courses_query as $course_data){
        $course_id = $course_data->ID;
        $course_title = $course_data->post_title;
        echo "<option value='$course_id'>$course_title</option>";
    }
    ?>
    </select>
    <input type="submit" value="Submit">
</form>
<br>
<br>
<?php

//GET E-LEARNING THAT HAS BEEN COMPLETED 

$elearning_id = $_POST['elearning']; // INDUCTION COURSE ID
$elearning_title = get_the_title($elearning_id);

echo "<h2>";
echo $elearning_title;
echo "</h2>";

$all_learners = $wpdb->get_results("SELECT ID FROM `wp_users` ORDER BY user_registered DESC;");

$completecounter = 0; //initialise counters
$progresscounter = 0;

echo"<div id='dvData'>";
echo "<table id='testTable'>";
echo "<thead>";
echo "<tr>";
echo "<td>First Name</td>";
echo "<td>Last Name</td>";
echo "<td>Email Address</td>";
echo "<td>E-Learning Module</td>";
echo "<td>Date Registered</td>";
echo "<td>Profile</td>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

foreach($all_learners as $a_learner){

$user_id = $a_learner->ID;
$user_info = get_userdata($user_id);
$user_registered = $user_info->user_registered;
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);

$user_course_status = Sensei_Utils::user_course_status( $elearning_id, $user_id );
$completed_course = Sensei_Utils::user_completed_course( $user_course_status );

if($user_course_status) {
    echo "<tr>";
    echo "<td>".ucfirst($first_name)."</td>";
    echo "<td>".ucfirst($last_name)."</td>";
    echo "<td>".$user_info->user_email."</td>";
    echo "<td>".$elearning_title."</td>";
    echo "<td>".$user_registered."</td>";
    echo "<td><a href='../profile/?uid=$user_id'>View Profile</a></td>";
    echo "</tr>";
    $completecounter++;
} else {
  $progresscounter++;
}
}
echo "</tbody>";
echo "</table>";

echo "<br>";
echo "<h3>".$completecounter." Users have completed this module</h3>";

echo"</div>";
?>
<script>
    var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
    this._filename = (typeof filename == 'undefined') ? tableId : filename;
    
    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls'); 
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it
      
      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("rn");
  }
};
</script>

<?php
