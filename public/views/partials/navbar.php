<nav>
    <a href="/index" class="logo">MartialHub</a>

    <div class="hamburger" id="hamburgerBtn">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="nav-right">
        <div class="nav-links">
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="/adminUsers" class="admin-link">ADMIN PANEL</a>
            <?php endif; ?>
            <a href="/events">EVENTS</a>
            <a href="/rankings">RANKINGS</a>
            <a href="/profile">ATHLETES</a>
        </div>
        <div class="nav-buttons">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="nav-auth-icons">
                    <div class="bell">
                        <svg class="bell-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z M13.73 19C13.5542 19.3031 13.3019 19.5547 12.9982 19.7295C12.6946 19.9044 12.3504 20 12 20C11.6496 20 11.3054 19.9044 11.0018 19.7295C10.6981 19.5547 10.4458 19.3031 10.27 19" 
                                stroke="black" 
                                stroke-width="1.5"
                                stroke-linecap="round" 
                                stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <a href="/profile" class="btn-signup">MY PROFILE</a>
                    <a href="/logout" class="btn-login">LOG OUT</a>
                </div>
            <?php else: ?>
                <a href="/register" class="btn-signup">CREATE ACCOUNT</a>
                <a href="/login" class="btn-login">LOG IN</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-content-wrapper">
            <a href="/events" class="mobile-link">EVENTS</a>
            <a href="/rankings" class="mobile-link">RANKINGS</a>
            <a href="/profile" class="mobile-link">ATHLETES</a>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="/adminUsers" class="mobile-link admin-link">ADMIN PANEL</a>
            <?php endif; ?>

            <hr class="mobile-divider">

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-actions-group">
                    <a href="/notifications" class="mobile-btn notifications">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    NOTIFICATIONS
                    </a>
                    <a href="/profile" class="mobile-btn profile">MY PROFILE</a>
                </div>
                <a href="/logout" class="mobile-btn logout mt-auto">LOG OUT</a>
            <?php else: ?>
                <div class="user-actions-group">
                    <a href="/login" class="mobile-btn login">LOG IN</a>
                    <a href="/register" class="mobile-btn register">CREATE ACCOUNT</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>