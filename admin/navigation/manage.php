<?php
// Include database connection and auth check
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Ensure user is an admin
if (!isAdmin()) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: /admin/index.php');
    exit;
}

// Check if the navigation table exists, create it if not
try {
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'navigation'");
    if ($tableCheck->rowCount() == 0) {
        // Table doesn't exist, create it
        $createTable = "CREATE TABLE IF NOT EXISTS `navigation` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(50) NOT NULL,
            `url` varchar(255) NOT NULL,
            `target` varchar(10) DEFAULT NULL,
            `icon` varchar(50) DEFAULT NULL,
            `parent_id` int(11) DEFAULT NULL,
            `page_id` int(11) DEFAULT NULL,
            `display_order` int(11) NOT NULL DEFAULT 0,
            `location` enum('header','footer','both') NOT NULL DEFAULT 'header',
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `parent_id` (`parent_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $pdo->exec($createTable);
        
        // Insert default items
        $insertDefaults = "INSERT INTO `navigation` (`title`, `url`, `target`, `icon`, `parent_id`, `display_order`, `location`, `is_active`) VALUES
            ('Home', 'index.php', NULL, NULL, NULL, 1, 'both', 1),
            ('Blog', 'blog.php', NULL, NULL, NULL, 2, 'both', 1),
            ('About', 'about.php', NULL, NULL, NULL, 3, 'both', 1),
            ('Contact', 'contact.php', NULL, NULL, NULL, 4, 'both', 1);";
        $pdo->exec($insertDefaults);
        
        // Check if whatnot username is set
        $whatnot_username = getSetting('whatnot_username');
        if ($whatnot_username) {
            $insertWhatnot = "INSERT INTO `navigation` (`title`, `url`, `target`, `icon`, `parent_id`, `display_order`, `location`, `is_active`) VALUES
                ('Whatnot', 'https://www.whatnot.com/user/$whatnot_username', '_blank', 'fas fa-video', NULL, 99, 'header', 1);";
            $pdo->exec($insertWhatnot);
        }
    } else {
        // Table exists, check if location column exists
        $columnCheck = $pdo->query("SHOW COLUMNS FROM navigation LIKE 'location'");
        if ($columnCheck->rowCount() == 0) {
            // Add location column
            $pdo->exec("ALTER TABLE navigation ADD COLUMN `location` enum('header','footer','both') NOT NULL DEFAULT 'header' AFTER `display_order`");
            // Update existing rows to have 'both' location
            $pdo->exec("UPDATE navigation SET location = 'both' WHERE id > 0");
        }
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
}

// Process form submission for adding/editing/deleting navigation items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Add/Edit
    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $title = trim($_POST['title']);
        $url = trim($_POST['url']);
        $target = !empty($_POST['target']) ? trim($_POST['target']) : null;
        $icon = !empty($_POST['icon']) ? trim($_POST['icon']) : null;
        $display_order = (int)$_POST['display_order'];
        $location = $_POST['location'] ?? 'header';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validate
        if (empty($title) || empty($url)) {
            $_SESSION['error_message'] = 'Title and URL are required.';
        } else {
            try {
                if ($id) {
                    // Update existing item
                    $query = "UPDATE navigation SET 
                              title = :title, 
                              url = :url, 
                              target = :target, 
                              icon = :icon, 
                              display_order = :display_order,
                              location = :location, 
                              is_active = :is_active 
                              WHERE id = :id";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':id', $id);
                } else {
                    // Add new item
                    $query = "INSERT INTO navigation 
                              (title, url, target, icon, display_order, location, is_active) 
                              VALUES 
                              (:title, :url, :target, :icon, :display_order, :location, :is_active)";
                    $stmt = $pdo->prepare($query);
                }
                
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':url', $url);
                $stmt->bindParam(':target', $target);
                $stmt->bindParam(':icon', $icon);
                $stmt->bindParam(':display_order', $display_order);
                $stmt->bindParam(':location', $location);
                $stmt->bindParam(':is_active', $is_active);
                $stmt->execute();
                
                $_SESSION['success_message'] = 'Navigation item ' . ($id ? 'updated' : 'added') . ' successfully!';
                header('Location: manage.php');
                exit;
            } catch (PDOException $e) {
                $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    // Handle Delete
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        try {
            $query = "DELETE FROM navigation WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $_SESSION['success_message'] = 'Navigation item deleted successfully!';
            header('Location: manage.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get all navigation items
try {
    $query = "SELECT * FROM navigation ORDER BY display_order ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $navigation_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $navigation_items = [];
    $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
}

// Page variables
$page_title = 'Manage Navigation';

// Include admin header
include_once '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <!-- Navigation Items List -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Navigation Items</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#navItemModal">
                    <i class="fas fa-plus me-1"></i> Add New Item
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($navigation_items)): ?>
                    <div class="alert alert-info">
                        No navigation items found. Add some using the button above.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Order</th>
                                    <th>Title</th>
                                    <th>URL</th>
                                    <th style="width: 80px;">Target</th>
                                    <th style="width: 80px;">Icon</th>
                                    <th style="width: 100px;">Location</th>
                                    <th style="width: 80px;">Status</th>
                                    <th style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($navigation_items as $item): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $item['display_order']; ?></td>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo htmlspecialchars($item['url']); ?></td>
                                        <td class="text-center"><?php echo $item['target'] ? htmlspecialchars($item['target']) : '-'; ?></td>
                                        <td class="text-center">
                                            <?php if ($item['icon']): ?>
                                                <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($item['location'] === 'header'): ?>
                                                <span class="badge bg-primary">Header</span>
                                            <?php elseif ($item['location'] === 'footer'): ?>
                                                <span class="badge bg-secondary">Footer</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Both</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?php echo $item['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $item['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-btn" 
                                                    data-id="<?php echo $item['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                                    data-url="<?php echo htmlspecialchars($item['url']); ?>"
                                                    data-target="<?php echo htmlspecialchars($item['target'] ?? ''); ?>"
                                                    data-icon="<?php echo htmlspecialchars($item['icon'] ?? ''); ?>"
                                                    data-location="<?php echo htmlspecialchars($item['location'] ?? 'header'); ?>"
                                                    data-order="<?php echo $item['display_order']; ?>"
                                                    data-active="<?php echo $item['is_active']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#navItemModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form class="d-inline-block" method="post" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Help Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Navigation Help</h6>
            </div>
            <div class="card-body">
                <h5>About Navigation Items</h5>
                <p>Navigation items are the links shown in your main website navigation bar and footer. Here's how to manage them:</p>
                
                <h6>Fields Explained:</h6>
                <ul>
                    <li><strong>Title:</strong> The text that appears in the navigation</li>
                    <li><strong>URL:</strong> Where the link points to. Use relative URLs like "about.php" for internal pages</li>
                    <li><strong>Target:</strong> Use "_blank" to open in a new tab, or leave empty for same tab</li>
                    <li><strong>Location:</strong> Choose where the link appears - header, footer, or both</li>
                    <li><strong>Icon:</strong> FontAwesome icon class, e.g., "fas fa-home"</li>
                    <li><strong>Order:</strong> Lower numbers appear first</li>
                    <li><strong>Status:</strong> Toggle to show/hide the link</li>
                </ul>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Changes take effect immediately on your website.
                </div>
            </div>
        </div>
        
        <!-- Icon Reference Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Icon Reference</h6>
            </div>
            <div class="card-body">
                <p>Here are some common Font Awesome icons you can use:</p>
                
                <div class="row">
                    <div class="col-6 mb-2">
                        <i class="fas fa-home me-2"></i> fas fa-home
                    </div>
                    <div class="col-6 mb-2">
                        <i class="fas fa-blog me-2"></i> fas fa-blog
                    </div>
                    <div class="col-6 mb-2">
                        <i class="fas fa-info-circle me-2"></i> fas fa-info-circle
                    </div>
                    <div class="col-6 mb-2">
                        <i class="fas fa-envelope me-2"></i> fas fa-envelope
                    </div>
                    <div class="col-6 mb-2">
                        <i class="fas fa-video me-2"></i> fas fa-video
                    </div>
                    <div class="col-6 mb-2">
                        <i class="fas fa-question-circle me-2"></i> fas fa-question-circle
                    </div>
                    <div class="col-6 mb-2">
                        <i class="fas fa-shopping-cart me-2"></i> fas fa-shopping-cart
                    </div>
                    <div class="col-6 mb-2">
                        <i class="fas fa-user me-2"></i> fas fa-user
                    </div>
                </div>
                
                <p class="mt-2 mb-0">
                    <a href="https://fontawesome.com/icons" target="_blank" class="text-decoration-none">
                        <i class="fas fa-external-link-alt me-1"></i> Browse all FontAwesome icons
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Item Modal -->
<div class="modal fade" id="navItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Navigation Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="item_id">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="url" class="form-label">URL *</label>
                        <input type="text" class="form-control" id="url" name="url" required>
                        <div class="form-text">Use relative URLs like "about.php" for internal pages</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="target" class="form-label">Target</label>
                            <select class="form-select" id="target" name="target">
                                <option value="">Same Window</option>
                                <option value="_blank">New Tab (_blank)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="display_order" name="display_order" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <select class="form-select" id="location" name="location">
                            <option value="header">Header Only</option>
                            <option value="footer">Footer Only</option>
                            <option value="both">Both Header & Footer</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon (FontAwesome class)</label>
                        <input type="text" class="form-control" id="icon" name="icon" placeholder="e.g., fas fa-home">
                        <div class="form-text">Leave blank for no icon</div>
                        <div id="icon-preview" class="mt-2"></div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit button clicks
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update modal title
            document.getElementById('modalTitle').textContent = 'Edit Navigation Item';
            
            // Fill the form with the item's data
            document.getElementById('item_id').value = this.dataset.id;
            document.getElementById('title').value = this.dataset.title;
            document.getElementById('url').value = this.dataset.url;
            document.getElementById('target').value = this.dataset.target;
            document.getElementById('icon').value = this.dataset.icon;
            document.getElementById('location').value = this.dataset.location;
            document.getElementById('display_order').value = this.dataset.order;
            document.getElementById('is_active').checked = this.dataset.active === '1';
            
            // Update icon preview
            updateIconPreview(this.dataset.icon);
        });
    });
    
    // Handle modal reset when adding a new item
    const addButton = document.querySelector('[data-bs-target="#navItemModal"]:not(.edit-btn)');
    if (addButton) {
        addButton.addEventListener('click', function() {
            // Update modal title
            document.getElementById('modalTitle').textContent = 'Add Navigation Item';
            
            // Reset the form
            document.getElementById('item_id').value = '';
            document.getElementById('title').value = '';
            document.getElementById('url').value = '';
            document.getElementById('target').value = '';
            document.getElementById('icon').value = '';
            document.getElementById('location').value = 'header';
            document.getElementById('display_order').value = '0';
            document.getElementById('is_active').checked = true;
            
            // Clear icon preview
            const preview = document.getElementById('icon-preview');
            if (preview) {
                preview.innerHTML = '';
            }
        });
    }
    
    // Preview icon on input change
    const iconInput = document.getElementById('icon');
    if (iconInput) {
        iconInput.addEventListener('input', function() {
            updateIconPreview(this.value);
        });
    }
    
    // Function to update icon preview
    function updateIconPreview(iconClass) {
        const preview = document.getElementById('icon-preview');
        if (!preview) return;
        
        if (iconClass && iconClass.trim()) {
            preview.innerHTML = `<span class="me-2">Preview:</span><i class="${iconClass.trim()}"></i>`;
        } else {
            preview.innerHTML = '';
        }
    }
});
</script>

<?php
// Include admin footer
include_once '../includes/footer.php';
?>