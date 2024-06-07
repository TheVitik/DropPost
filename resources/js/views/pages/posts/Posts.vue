<script>
import {ChannelService} from "../../../services/ChannelService.js";
import {PostService} from "../../../services/PostService.js";

export default {
  data() {
    return {
      project: null,
      channel: {
        type: '',
        name: '',
        username: '',
        members_count: 0
      },
      posts: [],
      dataviewValue: null,
      layout: 'grid'
    }
  },
  mounted() {
    const project = localStorage.getItem('current_project');
    if (project) {
      this.project = JSON.parse(project);
    }

    this.getChannel(this.$route.params.id);
    this.getPosts(this.$route.params.id);
  },
  methods: {
    addPost() {
      this.$router.push({name: 'create-post'});
    },
    getChannel(channelId) {
      let loader = this.$loading.show({
        container: this.$refs.channelsContainer,
      });

      const channelService = new ChannelService();
      channelService.getChannel(this.project.id, channelId)
          .then(channel => {
            this.channel = channel;
            loader.hide();
          })
          .catch(error => {
            console.log(error);
            loader.hide();
          });
    },
    getPosts(channelId) {
      if (this.project) {
        let loader = this.$loading.show({
          container: this.$refs.channelsContainer,
        });

        const postService = new PostService();
        postService.getPosts(channelId)
            .then(posts => {
              this.posts = posts;
              loader.hide();
            })
            .catch(error => {
              console.log(error);
              loader.hide();
            });
      }
    },
    formatDate(datetimeString) {
      const ukrainianMonths = [
        "Січня", "Лютого", "Березня", "Квітня", "Травня", "Червня",
        "Липня", "Серпня", "Вересня", "Жовтня", "Листопада", "Грудня"
      ];
      const date = new Date(datetimeString)

      const day = date.getDate();
      const month = ukrainianMonths[date.getMonth()];
      const hours = ("0" + date.getHours()).slice(-2);
      const minutes = ("0" + date.getMinutes()).slice(-2);
      return `${day} ${month}, ${hours}:${minutes}`;
    }
  }
}
</script>
<template>
  <div class="flex justify-content-between mb-3">
    <h5>Публікації каналу {{ channel.name }}</h5>
    <Button v-if="posts.length > 0" label="Додати" @click="addPost" type="button" class="py-3 px-5"></Button>
  </div>
  <DataView v-if="posts.length > 0" :value="posts" :layout="layout" :paginator="true" :rows="9">
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
        <router-link :to="{ 'name':'posts' }" v-for="(channel, index) in slotProps.items" :key="index" class="col-12">
          <div class="flex flex-column sm:flex-row sm:align-items-center p-4 gap-3"
               :class="{ 'border-top-1 surface-border': index !== 0 }">
            <div class="md:w-10rem relative">
              <img class="block xl:block mx-auto border-round w-full"
                   :src="'https://primefaces.org/cdn/primevue/images/product/bamboo-watch.jpg'" :alt="channel.name"/>
              <Tag :value="channel.type" :severity="'success'" class="absolute" style="left: 4px; top: 4px"></Tag>
            </div>
            <div class="flex flex-column md:flex-row justify-content-between md:align-items-center flex-1 gap-4">
              <div class="flex flex-row md:flex-column justify-content-between align-items-start gap-2">
                <div>
                  <span v-if="channel.username" class="font-medium text-secondary text-sm">{{ channel.username }}</span>
                  <div class="text-lg font-medium text-900 mt-2">{{ channel.name }}</div>
                </div>
                <div class="surface-100 p-1" style="border-radius: 30px">
                  <div class="surface-0 flex align-items-center gap-2 justify-content-center py-1 px-2"
                       style="border-radius: 30px; box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.04), 0px 1px 2px 0px rgba(0, 0, 0, 0.06)">
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
        <router-link :to="{ name: 'posts-view', params: { id: post.id} }" v-for="(post, index) in slotProps.items"
                     :key="index" class="col-12 sm:col-6 md:col-4 p-2" style="color:black">
          <div class="p-4 border-1 surface-border surface-card border-round flex flex-column">
            <div class="pt-4">
              <div class="flex flex-row justify-content-between align-items-start gap-2">
                <div>
                  <span v-if="post.content_html" v-html="post.content_html"
                        class="font-medium text-secondary text-sm"></span>
                  <div class="text-sm font-medium text-500 mt-1">{{ formatDate(post.created_at) }}</div>
                </div>
              </div>
            </div>
          </div>
        </router-link>
      </div>
    </template>
  </DataView>
  <div v-if="posts.length === 0" class="grid">
    <div class="col-12">
      <div class="card text-center">
        <h4>Канал не має публікацій</h4>
        <Button label="Створити пост" @click="addPost" type="button" class="py-3 px-5"></Button>
      </div>
    </div>
  </div>
</template>
<script setup>
</script>