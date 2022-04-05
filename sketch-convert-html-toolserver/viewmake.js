
let pinyinUtil = require("./pinyin/pinyinUtil.js");

const isNumber = (value) => {
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
const getOpacityHex=(v)=>{ 
    var round = Math.round(255*v);//四舍五入
    var hexString = round.toString(16)
    return hexString.length <2?"0"+hexString:hexString;   
}
let allstyle = {};
//获取唯一style
const get_only_style_name = (str) => {
    str = str || "style"
    let py = pinyinUtil.chineseToPinYin(str);
    py = isNumber(py.substr(0, 1)) ? "style" + py : py;
    py = py.length > 20 ? py.substr(0, 20) : py;
    if (allstyle[py]) {
        let nindex = 1;
        while (allstyle[py + "_" + nindex]) {
            nindex++;
        }
        py = py + "_" + nindex;
    }
    allstyle[py] = true;
    return py;
};
//a跟b重叠
const aoverlapb = (a, b) => {
    let maxX, maxY, minX, minY;
    maxX = a.x + a.width >= b.x + b.width ? a.x + a.width : b.x + b.width;
    maxY = a.y + a.height >= b.y + b.height ? a.y + a.height : b.y + b.height;
    minX = a.x <= b.x ? a.x : b.x;
    minY = a.y <= b.y ? a.y : b.y;
    if (
        maxX - minX <= a.width + b.width &&
        maxY - minY <= a.height + b.height
    ) {
        return true;
    }
    return false;
};
//value在一个区间内
const vinarea = (v, s, e) => {
    return v >= s && v <= e;
};

//a和b x轴交叉
const aboverlapx = (a, b) => {
    let ax2 = a.x + a.width;
    let bx2 = b.x + b.width;
    return (
        vinarea(b.x, a.x, ax2) ||
        vinarea(bx2, a.x, ax2) ||
        vinarea(a.x, b.x, bx2) ||
        vinarea(ax2, b.x, bx2)
    );
};

//a和b y轴交叉
const aboverlapy = (a, b) => {
    let ay2 = a.y + a.height;
    let by2 = b.y + b.height;
    return (
        vinarea(b.y, a.y, ay2) ||
        vinarea(by2, a.y, ay2) ||
        vinarea(a.y, b.y, by2) ||
        vinarea(ay2, b.y, by2)
    );
};


//视图是否重叠
const layersIsOverlap = (layers, obj) => {
    for (let key1 in obj) {
        for (let key2 in obj) {
            if (key1 == key2) continue;
            let item1 = layers[key1].rect;
            let item2 = layers[key2].rect;
            if (aoverlapb(item1, item2)) return true;
        }
    }
    return false;
};

//视图是否可以row
const layersIsCanRow = (layers, obj) => {
    for (let key1 in obj) {
        for (let key2 in obj) {
            if (key1 == key2) continue;
            let item1 = layers[key1].rect;
            let item2 = layers[key2].rect;

            if (aboverlapx(item1, item2)) return false; //如果出现x轴交叉，说明不支持row布局
        }
    }

    return true;
};

//视图是否可以column
const layersIsCanColumn = (layers, obj) => {
    for (let key1 in obj) {
        for (let key2 in obj) {
            if (key1 == key2) continue;
            let item1 = layers[key1].rect;
            let item2 = layers[key2].rect;
            if (aboverlapy(item1, item2)) return false; //如果出现y轴交叉，说明不支持column布局
        }
    }
    return true;
};

//获取布局的类型
const getLayersType = (layers, obj, rootlayers) => {
    if (rootlayers.type == "text") return "absolute";
    if (layersIsOverlap(layers, obj)) return "absolute";
    if (layersIsCanRow(layers, obj)) return "row";
    if (layersIsCanColumn(layers, obj)) return "column";
    return "absolute";
};

const getLayersXmlVue = (layers, o, rootlayers) => {
    let vtype = getLayersType(layers, o, rootlayers);
    let rootarea = rootlayers.rect;

    if (vtype == "row" || vtype == "column") {//为row或者column增加rowleft或者columntop
        let arro = [];
        for (let key in o) {
            arro.push({ id: key, rect: layers[key].rect });
        }
        arro.sort(function (a, b) {//排序
            if (vtype == "row")
                return a.rect.x - b.rect.x;
            return a.rect.y - b.rect.y;
        });
        let nowv = vtype == "row" ? rootarea.x : rootarea.y;
        for (let i = 0; i < arro.length; i++) {
            const item = arro[i];
            if (vtype == "row") {
                layers[item.id].rect['xleft'] = item.rect.x - nowv;
                layers[item.id].rect['xtop'] = item.rect.y - rootarea.y;
                nowv = item.rect.x + item.rect.width;
            } else {
                layers[item.id].rect['xtop'] = item.rect.y - nowv;
                layers[item.id].rect['xleft'] = item.rect.x - rootarea.x;
                nowv = item.rect.y + item.rect.height;
            }
        }
    }
    for (let k in o) {
        let item = layers[k];
        let basecss = {
            height: toMyFixed(item.rect.height) + "px",
            width: toMyFixed(item.rect.width) + "px",
        }; //css
        let divtext = "";//文字 
        let divimg = "";
        let sontag = typeof o[k] == "object" && o[k] != null ? "sontag_" + k + "_son" : "";
        //如果没有图片的，就是用css
        if (item.css && !item.exportable) {
            for (let i = 0; i < item.css.length; i++) {
                const c = item.css[i];
                let arr = c.replace(";", "").split(":");
                basecss[arr[0]] = arr[1];
            }
            if(basecss["opacity"] && basecss["background"]){
              basecss["background"] += getOpacityHex(basecss["opacity"]);
              delete basecss["opacity"];
            }
        }
        if (item.type == "text" && o[k] == null) {
            divtext = item.content;
        }
        if (item.shapeType == "oval" && item.type == "shape") {
            basecss['border-radius'] = (item.rect.width / 2) + "px";
        }

        if (item.exportable) {//如果有图片
            let maxindex = item.exportable.length - 1;
            let pngname = item.exportable[maxindex].path;
            divimg = ` :style="{'background-image':'url('+imgAssets('${pngname}')+')'}"`
            Object.assign(basecss, {
                "background-repeat": "no-repeat",   //不重复
                "background-size": "100% 100%"     // 满屏 
            });
        }
        if (vtype == "absolute") {
            basecss = Object.assign(basecss, {
                position: "absolute",
                left: toMyFixed(item.rect.x - rootarea.x) + "px",
                top: toMyFixed(item.rect.y - rootarea.y) + "px",
                "z-index": k
            });
        } else if (vtype == "row" || vtype == "column") {
            basecss['z-index'] = k;
            basecss['margin-left'] = toMyFixed(item.rect.xleft) + "px";
            basecss['margin-top'] = toMyFixed(item.rect.xtop) + "px";
        }
        let thisstylename = get_only_style_name(item.name); 
        item.xmlstylename = thisstylename;
        item.xmlstyle = basecss;
        item.xmltype = vtype;
        let endtag = (divtext+sontag).length>0?`>${divtext}${sontag}</div>`:`/>`;
        item.xmlvue = `<div class="${thisstylename}"${divimg}${endtag}`;
    }
};



const setAllLayersXmlVue = (layers, treeo, rootlayers) => {
    if (treeo && typeof treeo == "object") { //如果是对象
        getLayersXmlVue(layers, treeo, rootlayers);
        for (let key in treeo) {
            setAllLayersXmlVue(layers, treeo[key], layers[key]); //遍历该对象下有木有子对象
        }
    }
}
const getAllStyle = (layers) => {
    let stylearr = "";
    for (let i = 0; i < layers.length; i++) {
        const item = layers[i].xmlstyle;
        let styleone = "";
        for (const k in item) {
            styleone += `    ${k}:${item[k]};\n`
        }
        stylearr += "." + layers[i].xmlstylename + " {\n" + styleone + "}\n";
    }
    //stylearr=stylearr.replace(/"/g,"");
    return stylearr;
}

const getvuexmltree = (layers,treelayers,mainlayers) => {
    let tempobjxml = {};
    let settreexml = (o, k, rootlaye) => {
        let rootarea = rootlaye.rect;
        if (o == null) {
            // tempobjxml[k+"_son"] = layers[k].xmlvue;
        } else if (o !== null && typeof o == "object") {
            //如果是对象
            let xmlstr = "";
            let xmlarr = [];
            let vtype = "";
            for (let key in o) {
                settreexml(o[key], key, layers[key]);
                vtype = layers[key].xmltype;
                xmlarr.push({ id: key, rect: layers[key].rect });
            }
            xmlarr.sort(function (a, b) {  //排序
                if (vtype == "row") return a.rect.x - b.rect.x;
                return a.rect.y - b.rect.y;
            });

            let textstr = "";
            if (rootlaye.type == "text") {
                textstr = "\n<div class='defwidth100'>" + rootlaye.content + "\n</div>";
            }
            for (let i = 0; i < xmlarr.length; i++) {
                const item = xmlarr[i];
                xmlstr = xmlstr + layers[item.id].xmlvue +"\n";
            }
            tempobjxml[k] = xmlstr;
            var is0style = k=="0"?" mainview":""
            if (vtype == "row") {
                tempobjxml[k] =
                    `\n<div class='defflexrow${is0style}'>\n` +
                    tempobjxml[k] +
                    "\n</div>";
            } else if (vtype == "column") {
                tempobjxml[k] =
                    `\n<div class='defflexcolumn${is0style}'>\n` +
                    tempobjxml[k] +
                    "\n</div>";
            } else if (vtype == "absolute") {
                tempobjxml[k] =
                    `\n<div class='defabsolute${is0style}'>\n` +
                    textstr +
                    tempobjxml[k] +
                    "\n</div>";
            }
        }
    };
    settreexml(treelayers["0"], "0", mainlayers); 
    for (const key in tempobjxml) {
        for (const key2 in tempobjxml) {
            if (key == key2) continue;
            let tagtemp = "sontag_" + key + "_son";
            if (tempobjxml[key2].indexOf(tagtemp) > 0) {
                tempobjxml[key2] = tempobjxml[key2].replace(tagtemp, tempobjxml[key]);
                delete tempobjxml[key];
                break;
            }
        }
    } 
    return tempobjxml[0];
}


module.exports = {
    setAllLayersXmlVue, getAllStyle,getvuexmltree
}