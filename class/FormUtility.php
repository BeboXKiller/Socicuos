<?php

namespace App;

class FormUtility {
    public static function textField($name, $label, $type = 'text', $required = true) {
        $baseClasses = 'w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base';
        
        // Special styling for username field
        if ($name === 'username') {
            $baseClasses = 'w-full pl-2 sm:pl-3 pr-8 sm:pr-10 py-1.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base';
        }
        
        return sprintf('
            <div class="space-y-1 sm:space-y-2">
                <label for="%1$s" class="block text-xs sm:text-sm font-medium text-gray-700">%2$s:</label>
                <input 
                    type="%3$s" 
                    id="%1$s" 
                    name="%1$s" 
                    value="%4$s"
                    %5$s
                    class="%6$s"
                    autocomplete="%1$s"
                >
            </div>
        ',
        $name,
        $label,
        $type,
        isset($_POST[$name]) ? htmlspecialchars($_POST[$name], ENT_QUOTES, 'UTF-8') : '',
        $required ? 'required' : '',
        $baseClasses
        );
    }

    public static function textFieldUpdate($name, $label, $type = 'text', $required = false, $currentValue = '') {
        $baseClasses = 'w-full px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base';
        
        // Special styling for username field
        if ($name === 'username') {
            $baseClasses = 'w-full pl-2 sm:pl-3 pr-8 sm:pr-10 py-1.5 sm:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base';
        }

        // Use POST value if available, otherwise use current value
        $value = isset($_POST[$name]) ? $_POST[$name] : $currentValue;
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        return sprintf('
            <div class="space-y-1 sm:space-y-2">
                <label for="%1$s" class="block text-xs sm:text-sm font-medium text-gray-700">%2$s:</label>
                <input 
                    type="%3$s" 
                    id="%1$s" 
                    name="%1$s" 
                    value="%4$s"
                    %5$s
                    class="%6$s"
                    autocomplete="%1$s"
                >
            </div>
        ',
        $name,
        $label,
        $type,
        $value,
        $required ? 'required' : '',
        $baseClasses
        );
    }
}