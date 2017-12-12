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
    * Extensao de Classe de Regra de Negócio Profissao
    * Data de Criação   : 29/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMProfissao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.6  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"                  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeProfissao.class.php" );

class RCEMProfissao extends RProfissao
{
/**
    * @access Private
    * @var Object
*/
var $roCEMAtividade;

/**
     * Método construtor
     * @access Private
     * @param Object &$obRCEMAtividade Referência a classe Atividade
*/
function RCEMProfissao(&$obRCEMAtividade)
{
    $this->obTransacao              = new Transacao;
    $this->obRProfissao             = new RProfissao;
    $this->obTCEMAtividadeProfissao = new TCEMAtividadeProfissao;
    $this->roRCEMAtividade = &$obRCEMAtividade;
}

/**
    * Inclui dados setados na tabela de atividade_profissao
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirAtividadeProfissao($boFlaTransacao, $boTransacao = "")
{
    $boFlaTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMAtividadeProfissao->setDado( "cod_atividade", $this->roRCEMAtividade->getCodigoAtividade() );
        $this->obTCEMAtividadeProfissao->setDado( "cod_profissao", $this->inCodigoProfissao );
        $obErro = $this->obTCEMAtividadeProfissao->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeProfissao );

    return $obErro;
}

/**
    * Exclui dados setados na tabela de atividade_profissao
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function excluirAtividadeProfissao($boFlaTransacao, $boTransacao = "")
{
    $boFlaTransacao = false;
    $this->obTCEMAtividadeProfissao->setDado( "cod_atividade", $this->roRCEMAtividade->getCodigoAtividade() );
    $this->obTCEMAtividadeProfissao->setDado( "cod_profissao", $this->inCodigoProfissao );
    $obErro = $this->obTCEMAtividadeProfissao->exclusao( $boTransacao );

    return $obErro;
}

/**
    * Lista dados na tabela de atividade_profissao
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function listarAtividadeProfissao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCEMAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND A.COD_ATIVIDADE = ".$this->roRCEMAtividade->getCodigoAtividade()." ";
    }

    if ($this->inCodigoProfissao) {
        $stFiltro .= " AND PR.COD_PROFISSAO = ".$this->inCodigoProfissao." ";
    }

    if ( $this->roRCEMAtividade->getNomeAtividade() ) {
        $stFiltro .= " AND UPPER(A.NOM_ATIVIDADE) LIKE UPPER( '".$this->roRCEMAtividade->getNomeAtividade()."%')";
    }

    $stOrdem = " ORDER BY COD_PROFISSAO ";
    $obErro = $this->obTCEMAtividadeProfissao->recuperaAtividadeProfissao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista dados setados na tabela de atividade_profissao que estão selecionados
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function listarAtividadeProfissaoSelecionados(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCEMAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND AP.COD_ATIVIDADE = ".$this->roRCEMAtividade->getCodigoAtividade()." ";
    }

    if ($this->inCodigoProfissao) {
        $stFiltro .= " AND PR.COD_PROFISSAO = ".$this->inCodigoProfissao." ";
    }

    if ($this->stNomeProfissao) {
        $stFiltro .= " AND UPPER(PR.NOM_PROFISSAO) LIKE UPPER( '".$this->stNomeProfissao."%')";
    }

    $stOrdem = " ORDER BY NOM_PROFISSAO ";
    $obErro = $this->obTCEMAtividadeProfissao->recuperaAtividadeProfissaoSelecionados( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista dados setados na tabela de profissao que não estão selecionados para a atividade
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function listarAtividadeProfissaoDisponiveis(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoProfissao) {
        $stFiltro .= " AND PR.COD_PROFISSAO = ".$this->inCodigoProfissao." ";
    }
    if ( $this->roRCEMAtividade->getCodigoAtividade() ) {
        $stFiltro .= "
        WHERE
            PR.COD_PROFISSAO
        NOT IN
        (
            select AP.cod_profissao from economico.atividade_profissao as AP
            where AP.cod_atividade = ". $this->roRCEMAtividade->getCodigoAtividade() ."
        ) ";
    }

    if ($this->stNomeProfissao) {
        $stFiltro .= " AND UPPER(PR.NOM_PROFISSAO) LIKE UPPER('".$this->stNomeProfissao."%')";
    }

    $stOrdem = " ORDER BY NOM_PROFISSAO ";
    $obErro = $this->obTCEMAtividadeProfissao->recuperaAtividadeProfissaoDisponiveis( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTCEMAtividadeProfissao->debug();
    return $obErro;
}

}
