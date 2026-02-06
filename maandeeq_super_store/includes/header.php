<div class="header">
    <div class="header-title">
        <h2>Dashboard</h2>
    </div>
    <div class="user-info">
        <span>Welcome, <strong>
                <?php echo currentUserName(); ?>
            </strong> (
            <?php echo ucfirst($_SESSION['role']); ?>)
        </span>
    </div>
</div>