<?php
class SizeEnum {
    const MEDIUM = "M";
    const LARGE = "L";
    const XLARGE = "XL";

    public static function getMultiplier($sizeName) {
        $multipliers = [
            self::MEDIUM => 1.0,
            self::LARGE => 1.2,
            self::XLARGE => 1.4
        ];
        // Nếu sizeName null hoặc không khớp, mặc định là M (1.0)
        if (empty($sizeName)) return 1.0;
        return $multipliers[strtoupper($sizeName)] ?? 1.0;
    }
}
?>
