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
     * Classe de regra de negócio para lote urbano
     * Data de Criação: 26/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMLoteUrbano.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.8  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteUrbano.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"      );

//INCLUDE DAS CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"            );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoLoteUrbanoValor.class.php" );

class RCIMLoteUrbano extends RCIMLote
{
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
/**
    * @access Private
    * @var Object
*/
var $obTCIMLoteUrbano;

/**
     * MÃ©todo construtor
     * @access Private
*/
function RCIMLoteUrbano()
{
    parent::RCIMLote();
    $this->obTCIMLoteUrbano = new TCIMLoteUrbano;
    $this->obRCadastroDinamico = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoLoteUrbanoValor );
    $this->obRCadastroDinamico->setCodCadastro( 2 );
}

/**
    * Inclui os dados setados para Lote
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirLote( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMLoteUrbano->setDado( "cod_lote", $this->inCodigoLote );
            $obErro = $this->obTCIMLoteUrbano->inclusao( $boTransacao );
             if ( !$obErro->ocorreu() ) {
                 //O Restante dos valores vem setado da pÃ¡gina de processamento
                 $arChaveAtributoLoteUrbano =  array( "cod_lote" => $this->inCodigoLote );
                 $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLoteUrbano );
                 $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
             }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
    * Altera os dados do Lote Setado
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function alterarLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
         $obErro = parent::alterarLote( $boTransacao );
         if ( !$obErro->ocorreu() ) {
             //O Restante dos valores vem setado da pÃ¡gina de processamento
             $arChaveAtributoLoteUrbano =  array( "cod_lote" => $this->inCodigoLote );
             $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLoteUrbano );
             $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
         }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

/**
    * Exclui os dados de Lote setado
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function excluirLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //O Restante dos valores vem setado da pÃ¡gina de processamento
        $arChaveAtributoLote =  array( "cod_lote" => $this->inCodigoLote );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMLoteUrbano->setDado( "cod_lote", $this->inCodigoLote );
            $obErro = $this->obTCIMLoteUrbano->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::excluirLote( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    //O Restante dos valores vem setado da pÃ¡gina de processamento
                    $arChaveAtributoLoteUrbano =  array( "cod_lote" => $this->inCodigoLote );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLoteUrbano );
                    $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

    return $obErro;
}

function cancelarDesmembramento()
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteParcelado.class.php" );
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMParcelamentoSolo.class.php" );
        $boFlagExcluirParcelamentoSolo = true;
        $obTCIMLoteParcelamentoSolo=new TCIMParcelamentoSolo;
        $obTCIMLoteParcelamentoSolo->setDado('cod_parcelamento',$this->getCodigoParcelamento());
        $obTCIMLoteParcelado = new TCIMLoteParcelado;
        $stFiltro=' where cod_parcelamento='.$this->getCodigoParcelamento();
        $obErro = $obTCIMLoteParcelado->recuperaTodos($rsLotes,$stFiltro,'',$boTransacao);
        if (!$obErro->ocorreu()) {
            while ( !$rsLotes->eof()) {
                if ($rsLotes->getCampo('validado') == 'f' ) {
                    $obTCIMLoteParcelado->setDado('cod_lote',$rsLotes->getCampo('cod_lote'));
                    $obTCIMLoteParcelado->setDado('cod_parcelamento',$rsLotes->getCampo('cod_parcelamento'));
                    $obErro=$obTCIMLoteParcelado->exclusao($boTransacao);
                    if (!$obErro->ocorreu()) {
                        if ( $rsLotes->getCampo('cod_lote') != $this->getCodigoLote() ) {
                            $obRCIMLoteUrbano = new RCIMLoteUrbano;
                            $obRCIMLoteUrbano->setCodigoLote($rsLotes->getCampo('cod_lote'));
                            $obRCIMLoteUrbano->obRCIMLocalizacao->setCodigoLocalizacao($this->obRCIMLocalizacao->getCodigoLocalizacao() );
                            $obErro=$obRCIMLoteUrbano->excluirLote($boTransacao);
                         }
                    }
                } else {
                    $boFlagExcluirParcelamentoSolo = false;
                }
                if ($obErro->ocorreu()) {
                        break;
                } else {
                    $rsLotes->proximo();
                }
            }
            if (!$obErro->ocorreu() and $boFlagExcluirParcelamentoSolo) {
                $obErro=$obTCIMLoteParcelamentoSolo->exclusao($boTransacao);
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLoteParcelamentoSolo );

    return $obErro;
}

 /**
     * Altera os valores dos atributos do Lote setado guardando o histórico
     * @access Public
     * @param  Object $obTransacao ParÃ¢metro Transação
     * @return Object Objeto Erro
 */
 function alterarCaracteristicas($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
        $this->obTCIMLote->setDado( "cod_lote", $this->inCodigoLote );
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCIMLoteProcesso->setDado( "cod_lote"     , $this->inCodigoLote                     );
                $this->obTCIMLoteProcesso->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCIMLoteProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio()      );
                $obErro = $this->obTCIMLoteProcesso->inclusao( $boTransacao );
            }
         //CADASTRO DE ATRIBUTOS
         if ( !$obErro->ocorreu() ) {
             //O Restante dos valores vem setado da pÃ¡gina de processamento
             $arChaveAtributoTrecho = array( "cod_lote" => $this->inCodigoLote );
             $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
             $obErro = $this->obRCadastroDinamico->salvarValoresTimestamp( $boTransacao );
         }
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLote );

     return $obErro;
 }

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLotes(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= "  AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }
    $stOrdem  = " ORDER BY LL.VALOR ";
    $obErro = $this->obTCIMLoteUrbano->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLotesDesmembramento(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= "  AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }
    $stOrdem  = " ORDER BY LL.COD_LOCALIZACAO, VALOR ";
    $obErro = $this->obTCIMLoteUrbano->recuperaRelacionamentoLoteValidado( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os lotes que foram desmembrados e que ainda não foram validados. Só retorna o lote principal do desdobramento.
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDesmembramentosNaoValidados(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = 'AND lote_urbano.cod_lote is not null ';

    return $this->obTCIMLote->recuperaLotesDesmembradosNaoValidados($rsRecordSet,$stFiltro,'',$boTransacao);
}

function verificaBaixaLote(&$rsBaixaImovel, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= "  AND LPAD( UPPER( LL.VALOR) , 10,'0') = LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }

    $this->obTCIMLoteUrbano->recuperaRelacionamentoLoteBaixado( $rsBaixaImovel, $stFiltro, "", $boTransacao );

    return $obErro;
}

/**
    *
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function buscarLoteAglutinar(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->obRCIMLocalizacao->inCodigoLocalizacao) {
        $stFiltro .= " AND LL.COD_LOCALIZACAO = ".$this->obRCIMLocalizacao->inCodigoLocalizacao;
    }
    if ($this->inNumeroLote) {
        $stFiltro .= "  AND LPAD( UPPER( LL.VALOR) , 10,'0') != LPAD( UPPER('".$this->inNumeroLote."'), 10,'0') ";
    }
    $stOrdem  = " ORDER BY VALOR ";
    $obErro = $this->obTCIMLoteUrbano->recuperaRelacionamento( $rsRecordSet, $stFiltro,$stOrdem, $boTransacao );

    return $obErro;
}

}
?>
