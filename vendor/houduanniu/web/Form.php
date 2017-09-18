<?php
/**
 * Created by PhpStorm.
 * User: CPR137
 * Date: 2017/8/1
 * Time: 11:00
 */

namespace houduanniu\web;
use houduanniu\base\Application;


class Form
{
    static protected $instance;
    #表单结构数据
    protected $form_schema;
    #表单数据
    protected $form_data;
    #表单提交地址
    protected $action;
    #表单提交方法
    protected $method = 'post';

    #样式class
    protected $html_class = 'form-group';
    protected $field_html_class = 'form-control';
    protected $form_class = 'form form-horizontal';

    protected $form_name = 'autoform';


    protected function __construct()
    {

    }

    /**
     * 获取类实例化对象
     * @return $this
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 添加表单结构数据
     * @access protected
     * @author furong
     * @param $form_schema
     * @return $this
     * @since
     * @abstract
     */
    public function form_schema($form_schema)
    {
        self::getInstance()->form_schema = $form_schema;
        return $this;
    }

    /**
     * 添加表单数据
     * @access public
     * @author furong
     * @param $form_data
     * @return $this
     * @since
     * @abstract
     */
    public function form_data($form_data)
    {
        self::getInstance()->form_data = $form_data;
        return $this;
    }

    /**
     * 添加表单提交地址
     * @access public
     * @author furong
     * @param $action
     * @return $this
     * @since
     * @abstract
     */
    public function action($action)
    {
        self::getInstance()->action = $action;
        return $this;
    }

    /**
     * 添加post提交
     * @access public
     * @author furong
     * @return $this
     * @since
     * @abstract
     */
    public function method_post()
    {
        self::getInstance()->method = 'post';
        return $this;
    }

    /**
     * 添加get提交
     * @access public
     * @author furong
     * @return $this
     * @since
     * @abstract
     */
    public function method_get()
    {
        self::getInstance()->method = 'get';
        return $this;
    }

    /**
     * 创建表单
     * @access public
     * @author furong
     * @return string
     * @since
     * @abstract
     */
    public static function  create()
    {
        $form_schema = self::getInstance()->form_schema;
        $form_data = self::getInstance()->form_data;
        $action = !empty(self::getInstance()->action) ? self::getInstance()->action : U(Application::getController() . '/' . Application::getAction());
        $form_name = self::getInstance()->form_name;
        $method = self::getInstance()->method;
        $form_class = self::getInstance()->form_class;

        $action_str = !empty($action) ? 'action="' . $action . '"' : '';
        $name_str = !empty($form_name) ? 'name="' . $form_name . '"' : '';
        $id_str = !empty($form_name) ? 'id="' . $form_name . '"' : '';
        $method_str = !empty($method) ? 'method="' . $method . '"' : '';
        $class_str = !empty($form_class) ? 'class="' . $form_class . '"' : '';
        $inputs_str = !empty($form_schema) ? self::render($form_schema, $form_data) : '';
        $template = ' <form %s %s %s %s %s> %s';
        return sprintf($template, $action_str, $name_str, $id_str, $method_str, $class_str, $inputs_str);
    }

    /**
     * 创建表单input
     * @access public
     * @author furong
     * @param $form_schema
     * @param array $form_data
     * @return string
     * @since
     * @abstract
     */
    public static function render($form_schema, $form_data = [])
    {
        $form_str = '';
        foreach ($form_schema as $key => $value) {
            $htmlClass = self::getInstance()->html_class;
            $title = $value['title'];

            /*input属性开始*/
            $type = $value['type'];
            $name = $key;
            $placeholder = '请输入' . $title;
            $enum = isset($value['enum']) ? $value['enum'] : [];
            $fieldHtmlClass = self::getInstance()->field_html_class;
            $default_value = isset($form_data[$key]) ? $form_data[$key] : '';
            /*input属性结束*/


            $title_str = self::getInstance()->buildLabel($name, 'col-sm-2 control-label', $title);
            $form_control_str = '';
            $form_group_str = '';
            switch ($type) {
                case 'text':
                    $form_control_str = self::getInstance()->buildInput($type, $name, $fieldHtmlClass, $placeholder, $default_value);
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
                case 'hidden':
                    $form_group_str = self::getInstance()->buildInput($type, $name, '', '', $default_value);
                    break;
                case 'password':
                    $form_control_str = self::getInstance()->buildInput($type, $name, $fieldHtmlClass, $placeholder, $default_value);
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
                case "radio":
                    $form_control_str = self::getInstance()->buildInputRadio($name, $enum, $default_value);
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
                case 'checkboxs':
                    foreach ($enum as $key => $value) {
                        $radio_str = self::getInstance()->buildInput('checkbox', $name, '', '', $value) . ' ' . $value;
                        $form_control_str .= self::getInstance()->buildLabel('', 'checkbox-inline', $radio_str);
                    }
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
                case 'select':
                    $form_control_str = self::getInstance()->buildSelect($name, $enum, $fieldHtmlClass, $default_value);
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
                case 'mtext':
                    $form_control_str = self::getInstance()->buildTextarea($name, $fieldHtmlClass, $placeholder, $default_value);
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
                case 'editor':
                    $form_control_str = self::getInstance()->buildEditor($name, $placeholder, $default_value);
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
                case 'upload':
                    $form_control_str = self::getInstance()->buildUpload($name, $default_value);
                    $form_group_str = self::getInstance()->buildFormGroup($htmlClass, $title_str, $form_control_str);
                    break;
            }
            $form_str .= $form_group_str;
        }
        return $form_str;
    }

