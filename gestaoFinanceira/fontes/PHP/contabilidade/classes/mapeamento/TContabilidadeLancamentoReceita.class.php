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
    * Classe de mapeamento da tabela CONTABILIDADE.LANCAMENTO_RECEITA
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TContabilidadeLancamentoReceita.class.php 66028 2016-07-08 19:08:45Z michel $

    * Casos de uso: uc-02.02.05
                    uc-02.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TContabilidadeLancamentoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeLancamentoReceita()
{
    parent::Persistente();
    $this->setTabela('contabilidade.lancamento_receita');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,tipo,sequencia,exercicio,cod_entidade,cod_receita');

    $this->AddCampo('cod_lote','integer',true,'',true,false);
    $this->AddCampo('tipo','char',true,'1',true,false);
    $this->AddCampo('sequencia','integer',true,'',true,false);
    $this->AddCampo('exercicio','char',true,'4',true,false);
    $this->AddCampo('cod_entidade','integer',true,'',true,false);
    $this->AddCampo('cod_receita','integer',true,'',true,false);
    $this->AddCampo('estorno','boolean',true,'',false,false);

}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelacionamento.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamento().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta sql para recuperaRelacionamento
    * @access Private
    * @return String
*/
function montaRecuperaRelacionamento()
{
    $stSQL  = "SELECT                                            \n";
    $stSQL .= "    lo.cod_lote,                                  \n";
    $stSQL .= "    to_char(lo.dt_lote, 'dd/mm/yyyy') as dt_lote, \n";
    $stSQL .= "    lo.nom_lote,                                  \n";
    $stSQL .= "    l.sequencia,                                  \n";
    $stSQL .= "    l.cod_historico,                              \n";
    $stSQL .= "    l.tipo,                                       \n";
    $stSQL .= "    l.cod_entidade,                               \n";
    $stSQL .= "    abs( la.vl_lancamento ) as vl_lancamento,     \n";
    $stSQL .= "    h.cod_historico,                              \n";
    $stSQL .= "    h.nom_historico,                              \n";
    $stSQL .= "    lr.cod_receita,                               \n";
    $stSQL .= "    lr.estorno                                    \n";
    $stSQL .= "FROM                                              \n";
    $stSQL .= "    contabilidade.lancamento as l,            \n";
    $stSQL .= "    contabilidade.valor_lancamento as la,     \n";
    $stSQL .= "    contabilidade.historico_contabil as h,    \n";
    $stSQL .= "    contabilidade.lote as lo,                 \n";
    $stSQL .= "    contabilidade.lancamento_receita as lr    \n";
    $stSQL .= "WHERE                                             \n";
    $stSQL .= "    lo.cod_lote = l.cod_lote AND                  \n";
    $stSQL .= "    lo.exercicio = l.exercicio AND                \n";
    $stSQL .= "    lo.tipo = l.tipo AND                          \n";
    $stSQL .= "    lo.cod_entidade = l.cod_entidade AND          \n";
    $stSQL .= "    l.cod_historico = h.cod_historico AND         \n";
    $stSQL .= "    l.exercicio = h.exercicio AND                 \n";
    $stSQL .= "    l.sequencia = la.sequencia AND                \n";
    $stSQL .= "    l.exercicio = la.exercicio AND                \n";
    $stSQL .= "    l.tipo = la.tipo AND                          \n";
    $stSQL .= "    l.cod_lote = la.cod_lote AND                  \n";
    $stSQL .= "    l.cod_entidade = la.cod_entidade AND          \n";
    $stSQL .= "    lr.cod_lote = l.cod_lote  AND                 \n";
    $stSQL .= "    lr.exercicio = l.exercicio AND                \n";
    $stSQL .= "    lr.tipo = l.tipo AND                          \n";
    $stSQL .= "    lr.sequencia = l.sequencia AND                \n";
    $stSQL .= "    lr.cod_entidade = l.cod_entidade              \n";
    $stSQL .= $this->getDado("stFiltro");
    $stSQL .= "GROUP BY                                          \n";
    $stSQL .= "    abs( vl_lancamento ),                         \n";
    $stSQL .= "    lo.cod_lote,                                  \n";
    $stSQL .= "    dt_lote,                                      \n";
    $stSQL .= "    lo.nom_lote,                                  \n";
    $stSQL .= "    l.sequencia,                                  \n";
    $stSQL .= "    l.cod_historico,                              \n";
    $stSQL .= "    l.tipo,                                       \n";
    $stSQL .= "    l.cod_entidade,                               \n";
    $stSQL .= "    h.cod_historico,                              \n";
    $stSQL .= "    h.nom_historico,                              \n";
    $stSQL .= "    lr.estorno,                                   \n";
    $stSQL .= "    lr.cod_receita                                \n";

    return $stSQL;
}

function recuperaBoletins(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY substr(nom_lote,34,80),dt_lote ";
    $stGroup = "GROUP BY substr(nom_lote,34,80), dt_lote";
    $stSql  = $this->montaRecuperaBoletins().$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBoletins()
{
    $stSQL .= " SELECT                                                          \n";
    $stSQL .= " substr(nom_lote,34,80) as nom_lote                              \n";
    $stSQL .= " ,to_char(dt_lote,'dd/mm/yyyy') as dt_lote                       \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "    contabilidade.lote                                       \n";
    $stSQL .= " WHERE                                                           \n";
    $stSQL .= "    tipo = 'A'                                                   \n";
    $stSQL .= $this->getDado("stFiltro");

   return $stSQL;

}

function recuperaBoletinsLote(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lote ";
    $stSql  = $this->montaRecuperaBoletinsLote().$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBoletinsLote()
{
    $stSQL .= " SELECT                                                          \n";
    $stSQL .= " cod_lote as cod_lote                                            \n";
    $stSQL .= " ,nom_lote as nom_lote                                           \n";
    $stSQL .= " ,to_char(dt_lote,'dd/mm/yyyy') as dt_lote                       \n";
    $stSQL .= " ,cod_entidade as cod_entidade                                   \n";
    $stSQL .= " ,exercicio as exercicio                                         \n";
    $stSQL .= " ,tipo as tipo                                                   \n";
    $stSQL .= " FROM                                                            \n";
    $stSQL .= "    contabilidade.lote                                       \n";
    $stSQL .= " WHERE                                                           \n";
    $stSQL .= "    tipo = 'A'                                                   \n";
    $stSQL .= $this->getDado("stFiltro");

   return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaExistenciaReceita(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaExistenciaReceita().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaExistenciaReceita()
{
    $stSql  = " SELECT
                   count(cod_receita) as total
               FROM
                   contabilidade.lancamento_receita
              WHERE exercicio = '".$this->getDado('exercicio')."'
              ";

    if($this->getDado('cod_receita'))
        $stSql .= " AND cod_receita = ".$this->getDado('cod_receita')." \n";

    return $stSql;
}

}
