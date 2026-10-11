<?php
// Determine if we're in a subdirectory
$isSubdir = strpos($_SERVER['PHP_SELF'], '/product-pages/') !== false;
$basePath = $isSubdir ? '../' : '';
?>
<header>
    <nav>
        <div class="logo">
            <img src="<?php echo $basePath; ?>logo.jpg" alt="BSBS & Bobbi Logo">
            <div class="brand-name">
                <span class="main-brand">BSBS & Bobbi</span>
                <span class="sub-brand">PetFood Store</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo $basePath; ?>homepage.php" <?php echo basename($_SERVER['PHP_SELF']) == 'homepage.php' ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="<?php echo $basePath; ?>products.php" <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'class="active"' : ''; ?>>Products</a></li>
            <li><a href="<?php echo $basePath; ?>contact.php" <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'class="active"' : ''; ?>>Contact Us</a></li>
        </ul>
        <div class="icon-bar">
            <div class="login-icon" id="login-icon" onclick="window.location.href='adminlogin.php'">
                <i class="fas fa-user"></i>
            </div>
            <div class="cart-icon" id="cart-icon" onclick="window.location.href='cart.php'">

                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count">0</span>
            </div>
        </div>
    </nav>
</header>