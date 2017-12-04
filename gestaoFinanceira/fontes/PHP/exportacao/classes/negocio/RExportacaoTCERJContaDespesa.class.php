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
    * Data de Criação   : 11/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.10
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERJContaDespesa.class.php" );

class RExportacaoTCERJContaDespesa
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoConta;
/**
    * @access Private
    * @var Integer
*/
var $inEstruturalExportacaoTCE;
/**
    * @access Private
    * @var Integer
*/
var $inExercicio;
/**
    * @access Private
    * @var Boolean
*/
var $boLancamento;
/**
    * @access Private
    * @var Object
*/
var $obTExportacaoTCERJContaDespesa;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoConta($valor) { $this->inCodigoConta = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setEstruturalExportacaoTCE($valor) { $this->inEstruturalExportacaoTCE = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setExercicio($valor) { $this->inExercicio = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setLancamento($valor) { $this->boLancamento = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoConta() { return $this->inCodigoConta; }

/**
    * @access Public
    * @return Integer
*/
function getEstruturalExportacaoTCE() { return $this->inEstruturalExportacaoTCE;   }

/**
    * @access Public
    * @return Integer
*/
function getExercicio() { return $this->inExercicio;   }

/**
    * @access Public
    * @return Integer
*/
function getLancamento() { return $this->boLancamento;   }

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERJContaDespesa()
{
    $this->obTExportacaoTCERJContaDespesa = new TExportacaoTCERJContaDespesa();
}

/**
    * Método Salvar
    * @access Public
*/
function salvarConta($boTransacao = "")
{
    $this->obTExportacaoTCERJContaDespesa->setDado( "exercicio",          $this->getExercicio()       );
    $this->obTExportacaoTCERJContaDespesa->setDado( "cod_conta",          $this->getCodigoConta()     );
    $this->obTExportacaoTCERJContaDespesa->setDado( "cod_estrutural_tce", $this->getEstruturalExportacaoTCE()   );
    $this->obTExportacaoTCERJContaDespesa->setDado( "lancamento",         $this->getLancamento()      );
    $this->obTExportacaoTCERJContaDespesa->recuperaPorChave($rsContaDespesa, $boTransacao);
    if ( $rsContaDespesa->eof() ) {
            $obErro = $this->obTExportacaoTCERJContaDespesa->inclusao( $boTransacao );
    } else {
            $obErro = $this->obTExportacaoTCERJContaDespesa->exclusao( $boTransacao );
            $obErro = $this->obTExportacaoTCERJContaDespesa->inclusao( $boTransacao );
    }

    return $obErro;
}

/**
    * Método Listar
    * @access Public
*/
function listarContas(&$rsRecordSet, $boTransacao = "")
{
    $this->obTExportacaoTCERJContaDespesa->setDado('exercicio', $this->getExercicio()   );
    $obErro = $this->obTExportacaoTCERJContaDespesa->recuperaDadosArqDespesa($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
