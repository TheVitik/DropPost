<script>
import {telegramLoginTemp} from 'vue3-telegram-login'

export default {
  components: {
    telegramLoginTemp
  },
  data() {
    return {
      loader: null
    }
  },
  mounted() {
    this.loader = this.$loading.show({
      container: this.$refs.formContainer,
    });
  },
  methods: {
    telegramCallback(response) {
      console.log(response)

      this.loader = this.$loading.show({
        container: this.$refs.formContainer,
      });
      // send post to /api/auth/login
      axios.post('/api/auth/login', response).then(response => {
        this.loader.hide();
        localStorage.setItem('token', response.data.token);
        this.$router.push('/dashboard');
      }).catch(error => {
        this.loader.hide();
        console.log(error)
      })
    },
    widgetLoaded() {
      this.loader.hide();
    }
  }
}
</script>
<template>
  <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
    <div class="flex flex-column align-items-center justify-content-center">
      <div
          style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
        <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
          <div class="text-center mb-5">
            <div class="text-900 text-3xl font-medium mb-3">Вхід</div>
            <span class="text-600 font-medium">Увійдіть за допомогою Telegram</span>
          </div>
          <div>
            <telegram-login-temp
                mode="callback"
                telegram-login="droppost_xbot"
                @loaded="widgetLoaded"
                @callback="telegramCallback"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>