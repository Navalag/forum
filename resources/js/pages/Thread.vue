<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread', 'translations'],

        components: {Replies, SubscribeButton},

        data() {
            return {
                locale: window.App.locale,
                repliesCount: this.thread.replies_count,
                locked: this.thread.locked,
                pinned: this.thread.pinned,
                title: this.thread.title,
                body: this.thread.body,
                tags: this.thread.tags.map(tag => tag.name),
                form: {},
                editing: false
            };
        },

        created () {
            this.resetForm();
        },

        methods: {
            toggleLock () {
                let uri = `/${this.locale}/locked-threads/${this.thread.slug}`;

                axios[this.locked ? 'delete' : 'post'](uri);

                this.locked = ! this.locked;
            },

            togglePin () {
                let uri = `/${window.App.locale}/pinned-threads/${this.thread.slug}`;
                axios[this.pinned ? 'delete' : 'post'](uri);
                this.pinned = ! this.pinned;
            },

            update () {
                let uri = `/${window.App.locale}/threads/${this.thread.channel.slug}/${this.thread.slug}`;

                axios.patch(uri, this.form).then((response) => {
                    this.editing = false;

                    this.title = response.data.title;

                    this.body = response.data.body;

                    this.tags = response.data.tags.map(tag => tag.name);

                    flash(this.translations.thread_updated);
                })
                .catch((error) => {
                    flash('The thread contains spam', 'danger');
                });
            },

            resetForm () {
                this.form = {
                    title: this.thread.title,
                    tags: this.thread.tags.map(tag => tag.name).join(', '),
                    body: this.thread.body
                };

                this.editing = false;
            }
        }
    }
</script>
