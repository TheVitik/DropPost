export class PostService {
    constructor() {
        this.token = localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    }

    async getPosts(channelId) {
        return axios.get(`/api/channels/${channelId}/posts`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch posts');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching posts:', error);
                throw error;
            });
    }

    async getPost(postId) {
        return axios.get(`/api/posts/${postId}`)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to fetch post');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error fetching post:', error);
                throw error;
            });
    }

    async createPost(post) {
        console.log(post);
        // create post with files uploading
        let formData = new FormData();
        formData.append('content_html', post.content_html);
        formData.append('is_draft', post.is_draft);
        formData.append('is_template', post.is_template);
        formData.append('is_advertisement', post.is_advertisement);

        console.log(post.channels);

        post.channels.forEach(channelId => {
            console.log(channelId);
            formData.append('channels[]', channelId);
        });

        if (post.plan_publish_date) {
            formData.append('plan_publish_date', post.plan_publish_date.toDateString());
        }
        if (post.plan_delete_date) {
            formData.append('plan_delete_date', post.plan_delete_date.toDateString());
        }

        post.files.forEach(file => {
            formData.append('files[]', file);
        });

        return axios.post(`/api/posts`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
            .then(response => {
                if (response.status !== 201) {
                    throw new Error('Failed to create post');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error creating post:', error);
                throw error;
            });


    }

    async updatePost(post) {
        return axios.put(`/api/posts/${post.id}`, post)
            .then(response => {
                if (response.status !== 200) {
                    throw new Error('Failed to update post');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error updating post:', error);
                throw error;
            });
    }

    async deletePost(postId) {
        return axios.delete(`/api/posts/${postId}`)
            .then(response => {
                if (response.status !== 204) {
                    throw new Error('Failed to delete post');
                }
                return response.data;
            })
            .catch(error => {
                console.error('Error deleting post:', error);
                throw error;
            });
    }
}