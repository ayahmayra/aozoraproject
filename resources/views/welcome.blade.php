<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ \App\Models\Organization::first()->name ?? 'Aozora Education' }} - School Management System</title>
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <style>
    /* --- Reset & base --- */
    :root{
      --bg:#0f1724; /* deep navy */
      --card:#0b1220;
      --muted:#94a3b8;
      --accent:#5eead4;
      --glass: rgba(255,255,255,0.03);
      --radius:16px;
      --maxw:1100px;
      --gap:24px;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      background: linear-gradient(180deg, #071025 0%, #071226 45%, #071024 100%);
      color:#e6eef6;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      line-height:1.45;
      padding:32px 16px;
      display:flex;
      justify-content:center;
    }
    .wrap{width:100%;max-width:var(--maxw);}

    /* --- Header --- */
    header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin-bottom:36px;
    }
    .brand{
      display:flex;
      gap:12px;
      align-items:center;
      text-decoration:none;
      color:inherit;
    }
    .logo {
      width:44px;height:44px;border-radius:10px;
      background:linear-gradient(135deg,var(--accent),#60a5fa);
      display:flex;align-items:center;justify-content:center;
      font-weight:700;color:#013;box-shadow:0 6px 18px rgba(0,0,0,0.45);
    }
    nav{display:flex;gap:14px;align-items:center}
    nav a{color:var(--muted);text-decoration:none;font-size:14px;padding:8px 10px;border-radius:8px}
    nav a.cta{
      background:linear-gradient(90deg,#06b6d4,var(--accent));
      color:#013;font-weight:600;
      padding:8px 12px;
      box-shadow:0 6px 14px rgba(5,150,136,0.12);
    }

    /* --- Hero --- */
    .hero{
      background: linear-gradient(180deg, rgba(255,255,255,0.02), transparent);
      padding:40px;
      border-radius:var(--radius);
      display:grid;
      grid-template-columns:1fr 360px;
      gap:var(--gap);
      align-items:start;
      box-shadow: 0 8px 30px rgba(2,6,23,0.6);
      margin-bottom:28px;
    }
    .eyebrow{font-size:13px;color:var(--accent);font-weight:700;margin-bottom:10px}
    h1{font-size:34px;margin:0 0 12px 0;line-height:1.05}
    p.lead{color:var(--muted);margin:0 0 22px 0;max-width:70%}
    .hero .actions{display:flex;gap:12px}
    .btn{
      padding:12px 18px;border-radius:12px;border:0;font-weight:700;cursor:pointer;
      background:transparent;color:var(--accent);border:1px solid rgba(94,234,212,0.16);
      backdrop-filter: blur(4px);
    }
    .btn.primary{
      background:linear-gradient(90deg,#06b6d4,#60a5fa);
      color:#013;border:0;
      box-shadow:0 10px 28px rgba(6,182,212,0.12);
    }
    .hero-card{
      background:linear-gradient(180deg, rgba(255,255,255,0.012), rgba(255,255,255,0.02));
      padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);
    }
    .stat{display:flex;gap:10px;align-items:center;margin-bottom:8px}
    .stat strong{font-size:20px}
    .muted{color:var(--muted);font-size:13px}

    /* --- Features grid --- */
    .features{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:28px}
    .feature{
      background:var(--glass);
      padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);
      min-height:120px;
    }
    .feature h3{margin:0 0 8px 0;font-size:16px}
    .feature p{margin:0;color:var(--muted);font-size:14px}

    /* --- Partners --- */
    .partners{
      background: linear-gradient(180deg, rgba(255,255,255,0.01), transparent);
      padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);
      margin-bottom:28px;
    }
    .partner-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;align-items:center}
    .partner{
      height:44px;border-radius:8px;background:rgba(255,255,255,0.03);
      display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--muted);
      border:1px solid rgba(255,255,255,0.02);
    }

    /* --- CTA / Form --- */
    .subscribe{display:flex;gap:8px;align-items:center;margin-top:12px}
    .input{
      background:transparent;border:1px solid rgba(255,255,255,0.06);padding:10px 12px;border-radius:10px;color:inherit;
    }

    /* --- Footer --- */
    footer{display:flex;justify-content:space-between;align-items:center;gap:12px;color:var(--muted);font-size:13px;margin-top:20px}
    footer a{color:var(--muted);text-decoration:none}

    /* --- Animations --- */
    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.7; transform: scale(1.1); }
    }

    /* --- Responsiveness --- */
    @media (max-width:900px){
      .hero{grid-template-columns:1fr; padding:24px}
      .features{grid-template-columns:repeat(2,1fr)}
      .partner-grid{grid-template-columns:repeat(4,1fr)}
      p.lead{max-width:100%}
    }
    @media (max-width:520px){
      .features{grid-template-columns:1fr}
      .partner-grid{grid-template-columns:repeat(2,1fr)}
      header{flex-direction:column;align-items:flex-start;gap:16px}
      .hero{padding:18px}
      h1{font-size:24px}
    }
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <a class="brand" href="#">
        <div class="logo">
          @php
            $organization = \App\Models\Organization::first();
          @endphp
          {{ substr($organization->name ?? 'A', 0, 1) }}
        </div>
        <div>
          <div style="font-weight:700">{{ $organization->name ?? 'Aozora Education' }}</div>
          <div style="font-size:12px;color:var(--muted);margin-top:3px">{{ $organization->short_name ?? 'School Management System' }}</div>
        </div>
      </a>

      <nav>
        @if (Route::has('login'))
          @auth
            @if(auth()->user()->hasRole('admin'))
              <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            @elseif(auth()->user()->hasRole('parent'))
              <a href="{{ route('parent.dashboard') }}">Parent Dashboard</a>
            @elseif(auth()->user()->hasRole('teacher'))
              <a href="{{ route('teacher.dashboard') }}">Teacher Dashboard</a>
            @elseif(auth()->user()->hasRole('student'))
              <a href="{{ route('student.dashboard') }}">Student Dashboard</a>
            @else
              <a href="{{ url('/dashboard') }}">Dashboard</a>
            @endif
          @else
            <a href="{{ route('login') }}">Login</a>
            @if (Route::has('register'))
              <a class="cta" href="{{ route('register') }}">Register as Parent</a>
            @endif
          @endauth
        @endif
      </nav>
    </header>

    <main>
      <!-- HERO -->
      <section class="hero" aria-label="hero">
        <div>
          <div class="eyebrow">School Management</div>
          <h1>Sistem manajemen sekolah terintegrasi untuk pengalaman belajar yang lebih baik.</h1>
          <p class="lead">Platform komprehensif untuk mengelola siswa, guru, jadwal, absensi, dan komunikasi antara sekolah, orang tua, dan siswa — fokuskan pada pendidikan, bukan administrasi.</p>
          <div class="actions">
            @if (Route::has('login') && !auth()->check())
              <a href="{{ route('login') }}" class="btn primary">Masuk ke Sistem</a>
            @endif
            <a href="#features" class="btn">Pelajari Fitur</a>
          </div>

          <div style="margin-top:18px;color:var(--muted);font-size:13px">
            <strong style="color:var(--accent)">Keunggulan:</strong>
            <span style="margin-left:8px">Real-time — Terintegrasi — Mudah digunakan — Aman</span>
          </div>
        </div>

        <aside class="hero-card" aria-hidden="false">
          <div class="stat"><div style="width:8px;height:8px;background:var(--accent);border-radius:50%"></div><div><strong>Data Terlindungi</strong><div class="muted">Keamanan tingkat enterprise</div></div></div>
          <div class="stat"><div style="width:8px;height:8px;background:#60a5fa;border-radius:50%"></div><div><strong>Pengguna Aktif</strong><div class="muted">Guru, siswa, dan orang tua</div></div></div>
          <div style="height:10px"></div>
          <div style="display:flex;gap:8px;flex-wrap:wrap">
            <div style="padding:8px 10px;border-radius:10px;background:rgba(255,255,255,0.02);font-size:13px">Absensi: real-time tracking</div>
            <div style="padding:8px 10px;border-radius:10px;background:rgba(255,255,255,0.02);font-size:13px">Komunikasi: instant messaging</div>
          </div>
        </aside>
      </section>

      <!-- SHOWCASE -->
      <section style="background: var(--glass); padding: 40px; border-radius: var(--radius); border: 1px solid rgba(255,255,255,0.03); margin-bottom: 28px; text-align: center;">
        <div style="margin-bottom: 24px;">
          <div class="eyebrow" style="margin-bottom: 12px;">Live Demo</div>
          <h2 style="font-size: 28px; margin: 0 0 12px 0; line-height: 1.2;">Lihat Dashboard dalam Aksi</h2>
          <p style="color: var(--muted); margin: 0; max-width: 600px; margin: 0 auto;">Antarmuka yang intuitif dan modern untuk orang tua, guru, dan admin. Kelola semua aspek sekolah dengan mudah dan efisien.</p>
        </div>
        
        <div style="position: relative; max-width: 800px; margin: 0 auto;">
          <div style="background: linear-gradient(135deg, rgba(94,234,212,0.1), rgba(96,165,250,0.1)); padding: 20px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);">
            <div style="background: #1a1a1a; border-radius: 12px; padding: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="width: 12px; height: 12px; background: #ff5f57; border-radius: 50%;"></div>
                <div style="width: 12px; height: 12px; background: #ffbd2e; border-radius: 50%;"></div>
                <div style="width: 12px; height: 12px; background: #28ca42; border-radius: 50%;"></div>
                <div style="flex: 1; text-align: center; color: var(--muted); font-size: 14px;">Parent Dashboard</div>
              </div>
              
              <!-- Screenshot placeholder - replace with actual image -->
              <div style="background: linear-gradient(135deg, #0f1724, #1e293b); border-radius: 8px; padding: 24px; min-height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                
                <!-- Dashboard Preview Content -->
                <div style="position: relative; z-index: 1; width: 100%; max-width: 600px;">
                  <!-- Header -->
                  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding: 16px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                      <div style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%;"></div>
                      <div>
                        <div style="font-size: 18px; font-weight: 700; color: #e6eef6;">Welcome Parent Test 01!</div>
                        <div style="font-size: 12px; color: var(--muted);">Parent Dashboard • Sunday, 05 October 2025</div>
                      </div>
                    </div>
                    <div style="background: rgba(255,255,255,0.1); padding: 8px 12px; border-radius: 20px; font-size: 12px; color: var(--muted);">PT</div>
                  </div>
                  
                  <!-- Stats Cards -->
                  <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px;">
                    <div style="background: linear-gradient(135deg, #1e40af, #3b82f6); padding: 16px; border-radius: 8px; text-align: center;">
                      <div style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 4px;">2</div>
                      <div style="font-size: 12px; color: rgba(255,255,255,0.8);">My Children</div>
                    </div>
                    <div style="background: linear-gradient(135deg, #059669, #10b981); padding: 16px; border-radius: 8px; text-align: center;">
                      <div style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 4px;">0</div>
                      <div style="font-size: 12px; color: rgba(255,255,255,0.8);">Today's Classes</div>
                    </div>
                    <div style="background: linear-gradient(135deg, #7c3aed, #8b5cf6); padding: 16px; border-radius: 8px; text-align: center;">
                      <div style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 4px;">1</div>
                      <div style="font-size: 12px; color: rgba(255,255,255,0.8);">Unpaid Invoices</div>
                    </div>
                    <div style="background: linear-gradient(135deg, #dc2626, #ef4444); padding: 16px; border-radius: 8px; text-align: center;">
                      <div style="font-size: 24px; font-weight: 700; color: white; margin-bottom: 4px;">0</div>
                      <div style="font-size: 12px; color: rgba(255,255,255,0.8);">Messages</div>
                    </div>
                  </div>
                  
                  <!-- Student Card -->
                  <div style="background: linear-gradient(135deg, rgba(94,234,212,0.1), rgba(96,165,250,0.1)); padding: 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                      <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: var(--accent);">ST</div>
                      <div>
                        <div style="font-size: 16px; font-weight: 700; color: #e6eef6;">Student Test 01</div>
                        <div style="font-size: 12px; color: var(--muted);">student2@test.com</div>
                      </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 12px;">
                      <div style="color: var(--muted);">Student ID: <span style="color: #e6eef6;">Not assigned</span></div>
                      <div style="color: var(--muted);">Date of Birth: <span style="color: #e6eef6;">Feb 01, 2002</span></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Floating elements -->
          <div style="position: absolute; top: -10px; right: -10px; width: 20px; height: 20px; background: var(--accent); border-radius: 50%; animation: pulse 2s infinite;"></div>
          <div style="position: absolute; bottom: -10px; left: -10px; width: 16px; height: 16px; background: #60a5fa; border-radius: 50%; animation: pulse 2s infinite 1s;"></div>
        </div>
        
        <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
          <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--muted);">
            <div style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%;"></div>
            <span>Real-time Dashboard</span>
          </div>
          <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--muted);">
            <div style="width: 8px; height: 8px; background: #60a5fa; border-radius: 50%;"></div>
            <span>Mobile Responsive</span>
          </div>
          <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--muted);">
            <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
            <span>Dark Mode Support</span>
          </div>
        </div>
      </section>

      <!-- FEATURES -->
      <section id="features" class="features" aria-label="features">
        <div class="feature">
          <h3>Manajemen Siswa</h3>
          <p>Sistem terintegrasi untuk mengelola data siswa, enrollment, dan progress akademik dengan mudah dan efisien.</p>
        </div>
        <div class="feature">
          <h3>Absensi Digital</h3>
          <p>Tracking kehadiran real-time dengan notifikasi otomatis ke orang tua dan laporan yang dapat diakses kapan saja.</p>
        </div>
        <div class="feature">
          <h3>Komunikasi Terpadu</h3>
          <p>Platform komunikasi antara guru, siswa, dan orang tua dengan fitur messaging, announcement, dan notifikasi.</p>
        </div>
      </section>

      <!-- FEATURES -->
      <section class="partners" aria-label="features">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div style="font-weight:700">Fitur Utama Sistem</div>
          <div class="muted" style="font-size:13px">Semua yang dibutuhkan untuk manajemen sekolah</div>
        </div>

        <div class="partner-grid">
          <div class="partner">Manajemen User</div>
          <div class="partner">Data Siswa</div>
          <div class="partner">Data Guru</div>
          <div class="partner">Data Orang Tua</div>
          <div class="partner">Manajemen Mata Pelajaran</div>
          <div class="partner">Jadwal Kelas</div>
          <div class="partner">Absensi Digital</div>
          <div class="partner">Invoice & Pembayaran</fdiv>
          <div class="partner">Enrollment Siswa</div>
          <div class="partner">Role & Permission</div>
          <div class="partner">Organisasi</div>
          <div class="partner">Notifikasi</div>
        </div>
      </section>

      <!-- SUBSCRIBE -->
      <section style="display:flex;flex-direction:column;background:var(--glass);padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);margin-bottom:18px">
        <div style="font-weight:700">Tetap terupdate</div>
        <div class="muted" style="margin-top:6px">Dapatkan informasi terbaru tentang sistem dan fitur baru.</div>
        <div class="subscribe" role="form" aria-label="subscribe form">
          <input class="input" placeholder="Email untuk notifikasi" aria-label="email" />
          <button class="btn primary">Notify Me</button>
        </div>
      </section>
    </main>

    <footer style="margin-top: 40px; padding: 32px 0; border-top: 1px solid rgba(255,255,255,0.1);">
      <div style="display: flex; flex-direction: column; gap: 16px; align-items: center; text-align: center;">
        <div style="font-size: 14px; color: var(--muted);">
          © {{ date('Y') }} {{ $organization->name ?? 'Aozora Education' }}. All rights reserved.
        </div>
        <div style="display: flex; align-items: center; gap: 24px; flex-wrap: wrap; justify-content: center;">
          <span style="font-size: 14px; color: var(--muted);">
            Developed by <span style="font-weight: 600; color: var(--accent);">hermanspace.id</span>
          </span>
          <div style="display: flex; align-items: center; font-size: 14px; color: var(--muted);">
            <span style="color: #ef4444; margin-right: 4px;">♥</span>
            Made with love
          </div>
        </div>
      </div>
    </footer>
  </div>
</body>
</html>
