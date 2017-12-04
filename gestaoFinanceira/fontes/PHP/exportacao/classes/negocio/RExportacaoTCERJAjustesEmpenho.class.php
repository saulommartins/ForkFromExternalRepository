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
    * Data de Criação   : 15/05/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.14
*/

/*
$Log$
Revision 1.2  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_MAPEAMENTO . "FExportacaoEmpenho.class.php" );
include_once ( CAM_GF_EMP_MAPEAMENTO . "TEmpenhoAtributoEmpenhoValor.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"       );

class RExportacaoTCERJAjustesEmpenho
{
/**
    * @access Private
    * @var Char
*/
var $stCodEntidades;
/**
    * @access Private
    * @var Char
*/
var $stExercicio;

/**
    * @access Private
    * @var Char
*/
var $stDataIncial;

/**
    * @access Private
    * @var Char
*/
var $stDataFinal;

/**
    * @access Private
    * @var Integer
*/
var $inCodPreEmpenho;
/**
    * @access Private
    * @var Char
*/

var $stTimestamp;

/**
    * @access Private
    * @var Integer
*/
var $inCodAtributo;

/**
    * @access Private
    * @var Char
*/
var $stValor;

/**
    * @access Public
    * @param Char $valor
*/
function setEntidades($valor) { $this->stCodEntidades = $valor; }

/**
    * @access Public
    * @param Char $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }

/**
    * @access Public
    * @return char
*/
function setDataInicial($valor) { $this->stDataInicial = $valor; }
/**
    * @access Public
    * @return Char
*/
function setDataFinal($valor) { $this->stDataFinal = $valor; }

/**
    * @access Public
    * @return Char
*/
function setCodPreEmpenho($valor) { $this->inCodPreEmpenho = $valor; }
/**
    * @access Public
    * @return Char
*/
function setTimestamp($valor) { $this->stTimestamp = $valor; }
/**
    * @access Public
    * @return Char
*/
function setCodAtributo($valor) { $this->inCodAtributo = $valor; }
/**
    * @access Public
    * @return Char
*/
function setValor($valor) { $this->stValor = $valor; }

/**
    * @access Public
    * @return Char
*/
function getEntidades() { return $this->stCodEntidades;   }

/**
    * @access Public
    * @return Char
*/
function getExercicio() { return $this->stExercicio;   }

/**
    * @access Public
    * @return Char
*/
function getDataInicial() { return $this->stDataInicial;   }

/**
    * @access Public
    * @return Char
*/
function getDataFinal() { return $this->stDataFinal;   }

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERJAjustesEmpenho()
{
    $this->obFExportacaoEmpenho             = new FExportacaoEmpenho;
}

function listarAjustesEmpenho(&$rsRecordSet, $boTransacao = "")
{
            $this->obFExportacaoEmpenho->setDado('stExercicio'   , $this->getExercicio()         );
            $this->obFExportacaoEmpenho->setDado('stCodEntidades', $this->getEntidades()         );
            $this->obFExportacaoEmpenho->setDado('dtInicial'     , $this->getDataInicial()       );
            $this->obFExportacaoEmpenho->setDado('dtFinal'       , $this->getDataFinal()         );
            $obErro = $this->obFExportacaoEmpenho->recuperaDadosExportacaoAjustes( $rsRecordSet, $stFiltro, "e.exercicio, e.cod_entidade, e.cod_empenho" );
            if ($obErro->ocorreu()) { return $obErro; }
}

function salvarAjustes($boTransacao = "")
{
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->setPersistenteValores ( new TEmpenhoAtributoEmpenhoValor );
    $obRCadastroDinamico->setCodCadastro ( 1 );
    $obRCadastroDinamico->obRModulo->setCodModulo ( 10 );

    $obErro = new Erro;
    if ($this->stTimestamp) {
        $arChaveAtributo = array(
                                    "cod_pre_empenho" => $this->inCodPreEmpenho,
                                    "exercicio"       => $this->stExercicio,
                                    "timestamp"       => $this->stTimestamp
                                );
    } else {
        $arChaveAtributo = array(
                                    "cod_pre_empenho" => $this->inCodPreEmpenho,
                                    "exercicio"       => $this->stExercicio
                                );
    }

    if ( ($this->stValor) or ( $this->stTimestamp) ) {
        $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
        $obRCadastroDinamico->addAtributosDinamicos( $this->inCodAtributo , $this->stValor );

        if ($this->stTimestamp) {
            $obErro = $obRCadastroDinamico->excluirValores( $boTransacao );
        }
        if (!$obErro->ocorreu()) {
            if ($this->stValor) {
                $obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
    }

    return $obErro;
}

}

?>
