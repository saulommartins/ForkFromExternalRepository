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
* Classe para trabalhar com arquivos Texto
* Data de Criação: 13/01/2005

* @author Analista/Desenvolvedor: Diego Barbosa Victoria

* @package Framework
* @subpackage Arquivos

$Revision: 3612 $
$Name$
$Author: lucas $
$Date: 2005-12-08 08:59:23 -0200 (Qui, 08 Dez 2005) $

Casos de uso: uc-01.01.00

*/

/**
* Classe para trabalhar com arquivos
* @author Analista/Desenvolvedor: Diego Barbosa Victoria
* @package Framework
* @subpackage Arquivos
*/
include_once( CLA_ARQUIVO );
class ArquivoTexto extends Arquivo
{
/**
* @access Private
* @var String
*/
var $stFinalLinha;
/**
* @access Private
* @var Array
*/
var $arLinhas;
/**
* @access Private
* @var Object
*/
var $roUltimaLinha;

/**
* @access Public
* @param String $valor
*/
function setFinalLinha($valor) { $this->stFinalLinha = $valor; }

/**
* @access Public
* @Return String
*/
function getFinalLinha() { return $this->stFinalLinha; }

/**
* Método Construtor
* @access Private
*/
function ArquivoTexto($stNome)
{
    parent::Arquivo( $stNome );
    $this->stTipo       = "text/plain";
    $this->stFinalLinha = "\r\n";
    $this->arLinhas     = array();
}

function addLinha($stConteudo)
{
    include_once ( CLA_LINHA_ARQUIVO );
    $this->arLinhas[]       = new LinhaArquivo( $this );
    $this->roUltimaLinha    = &$this->arLinhas[ count( $this->arLinhas ) -1 ];
    $this->roUltimaLinha->setConteudo( $stConteudo );
}

function Gravar($stModo = 'w+')
{
    if ($this->stConteudo === null) {
        $inCount = 0;
        foreach ($this->arLinhas as $obLinha) {
            $this->stConteudo .= $obLinha->getConteudo();
            $this->stConteudo .= ( (count($this->arLinhas)-1)>($inCount) ) ? $this->stFinalLinha : '';
            $inCount++;
        }
    }
    parent::Gravar( $stModo );

    return $this->obErro;
}
}
?>
