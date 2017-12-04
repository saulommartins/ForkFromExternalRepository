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
    * Classe de mapeamento da tabela FN_ORCAMENTO_BALANCETE_RECEITA
    * Data de Criação: 15/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.21
*/

/*
$Log$
Revision 1.8  2007/01/03 10:32:28  cako
Bug #7916#

Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoBalanceteReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoBalanceteReceita()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_balancete_receita');

    $this->AddCampo('cod_estrutural'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('receita'           ,'integer',false,''    ,false,false);
    $this->AddCampo('recurso'           ,'integer',false,''    ,false,false);
    $this->AddCampo('descricao'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor_previsto'    ,'numeric',false,'14.2',false,false);
    $this->AddCampo('arrecadado_periodo','numeric',false,'14.2',false,false);
    $this->AddCampo('arrecadado_ano'    ,'numeric',false,'14.2',false,false);
    $this->AddCampo('diferenca'         ,'numeric',false,'14.2',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select * \n";
    $stSql .= "  from " . $this->getTabela() . "('" . $this->getDado("exercicio") ."',  \n";
    $stSql .= "  '" . $this->getDado("stFiltro") . "','" . $this->getDado("stDataInicial") . "','" . $this->getDado("stDataFinal") . "','".$this->getDado("stEntidade")."','".$this->getDado("stCodEstruturalInicial")."','".$this->getDado("stCodEstruturalFinal")."','".$this->getDado("stCodReduzidoInicial")."','".$this->getDado("stCodReduzidoFinal")."','".$this->getDado("inCodRecurso")."','".$this->getDado('stDestinacaoRecurso')."','".$this->getDado('inCodDetalhamento')."') as retorno(                      \n";
    $stSql .= "  cod_estrutural      varchar,                                           \n";
    $stSql .= "  receita             integer,                                           \n";
    $stSql .= "  recurso             varchar,                                           \n";
    $stSql .= "  descricao           varchar,                                           \n";
    $stSql .= "  valor_previsto      numeric,                                           \n";
    $stSql .= "  arrecadado_periodo  numeric,                                           \n";
    $stSql .= "  arrecadado_ano      numeric,                                           \n";
    $stSql .= "  diferenca           numeric                                           \n";
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
    $stSql .= "     cod_receita NOT NULL                                 ".$stQuebra;

    return $stSql;
}

function recuperaTransparencia(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTransparencia().$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTransparencia()
{
    $stSql = "SELECT
                    ".$this->getDado("stEntidade")."::integer as entidade,
                    replace(tabela.cod_estrutural,'.','')::numeric as cod_estrutural,
                    replace(coalesce(tabela.vl_original,0.00)::varchar,'.','') as vl_original,
                    replace(coalesce(tabela.ar_jan,0.00)::varchar,'.','') as ar_jan,
                    replace(coalesce(tabela.ar_fev,0.00)::varchar,'.','') as ar_fev,
                    replace(coalesce(tabela.ar_mar,0.00)::varchar,'.','') as ar_mar,
                    replace(coalesce(tabela.ar_abr,0.00)::varchar,'.','') as ar_abr,
                    replace(coalesce(tabela.ar_mai,0.00)::varchar,'.','') as ar_mai,
                    replace(coalesce(tabela.ar_jun,0.00)::varchar,'.','') as ar_jun,
                    replace(coalesce(tabela.ar_jul,0.00)::varchar,'.','') as ar_jul,
                    replace(coalesce(tabela.ar_ago,0.00)::varchar,'.','') as ar_ago,
                    replace(coalesce(tabela.ar_set,0.00)::varchar,'.','') as ar_set,
                    replace(coalesce(tabela.ar_out,0.00)::varchar,'.','') as ar_out,
                    replace(coalesce(tabela.ar_nov,0.00)::varchar,'.','') as ar_nov,
                    replace(coalesce(tabela.ar_dez,0.00)::varchar,'.','') as ar_dez,
                    tabela.cod_recurso::numeric,
                    tabela.descricao,
                    cast(orcamento.fn_tipo_conta_receita('".$this->getDado("exercicio")."', tabela.cod_estrutural) as varchar) as tipo,
                    cast(publico.fn_nivel(tabela.cod_estrutural) as integer) as nivel
              FROM
                    orcamento.fn_balancete_receita_transparencia ('".$this->getDado("exercicio")."',
                                                                  '".$this->getDado("stFiltro")."',
                                                                  '".$this->getDado("stDataInicial")."',
                                                                  '".$this->getDado("stDataFinal")."',
                                                                  '".$this->getDado("stEntidade")."',
                                                                  '".$this->getDado("stCodEstruturalInicial")."',
                                                                  '".$this->getDado("stCodEstruturalFinal")."',
                                                                  '".$this->getDado("stCodReduzidoInicial")."',
                                                                  '".$this->getDado("stCodReduzidoFinal")."',
                                                                  '".$this->getDado("inCodRecurso")."',
                                                                  '".$this->getDado('stDestinacaoRecurso')."',
                                                                  '".$this->getDado('inCodDetalhamento')."',
                                                                  ".$this->getDado('inMes').")
                    AS tabela                                   (cod_estrutural     varchar(150),
                                                                 cod_receita        integer,
                                                                 cod_recurso        varchar,
                                                                 descricao          varchar(160),
                                                                 vl_original        numeric,
                                                                 ar_jan             numeric,
                                                                 ar_fev             numeric,
                                                                 ar_mar             numeric,
                                                                 ar_abr             numeric,
                                                                 ar_mai             numeric,
                                                                 ar_jun             numeric,
                                                                 ar_jul             numeric,
                                                                 ar_ago             numeric,
                                                                 ar_set             numeric,
                                                                 ar_out             numeric,
                                                                 ar_nov             numeric,
                                                                 ar_dez             numeric)
              WHERE cast(publico.fn_nivel(tabela.cod_estrutural) as integer) <>0
              ORDER BY entidade, tabela.cod_estrutural";

    return $stSql;
}

function recuperaDadosMANAD(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosMANAD().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaDadosMANAD()
{
    $stSql  = "SELECT 'L200' as reg,            \n";
    $stSql .= "    '".$this->getDado("orgao").$this->getDado("unidade")."' as cod_org_un_orc,                          \n";
    $stSql .= "    '".$this->getDado("stExercicio")."' as exerc,                          \n";
    $stSql .= "    replace(tabela.cod_estrutural,'.','') as cod_cta_receita,         \n";
    $stSql .= "    replace(replace(tabela.vl_original,'.',','),'-','') as vl_rec_orcada,                    \n";
    $stSql .= "    replace(replace(tabela.totalizado,'.',','),'-','') as vl_rec_realizada,                \n";
    $stSql .= "    tabela.cod_recurso as cod_rec_vinc,                                      \n";
    $stSql .= "    tabela.descricao as desc_receita,                                           \n";
    $stSql .= "    tabela.tipo as ind_tipo_conta,                                                \n";
    $stSql .= "    tabela. nivel as nm_nivel_conta,                                             \n";
    $stSql .= "    tabela.cod_caracteristica                                        \n";
    $stSql .= "FROM                                                                 \n";
    $stSql .= "tcers.fn_exportacao_balancete_receita('".$this->getDado("stExercicio")     ."',    \n";
    $stSql .= "                        '".$this->getDado("stCodEntidades")  ."',    \n";
    $stSql .= "                        '".$this->getDado("dtInicial")       ."',    \n";
    $stSql .= "                        '".$this->getDado("dtFinal")         ."')    \n";
    $stSql .= "AS tabela              (   cod_estrutural     varchar,               \n";
    $stSql .= "                           cod_recurso        integer,               \n";
    $stSql .= "                           descricao          varchar,               \n";
    $stSql .= "                           vl_original        numeric,               \n";
    $stSql .= "                           totalizado         numeric,               \n";
    $stSql .= "                           tipo               varchar,               \n";
    $stSql .= "                           nivel              integer,               \n";
    $stSql .= "                           cod_caracteristica integer)               \n";
    $stSql .= "WHERE tabela.nivel<>0                                                \n";
    $stSql .= "ORDER BY tabela.cod_estrutural                                       \n";

    return $stSql;
}

}
