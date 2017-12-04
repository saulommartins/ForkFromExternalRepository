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
     * Classe de regra de negócio para natureza de transferência
     * Data de Criação: 11/10/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Vitor Davi Valentini

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMNaturezaTransferencia.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.16
*/

/*
$Log$
Revision 1.6  2007/03/08 21:22:11  rodrigo
Bug #8438#

Revision 1.5  2007/03/01 15:09:13  rodrigo
Bug #8438#

Revision 1.4  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMNaturezaTransferencia.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMDocumentoNatureza.class.php"     );

class RCIMNaturezaTransferencia
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNatureza;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoDocumento;
/**
    * @access Private
    * @var String
*/
var $stDescricaoNatureza;
/**
    * @access Private
    * @var Boolean
*/
var $boAutomaticaNatureza;
/**
    * @access Private
    * @var Array
*/
var $arDocumentosNatureza;
/**
    * @access Private
    * @var Array
*/
var $arDocumentosInterface;
/**
    * @access Private
    * @var Object
*/
var $obTCIMNaturezaTransferencia;
/**
    * @access Private
    * @var Object
*/
var $obTCIMDocumentoNatureza;
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoNatureza($valor) { $this->inCodigoNatureza     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoDocumento($valor) { $this->inCodigoDocumento    = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricaoNatureza($valor) { $this->stDescricaoNatureza  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAutomaticaNatureza($valor) { $this->boAutomaticaNatureza = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setDocumentosNatureza($valor) { $this->arDocumentosNatureza = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setDocumentosInterface($valor) { $this->arDocumentosInterface = $valor; }
/**
    * @access Public
    * @return Integer
*/
function getCodigoNatureza() { return $this->inCodigoNatureza;      }
/**
    * @access Public
    * @return Integer
*/
function getCodigoDocumento() { return $this->inCodigoDocumento;     }
/**
    * @access Public
    * @return String
*/
function getDescricaoNatureza() { return $this->stDescricaoNatureza;   }
/**
    * @access Public
    * @return Boolean
*/
function getAutomaticaNatureza() { return $this->boAutomaticaNatureza;  }
/**
    * @access Public
    * @return Array
*/
function getDocumentosNatureza() { return $this->arDocumentosNatureza;  }
/**
    * @access Public
    * @return Array
*/
function getDocumentosInterface() { return $this->arDocumentosInterface; }
/**
     * Método construtor
     * @access Private
*/
function RCIMNaturezaTransferencia()
{
    $this->obTCIMNaturezaTransferencia = new TCIMNaturezaTransferencia;
    $this->obTCIMDocumentoNatureza     = new TCIMDocumentoNatureza;
    $this->obTransacao                 = new Transacao;
}
/**
    * Inclui os dados setados na tabela de Natureza Transferência
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirNaturezaTransferencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaDescricaoNatureza( );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTCIMNaturezaTransferencia->proximoCod( $this->inCodigoNatureza, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMNaturezaTransferencia->setDado( "cod_natureza", $this->inCodigoNatureza     );
                $this->obTCIMNaturezaTransferencia->setDado( "descricao"   , $this->stDescricaoNatureza  );
                $this->obTCIMNaturezaTransferencia->setDado( "automatica"  , $this->boAutomaticaNatureza );
                $obErro = $this->obTCIMNaturezaTransferencia->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->salvarDocumentosNatureza( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMNaturezaTransferencia );

    return $obErro;
}
/**
    * Altera os dados da Natureza de Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarNaturezaTransferencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMNaturezaTransferencia->setDado( "cod_natureza", $this->inCodigoNatureza     );
        $this->obTCIMNaturezaTransferencia->setDado( "descricao"   , $this->stDescricaoNatureza  );
        $this->obTCIMNaturezaTransferencia->setDado( "automatica"  , $this->boAutomaticaNatureza );
        $obErro = $this->obTCIMNaturezaTransferencia->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->salvarDocumentosNatureza( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMNaturezaTransferencia );

    return $obErro;
}
/**
    * Exclui a Natureza da Transferência selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirNaturezaTransferencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMDocumentoNatureza->setCampoCod( "cod_natureza" );
        $this->obTCIMDocumentoNatureza->setDado( "cod_natureza", $this->inCodigoNatureza );
        $obErro = $this->obTCIMDocumentoNatureza->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMDocumentoNatureza->setCampoCod( "cod_documento" );
            $this->obTCIMNaturezaTransferencia->setDado( "cod_natureza", $this->inCodigoNatureza );
            $obErro = $this->obTCIMNaturezaTransferencia->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMNaturezaTransferencia );

    return $obErro;
}
/**
    * Recupera do banco de dados os dados da Natureza de Transferência selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarNaturezaTransferencia($boTransacao = "")
{
    $this->obTCIMNaturezaTransferencia->setDado( "cod_natureza", $this->inCodigoNatureza );
    $obErro = $this->obTCIMNaturezaTransferencia->recuperaPorChave( $rsNaturezaTransferencia, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stDescricaoNatureza  = $rsNaturezaTransferencia->getCampo( "descricao"  );
        $this->boAutomaticaNatureza = $rsNaturezaTransferencia->getCampo( "automatica" );
    }

    return $obErro;
}
/**
    * Recupera do banco de dados os dados de Documentos da Natureza de Transferência selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarDocumentosNatureza($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoNatureza) {
        $stFiltro .= " COD_NATUREZA = ".$this->inCodigoNatureza." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY COD_DOCUMENTO";
    $obErro = $this->obTCIMDocumentoNatureza->recuperaTodos( $rsDocumentosNatureza, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arConteudoDocumentos = array();
        $inId                 = 0;
        while ( !$rsDocumentosNatureza->eof() ) {
            $arConteudoDocumentos[ "inId"        ] =   ++$inId;
            $arConteudoDocumentos[ "codigo"      ] =   $rsDocumentosNatureza->getCampo( "cod_documento" );
            $arConteudoDocumentos[ "nome"        ] =   $rsDocumentosNatureza->getCampo( "nom_documento" );
            $arConteudoDocumentos[ "obrigatorio" ] = ( $rsDocumentosNatureza->getCampo( "cadastro" ) == 't' ? 'Cadastro' : ( $rsDocumentosNatureza->getCampo( "transferencia" ) == 't' ? 'Efetivação' : 'Não' ) );
            $arConteudoDocumentos[ "entregue"    ] = 'f';
            $this->arDocumentosNatureza[] = $arConteudoDocumentos;
            $rsDocumentosNatureza->proximo();
        }
    }

    return $obErro;
}
/**
    * Lista os dados da Natureza de Transferência não utilizadas selecionado no banco de dados
    * @access Public
    * @param  Object $rsNaturezaTransferencia Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNaturezaTransferenciaExcluir(&$rsNaturezaTransferencia, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoNatureza) {
        $stFiltro .= " COD_NATUREZA = ".$this->inCodigoNatureza." AND";
    }
    if ($this->stDescricaoNatureza) {
        $stFiltro .= " DESCRICAO = '".$this->stDescricaoNatureza."' AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    ( $stFiltro ) ? $prm = " AND " : $prm = " WHERE ";

    $stFiltro.=$prm." NOT EXISTS ( SELECT transferencia_imovel                                                      \n";
    $stFiltro.= "                    FROM imobiliario.transferencia_imovel                                          \n";
    $stFiltro.= "                   WHERE transferencia_imovel.cod_natureza = natureza_transferencia.cod_natureza ) \n";

    $stOrdem  = " ORDER BY DESCRICAO ";
    $obErro = $this->obTCIMNaturezaTransferencia->recuperaTodos( $rsNaturezaTransferencia, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
/**
    * Lista os dados da Natureza de Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $rsNaturezaTransferencia Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNaturezaTransferencia(&$rsNaturezaTransferencia, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoNatureza) {
        $stFiltro .= " COD_NATUREZA = ".$this->inCodigoNatureza." AND";
    }
    if ($this->stDescricaoNatureza) {
        $stFiltro .= " DESCRICAO = '".$this->stDescricaoNatureza."' AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    ( $stFiltro ) ? $prm = " AND " : $prm = " WHERE ";

    $stOrdem  = " ORDER BY DESCRICAO ";
    $obErro = $this->obTCIMNaturezaTransferencia->recuperaTodos( $rsNaturezaTransferencia, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Verifica se a Natureza a ser incluida já não existe
    * @access Public
    * @param  Object $rsNaturezaTransferencia Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaDescricaoNatureza($boTransacao = "")
{
    $stFiltro = " DESCRICAO = '".$this->stDescricaoNatureza."' AND";
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $this->obTCIMNaturezaTransferencia->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( $rsRecordSet->getNumLinhas() > 0 && $rsRecordSet->getCampo("descricao") == $this->stDescricaoNatureza ) {
        $obErro->setDescricao("Natureza já cadastrada. ($this->stDescricaoNatureza)");
    }

    return $obErro;
}

/**
    * Altera os dados da Natureza de Transferência selecionado no banco de dados
    * @access Public
    * @param  Object $rsNaturezaTransferencia Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarDocumentosNatureza($boTransacao = "")
{
    $obErro = $this->consultarDocumentosNatureza( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inTotalArray = count( $this->arDocumentosInterface ) - 1;
        for ($inCount = 0; $inCount <= $inTotalArray; $inCount ++) {
             if ($this->arDocumentosInterface[ $inCount ][ 'codigo' ] == 0 and $this->arDocumentosInterface[ $inCount ][ 'nome' ] != '') {
                 $obErro = $this->obTCIMDocumentoNatureza->proximoCod( $this->inCodigoDocumento, $boTransacao );
                 if ( !$obErro->ocorreu() ) {
                     $this->obTCIMDocumentoNatureza->setDado( "cod_documento" ,   $this->inCodigoDocumento                                             );
                     $this->obTCIMDocumentoNatureza->setDado( "cod_natureza"  ,   $this->inCodigoNatureza                                              );
                     $this->obTCIMDocumentoNatureza->setDado( "nom_documento" ,   $this->arDocumentosInterface[$inCount]["nome"]                             );
                     $this->obTCIMDocumentoNatureza->setDado( "cadastro"      , ( $this->arDocumentosInterface[$inCount]["obrigatorio"] == 'Cadastro' ? 't' : 'f' ) );
                     $this->obTCIMDocumentoNatureza->setDado( "transferencia" , ( $this->arDocumentosInterface[$inCount]["obrigatorio"] == 'Efetivação' ? 't' : 'f' ) );
                     $obErro = $this->obTCIMDocumentoNatureza->inclusao( $boTransacao );
                 }
            }
        }
        $inTotalArray     = count( $this->arDocumentosNatureza  ) - 1;
        $inTotalInterface = count( $this->arDocumentosInterface ) - 1;
        for ($inCount = 0; $inCount <= $inTotalArray; $inCount ++) {
             $boexcluirDocumento = 't';
             for ($inCountInterface = 0; $inCountInterface <= $inTotalInterface; $inCountInterface ++) {
                  if ($this->arDocumentosNatureza[ $inCount ][ 'codigo' ] == $this->arDocumentosInterface[ $inCountInterface ][ 'codigo' ]) {
                      $boexcluirDocumento = 'f';
                  }
             }
             if ($boexcluirDocumento == 't') {
                 $this->obTCIMDocumentoNatureza->setDado( "cod_documento", $this->arDocumentosNatureza[ $inCount ][ 'codigo' ] );
                 $obErro = $this->obTCIMDocumentoNatureza->exclusao( $boTransacao );
             }
        }
    }

    return $obErro;
}
}
?>
