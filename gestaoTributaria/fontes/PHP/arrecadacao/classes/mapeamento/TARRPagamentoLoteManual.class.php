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
    * Classe de mapeamento da tabela ARRECADACAO.PAGAMENTO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRPagamentoLoteManual.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.13  2007/06/26 21:30:33  cercato
Bug #9491#

Revision 1.12  2007/06/13 13:59:26  cercato
Bug #9387#

Revision 1.11  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.10  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

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
class TARRPagamentoLoteManual extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRPagamentoLoteManual()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.pagamento_lote_manual');

    $this->setCampoCod('');
    $this->setComplementoChave('numeracao,ocorrencia_pagamento,cod_convenio,cod_banco,cod_agencia');

    $this->AddCampo('numeracao','varchar',true,'17'         ,true,true);
    $this->AddCampo('ocorrencia_pagamento','integer',true,'',true,true);
    $this->AddCampo('cod_convenio','integer',true,''        ,true,true);
    $this->AddCampo('cod_banco','integer',true,''           ,true,true);
    $this->AddCampo('cod_agencia','integer',true,''         ,true,true);
}
/*
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
*/

function montaRecuperaRelacionamento()
{
    $stSql  = "       SELECT ap.numeracao                                              \n";
    $stSql .= "            , ap.ocorrencia_pagamento                                   \n";
    $stSql .= "            , ap.cod_convenio                                           \n";
    $stSql .= "            , cod_agencia                                               \n";
    $stSql .= "            , cod_banco                                                 \n";
    $stSql .= "            , CASE                                                      \n";
    $stSql .= "                 WHEN atp.pagamento = 't' THEN 'Pagamento'::text        \n";
    $stSql .= "                 ELSE 'Cancelamento'::text                              \n";
    $stSql .= "              END AS pagamento                                          \n";
    $stSql .= "         from arrecadacao.pagamento ap                                  \n";
    $stSql .= "   inner join arrecadacao.pagamento_lote_manual aplm                    \n";
    $stSql .= "           on ap.numeracao = aplm.numeracao                             \n";
    $stSql .= "          and ap.ocorrencia_pagamento = aplm.ocorrencia_pagamento       \n";
    $stSql .= "          and ap.cod_convenio = aplm.cod_convenio                       \n";
    $stSql .= "   inner join arrecadacao.tipo_pagamento atp                            \n";
    $stSql .= "           on atp.cod_tipo = ap.cod_tipo                                \n";

    return $stSql;
}

