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
    * Extensão da Classe de mapeamento
    * Data de Criação: 20/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.4  2007/05/08 15:14:29  vitor
Correção campo natureza.

Revision 1.3  2007/04/23 15:18:39  rodrigo_sr
uc-06.03.00

Revision 1.2  2007/04/23 13:48:03  vitor
Adicionado arquivo LancamentoContabil.txt

Revision 1.1  2007/04/20 16:02:02  vitor
Inclusão  uc-06.04.00

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCEPBLancamentoContabil extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEPBLancamentoContabil()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function montaRecuperaTodos()
{
$stSql  = "     SELECT                                                            \n";
$stSql .= "        '0' AS reservado_tce,                                          \n";
$stSql .= "        vl.oid_lancamento AS cod_lancamento,                           \n";
$stSql .= "        CASE WHEN cdpc.cod_estrutural IS NOT NULL THEN                 \n";
$stSql .= "             substr(replace(cdpc.cod_estrutural,'.',''),1,9)          \n";
$stSql .= "             ELSE                                                      \n";
$stSql .= "             substr(replace(ccpc.cod_estrutural,'.',''),1,9)          \n";
$stSql .= "             END AS conta_contabil,                                    \n";
$stSql .= "        CASE WHEN ccpc.tipo_valor IS NOT NULL THEN                     \n";
$stSql .= "             2                                                         \n";
$stSql .= "             ELSE                                                      \n";
$stSql .= "             1                                                         \n";
$stSql .= "             END AS natureza,                                          \n";
$stSql .= "         lpad(replace(abs(vl.vl_lancamento)::varchar,'.',','),16,'0') as vl_lancamento,   \n";
$stSql .= "        '0' AS reservado_tce2,                                         \n";
$stSql .= "        'Sem conta corrente' AS conta_corrente,                        \n";
$stSql .= "        0 AS tipo_conta,                                               \n"; 
$stSql .= "        CASE WHEN (	SELECT true FROM tesouraria.transferencia         \n";
$stSql .= "                     WHERE transferencia.exercicio=lo.exercicio        \n";
$stSql .= "                     AND transferencia.cod_lote=lo.cod_lote            \n";
$stSql .= "                     AND transferencia.tipo=lo.tipo                    \n";
$stSql .= "                     AND transferencia.cod_entidade=lo.cod_entidade    \n";
$stSql .= "                     AND transferencia.cod_tipo IN (1,2) -- 1(Pagamento Extra), 2(Arrecadação Extra) \n";
$stSql .= "                     ) IS TRUE                                         \n";
$stSql .= "             THEN 1                                                    \n";
$stSql .= "             ELSE 2                                                    \n";
$stSql .= "             END AS pagam_receb_extra                                  \n";
$stSql .= "                                                                       \n";
$stSql .= "    FROM                                                               \n";
$stSql .= "        contabilidade.lancamento         AS l,                         \n";
$stSql .= "        contabilidade.lote               AS lo,                        \n";
$stSql .= "        orcamento.entidade               AS en,                        \n";
$stSql .= "        sw_cgm                           AS cgm,                       \n";
$stSql .= "        contabilidade.valor_lancamento   AS vl                         \n";
$stSql .= "    LEFT  JOIN (  SELECT                                               \n";
$stSql .= "                      cc.cod_lote,                                     \n";
$stSql .= "                      cc.tipo,                                         \n";
$stSql .= "                      cc.sequencia,                                    \n";
$stSql .= "                      cc.exercicio,                                    \n";
$stSql .= "                      cc.tipo_valor,                                   \n";
$stSql .= "                      cc.cod_entidade,                                 \n";
$stSql .= "                      cc.cod_plano,                                    \n";
$stSql .= "                      pc.cod_estrutural                                \n";
$stSql .= "                  FROM                                                 \n";
$stSql .= "                      contabilidade.plano_analitica     AS pa          \n";
$stSql .= "                  JOIN                                                 \n";
$stSql .= "                      contabilidade.conta_credito       AS cc          \n";
$stSql .= "                      ON (                                             \n";
$stSql .= "                         cc.cod_plano    = pa.cod_plano    AND         \n";
$stSql .= "                         cc.exercicio    = pa.exercicio                \n";
$stSql .= "                         )                                             \n";
$stSql .= "                                                                       \n";
$stSql .= "                  JOIN                                                 \n";
$stSql .= "                      contabilidade.plano_conta         AS pc          \n";
$stSql .= "                      ON (                                             \n";
$stSql .= "                         pc.cod_conta    = pa.cod_conta    AND         \n";
$stSql .= "                         pc.exercicio    = pa.exercicio                \n";
$stSql .= "                         )                                             \n";
$stSql .= "                  WHERE                                                \n";
$stSql .= "                      pa.exercicio = '".$this->getDado('exercicio')."' \n";
$stSql .= "               )  AS  ccpc                                             \n";
$stSql .= "                  ON  (                                                \n";
$stSql .= "                      ccpc.cod_lote     = vl.cod_lote       AND        \n";
$stSql .= "                      ccpc.sequencia    = vl.sequencia      AND        \n";
$stSql .= "                      ccpc.tipo_valor   = vl.tipo_valor     AND        \n";
$stSql .= "                      ccpc.tipo         = vl.tipo           AND        \n";
$stSql .= "                      ccpc.exercicio    = vl.exercicio      AND        \n";
$stSql .= "                      ccpc.cod_entidade = vl.cod_entidade              \n";
$stSql .= "                      )                                                \n";
$stSql .= "                                                                       \n";
$stSql .= "     LEFT JOIN (  SELECT                                               \n";
$stSql .= "                      cd.cod_lote,                                     \n";
$stSql .= "                      cd.tipo,                                         \n";
$stSql .= "                      cd.sequencia,                                    \n";
$stSql .= "                      cd.exercicio,                                    \n";
$stSql .= "                      cd.tipo_valor,                                   \n";
$stSql .= "                      cd.cod_entidade,                                 \n";
$stSql .= "                      cd.cod_plano,                                    \n";
$stSql .= "                      pc.cod_estrutural                                \n";
$stSql .= "                  FROM                                                 \n";
$stSql .= "                      contabilidade.plano_analitica     AS pa          \n";
$stSql .= "                  JOIN                                                 \n";
$stSql .= "                      contabilidade.conta_debito        AS cd          \n";
$stSql .= "                      ON  (                                            \n";
$stSql .= "                          cd.cod_plano    = pa.cod_plano    AND        \n";
$stSql .= "                          cd.exercicio    = pa.exercicio               \n";
$stSql .= "                          )                                            \n";
$stSql .= "                  JOIN                                                 \n";
$stSql .= "                      contabilidade.plano_conta         AS pc          \n";
$stSql .= "                      ON  (                                            \n";
$stSql .= "                          pc.cod_conta    = pa.cod_conta    AND        \n";
$stSql .= "                          pc.exercicio    = pa.exercicio               \n";
$stSql .= "                          )                                            \n";
$stSql .= "                  WHERE                                                \n";
$stSql .= "                      pa.exercicio = '".$this->getDado('exercicio')."' \n";
$stSql .= "             )    AS  cdpc                                             \n";
$stSql .= "                  ON  (                                                \n";
$stSql .= "                      cdpc.cod_lote     = vl.cod_lote       AND        \n";
$stSql .= "                      cdpc.tipo_valor   = vl.tipo_valor     AND        \n";
$stSql .= "                      cdpc.tipo         = vl.tipo           AND        \n";
$stSql .= "                      cdpc.sequencia    = vl.sequencia      AND        \n";
$stSql .= "                      cdpc.exercicio    = vl.exercicio      AND        \n";
$stSql .= "                      cdpc.cod_entidade = vl.cod_entidade              \n";
$stSql .= "                      )                                                \n";
$stSql .= "     WHERE                                                             \n";
$stSql .= "         vl.cod_lote      = l.cod_lote        AND                      \n";
$stSql .= "         vl.tipo          = l.tipo            AND                      \n";
$stSql .= "         vl.sequencia     = l.sequencia       AND                      \n";
$stSql .= "         vl.exercicio     = l.exercicio       AND                      \n";
$stSql .= "         vl.cod_entidade  = l.cod_entidade    AND                      \n";
$stSql .= "                                                                       \n";
$stSql .= "         lo.cod_lote      = l.cod_lote        AND                      \n";
$stSql .= "         lo.exercicio     = l.exercicio       AND                      \n";
$stSql .= "         lo.tipo          = l.tipo            AND                      \n";
$stSql .= "         lo.cod_entidade  = l.cod_entidade    AND                      \n";
$stSql .= "                                                                       \n";
$stSql .= "         en.cod_entidade  = l.cod_entidade    AND                      \n";
$stSql .= "         en.exercicio     = l.exercicio       AND                      \n";
$stSql .= "         cgm.numcgm       = en.numcgm         AND                      \n";
$stSql .= "                                                                       \n";
$stSql .= "         l.exercicio = '".$this->getDado('exercicio')."'  AND          \n";
$stSql .= "         to_char(lo.dt_lote,'mm') = '".$this->getDado("inMes")."' AND  \n";
$stSql .= "         l.cod_entidade IN  ( ".$this->getDado('stEntidades')." ) AND  \n";
$stSql .= "                                                                       \n";
$stSql .= "  (  ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '4%'   OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '4%' )   OR \n";
$stSql .= "     ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '3%'   OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '3%' )   OR \n";
$stSql .= "     ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '9%'   OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '9%' )   OR \n";
$stSql .= "     ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '112%' OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '112%' ) OR \n";
$stSql .= "     ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '211%' OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '211%' ) OR \n";
$stSql .= "     ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '212%' OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '212%' ) OR \n";
$stSql .= "     ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '512%' OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '512%' ) OR \n";
$stSql .= "     ( substr(replace(cdpc.cod_estrutural,'.',''),1,14) like '612%' OR  substr(replace(ccpc.cod_estrutural,'.',''),1,14) like '612%' )    \n";
$stSql .= "  )                                                                    \n";
$stSql .= "     ORDER BY                                                          \n";
$stSql .= "     vl.oid_lancamento                                                 \n";

return $stSql;
    }
}
