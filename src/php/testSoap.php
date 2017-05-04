<?
$client = new SoapClient("http://opitestws.serv.dteco.com:80/opijaxws/trouble?wsdl");
echo "<pre>";
print_r($client);
echo "</pre>";
$parameters = array (
                     'Header' => array(
                                       'foo'=>'bar'
                                       ),
                     'Verb' => array (
                                      'fizz'=>'bang'
                                      )
                     );
echo $client->UpdateRemarks($parameters);
?>