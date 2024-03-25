<script>
import { fetchCardsByName, fetchSetCode } from '../services/cardService.js';

export default {
  data() {
    return {
      cards: [],
      displayCards: [],
      loadingCards: false,
      search: '',
      setCodes: [],
      setCode: '',
    };
  },
  methods: {
    searchCards() {
      if (this.search.length < 3) {
        this.loadingCards = false;
        return;
      }
      this.loadingCards = true;
      fetchCardsByName(this.search).then((cards) => {
        this.loadingCards = false;
        if (this.setCode !== '') {
          this.filterCards();
        }
        this.cards = cards;
        this.displayCards = cards;
      });
    },
    clearSearch() {
      this.search = '';
      this.cards = [];
    },
    clearCodes() {
      this.setCode = '';
      this.displayCards = this.cards;
    },
    filterCards() {
      this.displayCards = this.cards.filter((card) => card.setCode === this.setCode);
    },
  },
  created() {
    fetchSetCode().then((setCodes) => {
      this.setCodes = setCodes;
    });
  },

};
</script>

<template>
  <div>
    <h1>Rechercher une Carte</h1>
  </div>
  <div class="flex-row">
    <div>
      Filtrer par :
      <select v-model="setCode">
        <option value="" selected disabled hidden>Choisir un setCode</option>
        <option @click="filterCards" v-for="setCode in setCodes" :key="setCode" :value="setCode">
          {{ setCode }}
        </option>
      </select>
      <button v-if="setCode" @click="clearCodes">Supprimer le filtre</button>
    </div>
    <div>
      <input type="text" v-model="search" @input="searchCards" />
      <button type="reset" @click="clearSearch">Vider</button>
    </div>
  </div>
  <div class="card-list">
    <div v-if="loadingCards">Loading...</div>
    <div v-else>
      <div class="card" v-for="card in displayCards" :key="card.id">
        <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }"> {{ card.name }} - {{
            card.uuid
          }}
        </router-link>
      </div>
    </div>
  </div>
</template>