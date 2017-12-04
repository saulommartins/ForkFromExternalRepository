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
    * Classe para trabalhar as colunas de cada arquivo do exportador
    * @author Analista/Desenvolvedor: Diego Barbosa Victoria
    * @package Importador
*/
class ArquivoImportadorColuna
{
/**
    * @access Private
    * @var String
*/
var $stCampo;
/**
    * @access Private
    * @var String
*/
var $stTipoDado;

/**
    * @access Private
    * @var Integer
*/
var $inTamanhoMaximo;
/**
    * @access Private
    * @var Object
*/
var $roArquivoImportador;

/**
    * @access Private
    * @var Boolean
*/
var $boChavePrimaria;

/**
    * @access Private
    * @var Boolean
*/
var $boRequerido;

/**
    * @access Private
    * @var String
*/
var $stChaveEstrangeiraArquivo;

/**
    * @access Private
    * @var String
*/
var $stChaveEstrangeiraColuna;

/**
    * @access Public
    * @param String $valor
*/
function setCampo($valor) { $this->stCampo          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoDado($valor) { $this->stTipoDado       = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setTamanhoMaximo($valor) { $this->inTamanhoMaximo  = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setChavePrimaria($valor) { $this->boChavePrimaria  = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setRequerido($valor) { $this->boRequerido  = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setChaveEstrangeira($stArquivo,$stColuna)
{
    $this->stChaveEstrangeiraArquivo = $stArquivo;
    $this->stChaveEstrangeiraColuna = $stColuna;
}

/**
    * @access Public
    * @Return String
*/
function getCampo() { return $this->stCampo;      }
/**
    * @access Public
    * @Return String
*/
function getTipoDado() { return $this->stTipoDado;   }
/**
    * @access Public
    * @Return Integer
*/

function getTamanhoMaximo() { return $this->inTamanhoMaximo;}

/**
    * @access Public
    * @Return Boolean
*/
function getChavePrimaria() { return $this->boChavePrimaria;}

/**
    * @access Public
    * @Return Boolean
*/
function getRequerido() { return $this->boRequerido;}

/**
    * @access Public
    * @Return String
*/
function getChaveEstrangeiraArquivo() {return $this->stChaveEstrangeiraArquivo;}

/**
    * @access Public
    * @Return String
*/
function getChaveEstrangeiraColuna() {return $this->stChaveEstrangeiraColuna;}

/**
    * Método Construtor
    * @access Private
*/
function ArquivoImportadorColuna(&$roArquivoImportadorColuna,$stCampo,$stTipoDado)
{
    $this->roArquivoImportador     = &$roArquivoImportadorColuna;
    $this->stTipoDado           = $stTipoDado;
    $this->inTamanhoMaximo      = null;
    $this->stCampo              = $stCampo;
    $this->boRequerido          = false;
    $this->boChavePrimaria      = false;
    $this->stChaveEstrangeiraArquivo = null;
    $this->stChaveEstrangeiraColuna  = null;
}

function Validar($stCampo)
{
    if (!$this->stCampo) {
        $this->roArquivoImportador->obErro->setDescricao('Deve ser setada a coluna');
    } else {
         if ($this->boChavePrimaria == true || $this->stChaveEstrangeiraArquivo != null) {
            $this->boRequerido = true;
         }
         if (trim($stCampo) != "" && (strtoupper($this->boRequerido) == "TRUE" || $this->boRequerido == true ) ) {
            switch ( strtoupper(trim($this->stTipoDado)) ) {
                case "CARACTER":
                    if (trim($stCampo) == "" || (strlen($stCampo) > $this->getTamanhoMaximo() && $this->getTamanhoMaximo() != null)) {
                        $this->roArquivoImportador->obErro->setDescricao('O tipo de dado da coluna '.$this->stCampo.' do arquivo '.$this->roArquivoImportador->getNomeArquivo().' não confere.  ');
                    }
                break;
                case "INTEIRO":
                    if (!preg_match ("/^[0-9]{1,10}$/",$stCampo) || $stCampo > 2147483647 ) {
                        $this->roArquivoImportador->obErro->setDescricao('O tipo de dado da coluna '.$this->stCampo.' do arquivo '.$this->roArquivoImportador->getNomeArquivo().' não confere.  ');
                    }
                break;
                case "NUMERICO":
                    $arTamanho = explode( ".", $this->getTamanhoMaximo());
                    if ( count($arTamanho)<=1 ) {
                        $this->roArquivoImportador->obErro->setDescricao("Utilize o tipo INTEIRO ou informe o tamanho com formato numérico. Ex: 15.4");
                    } else {
                        if (!preg_match ("/^[0-9]{1,".($arTamanho[0]-$arTamanho[1])."}([.][0-9]{1,".$arTamanho[1]."}){0,}$/",$stCampo) ) {
                            $this->roArquivoImportador->obErro->setDescricao('O tipo de dado da coluna '.$this->stCampo.' do arquivo '.$this->roArquivoImportador->getNomeArquivo().' não confere.  ');
                        }
                    }
                break;
                case "BOOLEAN":
                    if (strtolower($stCampo) != "true" && strtolower($stCampo) != "false") {
                        $this->roArquivoImportador->obErro->setDescricao('O tipo de dado da coluna '.$this->stCampo.' do arquivo '.$this->roArquivoImportador->getNomeArquivo().' não confere.  ');
                    }
                break;
            }
        } elseif ((strtoupper($this->boRequerido) == "TRUE" || $this->boRequerido == true) && trim($stCampo) == "" ) {
            $this->roArquivoImportador->obErro->setDescricao('A coluna '.$this->stCampo.' do arquivo '.$this->roArquivoImportador->getNomeArquivo().' não pode ser vazia.  ');
        }

    }
}

}
?>