function recuperaListaFechaBaixaManual(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaFechaBaixaManual().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaFechaBaixaManual()
{
    $stSql   = "SELECT                                               \n";
    $stSql  .= "    ap.cod_lancamento,                               \n";
    $stSql  .= "    apl.numeracao,                                   \n";
    $stSql  .= "    ac.cod_parcela,                                  \n";
    $stSql  .= "    arp.data_pagamento                               \n";
    $stSql  .= "FROM                                                 \n";
    $stSql  .= "    arrecadacao.pagamento_lote as apl                \n";
    $stSql  .= "INNER JOIN                                           \n";
    $stSql  .= "    arrecadacao.carne as ac                          \n";
    $stSql  .= "ON                                                   \n";
    $stSql  .= "    ac.numeracao = apl.numeracao                     \n";
    $stSql  .= "INNER JOIN                                           \n";
    $stSql  .= "    arrecadacao.parcela as ap                        \n";
    $stSql  .= "ON                                                   \n";
    $stSql  .= "    ac.cod_parcela = ap.cod_parcela                  \n";
    $stSql  .= "INNER JOIN                                           \n";
    $stSql  .= "    arrecadacao.pagamento as arp                     \n";
    $stSql  .= "ON                                                   \n";
    $stSql  .= "    arp.numeracao = ac.numeracao                     \n";

    return $stSql;
}

function recuperaListaEstornoBaixaManual(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaEstornoBaixaManual().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaEstornoBaixaManual()
{
    $stSql  = "
SELECT
    alc.cod_calculo,
    CASE
        WHEN aec.inscricao_economica IS NOT NULL THEN aec.inscricao_economica
        WHEN aic.inscricao_municipal IS NOT NULL THEN aic.inscricao_municipal
    END AS inscricao,
    apa.numcgm,
    (
        SELECT
            nom_cgm
        FROM
            sw_cgm
        WHERE
            sw_cgm.numcgm = apa.numcgm
    ) AS nomcgm,
    ap.nr_parcela,
    ap.valor AS valor_parcela,
    CASE
        WHEN acgc.descricao IS NOT NULL THEN acgc.descricao||'/'||acgc.cod_grupo
        WHEN mc.descricao_credito IS NOT NULL THEN mc.descricao_credito||'/'||mc.cod_credito||'/'||mc.cod_natureza||'/'||mc.cod_genero||'/'||mc.cod_especie
    END AS descricao_grupo_credito,
    aplm.numeracao AS num_carne,
    alot.cod_banco,
    alot.cod_agencia,
    ma.num_agencia,
    mb.num_banco  ,
    aplm.ocorrencia_pagamento,
    aplm.cod_convenio,
    alot.cod_lote,
    alot.exercicio

FROM
    arrecadacao.pagamento_lote AS aplm

inner join
    (
         SELECT
            cod_lote,
            exercicio
        from
            arrecadacao.pagamento_lote
        group by cod_lote, exercicio having count(cod_lote) = 1
    ) AS tot_aplm
ON
    tot_aplm.cod_lote = aplm.cod_lote
    AND tot_aplm.exercicio = aplm.exercicio

inner join
    arrecadacao.lote AS alot
on
    alot.cod_lote = aplm.cod_lote
    AND alot.exercicio = aplm.exercicio
    AND alot.automatico = FALSE

inner join
    monetario.agencia ma
on
    ma.cod_agencia = alot.cod_agencia
    and ma.cod_banco = alot.cod_banco

inner join
    monetario.banco mb
on
    mb.cod_banco = ma.cod_banco

INNER JOIN
    arrecadacao.pagamento apa
ON
    apa.numeracao = aplm.numeracao AND
    apa.cod_convenio = aplm.cod_convenio

INNER JOIN
    arrecadacao.carne acr
ON
    acr.numeracao = aplm.numeracao AND
    acr.cod_convenio = aplm.cod_convenio

INNER JOIN
    arrecadacao.parcela ap
ON
    acr.cod_parcela = ap.cod_parcela

INNER JOIN
    arrecadacao.lancamento al
ON
    al.cod_lancamento = ap.cod_lancamento

INNER JOIN
    (
        SELECT
            max(cod_calculo) as cod_calculo,
            cod_lancamento
        FROM
            arrecadacao.lancamento_calculo
        GROUP BY
            cod_lancamento
    ) alc
ON
    alc.cod_lancamento = al.cod_lancamento

LEFT JOIN
    (
        SELECT
            agc.cod_grupo,
            agc.descricao,
            acgc.cod_calculo
        FROM
            arrecadacao.calculo_grupo_credito as acgc,
            arrecadacao.grupo_credito as agc
        WHERE
            agc.cod_grupo = acgc.cod_grupo
            AND agc.ano_exercicio = acgc.ano_exercicio
    ) acgc
ON
    acgc.cod_calculo = alc.cod_calculo

INNER JOIN
    arrecadacao.calculo ac
ON
    ac.cod_calculo = alc.cod_calculo

LEFT JOIN
    monetario.credito as mc
ON
    mc.cod_credito = ac.cod_credito AND
    mc.cod_natureza = ac.cod_natureza AND
    mc.cod_genero = ac.cod_genero AND
    mc.cod_especie = ac.cod_especie

LEFT JOIN
    arrecadacao.imovel_calculo AS aic
ON
    ac.cod_calculo = aic.cod_calculo

LEFT JOIN
    arrecadacao.cadastro_economico_calculo AS aec
ON
    ac.cod_calculo = aec.cod_calculo
\n";

    return $stSql;
}

function recuperaListaPagamentosManuaisAFechar(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaPagamentosManuaisAFechar().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    //$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaPagamentosManuaisAFechar()
{
    $stSql  = "    SELECT \n";
    $stSql .= "        aplm.numeracao, \n";
    $stSql .= "        to_char (ap.vencimento,'dd/mm/YYYY') AS vencimento_parcela,      \n";
    $stSql .= "        to_char (apa.data_pagamento, 'dd/mm/YYYY') as data_pagamento,\n";
    $stSql .= "        ap.valor AS valor_parcela, \n";
    $stSql .= "        apa.valor AS valor_pago, \n";
    $stSql .= "        swc.nom_cgm, \n";
    $stSql .= "        aic.numcgm, \n";
    $stSql .= "        aplm.cod_banco, \n";
    $stSql .= "        aplm.cod_agencia \n";
    $stSql .= "    FROM \n";
    $stSql .= "        arrecadacao.calculo_cgm AS aic \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        arrecadacao.calculo ac \n";
    $stSql .= "    ON \n";
    $stSql .= "        ac.cod_calculo = aic.cod_calculo  \n";
    $stSql .= "    INNER JOIN  \n";
    $stSql .= "        ( \n";
    $stSql .= "            SELECT  \n";
    $stSql .= "                max(cod_calculo) as cod_calculo, \n";
    $stSql .= "                cod_lancamento \n";
    $stSql .= "            FROM \n";
    $stSql .= "                arrecadacao.lancamento_calculo \n";
    $stSql .= "            GROUP BY \n";
    $stSql .= "                cod_lancamento \n";
    $stSql .= "        ) alc \n";
    $stSql .= "    ON \n";
    $stSql .= "        alc.cod_calculo = ac.cod_calculo \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        arrecadacao.lancamento al \n";
    $stSql .= "    ON \n";
    $stSql .= "        al.cod_lancamento = alc.cod_lancamento \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        arrecadacao.parcela ap \n";
    $stSql .= "    ON \n";
    $stSql .= "        al.cod_lancamento = ap.cod_lancamento \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        arrecadacao.carne acr \n";
    $stSql .= "    ON \n";
    $stSql .= "        acr.cod_parcela = ap.cod_parcela \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        arrecadacao.pagamento apa \n";
    $stSql .= "    ON \n";
    $stSql .= "        apa.numeracao = acr.numeracao AND \n";
    $stSql .= "        apa.cod_convenio = acr.cod_convenio \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        arrecadacao.pagamento_lote_manual AS aplm \n";
    $stSql .= "    ON \n";
    $stSql .= "        aplm.numeracao = apa.numeracao AND \n";
    $stSql .= "        aplm.cod_convenio = apa.cod_convenio \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        sw_cgm swc \n";
    $stSql .= "    ON \n";
    $stSql .= "        swc.numcgm = aic.numcgm \n";

    return $stSql;
}

}// end of class
?>
