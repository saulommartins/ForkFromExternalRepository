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
     * Classe de regra de negócio para corretor
     * Data de Criação: 24/01/2005

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMCorretor.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.4  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCorretor.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php"   );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"  );

class RCIMCorretor extends RCIMCorretagem
{
/**
    * @access Private
    * @var Object
*/
var $obRCGMPessoaFisica;
/**RCGMPessoaFisica.class.php
    * @access Private
    * @var Object
*/
var $roRCIMImobiliaria;
/**
    * @access Private
    * @var Object
*/
var $obTCIMICorretor;

//SETTERS

//GETTERS

/**
     * Método construtor
     * @access Private
*/
function RCIMCorretor()
{
    parent::RCIMCorretagem();
    $this->obTransacao        = new Transacao;
    $this->obTCIMCorretor     = new TCIMCorretor;
    $this->obRCGMPessoaFisica = new RCGMPessoaFisica;
    $this->roRCIMImobiliaria  = new RCIMImobiliaria( $this );
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
    * Seta dados na tabela para inclusao, alteracao e exclusao
    * @access Private
*/
function setarDados()
{
    $this->obTCIMCorretor->setDado( "creci" , $this->stRegistroCreci );
    $this->obTCIMCorretor->setDado( "numcgm", $this->obRCGMPessoaFisica->getNumCGM() );
}

/**
* Inclui os dados setados na tabela de Corretagem
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirCorretor($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirCorretagem( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setarDados();
            $obErro = $this->obTCIMCorretor->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCorretor );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Corretagem
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirCorretor($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->roRCIMImobiliaria->obRCIMCorretor->setRegistroCreci( $this->stRegistroCreci );
        $this->roRCIMImobiliaria->listarImobiliarias( $rsImobiliarias );
        if ( $rsImobiliarias->getNumLinhas() <= 0 ) {
            $this->setarDados();
            $obErro = $this->obTCIMCorretor->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::excluirCorretagem( $boTransacao );
            }
        } else {
            $obErro->setDescricao("Impossível excluir corretor responsável por uma Imobiliária!");
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMCorretor );

    return $obErro;
}

/**
* Lista os Corretores conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarCorretores(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stRegistroCreci) {
        $stFiltro .= " cor.creci = '".$this->stRegistroCreci."' AND ";
    }
    if ( $this->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " cor.numcgm = ".$this->obRCGMPessoaFisica->getNumCGM()." AND ";
    }
    if ( $this->obRCGMPessoaFisica->getNomCGM() ) {
        $stFiltro .= " UPPER (cgm.nom_cgm) like UPPER ('%".$this->obRCGMPessoaFisica->getNomCGM()."%') AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cor.creci ";
    $obErro = $this->obTCIMCorretor->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados do Corretor selecionado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarCorretor($boTransacao = "")
{
    $stFiltro = "";
    if ($this->stRegistroCreci) {
        $stFiltro .= " cor.creci = '".$this->stRegistroCreci."' AND ";
    }
    if ( $this->obRCGMPessoaFisica->getNumCGM() ) {
        $stFiltro .= " cor.numcgm = ".$this->obRCGMPessoaFisica->getNumCGM()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cor.creci ";
    $obErro = $this->obTCIMCorretor->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->stRegistroCreci              = $rsRecordSet->getCampo( "creci"    );
        $this->obRCGMPessoaFisica->setNumCGM( $rsRecordSet->getCampo( "numcgm")  );
        $this->obRCGMPessoaFisica->setNomCGM( $rsRecordSet->getCampo( "nom_cgm") );
    }

    return $obErro;
}

}
