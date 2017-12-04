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
    * Classe de regra de negócio para Edificação
    * Data de Criação   : 11/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCIMEdificacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.18  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucaoEdificacao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoEdificacao.class.php"       );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMConstrucaoEdificacao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucaoProcesso.class.php"   );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMDataConstrucao.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTipoEdificacao.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucao.class.php"                );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"                      );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                      );
//include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"           );
//include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeDependente.class.php"         );
//INCLUDE DAS CLASSES PARA O TRATAMENTO DOS ATRIBUTOS DINAMICOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"             );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoEdificacaoValor.class.php" );

/**
* Classe de regra de negócio para Edificação
* Data de Criação: 11/11/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCIMEdificacao extends RCIMConstrucao
{
/**
* @access Private
* @var Integer
*/
var $inCodigoTipo;
/**
* @access Private
* @var Object
*/
var $stTipoVinculo;
/**
* @access Private
* @var Object
*/
var $stDtConstrucao;
/**
* @access Private
* @var Object
*/
var $stTipoUnidade;
/**
* @access Private
* @var Boolean
*/
var $boListarBaixadas;
/**
* @access Private
* @var Object
*/
var $obTCIMConstrucaoEdificacao;
/**
* @access Private
* @var Object
*/
var $obTCIMTipoEdificacao;
/**
* @access Private
* @var Object
*/
var $obTCIMDataConstrucao;

/**
* @access Private
* @var Object
*/
var $obVCIMConstrucaoEdificacao;
/**
* @access Private
    * @var Object
*/
var $obRCIMTipoEdificacao;
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
* @param Integer $valor
*/
function setCodigoTipo($valor) { $this->inCodigoTipo = $valor;  }
/**
* @access Public
* @param String $valor
*/
function setTipoVinculo($valor) { $this->stTipoVinculo = $valor; }
/**
* @access Public
* @param String $valor
*/
function setDataConstrucao($valor) { $this->stDtConstrucao = $valor; }

//GETTERS
/**
* @access Public
* @return String
*/
function getCodigoTipo() { return $this->inCodigoTipo;  }
/**
* @access Public
* @return String
*/
function getTipoVinculo() { return $this->stTipoVinculo; }
/**
* @access Public
* @return String
*/
function getDataConstrucao() { return $this->stDtConstrucao; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCIMEdificacao()
{
    parent::RCIMConstrucao();
    $this->obTCIMConstrucaoEdificacao = new TCIMConstrucaoEdificacao;
    $this->obTCIMTipoEdificacao       = new TCIMTipoEdificacao;
    $this->obTCIMConstrucaoProcesso   = new TCIMConstrucaoProcesso;
    $this->obTCIMDataConstrucao        = new TCIMDataConstrucao;
    $this->obVCIMConstrucaoEdificacao = new VCIMConstrucaoEdificacao;
    $this->obRCIMTipoEdificacao       = new RCIMTipoEdificacao;
    $this->obRCIMImovel               = new RCIMImovel( new RCIMLote );
    $this->obRCadastroDinamico        = new RCadastroDinamico;
    $this->obRProcesso                = new RProcesso;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoTipoEdificacaoValor );
    $this->obRCadastroDinamico->setPersistenteAtributos( new TCIMAtributoTipoEdificacao );
    $this->obRCadastroDinamico->setCodCadastro( 5 );
    $this->boListarBaixadas = false;
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
* Inclui os dados setados na tabela de Edificação
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirEdificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::incluirConstrucao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoEdificacao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $this->obTCIMConstrucaoEdificacao->setDado( "cod_tipo"       , $this->inCodigoTipo       );
            $obErro = $this->obTCIMConstrucaoEdificacao->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
            $arChaveAtributoEdificacao =  array( "cod_construcao" => $this->inCodigoConstrucao,
                                                 "cod_tipo"       => $this->inCodigoTipo       );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEdificacao );
                $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoEdificacao );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Edificação
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarEdificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = parent::alterarConstrucao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoEdificacao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $this->obTCIMConstrucaoEdificacao->setDado( "cod_tipo"       , $this->inCodigoTipo       );
            $obErro = $this->obTCIMConstrucaoEdificacao->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributoEdificacao =  array( "cod_construcao" => $this->inCodigoConstrucao,
                                                     "cod_tipo"       => $this->inCodigoTipo       );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEdificacao );
                $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoEdificacao );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Edificação
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirEdificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $arChaveAtributoEdificacao =  array( "cod_construcao" => $this->inCodigoConstrucao,
                                             "cod_tipo"       => $this->inCodigoTipo       );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEdificacao );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoEdificacao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $this->obTCIMConstrucaoEdificacao->setDado( "cod_tipo"       , $this->inCodigoTipo       );
            $obErro = $this->obTCIMConstrucaoEdificacao->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::excluirConstrucao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoEdificacao );

    return $obErro;
}

