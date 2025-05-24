<?php
class PasswordGenerator {
    private $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    private $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private $numbers = '0123456789';
    private $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    public function generate($length = 12, $min_lowercase = 3, $min_uppercase = 3, $min_numbers = 3, $min_special = 3) {
        // Validate inputs
        $total_min = $min_lowercase + $min_uppercase + $min_numbers + $min_special;
        if ($total_min > $length) {
            throw new Exception("Total minimum characters ($total_min) cannot exceed password length ($length)");
        }
        
        $password = [];
        
        // Add required lowercase characters
        for ($i = 0; $i < $min_lowercase; $i++) {
            $password[] = $this->getRandomChar($this->lowercase);
        }
        
        // Add required uppercase characters
        for ($i = 0; $i < $min_uppercase; $i++) {
            $password[] = $this->getRandomChar($this->uppercase);
        }
        
        // Add required numbers
        for ($i = 0; $i < $min_numbers; $i++) {
            $password[] = $this->getRandomChar($this->numbers);
        }
        
        // Add required special characters
        for ($i = 0; $i < $min_special; $i++) {
            $password[] = $this->getRandomChar($this->special);
        }
        
        // Fill remaining length with random characters from all sets
        $all_chars = $this->lowercase . $this->uppercase . $this->numbers . $this->special;
        while (count($password) < $length) {
            $password[] = $this->getRandomChar($all_chars);
        }
        
        // Shuffle the password array to mix up the characters
        shuffle($password);
        
        return implode('', $password);
    }
    
    private function getRandomChar($chars) {
        $length = strlen($chars);
        return $chars[random_int(0, $length - 1)];
    }
    
    public function generateByPercentage($length, $lowercase_percent, $uppercase_percent, $numbers_percent, $special_percent) {
        // Validate percentages total to 100
        $total_percent = $lowercase_percent + $uppercase_percent + $numbers_percent + $special_percent;
        if ($total_percent != 100) {
            throw new Exception("Percentages must total 100");
        }
        
        // Convert percentages to counts
        $lowercase_count = (int)round(($lowercase_percent / 100) * $length);
        $uppercase_count = (int)round(($uppercase_percent / 100) * $length);
        $numbers_count = (int)round(($numbers_percent / 100) * $length);
        $special_count = $length - ($lowercase_count + $uppercase_count + $numbers_count);
        
        return $this->generate($length, $lowercase_count, $uppercase_count, $numbers_count, $special_count);
    }
}
?> 