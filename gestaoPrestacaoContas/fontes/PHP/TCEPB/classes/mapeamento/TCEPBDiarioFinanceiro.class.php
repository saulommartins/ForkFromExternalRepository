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
    * Data de Criação: 17/04/2007

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
Revision 1.2  2007/04/23 15:15:39  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/04/19 18:53:37  vitor
Inclusão  uc-06.04.00

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCEPBDiarioFinanceiro extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEPBDiarioFinanceiro()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function montaRecuperaTodos()
{
     $stSql  = "      SELECT                                                                     \n";
     $stSql .= "         '0' as reservado_tce,                                                   \n";
     $stSql .= "          vl.oid_lancamento as cod_lancamento,                                   \n";
     $stSql .= "          to_char( lo.dt_lote, 'dd/mm/yyyy') AS data_lancamento,                 \n";
     $stSql .= "          CASE WHEN (cle.estorno = false) or (cle.estorno is null) THEN          \n";
     $stSql .= "                    '1'                                                          \n";
     $stSql .= "             ELSE                                                                \n";
     $stSql .= "                     CASE WHEN lrt.estorno = false THEN                          \n";
     $stSql .= "                            '1'                                                  \n";
     $stSql .= "                 ELSE                                                            \n";
     $stSql .= "                     CASE WHEN clt.estorno = false THEN                          \n";
     $stSql .= "                            '1'                                                  \n";
     $stSql .= "                     ELSE                                                        \n";
     $stSql .= "                            '2'                                                  \n";
     $stSql .= "                     END                                                         \n";
     $stSql .= "                 END                                                             \n";
     $stSql .= "             END as tipo_registro,                                               \n";
     $stSql .= "         CASE WHEN l.cod_historico = 901 THEN                                    \n";
     $stSql .= "                    '1'                                                          \n";
     $stSql .= "             ELSE                                                                \n";
     $stSql .= "                 CASE WHEN l.cod_historico = 800 THEN                            \n";
     $stSql .= "                            '3'                                                  \n";
     $stSql .= "                 ELSE                                                            \n";
     $stSql .= "                       '2'                                                       \n";
     $stSql .= "                 END                                                             \n";
     $stSql .= "             END as tipo_movimentacao,                                           \n";
     $stSql .= "substr(trim(hc.nom_historico)||' '||trim(l.complemento), 1,150) as nom_historico,\n";
     $stSql .= "          '0' as reservado_tce2                                                  \n";
     $stSql .= "                                                                                 \n";
     $stSql .= "      FROM                                                                       \n";
     $stSql .= "             contabilidade.lancamento         AS l,                              \n";
     $stSql .= "             contabilidade.lote               AS lo,                             \n";
     $stSql .= "             contabilidade.historico_contabil AS hc,                             \n";
     $stSql .= "             orcamento.entidade               as en,                             \n";
     $stSql .= "             sw_cgm                           as cgm,                            \n";
     $stSql .= "             contabilidade.valor_lancamento   AS vl                              \n";
     $stSql .= "      LEFT JOIN                                                                  \n";
     $stSql .= "             contabilidade.conta_credito AS cc                                   \n";
     $stSql .= "              ON (                                                               \n";
     $stSql .= "                  cc.cod_lote     = vl.cod_lote       AND                        \n";
     $stSql .= "                  cc.tipo         = vl.tipo           AND                        \n";
     $stSql .= "                  cc.sequencia    = vl.sequencia      AND                        \n";
     $stSql .= "                  cc.exercicio    = vl.exercicio      AND                        \n";
     $stSql .= "                  cc.tipo_valor   = vl.tipo_valor     AND                        \n";
     $stSql .= "                  cc.cod_entidade = vl.cod_entidade                              \n";
     $stSql .= "              )                                                                  \n";
     $stSql .= "      LEFT JOIN                                                                  \n";
     $stSql .= "             contabilidade.conta_debito AS cd                                    \n";
     $stSql .= "              ON (                                                               \n";
     $stSql .= "                  cd.cod_lote     = vl.cod_lote       AND                        \n";
     $stSql .= "                  cd.tipo         = vl.tipo           AND                        \n";
     $stSql .= "                  cd.sequencia    = vl.sequencia      AND                        \n";
     $stSql .= "                  cd.exercicio    = vl.exercicio      AND                        \n";
     $stSql .= "                  cd.tipo_valor   = vl.tipo_valor     AND                        \n";
     $stSql .= "                  cd.cod_entidade = vl.cod_entidade                              \n";
     $stSql .= "              )                                                                  \n";
     $stSql .= "                                                                                 \n";
     $stSql .= "      LEFT JOIN (  SELECT                                                        \n";
     $stSql .= "                        l.cod_lote,                                              \n";
     $stSql .= "                        l.tipo,                                                  \n";
     $stSql .= "                        l.sequencia,                                             \n";
     $stSql .= "                        l.exercicio,                                             \n";
     $stSql .= "                        l.cod_entidade,                                          \n";
     $stSql .= "                        le.estorno                                               \n";
     $stSql .= "                    FROM                                                         \n";
     $stSql .= "                        contabilidade.lancamento AS l                            \n";
     $stSql .= "                    LEFT JOIN                                                    \n";
     $stSql .= "                        contabilidade.lancamento_empenho AS le                   \n";
     $stSql .= "                        ON (                                                     \n";
     $stSql .= "                           le.cod_lote     = l.cod_lote       AND                \n";
     $stSql .= "                           le.exercicio    = l.exercicio      AND                \n";
     $stSql .= "                           le.sequencia    = l.sequencia      AND                \n";
     $stSql .= "                           le.cod_entidade = l.cod_entidade                      \n";
     $stSql .= "                           )                                                     \n";
     $stSql .= "                     WHERE                                                       \n";
     $stSql .= "                           l.exercicio = '".$this->getDado('exercicio')."' AND   \n";
     $stSql .= "                           l.cod_entidade IN (".$this->getDado('stEntidades').") \n";
     $stSql .= "                 )  AS cle  ON (                                                 \n";
     $stSql .= "                      cle.cod_lote     = vl.cod_lote       AND                   \n";
     $stSql .= "                      cle.tipo         = vl.tipo           AND                   \n";
     $stSql .= "                      cle.sequencia    = vl.sequencia      AND                   \n";
     $stSql .= "                      cle.exercicio    = vl.exercicio      AND                   \n";
     $stSql .= "                      cle.cod_entidade = vl.cod_entidade                         \n";
     $stSql .= "                  )                                                              \n";
     $stSql .= "                                                                                 \n";
     $stSql .= "    LEFT JOIN (  SELECT                                                          \n";
     $stSql .= "                        l.cod_lote,                                              \n";
     $stSql .= "                        l.tipo,                                                  \n";
     $stSql .= "                        l.sequencia,                                             \n";
     $stSql .= "                        l.exercicio,                                             \n";
     $stSql .= "                        l.cod_entidade,                                          \n";
     $stSql .= "                        lt.estorno                                               \n";
     $stSql .= "                    FROM                                                         \n";
     $stSql .= "                        contabilidade.lancamento AS l                            \n";
     $stSql .= "                    LEFT JOIN                                                    \n";
     $stSql .= "                        contabilidade.lancamento_transferencia AS lt             \n";
     $stSql .= "                        ON (                                                     \n";
     $stSql .= "                           lt.cod_lote     = l.cod_lote       AND                \n";
     $stSql .= "                           lt.tipo         = l.tipo           AND                \n";
     $stSql .= "                           lt.sequencia    = l.sequencia      AND                \n";
     $stSql .= "                           lt.exercicio    = l.exercicio      AND                \n";
     $stSql .= "                           lt.cod_entidade = l.cod_entidade                      \n";
     $stSql .= "                           )                                                     \n";
     $stSql .= "                     WHERE                                                       \n";
     $stSql .= "                           l.exercicio = '".$this->getDado('exercicio')."' AND   \n";
     $stSql .= "                           l.cod_entidade IN (".$this->getDado('stEntidades').") \n";
     $stSql .= "                 )  AS clt  ON (                                                 \n";
     $stSql .= "                      clt.cod_lote     = vl.cod_lote       AND                   \n";
     $stSql .= "                      clt.tipo         = vl.tipo           AND                   \n";
     $stSql .= "                      clt.sequencia    = vl.sequencia      AND                   \n";
     $stSql .= "                      clt.exercicio    = vl.exercicio      AND                   \n";
     $stSql .= "                      clt.cod_entidade = vl.cod_entidade                         \n";
     $stSql .= "                  )                                                              \n";
     $stSql .= "                                                                                 \n";
     $stSql .= "       LEFT JOIN (  SELECT                                                       \n";
     $stSql .= "                        l.cod_lote,                                              \n";
     $stSql .= "                        l.tipo,                                                  \n";
     $stSql .= "                        l.sequencia,                                             \n";
     $stSql .= "                        l.exercicio,                                             \n";
     $stSql .= "                        l.cod_entidade,                                          \n";
     $stSql .= "                        lr.estorno                                               \n";
     $stSql .= "                    FROM                                                         \n";
     $stSql .= "                        contabilidade.lancamento AS l                            \n";
     $stSql .= "                    LEFT JOIN                                                    \n";
     $stSql .= "                        contabilidade.lancamento_receita AS lr                   \n";
     $stSql .= "                        ON (                                                     \n";
     $stSql .= "                           lr.cod_lote     = l.cod_lote       AND                \n";
     $stSql .= "                           lr.tipo         = l.tipo           AND                \n";
     $stSql .= "                           lr.sequencia    = l.sequencia      AND                \n";
     $stSql .= "                           lr.exercicio    = l.exercicio      AND                \n";
     $stSql .= "                           lr.cod_entidade = l.cod_entidade                      \n";
     $stSql .= "                           )                                                     \n";
     $stSql .= "                     WHERE                                                       \n";
     $stSql .= "                           l.exercicio = '".$this->getDado('exercicio')."' AND   \n";
     $stSql .= "                           l.cod_entidade IN (".$this->getDado('stEntidades').") \n";
     $stSql .= "                                                                                 \n";
     $stSql .= "                 )  AS lrt  ON (                                                 \n";
     $stSql .= "                      lrt.cod_lote     = vl.cod_lote       AND                   \n";
     $stSql .= "                      lrt.tipo         = vl.tipo           AND                   \n";
     $stSql .= "                      lrt.sequencia    = vl.sequencia      AND                   \n";
     $stSql .= "                      lrt.exercicio    = vl.exercicio      AND                   \n";
     $stSql .= "                      lrt.cod_entidade = vl.cod_entidade                         \n";
     $stSql .= "                  )                                                              \n";
     $stSql .= "     WHERE                                                                       \n";
     $stSql .= "              vl.cod_lote      = l.cod_lote        AND                           \n";
     $stSql .= "              vl.tipo          = l.tipo            AND                           \n";
     $stSql .= "              vl.sequencia     = l.sequencia       AND                           \n";
     $stSql .= "              vl.exercicio     = l.exercicio       AND                           \n";
     $stSql .= "              vl.cod_entidade  = l.cod_entidade    AND                           \n";
     $stSql .= "                                                                                 \n";
     $stSql .= "              lo.cod_lote      = l.cod_lote        AND                           \n";
     $stSql .= "              lo.exercicio     = l.exercicio       AND                           \n";
     $stSql .= "              lo.tipo          = l.tipo            AND                           \n";
     $stSql .= "              lo.cod_entidade  = l.cod_entidade    AND                           \n";
     $stSql .= "              hc.cod_historico = l.cod_historico   AND                           \n";
     $stSql .= "              hc.exercicio     = l.exercicio       AND                           \n";
     $stSql .= "              en.cod_entidade  = l.cod_entidade    AND                           \n";
     $stSql .= "              en.exercicio     = l.exercicio       AND                           \n";
     $stSql .= "              cgm.numcgm       = en.numcgm         AND                           \n";
     $stSql .= "              l.exercicio = '".$this->getDado('exercicio')."'           AND      \n";
     $stSql .= "              to_char(lo.dt_lote,'mm') = '".$this->getDado("inMes")."'  AND      \n";
     $stSql .= "              l.cod_entidade IN  ( ".$this->getDado('stEntidades')." )           \n";
     $stSql .= "                                                                                 \n";
     $stSql .= "              ORDER BY                                                           \n";
     $stSql .= "              to_char(dt_lote,'yyyy-mm-dd'),                                     \n";
     $stSql .= "              vl.oid_lancamento,                                                 \n";
     $stSql .= "              l.cod_entidade,                                                    \n";
     $stSql .= "              l.tipo,                                                            \n";
     $stSql .= "              l.sequencia ASC,                                                   \n";
     $stSql .= "              vl.vl_lancamento DESC,                                             \n";
     $stSql .= "              vl.tipo_valor DESC                                                 \n";

     return $stSql;
    }
}

?>