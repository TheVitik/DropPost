import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '@/layout/AppLayout.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    path: '/dashboard',
                    name: 'dashboard',
                    component: () => import('@/views/Dashboard.vue')
                },
                {
                    path: '/projects/create',
                    name: 'projects-create',
                    component: () => import('@/views/pages/projects/CreateProject.vue')
                },
                {
                    path: '/channels',
                    name: 'channels',
                    component: () => import('@/views/pages/channels/Channels.vue')
                },
                {
                    path: '/channels/create',
                    name: 'channels-create',
                    component: () => import('@/views/pages/channels/CreateChannel.vue')
                },
                {
                    path: '/channels/:id',
                    name: 'channel-view',
                    component: () => import('@/views/pages/channels/ViewChannel.vue')
                },
                {
                    path: '/channels/:id/posts',
                    name: 'posts',
                    component: () => import('@/views/pages/posts/Posts.vue')
                },
                {
                    path: '/posts/create',
                    name: 'create-post',
                    component: () => import('@/views/pages/posts/CreatePost.vue')
                },
                {
                    path: '/posts/:id',
                    name: 'posts-view',
                    component: () => import('@/views/pages/posts/ViewPost.vue')
                },
                {
                    path: '/ai-bots/create',
                    name: 'ai-bots-create',
                    component: () => import('@/views/pages/bots/CreateBot.vue')
                },
                {
                    path: '/ai-bots',
                    name: 'ai-bots',
                    component: () => import('@/views/pages/bots/AIBots.vue')
                },
                {
                    path: '/ai-bots/:id',
                    name: 'ai-bots-view',
                    component: () => import('@/views/pages/bots/ViewBot.vue')
                },
                {
                    path: '/templates/create',
                    name: 'templates-create',
                    component: () => import('@/views/pages/templates/CreateTemplate.vue')
                },
                {
                    path: '/templates',
                    name: 'templates',
                    component: () => import('@/views/pages/templates/Templates.vue')
                },
                {
                    path: '/templates/:id',
                    name: 'templates-view',
                    component: () => import('@/views/pages/templates/ViewTemplate.vue')
                },
            ]
        },
        {
            path: '/auth/login',
            name: 'login',
            component: () => import('@/views/pages/auth/Login.vue')
        },
    ]
});

export default router;
