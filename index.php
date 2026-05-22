<?php
$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
?>
<section class="hero-panel mb-5">
  <div class="row g-4 align-items-center">
    <div class="col-lg-7">
      <span class="eyebrow">Digital Department Workspace</span>
      <h1 class="hero-title">A sharper, simpler BCA classroom experience.</h1>
      <p class="hero-text">Browse semester-wise study materials, open previous-year papers, and keep academic resources organized in one calm, focused portal.</p>
      <div class="login-cta mb-4">
        <?php if (!$student): ?>
          <div class="d-flex flex-column flex-sm-row gap-3">
            <a href="/bca/login.php" class="btn hero-btn-student btn-lg flex-fill">
              <i class="bi bi-box-arrow-in-right me-2"></i>
              Student Login
            </a>
            <a href="/bca/admin/login.php" class="btn hero-btn-admin btn-lg flex-fill">
              <i class="bi bi-shield-lock me-2"></i>
              Admin Login
            </a>
          </div>
        <?php else: ?>
          <a href="/bca/dashboard.php" class="btn hero-btn-student btn-lg">
            <i class="bi bi-speedometer2 me-2"></i>
            Go to Dashboard
          </a>
        <?php endif; ?>
      </div>
      <div class="hero-metrics">
        <div class="metric-chip"><strong>Semester Filters</strong><span>Find the right content faster</span></div>
        <div class="metric-chip"><strong>Study Library</strong><span>Notes, PPTs, and reference files</span></div>
        <div class="metric-chip"><strong>Exam Prep</strong><span>Past papers by year and subject</span></div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="hero-showcase">
        <div class="showcase-label">Campus-ready workflow</div>
        <h3 class="fw-bold mb-3">Everything students need, without clutter.</h3>
        <div class="showcase-list">
          <div><i class="bi bi-check2-circle"></i> Semester-based access for easier navigation</div>
          <div><i class="bi bi-check2-circle"></i> Subject-level filters for precise results</div>
          <div><i class="bi bi-check2-circle"></i> Quick downloads for materials and papers</div>
        </div>
        <div class="showcase-card">
          <span>Best for</span>
          <strong>Daily study, revision, and exam preparation</strong>
        </div>
        <div class="showcase-orb orb-one"></div>
        <div class="showcase-orb orb-two"></div>
      </div>
    </div>
  </div>
</section>

<section class="mb-5">
  <div class="section-heading">
    <span class="eyebrow">Portal Highlights</span>
    <h2 class="section-title">Built for real student workflows</h2>
  </div>
  <div class="row g-4">
  <div class="col-md-4">
    <div class="feature-tile tile-primary">
      <div class="icon-box mb-3"><i class="bi bi-book"></i></div>
      <h5 class="fw-bold">Study Material</h5>
      <p class="mb-0">Lecture notes, presentations, and reference PDFs grouped by semester and subject.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="feature-tile tile-success">
      <div class="icon-box mb-3" style="color: var(--success); background: rgba(16, 185, 129, 0.08);"><i class="bi bi-file-earmark-text"></i></div>
      <h5 class="fw-bold">Question Papers</h5>
      <p class="mb-0">Previous-year papers arranged for targeted practice before mid-terms and end-terms.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="feature-tile tile-warning">
      <div class="icon-box mb-3" style="color: var(--warning); background: rgba(245, 158, 11, 0.08);"><i class="bi bi-shield-lock"></i></div>
      <h5 class="fw-bold">Secure Access</h5>
      <p class="mb-0">Students log in with their enrollment number and see content relevant to their semester.</p>
    </div>
  </div>
</div>
</section>

<section class="surface-card surface-grid">
  <div>
    <span class="eyebrow">How It Helps</span>
    <h3 class="fw-bold mb-2">A cleaner design for faster academic work</h3>
    <p class="text-muted mb-0">The updated interface reduces clutter, highlights the most-used actions, and makes subject-based browsing easier on mobile and desktop.</p>
  </div>
  <div class="quick-points">
    <div><strong>Focused layout</strong><span>Less noise, stronger visual hierarchy</span></div>
    <div><strong>Readable tables</strong><span>Better spacing for materials and papers</span></div>
    <div><strong>Consistent theme</strong><span>Unified colors, cards, buttons, and sections</span></div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
