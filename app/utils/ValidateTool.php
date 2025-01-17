<?php

namespace app\utils;

use app\BaseValidate;
use app\Request;
use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\MysqlAdapter;
use ReflectionClass;
use ReflectionMethod;
use think\db\exception\DbException;

/**
 * 验证工具库
 */
trait ValidateTool
{
    use TableTool;

    protected $type;

    /**
     * 忽略字段名列表
     * 格式：['字段名1','字段名2'...]
     *
     * @var string[]
     */
    private array $ignoreFields = [
        'uid',
        'create_time',
        'update_time',
        'delete_time'
    ];

    /**
     * 默认添加的字段信息（用于列表分页）
     *
     * @var array[]
     */
    private array $defaultAddFields = [
        'page'      => ['integer', 'max' => MysqlAdapter::INT_DISPLAY_REGULAR],
        'list_rows' => ['integer', 'max' => MysqlAdapter::INT_DISPLAY_REGULAR]
    ];

    /**
     * 默认字段注释
     *
     * @var array
     */
    private array $defaultFieldComment = [
        'id'        => '数据标识',
        'page'      => '第几页',
        'list_rows' => '每页数据条数'
    ];

    /**
     * 方法剔除字段列表（用于自动生成验证类文件）
     * 格式：'方法名' => ['字段名1','字段名2'...]
     *
     * @var array[]
     */
    private array $methodRemoveField = [
        'add'    => ['id', 'page', 'list_rows'],
        'update' => ['page', 'list_rows']
    ];

    /**
     * 方法只使用字段列表（用于自动生成验证类文件）
     * 格式：'方法名' => ['字段名1','字段名2'...]
     *
     * @var array[]
     */
    private array $methodOnlyField = [
        'info'   => ['id'],
        'delete' => ['id']
    ];

    /**
     * 忽略方法列表（用于自动生成验证类文件）
     * 格式：['方法名1','方法名2'...]
     *
     * @var string[]
     */
    private array $ignoreMethods = [
        '__construct',
        'initialize',
        'list'
    ];

    /**
     * 获取验证类变量
     *
     * @param string $itemName
     * @return array|string[] 【验证规则,错误信息,验证场景,验证场景列表】
     * @throws DbException
     */
    private function getValidateVariable(string $itemName): array
    {
        $tableName = $this->getTableName($itemName);
        $isExists  = $this->isExistsTable($tableName);
        $rule      = $fieldCommentList = [];
        $ruleStr   = $messageStr = '';
        if ($isExists) {
            // 获取验证规则（顺带收集验证规则和字段注释信息）
            $ruleStr = $this->getRule($tableName, $rule, $fieldCommentList);
            // 获取错误信息
            $messageStr = $this->getMessage($rule, $fieldCommentList);
        }

        // 获取验证场景
        $sceneStr = $this->getScene($itemName, $rule);
        // 获取list验证场景
        $sceneList = $this->getSceneList($rule);

        return [$ruleStr, $messageStr, $sceneStr, $sceneList];
    }

    /**
     * 获取验证规则（顺带收集验证规则和字段注释信息）
     *
     * @param string $tableName
     * @param array $rule
     * @param array $fieldCommentList
     * @return string
     */
    private function getRule(string $tableName, array &$rule, array &$fieldCommentList = []): string
    {
        $fieldList = $this->getTableFields($tableName);

        $adapter = new MysqlAdapter([]);
        foreach ($fieldList as $fieldInfo) {
            $fieldName = $fieldInfo['name'];
            // 忽略的字段不在验证类中生成
            if (!in_array($fieldName, $this->ignoreFields)) {
                switch ($fieldName) {
                    case 'mobile':
                        $rule[$fieldName] = ['require', 'mobile'];
                        break;
                    case 'email':
                        $rule[$fieldName] = ['require', 'email'];
                        break;
                    case 'id_card':
                        $rule[$fieldName] = ['require', 'idCard'];
                        break;
                    default:
                        $type = $adapter->getPhinxType($fieldInfo['type']);
                        switch ($type['name']) {
                            case AdapterInterface::PHINX_TYPE_CHAR:
                            case AdapterInterface::PHINX_TYPE_STRING:
                                $rule[$fieldName] = ['require', 'max' => $type['limit'] ?? MysqlAdapter::TEXT_TINY];
                                break;
                            case AdapterInterface::PHINX_TYPE_TEXT:
                                $rule[$fieldName] = ['require', 'max' => $type['limit'] ?? MysqlAdapter::TEXT_REGULAR];
                                break;
                            case AdapterInterface::PHINX_TYPE_TINY_INTEGER:
                                $rule[$fieldName] = ['require', 'integer', 'max' => $type['limit'] ?? MysqlAdapter::INT_DISPLAY_TINY];
                                break;
                            case AdapterInterface::PHINX_TYPE_SMALL_INTEGER:
                                $rule[$fieldName] = ['require', 'integer', 'max' => $type['limit'] ?? MysqlAdapter::INT_DISPLAY_SMALL];
                                break;
                            case AdapterInterface::PHINX_TYPE_MEDIUM_INTEGER:
                                $rule[$fieldName] = ['require', 'integer', 'max' => $type['limit'] ?? MysqlAdapter::INT_DISPLAY_MEDIUM];
                                break;
                            case AdapterInterface::PHINX_TYPE_INTEGER:
                                $rule[$fieldName] = ['require', 'integer', 'max' => $type['limit'] ?? MysqlAdapter::INT_DISPLAY_REGULAR];
                                break;
                            case AdapterInterface::PHINX_TYPE_BIG_INTEGER:
                                $rule[$fieldName] = ['require', 'integer', 'max' => $type['limit'] ?? MysqlAdapter::INT_DISPLAY_BIG];
                                break;
                        }
                }

                // 检测注释是否包含状态值或类型值的描述，包含则仅提取字段名描述
                $fileComment = $fieldInfo['comment'] ?: '';
                if (str_contains($fileComment, ':')) {
                    $fileComment = explode(':', $fileComment)[0];
                } elseif (str_contains($fileComment, '：')) {
                    $fileComment = explode('：', $fileComment)[0];
                }
                $fieldCommentList[$fieldName] = $fileComment ?: ($this->defaultFieldComment[$fieldName] ?? '');
            }
        }

        // 默认添加的字段信息（用于列表分页）
        if (!empty($this->defaultAddFields)) {
            foreach ($this->defaultAddFields as $fieldName => $ruleInfo) {
                $rule[$fieldName]             = $ruleInfo;
                $fieldCommentList[$fieldName] = $this->defaultFieldComment[$fieldName] ?? '';
            }
        }

        $ruleStr = [];
        foreach ($rule as $fieldName => $fieldInfo) {
            $ruleValues = [];
            foreach ($fieldInfo as $ruleName => $ruleValue) {
                if (is_int($ruleName)) {
                    $ruleValues[] = "'{$ruleValue}'";
                } else {
                    $ruleValues[] = "'{$ruleName}' => {$ruleValue}";
                }
            }
            $ruleValues = implode(',', $ruleValues);
            $ruleStr[]  = "\t\t'{$fieldName}' => [$ruleValues]";
        }

        return ltrim(implode(",\n", $ruleStr), "\t");
    }