/**
    * Altera os valores dos atributos da Edificação setada guardando o histórico
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarCaracteristicas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
               $this->obTCIMConstrucaoProcesso->setDado( "cod_construcao"     , $this->inCodigoConstrucao               );
               $this->obTCIMConstrucaoProcesso->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
               $this->obTCIMConstrucaoProcesso->setDado( "exercicio"      , $this->obRProcesso->getExercicio()      );
               $this->obTCIMConstrucaoProcesso->inclusao( $boTransacao );
            }

            $arChaveAtributoEdificacao =  array( "cod_construcao" => $this->inCodigoConstrucao,
                                                 "cod_tipo"       => $this->inCodigoTipo       );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEdificacao );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoEdificacao );

    return $obErro;
}

/**
* Lista as Edificações conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarEdificacoes(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " cod_construcao = ".$this->inCodigoConstrucao." AND ";
    }
    if ($this->inCodigoTipo) {
        $stFiltro .= " cod_tipo = ".$this->inCodigoTipo." AND ";
    }
    if ($this->stTipoVinculo) {
        if ($this->stTipoVinculo == "Condomínio") {
            $stFiltro .= " tipo_vinculo = 'Condomínio' AND ";
        } elseif ($this->stTipoVinculo == "Imóvel") {
            if ( $this->getTipoUnidade() == 'Autonoma' ) {
                $stFiltro .= " ( tipo_vinculo = 'Autônoma' ) AND ";
            } else {
                $stFiltro .= " ( tipo_vinculo = 'Autônoma' OR tipo_vinculo = 'Dependente' ) AND ";
            }
        }
    }
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " imovel_cond = ".$this->obRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
        $stFiltro .= " imovel_cond = ".$this->obRCIMCondominio->getCodigoCondominio()." AND ";
    }

    if ($this->boListarBaixadas == false) {
       //assim mostra apenas ativas
       $stFiltro .= " ((data_baixa IS NULL) OR (data_baixa IS NOT NULL AND data_reativacao IS NOT NULL)) AND ";
    } else {
       //assim mostra apenas baixadas
       $stFiltro .= " data_baixa IS NOT NULL AND data_reativacao IS NULL AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_construcao ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaEdificacoes( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCIMConstrucaoEdificacao->debug();
    return $obErro;
}

/**
* Lista as Edificações conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarEdificacoesImovel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " AND ve.cod_construcao = ".$this->inCodigoConstrucao;
    }
    if ($this->inCodigoTipo) {
        $stFiltro .= " AND ve.cod_tipo = ".$this->inCodigoTipo;
    }
    if ( $this->getTipoUnidade() == 'Autonoma' ) {
        $stFiltro .= " AND ( ve.tipo_vinculo = 'Autônoma' ) ";
    } else {
        $stFiltro .= " AND ( ve.tipo_vinculo = 'Autônoma' OR ve.tipo_vinculo = 'Dependente' ) ";
    }

    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " AND via.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao();
    }
    if ( $this->obRCIMImovel->roRCIMLote->getNumeroLote() ) {
        $stFiltro .= " AND LPAD( UPPER( LL.VALOR) , 10,'0') = ";
        $stFiltro .= " LPAD( UPPER('".$this->obRCIMImovel->roRCIMLote->getNumeroLote()."'), 10,'0') ";
    }
    if ( $this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " AND vla.cod_localizacao = ".$this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao();
    }

    if ($this->boListarBaixadas == false) {
       //assim mostra apenas ativas
       $stFiltro .= " AND ((data_baixa IS NULL) OR (data_baixa IS NOT NULL AND data_reativacao IS NOT NULL)) ";
    } else {
       //assim mostra apenas baixadas
       $stFiltro .= " AND data_baixa IS NOT NULL AND data_reativacao IS NULL ";
    }

    $stOrder = " ORDER BY ve.cod_construcao ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaRelacionamentoConsulta( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCIMConstrucaoEdificacao->debug();
    return $obErro;
}

/**
* Lista as Edificações conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarEdificacoesImovelAlteracao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " AND ve.cod_construcao = ".$this->inCodigoConstrucao;
    }
    if ($this->inCodigoTipo) {
        $stFiltro .= " AND ve.cod_tipo = ".$this->inCodigoTipo;
    }
    /*
    if ( $this->getTipoUnidade() == 'Autonoma' ) {
        $stFiltro .= " AND ( ve.tipo_vinculo = 'Autônoma' ) ";
    } else {
        $stFiltro .= " AND ( ve.tipo_vinculo = 'Autônoma' OR ve.tipo_vinculo = 'Dependente' ) ";
    }
    */
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " AND ve.imovel_cond = ".$this->obRCIMImovel->getNumeroInscricao();
    }
    if ( $this->obRCIMImovel->roRCIMLote->getNumeroLote() ) {
        $stFiltro .= " AND LPAD( UPPER( LL.VALOR) , 10,'0') = ";
        $stFiltro .= " LPAD( UPPER('".$this->obRCIMImovel->roRCIMLote->getNumeroLote()."'), 10,'0') ";
    }
    if ( $this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " AND vla.cod_localizacao = ".$this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao();
    }
    if ($this->boListarBaixadas == false) {
       //assim mostra apenas ativas
       //$stFiltro .= " AND ((data_baixa IS NULL) OR (data_baixa IS NOT NULL AND data_reativacao IS NOT NULL)) ";
        $stFiltro .= " AND ve.situacao = 'ativo' ";
    } else {
       //assim mostra apenas baixadas
       //$stFiltro .= " AND data_baixa IS NOT NULL AND data_reativacao IS NULL ";
        $stFiltro .= " AND ve.situacao = 'baixado' ";
    }

    $stOrder = " ORDER BY ve.cod_construcao ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaRelacionamentoAlteracao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarEdificacoesImovelBaixa(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= "dados.cod_construcao_dep_aut = ".$this->inCodigoConstrucao." AND ";
    }

    if ($this->inCodigoTipo) {
        $stFiltro .= "dados.cod_tipo = ".$this->inCodigoTipo." AND ";
    }

    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= "dados.imovel_cond = ".$this->obRCIMImovel->getNumeroInscricao()." AND ";
    }

    if ( $this->obRCIMImovel->roRCIMLote->getNumeroLote() ) {
        $stFiltro .= "LPAD( UPPER( dados.numero_lote ) , 10,'0') = ";
        $stFiltro .= " LPAD( UPPER('".$this->obRCIMImovel->roRCIMLote->getNumeroLote()."'), 10,'0') AND ";
    }

    if ( $this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= "dados.cod_localizacao = ".$this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao()." AND ";
    }

    if ($this->boListarBaixadas == false) {
       //assim mostra apenas ativas
       $stFiltro .= "dados.situacao_unidade = 'Ativo' AND ";
    } else {
       //assim mostra apenas baixadas
       $stFiltro .= "dados.situacao_unidade = 'Baixado' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = " ORDER BY dados.cod_construcao ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaRelacionamentoBaixa( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCIMConstrucaoEdificacao->debug();
    return $obErro;
}

/**
* Lista as Edificações conforme o filtro setado
* Otimização da consulta de cadastro imobiliário - GRIS - 04/01/2006
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarEdificacoesImovelConsulta(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " AND COALESCE(unidade.cod_construcao_dependente,unidade.cod_construcao) = ".$this->inCodigoConstrucao;
    }
    if ($this->inCodigoTipo) {
        $stFiltro .= " AND construcao_edificacao.cod_tipo = ".$this->inCodigoTipo;
    }
    if ( $this->getTipoUnidade() == 'Autonoma' ) {
        $stFiltro .= " AND ( unidade.tipo_vinculo = 'Autônoma' ) ";
/*    } else {
        $stFiltro .= " AND ( unidade.tipo_vinculo = 'Autônoma' OR unidade.tipo_vinculo = 'Dependente' ) ";*/
    }
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " AND imovel.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao();
    }
    if ( $this->obRCIMImovel->roRCIMLote->getNumeroLote() ) {
        $stFiltro .= " AND LPAD( UPPER(lote_localizacao.valor) , 10,'0') = ";
        $stFiltro .= " LPAD( UPPER('".$this->obRCIMImovel->roRCIMLote->getNumeroLote()."'), 10,'0') ";
    }
    if ( $this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " AND lote_localizacao.cod_localizacao = ".$this->obRCIMImovel->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao();
    }
    if ($this->boListarBaixadas == false) {
        $stFiltro .= " AND (ibc.dt_inicio IS NULL OR (ibc.dt_inicio IS NOT NULL AND ibc.dt_termino IS NOT NULL) AND construcao.cod_construcao = ibc.cod_construcao)";
    }
    $stOrder = " ORDER BY COALESCE(unidade.cod_construcao_dependente,unidade.cod_construcao) ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaRelacionamentoListarConsulta( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCIMConstrucaoEdificacao->debug();
    return $obErro;
}

