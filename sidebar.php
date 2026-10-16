
<?php
$currentPage = basename($_SERVER["PHP_SELF"]);
?>

<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <i data-lucide="salad"></i>
        </div>

        <div class="sidebar-brand-info">
            <span class="sidebar-brand-name">Meal Planner</span>
            <span class="sidebar-brand-subtitle">YOUR DAILY MEAL GUIDE</span>
        </div>

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Collapse sidebar"
            title="Collapse sidebar"
        >
            <i data-lucide="panel-left-close"></i>
        </button>
    </div>

    <p class="sidebar-label">MAIN MENU</p>

    <nav class="sidebar-nav">

        <a href="dashboard.php"
           class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>"
           title="Dashboard">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>

        <a href="view_meal.php"
           class="<?= $currentPage === 'view_meal.php' ? 'active' : '' ?>"
           title="My Meals">
            <i data-lucide="utensils"></i>
            <span>My Meals</span>
        </a>

        <a href="recent_meal.php"
           class="<?= $currentPage === 'recent_meal.php' ? 'active' : '' ?>"
           title="Recently Viewed">
            <i data-lucide="history"></i>
            <span>Recently Viewed</span>
        </a>

        <a href="profile.php"
           class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>"
           title="Profile">
            <i data-lucide="user-round"></i>
            <span>Profile</span>
        </a>

    </nav>

    <div class="sidebar-bottom">
        <a href="logout.php" title="Logout">
            <i data-lucide="log-out"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("sidebarToggle");

    if (window.lucide) {
        lucide.createIcons();
    }

    if (!sidebar || !toggle) {
        return;
    }

    toggle.addEventListener("click", function () {
        sidebar.classList.toggle("collapsed");

        const collapsed = sidebar.classList.contains("collapsed");

        toggle.innerHTML = collapsed
            ? '<i data-lucide="panel-left-open"></i>'
            : '<i data-lucide="panel-left-close"></i>';

        toggle.setAttribute(
            "aria-label",
            collapsed ? "Expand sidebar" : "Collapse sidebar"
        );

        toggle.setAttribute(
            "title",
            collapsed ? "Expand sidebar" : "Collapse sidebar"
        );

        if (window.lucide) {
            lucide.createIcons();
        }
    });
});
</script>
