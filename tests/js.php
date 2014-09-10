<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Fobia\Helper\JavaScript;

$options = array('key1'=>true,'key2'=>123,'key3'=>'value');
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <script type="text/javascript">
    var options = <?php echo JavaScript::encode($options) ?>;
    console.log(options);
    </script>
</head>
<body>
<?php echo JavaScript::encode($options); ?>
</body>
</html>