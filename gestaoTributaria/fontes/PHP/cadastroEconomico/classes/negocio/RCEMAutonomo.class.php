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
    * Classe de regra de negócio para Autonomo
    * Data de Criação: 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMAutonomo.class.php 60957 2014-11-26 13:55:58Z michel $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.9  2007/04/19 21:14:53  rodrigo
Bug #9031#

Revision 1.8  2006/12/20 11:32:46  dibueno
Bug #7874#

Revision 1.7  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomicoAutonomo.class.php"    );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoCadEconAutonomoValor.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                      );

/**
* Classe de regra de negócio para Autonomo
* Data de Criação: 01/12/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMAutonomo extends RCEMInscricaoEconomica
{
/**
* @access Private
* @var Object
*/
var $obTCEMCadastroEconomicoAutonomo;
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
function RCEMAutonomo()
{
    parent::RCEMInscricaoEconomica();
    $this->obTransacao                     = new Transacao;
    $this->obTCEMCadastroEconomicoAutonomo = new TCEMCadastroEconomicoAutonomo;
    $this->obRCGMPessoaFisica              = new RCGMPessoaFisica;
    $this->obTransacao                     = new Transacao;
    $this->obRCadastroDinamico             = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCEMAtributoCadEconAutonomoValor );
    $this->obRCadastroDinamico->setCodCadastro( 3 );

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
        $obErro = parent::incluirInscricao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMCadastroEconomicoAutonomo->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
            $this->obTCEMCadastroEconomicoAutonomo->setDado( "numcgm"             , $this->obRCGMPessoaFisica->getNumCGM() );
            
            $stFiltro = " AND coalesce( ef.numcgm, ed.numcgm, au.numcgm ) = ".$this->obRCGMPessoaFisica->getNumCGM();
            $obErro = $this->obTCEMCadastroEconomico->recuperaInscricao($rsInscricao, $stFiltro, "", $boTransacao);
    
            if($rsInscricao->getNumLinhas()>0)
                $obErro->setDescricao("ERROR: CGM ".$this->obRCGMPessoaFisica->getNumCGM()." pertencente a outra Inscrição Econômica. Contate suporte! ");
    
            if ( !$obErro->ocorreu() )            
                $obErro = $this->obTCEMCadastroEconomicoAutonomo->inclusao( $boTransacao );
                
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributoInscricao = array( "inscricao_economica" => $this->getInscricaoEconomica() );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoAutonomo );

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
        $this->obTCEMCadastroEconomicoAutonomo->setDado("inscricao_economica",$this->getInscricaoEconomica());
        $this->obTCEMCadastroEconomicoAutonomo->setDado("numcgm",$this->obRCGMPessoaFisica->getNumCGM()     );
        $this->obTCEMCadastroEconomicoAutonomo->alteracao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoAutonomo );

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
        $obErro = new Erro;
        $obErro->setDescricao( "Inscrição Econômica (".$this->getInscricaoEconomica().") apresenta lançamentos contábeis. Não pode ser excluída!" );

        return $obErro;
    }

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arChaveAtributoInscricao = array( "inscricao_economica" => $this->getInscricaoEconomica() );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMCadastroEconomicoAutonomo->setDado( "inscricao_economica", $this->getInscricaoEconomica() );
            $obErro = $this->obTCEMCadastroEconomicoAutonomo->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::excluirInscricao();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoAutonomo );

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
        $stFiltro .= " and ce.inscricao_economica = ".$this->getInscricaoEconomica();
    }
    if ( $this->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " and cgm.numcgm = ".$this->obRCGMPessoaFisica->getNumCGM();
    }

    $stOrdem  = " order by ce.inscricao_economica ";

    $obErro = $this->obTCEMCadastroEconomicoAutonomo->recuperaInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
