<?php

/**
 * SMTP Setup Helper Script
 * 
 * This script helps users set up SMTP configuration for customization request emails
 * Run this from the project root: php setup_smtp.php
 */

echo "🔧 SMTP Setup Helper for Customization Request Emails\n";
echo "====================================================\n\n";

// Check if .env file exists
if (!file_exists('.env')) {
    echo "⚠️  .env file not found!\n";
    echo "Creating .env file from .env.example...\n";
    
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ .env file created from .env.example\n\n";
    } else {
        echo "❌ .env.example file not found!\n";
        echo "Please create a .env file manually.\n\n";
        exit(1);
    }
} else {
    echo "✅ .env file found\n\n";
}

// Display current mail configuration
echo "📧 Current Mail Configuration:\n";
echo "-------------------------------\n";

// Load .env file
$envContent = file_get_contents('.env');
$envLines = explode("\n", $envContent);

$mailConfig = [];
foreach ($envLines as $line) {
    if (strpos($line, 'MAIL_') === 0 && strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $mailConfig[trim($key)] = trim($value);
    }
}

echo "MAIL_MAILER: " . ($mailConfig['MAIL_MAILER'] ?? 'NOT SET') . "\n";
echo "MAIL_HOST: " . ($mailConfig['MAIL_HOST'] ?? 'NOT SET') . "\n";
echo "MAIL_PORT: " . ($mailConfig['MAIL_PORT'] ?? 'NOT SET') . "\n";
echo "MAIL_USERNAME: " . (isset($mailConfig['MAIL_USERNAME']) && $mailConfig['MAIL_USERNAME'] ? 'SET' : 'NOT SET') . "\n";
echo "MAIL_PASSWORD: " . (isset($mailConfig['MAIL_PASSWORD']) && $mailConfig['MAIL_PASSWORD'] ? 'SET' : 'NOT SET') . "\n";
echo "MAIL_FROM_ADDRESS: " . ($mailConfig['MAIL_FROM_ADDRESS'] ?? 'NOT SET') . "\n";
echo "MAIL_FROM_NAME: " . ($mailConfig['MAIL_FROM_NAME'] ?? 'NOT SET') . "\n\n";

// Check if SMTP is configured
$isConfigured = isset($mailConfig['MAIL_USERNAME']) && 
                $mailConfig['MAIL_USERNAME'] && 
                isset($mailConfig['MAIL_PASSWORD']) && 
                $mailConfig['MAIL_PASSWORD'] && 
                isset($mailConfig['MAIL_FROM_ADDRESS']) && 
                $mailConfig['MAIL_FROM_ADDRESS'];

if ($isConfigured) {
    echo "✅ SMTP appears to be configured!\n\n";
    echo "🧪 To test your configuration, run:\n";
    echo "   php test_smtp_configuration.php\n\n";
} else {
    echo "⚠️  SMTP configuration is incomplete!\n\n";
    
    echo "📋 Required Settings:\n";
    echo "---------------------\n";
    echo "1. MAIL_USERNAME - Your SMTP username\n";
    echo "2. MAIL_PASSWORD - Your SMTP password\n";
    echo "3. MAIL_FROM_ADDRESS - Verified sender email\n\n";
    
    echo "🔧 Quick Setup Options:\n";
    echo "-----------------------\n";
    echo "1. Gmail (Recommended for testing):\n";
    echo "   - Enable 2-Factor Authentication\n";
    echo "   - Generate App Password\n";
    echo "   - Use: smtp.gmail.com:587\n\n";
    
    echo "2. Mailtrap (Recommended for development):\n";
    echo "   - Sign up at mailtrap.io\n";
    echo "   - Create inbox and get credentials\n";
    echo "   - Use: smtp.mailtrap.io:2525\n\n";
    
    echo "3. SendGrid (Recommended for production):\n";
    echo "   - Sign up at sendgrid.com\n";
    echo "   - Create API key\n";
    echo "   - Use: smtp.sendgrid.net:587\n\n";
}

echo "📖 For detailed instructions, see:\n";
echo "   SMTP_CONFIGURATION_GUIDE.md\n\n";

echo "📝 Configuration Template:\n";
echo "   .env.smtp.template\n\n";

echo "🧪 Test Scripts:\n";
echo "   test_smtp_configuration.php\n\n";

echo "💡 Next Steps:\n";
echo "1. Update your .env file with SMTP credentials\n";
echo "2. Run: php test_smtp_configuration.php\n";
echo "3. Check your email inboxes\n";
echo "4. Test the customization form\n\n";

echo "📧 Customization Request Emails will be sent to:\n";
echo "- Admin: maildikshantjoshi@gmail.com\n";
echo "- Customer: Email provided in the form\n\n";

echo "🎉 Setup complete! Happy emailing! 🎉\n";






