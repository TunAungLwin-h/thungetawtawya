<?php
// admin/registration_view.php
session_start();
require_once __DIR__ . '/../configuration/db.php';
require_once __DIR__ . '/auth/admin_guard.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "Invalid ID";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$registration = $stmt->fetch();

if (!$registration) {
    echo "Registration not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration #<?php echo $registration['id']; ?> — Admin Dashboard</title>
    <link rel="stylesheet" href="../admin/assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .registration-details {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .detail-section {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detail-section h3 {
            color: #9c6644;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f1f1f1;
            font-size: 1.25rem;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .detail-label {
            flex: 0 0 180px;
            font-weight: 600;
            color: #555;
        }
        
        .detail-value {
            flex: 1;
            color: #333;
        }
        
        .detail-value .empty {
            color: #999;
            font-style: italic;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .header-actions h1 {
            color: #9c6644;
            margin: 0;
            font-size: 1.75rem;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        @media (max-width: 768px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .detail-row {
                flex-direction: column;
            }
            
            .detail-label {
                margin-bottom: 0.5rem;
            }
            
            .header-actions {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php  include('header.php'); ?>

    
    <?php include('sidebar.php'); ?>

    <main class="main-content">
        <div class="registration-details">
            <div class="header-actions">
                <h1>Registration Details</h1>
                <div>
                    <span class="badge badge-info">ID: #<?php echo $registration['id']; ?></span>
                    <?php if ($registration['roll_number']): ?>
                        <span class="badge badge-success">Roll: <?php echo htmlspecialchars($registration['roll_number']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="detail-grid">
                <!-- Personal Information Section -->
                <div class="detail-section">
                    <h3><i class="fa-solid fa-user-circle"></i> Personal Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['name']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label"> Email</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['email']);?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Date of Birth</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['dob']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Roll Number</div>
                        <div class="detail-value">
                            <?php echo $registration['roll_number'] ? htmlspecialchars($registration['roll_number']) : '<span class="empty">Not assigned</span>'; ?>
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="detail-section">
                    <h3><i class="fa-solid fa-location-dot"></i> Address Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Region</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['address_region']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Township</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['address_township']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Village/Ward</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['address_village']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Monastery Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['monastery_name']); ?></div>

                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Sayadaw Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['abbot_name']); ?></div>

                    </div>
                    
                </div>

                <!-- Parent Information Section -->
                <div class="detail-section">
                    <h3><i class="fa-solid fa-users"></i> Parent Information</h3>
                    <div class="detail-row">
                        <div class="detail-label">Father's Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['father_name']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Mother's Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($registration['mother_name']); ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Parent's Address</div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($registration['parent_address_region']) . ' / ' . htmlspecialchars($registration['parent_address_village']); ?>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="detail-section">
                    <h3><i class="fa-solid fa-info-circle"></i> Additional Details</h3>
                    <div class="detail-row">
                        <div class="detail-label">Candidates ID</div>
                        <div class="detail-value"><?php echo $registration['id']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <?php echo $registration['roll_number'] ? '<span style="color:#28a745;"><i class="fa-solid fa-check-circle"></i> Roll Assigned</span>' : '<span style="color:#6c757d;"><i class="fa-solid fa-clock"></i> Pending Roll</span>'; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0;">
                <div>
                    <a href="registrations.php" class="btn btn-ghost">
                        <i class="fa-solid fa-list"></i> View All Registrations
                    </a>
                </div>
                <div>
                    <span class="muted">Last updated: <?php echo date('Y-m-d H:i'); ?></span>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Add some interactive enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add print functionality if needed
            const printButton = document.createElement('button');
            printButton.className = 'btn btn-ghost';
            printButton.innerHTML = '<i class="fa-solid fa-print"></i> Print Details';
            printButton.onclick = () => window.print();
            
            const actionsDiv = document.querySelector('.header-actions');
            actionsDiv.insertBefore(printButton, actionsDiv.children[1]);
        });
    </script>
</body>

</html>