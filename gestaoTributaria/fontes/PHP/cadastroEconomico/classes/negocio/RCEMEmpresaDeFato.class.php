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
    * Classe de regra de negócio para Empresa de Fato
    * Data de Criação: 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMEmpresaDeFato.class.php 60957 2014-11-26 13:55:58Z michel $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.10  2006/12/20 11:32:41  dibueno
Bug #7874#

Revision 1.9  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomicoEmpresaFato.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoEmpresaFatoValor.class.php"     );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                      );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoElemCadEconomicoValor.class.php");

/**
* Classe de regra de negócio para Empresa de Fato
* Data de Criação: 01/12/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMEmpresaDeFato extends RCEMInscricaoEconomica
{
/**
* @access Private
* @var Object
*/
var $obTCEMCadastroEconomicoEmpresaFato;
/**
* @access Private
* @var Object
*/
var $obRCGM;

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMEmpresaDeFato()
{
    parent::RCEMInscricaoEconomica();
    $this->obTCEMCadastroEconomicoEmpresaFato = new TCEMCadastroEconomicoEmpresaFato;
    $this->obRCadastroDinamico = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCEMAtributoEmpresaFatoValor );
    $this->obRCadastroDinamico->setCodCadastro( 1 );
    $this->obRCGMPessoaFisica                 = new RCGMPessoaFisica;
    $this->obTransacao                        = new Transacao;
}

/**
    * Inclui os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirInscricao($boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMCadastroEconomicoEmpresaFato->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
            $this->obTCEMCadastroEconomicoEmpresaFato->setDado( "numcgm"             , $this->obRCGMPessoaFisica->getNumCGM() );
            
            $stFiltro = " AND coalesce( ef.numcgm, ed.numcgm, au.numcgm ) = ".$this->obRCGMPessoaFisica->getNumCGM();
            $obErro = $this->obTCEMCadastroEconomico->recuperaInscricao($rsInscricao, $stFiltro, "", $boTransacao);
    
            if($rsInscricao->getNumLinhas()>0)
                $obErro->setDescricao("ERROR: CGM ".$this->obRCGMPessoaFisica->getNumCGM()." pertencente a outra Inscrição Econômica. Contate suporte! ");
    
            if ( !$obErro->ocorreu() )
                $obErro = $this->obTCEMCadastroEconomicoEmpresaFato->inclusao( $boTransacao );
            
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributoInscricao = array( "inscricao_economica" => $this->getInscricaoEconomica() );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaFato );

    return $obErro;
}

/**
    * Alterar os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::alterarInscricao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arChaveAtributoInscricao = array ( "inscricao_economica" => $this->getInscricaoEconomica() );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
            $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaFato );

    return $obErro;
}

/**
    * Excluir os dados referentes a Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirInscricao($boTransacao = "")
{
    include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoFaturamento.class.php" );
    $obTARRCEFaturamento = new TARRCadastroEconomicoFaturamento;
    $obTARRCEFaturamento->setDado ( 'inscricao_economica', $this->getInscricaoEconomica() );

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro = " WHERE CEF.inscricao_economica =  ".$this->getInscricaoEconomica();
        $obTARRCEFaturamento->recuperaTimestampCadastroEconomicoFaturamento( $rsLista, $stFiltro, $stOrdem, $boTransacao );
    } else
        $rsLista = new Recordset;

    if ( !$rsLista->eof() ) {
        if ( $rsLista->getCampo("timestamp" ) ) {
            $obErro = new Erro;
            $obErro->setDescricao( "Inscrição Econômica (".$this->getInscricaoEconomica().") apresenta lançamentos contábeis. Não pode ser excluída!" );

            return $obErro;
        }
    }

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arChaveAtributoInscricao = array ( "inscricao_economica" => $this->getInscricaoEconomica() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMCadastroEconomicoEmpresaFato->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
            $obErro = $this->obTCEMCadastroEconomicoEmpresaFato->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::excluirInscricao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoEmpresaFato );

    return $obErro;
}

/**
    * Lista os dados referentes inscrição econômica
    * @access Public
    * @param  Object $rsRecordSet objeto preenchido com os dados retornados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarInscricao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getInscricaoEconomica() ) {
        $stFiltro .= " AND ce.inscricao_economica = ".$this->getInscricaoEconomica();
    }

    if ( $this->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " AND ef.numcgm = ".$this->obRCGMPessoaFisica->getNumCGM();
    }

    $stOrdem  = " order by ce.inscricao_economica ";

    $obErro = $this->obTCEMCadastroEconomicoEmpresaFato->recuperaInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    //$this->obTCEMCadastroEconomicoEmpresaFato->debug();
    return $obErro;
}

/**
    * Adiciona um objeto de CGM Pessoa Física
    * @access Public
*/
function addCGMPessoaFisica()
{
    $this->arCGMPessoaFisica[] = new RCGMPessoaFisica( $this );
}

/**
    * Definir elementos para inscricao economica
    * @access Public
*/
function definirElementos($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
    }
}
}
