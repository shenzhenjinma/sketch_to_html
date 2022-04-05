 var process = require('child_process');
 
  var cmd = '/usr/bin/zip -j /home/22.zip /home/install.sh /home/22.sh';
  process.exec(cmd, function(error, stdout, stderr) {
      console.log("error:"+error);
      console.log("stdout:"+stdout);
      console.log("stderr:"+stderr);
  });