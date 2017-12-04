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

    * Classe de mapeamento da tabela ARRECADACAO.PAGAMENTO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPagamento.class.php 64995 2016-04-18 18:40:22Z evandro $

* Casos de uso: uc-05.03.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.PAGAMENTO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRPagamento()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.pagamento');

    $this->setCampoCod('');
    $this->setComplementoChave('numeracao,ocorrencia_pagamento,exercicio');

    $this->AddCampo('numeracao','varchar',true,'17',true,true);
    $this->AddCampo('ocorrencia_pagamento','integer',true,'',true,false);
    $this->AddCampo('cod_convenio','integer',true,'',true,false);
    $this->AddCampo('data_pagamento','date',true,'',false,false);
    $this->AddCampo('inconsistente','boolean',true,'',false,false);
    $this->AddCampo('valor','numeric',true,'14,2',false,false);
    $this->AddCampo('observacao','text',true,'',false,false);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    $this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('data_baixa','date',false,'',false,false);
}

function recuperaPagamentoPorCreditoConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPagamentoPorCreditoConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPagamentoPorCreditoConsulta()
{
    $stSql .= " SELECT                                                                              \n";
    $stSql .= "     apag.numeracao,                                                                 \n";
    $stSql .= "     ac.cod_credito,                                                                 \n";
    $stSql .= "     ac.cod_especie,                                                                 \n";
    $stSql .= "     ac.cod_genero,                                                                  \n";
    $stSql .= "     ac.cod_natureza,                                                                \n";
    $stSql .= "     mc.descricao_credito,                                                           \n";
    $stSql .= "     0.00 as desconto_credito,                                                       \n";
    $stSql .= "     apar.nr_parcela                                                                 \n";
    $stSql .= "     , ( CASE WHEN apar.nr_parcela = 0 THEN                                          \n";
    $stSql .= "             ac.valor                                                                \n";
    $stSql .= "         ELSE                                                                        \n";
    $stSql .= "             ac.valor / arrecadacao.fn_total_parcelas(alc.cod_lancamento)            \n";
    $stSql .= "         END                                                                         \n";
    $stSql .= "     )::numeric(14,2) as valor_credito                                               \n";
    $stSql .= "     , ( select sum(valor)                                                           \n";
    $stSql .= "         from arrecadacao.pagamento_acrescimo y, monetario.acrescimo z               \n";
    $stSql .= "         where y.numeracao = apag.numeracao and y.cod_acrescimo = z.cod_acrescimo    \n";
    $stSql .= "             and y.cod_calculo=ac.cod_calculo and z.cod_tipo=2                       \n";
    $stSql .= "     ) as valor_juro                                                                 \n";
    $stSql .= "     , ( select sum(valor) from arrecadacao.pagamento_acrescimo y,                   \n";
    $stSql .= "             monetario.acrescimo z                                                   \n";
    $stSql .= "         where y.numeracao = apag.numeracao and y.cod_acrescimo = z.cod_acrescimo    \n";
    $stSql .= "             and y.cod_calculo=ac.cod_calculo and z.cod_tipo=3                       \n";
    $stSql .= "     ) as valor_multa                                                                \n";
    $stSql .= "     , 0.00 as valor_correcao                                                        \n";
    $stSql .= "     , apc.valor as valor_total_credito                                              \n";

    $stSql .= " FROM                                                                                \n";

    $stSql .= "     arrecadacao.calculo ac                                                          \n";

    $stSql .= "     INNER JOIN arrecadacao.lancamento_calculo alc                                   \n";
    $stSql .= "     ON alc.cod_calculo = ac.cod_calculo                                             \n";

    $stSql .= "     INNER JOIN monetario.credito mc                                                 \n";
    $stSql .= "     ON mc.cod_credito = ac.cod_credito                                              \n";
    $stSql .= "     AND mc.cod_especie = ac.cod_especie                                             \n";
    $stSql .= "     AND mc.cod_genero  = ac.cod_genero                                              \n";
    $stSql .= "     AND mc.cod_natureza= ac.cod_natureza                                            \n";

    $stSql .= "     , arrecadacao.pagamento_calculo apc                                             \n";
    $stSql .= "     , arrecadacao.pagamento apag                                                    \n";

    $stSql .= "     INNER JOIN arrecadacao.carne as carne                                           \n";
    $stSql .= "     ON carne.numeracao = apag.numeracao                                             \n";

    $stSql .= "     INNER JOIN arrecadacao.parcela as apar                                          \n";
    $stSql .= "     ON apar.cod_parcela = carne.cod_parcela                                         \n";

    $stSql .= " WHERE                                                                               \n";
    $stSql .= "     ac.cod_calculo = apc.cod_calculo                                                \n";
    $stSql .= "     AND apc.numeracao = apag.numeracao                                              \n";
    $stSql .= "     AND apc.ocorrencia_pagamento = apag.ocorrencia_pagamento                        \n";

    return $stSql;

}

function recuperaResumoLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaResumoLote().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaResumoLote()
{
    $stSql .= " select                                                                          \n";
    $stSql .= "     l.cod_lote                                                                  \n";
    $stSql .= "     , la.nom_arquivo                                                            \n";
    $stSql .= "     , to_char(l.data_lote, 'dd/mm/YYYY') as data_lote                           \n";
    $stSql .= "     , to_char(arrecadacao.dtBaixaLote(l.cod_lote,                               \n";
    $stSql .= "                 l.exercicio::int ),'dd/mm/YYYY'                                 \n";
    $stSql .= "     ) as data_baixa                                                             \n";
    $stSql .= "     , mb.cod_banco                                                              \n";
    $stSql .= "     , mb.num_banco, mb.nom_banco                                                \n";
    $stSql .= "     , ma.cod_agencia                                                            \n";
    $stSql .= "     , ma.num_agencia                                                            \n";
    $stSql .= "     , ma.nom_agencia                                                            \n";
    $stSql .= "     , l.exercicio, now() as hoje                                                \n";
    $stSql .= " FROM                                                                            \n";
    $stSql .= "     arrecadacao.lote as l                                                       \n";
    $stSql .= "     , arrecadacao.lote_arquivo as la                                            \n";
    $stSql .= "     , monetario.banco as mb                                                     \n";
    $stSql .= "     , monetario.agencia as ma                                                   \n";
    $stSql .= " WHERE                                                                           \n";
    $stSql .= "     l.cod_banco = mb.cod_banco                                                  \n";
    $stSql .= "     and l.cod_banco = ma.cod_banco                                              \n";
    $stSql .= "     and l.cod_agencia = ma.cod_agencia                                          \n";
    $stSql .= "     and la.cod_lote = l.cod_lote                                                \n";
    $stSql .= "     and la.exercicio = l.exercicio                                              \n";

    return $stSql;

}

