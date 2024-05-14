const nodemailer = require('nodemailer');

// Konfigurasi transporter untuk mengirim email menggunakan SMTP
const transporter = nodemailer.createTransport({
  host: 'smtp.example.com',
  port: 587,
  secure: false, // true untuk SSL, false untuk STARTTLS
  auth: {
    user: 'your_email@example.com',
    pass: 'your_password'
  }
});

// Fungsi untuk mengirim email aktivasi
function sendActivationEmail(email, activationLink) {
  const mailOptions = {
    from: 'your_email@example.com',
    to: email,
    subject: 'Aktivasi Akun',
    html: `<p>Silakan klik <a href="${activationLink}">tautan ini</a> untuk mengaktifkan akun Anda.</p>`
  };

  transporter.sendMail(mailOptions, function(error, info) {
    if (error) {
      console.log('Gagal mengirim email:', error);
    } else {
      console.log('Email terkirim: ' + info.response);
    }
  });
}

// Contoh penggunaan
const adminEmail = 'admin@example.com';
const activationLink = 'https://yourapp.com/activate?token=abc123';
sendActivationEmail(adminEmail, activationLink);
