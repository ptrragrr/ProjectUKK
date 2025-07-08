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
                heading: "Management",
                route: "/dashboard/management",
                name: "kelola",
                keenthemesIcon: "bi bi-kanban",
            },
            {
                heading: "Orders",
                route: "/dashboard/pesanan",
                name: "pesanan",
                keenthemesIcon: "bi bi-list-ol",
            },
            {
                heading: "Transactions",
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
