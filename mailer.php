<?php
require_once __DIR__ . '/config.php';

class Mailer {
    public static function send(string $to, string $subject, string $htmlBody): bool {
        $host = MAIL_SMTP_HOST;
        $port = MAIL_SMTP_PORT;
        $user = MAIL_SMTP_USER;
        $pass = MAIL_SMTP_PASS;
        $from = MAIL_FROM;
        $fromName = MAIL_FROM_NAME;

        $socket = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$socket) {
            error_log("Mailer: não conectou em {$host}:{$port} – {$errstr}");
            return false;
        }

        $r = fn() => fgets($socket, 515);
        $w = function(string $cmd) use ($socket) { fwrite($socket, $cmd . "\r\n"); };

        $r(); // 220 greeting
        $w('EHLO ' . (gethostname() ?: 'localhost'));
        while (($line = $r()) !== false && substr($line, 3, 1) !== ' ');

        // STARTTLS apenas em porta 587
        if ($port === 587) {
            $w('STARTTLS');
            $r();
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $w('EHLO ' . (gethostname() ?: 'localhost'));
            while (($line = $r()) !== false && substr($line, 3, 1) !== ' ');
        }

        if ($user !== '') {
            $w('AUTH LOGIN');
            $r();
            $w(base64_encode($user));
            $r();
            $w(base64_encode($pass));
            $r();
        }

        $w("MAIL FROM: <{$from}>");
        $r();
        $w("RCPT TO: <{$to}>");
        $r();
        $w('DATA');
        $r();

        $subjectEncoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $nameEncoded    = '=?UTF-8?B?' . base64_encode($fromName) . '?=';

        $msg  = "From: {$nameEncoded} <{$from}>\r\n";
        $msg .= "To: {$to}\r\n";
        $msg .= "Subject: {$subjectEncoded}\r\n";
        $msg .= "MIME-Version: 1.0\r\n";
        $msg .= "Content-Type: text/html; charset=UTF-8\r\n";
        $msg .= "\r\n";
        $msg .= $htmlBody . "\r\n";
        $msg .= '.';

        $w($msg);
        $r();
        $w('QUIT');
        fclose($socket);

        return true;
    }
}
