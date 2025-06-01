<?php

namespace App;

class FormUtility {
    public static function getOldValue($key, $default = '') {
        return isset($_SESSION['form_data'][$key]) 
            ? htmlspecialchars($_SESSION['form_data'][$key]) 
            : $default;
    }

    public static function textField($name, $label, $type = 'text', $required = true) {
        return sprintf('
            <div class="space-y-2">
                <label for="%1$s" class="block text-sm font-medium text-gray-700">%2$s:</label>
                <input 
                    type="%3$s" 
                    id="%1$s" 
                    name="%1$s" 
                    value="%4$s"
                    %5$s
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
        ',
        $name,
        $label,
        $type,
        self::getOldValue($name),
        $required ? 'required' : ''
        );
    }
}