/**
* Recupera as informacoes das edificações de um imóvel
* Otimização da consulta de cadastro imobiliário - GRIS - 10/01/2006
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarEdificacoesConsulta(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " AND  imovel.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao();
    }
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " AND unidade.cod_construcao = ".$this->inCodigoConstrucao;
    }
    if ( $this->getTipoUnidade() == 'Autonoma' ) {
        $stFiltro .= " AND ( unidade.tipo_vinculo = 'Autônoma' ) ";
//     } else {
//         $stFiltro .= " AND ( ve.tipo_vinculo = 'Autônoma' OR ve.tipo_vinculo = 'Dependente' ) ";
    }
    $stOrder = " ORDER BY unidade.cod_construcao ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaRelacionamentoConsultaEdificacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCIMConstrucaoEdificacao->debug();
    return $obErro;
}

/**
* Lista as Unidades Autonomas conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarUnidadesAutonomas(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " AND UA.cod_construcao = ".$this->inCodigoConstrucao;
    }
    if ($this->inCodigoTipo) {
        $stFiltro .= " AND UA.cod_tipo = ".$this->inCodigoTipo;
    }
    if ( $this->obRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " AND UA.inscricao_municipal = ".$this->obRCIMImovel->getNumeroInscricao();
    }
    $stOrder = " ORDER BY UA.cod_construcao ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaUnidadeAutonoma( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
    $obErro = $this->obTCIMConstrucaoEdificacao->recuperaRelacionamentoProcesso( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Edificação selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarEdificacao($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= "AND unidade.cod_construcao = ".$this->inCodigoConstrucao." AND ";
    }
    if ($stFiltro) {
        $stFiltro = /*" WHERE ".*/substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    //$stOrder = " ORDER BY cod_construcao ";
    $obErro = $this->obVCIMConstrucaoEdificacao->recuperaRelacionamentoListarConsulta( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodigoConstrucao                = $rsRecordSet->getCampo( "cod_construcao" );
        $this->flAreaConstruida                  = $rsRecordSet->getCampo( "area_real"      );
        $this->inCodigoTipo                      = $rsRecordSet->getCampo( "cod_tipo"       );
//        echo "Time na Edificação: ".$rsRecordSet->getCampo( "timestamp_construcao"    );
//        parent::setTimestampConstrucao           ( $rsRecordSet->getCampo( "timestamp_construcao"    ) );
        $this->obRCIMTipoEdificacao->setNomeTipo ( $rsRecordSet->getCampo( "nom_tipo"     ) );
    }

    return $obErro;
}
/**
* Recupera do banco de dados os dados da Edificação selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
//function consultarTimestamp($boTransacao = "") {
//    $stFiltro = "";
//    if ($this->inCodigoConstrucao) {
//        $stFiltro .= "cod_construcao = ".$this->inCodigoConstrucao." AND ";
//    }
//    if ($stFiltro) {
//        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
//    }
//    $stOrder = " ORDER BY cod_construcao ";
//    $obErro = $this->obTCIMConstrucaoEdificacao->recuperaTimestampConstrucao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
////    $this->obTCIMConstrucaoEdificacao->debug();
//    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
//        parent::setTimestampConstrucao           ( $rsRecordSet->getCampo( "timestamp_construcao"    ) );
//    }
//    return $obErro;
//}

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
    $obErro = $this->obTCIMConstrucaoEdificacao->recuperaAreaConstrucaoCondominio( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    $rsRecordSet->addFormatacao( 'area_real' , 'NUMERIC_BR' );
    $flAreaConstrucao = $rsRecordSet->getCampo('area_real');

    return $obErro;
}

/**
* Inclui os dados da Reforma
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
            $arChaveAtributoConstrucaoOutros =  array( "cod_construcao" => $this->getCodigoConstrucao() );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoConstrucaoOutros );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucaoOutros );

    return $obErro;
}

/**
    * Listagem das Edificações Pertencentes a determinado lote/construcao
    * @access Public
    * @param  Objecat $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEdificacoesLote(&$rsRecordSet ,$boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoConstrucao) {
        $stFiltro .= " AND ic.cod_construcao  = ".$this->inCodigoLote;
    }
    if ( $this->obRCIMImovel->roRCIMLote->getCodigoLote()) {
        $stFiltro .= " AND il.cod_lote = ".$this->obRCIMImovel->roRCIMLote->getCodigoLote();
    }
    $stOrdem  = " ORDER BY ic.cod_construcao, ii.inscricao_municipal ";
    $obErro = $this->obRCIMImovel->roRCIMLote->obTCIMLote->recuperaEdificacoesLote( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}//fecha classe
