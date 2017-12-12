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

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.09

*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:05  hboaventura
Ticket#10234#

Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERJContaReceita.class.php" );

class RExportacaoTCERJContaReceita
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
var $obTExportacaoTCERJContaReceita;

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
function RExportacaoTCERJContaReceita()
{
    $this->obTExportacaoTCERJContaReceita = new TExportacaoTCERJContaReceita;
}

/**
    * Método Salvar
    * @access Public
*/
function salvarConta($boTransacao = "")
{
    $this->obTExportacaoTCERJContaReceita->setDado( "exercicio",          $this->getExercicio()       );
    $this->obTExportacaoTCERJContaReceita->setDado( "cod_conta",          $this->getCodigoConta()     );
    $this->obTExportacaoTCERJContaReceita->setDado( "cod_estrutural_tce", $this->getEstruturalExportacaoTCE()   );
    $this->obTExportacaoTCERJContaReceita->setDado( "lancamento",         $this->getLancamento()      );
    $this->obTExportacaoTCERJContaReceita->recuperaPorChave($rsContaReceita, $boTransacao);
    if ( $rsContaReceita->eof() ) {
            $obErro = $this->obTExportacaoTCERJContaReceita->inclusao( $boTransacao );
    } else {
            $obErro = $this->obTExportacaoTCERJContaReceita->exclusao( $boTransacao );
            $obErro = $this->obTExportacaoTCERJContaReceita->inclusao( $boTransacao );
    }

    return $obErro;
}

/**
    * Método Listar
    * @access Public
*/
function listarContas(&$rsRecordSet, $boTransacao = "")
{
    $this->obTExportacaoTCERJContaReceita->setDado('exercicio', $this->getExercicio()   );
    $obErro = $this->obTExportacaoTCERJContaReceita->recuperaDadosArqReceita($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
