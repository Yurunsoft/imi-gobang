<template>
  <div>
    <gobang :disable="gobang.disable"></gobang>
  </div>
</template>

<script>
import Gobang from "@/components/Gobang.vue";
export default {
  components: {
    Gobang,
  },
  data() {
    return {
      gobang: {
        disable: false,
      },
    };
  },
  mounted(){

  },
  methods: {
  },
};
</script>

<style lang="less" scoped>

</style>
