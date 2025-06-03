<?php

namespace App;

class FormUtility {
    public static function textField($name, $label, $type = 'text', $required = true) {
        $classes = 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
        if ($name === 'username') {
            $classes = 'w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
        }
        
        return sprintf('
            <div class="space-y-2">
                <label for="%1$s" class="block text-sm font-medium text-gray-700">%2$s:</label>
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
        $classes
        );
    }    public static function textFieldUpdate($name, $label, $type = 'text', $required = false, $currentValue = '') {
        $classes = 'w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
        if ($name === 'username') {
            $classes = 'w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
        }

        // Use POST value if available, otherwise use current value
        $value = isset($_POST[$name]) ? $_POST[$name] : $currentValue;
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        return sprintf('
            <div class="space-y-2">
                <label for="%1$s" class="block text-sm font-medium text-gray-700">%2$s:</label>
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
        $classes
        );
    
    }
}