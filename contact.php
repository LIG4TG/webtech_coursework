<?php
/**
 * Contact Us Page
 * Includes JavaScript validation and PHP form processing
 * SET08101 Web Technologies Coursework
 */

require_once "includes/db.php";

$pageTitle = "Contact Us";

// PHP form processing
$formSubmitted = false;
$formData = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve and sanitise inputs
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    // Server-side validation (mirrors the JS validation)
    if (empty($name)) {
        $errors["name"] = "Please enter your name.";
    } elseif (strlen($name) < 2) {
        $errors["name"] = "Name must be at least 2 characters.";
    }

    if (empty($email)) {
        $errors["email"] = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Please enter a valid email address.";
    }

    if (empty($subject)) {
        $errors["subject"] = "Please enter a subject.";
    }

    if (empty($message)) {
        $errors["message"] = "Please enter your message.";
    } elseif (strlen($message) < 10) {
        $errors["message"] = "Message must be at least 10 characters.";
    }

    if (empty($errors)) {
        // No errors — form is valid
        $formSubmitted = true;
        $formData = [
            "name" => $name,
            "email" => $email,
            "subject" => $subject,
            "message" => $message,
        ];
    }
}

require_once "includes/header.php";
?>

<link rel="stylesheet" href="css/contact.css">

<div class="contact-wrapper">

    <?php if ($formSubmitted): ?>
        <!-- ── Success message ─────────────────────────────────── -->
        <div class="success-card" role="alert" aria-live="polite">
            <div class="success-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="2"/>
                    <path d="M7 12.5l3.5 3.5 6.5-7" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h2>Message Received</h2>
            <p>Thank you for getting in touch, <strong><?php echo e(
                $formData["name"],
            ); ?></strong>.</p>
            <p>We have received the following query:</p>

            <div class="submission-summary">
                <dl>
                    <div class="summary-row">
                        <dt>Name</dt>
                        <dd><?php echo e($formData["name"]); ?></dd>
                    </div>
                    <div class="summary-row">
                        <dt>Email</dt>
                        <dd><?php echo e($formData["email"]); ?></dd>
                    </div>
                    <div class="summary-row">
                        <dt>Subject</dt>
                        <dd><?php echo e($formData["subject"]); ?></dd>
                    </div>
                    <div class="summary-row">
                        <dt>Message</dt>
                        <dd><?php echo nl2br(e($formData["message"])); ?></dd>
                    </div>
                </dl>
            </div>

            <a href="contact.php" class="btn btn-outlined">Send another message</a>
        </div>

    <?php else: ?>
        <!-- ── Contact form ────────────────────────────────────── -->
        <div class="contact-header">
            <h2>Contact Us</h2>
            <p>Have a question about hydrothermal vents or the database? We'd love to hear from you.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-banner" role="alert">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="2"
                          stroke-linecap="round"/>
                </svg>
                <span>Please correct the errors below before submitting.</span>
            </div>
        <?php endif; ?>

        <form id="contactForm" method="POST" action="contact.php" novalidate>

            <div class="form-grid">

                <!-- Name -->
                <div class="field-group <?php echo isset($errors["name"])
                    ? "has-error"
                    : ""; ?>">
                    <label for="name">Full Name <span class="required" aria-hidden="true">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php echo e($_POST["name"] ?? ""); ?>"
                        autocomplete="name"
                        aria-required="true"
                        aria-describedby="name-error"
                        placeholder="e.g. Jane Smith"
                    >
                    <span class="field-error" id="name-error" role="alert">
                        <?php echo isset($errors["name"])
                            ? e($errors["name"])
                            : ""; ?>
                    </span>
                </div>

                <!-- Email -->
                <div class="field-group <?php echo isset($errors["email"])
                    ? "has-error"
                    : ""; ?>">
                    <label for="email">Email Address <span class="required" aria-hidden="true">*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo e($_POST["email"] ?? ""); ?>"
                        autocomplete="email"
                        aria-required="true"
                        aria-describedby="email-error"
                        placeholder="e.g. jane@example.com"
                    >
                    <span class="field-error" id="email-error" role="alert">
                        <?php echo isset($errors["email"])
                            ? e($errors["email"])
                            : ""; ?>
                    </span>
                </div>

            </div><!-- /.form-grid -->

            <!-- Subject -->
            <div class="field-group <?php echo isset($errors["subject"])
                ? "has-error"
                : ""; ?>">
                <label for="subject">Subject <span class="required" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="<?php echo e($_POST["subject"] ?? ""); ?>"
                    aria-required="true"
                    aria-describedby="subject-error"
                    placeholder="What is your query about?"
                >
                <span class="field-error" id="subject-error" role="alert">
                    <?php echo isset($errors["subject"])
                        ? e($errors["subject"])
                        : ""; ?>
                </span>
            </div>

            <!-- Message -->
            <div class="field-group <?php echo isset($errors["message"])
                ? "has-error"
                : ""; ?>">
                <label for="message">Message <span class="required" aria-hidden="true">*</span></label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    aria-required="true"
                    aria-describedby="message-error"
                    placeholder="Write your message here..."
                ><?php echo e($_POST["message"] ?? ""); ?></textarea>
                <span class="field-error" id="message-error" role="alert">
                    <?php echo isset($errors["message"])
                        ? e($errors["message"])
                        : ""; ?>
                </span>
            </div>

            <div class="form-footer">
                <p class="required-note"><span class="required">*</span> Required fields</p>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="btn-text">Send Message</span>
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"
                              stroke="currentColor" stroke-width="2"
                              stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

        </form>

    <?php endif; ?>

</div><!-- /.contact-wrapper -->

<script src="js/contact.js"></script>

<?php require_once "includes/footer.php"; ?>
