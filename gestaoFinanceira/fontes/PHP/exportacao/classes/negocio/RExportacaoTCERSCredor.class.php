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
    * Data de Criação   : 10/02/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.06
*/

/*
$Log$
Revision 1.12  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GA_CGM_NEGOCIO . "RCGM.class.php" );

class RExportacaoTCERSCredor extends RCGM
{
/**
    * @access Private
    * @var Integer
*/
var $inTipoCredor;
/**
    * @access Private
    * @var Integer
*/
var $inNumCgm;
/**
    * @access Private
    * @var String
*/
var $stExercicio;

/**
    * @access Private
    * @var String
*/
var $stAno;

/**
    * @access Private
    * @var Object
*/
var $obTExportacaoTCERSCredor;

/**
    * @access Public
    * @param Integer $valor
*/
function setTipoCredor($valor) { $this->inTipoCredor = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setNumCgm($valor) { $this->inNumCgm = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setAno($valor) { $this->stAno = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getTipoCredor() { return $this->inTipoCredor; }

/**
    * @access Public
    * @return Integer
*/
function getNumCgm() { return $this->inNumCgm;   }

/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;   }

/**
    * @access Public
    * @return String
*/
function getAno() { return $this->stAno;   }

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERSCredor()
{
    parent::RCGM();
}

/**
    * Método Salvar
    * @access Public
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERSCredor.class.php" );
    $obTExportacaoTCERSCredor = new TExportacaoTCERSCredor;

    $obTExportacaoTCERSCredor->setDado( "exercicio",   $this->getExercicio()       );
    $obTExportacaoTCERSCredor->setDado( "numcgm",    $this->getNumCGM()       );
    $obTExportacaoTCERSCredor->setDado( "tipo",           $this->getTipoCredor()    );
    $obErro = $obTExportacaoTCERSCredor->recuperaPorChave($rsCredor, $boTransacao);
    if (!$obErro->ocorreu()) {
        if ( $rsCredor->eof() ) {
                $obErro = $obTExportacaoTCERSCredor->inclusao( $boTransacao );
        } else {
                $obErro = $obTExportacaoTCERSCredor->alteracao( $boTransacao );
        }
    }

    return $obErro;
}

/**
    * Método Listar
    * @access Public
*/
function listar(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERSCredor.class.php" );
    $obTExportacaoTCERSCredor = new TExportacaoTCERSCredor;

    $obTExportacaoTCERSCredor->setDado('exercicio', $this->getExercicio()   );
    $obTExportacaoTCERSCredor->setDado('numcgm', $this->getNumCGM()   );
    $obErro = $obTExportacaoTCERSCredor->recuperaDadosCredor($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método listarTipos
    * @access Public
*/
function listarTipos(&$rsRecordSet)
{
    $arTipos[0]['valor'  ] = "1"            ;
    $arTipos[0]['desc'   ] = "01 - Credores da Administração Pública Municipal" ;
    $arTipos[1]['valor'  ] = "2"            ;
    $arTipos[1]['desc'   ] = "02 - Credores que não pertencem à Administração Pública Municipal" ;
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche($arTipos);
}

function listarConversao(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERSCredor.class.php" );
    $obTExportacaoTCERSCredor = new TExportacaoTCERSCredor;

    if ($this->getAno()) {
        $obTExportacaoTCERSCredor->setDado('ano', $this->getAno());
    }
    $obTExportacaoTCERSCredor->setDado( 'exercicio',$this->getExercicio() );
    $obTExportacaoTCERSCredor->setDado('numcgm', $this->getNumCGM()   );
    $obErro = $obTExportacaoTCERSCredor->recuperaDadosCredorConversao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarExercicios(&$rsRecordSet, $boTransacao = "")
{
    include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERSCredor.class.php" );
    $obTExportacaoTCERSCredor = new TEXportacaoTCERSCredor;
    $obErro = $obTExportacaoTCERSCredor->recuperaExercicios( $rsRecordSet, $boTransacao);

    return $obErro;

}

}
?>
