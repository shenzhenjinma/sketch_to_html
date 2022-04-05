module.exports ={
  getfile:()=>{
    return `
<template lang="html">
__template__
</template>
<script>
export default {
  data() {
    return {
      imgAssets: imgName => require("./" + imgName)
    };
  }
};
</script>
<style scoped>
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
    `
  }
}