    /**
     * 构建表单组
     * @access  protected
     * @author furong
     * @param $htmlClass
     * @param $title_str
     * @param $form_control_str
     * @return string
     * @since 2017年7月13日 16:56:17
     * @abstract
     */
    protected function buildFormGroup($htmlClass, $title_str, $form_control_str)
    {
        $class_str = !empty($htmlClass) ? 'class="' . $htmlClass . '"' : '';
        $template = <<<EOT
             <div %s>
                %s
                <div class="col-sm-10">
                    %s
                </div>
              </div>
EOT;
        return sprintf($template, $class_str, $title_str, $form_control_str);
    }

    /**
     * 构建input表单
     * @access protected
     * @author furong
     * @param $type 表单类型
     * @param $name 表单名称
     * @param $fieldHtmlClass 字段样式class
     * @param $placeholder 默认提示
     * @return string
     * @since  2017年7月13日 16:22:14
     * @abstract
     */
    protected function buildInput($type, $name, $fieldHtmlClass, $placeholder, $default_value = '')
    {
        $type_str = !empty($type) ? 'type="' . $type . '"' : '';
        $name_str = !empty($name) ? 'name="' . $name . '"' : '';
        $id_str = !empty($name) ? 'id="' . $name . '"' : '';
        $class_str = !empty($fieldHtmlClass) ? 'class="' . $fieldHtmlClass . '"' : '';
        $placeholder_str = !empty($placeholder) ? 'placeholder="' . $placeholder . '"' : '';
        $value_str = !empty($default_value) ? 'value="' . $default_value . '"' : '';

        $template = '<input %s %s %s %s %s %s />';

        return sprintf($template, $type_str, $name_str, $id_str, $class_str, $placeholder_str, $value_str);
    }

    /**
     * 构建input表单
     * @access protected
     * @author furong
     * @param $type 表单类型
     * @param $name 表单名称
     * @param $fieldHtmlClass 字段样式class
     * @param $placeholder 默认提示
     * @return string
     * @since  2017年7月13日 16:22:14
     * @abstract
     */
    protected function buildInputRadio($name, $enum, $default_value)
    {

        $radis_str = '';
        $type_str = 'type="radio"';
        $name_str = !empty($name) ? 'name="' . $name . '"' : '';
        $id_str = !empty($name) ? 'id="' . $name . '"' : '';
        foreach ($enum as $key => $value) {
            $checked_str = $value['value'] == $default_value ? 'checked="true"' : '';
            $value_str = !empty($value['value']) ? 'value="' . $value['value'] . '"' : '';
            $radio_text_str = $value['name'];
            $template = '<input %s %s %s %s %s /> %s';
            $radis_str .= sprintf($template, $type_str, $name_str, $id_str, $value_str, $checked_str, $radio_text_str);
        }
        return $radis_str;
    }

    /**
     * 构造表单标题
     * @access protected
     * @author furong
     * @param $input_name
     * @param $title 标题
     * @return string
     * @since  2017年7月13日 16:48:06
     * @abstract
     */
    protected function buildTitle($input_name, $title)
    {
        if (empty($title)) {
            return '';
        }
        $for_str = !empty($input_name) ? 'for="' . $input_name . '"' : '';
        $template = '<label %s>%s</label>';
        return sprintf($template, $for_str, $title);
    }