    /**
     * 获取错误信息
     *
     * @param $rule
     * @param $fieldCommentList
     * @return string
     */
    private function getMessage($rule, $fieldCommentList): string
    {
        $messageList = [];
        foreach ($rule as $fieldName => $fieldInfo) {
            foreach ($fieldInfo as $ruleName => $ruleValue) {
                $useRule      = is_int($ruleName) ? $ruleValue : $ruleName;
                $useFieldName = "{$fieldName}.{$useRule}";
                switch ($useRule) {
                    case 'require':
                        $messageList[$useFieldName] = "{$fieldCommentList[$fieldName]}必须";
                        break;
                    case 'integer':
                        $messageList[$useFieldName] = "{$fieldCommentList[$fieldName]}数据格式不正确";
                        break;
                    case 'max':
                        $messageList[$useFieldName] = "{$fieldCommentList[$fieldName]}长度最大为{$ruleValue}";
                        break;
                    case 'mobile':
                    case 'email':
                    case 'idCard':
                    case 'ip':
                    case 'url':
                        $messageList[$useFieldName] = "{$fieldCommentList[$fieldName]}格式不正确";
                        break;
                }
            }
        }
        $messageStr = [];
        foreach ($messageList as $useFieldName => $message) {
            $messageStr[] = "\t\t'{$useFieldName}'=>'{$message}'";
        }

        return ltrim(implode(",\n", $messageStr), "\t");
    }

    /**
     * 获取验证场景
     *
     * @param string $itemName
     * @param array $rule
     * @return string
     */
    private function getScene(string $itemName, array $rule): string
    {
        $oldType = $this->type;
        // 临时切换type为控制器，以便获取对应的控制器类名
        $this->type = 'Controller';
        // 获取控制器类名
        $controllerClassName = $this->getClassName($itemName);
        // 切回原有的type
        $this->type = $oldType;
        $sceneList  = [];
        if (class_exists($controllerClassName)) {
            $fieldNames = array_keys($rule);
            $controller = new ReflectionClass($controllerClassName);
            $methods    = $controller->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $methodName = $method->getName();
                if ($method->isStatic() || in_array($methodName, $this->ignoreMethods)) {
                    continue;
                }
                if (empty($fieldNames)) {
                    $sceneList[$methodName][] = "''";
                    continue;
                }

                foreach ($fieldNames as $fieldName) {
                    // 当方法有指定只使用字段列表时，只收集指定字段
                    if (key_exists($methodName, $this->methodOnlyField)) {
                        if (in_array($fieldName, $this->methodOnlyField[$methodName])) {
                            $sceneList[$methodName][] = "'{$fieldName}'";
                        }
                    } else {
                        // 跳过当前方法需要剔除的字段
                        if (key_exists($methodName, $this->methodRemoveField) && in_array($fieldName, $this->methodRemoveField[$methodName])) {
                            continue;
                        }
                        $sceneList[$methodName][] = "'{$fieldName}'";
                    }
                }
            }
        }

        $sceneStr = [];
        foreach ($sceneList as $methodName => $rules) {
            $rules      = implode(',', $rules);
            $sceneStr[] = "\t\t'{$methodName}' => [$rules]";
        }

        return ltrim(implode(",\n", $sceneStr), "\t");
    }

    /**
     * 获取list验证场景
     *
     * @param array $rule
     * @return string
     */
    public function getSceneList(array $rule): string
    {
        $fieldNames   = array_keys($rule);
        $sceneListStr = [];
        foreach ($fieldNames as $fieldName) {
            if (!in_array($fieldName, ['id', 'page', 'list_rows'])) {
                $sceneListStr[] = "\t\t\t->remove('{$fieldName}', 'require')";
            }
        }

        if (empty($sceneListStr)) {
            return '';
        }

        return "\n" . implode("\n", $sceneListStr);
    }

    /**
     * 获取验证类
     *
     * @param Request $request
     * @return string
     */
    private function getValidateClass(Request $request): string
    {
        return app()->getNamespace() . '\\validate\\' . $request->controller();
    }

    /**
     * 获取验证对象
     *
     * @param string $validateClass
     * @return BaseValidate
     */
    private function getValidateObj(string $validateClass): BaseValidate
    {
        return new $validateClass;
    }
}