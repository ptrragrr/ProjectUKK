import {
    createRouter,
    createWebHistory,
    type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useConfigStore } from "@/stores/config";
import NProgress from "nprogress";
import "nprogress/nprogress.css";

declare module "vue-router" {
    interface RouteMeta {
        pageTitle?: string;
        permission?: string;
    }
}

const routes: Array<RouteRecordRaw> = [
    {
        path: "/",
        redirect: "/dashboard",
        component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
        meta: {
            middleware: "auth",
        },
        children: [
            {
                path: "/dashboard",
                name: "dashboard",
                component: () => import("@/pages/dashboard/Index.vue"),
                meta: {
                    pageTitle: "Dashboard",
                    breadcrumbs: ["Dashboard"],
                },
            },
            {
                path: "/dashboard/profile",
                name: "dashboard.profile",
                component: () => import("@/pages/dashboard/profile/Index.vue"),
                meta: {
                    pageTitle: "Profile",
                    breadcrumbs: ["Profile"],
                },
            },
            {
                path: "/dashboard/setting",
                name: "dashboard.setting",
                component: () => import("@/pages/dashboard/setting/Index.vue"),
                meta: {
                    pageTitle: "Website Setting",
                    breadcrumbs: ["Website", "Setting"],
                },
            },
            {
                path: "/dashboard/input",
                name: "dashboard.input",
                component: () => import("@/pages/dashboard/input/index.vue"),
                // meta: {
                //     pageTitle: "Website Setting",
                //     breadcrumbs: ["Website", "Setting"],
                // },
            },

            // MASTER
            {
                path: "/dashboard/master/users/roles",
                name: "dashboard.master.users.roles",
                component: () =>
                    import("@/pages/dashboard/master/users/roles/Index.vue"),
                meta: {
                    pageTitle: "User Roles",
                    breadcrumbs: ["Master", "Users", "Roles"],
                },
            },
            // {
            //     path: "/dashboard/transaksi",
            //     name: "dashboard.transaksi",
            //     component: () =>
            //         import("@/pages/dashboard/transaksi/index.vue"),
            //     meta: {
            //         // pageTitle: "Transaksi",
            //         // breadcrumbs: ["Transaksi", "Transaksi"],
            //     },
            // },
            {
                path: "/dashboard/tiket",
                name: "dashboard.tiket",
                component: () => import("@/pages/dashboard/tiket/Index.vue"),
                // meta: {
                //     pageTitle: "Management",
                //     breadcrumbs: ["Management", "Management"],
                // },
            },
            //  {
            //     path: "/dashboard/tambah/management/konser",
            //     name: "dashboard.tambah.management.konser",
            //     component: () => import("@/pages/dashboard/tiket/konser/index.vue"),
            //     meta: {
            //         pageTitle: "Management",
            //         breadcrumbs: ["Management", "Management"],
            //     },
            // },
            // {
            //     path: "/dashboard/order",
            //     name: "dashboard.pesanan",
            //     component: () => import("@/pages/dashboard/order/index.vue"),
            //     meta: {
            //         // pageTitle: "Order",
            //         // breadcrumbs: ["Order", "Order"],
            //     },
            // },
            {
                path: "/dashboard/master/users",
                name: "dashboard.master.users",
                component: () =>
                    import("@/pages/dashboard/master/users/Index.vue"),
                meta: {
                    pageTitle: "Users",
                    breadcrumbs: ["Master", "Users"],
                },
            },
            {
                path: '/dashboard/transaksi/detail/:id',
                name: 'dashboard.transaksi.detail',
                component: () => import('@/pages/dashboard/transaksi/detail.vue'),
                meta: {
                    pageTitle: "Detail Transaksi",
                },
            }
        ],
    },
//     {
//     path: "/",
//     // component: () => import("@/layouts/default-layout/UserLayout.vue"),
//     meta: {
//         middleware: "auth",
//     },
//     children: [
        
//     ],
// },
// {
//     path: "/contact",
//     name: "contact",
//     component: () => import("@/pages/dashboard/users/contact/index.vue"),
//     meta: {
//         pageTitle: "Kontak",
//         breadcrumbs: ["Contact"],
//     },
// },
// {
//         path: "/home",
//         name: "home",
//         component: () => import("@/pages/dashboard/users/home/index.vue"),
//         meta: {
//             pageTitle: "Beranda Pengguna",
//             breadcrumbs: ["Home"],
//         },
//     },
    {
        path: "/sign-in",
        component: () => import("@/layouts/AuthLayout.vue"),
        children: [
            {
                path: "/sign-in",
                name: "sign-in",
                component: () => import("@/pages/auth/sign-in/Index.vue"),
                meta: {
                    pageTitle: "Sign In",
                    middleware: "guest",
                },
            },
        ],
    },
    {
        path: "/",
        component: () => import("@/layouts/SystemLayout.vue"),
        children: [
            {
                // the 404 route, when none of the above matches
                path: "/404",
                name: "404",
                component: () => import("@/pages/errors/Error404.vue"),
                meta: {
                    pageTitle: "Error 404",
                },
            },
            {
                path: "/500",
                name: "500",
                component: () => import("@/pages/errors/Error500.vue"),
                meta: {
                    pageTitle: "Error 500",
                },
            },
        ],
    },
    {
        path: "/:pathMatch(.*)*",
        redirect: "/404",
    },

    {
        path: "/dashboard_pengguna",
        name: "dashboard_pengguna",
        component: () => import("@/pages/dashboard_pengguna/navbarpg.vue"),
        // meta: {
        //     middleware: "auth",
        // },
        children: [
            // {
            //     path: "/dashboard",
            //     name: "dashboard",
            //     component: () => import("@/pages/dashboard/Index.vue"),
            //     meta: {
            //         pageTitle: "Dashboard",
            //         breadcrumbs: ["Dashboard"],
            //     },
            // },
            {
                path: "",
                name: "home",
                component: () => import("@/pages/dashboard_pengguna/home.vue"),
                meta: {
                    pageTitle: "Home",
                    breadcrumbs: ["Home"],
                },
            },
            {
                path: "ticket",
                name: "ticket",
                component: () => import("@/pages/dashboard_pengguna/tiket.vue"),
                meta: {
                    pageTitle: "Ticket",
                    breadcrumbs: ["Ticket"],
                },
            },
             {
                path: "about",
                name: "about",
                component: () => import("@/pages/dashboard_pengguna/about.vue"),
                meta: {
                    pageTitle: "About",
                    breadcrumbs: ["About"],
                },
            },
            
             {
                path: "contact",
                name: "contact",
                component: () => import("@/pages/dashboard_pengguna/contact.vue"),
                meta: {
                    pageTitle: "Contact",
                    breadcrumbs: ["Contact"],
                },
            },
            {
                path: "checkout",
                name: "checkout",
                component: () => import("@/pages/dashboard_pengguna/checkout.vue"),
                meta: {
                    pageTitle: "Checkout",
                    breadcrumbs: ["Checkout"],
                },
            },
            
            
            
        ],
    },
];



