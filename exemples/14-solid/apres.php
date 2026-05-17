<?php

declare(strict_types=1);

class User {
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
    ) {}
}

//
//
//
//

class UserManager implements UserManagerInterface {
    public function addUser($name, $email, $phone) {
        // ...
    }
}

class ReportCreator implements ReportCreatorInterface {
    public function generateReport() {}
}

//
//
//
//

class JsonExport implements ExportStrategyInterface {
    public function export($contenu) {}
}
class CSVExport implements ExportStrategyInterface {
    public function export($contenu) {}
}
class XMLExport implements ExportStrategyInterface {
    public function export($contenu) {}
}

class ExportStrategyFactory {
    public static function getStrategy($type) {
        return match($type) {
            'json' => new JsonExport(),
            'csv' => new CSVExport(),
            'xml' => new XMLExport(),
        };
    }
}

class UserExporter implements UserExporterInterface {
    public function export($type, $contenu) {
        $strategy = ExportStrategyFactory::getStrategy($type);
        $strategy->export($contenu);
    }

}

interface DiscountStrategyInteface {
    public function getDiscount(): int;
}

class GoldDiscount implements DiscountStrategyInteface {
    public function getDiscount(): int {
        return 10;
    }
}
class SilverDiscount implements DiscountStrategyInteface {
    public function getDiscount(): int
    {
        return 10;
    }
}
class NormalDiscount implements DiscountStrategyInteface {
    public function getDiscount(): int {
        return 1;
    }
}
class OkDiscount implements DiscountStrategyInteface {
    public function getDiscount(): int {
        return 99;
    }
}

class Discounter implements DiscounterInterface {
    public function calculateDiscount($user, $membershipType) {
        $strategy = match ($membershipType) {
            'gold' => new GoldDiscount(),
            'premium' => new SilverDiscount(),
            'ok' => new OkDiscount(),
            default => new NormalDiscount(),
        };

        return $strategy->getDiscount();
    }
}

//
//
//
//

class EmailNotification implements NotificationStrategyInterface {
    public function notify($user, $message) {
        var_dump(`📧 Email envoyé à ${$user->email}: ${message}`);
    }
}
class SMSNotification implements NotificationStrategyInterface {
    public function notify($user, $message) {
        var_dump(`📱 SMS envoyé au ${$user->phone}: ${message}`);
    }
}
class PushNotification implements NotificationStrategyInterface {
    public function notify($user, $message) {
        var_dump(`🔔 Notification push envoyée à ${$user->name}: ${message}`);
    }
}
class SlackNotification implements NotificationStrategyInterface {
    public function notify($user, $message) {
        var_dump(`💬 Message Slack envoyé: ${message}`);
    }
}

class DiscordNotification implements NotificationStrategyInterface {
    public function notify($user, $message) {
        var_dump("Discord notification");
    }
}

class NotificationFactory {
    public static function getStrategy($type) {
        return match ($type) {
            'email' => new EmailNotification(),
            'sms' => new SMSNotification(),
            'slack' => new SlackNotification(),
            'push' => new PushNotification(),
            'discord' => new DiscordNotification(),
        };
    }
}

class Notifier implements NotifierInterface {
    public function notifyUser($user, $message, $type) {
        $strategy = NotificationFactory::getStrategy($type);
        $strategy->notify($user, $message);
    }
}

