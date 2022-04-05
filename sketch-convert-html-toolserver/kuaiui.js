
let datchange = require("./datchange.js");
let viewmake = require("./viewmake.js");
let htmlformat = require("./formathtml/htmlformat.js");

let windowwidth = process.argv[2];//宽度大小
let artboardpath = process.argv[3];//json路径
let outputfilepath = process.argv[4];//输出目录位置

let artboard = require(artboardpath);
 
for (let nowindex = 0; nowindex < artboard.artboards.length; nowindex++) {
    if(artboard.artboards[nowindex].layers.length<1){
        break;
    }
    let widthx = windowwidth / artboard.artboards[nowindex].width; //宽度比例
    let mainbgcolor="#ffffff";
    try{
        mainbgcolor=artboard.artboards[0].backgroundColor['argb-hex'];
    }catch(e){}
    let mainlayers ={
        name: "mainview",
        rect: {
            x: 0,
            y: 0,
            width: artboard.artboards[nowindex].width,
            height: artboard.artboards[nowindex].height
        },
    	css: [
    	  "background:"+mainbgcolor,
    	  "overflow:"+"hidden"
    	],
    };//最外层的视图

    let layers = [mainlayers].concat(artboard.artboards[nowindex].layers); //拿到所有layers
    layers = datchange.changelayerswidthheight(layers, widthx); //改变高度 
    let layerroot = datchange.getlayerroot(layers);//获取root son 关系 
    let treelayers = datchange.maketree(layerroot);//创建树结构  
    //console.log("treelayers=>\n",JSON.stringify(treelayers));
    viewmake.setAllLayersXmlVue(layers, treelayers, mainlayers); //设置xml视图
    let xmlstyle = viewmake.getAllStyle(layers); 
    let vuexmltree= viewmake.getvuexmltree(layers, treelayers, mainlayers); 
    vuexmltree = htmlformat.format(vuexmltree)
    let objectID = artboard.artboards[nowindex].objectID;
    if(outputfilepath && datchange.getArtboardsIsOk(objectID)){
        console.log(111,objectID)
        const fs = require("fs");
        let fd = fs.openSync(outputfilepath + objectID + '.vue', 'w');
        let tempfile = require('./vuefile.js').getfile();
        tempfile=tempfile.replace("__template__",vuexmltree);
        tempfile=tempfile.replace("__style__",xmlstyle);
        fs.writeFileSync(fd, tempfile); 
        fs.closeSync(fd);
        
        let fdhtml = fs.openSync(outputfilepath + objectID + '.html', 'w');
        let tempfilehtml = require('./htmlfile.js').getfile();
        tempfilehtml=tempfilehtml.replace("__template__",vuexmltree);
        tempfilehtml=tempfilehtml.replace("__style__",xmlstyle);
        tempfilehtml=tempfilehtml.replace(/\:style\=\"\{\'background-image\'\:\'url\(\'\+imgAssets\(\'/g,`style="background-image:url(`);
        tempfilehtml=tempfilehtml.replace(/\'\)\+\'\)\'\}/g,`)`);
        tempfilehtml=tempfilehtml.replace(/\/\>/g,`></div>`);
        fs.writeFileSync(fdhtml, tempfilehtml); 
        fs.closeSync(fdhtml);
    }
    let zipfilearrstr=outputfilepath + objectID + '.vue ' + outputfilepath + objectID + '.html ';
    for (let j = 0; j < layers.length; j++) {
        const exportable = layers[j]['exportable'];
        if(exportable){
            zipfilearrstr+=outputfilepath+"assets/"+layers[j]['exportable'][exportable.length-1].path+ " ";
        } 
    }
    let cmds = "zip -j "+outputfilepath+objectID+".zip "+zipfilearrstr + "&";
    require('child_process').execSync(cmds).toString();
}