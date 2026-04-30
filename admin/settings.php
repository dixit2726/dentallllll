<?php
$pageTitle = "Account Settings";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/topbar.php';

$msg_type = '';
$msg      = '';

// ── Handle password change ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current  = $_POST['current_password'];
    $new_pw   = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    if (strlen($new_pw) < 8) {
        $msg = "New password must be at least 8 characters.";
        $msg_type = 'error';
    } elseif ($new_pw !== $confirm) {
        $msg = "New passwords do not match.";
        $msg_type = 'error';
    } else {
        $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (password_verify($current, $user['password'])) {
            $hash = password_hash($new_pw, PASSWORD_DEFAULT);
            $upd  = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $upd->bind_param("si", $hash, $_SESSION['admin_id']);
            if ($upd->execute()) {
                logActivity($conn, $_SESSION['admin_id'], "Password Change", "Admin changed their password");
                $msg = "Password updated successfully!";
                $msg_type = 'success';
            }
        } else {
            $msg = "Current password is incorrect.";
            $msg_type = 'error';
        }
    }
}

$admin = $conn->query("SELECT * FROM admins WHERE id=" . (int)$_SESSION['admin_id'])->fetch_assoc();
?>

<div class="content-wrapper">

    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Account Settings</h1>
            <p class="page-subtitle">Manage your profile, security, and session preferences.</p>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="dashboard-grid">

        <!-- ── Profile Card ── -->
        <div class="card">
            <h2 class="card-title" style="margin-bottom:24px;"><i class="fas fa-id-card" style="color:var(--primary);margin-right:8px;"></i>Admin Profile</h2>

            <div style="display:flex;align-items:center;gap:20px;margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid var(--border);">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['full_name']); ?>&size=128&background=2563eb&color=fff&bold=true"
                     alt="Avatar"
                     style="width:80px;height:80px;border-radius:50%;border:4px solid var(--primary-light);">
                <div>
                    <div style="font-size:1.15rem;font-weight:800;"><?php echo htmlspecialchars($admin['full_name']); ?></div>
                    <div style="color:var(--text-muted);font-size:0.85rem;margin-top:4px;"><?php echo ucfirst(str_replace('_',' ',$admin['role'])); ?></div>
                    <span class="badge badge-booked" style="margin-top:8px;">Active Session</span>
                </div>
            </div>

            <div style="display:grid;gap:16px;">
                <div>
                    <div class="text-muted text-sm">Username</div>
                    <div class="fw-600"><?php echo htmlspecialchars($admin['username']); ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm">Email Address</div>
                    <div class="fw-600"><?php echo htmlspecialchars($admin['email']); ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm">Member Since</div>
                    <div class="fw-600"><?php echo date('F j, Y', strtotime($admin['created_at'])); ?></div>
                </div>
                <div>
                    <div class="text-muted text-sm">Last Login</div>
                    <div class="fw-600"><?php echo $admin['last_login'] ? date('M j, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></div>
                </div>
            </div>

            <!-- Session Info -->
            <div style="margin-top:24px;padding:16px;background:var(--bg-main);border-radius:var(--radius-sm);">
                <div class="text-muted text-sm" style="margin-bottom:6px;">Current Session</div>
                <div class="fw-600">IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?></div>
                <div class="text-muted text-sm" style="margin-top:4px;">Started: <?php echo date('M j, Y H:i'); ?></div>
            </div>

            <a href="logout.php" class="btn btn-danger" style="width:100%;margin-top:20px;">
                <i class="fas fa-right-from-bracket"></i> Sign Out
            </a>
        </div>

        <!-- ── Change Password ── -->
        <div class="card">
            <h2 class="card-title" style="margin-bottom:24px;"><i class="fas fa-lock" style="color:var(--secondary);margin-right:8px;"></i>Change Password</h2>

            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <div style="position:relative;">
                        <input type="password" name="current_password" class="form-control"
                               id="curPw" placeholder="Enter current password" required>
                        <button type="button" onclick="togglePw('curPw',this)"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <div style="position:relative;">
                        <input type="password" name="new_password" class="form-control"
                               id="newPw" placeholder="Minimum 8 characters" required
                               oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePw('newPw',this)"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <!-- Strength bar -->
                    <div style="height:4px;border-radius:4px;background:var(--border);margin-top:8px;overflow:hidden;">
                        <div id="strengthBar" style="height:100%;width:0;border-radius:4px;transition:width .3s,background .3s;"></div>
                    </div>
                    <div id="strengthLabel" style="font-size:0.72rem;margin-top:4px;color:var(--text-muted);"></div>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control"
                           placeholder="Repeat new password" required>
                </div>

                <!-- Password requirements -->
                <div style="background:var(--bg-main);border-radius:var(--radius-sm);padding:14px;margin-bottom:20px;">
                    <div class="text-muted text-sm fw-600" style="margin-bottom:8px;">Requirements:</div>
                    <ul style="list-style:none;display:grid;gap:4px;">
                        <li class="text-sm" id="req-len" style="color:#cbd5e1;"><i class="fas fa-circle-xmark"></i> At least 8 characters</li>
                        <li class="text-sm" id="req-up"  style="color:#cbd5e1;"><i class="fas fa-circle-xmark"></i> One uppercase letter</li>
                        <li class="text-sm" id="req-num" style="color:#cbd5e1;"><i class="fas fa-circle-xmark"></i> One number</li>
                        <li class="text-sm" id="req-sym" style="color:#cbd5e1;"><i class="fas fa-circle-xmark"></i> One special character</li>
                    </ul>
                </div>

                <button type="submit" name="change_password" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-key"></i> Update Password
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    const ico = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'far fa-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'far fa-eye';
    }
}

function checkStrength(val) {
    const bar    = document.getElementById('strengthBar');
    const label  = document.getElementById('strengthLabel');
    let score    = 0;
    const checks = {
        'req-len': val.length >= 8,
        'req-up':  /[A-Z]/.test(val),
        'req-num': /[0-9]/.test(val),
        'req-sym': /[^A-Za-z0-9]/.test(val)
    };
    for (const [id, ok] of Object.entries(checks)) {
        const el = document.getElementById(id);
        el.style.color = ok ? 'var(--secondary)' : '#cbd5e1';
        el.querySelector('i').className = ok ? 'fas fa-circle-check' : 'fas fa-circle-xmark';
        if (ok) score++;
    }
    const levels = [
        { w:'0%',   c:'transparent', t:'' },
        { w:'25%',  c:'#ef4444',     t:'Weak' },
        { w:'50%',  c:'#f59e0b',     t:'Fair' },
        { w:'75%',  c:'#3b82f6',     t:'Good' },
        { w:'100%', c:'#10b981',     t:'Strong' }
    ];
    bar.style.width      = levels[score].w;
    bar.style.background = levels[score].c;
    label.textContent    = levels[score].t;
    label.style.color    = levels[score].c;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
