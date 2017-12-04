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
    * Classe de regra de negócio para Sociedade
    * Data de Criação: 30/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMSociedade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.8  2007/04/23 19:21:05  dibueno
retirado espaço vazio ao final do arquivo

Revision 1.7  2007/01/22 15:59:03  cercato
Bug #8157#

Revision 1.6  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMSociedade.class.php"     );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoSociedade.class.php" );

class RCEMSociedade
{
/**
    * @access Private
    * @var Integer
*/
var $codigo_processo;

/**
    * @access Private
    * @var String
*/
var $ano_exercicio;

/**
* @access Private
* @var Object
*/
var $obTCEMProcessoSociedade;

/**
    * @access Private
    * @var Float
*/
var $flCapitalSocial;
/**
    * @access Private
    * @var Float
*/
var $flQuotaSocio;

//SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setAnoExercicio($valor) { $this->ano_exercicio = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoProcesso($valor) { $this->codigo_processo = $valor; }

/**
    * @access Public
    * @param Float $valor
*/
function setCapitalSocial($valor) { $this->flCapitalSocial = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setQuotaSocios($valor) { $this->flQuotaSocios = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer $valor
*/
function getAnoExercicio() { return $this->ano_exercicio; }

/**
    * @access Public
    * @return Integer $valor
*/
function getCodigoProcesso() { return $this->codigo_processo; }

/**
    * @access Public
    * @return Float
*/
function getCapitalSocial() { return $this->flCapitalSocial; }
/**
    * @access Public
    * @return Float
*/
function getQuotaSocios() { return $this->flQuotaSocios; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMSociedade()
{
    $this->obTCEMProcessoSociedade = new TCEMProcessoSociedade;
    $this->obTransacao     = new Transacao;
    $this->obTCEMSociedade = new TCEMSociedade;
    $this->obRCGM          = new RCGM;
    $this->roRCEMInscricao = &$obRCEMInscricao;
}

/**
    * Metodo para consultar Responsavel Tecnico
    * @access Public
    * @return $obErro boolean
*/
function listarSociedade(&$rsListaSociedade, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }

    if ( $this->roRCEMInscricao->getInscricaoEconomica() ) {
        $stFiltro .= " inscricao_economica = ".$this->roRCEMInscricao->getInscricaoEconomica()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE \r\n\t ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $stOrder = "ORDER BY numcgm";
    $obErro = $this->obTCEMSociedade->recuperaTodos( $rsListaResponsavelTecnico ,$stFiltro, $stOrder, $boTransacao );
    #$this->obTCEMSociedade->debug();

    return $obErro;
}

/**
    * Inclui os dados referentes a Sociedade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirSociedade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMSociedade->setDado( "numcgm"              , $this->obRCGM->getNumCGM() );
        $this->obTCEMSociedade->setDado( "quota_socio"         , $this->getQuotaSocios() );
        $this->obTCEMSociedade->setDado( "inscricao_economica" , $this->roRCEMInscricao->getInscricaoEconomica() );
        $obErro = $this->obTCEMSociedade->inclusao( $boTransacao );
        if (!$obErro->ocorreu() && ($this->getAnoExercicio() != "") && ($this->getCodigoProcesso() != "")) {
            $this->obTCEMProcessoSociedade->setDado( "timestamp", "('now'::text)::timestamp(3)");
            $this->obTCEMProcessoSociedade->setDado("ano_exercicio", $this->getAnoExercicio());
            $this->obTCEMProcessoSociedade->setDado("cod_processo", $this->getCodigoProcesso());
            $this->obTCEMProcessoSociedade->setDado("numcgm", $this->obRCGM->getNumCGM());

            $this->obTCEMProcessoSociedade->setDado("inscricao_economica", $this->roRCEMInscricao->getInscricaoEconomica());

            $obErro = $this->obTCEMProcessoSociedade->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMSociedade );

    return $obErro;
}

/**
    * Exclui os dados referentes a Sociedade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirSociedade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMProcessoSociedade->setDado("inscricao_economica", $this->roRCEMInscricao->getInscricaoEconomica());
        $this->obTCEMProcessoSociedade->setDado( "timestamp", "");
        $obErro = $this->obTCEMProcessoSociedade->exclusao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTCEMSociedade->setDado( "inscricao_economica" , $this->roRCEMInscricao->getInscricaoEconomica() );
            $obErro = $this->obTCEMSociedade->exclusao( $boTransacao );
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMSociedade );

    return $obErro;
}

/**
    * Faz a referencia com um objeto de Inscrição Ecômica
    * @access Public
    * @param Objet objeto de Inscricao Economica
*/
function addInscricao(&$RCEMInscricaoEconomica)
{
    $this->roRCEMInscricao = &$RCEMInscricaoEconomica;
}

}
