<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

/**
    * Classe Mãe composta por métodos e atributos comuns a todos Objetos
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class Objeto
{
/**
    * @var String
    * @access Private
*/
var $stDebug;

/**
    * @access Public
    * @param String $valor
*/
function setDebug($valor) { $this->stDebug = $valor; }

/**
    * @access Public
    * @return String
*/
function getDebug() { return $this->stDebug; }

/**
    * Método Construtor
    * @access Private
*/
function Objeto() {}

/**
    * Método utilizado para debugar o objeto.
    * Pode-se efetuar debug de Html, Sql, e todos os tipos de variáveis em PHP.
    * @access Public
    * @param Mixed $valor Pode ser passado qualquer tipo de variável em PHP.
*/
function debug($valor = '')
{
    switch ($valor) {
        case '':
            if ( is_subclass_of($this,'persistente') ) {
                ob_start();
                if ($this->stDebug == 'inclusao') {
                    print_r( $this->MontaInclusao($boTransacao, $arBlob) );
                } elseif ($this->stDebug == 'alteracao') {
                    print_r( $this->MontaAlteracao($arBlob).($this->montaChave()?" WHERE ".$this->montaChave():"" ) );
                } elseif ($this->stDebug == 'exclusao') {
                    print_r( $this->MontaExclusao().($this->montaChave()?" WHERE ".$this->montaChave():"" ) );
                } elseif ( strstr(strtolower($this->stDebug),'select')  ) {
                    print_r( $this->stDebug );
                } else {
                    var_dump( $this );
                }

                $stOutput = ob_get_contents();
                ob_clean();
                echo "<div align='left'>";
                if( !$this->stDebug )
                    highlight_string( "\n<?php debug \n $stOutput \n?>\n");
                else
                    highlight_string( "\n<?sql debug \n $stOutput \n?>\n");

                echo "</div>";

            }elseif(   is_subclass_of($this,'componente')
                    || is_subclass_of($this,'tabela')    ){
                if (method_exists($this,'montaHtml')) {
                    $this->MontaHtml();
                    echo "<div align='left'>";
                    highlight_string(" \n<?php debug \n ".$this->getHTML()." \n ?>\n ");
                    echo "</div>";
                }
            } else {
                ob_start();
                var_dump($this);
                $stOutput = ob_get_contents();
                ob_clean();
                echo "<div align='left'>";
                highlight_string( "\n<?php debug \n $stOutput \n?>\n");
                echo "</div>";
            }
        break;
        case is_array($valor):
        case is_object($valor):
            echo "<pre>";
            var_dump($valor);
            echo "</pre>";
        break;
        case !is_array($valor):
        case !is_object($valor):
            echo $valor;
        break;
    }

    return true;
}

function debugAjax()
{
    ob_start();
    if ( is_subclass_of($this,'persistente') ) {
        if ($this->stDebug == 'inclusao') {
            print_r( $this->MontaInclusao($boTransacao, $arBlob) );
        } elseif ($this->stDebug == 'alteracao') {
            print_r( $this->MontaAlteracao($arBlob).($this->montaChave()?" WHERE ".$this->montaChave():"" ) );
        } elseif ($this->stDebug == 'exclusao') {
            print_r( $this->MontaExclusao().($this->montaChave()?" WHERE ".$this->montaChave():"" ) );
        } elseif ( strstr(strtolower(trim($this->stDebug)),'select')  ) {
            print_r( $this->stDebug );
        } else {
            var_dump( $this );
        }
    }
    $stOutput = ob_get_contents();
    ob_clean();
    ob_end_clean();

    $stHtml = str_replace("  ","",$stOutput);
    $stHtml = str_replace("\n","\\n",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stHtml = str_replace(chr(13),"",$stHtml);
    $stHtml = str_replace(chr(13).chr(10),"",$stHtml);
    echo "prompt('Debug ajax:\\n' + '$stHtml','$stHtml');";
}

}
?>
