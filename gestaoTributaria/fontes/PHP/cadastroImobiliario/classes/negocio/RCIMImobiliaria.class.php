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
     * Classe de regra de negócio para imobiliaria
     * Data de Criação: 18/01/2005

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMImobiliaria.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.4  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImobiliaria.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretor.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"   );

class RCIMImobiliaria extends RCIMCorretagem
{
/**
    * @access Private
    * @var Object
*/
var $obRCIMCorretor;
/**
    * @access Private
    * @var Object
*/
var $obRCGMPessoaJuridica;
/**
    * @access Private
    * @var Object
*/
var $obTCIMImobiliaria;

//SETTERS

//GETTERS

/**
     * Método construtor
     * @access Private
*/
function RCIMImobiliaria(&$obRCIMCorretor)
{
    parent::RCIMCorretagem();
    $this->obTransacao          = new Transacao;
    $this->obTCIMImobiliaria    = new TCIMImobiliaria;
    $this->obRCIMCorretor       = &$obRCIMCorretor;
    $this->obRCGMPessoaJuridica = new RCGMPessoaJuridica;
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
    * Seta dados na tabela para inclusao, alteracao e exclusao
    * @access Private
*/
function setarDados()
{
    $this->obTCIMImobiliaria->setDado( "creci"      , $this->stRegistroCreci                   );
    $this->obTCIMImobiliaria->setDado( "responsavel", $this->obRCIMCorretor->stRegistroCreci   );
    $this->obTCIMImobiliaria->setDado( "numcgm"     , $this->obRCGMPessoaJuridica->getNumCGM() );
}

/**
* Inclui os dados setados na tabela de Imobiliaria
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirImobiliaria($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirCorretagem( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setarDados();
            $obErro = $this->obTCIMImobiliaria->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMImobiliaria );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Imobiliaria
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarImobiliaria($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setarDados();
        $obErro = $this->obTCIMImobiliaria->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMImobiliaria );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Imobiliaria
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirImobiliaria($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setarDados();
        $obErro = $this->obTCIMImobiliaria->exclusao( $boTransacao );
        $obErro = parent::excluirCorretagem( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = parent::excluirCorretagem( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMImobiliaria );

    return $obErro;
}

/**
* Lista as Imobiliarias conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarImobiliarias(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stRegistroCreci) {
        $stFiltro .= " imo.creci = '".$this->stRegistroCreci."' AND ";
    }
    if ( $this->obRCGMPessoaJuridica->getNumCGM() ) {
        $stFiltro .= " imo.numcgm = ".$this->obRCGMPessoaJuridica->getNumCGM()." AND ";
    }
    if ( $this->obRCGMPessoaJuridica->getNomCGM() ) {
        $stFiltro .= " UPPER (cgm.nom_cgm) like UPPER ('%".$this->obRCGMPessoaJuridica->getNomCGM()."%') AND ";
    }
    if ( $this->obRCIMCorretor->getRegistroCreci() ) {
        $stFiltro .= " imo.responsavel = '".$this->obRCIMCorretor->getRegistroCreci()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY imo.creci ";
    $obErro = $this->obTCIMImobiliaria->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Imobiliaria selecionado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarImobiliaria($boTransacao = "")
{
    $stFiltro = "";
    if ($this->stRegistroCreci) {
        $stFiltro .= " imo.creci = '".$this->stRegistroCreci."' AND ";
    }
    if ( $this->obRCGMPessoaJuridica->getNumCGM() ) {
        $stFiltro .= " imo.numcgm = ".$this->obRCGMPessoaJuridica->getNumCGM()." AND ";
    }
    if ( $this->obRCIMCorretor->getRegistroCreci() ) {
        $stFiltro .= " imo.responsavel = '".$this->obRCIMCorretor->getRegistroCreci()."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY imo.creci ";
    $obErro = $this->obTCIMImobiliaria->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->stRegistroCreci                              = $rsRecordSet->getCampo( "creci"        );
        $this->obRCGMPessoaJuridica->setNumCGM              ( $rsRecordSet->getCampo( "numcgm"     ) );
        $this->obRCGMPessoaJuridica->setNomCGM              ( $rsRecordSet->getCampo( "nom_cgm"    ) );
        $this->obRCIMCorretor->setRegistroCreci             ( $rsRecordSet->getCampo( "responsavel") );
        $this->obRCIMCorretor->obRCGMPessoaFisica->setNumCGM( $rsRecordSet->getCampo( "cgm_resp"   ) );
        $this->obRCIMCorretor->obRCGMPessoaFisica->setNomCGM( $rsRecordSet->getCampo( "nome_resp"  ) );
    }

    return $obErro;
}

}
