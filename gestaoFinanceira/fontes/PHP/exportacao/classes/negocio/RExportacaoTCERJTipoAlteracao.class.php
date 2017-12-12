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
    * Data de Criação   : 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.13
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERJTipoAlteracao.class.php" );

class RExportacaoTCERJTipoAlteracao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipo;
/**
    * @access Private
    * @var Integer
*/
var $stTipo;

/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipoAlteracao;
/**
    * @access Private
    * @var Integer
*/
var $inExercicio;

/**
    * @access Private
    * @var Object
*/
var $obTExportacaoTCERJTipoAlteracao;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTipo($valor) { $this->inCodigoTipo = $valor; }
/**
    * @access Public
    * @param Char $valor
*/
function setTipo($valor) { $this->stTipo = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTipoAlteracao($valor) { $this->inCodigoTipoAlteracao = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setExercicio($valor) { $this->inExercicio = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoTipo() { return $this->inCodigoTipo; }
/**
    * @access Public
    * @return Char
*/
function getTipo() { return $this->stTipo; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoTipoAlteracao() { return $this->inCodigoTipoAlteracao;   }

/**
    * @access Public
    * @return Integer
*/
function getExercicio() { return $this->inExercicio;   }

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERJTipoAlteracao()
{
    $this->obTExportacaoTCERJTipoAlteracao = new TExportacaoTCERJTipoAlteracao;
}

/**
    * Método Salvar
    * @access Public
*/
function salvarTipoAlteracao($boTransacao = "")
{
    $this->obTExportacaoTCERJTipoAlteracao->setDado( "exercicio",         $this->getExercicio()           );
    $this->obTExportacaoTCERJTipoAlteracao->setDado( "cod_tipo",          $this->getCodigoTipo()          );
    $this->obTExportacaoTCERJTipoAlteracao->setDado( "cod_tipo_alteracao",$this->getCodigoTipoAlteracao() );
    $this->obTExportacaoTCERJTipoAlteracao->setDado( "tipo"              ,$this->getTipo()                );
    $this->obTExportacaoTCERJTipoAlteracao->recuperaPorChave($rsTipoAlteracao, $boTransacao);
    if ( $rsTipoAlteracao->eof() ) {
            $obErro = $this->obTExportacaoTCERJTipoAlteracao->inclusao( $boTransacao );
    } else {
            $obErro = $this->obTExportacaoTCERJTipoAlteracao->exclusao( $boTransacao );
            $obErro = $this->obTExportacaoTCERJTipoAlteracao->inclusao( $boTransacao );
    }

    return $obErro;
}

/**
    * Método Listar
    * @access Public
*/
function listarTipoAlteracao(&$rsRecordSet, $boTransacao = "")
{
    $this->obTExportacaoTCERJTipoAlteracao->setDado('exercicio', $this->getExercicio()   );
    $obErro = $this->obTExportacaoTCERJTipoAlteracao->recuperaDadosArqTipoAlteracao($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