const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to) {
        // If the route has a hash, scroll to the section with the specified ID; otherwise, scroll to the top of the page.
        if (to.hash) {
            return {
                el: to.hash,
                top: 80,
                behavior: "smooth",
            };
        } else {
            return {
                top: 0,
                left: 0,
                behavior: "smooth",
            };
        }
    },
});

router.beforeEach(async (to, from, next) => {
  if (to.name) {
    NProgress.start();
  }

  const authStore = useAuthStore();
  const configStore = useConfigStore();

  if (to.meta.pageTitle) {
    document.title = `${to.meta.pageTitle} - ${import.meta.env.VITE_APP_NAME}`;
  } else {
    document.title = import.meta.env.VITE_APP_NAME;
  }

  configStore.resetLayoutConfig();

  if (!authStore.isAuthenticated) await authStore.verifyAuth();

  if (to.meta.middleware === "auth") {
    if (authStore.isAuthenticated) {
      if (to.meta.permission && !authStore.user.permission.includes(to.meta.permission)) {
        return next({ name: "404" });
      } 
    //   else if (to.name === "dashboard" && authStore.user.role?.name === "pengguna") {
    //     return next({ name: "home" });
    //   } 
    else if (
  to.name === "dashboard" &&
  authStore.user &&
  authStore.user.role &&
  authStore.user.role.name === "pengguna"
) {
  return next({ name: "home" });
}
      else {
        return next();
      }
    } else {
      return next({ name: "dashboard_pengguna" });
    }
  }

  if (to.meta.middleware === "guest" && authStore.isAuthenticated) {
    return next({ name: "dashboard" });
  }

  // fallback, biar next() selalu terpanggil
  return next();
});

// router.beforeEach(async (to, from, next) => {
//     if (to.name) {
//         // Start the route progress bar.
//         NProgress.start();
//     }

//     const authStore = useAuthStore();
//     const configStore = useConfigStore();

//     // current page view title
//     if (to.meta.pageTitle) {
//         document.title = `${to.meta.pageTitle} - ${import.meta.env.VITE_APP_NAME
//             }`;
//     } else {
//         document.title = import.meta.env.VITE_APP_NAME as string;
//     }

//     // reset config to initial state
//     configStore.resetLayoutConfig();

//     // verify auth token before each page change
//     if (!authStore.isAuthenticated) await authStore.verifyAuth();

//     // before page access check if page requires authentication
    
//         if (authStore.isAuthenticated) {
//             if (to.meta.middleware == "auth") {
//                 if (authStore.isAuthenticated) {
//                     if (
//                         to.meta.permission &&
//                         !authStore.user.permission.includes(to.meta.permission)
//                     ) {
//                         next({ name: "404" });
//                     } else if (to.name === "dashboard" && authStore.user.role?.name === "pengguna") {
//                         next({ name: "home" });
//                     } else {
//                         next();
//                     }
//                 } else {
//                     next({ name: "sign-in" });
//                 }
//             }
//         } else if (to.meta.middleware == "guest" && authStore.isAuthenticated) {
//             next({ name: "dashboard" });
//         } else {
//             next();
//         }
//     }
// );

router.afterEach(() => {
    // Complete the animation of the route progress bar.
    NProgress.done();
});

export default router;
