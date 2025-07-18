const MainMenuConfig: Array<MenuItem> = [
    {
        pages: [
            {
                heading: "Dashboard",
                name: "dashboard",
                route: "/dashboard",
                keenthemesIcon: "element-11",
            },
        ],
    },

    // WEBSITE SECTION
    {
        heading: "Website",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER SECTION
            {
                sectionTitle: "Master",
                route: "/master",
                keenthemesIcon: "bi bi-exclamation-octagon-fill",
                name: "master",
                sub: [
                    {
                        sectionTitle: "User Management",
                        route: "/users",
                        name: "master-user",
                        sub: [
                            {
                                heading: "Roles",
                                name: "master-role",
                                route: "/dashboard/master/users/roles",
                            },
                            {
                                heading: "Users",
                                name: "master-user",
                                route: "/dashboard/master/users",
                            },
                        ],
                    },
                ],
            },
             {
                sectionTitle: "Tambah",
                route: "/tambah",
                keenthemesIcon: "bi bi-exclamation-octagon-fill",
                name: "tambah",
                sub: [
                    {
                        sectionTitle: " Management ",
                        route: "/management",
                        name: "tambah-management",
                        sub: [
                            {
                                heading: "Konser",
                                name: "tambah-konser",
                                route: "/dashboard/tambah/management/konser",
                            },
                            {
                                heading: "Tiket",
                                name: "tambah-tiket",
                                route: "/dashboard/tambah/management/tiket",
                            },
                        ],
                    },
                ],
            },
            // {
            //     heading: "Management",
            //     route: "/dashboard/management",
            //     name: "kelola",
            //     keenthemesIcon: "bi bi-kanban",
            // },
            // {
            //     heading: "Order",
            //     route: "/dashboard/order",
            //     name: "pesanan",
            //     keenthemesIcon: "bi bi-list-ol",
            // },
            {
                heading: "Transaksi",
                route: "/dashboard/transaksi",
                name: "transaksi",
                keenthemesIcon: "bi bi-wallet2",
            },
            {
                heading: "History",
                route: "/dashboard/riwayat",
                name: "riwayat",
                keenthemesIcon: "bi bi-clock-history",
            },
            {
                heading: "Settings",
                route: "/dashboard/setting",
                name: "setting",
                keenthemesIcon: "bi bi-gear",
            },
        ],
    },
];

export default MainMenuConfig;
