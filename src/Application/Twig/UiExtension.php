<?php

namespace Application\Twig;

/**
 * @author Borut Balažek <bobalazek124@gmail.com>
 */
class UiExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'application/ui';
    }

    /**
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
             new \Twig_SimpleFunction(
                'array_labels',
                [
                    $this,
                    'arrayLabels',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
               'json_decode',
               [
                   $this,
                   'jsonDecode',
               ],
               [
                   'is_safe' => ['html'],
               ]
           ),
            new \Twig_SimpleFunction(
                'pagination',
                [
                    $this,
                    'pagination',
                ],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @return string
     */
    public function arrayLabels($array = [])
    {
        if (!count($array)) {
            return '';
        }

        $output = '<ul class="list-inline">';
        foreach ($array as $one) {
            $output .= '<li>'.$one.'</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    /**
     * @return array
     */
    public function jsonDecode($string)
    {
        return json_decode($string);
    }

    /**
     * @return string
     */
    public function pagination($output)
    {
        return $output;
    }
}
