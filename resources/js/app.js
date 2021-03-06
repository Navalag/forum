/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('perfect-scrollbar/dist/js/perfect-scrollbar.jquery');

require('./modules/helpers');
require('./modules/_mobile_menu');
require('./modules/_desktop_ menu');
require('./modules/_avatar_mobile_aside');
// require('./modules/_avatar_scroll');
require('./modules/_button_switching');
require('./modules/_init_magnific_popup');
require('./modules/_modal_show_overtime');
require('./modules/_popup_settings');
require('./modules/_search_compose');
require('./modules/_search_popup');

import InstantSearch from 'vue-instantsearch';

window.Vue = require('vue');

let authorizations = require('./authorizations');

Vue.use(InstantSearch);

Vue.prototype.authorize = function (...params) {
    if (! window.App.signedIn) return false;

    if (typeof params[0] === 'string') {
        return authorizations[params[0]](params[1]);
    }

    return params[0](window.App.user);
};

Vue.prototype.signedIn = window.App.signedIn;

window.events = new Vue();

window.flash = function (message, level = 'success') {
    window.events.$emit('flash', { message, level });
};

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/Flash.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('flash', require('./components/Flash.vue').default);
Vue.component('paginator', require('./components/Paginator.vue').default);
Vue.component('user-notifications', require('./components/UserNotifications.vue').default);
Vue.component('avatar-form', require('./components/AvatarForm.vue').default);
Vue.component('wysiwyg', require('./components/Wysiwyg.vue').default);
Vue.component('user-settings', require('./components/UserSettings.vue').default);
Vue.component('thread-tittle-input', require('./components/ThreadTittleInput.vue').default);
Vue.component('channel-card', require('./components/ChannelCard.vue').default);

Vue.component('thread-view', require('./pages/Thread.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
