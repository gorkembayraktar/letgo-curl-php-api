<?php 

$requireList  = ["helper.emailReader","helper.common","helper.file","helper.http","helper.httpResponse" ,
"app.letgoState","app.letgoAPI","app.letgoMessages","app.letgo"
];
foreach($requireList as $require) require str_replace('.',"/",$require).".php";
FileHelper::folder(dirname(__DIR__)."\cookies\\");