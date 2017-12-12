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
    * Classe de mapeamento da tabela FN_ORCAMENTO_RELACAO_RECEITA
    * Data de Criação: 24/09/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Eduardo Martins
    * @author Desenvolvedor: Anderson Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoRelacaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoRelacaoReceita()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_relacao_receita');

    $this->AddCampo('exercicio'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_conta'         ,'integer',false,''    ,false,false);
    $this->AddCampo('classificacao'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('descricao_receita' ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_recurso'       ,'varchar',false,''    ,false,false);
    $this->AddCampo('nom_recurso'       ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_receita'       ,'integer',false,''    ,false,false);
    $this->AddCampo('valor_previsto'    ,'numeric',false,'14.2',false,false);
    $this->AddCampo('cod_entidade'      ,'integer',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = "select *                                                                 \n";
    $stSql .= "  from " . $this->getTabela() . "('" . $this->getDado("exercicio") ."',  \n";
    $stSql .= "  '" . $this->getDado("stFiltro") . "') as retorno(                      \n";
    $stSql .= "  exercicio           char(4),                                           \n";
    $stSql .= "  cod_conta           integer,                                           \n";
    $stSql .= "  classificacao       varchar,                                           \n";
    $stSql .= "  descricao_receita   varchar,                                           \n";
    $stSql .= "  cod_recurso         varchar,                                           \n";
    $stSql .= "  nom_recurso         varchar,                                           \n";
    $stSql .= "  cod_receita         integer,                                           \n";
    $stSql .= "  valor_previsto      numeric,                                           \n";
    $stSql .= "  cod_entidade        integer,                                           \n";
    $stSql .= "  vl_arrecadado       numeric                                            \n";
    $stSql .= "  )                                                                        ";

    return $stSql;
}

function consultaValorConta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaValorConta().$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConsultaValorConta()
{
    $stQuebra = "\n";

    $stSql .= "SELECT coalesce(SUM(func.vl_arrecadado),0.00) as sum                                                                     ".$stQuebra;
    $stSql .= "  FROM (SELECT conta_receita.cod_estrutural  AS classificacao                                                            ".$stQuebra;
    $stSql .= "             , receita.cod_receita                                                                                       ".$stQuebra;
    $stSql .= "             , (orcamento.fn_receita_realizada_periodo(orcamento.conta_receita.exercicio                                 ".$stQuebra;
    $stSql .= "                                                    , cast(orcamento.receita.cod_entidade as varchar)                    ".$stQuebra;
    $stSql .= "                                                    , orcamento.receita.cod_receita                                      ".$stQuebra;
    $stSql .= "                                                    , '01/01/'||orcamento.conta_receita.exercicio                        ".$stQuebra;
    $stSql .= "                                                    , TO_CHAR( now(), 'dd/mm/' )||orcamento.conta_receita.exercicio) *-1 ".$stQuebra;
    $stSql .= "                                                     ) as vl_arrecadado                                                  ".$stQuebra;
    $stSql .= "          FROM orcamento.conta_receita                                                                                   ".$stQuebra;
    $stSql .= "             , orcamento.receita                                                                                         ".$stQuebra;
    $stSql .= "         WHERE conta_receita.cod_conta = receita.cod_conta                                                               ".$stQuebra;
    $stSql .= "           AND conta_receita.exercicio = receita.exercicio                                                               ".$stQuebra;
    $stSql .= "           AND receita.exercicio = '".$this->getDado("exercicio")."'                                                     ".$stQuebra;
    $stSql .= "       ) AS func                                                                                                         ".$stQuebra;
    $stSql .= " WHERE cod_receita is NOT NULL                                                                                           ".$stQuebra;

    return $stSql;
}


function consultaLancamentoAnterior(&$rsRecordSet, $stFiltro = "", $stGroup = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaConsultaLancamentoAnterior().$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaConsultaLancamentoAnterior()
{

    $stSql = "  SELECT coalesce(SUM(tabela.vl_arrecadado),0.00) AS vl_arrecadado
                     , tabela.cod_receita
                     , tabela.cod_estrutural
                     , REPLACE(publico.fn_mascarareduzida(tabela.cod_estrutural),'.','') as ordem_estrutural
                  FROM (SELECT conta_receita.cod_estrutural
                             , receita.cod_receita                                                                                       
                             , (orcamento.fn_receita_realizada_periodo( orcamento.conta_receita.exercicio                                 
                                                                      , cast(orcamento.receita.cod_entidade as varchar)                    
                                                                      , orcamento.receita.cod_receita                                      
                                                                      , '01/01/'||orcamento.conta_receita.exercicio                        
                                                                      , TO_CHAR( now(), 'dd/mm/' )||orcamento.conta_receita.exercicio) *-1 
                                                                     ) AS vl_arrecadado                                                  
                          FROM orcamento.conta_receita                                                                                   
                             
                    INNER JOIN orcamento.receita                                                                                         
                            ON conta_receita.cod_conta = receita.cod_conta                                                               
                           AND conta_receita.exercicio = receita.exercicio                                                               
                         
                         WHERE receita.exercicio = '".$this->getDado("exercicio")."'
                       ) AS tabela ";
    
    return $stSql;
}

}

?>