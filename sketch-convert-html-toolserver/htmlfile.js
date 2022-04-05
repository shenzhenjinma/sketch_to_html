module.exports ={
  getfile:()=>{
    return `
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title>快ui kuaiui.cn</title>
</head>
<style>
.defwidth100 {
    width: 100%;
}
.defflexrow {
    display: flex;
    flex-direction: row;
}
.defflexcolumn {
    display: flex;
    flex-direction: column;
}
.defabsolute {
    width: 100%;
    height: 100%;
    position: absolute;
}
__style__
</style>
<body style="margin: 0">
__template__
</body>
</html>
    `
  }
}