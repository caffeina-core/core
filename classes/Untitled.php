<?php
/**
 * Extract JSON schema from long records array
 */

class JSONC {

  public static function compress($collection){
    $collection = (array)$collection;
    $schema = array_keys((array)$collection[0]);
    foreach($collection as $e) $values[] = array_values((array)$e);

    return json_encode([
      'schema'   => $schema,
      'values'   => $values,
    ],JSON_NUMERIC_CHECK);

  }

}


$collection = json_decode(file_get_contents('http://sapi.alidays.it/service/1.1.1/rs/catalog/experiences'));


$data = []; for($i=1000;$i--;) $data[] = [
  'title'      => md5(time()+$i),
  'time'       => time()+$i,
  'other_data' => md5(time()*2+$i),
];

$data = $collection->response->data;

$jc = JSONC::compress($data);
$js = json_encode($data,JSON_NUMERIC_CHECK);

$jc_l = strlen($jc);
$js_l = strlen($js);


echo "Normal: ",            $js_l," bytes.",PHP_EOL;
echo "Compressed: ",        $jc_l," bytes.",PHP_EOL;
echo "Compession Ratio: ", number_format(($js_l-$jc_l)*100/$js_l,3),"%.",PHP_EOL;

echo PHP_EOL;

$jcz = gzcompress($jc);
$jsz = gzcompress($js);

$jcz_l = strlen($jcz);
$jsz_l = strlen($jsz);


echo "Normal (GZ): ",                 $jsz_l," bytes.",PHP_EOL;
echo "Compressed (GZ): ",             $jcz_l," bytes.",PHP_EOL;
echo "Compession Ratio (over GZ): ",  number_format(($jsz_l-$jcz_l)*100/$jsz_l,3),"%.",PHP_EOL;


/*

Normal: 63001 bytes.
Compressed: 48036 bytes.
Compession Ratio: 23.753591212838%.

Normal (GZ): 23739 bytes.
Compressed (GZ): 23186 bytes.
Compession Ratio (over GZ): 2.3294999789376%.


[Finished in 2.6s]

 */
