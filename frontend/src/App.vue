<template>
  <div id="app">
    <input v-model="searchterm" placeholder="twitter search term">
    <button v-on:click="readTweet">Search</button>
  </div>
</template>

<script>
import axios from 'axios'

function readPhrase(phrase) {
  const tokenOptions = {
      method: 'POST',
      responseType: 'arraybuffer',
      url: 'http://127.0.0.1:8001/say',
      data: phrase
  }

  axios(tokenOptions)
    .then(response => {
        let audioContext = new (window.AudioContext || window.webkitAudioContext)()
        let bufferSource = audioContext.createBufferSource()

        audioContext.decodeAudioData(response.data, function (buf) {
            bufferSource.buffer = buf
            bufferSource.connect(audioContext.destination)
            bufferSource.start(0)
        })
    })
}

function getTweets(searchterm) {
  const tokenOptions = {
    method: 'GET',
    headers: {
      'Authorization': `Bearer tokengoeshere`
    },
    url: `https://api.twitter.com/1.1/search/tweets.json?q=${searchterm}`
  }

  axios(tokenOptions)
    .then(response => {
      console.log(response.data.statuses[0].text);
      readPhrase(response.data.statuses[0].text);
    })
}

export default {
  name: 'app',
  data () {
    return {
      searchterm: '',
      readingTweet: ''
    }
  },
    mounted() {
      readPhrase('stuff')
    },
  methods: {
    readTweet: function() {
        readPhrase(this.searchterm);
    }
  }
}
</script>

<style lang="scss">

</style>
