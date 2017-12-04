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
    * Classe de Regra de Atributo Empenho Valor
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.6  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                 );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                 );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPreEmpenho.class.php"                );

/**
    * Classe de Regra de Atributo Empenho Valor
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class REmpenhoAtributoEmpenhoValor
{
/**
    * @access Private
    * @var Reference Object
*/
var $roPreEmpenho;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
/**
    * @access Private
    * @var String
*/
var $stValor;

/**
    * @access Public
    * @param Object $Valor
*/
function setRCadastroDinamico($valor) { $this->obRCadastroDinamico = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setValor($valor) { $this->stValor = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRCadastroDinamico() { return $this->obRCadastroDinamico; }
/**
    * @access Public
    * @return String
*/
function getValor() { return $this->stValor; }

/**
     * Método construtor
     * @access Public
     * @param Reference Object $roPreEmpenho
*/
function REmpenhoAtributoEmpenhoValor(&$roPreEmpenho)
{
    $this->obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
    $this->obRCadastroDinamico  = new RCadastroDinamico;
    $this->obTransacao          = new Transacao;
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 10 );
    $this->roPreEmpenho         = &$roPreEmpenho;
}

/**
    * Incluir Ordem Pagamento
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $arChavePersistenteValores = array( "cod_pre_empenho" => $this->roPreEmpenho->getCodPreEmpenho(),
                                            "exercicio"       => $this->roPreEmpenho->getExercicio() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
        $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Alterar Ordem Pagamento
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $arChavePersistenteValores = array( "cod_pre_empenho" => $this->roPreEmpenho->getCodPreEmpenho(),
                                            "exercicio"       => $this->roPreEmpenho->getExercicio() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
        $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Exclui Ordem Pagamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $arChavePersistenteValores = array( "cod_pre_empenho" => $this->roPreEmpenho->getCodPreEmpenho(),
                                            "exercicio"       => $this->roPreEmpenho->getExercicio() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

}
