<?php

require __DIR__ . '/vendor/autoload.php';

class MakeRepositoryCommand
{
    public function run($arguments)
    {
        if (empty($arguments[1])) {
            echo "Usage: php cli.php make:repository {name}\n";
            return;
        }

        $name = $arguments[1];
        $interfacePath = __DIR__ . "/src/Repositories/Contracts/{$name}RepositoryInterface.php";
        $repositoryPath = __DIR__ . "/src/Repositories/Eloquent/{$name}Repository.php";

        // Tạo thư mục nếu chưa tồn tại
        $this->ensureDirectoryExists(dirname($interfacePath));
        $this->ensureDirectoryExists(dirname($repositoryPath));

        // Nội dung của Interface
        $interfaceContent = <<<EOT
<?php

namespace AtCore\CoreRepo\Repositories\Contracts;

interface {$name}RepositoryInterface extends BaseRepositoryInterface
{
    // Add your custom methods here
}
EOT;

        // Nội dung của Repository
        $repositoryContent = <<<EOT
<?php

namespace AtCore\CoreRepo\Repositories\Eloquent;

use AtCore\CoreRepo\Repositories\Contracts\\{$name}RepositoryInterface;

class {$name}Repository implements {$name}RepositoryInterface
{
    // Implement your methods here
}
EOT;

        // Kiểm tra và tạo file Interface
        if (!file_exists($interfacePath)) {
            file_put_contents($interfacePath, $interfaceContent);
            echo "Created: {$interfacePath}\n";
        } else {
            echo "File already exists: {$interfacePath}\n";
        }

        // Kiểm tra và tạo file Repository
        if (!file_exists($repositoryPath)) {
            file_put_contents($repositoryPath, $repositoryContent);
            echo "Created: {$repositoryPath}\n";
        } else {
            echo "File already exists: {$repositoryPath}\n";
        }

        echo "Repository and Interface for {$name} created successfully.\n";
    }

    /**
     * Đảm bảo thư mục tồn tại, nếu chưa tồn tại thì tạo mới.
     */
    private function ensureDirectoryExists($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}

$command = new MakeRepositoryCommand();
$command->run($argv);
