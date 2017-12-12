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
     * Classe de regra de negócio para construção outros
     * Data de Criação: 09/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMConstrucaoOutros.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.6  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMDataConstrucao.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucao.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"     );

//INCLUDE DAS CLASSES PARA O TRATAMENTO DOS ATRIBUTOS DINAMICOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"         );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoConstrucaoOutrosValor.class.php" );

class RCIMConstrucaoOutros extends RCIMConstrucao
{
/**
* @access Private
* @var String
*/
var $stDescricao;
/**
* @access Private
* @var Object
*/
var $stTipoVinculo;
/**
* @acess Private
* @var String
*/
var $stDtConstrucao;
/**
* @access Private
* @var Object
*/
var $obTCIMConstrucaoOutros;
/**
    * @access Private
    * @var Object
*/
var $obRCIMCondominio;
/**
    * @access Private
    * @var Object
*/
var $obRCIMImovel;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
//SETTERS
/**
* @access Public
* @param String $valor
*/
function setTipoVinculo($valor) { $this->stTipoVinculo = $valor; }
/**
* @access Public
* @param String $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor;   }
/**
* @access Public
*param String Valor
*/
function setDataConstrucao($valor) { $this->stDtConstrucao = $valor; }
//GETTERS
/**
* @access Public
* @return String
*/
function getTipoVinculo() { return $this->stTipoVinculo; }
/**
* @access Public
* @return String
*/
function getDescricao() { return $this->stDescricao;   }
/**
* @access Public
* @return String
*/
function getDataConstrucao() {return $this->stDtConstrucao; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCIMConstrucaoOutros()
{
    parent::RCIMConstrucao();
    $this->obTCIMConstrucaoOutros = new TCIMConstrucaoOutros;
    $this->obRCIMCondominio       = new RCIMCondominio;
    $this->obTCIMDataConstrucao   = new TCIMDataConstrucao;
    $this->obRCIMImovel           = new RCIMImovel( new RCIMLote );
    $this->obRCadastroDinamico    = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoConstrucaoOutrosValor );
    $this->obRCadastroDinamico->setCodCadastro( 9 );
    $this->obVCIMConstrucaoOutros = new VCIMConstrucaoOutros;
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Construcao Outros
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirConstrucao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoOutros->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $this->obTCIMConstrucaoOutros->setDado( "descricao"      , $this->stDescricao        );
            $obErro = $this->obTCIMConstrucaoOutros->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributoConstrucaoOutros =  array( "cod_construcao" => $this->inCodigoConstrucao );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucaoOutros );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
           }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoOutros );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Construcao Outros
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::alterarConstrucao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoOutros->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $this->obTCIMConstrucaoOutros->setDado( "descricao"      , $this->stDescricao        );
            $obErro = $this->obTCIMConstrucaoOutros->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributoConstrucaoOutros =  array( "cod_construcao" => $this->inCodigoConstrucao );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucaoOutros );
                $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoOutros );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Construcao Outros
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arChaveAtributoConstrucaoOutros =  array( "cod_construcao" => $this->inCodigoConstrucao );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucaoOutros );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoOutros->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $obErro = $this->obTCIMConstrucaoOutros->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::excluirConstrucao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoOutros );

    return $obErro;
}

/**
    * Altera os valores dos atributos da Construção setada guardando o histórico
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarCaracteristicas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //CADASTRO DE ATRIBUTOS
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
               $this->obTCIMConstrucaoProcesso->setDado( "cod_construcao"     , $this->inCodigoConstrucao               );
               $this->obTCIMConstrucaoProcesso->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
               $this->obTCIMConstrucaoProcesso->setDado( "exercicio"      , $this->obRProcesso->getExercicio()      );
               $this->obTCIMConstrucaoProcesso->inclusao( $boTransacao );
            }
            //O Restante dos valores vem setado da página de processamento
            $arChaveAtributoConstrucaoOutros =  array( "cod_construcao" => $this->inCodigoConstrucao );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucaoOutros );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            //$this->obRCadastroDinamico->obPersistenteValores->debug();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoOutros );

    return $obErro;
}

/**
* Lista as Construcoes conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarConstrucoes(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " cod_construcao = ".$this->inCodigoConstrucao." AND ";
    }
    if ($this->stTipoVinculo) {
        $stFiltro .= " tipo_vinculo = ".$this->stTipoVinculo." AND ";
    }
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " imovel_cond = ".$this->obRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
        $stFiltro .= " imovel_cond = ".$this->obRCIMCondominio->getCodigoCondominio()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_construcao ";
    $obErro = $this->obVCIMConstrucaoOutros->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarProcessos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoConstrucao()) {
        $stFiltro .= " cp.cod_construcao = ".$this->getCodigoConstrucao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY cp.timestamp";
    $obErro = $this->obTCIMConstrucaoOutros->recuperaRelacionamentoProcesso( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Construcao selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarConstrucao($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " cod_construcao = ".$this->inCodigoConstrucao." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_construcao ";
    $obErro = $this->obVCIMConstrucaoOutros->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $rsRecordSet->addFormatacao( "area_real", "NUMERIC_BR" );
        $this->inCodigoConstrucao = $rsRecordSet->getCampo( "cod_construcao" );
        $this->flAreaConstruida   = $rsRecordSet->getCampo( "area_real"      );
        $this->tmTimestampConstrucao = $rsRecordSet->getCampo( "timestamp"      );
        $this->stDescricao        = $rsRecordSet->getCampo( "descricao"      );
        $this->obTCIMConstrucaoProcesso->setDado( "cod_construcao", $this->inCodigoConstrucao );
        $obErro = $this->obTCIMConstrucaoProcesso->recuperaPorChave( $rsProcesso, $boTransacao );
        if ( !$obErro->ocorreu() and !$rsProcesso->eof() ) {
            $this->obRProcesso->setCodigoProcesso( $rsProcesso->getCampo( "cod_processo") );
            $this->obRProcesso->setExercicio( $rsProcesso->getCampo( "exercicio" ) );
        }
    }

    return $obErro;
}

/**
* Recupera a área da construção ou edificação ligada a Construção/Edificação vinculada a Condomínio
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function buscaAreaConstrucaoCondominio(&$flAreaConstrucao)
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= "AC.cod_construcao = ".$this->inCodigoConstrucao." AND ";
    }
    if ( parent::getTimestampConstrucao() ) {
        $stFiltro .= "AC.timestamp = '".parent::getTimestampConstrucao()."' AND";
    } else {
        $stFiltro .= "AC.timestamp = ( SELECT max(timestamp) FROM imobiliario.area_construcao WHERE cod_construcao = ".$this->inCodigoConstrucao." ) AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro = $this->obTCIMConstrucaoOutros->recuperaAreaConstrucaoCondominio( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    $rsRecordSet->addFormatacao( 'area_real' , 'NUMERIC_BR' );
    $flAreaConstrucao = $rsRecordSet->getCampo('area_real');

    return $obErro;
}

/**
* Inclui os dados setados na tabela de Construcao Outros(Reforma)
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirReforma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirReforma( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arChaveAtributoConstrucaoOutros =  array( "cod_construcao" => $this->inCodigoConstrucao );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucaoOutros );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoOutros );

    return $obErro;
}

}