    /**
     * 构建标签内容
     * @access public
     * @author furong
     * @param $for
     * @param $class
     * @param $include_str
     * @return string
     * @since 2017年7月13日 17:25:43
     * @abstract
     */
    protected function buildLabel($for, $class, $include_str)
    {
        if (empty($include_str)) {
            return '';
        }

        $for_str = !empty($for) ? 'for="' . $for . '"' : '';
        $class_str = !empty($class) ? 'class="' . $class . '"' : '';

        $template = '<label %s %s>%s</label>';
        return sprintf($template, $for_str, $class_str, $include_str);
    }

    /**
     * 构建文本域
     * @access protected
     * @author furong
     * @param $name
     * @param $fieldHtmlClass
     * @param $placeholder
     * @param string $default_value
     * @return string
     * @since 2017年7月14日 10:28:51
     * @abstract
     */
    protected function buildTextarea($name, $fieldHtmlClass, $placeholder, $default_value = '')
    {
        $name_str = !empty($name) ? 'name="' . $name . '"' : '';
        $id_str = !empty($name) ? 'id="' . $name . '"' : '';
        $class_str = !empty($fieldHtmlClass) ? 'class="' . $fieldHtmlClass . '"' : '';
        $placeholder_str = !empty($placeholder) ? 'placeholder="' . $placeholder . '"' : '';
        $value_str = $default_value;
        $template = '<textarea %s %s %s %s >%s</textarea>';

        return sprintf($template, $name_str, $id_str, $class_str, $placeholder_str, $value_str);
    }

    /**
     * 构建编辑器
     * @access protected
     * @author furong
     * @param $name
     * @param $placeholder
     * @param string $default_value
     * @return string
     * @since
     * @abstract
     */
    protected function buildEditor($name, $placeholder, $default_value = '')
    {
        $name_str = !empty($name) ? 'name="' . $name . '"' : '';
        $id_str = !empty($name) ? 'id="' . $name . '"' : '';
        $placeholder_str = !empty($placeholder) ? 'placeholder="' . $placeholder . '"' : '';
        $value_str = !empty($default_value) ? $default_value : '';
        $template = '<textarea %s %s %s style="height:500px;" >%s</textarea>';
        return sprintf($template, $name_str, $id_str, $placeholder_str, $value_str);
    }

    /**
     * 构建select 表单
     * @access protected
     * @author furong
     * @param $option_data
     * @param $fieldHtmlClass
     * @return string
     * @since 2017年7月14日 11:06:09
     * @abstract
     */
    protected function buildSelect($input_name, $option_data, $fieldHtmlClass, $select_value)
    {
        $name_str = !empty($input_name) ? 'name="' . $input_name . '"' : '';
        $id_str = !empty($input_name) ? 'id="' . $input_name . '"' : '';
        $class_str = !empty($fieldHtmlClass) ? 'class="' . $fieldHtmlClass . '"' : '';
        $selected_data_str = !empty($select_value) ? 'data-selected="' . $select_value . '"' : '';
        $options_str = '<option value="">请选择</option>';
        foreach ($option_data as $key => $value) {
            $value_str = !empty($value['value']) ? 'value="' . $value['value'] . '"' : '';
            $selected_str = ($select_value == $value['value']) ? 'selected="true"' : '';

            $option = !empty($value['option']) ? $value['option'] : '';

            $option_template = '<option %s %s >%s</option>';
            $options_str .= sprintf($option_template, $value_str, $selected_str, $option);
        }
        $select_template = '<select %s %s %s %s>%s</select>';
        return sprintf($select_template, $name_str, $id_str, $class_str, $selected_data_str, $options_str);
    }

    /**
     * 构建上传表单
     * @access  protected
     * @author furong
     * @param $name
     * @param string $default_value
     * @return string
     * @since 2017年7月26日 16:03:40
     * @abstract
     */
    protected function buildUpload($name, $default_value = '')
    {
        $hiddenInput = $this->buildInput('hidden', $name, '', '', $default_value);
        $image_url = !empty($default_value) ? getImage($default_value) : '';
        $fileInput = '<input type="file" id="upload_file" data-preview="' . $image_url . '" />';

        return $hiddenInput . $fileInput;
    }

} 