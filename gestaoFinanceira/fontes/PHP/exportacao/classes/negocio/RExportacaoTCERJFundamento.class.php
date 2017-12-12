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

    * Casos de uso: uc-02.08.12
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERJFundamento.class.php" );

class RExportacaoTCERJFundamento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipoNorma;

/**
    * @access Private
    * @var Integer
*/
var $inCodigoFundamentoLegal;

/**
    * @access Private
    * @var Object
*/
var $obTExportacaoTCERJFundamento;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTipoNorma($valor) { $this->inCodigoTipoNorma = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoFundamentoLegal($valor) { $this->inCodigoFundamentoLegal = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoTipoNorma() { return $this->inCodigoTipoNorma; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoFundamentoLegal() { return $this->inCodigoFundamentoLegal;   }

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERJFundamento()
{
    $this->obTExportacaoTCERJFundamento = new TExportacaoTCERJFundamento;
}

/**
    * Método Salvar
    * @access Public
*/
function salvarFundamento($boTransacao = "")
{
    $this->obTExportacaoTCERJFundamento->setDado( "cod_tipo_norma",       $this->getCodigoTipoNorma()         );
    $this->obTExportacaoTCERJFundamento->setDado( "cod_fundamento_legal", $this->getCodigoFundamentoLegal()   );
    $this->obTExportacaoTCERJFundamento->recuperaPorChave($rsFundamento, $boTransacao);
    if ( $rsFundamento->eof() ) {
            $obErro = $this->obTExportacaoTCERJFundamento->inclusao( $boTransacao );
    } else {
            $obErro = $this->obTExportacaoTCERJFundamento->exclusao( $boTransacao );
            $obErro = $this->obTExportacaoTCERJFundamento->inclusao( $boTransacao );
    }

    return $obErro;
}

/**
    * Método Listar
    * @access Public
*/
function listarFundamentos(&$rsRecordSet, $boTransacao = "")
{
    $obErro = $this->obTExportacaoTCERJFundamento->recuperaDadosArqFundamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
