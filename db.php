<?php
// Create connection
$con=mysqli_connect("mysql","tones","GmQS8JeKfY2jBCCa","tones");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$result = mysqli_query($con,"SELECT * FROM batphone_contacts");

echo "<table border='1'>
<tr>
<th>ID</th>
<th>Name</th>
<th>Phone</th>
<th>Domain</th>
<th>Enabled</th>
</tr>";

while($row = mysqli_fetch_array($result))
  {
  echo "<tr>";
  echo "<td>" . $row['id'] . "</td>";
  echo "<td>" . $row['name'] . "</td>";
  echo "<td>" . $row['phone'] . "</td>";
  echo "<td>" . $row['domain'] . "</td>";
  echo "<td>" . $row['enabled'] . "</td>";
  echo "</tr>";
  }
echo "</table>";

mysqli_close($con);

?>
