<?php

namespace App;

class Alert
{
    public static function PrintMessage($text, $type)
    {        $alertClasses = match($type) {
            'Danger' => 'bg-red-50 text-red-800 border border-red-200',
            'Success' => 'bg-green-50 text-green-800 border border-green-200',
            'Normal' => 'bg-blue-50 text-blue-800 border border-blue-200',
            default => 'bg-blue-50 text-blue-800 border border-blue-200'
        };

        echo sprintf(            
            '<div class="alert-message fixed top-20 left-1/2 transform -translate-x-1/2 w-full max-w-md p-4 rounded-lg shadow-sm %s" style="transition: opacity 0.5s ease-out;">
                        <div class="flex items-center justify-center">
                            <p class="text-sm font-medium">%s</p>
                        </div>
                    </div>',
            $alertClasses,
            htmlspecialchars($text)
        );
    }

    public function alertAfterSignUp()
    {
        if (isset($_GET["doneSignUp"])) {
            self::PrintMessage("Done creating your account", 'Success');
        }
    }
}