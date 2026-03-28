

const routes = [
    {
        path: '/app/:tenant',
        children: [
            {
                path: '', // Esto resuelve a /app/:tenant/
                name: 'login',
                meta: {
                    layout: 'auth',
                    auth: false,
                },
                component: () => import('../pages/auth/index.vue')
            },
            {
                path: 'manager',
                children: [
                    {
                        path: '',
                        name: 'manager.dashboard',
                        meta: {
                            layout: 'manager',
                            auth: true
                        },
                        component: () => import('../pages/manager/dashboard.vue')
                    },
                    {
                        path: 'projects',
                        children: [
                            {
                                path: '',
                                name: 'manager.projects.index',
                                meta: {
                                    layout: 'manager',
                                    auth: true
                                },
                                component: () => import('../pages/manager/projects/index.vue')
                            },
                            {
                                path: ':id',
                                name: 'manager.projects.show',
                                meta: {
                                    layout: 'manager',
                                    auth: true
                                },
                                component: () => import('../pages/manager/projects/show.vue')
                            }
                        ]
                    },
                    {
                        path: 'employees',
                        children: [
                            {
                                path: '',
                                name: 'manager.employees.index',
                                meta: {
                                    layout: 'manager',
                                    auth: true
                                },
                                component: () => import('../pages/manager/employees/index.vue')
                            }
                        ]
                    }
                ]
            }
        ]
    }
];
export default routes;