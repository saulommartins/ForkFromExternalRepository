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
    * Classe de mapeamento da tabela CONTABILIDADE.LANCAMENTO
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.04
                    uc-02.02.05
                    uc-02.02.23
*/

/*
$Log$
Revision 1.9  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.LANCAMENTO
  * Data de Criação: 01/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeLancamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeLancamento()
{
    parent::Persistente();
    $this->setTabela('contabilidade.lancamento');

    $this->setCampoCod('sequencia');
    $this->setComplementoChave('cod_lote,tipo,exercicio,cod_entidade');

    $this->AddCampo('sequencia','integer',true,'',true,false);
    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('tipo','char',true,'1',true,true);
    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('cod_historico','integer',true,'',false,true);
    $this->AddCampo('complemento','varchar',true,'400',false,false);

}
/**
    * Monta sql para relatório Diário
    * @access Private
    * @return String
*/
function montaRelatorioDiario()
{
    $stSQL  = " SELECT                                                                                                       \n";
    $stSQL .= "       to_char(lote.dt_lote,'dd/mm/yyyy')                                           AS dt_lote                \n";
    $stSQL .= "     , CASE WHEN historico_contabil.complemento = true                                                        \n";
    $stSQL .= "            THEN historico_contabil.nom_historico || ' ' || lancamento.complemento                            \n";
    $stSQL .= "            ELSE historico_contabil.nom_historico                                                             \n";
    $stSQL .= "       END                                                                         AS historico               \n";
    $stSQL .= "     , abs(valor_lancamento_debito.vl_lancamento)                                  AS vl_lancamento_debito    \n";
    $stSQL .= "     , plano_conta_debito.cod_estrutural                                           AS cod_estrutural_debito   \n";
    $stSQL .= "     , plano_conta_debito.nom_conta                                                AS nom_conta_debito        \n";
    $stSQL .= "     , abs(valor_lancamento_credito.vl_lancamento)                                 AS vl_lancamento_credito   \n";
    $stSQL .= "     , plano_conta_credito.cod_estrutural                                          AS cod_estrutural_credito  \n";
    $stSQL .= "     , plano_conta_credito.nom_conta                                               AS nom_conta_credito       \n";
    $stSQL .= "  FROM                                                                                                        \n";
    $stSQL .= "       contabilidade.lote                                                                                     \n";
    $stSQL .= "     , contabilidade.lancamento                                                                               \n";
    $stSQL .= "     , contabilidade.historico_contabil                                                                       \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     , contabilidade.valor_lancamento     AS valor_lancamento_debito                                          \n";
    $stSQL .= "     , contabilidade.conta_debito                                                                             \n";
    $stSQL .= "     , contabilidade.plano_analitica      AS plano_analitica_debito                                           \n";
    $stSQL .= "     , contabilidade.plano_conta          AS plano_conta_debito                                               \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     , contabilidade.valor_lancamento     AS valor_lancamento_credito                                         \n";
    $stSQL .= "     , contabilidade.conta_credito                                                                            \n";
    $stSQL .= "     , contabilidade.plano_analitica      AS plano_analitica_credito                                          \n";
    $stSQL .= "     , contabilidade.plano_conta          AS plano_conta_credito                                              \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "   WHERE     lancamento.exercicio                    = lote.exercicio                                           \n";
    $stSQL .= "     AND     lancamento.cod_entidade                 = lote.cod_entidade                                      \n";
    $stSQL .= "     AND     lancamento.tipo                         = lote.tipo                                              \n";
    $stSQL .= "     AND     lancamento.cod_lote                     = lote.cod_lote                                          \n";
    $stSQL .= "     AND     lancamento.exercicio                    = historico_contabil.exercicio                           \n";
    $stSQL .= "     AND     lancamento.cod_historico                = historico_contabil.cod_historico                       \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     lancamento.exercicio                    = valor_lancamento_debito.exercicio                      \n";
    $stSQL .= "     AND     lancamento.cod_entidade                 = valor_lancamento_debito.cod_entidade                   \n";
    $stSQL .= "     AND     lancamento.sequencia                    = valor_lancamento_debito.sequencia                      \n";
    $stSQL .= "     AND     lancamento.cod_lote                     = valor_lancamento_debito.cod_lote                       \n";
    $stSQL .= "     AND     lancamento.tipo                         = valor_lancamento_debito.tipo                           \n";
    $stSQL .= "     AND     valor_lancamento_debito.tipo_valor      = 'D'                                                    \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     valor_lancamento_debito.exercicio       = conta_debito.exercicio                                 \n";
    $stSQL .= "     AND     valor_lancamento_debito.cod_entidade    = conta_debito.cod_entidade                              \n";
    $stSQL .= "     AND     valor_lancamento_debito.sequencia       = conta_debito.sequencia                                 \n";
    $stSQL .= "     AND     valor_lancamento_debito.cod_lote        = conta_debito.cod_lote                                  \n";
    $stSQL .= "     AND     valor_lancamento_debito.tipo            = conta_debito.tipo                                      \n";
    $stSQL .= "     AND     valor_lancamento_debito.tipo_valor      = conta_debito.tipo_valor                                \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     conta_debito.exercicio                  = plano_analitica_debito.exercicio                       \n";
    $stSQL .= "     AND     conta_debito.cod_plano                  = plano_analitica_debito.cod_plano                       \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     plano_analitica_debito.exercicio        = plano_conta_debito.exercicio                           \n";
    $stSQL .= "     AND     plano_analitica_debito.cod_conta        = plano_conta_debito.cod_conta                           \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     lancamento.exercicio                    = valor_lancamento_credito.exercicio                     \n";
    $stSQL .= "     AND     lancamento.cod_entidade                 = valor_lancamento_credito.cod_entidade                  \n";
    $stSQL .= "     AND     lancamento.sequencia                    = valor_lancamento_credito.sequencia                     \n";
    $stSQL .= "     AND     lancamento.cod_lote                     = valor_lancamento_credito.cod_lote                      \n";
    $stSQL .= "     AND     lancamento.tipo                         = valor_lancamento_credito.tipo                          \n";
    $stSQL .= "     AND     valor_lancamento_credito.tipo_valor     = 'C'                                                    \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     valor_lancamento_credito.exercicio      = conta_credito.exercicio                                \n";
    $stSQL .= "     AND     valor_lancamento_credito.cod_entidade   = conta_credito.cod_entidade                             \n";
    $stSQL .= "     AND     valor_lancamento_credito.sequencia      = conta_credito.sequencia                                \n";
    $stSQL .= "     AND     valor_lancamento_credito.cod_lote       = conta_credito.cod_lote                                 \n";
    $stSQL .= "     AND     valor_lancamento_credito.tipo           = conta_credito.tipo                                     \n";
    $stSQL .= "     AND     valor_lancamento_credito.tipo_valor     = conta_credito.tipo_valor                               \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     conta_credito.exercicio                 = plano_analitica_credito.exercicio                      \n";
    $stSQL .= "     AND     conta_credito.cod_plano                 = plano_analitica_credito.cod_plano                      \n";
    $stSQL .= "                                                                                                              \n";
    $stSQL .= "     AND     plano_analitica_credito.exercicio       = plano_conta_credito.exercicio                          \n";
    $stSQL .= "     AND     plano_analitica_credito.cod_conta       = plano_conta_credito.cod_conta                          \n";

    $stSQL .= "     AND     lote.exercicio      = '".$this->getDado("exercicio")."'                                          \n";
    $stSQL .= "     AND     lote.cod_entidade   = ".$this->getDado("cod_entidade")."                                         \n";

    if ( $this->getDado("stDtInicial") == $this->getDado("stDtFinal") ) {
        $stSQL .= " AND     lote.dt_lote = to_date('".$this->getDado("stDtInicial")."','dd/mm/yyyy')                         \n";
    } else {
        $stSQL .= " AND     lote.dt_lote between to_date('".$this->getDado("stDtInicial")."','dd/mm/yyyy')                   \n";
        $stSQL .= "                          and to_date('".$this->getDado("stDtFinal")."','dd/mm/yyyy')                     \n";
    }
    $stSQL .= " ORDER BY lote.dt_lote, lancamento.oid                                                                        \n";

    return $stSQL;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRelatorioDiario.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function relatorioDiario(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRelatorioDiario().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta sql para relatório Diário
    * @access Private
    * @return String
*/
function montaRetornaTotalizadorPeriodo()
{
    $stSQL  = " SELECT                                                                                          \n";
    $stSQL .= "          abs(sum(vl_d.vl_lancamento)) as vl_lancamento_debito                                   \n";
    $stSQL .= "         ,abs(sum(vl_c.vl_lancamento)) as vl_lancamento_credito                                  \n";
    $stSQL .= " FROM     contabilidade.lancamento           as lanc                                             \n";
    $stSQL .= "         ,contabilidade.lote                 as lote                                             \n";
    $stSQL .= "         ,contabilidade.historico_contabil   as hist                                             \n";
    $stSQL .= "                                                                                                 \n";
    $stSQL .= "         ,contabilidade.valor_lancamento     as vl_d                                             \n";
    $stSQL .= "                                                                                                 \n";
    $stSQL .= "         ,contabilidade.valor_lancamento     as vl_c                                             \n";
    $stSQL .= "                                                                                                 \n";
    $stSQL .= " WHERE   lanc.exercicio      = lote.exercicio                                                    \n";
    $stSQL .= " AND     lanc.cod_entidade   = lote.cod_entidade                                                 \n";
    $stSQL .= " AND     lanc.tipo           = lote.tipo                                                         \n";
    $stSQL .= " AND     lanc.cod_lote       = lote.cod_lote                                                     \n";
    $stSQL .= "                                                                                                 \n";
    $stSQL .= " AND     lanc.exercicio      = hist.exercicio                                                    \n";
    $stSQL .= " AND     lanc.cod_historico  = hist.cod_historico                                                \n";
    $stSQL .= "                                                                                                 \n";
    $stSQL .= " /*Débito - início*/                                                                             \n";
    $stSQL .= " AND     lanc.exercicio      = vl_d.exercicio                                                    \n";
    $stSQL .= " AND     lanc.cod_entidade   = vl_d.cod_entidade                                                 \n";
    $stSQL .= " AND     lanc.sequencia      = vl_d.sequencia                                                    \n";
    $stSQL .= " AND     lanc.cod_lote       = vl_d.cod_lote                                                     \n";
    $stSQL .= " AND     lanc.tipo           = vl_d.tipo                                                         \n";
    $stSQL .= " AND     vl_d.tipo_valor     = 'D'                                                               \n";
    $stSQL .= " /*Débito - fim*/                                                                                \n";
    $stSQL .= "                                                                                                 \n";
    $stSQL .= " /*Crédito - início*/                                                                            \n";
    $stSQL .= " AND     lanc.exercicio      = vl_c.exercicio                                                    \n";
    $stSQL .= " AND     lanc.cod_entidade   = vl_c.cod_entidade                                                 \n";
    $stSQL .= " AND     lanc.sequencia      = vl_c.sequencia                                                    \n";
    $stSQL .= " AND     lanc.cod_lote       = vl_c.cod_lote                                                     \n";
    $stSQL .= " AND     lanc.tipo           = vl_c.tipo                                                         \n";
    $stSQL .= " AND     vl_c.tipo_valor     = 'C'                                                               \n";
    $stSQL .= " /*Crédito - fim*/                                                                               \n";
    $stSQL .= "                                                                                                 \n";
    $stSQL .= " AND     lanc.exercicio      = '".$this->getDado("exercicio")."'                                 \n";
    $stSQL .= " AND     lanc.cod_entidade   = ".$this->getDado("cod_entidade")."                                \n";
    $stSQL .= " AND     lote.dt_lote between to_date('".$this->getDado("stDtInicial")."','dd/mm/yyyy')          \n";
    $stSQL .= "                          and to_date('".$this->getDado("stDtFinal")."','dd/mm/yyyy')            \n";
    $stSQL .= " AND     lanc.cod_entidade   = ".$this->getDado("cod_entidade")."                                \n";
    $stSQL .= "                                                                                                 \n";

    return $stSQL;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRelatorioTotalizadorPeriodo.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function retornaTotalizadorPeriodo(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRetornaTotalizadorPeriodo().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaProximaSequencia(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaProximaSequencia();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProximaSequencia()
{
    $stSql  = " SELECT (max(sequencia) + 1) as prox_seq \n";
    $stSql .= "   FROM contabilidade.lancamento         \n";

    return $stSql;
}

function recuperaLancamentoEmpenhoContaCredito(&$rsRecordSet, $stFiltro, $boTransacao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaLancamentoEmpenhoContaCredito().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentoEmpenhoContaCredito()
{
    $stSql = "  SELECT lancamento.*
                     , plano_conta.cod_estrutural AS cod_estrutural_mascara
                     , plano_analitica.cod_plano
                     , REPLACE(plano_conta.cod_estrutural, '.', '') AS cod_estrutural
                  FROM contabilidade.lancamento
            INNER JOIN contabilidade.lancamento_empenho
                    ON lancamento.exercicio = lancamento_empenho.exercicio
                   AND lancamento.cod_entidade = lancamento_empenho.cod_entidade
                   AND lancamento.tipo = lancamento_empenho.tipo
                   AND lancamento.cod_lote = lancamento_empenho.cod_lote
                   -- AND lancamento.sequencia = lancamento_empenho.sequencia
            INNER JOIN contabilidade.liquidacao
                    ON lancamento_empenho.exercicio = liquidacao.exercicio
                   AND lancamento_empenho.cod_lote = liquidacao.cod_lote
                   AND lancamento_empenho.tipo = liquidacao.tipo
                   -- AND lancamento_empenho.sequencia = liquidacao.sequencia
                   AND lancamento_empenho.cod_entidade = liquidacao.cod_entidade
            INNER JOIN contabilidade.valor_lancamento
                    ON lancamento.exercicio = valor_lancamento.exercicio
                   AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                   AND lancamento.tipo = valor_lancamento.tipo
                   AND lancamento.cod_lote = valor_lancamento.cod_lote
                   AND lancamento.sequencia = valor_lancamento.sequencia
            INNER JOIN contabilidade.conta_credito
                    ON valor_lancamento.exercicio = conta_credito.exercicio
                   AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                   AND valor_lancamento.tipo = conta_credito.tipo
                   AND valor_lancamento.cod_lote = conta_credito.cod_lote
                   AND valor_lancamento.sequencia = conta_credito.sequencia
                   AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
            INNER JOIN contabilidade.plano_analitica
                    ON conta_credito.cod_plano = plano_analitica.cod_plano
                   AND conta_credito.exercicio = plano_analitica.exercicio
            INNER JOIN contabilidade.plano_conta
                    ON plano_analitica.cod_conta = plano_conta.cod_conta
                   AND plano_analitica.exercicio = plano_conta.exercicio ";

    return $stSql;
}

function excluiLancamentosAberturaAnteriores($boTransacao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExcluiLancamentosAberturaAnteriores();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExcluiLancamentosAberturaAnteriores()
{
    if(!$this->getDado("tipo"))
        $this->setDado('tipo', 'M');
    
    $stSql = "
    --CONTA DEBITO
            DELETE from contabilidade.conta_debito
            where exercicio  = '".Sessao::getExercicio()."'
            and cod_entidade = ".$this->getDado("cod_entidade")."
            and tipo = '".$this->getDado("tipo")."'
            and cod_lote IN (".$this->getDado('cod_lote').");

    --CONTA CREDITO
            DELETE from contabilidade.conta_credito
            where exercicio  = '".Sessao::getExercicio()."'
            and cod_entidade = ".$this->getDado("cod_entidade")."
            and tipo = '".$this->getDado("tipo")."'
            and cod_lote IN (".$this->getDado('cod_lote').");
    
    --VALOR LANCAMENTO
            DELETE from contabilidade.valor_lancamento
            where exercicio  = '".Sessao::getExercicio()."'
            and cod_entidade = ".$this->getDado("cod_entidade")."
            and tipo = '".$this->getDado("tipo")."'
            and cod_lote IN (".$this->getDado('cod_lote').");

    --CONTABILIDADE LANCAMENTO
            DELETE  from contabilidade.lancamento
            where exercicio  = '".Sessao::getExercicio()."'
            and cod_entidade = ".$this->getDado("cod_entidade")."
            and tipo = '".$this->getDado("tipo")."'
            and cod_lote IN (".$this->getDado('cod_lote').");
    ";
    
    return $stSql;
}

}//fim classe
