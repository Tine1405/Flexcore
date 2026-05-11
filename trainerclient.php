<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FLEXCORE | Trainer Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #a855f7;
            --primary-dark: #9333ea;
            --bg-dark: #09090b;
            --card-bg: rgba(24, 24, 27, 0.9);
            --text-main: #fafafa;
            --text-dim: #a1a1aa;
            --sidebar-width: 260px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background: radial-gradient(circle at 0% 0%, #1e1b4b 0%, #09090b 50%);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(9, 9, 11, 0.95);
            border-right: 1px solid rgba(255,255,255,0.05);
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .logo { 
            font-weight: 800; font-size: 24px; color: var(--primary); 
            letter-spacing: 2px; margin-bottom: 50px; 
            text-shadow: 0 0 15px rgba(168, 85, 247, 0.4);
        }

        .nav-menu { list-style: none; flex-grow: 1; }
        .nav-item {
            padding: 14px 18px;
            margin-bottom: 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-dim);
            text-decoration: none;
            display: block;
            font-weight: 600;
            font-size: 14px;
        }
        .nav-item.active, .nav-item:hover {
            background: rgba(168, 85, 247, 0.1);
            color: var(--primary);
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 40px;
            max-width: 1400px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .trainer-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.05);
            padding: 8px 16px;
            border-radius: 30px;
        }

        .avatar-circle {
            width: 35px; height: 35px; border-radius: 50%;
            background: var(--primary); display: flex; align-items: center;
            justify-content: center; font-weight: bold; font-size: 14px;
        }

        /* --- Stats --- */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: var(--transition);
        }
        .stat-card:hover { border-color: var(--primary); }
        .stat-card h4 { color: var(--text-dim); font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card p { font-size: 32px; font-weight: 800; color: #fff; margin-top: 5px; }

        /* --- Layout Grids --- */
        .dashboard-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }

        .content-box {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 30px;
        }

        .content-box h3 { margin-bottom: 25px; font-size: 20px; font-weight: 800; }

        /* --- Client Cards --- */
        .client-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .client-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 20px;
            padding: 20px;
            transition: var(--transition);
        }
        .client-card:hover { transform: translateY(-5px); border-color: var(--primary); }

        .client-header {
            display: flex; gap: 15px; align-items: center; margin-bottom: 20px;
        }
        .client-img {
            width: 60px; height: 60px; border-radius: 12px; object-fit: cover;
            border: 2px solid var(--primary);
        }
        .client-info h4 { font-size: 18px; }
        .client-info span { font-size: 12px; color: var(--primary); font-weight: 700; }

        /* --- Announcement Area --- */
        .announcement-box {
            background: rgba(168, 85, 247, 0.05);
            border: 1px dashed var(--primary);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .announcement-box label {
            display: block; font-size: 10px; color: var(--text-dim);
            text-transform: uppercase; margin-bottom: 8px; font-weight: 800;
        }
        .announcement-box p { font-size: 13px; line-height: 1.4; color: #eee; font-style: italic; }

        .update-btn {
            width: 100%; padding: 10px; background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1); color: #fff;
            border-radius: 8px; font-size: 12px; cursor: pointer; transition: 0.2s;
        }
        .update-btn:hover { background: var(--primary); }

        /* --- Calendar Styling --- */
        #calendar { font-size: 13px; color: #fff; }
        .fc-toolbar-title { font-size: 16px !important; font-weight: 800 !important; }
        .fc .fc-button-primary { background: var(--primary); border: none; font-size: 12px; }
        .fc-daygrid-day:hover { background: rgba(255,255,255,0.02); }

        @media (max-width: 1100px) {
            .dashboard-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">FLEXCORE</div>
        <nav class="nav-menu">
            <a href="#" class="nav-item active">Dashboard Overview</a>
            <a href="#" class="nav-item">Client Management</a>
            <a href="#" class="nav-item">Training Schedule</a>
            <a href="#" class="nav-item">Workout Library</a>
            <a href="#" class="nav-item">Revenue Reports</a>
        </nav>
        <a href="logout.php" class="nav-item" style="margin-top: auto; color: #ef4444;">Logout</a>
    </aside>

    <main class="main-content">
        <header>
            <div>
                <h1 style="font-size: 32px;">Trainer Portal</h1>
                <p style="color: var(--text-dim)">Welcome back, Coach Sarah. You have 4 sessions today.</p>
            </div>
            <div class="trainer-profile">
                <span>Sarah Miller</span>
                <div class="avatar-circle">SM</div>
            </div>
        </header>

        <section class="stat-grid">
            <div class="stat-card">
                <h4>Total Active Clients</h4>
                <p>18</p>
            </div>
            <div class="stat-card">
                <h4>Sessions Completed</h4>
                <p>142</p>
            </div>
            <div class="stat-card">
                <h4>Pending Inquiries</h4>
                <p>5</p>
            </div>
        </section>

        <div class="dashboard-layout">
            <div class="left-col">
                <div class="content-box">
                    <h3>My Clients</h3>
                    <div class="client-grid">
                        
                        <div class="client-card">
                            <div class="client-header">
                                <img src="https://images.unsplash.com/photo-1594381898411-846e7d193883?w=100&h=100&fit=crop" class="client-img" alt="User">
                                <div class="client-info">
                                    <h4>James Dean</h4>
                                    <span>Hypertrophy Plan</span>
                                </div>
                            </div>
                            <div class="announcement-box">
                                <label>📢 Today's Instruction</label>
                                <p>"Focus on slow eccentrics for the chest press. 4 sets of 12 reps. Stay hydrated!"</p>
                            </div>
                            <button class="update-btn">Update Directive</button>
                        </div>

                        <div class="client-card">
                            <div class="client-header">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop" class="client-img" alt="User">
                                <div class="client-info">
                                    <h4>Anna Karl</h4>
                                    <span>Fat Loss / HIIT</span>
                                </div>
                            </div>
                            <div class="announcement-box" style="border-color: #f59e0b; background: rgba(245, 158, 11, 0.05);">
                                <label style="color: #f59e0b;">📢 Nutrition Note</label>
                                <p>"Increase fiber intake this week. Aim for 3 liters of water minimum. Let's crush cardio!"</p>
                            </div>
                            <button class="update-btn">Update Directive</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="content-box">
                    <h3>Upcoming Sessions</h3>
                    <div id="calendar"></div>
                </div>
                
                <div class="content-box" style="background: linear-gradient(135deg, var(--primary-dark), #4f46e5); border: none;">
                    <h3 style="color: white; margin-bottom: 10px;">Trainer Tip</h3>
                    <p style="color: rgba(255,255,255,0.8); font-size: 14px;">Consistency in tracking client PRs leads to a 40% higher retention rate. Update those logs!</p>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'today'
                },
                events: [
                    { title: 'James: Chest Day', start: '2026-04-19T08:00:00', color: '#a855f7' },
                    { title: 'Anna: Cardio', start: '2026-04-19T10:00:00', color: '#a855f7' },
                    { title: 'Mike: Legs', start: '2026-04-20T09:00:00', color: '#a855f7' }
                ],
                height: 'auto'
            });
            calendar.render();
        });
    </script>
</body>
</html>