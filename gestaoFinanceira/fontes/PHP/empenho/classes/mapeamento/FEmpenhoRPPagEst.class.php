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
    * Classe de mapeamento da tabela FN_EMPENHO_RESTOS_PAGAR_PAGAMENTO_ESTORNO
    * Data de Criação: 23/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso : uc-02.03.09
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoRPPagEst extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoRPPagEst()
{
    parent::Persistente();

    $this->setTabela('empenho.fn_empenho_restos_pagar_pagamento_estorno');

    $this->AddCampo('entidade'      ,'integer',false,''    ,false,false);
    $this->AddCampo('empenho'       ,'integer',false,''    ,false,false);
    $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_nota'      ,'integer',false,''    ,false,false);
    $this->AddCampo('data'          ,'text',false,''       ,false,false);
    $this->AddCampo('conta'         ,'integer',false,''    ,false,false);
    $this->AddCampo('banco'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'         ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select * \n";
    $stSql .= "  from " . $this->getTabela() . "('" . $this->getDado("exercicio") ."',  \n";
    $stSql .= "  '" . $this->getDado("stFiltro") . "','" . $this->getDado("stDataInicial") . "', \n";
    $stSql .= "  '" . $this->getDado("stDataFinal") . "','".$this->getDado("stEntidade")."',\n";
    $stSql .= "  '" . $this->getDado("inOrgao")."','".$this->getDado("inUnidade")."',\n";
    $stSql .= "  '" . $this->getDado("inRecurso")."','".$this->getDado('stDestinacaoRecurso')."','".$this->getDado('inCodDetalhamento')."','".str_replace(".","",$this->getDado("stElementoDespesa"))."',\n";
    $stSql .= "  '" . $this->getDado("stElementoDespesa")."','" . $this->getDado("inSituacao")."') as retorno( \n";
    $stSql .= "  entidade            integer,                                           \n";
    $stSql .= "  empenho             integer,                                           \n";
    $stSql .= "  exercicio           char(4),                                           \n";
    $stSql .= "  cod_nota            integer,                                           \n";
    $stSql .= "  data                text,                                              \n";
    $stSql .= "  conta               integer,                                           \n";
    $stSql .= "  banco               varchar,                                           \n";
    $stSql .= "  valor               numeric                                            \n";
    $stSql .= "  )                                                                        ";

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
