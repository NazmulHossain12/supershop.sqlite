<?php
// Beekeeper password decryption attempt
// IV (12 bytes / 24 hex), Tag (16 bytes / 32 hex), Ciphertext (rest, base64)

$encryptedString = "8442f82dc53eaf16469f604a6f719d4c5332c134243790e91c49e8dbbd503265fd11692dead442115c783f3a0cb8252bYbzV9TChHNaZiN9T0JDGch8SpEoV9/D6DxwdrJr8Fuw=";
$masterKeyHex = "bb7fc3b89db5ef1b4ed5fc99df6fc8081a1d54cfebc898d3c69ebbb3a73abc9faf1c087d264a11e88661f000fd6d80c5"; // First 48 bytes of .key in hex

function decryptBeekeeper($encrypted, $keyHex)
{
    if (strlen($encrypted) < 56)
        return false;

    $iv = hex2bin(substr($encrypted, 0, 24));
    $tag = hex2bin(substr($encrypted, 24, 32));
    $ciphertext = base64_decode(substr($encrypted, 56));

    // Beekeeper often uses the first 32 bytes as the encryption key
    $key = hex2bin(substr($keyHex, 0, 64));

    $plaintext = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    return $plaintext;
}

$result = decryptBeekeeper($encryptedString, $masterKeyHex);
if ($result) {
    echo "Decrypted Password: " . $result . "\n";
} else {
    echo "Decryption Failed.\n";
    // Try another key offset or length if needed
    $key2 = hex2bin(substr($masterKeyHex, 32, 64)); // Try from middle
    // ... or try the base64 part of the key file if it exists
}
