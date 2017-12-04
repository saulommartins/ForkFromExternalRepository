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
    * Comentário sobre a finalidade do arquivo.
    * Data de Criação: 13/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.09

    $Id: FEmpenhoRPPagEstCredor.class.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class FEmpenhoRPPagEstCredor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoRPPagEstCredor()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_empenho_restos_pagar_pagamento_estorno_credor');

    $this->AddCampo('entidade'      ,'integer',false,''    ,false,false);
    $this->AddCampo('empenho'       ,'integer',false,''    ,false,false);
    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_nota'      ,'integer',false,''    ,false,false);
    $this->AddCampo('data'          ,'text'   ,false,''    ,false,false);
    $this->AddCampo('conta'         ,'integer',false,''    ,false,false);
    $this->AddCampo('banco'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'         ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "SELECT * 												  \n";
    $stSql .= "  FROM " .$this->getTabela(). "							  \n";
    $stSql .= "	    ( '".$this->getDado("exercicio")."'				      \n";
    $stSql .= "     , '".$this->getDado("stFiltro")."'					  \n";
    $stSql .= "     , '".$this->getDado("stDataInicial")."'				  \n";
    $stSql .= "     , '".$this->getDado("stDataFinal")."'				  \n";
    $stSql .= "     , '".$this->getDado("stEntidade")."'				  \n";
    $stSql .= "     , '".$this->getDado("inOrgao")."'				  	  \n";
    $stSql .= "     , '".$this->getDado("inUnidade")."'				  	  \n";
    $stSql .= "     , '".$this->getDado("inRecurso")."'				  	  \n";
    $stSql .= "     , '".$this->getDado("stDestinacaoRecurso")."'		  \n";
    $stSql .= "     , '".$this->getDado("inCodDetalhamento")."'		      \n";
    $stSql .= "     , '".$this->getDado("stElementoDespesa")."'			  \n";
    $stSql .= "     , '".$this->getDado("stElementoDespesa")."'			  \n";
    $stSql .= "     , '".$this->getDado("inSituacao")."'			 	  \n";
    $stSql .= "		, '".$this->getDado("inCodCredor")."','','') as retorno(	  \n";
    $stSql .= "  entidade            integer,                             \n";
    $stSql .= "  empenho             integer,                             \n";
    $stSql .= "  exercicio           char(4),                             \n";
    $stSql .= "  credor              varchar,                             \n";
    $stSql .= "  cod_estrutural      varchar,                             \n";
    $stSql .= "  cod_nota            integer,                             \n";
    $stSql .= "  data                text,                                \n";
    $stSql .= "  conta               integer,                             \n";
    $stSql .= "  banco               varchar,                             \n";
    $stSql .= "  valor               numeric                              \n";
    $stSql .= "  )                                                          ";

    return $stSql;

}

function consultaValorConta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaValorConta().$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConsultaValorConta()
{
    $stQuebra = "\n";
    $stSql .= " SELECT SUM(func.vl_original) FROM                        ".$stQuebra;
    $stSql .= " ( ".$this->montaRecuperaTodos()." ) as func              ".$stQuebra;
    $stSql .= " WHERE                                                    ".$stQuebra;
    $stSql .= "     empenho NOT NULL                                 ".$stQuebra;

    return $stSql;
}

}
