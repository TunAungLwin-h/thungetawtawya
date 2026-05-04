<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Thungetaw Tawya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    :root {
        --monastery-brown: #a67c52;
        --forest-green: #2d4a22;
        --bg-cream: #fdfaf7;
        --text-dark: #332f2b;
    }

    body {
        font-family: 'Segoe UI', serif;
        background: var(--bg-cream);
        color: var(--text-dark);
        margin: 0;
    }

    .container {
        max-width: 1100px;
        margin: 50px auto;
        padding: 20px;
    }

    /* Layout Swapped: Form Left, Info Right */
    .contact-wrapper {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        /* Adjusted for form priority */
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    /* Right Side: Contact Info (Now on the right) */
    .contact-info {
        background: var(--monastery-brown);
        color: white;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .contact-info h2 {
        margin-top: 0;
        font-size: 28px;
    }

    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }

    .info-item i {
        font-size: 20px;
        margin-right: 15px;
        width: 30px;
    }

    /* Left Side: Form (Now on the left) */
    .contact-form {
        padding: 40px;
        background: #fff;
    }

    .contact-form h2 {
        color: var(--forest-green);
        margin-top: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box;
        font-family: inherit;
    }

    .btn-submit {
        background: var(--forest-green);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background: var(--monastery-brown);
    }

    /* Map Section */
    .map-container {
        margin-top: 40px;
        border-radius: 15px;
        overflow: hidden;
        height: 350px;
        border: 5px solid white;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .padding {
        height: 20px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .contact-wrapper {
            grid-template-columns: 1fr;
        }

        .contact-info {
            order: 2;
            /* Move info below form on mobile */
        }
    }
    </style>
</head>

<body>
    <div class="padding"></div>
    <?php include('navbar.php'); ?>

    <div class="container">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> Sadhu! Your message has been sent to the monastery.
        </div>
        <?php endif; ?>

        <div class="contact-wrapper">
            <div class="contact-form">
                <h2>Send a Message</h2>
                <form action="contact_process.php" method="POST">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="sender_name" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="sender_email" required placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label>Inquiry Type</label>
                        <select name="category">
                            <option value="Inquiry">General Inquiry</option>
                            <option value="Donation">Donation/Sponsorship</option>
                            <option value="Visit">Plan a Visit</option>
                            <option value="Complaint">Feedback/Complaint</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message_body" rows="5" required placeholder="How can we help you?"></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Submit Message</button>
                </form>
            </div>

            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>We welcome your inquiries about meditation retreats, donations, or general information.</p>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span>+95 9 123 456 789</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span>info@thungetawtawya.com</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-location-dot"></i>
                    <span>Thungetaw Forest, Mandalay Region, Myanmar</span>
                </div>

                <div style="margin-top: 20px;">
                    <a href="#" style="color:white; margin-right:15px;"><i class="fab fa-facebook fa-2xl"></i></a>
                    <a href="#" style="color:white;"><i class="fab fa-telegram fa-2xl"></i></a>
                </div>
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3704.4426050175753!2d95.93156917426276!3d21.801823460780593!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30cb09cf4b1a7907%3A0x4bfe2df0be9b2681!2sThu%20Nge%20Daw%20Taw%20Ya!5e0!3m2!1sen!2smm!4v1770965692161!5m2!1sen!2smm"
                        width="400" height="350" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

</body>

</html>