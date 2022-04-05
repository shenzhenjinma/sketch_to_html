

//a是否包含b
const acontainb = (a, b) => {
    let ax2 = a.x + a.width;
    let ay2 = a.y + a.height;
    let bx2 = b.x + b.width;
    let by2 = b.y + b.height;
    return a.x <= b.x && a.y <= b.y && ax2 >= bx2 && ay2 >= by2;
};
//a和b的差异
const adiffb = (a, b) => {
    return (a.width - b.width) * (a.height - b.height);
};
//拿到大与小的包含关系
const getlayerroot = layers => {
    var itemtag = {};
    for (let i = 1; i < layers.length; i++) {
        itemtag[i] = itemtag[i] || { root: 0, diff: 0 }
    }
    for (let i = 0; i < layers.length; i++) {
        const item_i = layers[i];
        for (let j = 0; j < layers.length; j++) {
            const item_j = layers[j];
            if (i == j || j==0) continue;
            if (acontainb(item_i.rect, item_j.rect)) {
                //a包含b
                var diff = adiffb(item_i.rect, item_j.rect); //计算差异大小 
                if (!itemtag[j].diff || itemtag[j].diff > diff) {
                 if(itemtag[i] && itemtag[i].root !=j) itemtag[j] = { root: i, diff };
                }
            }
        }
    }
    return itemtag;
};
 

//创建树结构
const maketree = layerroot => { 
    let rootmap = JSON.parse(JSON.stringify(layerroot)); 
    delete rootmap["0"]
    let isyou0=false;
    for (let key in rootmap) {
        if (rootmap[key].root == "0") {
            isyou0=true;
            break;
        } 
    }
    if(isyou0==false){
        for (let key in rootmap) {
           rootmap[key].root=0;
           break;
        }
    }
     
    const selfget = (rootid)=>{
        let tempobj=null; 
        for (let key in rootmap) {
            let temprootid = rootmap[key].root;
            if(temprootid==rootid){
                delete rootmap[key]
                tempobj = tempobj || {}
                tempobj[key]=selfget(key)
            }
        }
        return tempobj;
    } 
    let  treeobj={0:selfget("0")}; 
    //越边的也属于最外层的子视图
    for (let key in rootmap) {
        if (key == "0") continue;
        treeobj["0"][key] = null;
        delete rootmap[key];
    }
    return treeobj;
};
const isNumber=(value) =>{
    return !Number.isNaN(Number(value))
}
const toMyFixed=(num)=> {
    num = num.toString()
    let index = num.indexOf('.')
    if (index !== -1) {
        num = num.substring(0,  index + 3)
    } else {
        num = num.substring(0)
    }
    return num
}
const changelayerswidthheight = (layers,widthx)=>{ 
    for (let i = 0; i < layers.length; i++) {
        const item = layers[i];
        item.rect.width=Number((item.rect.width*widthx));
        item.rect.height=Number((item.rect.height*widthx));
        item.rect.x=Number((item.rect.x*widthx));
        item.rect.y=Number((item.rect.y*widthx));
        if (item.css) {
            for (let j = 0; j < item.css.length; j++) {
                const c = item.css[j];
                let arr = c.split(":");
                if(arr[1].indexOf("px;")!=-1){
                    let ns=arr[1].replace("px;","");
                    if(isNumber(ns)){
                        ns = toMyFixed(ns*widthx)
                    }
                    item.css[j]=`${arr[0]} : ${ns}px;`
                }
            }
        }
    } 
    return layers;
}
const getArtboardsIsOk=(s)=>{
    return !(/[^0-9a-zA-Z-]/gi.test(s));
}
module.exports ={
    changelayerswidthheight,getlayerroot, maketree,acontainb,getArtboardsIsOk
}