function recuperaResumoLoteLista(&$rsRecordSet, $stFiltro = "", $stFiltroSomaPL ="", $stOrdem = "", $boTransacao = "", $stFiltroJoin = "", $boDivida = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaResumoLoteLista($stFiltro, $stFiltroSomaPL, $stFiltroJoin, $boDivida).$stGrupo.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaResumoLoteLista($stFiltro, $stFiltroSomaPL, $stFiltroJoin)
{
    $stSql  = " SELECT                                                                                  \n";
    $stSql .= "     tudo.origem                                                                         \n";
    $stSql .= "     , tudo.cod_grupo                                                                    \n";
    $stSql .= "     , tudo.descricao                                                                    \n";
    $stSql .= "     , tudo.exercicio                                                                    \n";
    $stSql .= "     , sum(valor) as somatorio                                                           \n";
    $stSql .= "     , split_part ( acrescimos, '§', 1 )::numeric(14,2) as juros                         \n";
    $stSql .= "     , split_part ( acrescimos, '§', 2 )::numeric(14,2) as multa                         \n";
    $stSql .= "     , tudo.diferenca                                                                    \n";
    $stSql .= "     , (    sum(valor)                                                                   \n";
    $stSql .= "            + split_part ( acrescimos, '§', 1 )::numeric(14,2)                           \n";
    $stSql .= "            + split_part ( acrescimos, '§', 2 )::numeric(14,2)                           \n";
    $stSql .= "            + tudo.diferenca                                                             \n";
    $stSql .= "     ) as total                                                                          \n";

    $stSql .= " FROM                                                                                    \n";
    $stSql .= " (                                                                                       \n";
    $stSql .= "     select                                                                              \n";
    $stSql .= "         split_part ( monetario.fn_busca_mascara_credito( c.cod_credito, c.cod_especie,  \n";
    $stSql .= "                      c.cod_genero, c.cod_natureza ), '§' , 1 )::varchar                 \n";
    $stSql .= "     as origem                                                                           \n";
    $stSql .= "     , acgc.cod_grupo                                                                    \n";
    $stSql .= "     , mc.descricao_credito as descricao                                                 \n";
    $stSql .= "     , c.exercicio                                                                       \n";

    $stSql .= "     , arrecadacao.somaPagAcrescimosLoteDetalhe ( plote.cod_lote, c.exercicio::int,      \n";
    $stSql .= "       c.cod_credito, c.cod_especie, c.cod_genero, c.cod_natureza,                       \n";
    $stSql .= "       '".$stFiltroSomaPL."'                                                             \n";
    $stSql .= "     ) as acrescimos                                                                     \n";

    $stSql .= "     , arrecadacao.somaPagDifCredito ( plote.cod_lote, c.exercicio::int,                 \n";
    $stSql .= "                 c.cod_credito, c.cod_especie, c.cod_genero, c.cod_natureza,             \n";
    $stSql .= "    '".$stFiltroSomaPL."')                                 \n";
    $stSql .= "     as diferenca                                                                        \n";
    $stSql .= "     , pagc.numeracao                                                                    \n";
    $stSql .= "     , pagc.ocorrencia_pagamento                                                         \n";
    $stSql .= "     , pagc.valor                                                                        \n";

    $stSql .= " FROM                                                                                    \n";
    $stSql .= "     arrecadacao.calculo c                                                               \n";

    $stSql .= "     INNER JOIN arrecadacao.pagamento_calculo pagc                                       \n";
    $stSql .= "     ON pagc.cod_calculo = c.cod_calculo                                                 \n";

    $stSql .= "     INNER JOIN arrecadacao.pagamento_lote plote                                         \n";
    $stSql .= "     ON plote.numeracao = pagc.numeracao                                                 \n";
    $stSql .= "     AND plote.cod_convenio = pagc.cod_convenio                                          \n";
    $stSql .= "     AND plote.ocorrencia_pagamento = pagc.ocorrencia_pagamento                          \n";

    $stSql .= $stFiltroJoin;

    $stSql .= "     INNER JOIN monetario.credito mc                                                     \n";
    $stSql .= "     ON mc.cod_credito = c.cod_credito                                                   \n";
    $stSql .= "     AND mc.cod_especie = c.cod_especie                                                  \n";
    $stSql .= "     AND mc.cod_genero = c.cod_genero                                                    \n";
    $stSql .= "     AND mc.cod_natureza = c.cod_natureza                                                \n";

    $stSql .= "     LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                                 \n";
    $stSql .= "     ON acgc.cod_calculo = c.cod_calculo                                                 \n";

    $stSql .= "     LEFT JOIN arrecadacao.grupo_credito as agc                                          \n";
    $stSql .= "     ON agc.cod_grupo = acgc.cod_grupo                                                   \n";
    $stSql .= "     AND agc.ano_exercicio = acgc.ano_exercicio                                          \n";

    $stSql .= "     ". $stFiltro ."                                                                     \n";
    $stSql .= "     and plote.numeracao not in ( select numeracao from arrecadacao.lote_inconsistencia )\n";

    $stSql .= " ORDER BY                                                                                \n";
    $stSql .= "     c.exercicio, acgc.cod_grupo, c.cod_credito                                          \n";
    $stSql .= " ) as tudo                                                                               \n";

    $stSql .= " GROUP BY                                                                                \n";
    $stSql .= "     tudo.origem, tudo.cod_grupo, tudo.descricao                                         \n";
    $stSql .= "     , tudo.exercicio                                                                    \n";
    $stSql .= "     , tudo.acrescimos, tudo.diferenca                                                   \n";

    return $stSql;
}

function recuperaResumoLoteListaOrigem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaResumoLoteListaOrigem( $stFiltro ). $stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaResumoLoteListaOrigem($stFiltro)
{
    $stSql  = "
     SELECT * FROM (

     SELECT

     ( CASE WHEN agc.cod_grupo is not null then
                 'grupo'
             else
                 'credito'
             end
         ) as tipo
         , ( CASE WHEN agc.cod_grupo is not null then
                 LPAD( agc.cod_grupo::varchar, (select length( max(cod_grupo)::varchar) as valor from
                       arrecadacao.grupo_credito ) , '0')::varchar
             else
                 split_part ( monetario.fn_busca_mascara_credito( mc.cod_credito,
                     mc.cod_especie, mc.cod_genero, mc.cod_natureza),'§', 1 )::varchar
             end
         ) as origem
          , CASE WHEN al.divida = true THEN
                ( CASE WHEN agc.cod_grupo is not null then
                        agc.descricao||'(D.A.)'
                    else
                        mc.descricao_credito||'(D.A.)'
                    end
                )
            ELSE
                ( CASE WHEN agc.cod_grupo is not null then
                        agc.descricao
                    else
                        mc.descricao_credito
                    end
                )
            END as descricao
         , calc.exercicio as origem_exercicio
         , carne.cod_convenio
         , numeracoes.tipo_numeracao
FROM
    (

    SELECT
        cod_lote
        , exercicio as lote_exercicio
        , numeracao
        , tipo_numeracao

    FROM
        (
        select

            lote.cod_lote
            , lote.exercicio
            , plote.numeracao
            , 'PAGAMENTO'::varchar as tipo_numeracao

        FROM

            arrecadacao.lote

            INNER JOIN arrecadacao.pagamento_lote as plote
            ON plote.cod_lote = lote.cod_lote
            AND plote.exercicio = lote.exercicio

\n";

    $stSql .= "     ". $stFiltro ."                                                                     \n";
    $stSql .= "

    UNION

        select

            lote.cod_lote
            , lote.exercicio
            , ali.numeracao
            , 'INCONSISTENCIA'::varchar as tipo_numeracao

        FROM
            arrecadacao.lote

            INNER JOIN arrecadacao.lote_inconsistencia as ali
            ON ali.cod_lote = lote.cod_lote
            AND ali.exercicio = lote.exercicio

\n";

    $stSql .= "     ". $stFiltro ."                                                                     \n";
    $stSql .= "

    ) as numeracoes



) as numeracoes

INNER JOIN arrecadacao.carne
ON carne.numeracao = numeracoes.numeracao
--AND carne.exercicio = numeracoes.lote_exercicio

INNER JOIN arrecadacao.parcela as ap
ON ap.cod_parcela = carne.cod_parcela

INNER JOIN arrecadacao.lancamento_calculo as alc
ON alc.cod_lancamento = ap.cod_lancamento

INNER JOIN arrecadacao.lancamento as al
ON al.cod_lancamento = ap.cod_lancamento

LEFT JOIN arrecadacao.calculo as calc
ON calc.cod_calculo = alc.cod_calculo

LEFT JOIN monetario.credito as mc
ON mc.cod_credito = calc.cod_credito
AND mc.cod_especie = calc.cod_especie
AND mc.cod_genero = calc.cod_genero
AND mc.cod_natureza = calc.cod_natureza

LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
ON acgc.cod_calculo = calc.cod_calculo

LEFT JOIN arrecadacao.grupo_credito as agc
ON agc.cod_grupo = acgc.cod_grupo
AND agc.ano_exercicio = acgc.ano_exercicio


GROUP BY
    agc.cod_grupo, agc.descricao
    , mc.descricao_credito
    , mc.cod_credito
    , mc.cod_especie
    , mc.cod_genero
    , mc.cod_natureza
    , calc.exercicio
    , al.divida
    , carne.cod_convenio
    , numeracoes.tipo_numeracao
ORDER BY
    tipo, calc.exercicio, descricao
) as origem
GROUP BY origem.tipo
       , origem.origem
       , origem.descricao
       , origem.origem_exercicio
       , origem.cod_convenio
       , origem.tipo_numeracao
 \n";

    return $stSql;

}

function recuperaResumoLoteListaInconsistenteAgrupado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaResumoLoteListaInconsistenteAgrupado( $stFiltro ); #.$stGrupo.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
//1
function montaRecuperaResumoLoteListaInconsistenteAgrupado($stFiltro)
{
    $stSql  = " SELECT DISTINCT                                                                         \n";
    $stSql .= "     tudo.numeracao                                                                      \n";
    $stSql .= "     , tudo.origem                                                                       \n";
    $stSql .= "     , tudo.cod_grupo                                                                    \n";
    $stSql .= "     , tudo.descricao                                                                    \n";
    $stSql .= "     , tudo.inscricao                                                                    \n";
    $stSql .= "     , tudo.contribuinte                                                                 \n";
    $stSql .= "     , tudo.exercicio                                                                    \n";
    $stSql .= "     , tudo.data_pagamento                                                               \n";
    $stSql .= "     , tudo.valor                                                                        \n";

    $stSql .= " FROM                                                                                    \n";
    $stSql .= " (                                                                                       \n";
    $stSql .= "     SELECT                                                                              \n";
    $stSql .="          ali.numeracao                                                                   \n";
    $stSql .="          , ( CASE WHEN acgc.cod_grupo IS NOT NULL THEN                                   \n";
    $stSql .="                  agc.descricao                                                           \n";
    $stSql .="              ELSE                                                                        \n";
    $stSql .="                  split_part ( monetario.fn_busca_mascara_credito( c.cod_credito,         \n";
    $stSql .="                  c.cod_especie, c.cod_genero, c.cod_natureza ), '§' , 1 )::varchar       \n";
    $stSql .="              END                                                                         \n";
    $stSql .="          ) as origem                                                                     \n";
    $stSql .="          , acgc.cod_grupo                                                                \n";
    $stSql .="          , mc.descricao_credito as descricao                                             \n";
    $stSql .="          , c.exercicio                                                                   \n";
    $stSql .="          , ali.valor                                                                     \n";
    $stSql .="          , to_char(ali.data_pagamento, 'dd/mm/YYYY') as data_pagamento                   \n";
    $stSql .="          , arrecadacao.buscaInscricaoLancamento ( alc.cod_lancamento ) as inscricao      \n";
    //$stSql .="          , ( cgm.numcgm||' - '||cgm.nom_cgm ) as contribuinte                            \n";
    $stSql .="          , descobreProprietarios ( alc.cod_lancamento ) as contribuinte                  \n";
    $stSql .="      FROM                                                                                \n";
    $stSql .="          arrecadacao.calculo c                                                           \n";
    $stSql .="          INNER JOIN (                                                                    \n";
    $stSql .="              select                                                                      \n";
    $stSql .="                  max(cod_calculo) as cod_calculo                                         \n";
    $stSql .="                  , cod_lancamento                                                        \n";
    $stSql .="              from                                                                        \n";
    $stSql .="                  arrecadacao.lancamento_calculo as alc                                   \n";
    $stSql .="              GROUP BY cod_lancamento                                                     \n";
    $stSql .="          ) as alc                                                                        \n";
    $stSql .="          ON alc.cod_calculo = c.cod_calculo                                              \n";

    $stSql .="          INNER JOIN arrecadacao.calculo_cgm  as accgm                                    \n";
    $stSql .="          ON accgm.cod_calculo = c.cod_calculo                                            \n";
    $stSql .="          INNER JOIN sw_cgm as cgm                                                        \n";
    $stSql .="          ON cgm.numcgm = accgm.numcgm                                                    \n";

    $stSql .="          INNER JOIN arrecadacao.parcela as ap                                            \n";
    $stSql .="          ON ap.cod_lancamento = alc.cod_lancamento                                       \n";

    $stSql .="          INNER JOIN arrecadacao.carne as carne                                           \n";
    $stSql .="          ON carne.cod_parcela = ap.cod_parcela                                           \n";

    $stSql .="          INNER JOIN arrecadacao.lote_inconsistencia as ali                               \n";
    $stSql .="          ON ali.numeracao = carne.numeracao                                              \n";

    $stSql .="          LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                             \n";
    $stSql .="          ON acgc.cod_calculo = c.cod_calculo                                             \n";
    $stSql .="          INNER JOIN monetario.credito mc                                                 \n";
    $stSql .="          ON mc.cod_credito = c.cod_credito                                               \n";
    $stSql .="          and mc.cod_especie = c.cod_especie                                              \n";
    $stSql .="          and mc.cod_genero = c.cod_genero                                                \n";
    $stSql .="          and mc.cod_natureza = c.cod_natureza                                            \n";

    $stSql .="          LEFT JOIN arrecadacao.grupo_credito as agc                                      \n";
    $stSql .="          ON agc.cod_grupo = acgc.cod_grupo                                               \n";
    $stSql .="          AND agc.ano_exercicio = acgc.ano_exercicio                                      \n";

    $stSql .="          ". $stFiltro ."                                                                 \n";

    $stSql .="      GROUP BY                                                                            \n";
    $stSql .="          ali.numeracao, ali.data_pagamento                                               \n";
    $stSql .="          , c.cod_credito, c.exercicio , acgc.cod_grupo                                   \n";
    $stSql .="          , c.cod_especie, c.cod_genero, c.cod_natureza                                   \n";
    $stSql .="          , mc.descricao_credito ,  agc.descricao                                         \n";
    $stSql .="          , alc.cod_lancamento, cgm.numcgm, cgm.nom_cgm                                   \n";
    $stSql .="          , ali.valor                                                                     \n";

    $stSql .="      ORDER BY                                                                            \n";
    $stSql .="          c.exercicio, acgc.cod_grupo desc, c.cod_credito                                 \n";

    $stSql .= " ) as tudo                                                                               \n";

    return $stSql;

}

function recuperaResumoLoteListaDiff(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaResumoLoteDiff($stFiltro).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaResumoLoteDiff($stFiltro)
{
    $stSql =  " select                                                                                  \n";
    $stSql .= "     '' as cod                                                                           \n";
    $stSql .= "     , 0 as cod_credito                                                                  \n";
    $stSql .= "     , 0 as cod_especie                                                                  \n";
    $stSql .= "     , 0 as cod_genero                                                                   \n";
    $stSql .= "     , 0 as cod_natureza                                                                 \n";
    $stSql .= "     , 'DIFERENÇA DE PAGAMENTO'                                                          \n";
    $stSql .= "     , '0.00'::numeric                                                                   \n";
    $stSql .= "     , '0.00'::numeric                                                                   \n";
    $stSql .= "     , arrecadacao.somaPagDiffLote(".$stFiltro.") as somatorio;                          \n";

    return $stSql;

}

function recuperaResumoLoteListaInconsistente(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaResumoLoteListaInconsistente().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaResumoLoteListaInconsistente()
{
    $stSql  = " select                                                                          \n";
    $stSql .= "     li.numeracao                                                                \n";
    $stSql .= "     , to_char ( li.data_pagamento, 'dd/mm/yyyy' ) as data_pagamento             \n";
    $stSql .= "     , replace ( li.valor::varchar,'.',',' ) as valor                            \n";
    $stSql .= " from                                                                            \n";
    $stSql .= "     arrecadacao.lote_inconsistencia li                                          \n";

    return $stSql;
}

function recuperaResumoLoteBaixaManual(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaResumoLoteBaixaManual().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaResumoLoteBaixaManual()
{
    $stSql .= " SELECT                                                                                  \n";
    $stSql .= "     l.cod_lote                                                                          \n";
    $stSql .= "     , to_char(l.data_lote, 'dd/mm/YYYY') as data_lote                                   \n";
    $stSql .= "     , to_char(arrecadacao.dtBaixaLote(l.cod_lote, l.exercicio::int),'dd/mm/YYYY') as data_baixa\n";
    $stSql .= "     , mb.cod_banco                                                                      \n";
    $stSql .= "     , mb.num_banco                                                                      \n";
    $stSql .= "     , mb.nom_banco                                                                      \n";
    $stSql .= "     , ma.cod_agencia                                                                    \n";
    $stSql .= "     , ma.num_agencia                                                                    \n";
    $stSql .= "     , ma.nom_agencia                                                                    \n";
    $stSql .= "     , l.exercicio, now() as hoje                                                        \n";
    $stSql .= "     , la.nom_arquivo                                                                    \n";
    $stSql .= "     ,( select count(*) from arrecadacao.pagamento_lote where cod_lote=l.cod_lote) as registros \n";
    $stSql .= " FROM                                                                                    \n";
    $stSql .= "     arrecadacao.lote as l                                                               \n";
    $stSql .= "     INNER JOIN monetario.banco as mb ON mb.cod_banco = l.cod_banco                      \n";
    $stSql .= "     INNER JOIN monetario.agencia as ma                                                  \n";
    $stSql .= "     ON ma.cod_agencia = l.cod_agencia and mb.cod_banco = ma.cod_banco                   \n";
    $stSql .= "     LEFT JOIN arrecadacao.lote_arquivo as la ON la.cod_lote = l.cod_lote                \n";

    return $stSql;

}

function recuperaConsultaLotesPagamento(&$rsRecordSet, $stFiltro = "", $stFiltroCGM ="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montarecuperaConsultaLotesPagamento( $stFiltroCGM, $stFiltro, $stOrdem );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montarecuperaConsultaLotesPagamento($stFiltroCGM, $stFiltro, $stOrdem)
{
    $stSql  = " SELECT  \n";
    $stSql .= "      *  \n";
    $stSql .= "      , ( coalesce(soma_i,0.00) + coalesce (soma_p, 0.00) ) as soma  \n";
    $stSql .= "  FROM   \n";
    $stSql .= "    (    \n";
    $stSql .= " SELECT DISTINCT ala.cod_lote as cod_lote    \n";
    $stSql .= "      , ala.nom_arquivo  \n";
    $stSql .= "      , ala.exercicio    \n";
    $stSql .= "      , to_char(lote.data_lote, 'dd/mm/YYYY') as data_lote   \n";
    $stSql .= "      , CASE WHEN lote.automatico = 't' THEN 'Baixa Automática'::text    \n";
    $stSql .= "             WHEN lote.automatico = 'f' AND atp.pagamento = 't' THEN 'Baixa Manual'::text    \n";
    $stSql .= "        ELSE 'Cancelamento Manual'::text  END as nom_tipo    \n";
    $stSql .= "      , now()::timestamp(3) as hoje  \n";
    $stSql .= "      , (
                        SELECT
                            sum(ali.valor)
                        FROM
                            arrecadacao.lote_inconsistencia as ali
                        WHERE
                            ali.cod_lote = ala.cod_lote
                            AND ali.exercicio = ala.exercicio
                    ) as soma_i
                    , (
                            SELECT
                                sum( apag.valor )
                            FROM
                                arrecadacao.pagamento_lote pag_lote

                            LEFT JOIN arrecadacao.pagamento as apag
                            ON apag.numeracao = pag_lote.numeracao
                                AND pag_lote.ocorrencia_pagamento = apag.ocorrencia_pagamento

                            LEFT JOIN arrecadacao.tipo_pagamento as atp
                            ON atp.cod_tipo = apag.cod_tipo

                            WHERE
                                ala.cod_lote = pag_lote.cod_lote
                                AND pag_lote.exercicio = ala.exercicio
                    )as soma_p \n";

    $stSql .= "      , to_char(apag.data_baixa,'dd/mm/YYYY') as data_baixa  \n";
    $stSql .= "      , cgm.numcgm   \n";
    $stSql .= "      , cgm.nom_cgm  \n";
    $stSql .= "      , mb.cod_banco     \n";
    $stSql .= "      , mb.num_banco     \n";
    $stSql .= "      , mb.nom_banco     \n";
    $stSql .= "      , mag.cod_agencia  \n";
    $stSql .= "      , mag.num_agencia  \n";
    $stSql .= "      , mag.nom_agencia  \n";

    if ($stFiltroCGM) {
        $stSql .= " , accgm.numcgm as cgm_contribuinte                                                  \n";
        $stSql .="  , arrecadacao.buscaContribuinteLancamento (apar.cod_lancamento) as nom_contribuinte \n";
    }
    $stSql .= "   FROM arrecadacao.lote_arquivo as ala  \n";

    $stSql .= "  INNER JOIN arrecadacao.lote    \n";
    $stSql .= "     ON lote.cod_lote  = ala.cod_lote    \n";
    $stSql .= "    AND lote.exercicio = ala.exercicio   \n";
    $stSql .= "   LEFT JOIN arrecadacao.lote_inconsistencia as ali  \n";
    $stSql .= "     ON ali.cod_lote = ala.cod_lote  \n";
    $stSql .= "    AND ali.exercicio = ala.exercicio    \n";
    $stSql .= "   LEFT JOIN arrecadacao.pagamento_lote pag_lote     \n";
    $stSql .= "     ON lote.cod_lote = pag_lote.cod_lote    \n";
    $stSql .= "    AND pag_lote.exercicio = lote.exercicio  \n";
    $stSql .= "   LEFT JOIN arrecadacao.pagamento as apag   \n";
    $stSql .= "     ON apag.numeracao = pag_lote.numeracao  \n";
    $stSql .= "    AND pag_lote.ocorrencia_pagamento = apag.ocorrencia_pagamento    \n";
    $stSql .= "   LEFT JOIN arrecadacao.tipo_pagamento as atp   \n";
    $stSql .= "     ON atp.cod_tipo = apag.cod_tipo     \n";
    $stSql .= "   LEFT JOIN monetario.banco as mb   \n";
    $stSql .= "     ON mb.cod_banco = lote.cod_banco    \n";
    $stSql .= "   LEFT JOIN monetario.agencia mag   \n";
    $stSql .= "     ON mag.cod_agencia = lote.cod_agencia   \n";
    $stSql .= "    AND mag.cod_banco = mb.cod_banco     \n";
    $stSql .= "  INNER JOIN sw_cgm cgm  \n";
    $stSql .= "     ON cgm.numcgm = lote.numcgm     \n";

    if ($stFiltroCGM) {

        $stSql .= " INNER JOIN arrecadacao.carne as acn ON acn.numeracao = apag.numeracao
                          INNER JOIN arrecadacao.parcela as apar ON apar.cod_parcela = acn.cod_parcela
                          INNER JOIN arrecadacao.lancamento_calculo as alc ON alc.cod_lancamento = apar.cod_lancamento
                          INNER JOIN arrecadacao.calculo_cgm as accgm ON accgm.cod_calculo = alc.cod_calculo                           \n";
        $stSql .= $stFiltroCGM;
    }

    $stSql .= "     ". $stFiltro ."                                                                     \n";

    $stSql .= " GROUP BY  ala.cod_lote  \n";
    $stSql .= "     , ala.exercicio     \n";
    $stSql .= "     , ala.nom_arquivo   \n";
    $stSql .= "     , lote.data_lote    \n";
    $stSql .= "     , lote.automatico   \n";
    $stSql .= "     , nom_tipo  \n";
    $stSql .= "     , hoje  \n";
    $stSql .= "     , atp.pagamento     \n";
    $stSql .= "     , apag.data_baixa   \n";
    $stSql .= "     , cgm.numcgm    \n";
    $stSql .= "     , cgm.nom_cgm   \n";
    $stSql .= "     , mb.cod_banco  \n";
    $stSql .= "     , mb.num_banco  \n";
    $stSql .= "     , mb.nom_banco  \n";
    $stSql .= "     , mag.cod_agencia   \n";
    $stSql .= "     , mag.num_agencia   \n";
    $stSql .= "     , mag.nom_agencia   \n";
    if ($stFiltroCGM) {
        $stSql .= " , accgm.numcgm \n";
        $stSql .= " , apar.cod_lancamento \n";
    }
    $stSql .= " ) as busca  \n";

    return $stSql;
}

//2
function recuperaListaPagamentosLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "" , $inLote)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaPagamentosLote( $inLote, $stFiltro ).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaListaPagamentosLote($inLote = false, $stFiltro)
{
    $stSql = "  SELECT                                                                                  \n";
    $stSql .="      tabela2.*                                                                           \n";
    $stSql .= " FROM                                                                                    \n";
    $stSql .= "     (                                                                                   \n";
    $stSql .= "         SELECT                                                                          \n";
    $stSql .= "             tabela.*                                                                    \n";
    $stSql .= "                , ( SELECT coalesce (sum(valor), 0.00) as juros                          \n";
    $stSql .= "                  FROM                                                                   \n";
    $stSql .= "                     arrecadacao.pagamento_acrescimo                                     \n";
    $stSql .= "                 WHERE                                                                   \n";
    $stSql .= "                     cod_tipo = 1                                                        \n";
    $stSql .= "                     and numeracao = tabela.numeracao                                    \n";
    $stSql .= "                     and ocorrencia_pagamento = tabela.ocorrencia_pagamento               \n";
    $stSql .= "                     and cod_convenio = tabela.cod_convenio                              \n";
    $stSql .= "             )::numeric(14,2) as correcao                                                \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "    , COALESCE(                                                                          \n";
    $stSql .= "	( SELECT                                                                                \n";
    $stSql .= "		sum(valor)                                                                          \n";
    $stSql .= "	    FROM                                                                                \n";
    $stSql .= "		arrecadacao.pagamento_diferenca                                                     \n";
    $stSql .= "	    WHERE                                                                               \n";
    $stSql .= "		    pagamento_diferenca.numeracao 	     = tabela.numeracao                         \n";
    $stSql .= "		AND pagamento_diferenca.cod_convenio 	     = tabela.cod_convenio                  \n";
    $stSql .= "		AND pagamento_diferenca.ocorrencia_pagamento = tabela.ocorrencia_pagamento          \n";
    $stSql .= "	), 0.00                                                                                 \n";
    $stSql .= "      )::numeric(14,2) AS diferenca                                                                      \n";
    $stSql .= "             , ( SELECT coalesce (sum(valor), 0.00) as juros                             \n";
    $stSql .= "                 FROM                                                                    \n";
    $stSql .= "                     arrecadacao.pagamento_acrescimo                                     \n";
    $stSql .= "                 WHERE                                                                   \n";
    $stSql .= "                     cod_tipo = 2                                                        \n";
    $stSql .= "                     and numeracao = tabela.numeracao                                    \n";
    $stSql .= "                     and ocorrencia_pagamento = tabela.ocorrencia_pagamento              \n";
    $stSql .= "                     and cod_convenio = tabela.cod_convenio                              \n";
    $stSql .= "             )::numeric(14,2) as juros                                                   \n";
    $stSql .= "             , ( select coalesce ( ( aplica_juro (                                       \n";
    $stSql .= "                                         tabela.numeracao                                \n";
    $stSql .= "                                         , tabela.exercicio                              \n";
    $stSql .= "                                         , tabela.cod_parcela                            \n";
    $stSql .= "                                         , tabela.data_pagamento )                       \n";
    $stSql .= "                                     * arrecadacao.calculaProporcaoParcela(              \n";
    $stSql .= "                                             tabela.cod_parcela )                        \n";
    $stSql .= "                                     )                                                   \n";
    $stSql .= "                                     , 0.00 )                                            \n";
    $stSql .= "             )::numeric(14,2) as juros_calculado                                         \n";
    $stSql .= "             , ( select coalesce (sum(valor), 0.00) as multa                             \n";
    $stSql .= "                 from arrecadacao.pagamento_acrescimo                                    \n";
    $stSql .= "                 where cod_tipo = 3                                                      \n";
    $stSql .= "                 and numeracao = tabela.numeracao                                        \n";
    $stSql .= "                 and ocorrencia_pagamento = tabela.ocorrencia_pagamento                  \n";
    $stSql .= "                 and cod_convenio = tabela.cod_convenio                                  \n";
    $stSql .= "             )::numeric(14,2) as multa                                                   \n";
    $stSql .= "             , ( select coalesce ((aplica_multa (                                        \n";
    $stSql .= "                                     tabela.numeracao                                    \n";
    $stSql .= "                                     , tabela.exercicio                                  \n";
    $stSql .= "                                     , tabela.cod_parcela                                \n";
    $stSql .= "                                     , tabela.data_pagamento )                           \n";
    $stSql .= "                                 * arrecadacao.calculaProporcaoParcela(                  \n";
    $stSql .= "                                                         tabela.cod_parcela )            \n";
    $stSql .= "                                 ), 0.00 )                                               \n";
    $stSql .= "             )::numeric(14,2) as multa_calculada                                         \n";
    $stSql .= "             , arrecadacao.fn_busca_soma_pagamento_calculo(                              \n";
    $stSql .= "                         tabela.numeracao                                                \n";
    $stSql .= "                         , tabela.cod_convenio                                           \n";
    $stSql .= "                         , tabela.ocorrencia_pagamento                                   \n";
    $stSql .= "             )::numeric(14,2) as valor_pago_calculo                                                     \n";
    $stSql .= "             , arrecadacao.buscaContribuinteLancamento( tabela.cod_lancamento            \n";
    $stSql .= "             ) as contribuinte                                                           \n";
    $stSql .= "             , (                                                                         \n";
    $stSql .= "                 SELECT atp2.cod_tipo                                                    \n";
    $stSql .= "                 FROM arrecadacao.tipo_pagamento as atp2                                 \n";
    $stSql .= "                     INNER JOIN arrecadacao.pagamento as apag2                           \n";
    $stSql .= "                     ON apag2.cod_tipo = atp2.cod_tipo                                   \n";
    $stSql .= "                 WHERE                                                                   \n";
    $stSql .= "                     apag2.numeracao = tabela.numeracao                                  \n";
    $stSql .= "                     AND apag2.cod_convenio = tabela.cod_convenio                        \n";
    $stSql .= "                     AND apag2.ocorrencia_pagamento = tabela.ocorrencia_pagamento        \n";
    $stSql .= "             ) as cod_tipo_pagamento                                                     \n";
    $stSql .= "             , (                                                                         \n";
    $stSql .= "                 SELECT atp2.nom_resumido                                                \n";
    $stSql .= "                 FROM arrecadacao.tipo_pagamento as atp2                                 \n";
    $stSql .= "                     INNER JOIN arrecadacao.pagamento as apag2                           \n";
    $stSql .= "                     ON apag2.cod_tipo = atp2.cod_tipo                                   \n";
    $stSql .= "                 WHERE                                                                   \n";
    $stSql .= "                     apag2.numeracao = tabela.numeracao                                  \n";
    $stSql .= "                     AND apag2.cod_convenio = tabela.cod_convenio                        \n";
    $stSql .= "                     AND apag2.ocorrencia_pagamento = tabela.ocorrencia_pagamento        \n";
    $stSql .= "             ) as nom_tipo                                                               \n";
    $stSql .= "         FROM                                                                            \n";
    $stSql .= "             (                                                                           \n";
    $stSql .= "                 SELECT DISTINCT                                                         \n";
    $stSql .= "                     lote.cod_lote                                                       \n";
    $stSql .= "                     , to_char(lote.data_lote, 'dd/mm/YYYY') as data_lote                \n";
    $stSql .= "                     , to_char(pagamento.data_pagamento, 'dd/mm/YYYY'                    \n";
    $stSql .= "                     ) as data_pagamento_br                                              \n";
    $stSql .= "                     , pagamento.data_pagamento                                          \n";
    $stSql .= "                     , atp.cod_tipo                                                      \n";
    $stSql .= "                     , atp.nom_resumido as nom_tipo                                      \n";
    $stSql .= "                     , carne.numeracao                                                   \n";
    $stSql .= "                     , carne.cod_convenio                                                \n";
    $stSql .= "                     , carne.exercicio::int                                              \n";
    $stSql .= "                     , pagamento.ocorrencia_pagamento                                    \n";
    $stSql .= "                     , cgm.numcgm                                                        \n";
    $stSql .= "                     , cgm.nom_cgm                                                       \n";
    $stSql .= "                     , parcela.cod_parcela                                               \n";
    $stSql .= "                     , parcela.cod_lancamento                                            \n";
    $stSql .= "                     , parcela.nr_parcela                                                \n";
    $stSql .= "                     , pagamento.valor as valor_pago_normal                              \n";
    $stSql .= "                     , now()::date as hoje                                               \n";
    $stSql .= "                     , ali.numeracao as inconsistencia_numeracao                         \n";
    $stSql .= "                     , arrecadacao.fn_info_parcela (parcela.cod_parcela) as info_parcela \n";
    $stSql .= "                     , arrecadacao.buscaInscricaoLancamento( parcela.cod_lancamento      \n";
    $stSql .= "                     ) as inscricao                                                      \n";
    $stSql .= "                     , arrecadacao.buscaVinculoLancamento ( parcela.cod_lancamento       \n";
    $stSql .= "                     ) as origem                                                         \n";
    $stSql .= "                 FROM                                                                    \n";
    $stSql .= "                     arrecadacao.lote                                                    \n";

    $stSql .= "                     INNER JOIN arrecadacao.pagamento_lote                               \n";
    $stSql .= "                     ON pagamento_lote.cod_lote = lote.cod_lote                          \n";
    $stSql .= "                     AND pagamento_lote.exercicio = lote.exercicio                       \n";

    $stSql .= "                     INNER JOIN arrecadacao.pagamento                                    \n";
    $stSql .= "                     ON pagamento.numeracao = pagamento_lote.numeracao                   \n";
    $stSql .= "                     AND pagamento.ocorrencia_pagamento                                  \n";
    $stSql .= "                         = pagamento_lote.ocorrencia_pagamento                           \n";
    $stSql .= "                     AND pagamento.cod_convenio = pagamento_lote.cod_convenio            \n";

    $stSql .= "                     INNER JOIN arrecadacao.tipo_pagamento as atp                        \n";
    $stSql .= "                     ON atp.cod_tipo = pagamento.cod_tipo                                \n";

    $stSql .= "                     INNER JOIN arrecadacao.carne                                        \n";
    $stSql .= "                     ON carne.numeracao = pagamento_lote.numeracao                       \n";
    $stSql .= "                     AND carne.cod_convenio = pagamento_lote.cod_convenio                \n";

    $stSql .= "                     INNER JOIN arrecadacao.parcela                                      \n";
    $stSql .= "                     ON  parcela.cod_parcela = carne.cod_parcela                         \n";

    $stSql .= "                     INNER JOIN sw_cgm as cgm                                            \n";
    $stSql .= "                     ON cgm.numcgm = pagamento.numcgm                                    \n";

    $stSql .= "                     LEFT JOIN arrecadacao.lote_inconsistencia as ali                    \n";
    $stSql .= "                     ON ali.numeracao = carne.numeracao                                  \n";
    $stSql .= "                     AND pagamento.ocorrencia_pagamento = ali.ocorrencia                 \n";
    $stSql .= "                     AND ali.exercicio = carne.exercicio                                 \n";
    $stSql .= "                     AND ali.cod_lote = lote.cod_lote                                    \n";

    $stSql .= "                     INNER JOIN arrecadacao.lancamento_calculo as alc                    \n";
    $stSql .= "                     ON alc.cod_lancamento = parcela.cod_lancamento                      \n";

    $stSql .= "                     INNER JOIN arrecadacao.calculo as c                                 \n";
    $stSql .= "                     ON c.cod_calculo = alc.cod_calculo                                  \n";

    $stSql .= "                     INNER JOIN arrecadacao.calculo_cgm as accgm                         \n";
    $stSql .= "                     ON accgm.cod_calculo = c.cod_calculo                                \n";

    $stSql .= "                     INNER JOIN monetario.credito as mc                                  \n";
    $stSql .= "                     ON mc.cod_credito = c.cod_credito                                   \n";
    $stSql .= "                     AND mc.cod_especie = c.cod_especie                                  \n";
    $stSql .= "                     AND mc.cod_genero = c.cod_genero                                    \n";
    $stSql .= "                     AND mc.cod_natureza = c.cod_natureza                                \n";

    $stSql .= "                     LEFT JOIN arrecadacao.calculo_grupo_credito as acgc                 \n";
    $stSql .= "                     ON acgc.cod_calculo = c.cod_calculo                                 \n";

    $stSql .= "                     LEFT JOIN arrecadacao.grupo_credito as agc                          \n";
    $stSql .= "                     ON agc.cod_grupo = acgc.cod_grupo                                   \n";
    $stSql .= "                     AND agc.ano_exercicio = acgc.ano_exercicio                          \n";

    $stSql .= "                 ". $stFiltro ."                                                         \n";

    $stSql .= "             ) as tabela                                                                 \n";

    $stSql .= "         ) as tabela2                                                                    \n";
    $stSql .= "     ORDER BY                                                                            \n";
    $stSql .= "         exercicio, cod_lancamento, nr_parcela                                           \n";

    return $stSql;

}

function recuperaListaPagamentosLoteAnalitico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "" , $inLote)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaPagamentosLoteAnalitico( $inLote, $stFiltro ).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaPagamentosLoteAnalitico($inLote = false, $stFiltro)
{
    $stSql = "
       SELECT
            tabela2.*,
            tabela2.valor_pago_calculo + tabela2.juros + tabela2.multa + tabela2.diferenca + tabela2.correcao as valor_pago_normal

        FROM
            (
                SELECT
                    tabela.*

                    , ( SELECT coalesce (sum(valor), 0.00) as juros
                        FROM
                            arrecadacao.pagamento_acrescimo
                        WHERE
                            cod_tipo = 1
                            and numeracao = tabela.numeracao
                            and ocorrencia_pagamento = tabela.ocorrencia_pagamento
                            and cod_convenio = tabela.cod_convenio
                            and cod_calculo = tabela.cod_calculo
                    )::numeric(14,2) as correcao

                    , ( SELECT coalesce (sum(valor), 0.00) as juros
                        FROM
                            arrecadacao.pagamento_acrescimo
                        WHERE
                            cod_tipo = 2
                            and numeracao = tabela.numeracao
                            and ocorrencia_pagamento = tabela.ocorrencia_pagamento
                            and cod_convenio = tabela.cod_convenio
                            and cod_calculo = tabela.cod_calculo
                    )::numeric(14,2) as juros

                    , ( select coalesce (sum(valor), 0.00) as multa
                        from arrecadacao.pagamento_acrescimo
                        where cod_tipo = 3
                        and numeracao = tabela.numeracao
                        and ocorrencia_pagamento = tabela.ocorrencia_pagamento
                        and cod_convenio = tabela.cod_convenio
                        and cod_calculo = tabela.cod_calculo
                    )::numeric(14,2) as multa
                    , arrecadacao.buscaContribuinteLancamento( tabela.cod_lancamento ) as contribuinte
                    , (
                        SELECT atp2.cod_tipo
                        FROM arrecadacao.tipo_pagamento as atp2
                            INNER JOIN arrecadacao.pagamento as apag2
                            ON apag2.cod_tipo = atp2.cod_tipo
                        WHERE
                            apag2.numeracao = tabela.numeracao
                            AND apag2.cod_convenio = tabela.cod_convenio
                            AND apag2.ocorrencia_pagamento = tabela.ocorrencia_pagamento
                    ) as cod_tipo_pagamento
                    , (
                        SELECT atp2.nom_resumido
                        FROM arrecadacao.tipo_pagamento as atp2
                            INNER JOIN arrecadacao.pagamento as apag2
                            ON apag2.cod_tipo = atp2.cod_tipo
                        WHERE
                            apag2.numeracao = tabela.numeracao
                            AND apag2.cod_convenio = tabela.cod_convenio
                            AND apag2.ocorrencia_pagamento = tabela.ocorrencia_pagamento
                    ) as nom_tipo
                FROM
                    (
                        SELECT DISTINCT
                            lote.cod_lote
                            , to_char(lote.data_lote, 'dd/mm/YYYY') as data_lote
                            , to_char(pagamento.data_pagamento, 'dd/mm/YYYY'
                            ) as data_pagamento_br
                            , pagamento.data_pagamento
                            , atp.cod_tipo
                            , atp.nom_resumido as nom_tipo
                            , carne.numeracao
                            , carne.cod_convenio
                            , carne.exercicio::int
                            , pagamento.ocorrencia_pagamento
                            , cgm.numcgm
                            , cgm.nom_cgm
                            , parcela.cod_parcela
                            , parcela.cod_lancamento
                            , parcela.nr_parcela
                            , pagamento_calculo.valor as valor_pago_calculo


                            , COALESCE(
                                ( SELECT
                                        sum(valor)
                                    FROM
                                        arrecadacao.pagamento_diferenca
                                    WHERE
                                        pagamento_diferenca.numeracao = pagamento.numeracao
                                        AND pagamento_diferenca.cod_convenio = pagamento.cod_convenio
                                        AND pagamento_diferenca.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                                        AND pagamento_diferenca.cod_calculo = pagamento_calculo.cod_calculo
                                ), 0.00
                              )AS diferenca

                            , now()::date as hoje
                            , ali.numeracao as inconsistencia_numeracao
                            , arrecadacao.fn_info_parcela (parcela.cod_parcela) as info_parcela
                            , arrecadacao.buscaInscricaoLancamento( parcela.cod_lancamento
                            ) as inscricao
                            , c.cod_calculo
                            , c.cod_credito ||'.'|| c.cod_especie ||'.'|| c.cod_genero ||'.'|| c.cod_natureza as origem
                            , mc.descricao_credito

                        FROM
                            arrecadacao.lote
                            INNER JOIN arrecadacao.pagamento_lote
                            ON pagamento_lote.cod_lote = lote.cod_lote
                            AND pagamento_lote.exercicio = lote.exercicio
                            INNER JOIN arrecadacao.pagamento
                            ON pagamento.numeracao = pagamento_lote.numeracao
                            AND pagamento.ocorrencia_pagamento
                                = pagamento_lote.ocorrencia_pagamento
                            AND pagamento.cod_convenio = pagamento_lote.cod_convenio


                            INNER JOIN arrecadacao.tipo_pagamento as atp
                            ON atp.cod_tipo = pagamento.cod_tipo
                            INNER JOIN arrecadacao.carne
                            ON carne.numeracao = pagamento_lote.numeracao
                            AND carne.cod_convenio = pagamento_lote.cod_convenio
                            INNER JOIN arrecadacao.parcela
                            ON  parcela.cod_parcela = carne.cod_parcela
                            INNER JOIN sw_cgm as cgm
                            ON cgm.numcgm = pagamento.numcgm
                            LEFT JOIN arrecadacao.lote_inconsistencia as ali
                            ON ali.numeracao = carne.numeracao
                            AND pagamento.ocorrencia_pagamento = ali.ocorrencia
                            AND ali.exercicio = carne.exercicio
                            AND ali.cod_lote = lote.cod_lote
                            INNER JOIN arrecadacao.lancamento_calculo as alc
                            ON alc.cod_lancamento = parcela.cod_lancamento

                            INNER JOIN arrecadacao.calculo as c
                            ON c.cod_calculo = alc.cod_calculo

                            INNER JOIN arrecadacao.pagamento_calculo
                            ON pagamento_calculo.numeracao = pagamento_lote.numeracao
                            AND pagamento_calculo.ocorrencia_pagamento = pagamento_lote.ocorrencia_pagamento
                            AND pagamento_calculo.cod_convenio = pagamento_lote.cod_convenio
                            AND pagamento_calculo.cod_calculo = c.cod_calculo


                            INNER JOIN monetario.credito as mc
                            ON mc.cod_credito = c.cod_credito
                            AND mc.cod_especie = c.cod_especie
                            AND mc.cod_genero = c.cod_genero
                            AND mc.cod_natureza = c.cod_natureza
                            LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
                            ON acgc.cod_calculo = c.cod_calculo
                            LEFT JOIN arrecadacao.grupo_credito as agc
                            ON agc.cod_grupo = acgc.cod_grupo
                            AND agc.ano_exercicio = acgc.ano_exercicio                          \n";

    $stSql .= "                 ". $stFiltro ."                                                         \n";
    $stSql .= "             ) as tabela                                                                 \n";
    $stSql .= "         ) as tabela2                                                                    \n";
    $stSql .= "     ORDER BY                                                                            \n";
    $stSql .= "         exercicio, cod_lancamento, nr_parcela                                           \n";

    return $stSql;

}

function recuperaListaResumoBaixaAutomatica(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "" , $inLote)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaResumoBaixaAutomatica( $inLote, $stFiltro ).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaResumoBaixaAutomatica($inLote = false, $stFiltro)
{
    $stSql = "
            SELECT 
                tabela.*        
                ,valor_pago_calculo + juros + multa + diferenca + correcao as valor_pago_normal
            FROM (
            SELECT distinct

                pagamento.numeracao
                , calculo.cod_credito ||'.'|| calculo.cod_especie ||'.'|| calculo.cod_genero ||'.'|| calculo.cod_natureza as origem
                , mc.descricao_credito
                 ,coalesce (( SELECT SUM(valor) as juros
                        FROM
                            arrecadacao.pagamento_acrescimo
                        WHERE
                            cod_tipo = 1
                            and numeracao = pagamento.numeracao
                            and ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                            and cod_convenio = pagamento.cod_convenio
                        and cod_calculo = pagamento_calculo.cod_calculo
                            
                    ),0.00) as correcao

                    ,coalesce ( ( SELECT SUM(valor) as juros
                        FROM
                            arrecadacao.pagamento_acrescimo
                        WHERE
                            cod_tipo = 2
                            and numeracao = pagamento.numeracao
                            and ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                            and cod_convenio = pagamento.cod_convenio
                        and cod_calculo = pagamento_calculo.cod_calculo
                            
                    ),0.00) as juros

                    ,coalesce ( ( select SUM(valor) as multa
                        from arrecadacao.pagamento_acrescimo
                        where cod_tipo = 3
                        and numeracao = pagamento.numeracao
                        and ocorrencia_pagamento = pagamento.ocorrencia_pagamento
                        and cod_convenio = pagamento.cod_convenio
                        and cod_calculo = pagamento_calculo.cod_calculo
                    ),0.00) as multa

                , COALESCE(
                                ( SELECT
                                        SUM(valor)
                                    FROM
                                        arrecadacao.pagamento_diferenca
                                    WHERE
                                        pagamento_diferenca.numeracao = pagamento.numeracao
                                        AND pagamento_diferenca.cod_convenio = pagamento.cod_convenio
                                        AND pagamento_diferenca.ocorrencia_pagamento = pagamento.ocorrencia_pagamento  
                                        and pagamento_diferenca.cod_calculo = pagamento_calculo.cod_calculo
                                ), 0.00
                )AS diferenca

                ,pagamento_calculo.valor as valor_pago_calculo

                FROM

                            arrecadacao.lote

                            INNER JOIN arrecadacao.pagamento_lote
                            ON pagamento_lote.cod_lote = lote.cod_lote
                            AND pagamento_lote.exercicio = lote.exercicio

                            INNER JOIN arrecadacao.pagamento
                            ON pagamento.numeracao              = pagamento_lote.numeracao
                            AND pagamento.ocorrencia_pagamento  = pagamento_lote.ocorrencia_pagamento
                            AND pagamento.cod_convenio          = pagamento_lote.cod_convenio

                          INNER JOIN arrecadacao.tipo_pagamento as atp
                            ON atp.cod_tipo = pagamento.cod_tipo

                        INNER JOIN sw_cgm as cgm
                            ON cgm.numcgm = pagamento.numcgm
                        
                        inner join arrecadacao.pagamento_calculo
                                ON pagamento_calculo.numeracao                  = pagamento.numeracao
                                AND pagamento_calculo.ocorrencia_pagamento      = pagamento.ocorrencia_pagamento
                                AND pagamento_calculo.cod_convenio              = pagamento.cod_convenio

                        INNER JOIN arrecadacao.calculo
                                on calculo.cod_calculo = pagamento_calculo.cod_calculo

                        INNER JOIN arrecadacao.lancamento_calculo as alc
                                ON alc.cod_calculo = calculo.cod_calculo

                        INNER JOIN arrecadacao.lancamento
                                ON lancamento.cod_lancamento = alc.cod_lancamento

                        INNER JOIN monetario.credito as mc
                            ON mc.cod_credito = calculo.cod_credito
                            AND mc.cod_especie = calculo.cod_especie
                            AND mc.cod_genero = calculo.cod_genero
                            AND mc.cod_natureza = calculo.cod_natureza

                        LEFT JOIN arrecadacao.lote_inconsistencia as ali
                            ON ali.cod_lote = lote.cod_lote
                            AND ali.exercicio = lote.exercicio

                            LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
                                ON acgc.cod_calculo = calculo.cod_calculo

                            LEFT JOIN arrecadacao.grupo_credito as agc
                                ON agc.cod_grupo = acgc.cod_grupo
                                AND agc.ano_exercicio = acgc.ano_exercicio ";

    $stSql .= "         ". $stFiltro ."                                                         
                        ) as tabela                                                                 
                            ORDER BY                                                                            
                            numeracao                            \n";

    return $stSql;

}


function recuperaListaPagamentosLoteDA(&$rsRecordSet, $stCodLote = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaPagamentosLoteDA( $stCodLote );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaListaPagamentosLoteDA($stCodLote)
{
    $stSql = " SELECT DISTINCT
                          APC.cod_calculo
                        , APC.numeracao
                        , acrn.cod_convenio
                        , atp.nom_tipo
                        , (
                                SELECT
                                    ap.nr_parcela||'/'||count(parcela.cod_parcela)
                                FROM
                                    arrecadacao.parcela
                                WHERE
                                    parcela.cod_lancamento in (
                                        SELECT
                                            cod_lancamento
                                        FROM
                                            arrecadacao.parcela
                                        WHERE
                                            parcela.cod_parcela = ap.cod_parcela
                                )AND parcela.nr_parcela != 0
                        ) AS info_parcela
                        , AC.cod_credito
                        , AC.cod_especie
                        , AC.cod_genero
                        , AC.cod_natureza
                        , APC.valor AS valor_pago_calculo
                        , COALESCE((
                                SELECT
                                    SUM(pagamento_acrescimo.valor)
                                FROM
                                    arrecadacao.pagamento_acrescimo
                                WHERE
                                    pagamento_acrescimo.cod_calculo = AC.cod_calculo
                                    AND pagamento_acrescimo.numeracao = APC.numeracao
                                    AND pagamento_acrescimo.cod_tipo = 2
                                    AND pagamento_acrescimo.ocorrencia_pagamento = APC.ocorrencia_pagamento
                        ), 0.00 ) AS juros
                        , COALESCE((
                                SELECT
                                    SUM(pagamento_acrescimo.valor)
                                FROM
                                    arrecadacao.pagamento_acrescimo
                                WHERE
                                    pagamento_acrescimo.cod_calculo = AC.cod_calculo
                                    AND pagamento_acrescimo.numeracao = APC.numeracao
                                    AND pagamento_acrescimo.cod_tipo = 3
                                    AND pagamento_acrescimo.ocorrencia_pagamento = APC.ocorrencia_pagamento
                        ), 0.00 ) AS multa
                        , APAG.valor AS valor_pago_normal
                        , to_char(APAG.data_pagamento, 'dd/mm/YYYY') AS data_pagamento_br
                        , CASE WHEN pagamento_diferenca.valor IS NOT NULL THEN
                            pagamento_diferenca.valor
                          WHEN APL.cod_convenio = -1 THEN 
                          (
                          CASE WHEN parcelamento.judicial = TRUE THEN
                            aplica_acrescimo_modalidade_carne( acrn.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, dp.dt_vencimento_parcela, APAG.data_pagamento, 'true' )::numeric
                          ELSE
                            aplica_acrescimo_modalidade_carne( acrn.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, dp.dt_vencimento_parcela, APAG.data_pagamento, 'true' )::numeric
                          END)
                          ELSE
                            (
                                APAG.valor - (
                                    ap.valor
                                    + (
                                        SELECT
                                            aplica_multa( acrn.numeracao, acrn.exercicio::integer, acrn.cod_parcela, APAG.data_pagamento )
                                                +
                                            aplica_juro( acrn.numeracao, acrn.exercicio::integer, acrn.cod_parcela, APAG.data_pagamento )
                                                +
                                            aplica_correcao( acrn.numeracao, acrn.exercicio::integer, acrn.cod_parcela, APAG.data_pagamento )
                                    )
                                )
                            ) END  diferenca
                        , mc.descricao_credito
                        , COALESCE( ic.inscricao_municipal, cec.inscricao_economica, acc.numcgm ) AS inscricao
                        , arrecadacao.buscaContribuinteLancamento (lc.cod_lancamento) AS contribuinte

                FROM arrecadacao.calculo                          AS AC

               INNER JOIN arrecadacao.lancamento_calculo          AS LC
                       ON ac.cod_calculo = lc.cod_calculo

                LEFT JOIN arrecadacao.calculo_cgm AS acc
                        ON acc.cod_calculo = ac.cod_calculo

                LEFT JOIN arrecadacao.imovel_calculo AS ic
                        ON ic.cod_calculo = ac.cod_calculo

                LEFT JOIN arrecadacao.cadastro_economico_calculo AS cec
                        ON cec.cod_calculo = ac.cod_calculo

                INNER JOIN monetario.credito AS mc
                        ON mc.cod_credito = ac.cod_credito
                    AND mc.cod_natureza = ac.cod_natureza
                    AND mc.cod_especie = ac.cod_especie
                    AND mc.cod_genero = ac.cod_genero

                INNER JOIN arrecadacao.pagamento_calculo                AS APC
                        ON APC.cod_calculo          = AC.cod_calculo

                LEFT JOIN
                    (
                        SELECT
                            sum(valor) as valor,
                            numeracao,
                            ocorrencia_pagamento
                        FROM
                            arrecadacao.pagamento_diferenca
                        GROUP BY
                            numeracao,
                            ocorrencia_pagamento
                    )AS pagamento_diferenca
                ON
                    pagamento_diferenca.numeracao = apc.numeracao
                    and pagamento_diferenca.ocorrencia_pagamento = apc.ocorrencia_pagamento

                INNER JOIN arrecadacao.pagamento                        AS APAG
                        ON APAG.numeracao            = APC.numeracao
                    AND APAG.cod_convenio         = APC.cod_convenio
                    AND APAG.ocorrencia_pagamento = APC.ocorrencia_pagamento

                INNER JOIN arrecadacao.tipo_pagamento AS atp
                        ON atp.cod_tipo            = APAG.cod_tipo

                INNER JOIN arrecadacao.pagamento_lote                   AS APL
                        ON APL.numeracao            = APC.numeracao
                    AND APL.cod_convenio         = APC.cod_convenio
                    AND APL.ocorrencia_pagamento = APC.ocorrencia_pagamento
                    AND APL.cod_lote             IN ( ".$stCodLote." )
                    AND APL.cod_convenio = -1

                INNER JOIN arrecadacao.carne AS acrn
                        ON acrn.numeracao = APL.numeracao

                INNER JOIN arrecadacao.parcela AS ap
                        ON ap.cod_parcela = acrn.cod_parcela

                INNER JOIN divida.parcela_calculo AS dpc
                        ON dpc.cod_calculo = AC.cod_calculo

                INNER JOIN divida.parcela AS dp
                        ON dp.num_parcelamento = dpc.num_parcelamento
                        AND dp.num_parcela = dpc.num_parcela
                        AND ap.nr_parcela = dp.num_parcela
                
                INNER JOIN divida.parcelamento
                        ON parcelamento.num_parcelamento = dp.num_parcelamento
                        
                ORDER BY APC.numeracao
                        , APC.cod_calculo \n";

    return $stSql;
}

//3
function recuperaListaPagamentosLoteDARelatorio(&$rsRecordSet, $stCodLote = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaPagamentosLoteDARelatorio( $stCodLote );
    $this->stDebug = $stSql;
    
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaListaPagamentosLoteDARelatorio($stCodLote)
{
    $stSql = " SELECT   DISTINCT
                          APC.cod_calculo
                        , APC.numeracao
                        , atp.nom_tipo
                        , (
                                SELECT
                                    ap.nr_parcela||'/'||count(parcela.cod_parcela)
                                FROM
                                    arrecadacao.parcela
                                WHERE
                                    parcela.cod_lancamento in (
                                        SELECT
                                            cod_lancamento
                                        FROM
                                            arrecadacao.parcela
                                        WHERE
                                            parcela.cod_parcela = ap.cod_parcela
                                )AND parcela.nr_parcela != 0
                        ) AS info_parcela
                        , AC.cod_credito
                        , AC.cod_especie
                        , AC.cod_genero
                        , AC.cod_natureza
                        , APC.valor + COALESCE( APDIF.valor, 0.00) + COALESCE((
                            SELECT
                                SUM(pagamento_acrescimo.valor)
                            FROM
                                arrecadacao.pagamento_acrescimo
                            WHERE
                                pagamento_acrescimo.cod_calculo = AC.cod_calculo
                                AND pagamento_acrescimo.numeracao = APC.numeracao
                                AND pagamento_acrescimo.ocorrencia_pagamento = APC.ocorrencia_pagamento
                        ), 0.00 )::numeric(14,2) AS valor_pago_normal
                        , COALESCE((
                                SELECT
                                    SUM(pagamento_acrescimo.valor)
                                FROM
                                    arrecadacao.pagamento_acrescimo
                                WHERE
                                    pagamento_acrescimo.cod_calculo = AC.cod_calculo
                                    AND pagamento_acrescimo.numeracao = APC.numeracao
                                    AND pagamento_acrescimo.cod_tipo = 2
                                    AND pagamento_acrescimo.ocorrencia_pagamento = APC.ocorrencia_pagamento
                        ), 0.00 )::numeric(14,2) AS juros
                        , COALESCE((
                                SELECT
                                    SUM(pagamento_acrescimo.valor)
                                FROM
                                    arrecadacao.pagamento_acrescimo
                                WHERE
                                    pagamento_acrescimo.cod_calculo = AC.cod_calculo
                                    AND pagamento_acrescimo.numeracao = APC.numeracao
                                    AND pagamento_acrescimo.cod_tipo = 3
                                    AND pagamento_acrescimo.ocorrencia_pagamento = APC.ocorrencia_pagamento
                        ), 0.00 )::numeric(14,2) AS multa
                        , COALESCE((
                                SELECT
                                    SUM(pagamento_acrescimo.valor)
                                FROM
                                    arrecadacao.pagamento_acrescimo
                                WHERE
                                    pagamento_acrescimo.cod_calculo = AC.cod_calculo
                                    AND pagamento_acrescimo.numeracao = APC.numeracao
                                    AND pagamento_acrescimo.cod_tipo = 1
                                    AND pagamento_acrescimo.ocorrencia_pagamento = APC.ocorrencia_pagamento
                        ), 0.00 )::numeric(14,2) AS correcao
                        , APC.valor::numeric(14,2) AS valor_pago_calculo
                        , to_char(APAG.data_pagamento, 'dd/mm/YYYY') AS data_pagamento_br
                         ,CASE WHEN APDIF.valor IS NOT NULL THEN
                            COALESCE(APDIF.valor,0.00)::numeric(14,2)
                          WHEN APL.cod_convenio = -1 THEN 
                          (
                          CASE WHEN parcelamento.judicial = TRUE THEN
                            COALESCE(aplica_acrescimo_modalidade_carne( acrn.numeracao,  1, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, dp.dt_vencimento_parcela, APAG.data_pagamento, 'true' )::numeric,0.00)::numeric(14,2)
                          ELSE
                            COALESCE(aplica_acrescimo_modalidade_carne( acrn.numeracao,  0, NULL, NULL, parcelamento.cod_modalidade, 1, parcelamento.num_parcelamento ,dp.vlr_parcela, dp.dt_vencimento_parcela, APAG.data_pagamento, 'true' )::numeric,0.00)::numeric(14,2)
                          END)
                          ELSE
                            COALESCE(
                              ( APAG.valor - (
                                    ap.valor
                                    + (
                                        SELECT
                                            aplica_multa( acrn.numeracao, acrn.exercicio::integer, acrn.cod_parcela, APAG.data_pagamento )
                                                +
                                            aplica_juro( acrn.numeracao, acrn.exercicio::integer, acrn.cod_parcela, APAG.data_pagamento )
                                                +
                                            aplica_correcao( acrn.numeracao, acrn.exercicio::integer, acrn.cod_parcela, APAG.data_pagamento )
                                    )
                                )
                            ),0.00)::numeric(14,2) END AS  diferenca
                        , mc.descricao_credito
                        , COALESCE( ic.inscricao_municipal, cec.inscricao_economica, acc.numcgm ) AS inscricao
                        , arrecadacao.buscaContribuinteLancamento( lc.cod_lancamento ) AS contribuinte

                FROM arrecadacao.calculo                          AS AC

                INNER JOIN arrecadacao.lancamento_calculo         AS LC
                        ON AC.cod_calculo = LC.cod_calculo
                LEFT JOIN arrecadacao.calculo_cgm AS acc
                        ON acc.cod_calculo = ac.cod_calculo

                LEFT JOIN arrecadacao.imovel_calculo AS ic
                        ON ic.cod_calculo = ac.cod_calculo

                LEFT JOIN arrecadacao.cadastro_economico_calculo AS cec
                        ON cec.cod_calculo = ac.cod_calculo

                INNER JOIN monetario.credito AS mc
                        ON mc.cod_credito = ac.cod_credito
                    AND mc.cod_natureza = ac.cod_natureza
                    AND mc.cod_especie = ac.cod_especie
                    AND mc.cod_genero = ac.cod_genero

                INNER JOIN arrecadacao.pagamento_calculo                AS APC
                        ON APC.cod_calculo          = AC.cod_calculo

                INNER JOIN arrecadacao.pagamento                        AS APAG
                        ON APAG.numeracao            = APC.numeracao
                    AND APAG.cod_convenio         = APC.cod_convenio
                    AND APAG.ocorrencia_pagamento = APC.ocorrencia_pagamento

                LEFT JOIN arrecadacao.pagamento_diferenca                        AS APDIF
                        ON APDIF.numeracao            = APC.numeracao
                    AND APDIF.cod_convenio         = APC.cod_convenio
                    AND APDIF.ocorrencia_pagamento = APC.ocorrencia_pagamento
                    AND APDIF.cod_calculo = AC.cod_calculo

                INNER JOIN arrecadacao.tipo_pagamento AS atp
                        ON atp.cod_tipo            = APAG.cod_tipo

                INNER JOIN arrecadacao.pagamento_lote                   AS APL
                        ON APL.numeracao            = APC.numeracao
                    AND APL.cod_convenio         = APC.cod_convenio
                    AND APL.ocorrencia_pagamento = APC.ocorrencia_pagamento
                    AND APL.cod_lote             IN ( ".$stCodLote." )
                    AND APL.cod_convenio = -1

                INNER JOIN arrecadacao.carne AS acrn
                        ON acrn.numeracao = APL.numeracao

                INNER JOIN arrecadacao.parcela AS ap
                        ON ap.cod_parcela = acrn.cod_parcela

                INNER JOIN divida.parcela_calculo AS dpc
                        ON dpc.cod_calculo = AC.cod_calculo

                INNER JOIN divida.parcela AS dp
                        ON dp.num_parcelamento = dpc.num_parcelamento
                        AND dp.num_parcela = dpc.num_parcela
                        AND ap.nr_parcela = dp.num_parcela
                
                  INNER JOIN divida.parcelamento
                        ON parcelamento.num_parcelamento = dp.num_parcelamento

                ORDER BY APC.numeracao
                        , APC.cod_calculo \n";

    return $stSql;

}

function recuperaListaPagamentosBaixaCanceladas(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "",$dtDataBase)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_parcela ";
    $stSql  = $this->montaRecuperaListaPagamentosBaixaCanceladas($dtDataBase).$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaPagamentosBaixaCanceladas($dtDataBase)
{
    $stSql  = " SELECT tbl.*,                                                                       \n";
    $stSql .= "     tbl.valor_credito_jurosp::numeric(14,2) as juros,                               \n";
    $stSql .= "     tbl.valor_credito_multap::numeric(14,2) as multa,                               \n";
    $stSql .= "     descontop::numeric(14,2) as desconto                                            \n";
    $stSql .= " FROM    (                                                                           \n";
    $stSql .= "     SELECT                                                                          \n";
    $stSql .= "         calc.cod_credito ||'.'|| calc.cod_especie ||'.'|| calc.cod_genero           \n";
    $stSql .= "         ||'.'||calc.cod_natureza as cod                                             \n";
    $stSql .= "         , calc.cod_credito                                                          \n";
    $stSql .= "         , calc.cod_especie                                                          \n";
    $stSql .= "         , calc.cod_genero                                                           \n";
    $stSql .= "         , calc.cod_natureza                                                         \n";
    $stSql .= "         , mc.descricao_credito                                                      \n";
    $stSql .= "         , alc.cod_lancamento                                                        \n";
    $stSql .= "         , calc.cod_calculo                                                          \n";
    $stSql .= "         , calc.valor                                                                \n";

    $stSql .= "         , ( CASE WHEN ap.nr_parcela = 0 THEN                                        \n";
    $stSql .= "                 alc.valor::numeric(14,2)                                            \n";
    $stSql .= "             ELSE                                                                    \n";
    $stSql .= "                 (alc.valor/ arrecadacao.fn_total_parcelas(alc.cod_lancamento)       \n";
    $stSql .= "                 )::numeric(14,2)                                                    \n";
    $stSql .= "             END                                                                     \n";
    $stSql .= "         ) as somatorio                                                              \n";

    $stSql .= "         , ( CASE WHEN ( apag.data_pagamento is not null ) THEN                      \n";
    $stSql .= "                CASE WHEN ( ap.valor > 0 ) THEN                                      \n";
    $stSql .= "                     CASE WHEN ( ap.vencimento >  '".$dtDataBase."'                  \n";
    $stSql .= "                                 OR ap.nr_parcela = 0 ) THEN                         \n";
    $stSql .= "                         (0.00)                                                      \n";
    $stSql .= "                     ELSE                                                            \n";
    $stSql .= "                         CASE WHEN ap.nr_parcela = 0 THEN                            \n";
    $stSql .= "                         ( ( (                                                       \n";
    $stSql .= "                             calc.valor                                              \n";
    $stSql .= "                             )                                                       \n";
    $stSql .= "                             * ( 100 / ap.valor )                                    \n";
    $stSql .= "                           )                                                         \n";
    $stSql .= "                         * ( aplica_juro                                             \n";
    $stSql .= "                    (ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."')\n";
    $stSql .= "                           )  / 100 )                                                \n";
    $stSql .= "                         ELSE                                                        \n";
    $stSql .= "                         ( ( (                                                       \n";
    $stSql .= "                             calc.valor                                              \n";
    $stSql .= "                             * arrecadacao.calculaProporcaoParcela(ap.cod_parcela)   \n";
    $stSql .= "                             )                                                       \n";
    $stSql .= "                           * ( 100 / ap.valor )                                      \n";
    $stSql .= "                           )                                                         \n";
    $stSql .= "                           *                                                         \n";
    $stSql .= "                           ( aplica_juro                                             \n";
    $stSql .= "                   (ac.numeracao,ac.exercicio::int,ac.cod_parcela, '".$dtDataBase."')\n";
    $stSql .= "                         * arrecadacao.calculaProporcaoParcela(ap.cod_parcela)       \n";
    $stSql .= "                           )                                                         \n";
    $stSql .= "                         / 100                                                       \n";
    $stSql .= "                         )::numeric(14,6)                                            \n";
    $stSql .= "                         END                                                         \n";
    $stSql .= "                     END                                                             \n";
    $stSql .= "                 ELSE                                                                \n";
    $stSql .= "                     0.00                                                            \n";
    $stSql .= "                 END                                                                 \n";
    $stSql .= "             END                                                                     \n";
    $stSql .= "         ) as valor_credito_jurosp                                                   \n";
    $stSql .= "         , ( CASE WHEN ( apag.data_pagamento is not null ) THEN                      \n";
    $stSql .= "                 CASE WHEN ( ap.valor > 0 ) THEN                                     \n";
    $stSql .= "                     CASE WHEN ( ap.vencimento >  '".$dtDataBase."'                  \n";
    $stSql .= "                                 OR ap.nr_parcela = 0 ) THEN                         \n";
    $stSql .= "                         (0.00)                                                      \n";
    $stSql .= "                     ELSE                                                            \n";
    $stSql .= "                         CASE WHEN ap.nr_parcela = 0 THEN                            \n";
    $stSql .= "                             ( ( (                                                   \n";
    $stSql .= "                                 calc.valor  )                                       \n";
    $stSql .= "                                 * ( 100 / ap.valor )                                \n";
    $stSql .= "                               )                                                     \n";
    $stSql .= "                               * ( aplica_multa                                      \n";
    $stSql .= "                   (ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."') \n";
    $stSql .= "                                 )                                                   \n";
    $stSql .= "                                 / 100 )                                             \n";
    $stSql .= "                         ELSE                                                        \n";
    $stSql .= "                             ( ( (                                                   \n";
    $stSql .= "                                 calc.valor                                          \n";
    $stSql .= "                             * arrecadacao.calculaProporcaoParcela(ap.cod_parcela)   \n";
    $stSql .= "                                 )                                                   \n";
    $stSql .= "                             * ( 100                                                 \n";
    $stSql .= "                             / ap.valor )                                            \n";
    $stSql .= "                               )                                                     \n";
    $stSql .= "                             *                                                       \n";
    $stSql .= "                             ( aplica_multa                                          \n";
    $stSql .= "                   (ac.numeracao,ac.exercicio::int,ac.cod_parcela,'".$dtDataBase."') \n";
    $stSql .= "                             * arrecadacao.calculaProporcaoParcela(ap.cod_parcela)   \n";
    $stSql .= "                             )                                                       \n";
    $stSql .= "                             / 100                                                   \n";
    $stSql .= "                             )::numeric(14,6)                                        \n";
    $stSql .= "                         END                                                         \n";
    $stSql .= "                     END                                                             \n";
    $stSql .= "                 ELSE                                                                \n";
    $stSql .= "                     0.00                                                            \n";
    $stSql .= "                 END                                                                 \n";
    $stSql .= "             END                                                                     \n";
    $stSql .= "         ) as valor_credito_multap                                                   \n";
    $stSql .= "         , ( CASE WHEN (apd.valor is not null)                                       \n";
    $stSql .= "                 AND (( apag.data_pagamento <= ap.vencimento )                       \n";
    $stSql .= "                      OR (apd.vencimento >= '".$dtDataBase."')) THEN                 \n";
    $stSql .= "                 arrecadacao.fn_desconto_credito_lancamento( alc.cod_lancamento,     \n";
    $stSql .= "                         ap.cod_parcela,calc.cod_calculo,calc.cod_credito,           \n";
    $stSql .= "                         calc.cod_especie,calc.cod_genero,                           \n";
    $stSql .= "                         calc.cod_natureza,'".$dtDataBase."', calc.valor,            \n";
    $stSql .= "                         (calc.exercicio)::int )::numeric(14,6)                      \n";
    $stSql .= "             ELSE                                                                    \n";
    $stSql .= "                 0.00                                                                \n";
    $stSql .= "             END                                                                     \n";
    $stSql .= "         ) as descontop                                                              \n";
    $stSql .= "         , ac.numeracao                                                              \n";
    $stSql .= "         , ap.cod_parcela                                                            \n";
    $stSql .= "         , ap.nr_parcela                                                             \n";
    $stSql .= "     FROM                                                                            \n";
    $stSql .= "         arrecadacao.lancamento_calculo alc                                          \n";
    $stSql .= "         , arrecadacao.calculo calc                                                  \n";

    $stSql .= "         LEFT JOIN   (                                                               \n";
    $stSql .= "             SELECT                                                                  \n";
    $stSql .= "                 gc.cod_grupo, gc.descricao, gc.ano_exercicio,                       \n";
    $stSql .= "                 cgc.cod_calculo, m.cod_modulo, acg.cod_credito, acg.desconto        \n";
    $stSql .= "             FROM                                                                    \n";
    $stSql .= "                 arrecadacao.calculo_grupo_credito cgc                               \n";

    $stSql .= "                 INNER JOIN arrecadacao.grupo_credito gc                             \n";
    $stSql .= "                 ON gc.cod_grupo     = cgc.cod_grupo                                 \n";
    $stSql .= "                 AND gc.ano_exercicio = cgc.ano_exercicio                            \n";

    $stSql .= "                 INNER JOIN administracao.modulo m                                   \n";
    $stSql .= "                 ON m.cod_modulo     = gc.cod_modulo                                 \n";

    $stSql .= "                 INNER JOIN arrecadacao.credito_grupo acg                            \n";
    $stSql .= "                 ON gc.cod_grupo = acg.cod_grupo                                     \n";
    $stSql .= "         ) as grupo                                                                  \n";
    $stSql .= "         ON grupo.cod_calculo = calc.cod_calculo                                     \n";
    $stSql .= "         AND grupo.cod_credito = calc.cod_credito                                    \n";
    $stSql .= "         AND grupo.ano_exercicio = calc.exercicio                                    \n";

    $stSql .= "         , monetario.credito mc                                                      \n";
    $stSql .= "         , arrecadacao.parcela ap                                                    \n";

    $stSql .= "         LEFT JOIN arrecadacao.parcela_desconto apd                                  \n";
    $stSql .= "         ON apd.cod_parcela = ap.cod_parcela                                         \n";
    $stSql .= "         , arrecadacao.carne ac                                                      \n";

    $stSql .= "         LEFT JOIN arrecadacao.pagamento apag                                        \n";
    $stSql .= "         ON apag.numeracao = ac.numeracao                                            \n";
    $stSql .= "         AND apag.cod_convenio = ac.cod_convenio                                     \n";
    $stSql .= "         AND apag.ocorrencia_pagamento = 1                                           \n";

    $stSql .= "     WHERE                                                                           \n";
    $stSql .= "         mc.cod_credito = calc.cod_credito                                           \n";
    $stSql .= "         and mc.cod_especie = calc.cod_especie                                       \n";
    $stSql .= "         and mc.cod_genero = calc.cod_genero                                         \n";
    $stSql .= "         and mc.cod_natureza = calc.cod_natureza                                     \n";
    $stSql .= "         and calc.cod_calculo = alc.cod_calculo                                      \n";
    $stSql .= "         AND ac.cod_parcela = ap.cod_parcela                                         \n";
    $stSql .= "         AND alc.cod_lancamento = ap.cod_lancamento                                  \n";
    $stSql .= "         AND calc.cod_calculo = alc.cod_calculo                                      \n";

    $stSql .= "     ) as tbl                                                                        \n";

    return $stSql;

}

/**
    *  lista de itbi no arquivo de baixa.
    *
*/
function recuperaListaITBIArquivo(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaListaITBIArquivo().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaITBIArquivo()
{
    $stSql =  " select  carne.numeracao                                                                 \n";
    $stSql .= "     , carne.impresso                                                                    \n";
    $stSql .= "     , carne.exercicio::integer                                                          \n";
    $stSql .= "     , calculo_cgm.numcgm                                                                \n";
    $stSql .= "     , imovel_calculo.inscricao_municipal                                                \n";
    $stSql .= "     , ( select nom_cgm from sw_cgm where numcgm = calculo_cgm.numcgm )                  \n";
    $stSql .= "     , transferencia_imovel.cod_transferencia                                            \n";
    $stSql .= "     , transferencia_imovel.cod_natureza                                                 \n";
    $stSql .= "     , matricula_imovel_transferencia.mat_registro_imovel                                \n";
    $stSql .= " from                                                                                    \n";

    $stSql .= "         arrecadacao.calculo_grupo_credito                                               \n";

    $stSql .= "         INNER JOIN arrecadacao.calculo                                                  \n";
    $stSql .= "         ON calculo.cod_calculo = calculo_grupo_credito.cod_calculo                      \n";

    $stSql .= "         INNER JOIN arrecadacao.imovel_calculo                                           \n";
    $stSql .= "         ON imovel_calculo.cod_calculo = calculo.cod_calculo                             \n";

    $stSql .= "         INNER JOIN imobiliario.transferencia_imovel                                     \n";
    $stSql .= "         ON transferencia_imovel.inscricao_municipal = imovel_calculo.inscricao_municipal\n";

    $stSql .= "         LEFT JOIN imobiliario.matricula_imovel_transferencia                            \n";
    $stSql .= "         ON transferencia_imovel.cod_transferencia =                                     \n";
    $stSql .= "         matricula_imovel_transferencia.cod_transferencia                                \n";

    $stSql .= "         INNER JOIN arrecadacao.calculo_cgm                                              \n";
    $stSql .= "         ON calculo_cgm.cod_calculo = calculo.cod_calculo                                \n";

    $stSql .= "         INNER JOIN arrecadacao.lancamento_calculo                                       \n";
    $stSql .= "         ON lancamento_calculo.cod_calculo = calculo.cod_calculo                         \n";

    $stSql .= "         INNER JOIN arrecadacao.lancamento                                               \n";
    $stSql .= "         ON lancamento.cod_lancamento = lancamento_calculo.cod_lancamento                \n";

    $stSql .= "         INNER JOIN arrecadacao.parcela                                                  \n";
    $stSql .= "         ON parcela.cod_lancamento = lancamento.cod_lancamento                           \n";

    $stSql .= "         INNER JOIN arrecadacao.carne                                                    \n";
    $stSql .= "         ON carne.cod_parcela = parcela.cod_parcela                                      \n";

    $stSql .= "     WHERE                                                                               \n";
    $stSql .= "         calculo.cod_calculo = ( select alc.cod_calculo                                  \n";
    $stSql .= "                                 from arrecadacao.lancamento_calculo alc                 \n";
    $stSql .= "                                 where alc.cod_lancamento = lancamento.cod_lancamento    \n";
    $stSql .= "                                 order by cod_calculo desc limit 1                       \n";
    $stSql .= "                                 )                                                       \n";
    $stSql .= "         and  carne.numeracao = ( select ultima_numeracao.numeracao                      \n";
    $stSql .= "                                  from arrecadacao.carne as ultima_numeracao             \n";
    $stSql .= "                                  where                                                  \n";
    $stSql .= "                                     ultima_numeracao.cod_parcela = parcela.cod_parcela  \n";
    $stSql .= "                                  order by timestamp desc limit 1 )                      \n";

    return $stSql;

}

/**
    * recuperar pagamentos efetuados de itbi no arquivo de baixa
    *
*/
function recuperaPagosITBI(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : "";
    $stSql  = $this->montaRecuperaPagosITBI().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPagosITBI()
{
    $stSql .= "       select pagamento.numeracao                                  \n";
    $stSql .= "            , pagamento.cod_convenio                               \n";
    $stSql .= "            , tipo_pagamento.cod_tipo                              \n";
    $stSql .= "            , tipo_pagamento.nom_tipo                              \n";
    $stSql .= "            , tipo_pagamento.pagamento                             \n";
    $stSql .= "         from arrecadacao.pagamento                                \n";
    $stSql .= "   INNER JOIN arrecadacao.tipo_pagamento                           \n";
    $stSql .= "           on tipo_pagamento.cod_tipo = pagamento.cod_tipo         \n";
    $stSql .= "        where pagamento.inconsistente = false                      \n";

    return $stSql;

}

function recuperaResumoLoteListaBaixaAutomatica(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGrupo     = "\n group by plote.cod_lote, plote.exercicio, c.cod_credito,c.cod_especie,c.cod_genero,c.cod_natureza,mc.descricao_credito ";
    $stSql = $this->montaRecuperaResumoLoteListaBaixaAutomatica().$stFiltro.$stGrupo.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaResumoLoteListaBaixaAutomatica()
{
    $stSql  = " select                                                                              \n";
    $stSql .= "     c.cod_credito||'.'||c.cod_especie||'.'||c.cod_genero||'.'||c.cod_natureza as cod\n";
    $stSql .= "     , c.cod_credito                                                                 \n";
    $stSql .= "     , c.cod_especie                                                                 \n";
    $stSql .= "     , c.cod_genero                                                                  \n";
    $stSql .= "     , c.cod_natureza                                                                \n";
    $stSql .= "     , mc.descricao_credito                                                          \n";
    $stSql .= "     , sum(pagc.valor) as somatorio                                                  \n";
    $stSql .= "     , arrecadacao.somaPagAcrescimosLoteJuros( plote.cod_lote, plote.exercicio::int, \n";
    $stSql .= "             c.cod_credito, c.cod_especie, c.cod_genero, c.cod_natureza, 'todos'     \n";
    $stSql .= "     ) as juros                                                                      \n";
    $stSql .= "     , arrecadacao.somaPagAcrescimosLoteMulta(plote.cod_lote, plote.exercicio::int,  \n";
    $stSql .= "             c.cod_credito, c.cod_especie, c.cod_genero, c.cod_natureza, 'todos'     \n";
    $stSql .= "     ) as multa                                                                      \n";
    $stSql .= "     , arrecadacao.somaPagDifCredito(plote.cod_lote, plote.exercicio::int,           \n";
    $stSql .= "             c.cod_credito, c.cod_especie, c.cod_genero, c.cod_natureza, 'todos'     \n";
    $stSql .= "     ) as diferenca                                                                  \n";
    $stSql .= " from                                                                                \n";
    $stSql .= "     arrecadacao.calculo c                                                           \n";
    $stSql .= "     , arrecadacao.pagamento_calculo pagc                                            \n";
    $stSql .= "     , arrecadacao.pagamento_lote plote                                              \n";
    $stSql .= "     , monetario.credito mc                                                          \n";
    $stSql .= " where                                                                               \n";
    $stSql .= "     mc.cod_credito = c.cod_credito                                                  \n";
    $stSql .= "     and mc.cod_especie = c.cod_especie                                              \n";
    $stSql .= "     and mc.cod_genero = c.cod_genero                                                \n";
    $stSql .= "     and mc.cod_natureza = c.cod_natureza                                            \n";
    $stSql .= "     and pagc.cod_calculo = c.cod_calculo                                            \n";
    $stSql .= "     and plote.numeracao = pagc.numeracao                                            \n";
    $stSql .= "     and plote.ocorrencia_pagamento = pagc.ocorrencia_pagamento                      \n";
    $stSql .= "     and plote.cod_convenio = pagc.cod_convenio                                      \n";

    return $stSql;

}

function recuperaListaLotes(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaListaLotes().$stFiltro;
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaListaLotes()
{
    $stSql  = " SELECT DISTINCT                                                                     \n";
    $stSql .= "     lote.cod_lote                                                                   \n";
    $stSql .= "     , to_char(lote.data_lote, 'dd/mm/YYYY') as data_lote                            \n";
    $stSql .= "     , to_char(pagamento.data_pagamento, 'dd/mm/YYYY') as data_pagamento             \n";
    $stSql .= "     , to_char(pagamento.data_baixa, 'dd/mm/YYYY') as data_baixa                     \n";
    $stSql .= "     , lote.exercicio                                                                \n";

    $stSql .= " FROM                                                                                \n";

    $stSql .= "     arrecadacao.lote                                                                \n";

    $stSql .= "     LEFT JOIN  monetario.agencia                                                   \n";
    $stSql .= "     ON lote.cod_agencia = agencia.cod_agencia                                       \n";
    $stSql .= "     AND lote.cod_banco = agencia.cod_banco                                          \n";

    $stSql .= "     LEFT JOIN monetario.banco                                                      \n";
    $stSql .= "     ON lote.cod_banco = banco.cod_banco                                             \n";

    $stSql .= "     LEFT JOIN arrecadacao.pagamento_lote                                           \n";
    $stSql .= "     ON pagamento_lote.cod_lote = lote.cod_lote                                      \n";
    $stSql .= "     AND pagamento_lote.exercicio = lote.exercicio                                   \n";

    $stSql .= "     LEFT JOIN arrecadacao.pagamento                                                \n";
    $stSql .= "     ON pagamento.numeracao = pagamento_lote.numeracao                               \n";
    $stSql .= "     AND pagamento.ocorrencia_pagamento = pagamento_lote.ocorrencia_pagamento        \n";
    $stSql .= "     AND pagamento.cod_convenio = pagamento_lote.cod_convenio                        \n";

    $stSql .= "     LEFT JOIN arrecadacao.tipo_pagamento                                           \n";
    $stSql .= "     ON tipo_pagamento.cod_tipo = pagamento.cod_tipo                                 \n";

    $stSql .= "     LEFT JOIN arrecadacao.lote_inconsistencia as ali                                \n";
    $stSql .= "     ON  ali.cod_lote = lote.cod_lote                                                \n";
    $stSql .= "     AND ali.exercicio = lote.exercicio                                              \n";

    return $stSql;

}

}// end of class
?>
