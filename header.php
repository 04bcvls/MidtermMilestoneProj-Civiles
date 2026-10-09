<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutri-Sarap Barangay Kitchen</title>
    <style>
        :root {
            --primary: #107c41;
            --primary-hover: #0b592e;
            --accent: #d97706;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --card-subtle: #f3f7f4;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --req: #dc2626;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1040px;
            margin: 0 auto;
            background: var(--card-bg);
            padding: 28px;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        /* Required Asterisk */
        .req {
            color: var(--req);
            font-weight: bold;
            margin-left: 2px;
        }

        /* Top Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #052e16 0%, #107c41 100%);
            color: white;
            padding: 18px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
        }

        .hero-banner h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            color: #fef08a;
        }

        .hero-banner p {
            margin: 4px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #bbf7d0;
            padding-bottom: 14px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 10px;
        }

        nav h2 {
            margin: 0;
            font-size: 1.3rem;
            color: var(--primary);
        }

        nav a {
            color: var(--primary);
            text-decoration: none;
            margin-left: 14px;
            font-weight: 700;
        }

        /* Knorr Split Hero Layout */
        .recipe-hero {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 20px;
            margin-bottom: 24px;
            border-radius: 12px;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .recipe-hero {
                grid-template-columns: 1fr;
            }
        }

        .hero-image-box img {
            width: 100%;
            height: 100%;
            min-height: 280px;
            max-height: 380px;
            object-fit: cover;
            border-radius: 12px;
        }

        .hero-placeholder {
            width: 100%;
            height: 280px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            border-radius: 12px;
        }

        .hero-info-box {
            background: var(--card-subtle);
            padding: 24px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #e0ebe3;
        }

        .hero-info-box h1 {
            margin: 0 0 8px 0;
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 800;
        }

        .hero-info-box p {
            color: #334155;
            margin: 0 0 16px 0;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        /* 4-Box Stat Grid */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin: 16px 0;
        }

        @media (max-width: 500px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .stat-card {
            background: #ffffff;
            padding: 10px 6px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #d1e2d6;
        }

        .stat-icon {
            font-size: 1.1rem;
            display: block;
            margin-bottom: 2px;
        }

        .stat-val {
            font-weight: 800;
            color: var(--primary);
            font-size: 0.95rem;
            display: block;
        }

        .stat-lbl {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Two Column Side-by-Side Content Grid */
        .recipe-body-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 20px;
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .recipe-body-grid {
                grid-template-columns: 1fr;
            }
        }

        .content-card {
            background: var(--card-subtle);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e0ebe3;
        }

        .content-card h3 {
            color: var(--primary);
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 1.3rem;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 8px;
        }

        /* Leaf Ingredient Bullets */
        .ingredient-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ingredient-list li {
            padding: 8px 0;
            border-bottom: 1px dashed #cbd5e1;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .ingredient-list li:last-child {
            border-bottom: none;
        }

        .leaf-icon {
            color: var(--primary);
            font-size: 1rem;
        }

        /* Numbered Step Circles */
        .step-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .step-list li {
            display: flex;
            gap: 14px;
            margin-bottom: 16px;
        }

        .step-num {
            background: var(--primary);
            color: white;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.85rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .step-text {
            flex: 1;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        /* General Controls & Badges */
        .badge {
            background: #dcfce7;
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid #86efac;
            display: inline-block;
        }

        .badge-amber {
            background: #fef3c7;
            color: #b45309;
            border-color: #fde68a;
        }

        .badge-blue {
            background: #e0f2fe;
            color: #0369a1;
            border-color: #bae6fd;
        }

        .card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s;
            overflow: hidden;
        }

        .card:hover {
            border-color: var(--primary);
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .card h3 {
            margin-top: 0;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 8px 0;
        }

        .edited-tag {
            color: var(--accent);
            font-style: italic;
            font-size: 0.8rem;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 8px;
            box-sizing: border-box;
            margin-top: 4px;
        }

        button,
        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
        }

        button:hover,
        .btn:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: #64748b;
        }

        .btn-danger {
            background: #dc2626;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-mini {
            padding: 2px 6px;
            font-size: 0.75rem;
            border-radius: 4px;
            line-height: 1;
        }

        .error {
            background: #fef2f2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 1px solid #fecaca;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
        }

        /* Micro Action Pill Buttons for Edit/Delete */
        .action-links {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 8px;
            vertical-align: middle;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            font-size: 0.78rem;
            font-weight: 700;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            line-height: 1.2;
        }

        .btn-action-edit {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .btn-action-edit:hover {
            background: #fde68a;
            color: #78350f;
        }

        .btn-action-delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-action-delete:hover {
            background: #fecaca;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="hero-banner">
            <h1>🌱 Nutri-Sarap Barangay Kitchen</h1>
            <p>Mga paboritong Lutong Bahay — masustansya, matipid, at masarap!</p>
        </div>
        <nav>
            <h2><a href="index.php" style="color: inherit; text-decoration: none;">🍲 Recipe Exchange</a></h2>
            <div>
                <?php if (is_logged_in()): ?>
                    <span>Kain tayo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                    <a href="index.php">Feed</a>
                    <a href="favorites.php">Favorites</a>
                    <a href="recipe_create.php">+ Add Recipe</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>