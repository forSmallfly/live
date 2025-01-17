<?php
declare (strict_types = 1);

namespace app\command\make;

use app\utils\ValidateTool;
use think\console\command\Make;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\db\exception\DbException;
use think\helper\Str;

/**
 * 创建组件脚本
 */
class Item extends Make
{
    use ValidateTool;

    /**
     * 组件名称
     *
     * @var string
     */
    protected string $itemName = '';

    /**
     * 文件类型列表
     *
     * @var string[]
     */
    protected array $types = ['Controller', 'Model', 'Validate'];

    protected function configure(): void
    {
        // 指令配置
        $this->setName('make:item')
            ->addArgument('itemName', Argument::REQUIRED, "The name of the item")
            ->setDescription('Create new item include controller/model/validate class');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return void
     * @throws DbException
     */
    protected function execute(Input $input, Output $output): void
    {
        $itemName = trim($input->getArgument('itemName'));
        $itemName = Str::studly($itemName);

        $this->itemName = $itemName;
        foreach ($this->types as $type) {
            // 设置当前处理的文件类型
            $this->type = $type;
            $this->make($itemName, $output);
        }
    }

    /**
     * 创建组件文件
     *
     * @param string $itemName
     * @param Output $output
     * @return void
     * @throws DbException
     */
    private function make(string $itemName, Output $output): void
    {
        $classname = $this->getClassName($itemName);
        $pathname  = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname . ' already exists!</error>');
            return;
        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildClass($classname));

        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }

    /**
     * 获取模板文件路径
     *
     * @return string
     */
    protected function getStub(): string
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

        return match ($this->type) {
            'Controller' => $stubPath . 'controller.stub',
            'Model' => $stubPath . 'model.stub',
            'Validate' => $stubPath . 'validate.stub',
            default => '',
        };
    }

    /**
     * 获取文件类名
     *
     * @param string $name
     * @return string
     */
    protected function getClassName(string $name): string
    {
        if ($this->type == 'Controller') {
            return parent::getClassName($name) . ($this->app->config->get('route.controller_suffix') ? 'Controller' : '');
        } else {
            return parent::getClassName($name);
        }
    }

    /**
     * 获取命名空间
     *
     * @param string $app
     * @return string
     */
    protected function getNamespace(string $app): string
    {
        return match ($this->type) {
            'Controller' => parent::getNamespace($app) . '\\controller',
            'Model' => parent::getNamespace($app) . '\\model',
            'Validate' => parent::getNamespace($app) . '\\validate',
            default => '',
        };
    }

    /**
     * 绑定类变量
     *
     * @param string $name
     * @return string
     * @throws DbException
     */
    protected function buildClass(string $name): string
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        switch ($this->type) {
            case 'Controller':
                return str_replace(['{%className%}', '{%actionSuffix%}', '{%namespace%}', '{%app_namespace%}', '{%lowerClass%}'], [
                    $class,
                    $this->app->config->get('route.action_suffix'),
                    $namespace,
                    $this->app->getNamespace(),
                    strtolower($class)
                ], $stub);
            case 'Validate':
                [$rule, $message, $scene, $sceneList] = $this->getValidateVariable($this->itemName);
                return str_replace(['{%className%}', '{%namespace%}', '{%rule%}', '{%message%}', '{%scene%}', '{%sceneList%}'], [
                    $class,
                    $namespace,
                    $rule,
                    $message,
                    $scene,
                    $sceneList
                ], $stub);
            default:
                return str_replace(['{%className%}', '{%actionSuffix%}', '{%namespace%}', '{%app_namespace%}'], [
                    $class,
                    $this->app->config->get('route.action_suffix'),
                    $namespace,
                    $this->app->getNamespace(),
                ], $stub);
        }
    }
}
