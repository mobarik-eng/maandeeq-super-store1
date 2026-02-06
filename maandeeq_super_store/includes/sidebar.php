<div class="sidebar">
    <h3>Maandeeq Store</h3>
    <ul>
        <li><a href="/maandeeq_super_store/admin/dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
        </li>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="/maandeeq_super_store/products/index.php"
                    class="<?php echo strpos($_SERVER['PHP_SELF'], '/products/') !== false ? 'active' : ''; ?>">Products</a>
            </li>
            <li><a href="/maandeeq_super_store/admin/employees.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>">Employees</a>
            </li>
        <?php endif; ?>

        <li><a href="/maandeeq_super_store/sales/create.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'create.php' ? 'active' : ''; ?>">New Sale</a></li>
        <li><a href="/maandeeq_super_store/sales/history.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>">Sales History</a>
        </li>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="/maandeeq_super_store/reports/debts.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'debts.php' ? 'active' : ''; ?>">Debts</a></li>
            <li><a href="/maandeeq_super_store/reports/sales_report.php"
                    class="<?php echo basename($_SERVER['PHP_SELF']) == 'sales_report.php' ? 'active' : ''; ?>">Reports</a>
            </li>
        <?php endif; ?>

        <li><a href="/maandeeq_super_store/auth/logout.php">Logout</a></li>
    </ul>
</div>