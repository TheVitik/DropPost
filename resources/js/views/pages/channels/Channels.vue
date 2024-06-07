<script>
import {ChannelService} from "../../../services/ChannelService.js";

export default {
  data() {
    return {
      project: null,
      channels: [],
      dataviewValue: null,
      layout: 'grid'
    }
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }

    this.getChannels();
  },
  methods: {
    addChannel() {
      this.$router.push('/channels/create');
    },
    getChannels() {
      if (this.project) {
        let loader = this.$loading.show({
          container: this.$refs.channelsContainer,
        });

        const channelService = new ChannelService();
        channelService.getChannels(this.project.id)
            .then(channels => {
              this.channels = channels;
              console.log(channels);
              loader.hide();
            })
            .catch(error => {
              console.log(error);
              loader.hide();
            });
      }
    }
  }
}
</script>
<template>
  <div class="flex justify-content-between mb-3">
    <h5>Канали</h5>
    <Button v-if="channels.length > 0" label="Додати" @click="addChannel" type="button" class="py-3 px-5"></Button>
  </div>
  <DataView v-if="channels.length > 0" :value="channels" :layout="layout" :paginator="true" :rows="9">
    <!--sortOrder="sortOrder" :sortField="sortField"-->
<!--    <template #header>
      <div class="grid grid-nogutter">
        <div class="col-6 text-left">
          <Dropdown v-model="sortKey" :options="sortOptions" optionLabel="label" placeholder="Sort By Price" @change="onSortChange($event)" />
        </div>
        <div class="col-6 text-right">
          <DataViewLayoutOptions v-model="layout" />
        </div>
      </div>
    </template>-->

    <template #list="slotProps">
      <div class="grid grid-nogutter">
        <router-link :to="{ 'name':'channels' }" v-for="(channel, index) in slotProps.items" :key="index" class="col-12">
          <div class="flex flex-column sm:flex-row sm:align-items-center p-4 gap-3" :class="{ 'border-top-1 surface-border': index !== 0 }">
            <div class="md:w-10rem relative">
              <img class="block xl:block mx-auto border-round w-full" :src="'https://primefaces.org/cdn/primevue/images/product/bamboo-watch.jpg'" :alt="channel.name" />
              <Tag :value="channel.type" :severity="'success'" class="absolute" style="left: 4px; top: 4px"></Tag>
            </div>
            <div class="flex flex-column md:flex-row justify-content-between md:align-items-center flex-1 gap-4">
              <div class="flex flex-row md:flex-column justify-content-between align-items-start gap-2">
                <div>
                  <span v-if="channel.username" class="font-medium text-secondary text-sm">{{ channel.username }}</span>
                  <div class="text-lg font-medium text-900 mt-2">{{ channel.name }}</div>
                </div>
                <div class="surface-100 p-1" style="border-radius: 30px">
                  <div class="surface-0 flex align-items-center gap-2 justify-content-center py-1 px-2" style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                    <span class="text-900 font-medium text-sm">{{ channel.members_count }}</span>
                    <i class="pi pi-star-fill text-yellow-500"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </router-link>
      </div>
    </template>

    <template #grid="slotProps">
      <div class="grid grid-nogutter">
        <router-link :to="{ name: 'channel-view', params: { id: channel.id} }" v-for="(channel, index) in slotProps.items" :key="index" class="col-12 sm:col-6 md:col-4 p-2">
          <div class="p-4 border-1 surface-border surface-card border-round flex flex-column">
            <div class="surface-50 flex justify-content-center border-round p-3">
              <div class="relative mx-auto">
                <img class="border-round w-full" :src="channel.photo_path" :alt="channel.name" style="max-width: 300px" />
                <Tag :value="channel.type" :severity="'success'" class="absolute" style="left: 4px; top: 4px"></Tag>
              </div>
            </div>
            <div class="pt-4">
              <div class="flex flex-row justify-content-between align-items-start gap-2">
                <div>
                  <span v-if="channel.username" class="font-medium text-secondary text-sm">{{ channel.username }}</span>
                  <div class="text-lg font-medium text-900 mt-1">{{ channel.name }}</div>
                </div>
                <div class="surface-100 p-1" style="border-radius: 30px">
                  <div class="surface-0 flex align-items-center gap-2 justify-content-center py-1 px-2" style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
                    <span class="text-900 font-medium text-sm">{{ channel.members_count }}</span>
                    <i class="pi pi-users text-blue-500"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </router-link>
      </div>
    </template>
  </DataView>
  <div v-if="channels.length === 0" class="grid">
    <div class="col-12">
      <div class="card text-center">
        <h4>Проект не має каналів</h4>
        <Button label="Підключити канал" @click="addChannel" type="button" class="py-3 px-5"></Button>
      </div>
    </div>
  </div>
</template>
<script setup>
</script>