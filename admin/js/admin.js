//  ADMIN JAVASCRIPT

// SIDEBAR

const sidebar = document.getElementById("sidebar");
const menuToggle = document.getElementById("menuToggle");
const closeSidebar = document.getElementById("closeSidebar");
const sidebarOverlay = document.getElementById("sidebarOverlay");

function openSidebar() {
    sidebar.classList.add("open");
    sidebarOverlay.classList.add("show");
}

function closeSide() {
    sidebar.classList.remove("open");
    sidebarOverlay.classList.remove("show");
}

menuToggle.addEventListener("click", openSidebar);
closeSidebar.addEventListener("click", closeSide);
sidebarOverlay.addEventListener("click", closeSide);

// ACTIVE SIDEBAR LINKS

document.querySelectorAll(".sidebar-link").forEach(link => {
    link.addEventListener("click", function (event) {
        if (
            this.classList.contains("logout") ||
            this.getAttribute("href") !== "#"
        ) {
            return;
        }

        event.preventDefault();

        document.querySelectorAll(".sidebar-link").forEach(item => {
            item.classList.remove("active");
        });

        this.classList.add("active");

        if (window.innerWidth <= 992) {
            closeSide();
        }
    });
});

// REVENUE CHART

const revenueChart = new Chart(
    document.getElementById("revenueChart"),
    {
        type: "line",
        data: {
            labels: [
                "Jan", "Feb", "Mar", "Apr",
                "May", "Jun", "Jul", "Aug",
                "Sep", "Oct", "Nov", "Dec"
            ],

            datasets: [
                {
                    label: "Revenue",
                    data: [
                        12000, 16000, 14000, 22000,
                        19000, 26000, 23000, 30000,
                        28000, 35000, 32000, 40000
                    ],
                    borderColor: "#d4af37",
                    backgroundColor: "rgba(212,175,55,0.08)",
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 5
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                },

                tooltip: {
                    backgroundColor: "#0c0c0c",
                    borderColor: "#d4af37",
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false
                }
            },

            scales: {
                x: {
                    grid: {
                        display: false
                    },

                    border: {
                        display: false
                    },

                    ticks: {
                        color: "#777777",

                        font: {
                            size: 10
                        }
                    }
                },

                y: {
                    beginAtZero: true,

                    border: {
                        display: false
                    },

                    grid: {
                        color: "rgba(255,255,255,0.06)"
                    },

                    ticks: {
                        color: "#777777",

                        font: {
                            size: 10
                        },

                        callback: function (value) {
                            return "$" + (value / 1000) + "k";
                        }
                    }
                }
            }
        }
    }
);

// PERIOD SELECT

const periodSelect = document.getElementById("periodSelect");

periodSelect.addEventListener("change", function () {
    if (this.value === "weekly") {
        revenueChart.data.labels = [
            "Mon", "Tue", "Wed",
            "Thu", "Fri", "Sat", "Sun"
        ];

        revenueChart.data.datasets[0].data = [
            3200, 4500, 3900,
            5200, 6800, 7400, 6100
        ];
    } else {
        revenueChart.data.labels = [
            "Jan", "Feb", "Mar", "Apr",
            "May", "Jun", "Jul", "Aug",
            "Sep", "Oct", "Nov", "Dec"
        ];

        revenueChart.data.datasets[0].data = [
            12000, 16000, 14000, 22000,
            19000, 26000, 23000, 30000,
            28000, 35000, 32000, 40000
        ];
    }

    revenueChart.update();
});

// ORDER DONUT

const orderChart = new Chart(
    document.getElementById("orderChart"),
    {
        type: "doughnut",
        data: {
            labels: [
                "Completed",
                "Pending",
                "Cancelled"
            ],

            datasets: [
                {
                    data: [168, 52, 28],

                    backgroundColor: [
                        "#d4af37",
                        "#f6c453",
                        "#d9534f"
                    ],
                    borderWidth: 0,
                    hoverOffset: 5
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "75%",

            plugins: {
                legend: {
                    display: false
                },

                tooltip: {
                    backgroundColor: "#0c0c0c",
                    borderColor: "#d4af37",
                    borderWidth: 1,
                    padding: 12
                }
            }
        }
    }
);

// THEME (dark / light) FOR THE CHARTS

function applyChartTheme() {
    const light = document.documentElement.getAttribute("data-theme") === "light";

    const tick = light ? "#6b6b6b" : "#777777";
    const grid = light
        ? "rgba(0,0,0,0.07)"
        : "rgba(255,255,255,0.06)";

    const tipBg = light ? "#ffffff" : "#0c0c0c";
    const tipText = light ? "#1c1c1c" : "#ffffff";

    revenueChart.options.scales.x.ticks.color = tick;
    revenueChart.options.scales.y.ticks.color = tick;
    revenueChart.options.scales.y.grid.color = grid;

    [revenueChart, orderChart].forEach(function (chart) {
        chart.options.plugins.tooltip.backgroundColor = tipBg;
        chart.options.plugins.tooltip.titleColor = tipText;
        chart.options.plugins.tooltip.bodyColor = tipText;

        chart.update();
    });
}

document.addEventListener("themechange", applyChartTheme);