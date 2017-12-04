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

    * Data de Criação:

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOREP.class.php 65168 2016-04-29 16:36:09Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOREP extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
$stSql = "
CREATE temporary TABLE tmp_balanco  AS (
  SELECT
    substr(cod_estrutural,1,5) AS cod_estrutural,
    substr(nom_conta,1,30) AS nom_conta,
    vl_saldo_atual,
    nivel,
    nom_sistema
  FROM
    contabilidade.fn_rl_balanco_patrimonial('".$this->getDado('exercicio')."','cod_entidade IN  ( ".$this->getDado('stEntidades')." )  ','01/01/".$this->getDado('exercicio')."','31/12/".$this->getDado('exercicio')."','')
     AS retorno( cod_estrutural varchar
                ,nivel integer
                ,nom_conta varchar
                ,vl_saldo_anterior numeric
                ,vl_saldo_debitos  numeric
                ,vl_saldo_creditos numeric
                ,vl_saldo_atual    numeric
                ,nom_sistema varchar
                )
  WHERE  (nivel = '2' OR nivel = '3')

  ORDER BY cod_estrutural
);

SELECT
'10' AS tipo_registro,
'".$this->getDado('stEntidades')."' AS cod_orgao,
'".$this->getDado('exercicio')."' AS exercicio,
CASE WHEN (saldo_patrimonial_exercicio > 0) THEN
    '01'
    ELSE
    '02'
    END AS tipo_saldo_pat_ex,
(saldo_patrimonial_exercicio * (-1) ) AS saldo_pat_ex,
CASE WHEN ( saldo_patrimonial_ex_ant  > 0 ) THEN
             '01'
    ELSE
             '02'
    END AS tipo_saldo_ex_ant,
CASE WHEN ( saldo_patrimonial_ex_ant > 0 ) THEN
    saldo_patrimonial_ex_ant
    ELSE
    ( saldo_patrimonial_ex_ant * (-1) )
    END AS saldo_pat_ex_ant,
CASE WHEN ( saldo_patrimonial_ex_atual  > 0 ) THEN
             '01'
    ELSE
             '02'
    END AS tipo_saldo_ex_atual,
CASE WHEN ( saldo_patrimonial_ex_atual > 0 ) THEN
    saldo_patrimonial_ex_atual
    ELSE
    ( saldo_patrimonial_ex_atual * (-1) )
    END AS saldo_pat_ex_atual
FROM(
SELECT
saldo_patrimonial_exercicio,
saldo_patrimonial_ex_ant AS saldo_patrimonial_ex_ant,
CASE WHEN (disp + cred_circ) + (bens_val_circ + cred_lp + investimentos + imobilizado ) > (((pass_circ + pass_lp) + saldo_patrimonial_ex_ant) * (-1)  ) THEN
(((disp + cred_circ) + (bens_val_circ + cred_lp + investimentos + imobilizado )) + ((pass_circ + pass_lp) + saldo_patrimonial_ex_ant)) + saldo_patrimonial_ex_ant
ELSE
((((pass_circ + pass_lp) + saldo_patrimonial_ex_ant) * (-1)) - ((disp + cred_circ) + (bens_val_circ + cred_lp + investimentos + imobilizado ))) + saldo_patrimonial_ex_ant
END AS saldo_patrimonial_ex_atual
FROM(
SELECT
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '2.4.1') AS saldo_patrimonial_ex_ant,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '1.1.1') AS disp,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '1.1.2') AS cred_circ,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '1.1.3') AS bens_val_circ,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '1.2.2') AS cred_lp,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '1.2.3') AS investimentos,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '1.4.2') AS imobilizado,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '2.1.0' AND nivel = 2) AS pass_circ,
(SELECT vl_saldo_atual FROM tmp_balanco WHERE cod_estrutural = '2.2.0' AND nivel = 2) AS pass_lp,
(SELECT (((tbl.e4 - tbl.e49 ) - ((tbl.e3 + tbl.e5) + e6)) ) AS saldo
FROM
(SELECT
(SELECT
      sum(coalesce(valor,0.00))  as total_estrutural_3
FROM
       (
SELECT
    substr(cod_estrutural,1,1) AS cod_estrutural,
    nom_conta,
    sum(valor) as valor
FROM (
(
SELECT
      tbl.cod_estrutural
      ,CPC.nom_conta
      ,sum(coalesce(tbl.valor,0.00)) as valor
FROM(
     SELECT CPC.exercicio
           ,substr( CPC.cod_estrutural, 1, 5 ) as cod_estrutural
           ,sum(coalesce(EIPE.vl_total,0.00))  as valor
     FROM contabilidade.plano_conta     AS CPC
         ,orcamento.conta_despesa       AS OCD
         ,orcamento.despesa             AS OD
         ,empenho.pre_empenho_despesa   AS EPED
         ,empenho.item_pre_empenho      AS EIPE
         ,empenho.empenho               AS EE
     WHERE CPC.exercicio        = OCD.exercicio
       AND CPC.cod_estrutural   = '3.'||OCD.cod_estrutural
       AND OCD.exercicio        = OD.exercicio
       AND OCD.cod_conta        = OD.cod_conta
       AND OD.exercicio         = EPED.exercicio
       AND OD.cod_despesa       = EPED.cod_despesa
       AND EIPE.exercicio       = EPED.exercicio
       AND EIPE.cod_pre_empenho = EPED.cod_pre_empenho
       AND EPED.exercicio       = EE.exercicio
       AND EPED.cod_pre_empenho = EE.cod_pre_empenho
       AND (  CPC.cod_estrutural like '3.3%'
           OR CPC.cod_estrutural like '3.4%' )
       AND CPC.exercicio = '".$this->getDado('exercicio')."'
       AND OD.cod_entidade IN( ".$this->getDado('stEntidades')." )
       AND EE.dt_empenho >= TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy' )
       AND EE.dt_empenho <= TO_DATE('31/12/".$this->getDado('exercicio')."','dd/mm/yyyy' )
     GROUP BY CPC.exercicio
             ,CPC.cod_estrutural
     ORDER BY CPC.exercicio
             ,CPC.cod_estrutural
) as tbl
,contabilidade.plano_conta AS CPC
WHERE tbl.exercicio      = CPC.exercicio
  AND tbl.cod_estrutural = substr( CPC.cod_estrutural,1,5 )
  AND publico.fn_nivel( CPC.cod_estrutural ) <= 3
 GROUP BY tbl.cod_estrutural
        ,CPC.nom_conta
 ORDER BY tbl.cod_estrutural
)
UNION
(
SELECT
      tbl.cod_estrutural
      ,CPC.nom_conta
      ,sum(coalesce(tbl.valor,0.00)) as valor
FROM(
        SELECT CPC.exercicio
           ,substr( CPC.cod_estrutural, 1, 5 ) as cod_estrutural
           ,sum(coalesce(EAI.vl_anulado,0.00)*-1)  as valor
     FROM contabilidade.plano_conta     AS CPC
         ,orcamento.conta_despesa       AS OCD
         ,orcamento.despesa             AS OD
         ,empenho.pre_empenho_despesa   AS EPED
         ,empenho.item_pre_empenho      AS EIPE
         ,empenho.empenho_anulado_item  AS EAI
         ,empenho.empenho               AS EE
     WHERE CPC.exercicio        = OCD.exercicio
       AND CPC.cod_estrutural   = '3.'||OCD.cod_estrutural
       AND OCD.exercicio        = OD.exercicio
       AND OCD.cod_conta        = OD.cod_conta
       AND OD.exercicio         = EPED.exercicio
       AND OD.cod_despesa       = EPED.cod_despesa
       AND EIPE.exercicio       = EPED.exercicio
       AND EIPE.cod_pre_empenho = EPED.cod_pre_empenho
       AND EIPE.cod_pre_empenho    = EAI.cod_pre_empenho
       AND EIPE.exercicio          = EAI.exercicio
       AND EIPE.num_item           = EAI.num_item
       AND EPED.exercicio       = EE.exercicio
       AND EPED.cod_pre_empenho = EE.cod_pre_empenho
       AND (  CPC.cod_estrutural like '3.3%'
           OR CPC.cod_estrutural like '3.4%' )
       AND CPC.exercicio = '".$this->getDado('exercicio')."'
       AND OD.cod_entidade IN( ".$this->getDado('stEntidades')." )
       AND to_date( to_char( EAI.timestamp, 'dd/mm/yyyy'), 'dd/mm/yyyy' ) >= TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy' )
       AND to_date( to_char( EAI.timestamp, 'dd/mm/yyyy'), 'dd/mm/yyyy' ) <= TO_DATE('31/12/".$this->getDado('exercicio')."','dd/mm/yyyy' )
     GROUP BY CPC.exercicio
             ,CPC.cod_estrutural
     ORDER BY CPC.exercicio
             ,CPC.cod_estrutural
) as tbl
,contabilidade.plano_conta AS CPC
WHERE tbl.exercicio      = CPC.exercicio
  AND tbl.cod_estrutural = substr( CPC.cod_estrutural,1,5 )
  AND publico.fn_nivel( CPC.cod_estrutural ) <= 3
 GROUP BY tbl.cod_estrutural
        ,CPC.nom_conta
 ORDER BY tbl.cod_estrutural
)
) as tbl GROUP BY
    cod_estrutural,
    nom_conta
 ORDER BY tbl.cod_estrutural
 )
 AS tmp
GROUP BY
 cod_estrutural
) AS E3,

(SELECT
     sum(tmp.vl_arrecadado) AS total_estrutural_5
FROM(
SELECT substr(tbl.cod_estrutural,1,1) AS cod_estrutural
      ,abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado
FROM(
      SELECT substr( OPC.cod_estrutural, 1, 9 )  AS cod_estrutural
            ,OPC.exercicio
            ,sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito
            ,sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito
      FROM contabilidade.plano_conta    AS OPC

      LEFT JOIN contabilidade.plano_analitica AS OCA
      ON( OPC.cod_conta = OCA.cod_conta
      AND OPC.exercicio = OCA.exercicio  )

      LEFT JOIN ( SELECT CCD.cod_plano
                        ,CCD.exercicio
                        ,sum( vl_lancamento ) as vl_lancamento
                  FROM contabilidade.conta_debito     AS CCD
                      ,contabilidade.valor_lancamento AS CVLD
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCD.cod_lote       = CVLD.cod_lote
                    AND CCD.tipo           = CVLD.tipo
                    AND CCD.sequencia      = CVLD.sequencia
                    AND CCD.exercicio      = CVLD.exercicio
                    AND CCD.tipo_valor     = CVLD.tipo_valor
                    AND CCD.cod_entidade   = CVLD.cod_entidade
                    AND CVLD.tipo_valor    = 'D'
                    AND CVLD.cod_lote      = CLA.cod_lote
                    AND CVLD.tipo          = CLA.tipo
                    AND CVLD.cod_entidade  = CLA.cod_entidade
                    AND CVLD.exercicio     = CLA.exercicio
                    AND CVLD.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCD.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLD.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCD.cod_plano
                          ,CCD.exercicio
                  ORDER BY CCD.cod_plano
                          ,CCD.exercicio
      ) AS CCD ON( OCA.cod_plano = CCD.cod_plano
               AND OCA.exercicio = CCD.exercicio
      )

      LEFT JOIN ( SELECT CCC.cod_plano
                        ,CCC.exercicio
                        ,sum(vl_lancamento) as vl_lancamento
                  FROM contabilidade.conta_credito    AS CCC
                      ,contabilidade.valor_lancamento AS CVLC
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCC.cod_lote       = CVLC.cod_lote
                    AND CCC.tipo           = CVLC.tipo
                    AND CCC.sequencia      = CVLC.sequencia
                    AND CCC.exercicio      = CVLC.exercicio
                    AND CCC.tipo_valor     = CVLC.tipo_valor
                    AND CCC.cod_entidade   = CVLC.cod_entidade
                    AND CVLC.tipo_valor    = 'C'
                    AND CVLC.cod_lote      = CLA.cod_lote
                    AND CVLC.tipo          = CLA.tipo
                    AND CVLC.cod_entidade  = CLA.cod_entidade
                    AND CVLC.exercicio     = CLA.exercicio
                    AND CVLC.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCC.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLC.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCC.cod_plano
                          ,CCC.exercicio
                  ORDER BY CCC.cod_plano
                          ,CCC.exercicio
      ) AS CCC ON ( OCA.cod_plano = CCC.cod_plano
               AND  OCA.exercicio = CCC.exercicio
      )
      WHERE OPC.exercicio = '".$this->getDado('exercicio')."'
      AND ( OPC.cod_estrutural LIKE '5.1.2%'
      OR    OPC.cod_estrutural LIKE '5.1.3%'
      OR    OPC.cod_estrutural LIKE '5.2%'
      )
      GROUP BY OPC.cod_estrutural
              ,OPC.exercicio
      ORDER BY OPC.cod_estrutural
              ,OPC.exercicio
) AS tbl
,contabilidade.plano_conta AS OCR
WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )
AND   (length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9
OR OCR.cod_estrutural = '4.9.7.2.1.01.00.00.00.00' OR OCR.cod_estrutural ='4.9.7.2.2.01.00.00.00.00')
AND   tbl.exercicio      = OCR.exercicio
GROUP BY tbl.cod_estrutural
        ,OCR.nom_conta
ORDER BY tbl.cod_estrutural
        ,OCR.nom_conta
) AS tmp
GROUP BY
        tmp.cod_estrutural

)AS E5,

(SELECT
     sum(tmp.vl_arrecadado) AS total_estrutural_4
FROM(
SELECT substr(tbl.cod_estrutural,1,1) AS cod_estrutural
      ,abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado
FROM(
      SELECT substr( OPC.cod_estrutural, 1, 9 )  AS cod_estrutural
            ,OPC.exercicio
            ,sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito
            ,sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito
      FROM contabilidade.plano_conta    AS OPC

      LEFT JOIN contabilidade.plano_analitica AS OCA
      ON( OPC.cod_conta = OCA.cod_conta
      AND OPC.exercicio = OCA.exercicio  )

      LEFT JOIN ( SELECT CCD.cod_plano
                        ,CCD.exercicio
                        ,sum( vl_lancamento ) as vl_lancamento
                  FROM contabilidade.conta_debito     AS CCD
                      ,contabilidade.valor_lancamento AS CVLD
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCD.cod_lote       = CVLD.cod_lote
                    AND CCD.tipo           = CVLD.tipo
                    AND CCD.sequencia      = CVLD.sequencia
                    AND CCD.exercicio      = CVLD.exercicio
                    AND CCD.tipo_valor     = CVLD.tipo_valor
                    AND CCD.cod_entidade   = CVLD.cod_entidade
                    AND CVLD.tipo_valor    = 'D'
                    AND CVLD.cod_lote      = CLA.cod_lote
                    AND CVLD.tipo          = CLA.tipo
                    AND CVLD.cod_entidade  = CLA.cod_entidade
                    AND CVLD.exercicio     = CLA.exercicio
                    AND CVLD.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCD.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLD.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCD.cod_plano
                          ,CCD.exercicio
                  ORDER BY CCD.cod_plano
                          ,CCD.exercicio
      ) AS CCD ON( OCA.cod_plano = CCD.cod_plano
               AND OCA.exercicio = CCD.exercicio
      )

      LEFT JOIN ( SELECT CCC.cod_plano
                        ,CCC.exercicio
                        ,sum(vl_lancamento) as vl_lancamento
                  FROM contabilidade.conta_credito    AS CCC
                      ,contabilidade.valor_lancamento AS CVLC
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCC.cod_lote       = CVLC.cod_lote
                    AND CCC.tipo           = CVLC.tipo
                    AND CCC.sequencia      = CVLC.sequencia
                    AND CCC.exercicio      = CVLC.exercicio
                    AND CCC.tipo_valor     = CVLC.tipo_valor
                    AND CCC.cod_entidade   = CVLC.cod_entidade
                    AND CVLC.tipo_valor    = 'C'
                    AND CVLC.cod_lote      = CLA.cod_lote
                    AND CVLC.tipo          = CLA.tipo
                    AND CVLC.cod_entidade  = CLA.cod_entidade
                    AND CVLC.exercicio     = CLA.exercicio
                    AND CVLC.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCC.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLC.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCC.cod_plano
                          ,CCC.exercicio
                  ORDER BY CCC.cod_plano
                          ,CCC.exercicio
      ) AS CCC ON ( OCA.cod_plano = CCC.cod_plano
               AND  OCA.exercicio = CCC.exercicio
      )
      WHERE OPC.exercicio = '".$this->getDado('exercicio')."'
      AND ( OPC.cod_estrutural LIKE '4.1%'
      OR    OPC.cod_estrutural LIKE '4.2%'
      )
      GROUP BY OPC.cod_estrutural
              ,OPC.exercicio
      ORDER BY OPC.cod_estrutural
              ,OPC.exercicio
) AS tbl
,contabilidade.plano_conta AS OCR
WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )
AND   (length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9
OR OCR.cod_estrutural = '4.9.7.2.1.01.00.00.00.00' OR OCR.cod_estrutural ='4.9.7.2.2.01.00.00.00.00')
AND   tbl.exercicio      = OCR.exercicio
GROUP BY tbl.cod_estrutural
        ,OCR.nom_conta
ORDER BY tbl.cod_estrutural
        ,OCR.nom_conta
) AS tmp
GROUP BY
        tmp.cod_estrutural
) AS E4,

(SELECT
     sum(tmp.vl_arrecadado) AS total_estrutural_49
FROM(
SELECT substr(tbl.cod_estrutural,1,3) AS cod_estrutural
      ,abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado
FROM(
      SELECT substr( OPC.cod_estrutural, 1, 9 )  AS cod_estrutural
            ,OPC.exercicio
            ,sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito
            ,sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito
      FROM contabilidade.plano_conta    AS OPC

      LEFT JOIN contabilidade.plano_analitica AS OCA
      ON( OPC.cod_conta = OCA.cod_conta
      AND OPC.exercicio = OCA.exercicio  )

      LEFT JOIN ( SELECT CCD.cod_plano
                        ,CCD.exercicio
                        ,sum( vl_lancamento ) as vl_lancamento
                  FROM contabilidade.conta_debito     AS CCD
                      ,contabilidade.valor_lancamento AS CVLD
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCD.cod_lote       = CVLD.cod_lote
                    AND CCD.tipo           = CVLD.tipo
                    AND CCD.sequencia      = CVLD.sequencia
                    AND CCD.exercicio      = CVLD.exercicio
                    AND CCD.tipo_valor     = CVLD.tipo_valor
                    AND CCD.cod_entidade   = CVLD.cod_entidade
                    AND CVLD.tipo_valor    = 'D'
                    AND CVLD.cod_lote      = CLA.cod_lote
                    AND CVLD.tipo          = CLA.tipo
                    AND CVLD.cod_entidade  = CLA.cod_entidade
                    AND CVLD.exercicio     = CLA.exercicio
                    AND CVLD.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCD.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLD.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCD.cod_plano
                          ,CCD.exercicio
                  ORDER BY CCD.cod_plano
                          ,CCD.exercicio
      ) AS CCD ON( OCA.cod_plano = CCD.cod_plano
               AND OCA.exercicio = CCD.exercicio
      )

      LEFT JOIN ( SELECT CCC.cod_plano
                        ,CCC.exercicio
                        ,sum(vl_lancamento) as vl_lancamento
                  FROM contabilidade.conta_credito    AS CCC
                      ,contabilidade.valor_lancamento AS CVLC
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCC.cod_lote       = CVLC.cod_lote
                    AND CCC.tipo           = CVLC.tipo
                    AND CCC.sequencia      = CVLC.sequencia
                    AND CCC.exercicio      = CVLC.exercicio
                    AND CCC.tipo_valor     = CVLC.tipo_valor
                    AND CCC.cod_entidade   = CVLC.cod_entidade
                    AND CVLC.tipo_valor    = 'C'
                    AND CVLC.cod_lote      = CLA.cod_lote
                    AND CVLC.tipo          = CLA.tipo
                    AND CVLC.cod_entidade  = CLA.cod_entidade
                    AND CVLC.exercicio     = CLA.exercicio
                    AND CVLC.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCC.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLC.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCC.cod_plano
                          ,CCC.exercicio
                  ORDER BY CCC.cod_plano
                          ,CCC.exercicio
      ) AS CCC ON ( OCA.cod_plano = CCC.cod_plano
               AND  OCA.exercicio = CCC.exercicio
      )
      WHERE OPC.exercicio = '".$this->getDado('exercicio')."'
      AND OPC.cod_estrutural LIKE '4.9%'

      GROUP BY OPC.cod_estrutural
              ,OPC.exercicio
      ORDER BY OPC.cod_estrutural
              ,OPC.exercicio
) AS tbl
,contabilidade.plano_conta AS OCR
WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )
AND   (length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9
OR OCR.cod_estrutural = '4.9.7.2.1.01.00.00.00.00' OR OCR.cod_estrutural ='4.9.7.2.2.01.00.00.00.00')
AND   tbl.exercicio      = OCR.exercicio
GROUP BY tbl.cod_estrutural
        ,OCR.nom_conta
ORDER BY tbl.cod_estrutural
        ,OCR.nom_conta
) AS tmp
GROUP BY
        tmp.cod_estrutural
) AS E49,

(SELECT
      sum(tmp.vl_arrecadado) AS total_estrutural_6
FROM(
SELECT substr(tbl.cod_estrutural,1,1) AS cod_estrutural
      ,abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado
FROM(
      SELECT substr( OPC.cod_estrutural, 1, 9 )  AS cod_estrutural
            ,OPC.exercicio
            ,sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito
            ,sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito
      FROM contabilidade.plano_conta    AS OPC

      LEFT JOIN contabilidade.plano_analitica AS OCA
      ON( OPC.cod_conta = OCA.cod_conta
      AND OPC.exercicio = OCA.exercicio  )

      LEFT JOIN ( SELECT CCD.cod_plano
                        ,CCD.exercicio
                        ,sum( vl_lancamento ) as vl_lancamento
                  FROM contabilidade.conta_debito     AS CCD
                      ,contabilidade.valor_lancamento AS CVLD
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCD.cod_lote       = CVLD.cod_lote
                    AND CCD.tipo           = CVLD.tipo
                    AND CCD.sequencia      = CVLD.sequencia
                    AND CCD.exercicio      = CVLD.exercicio
                    AND CCD.tipo_valor     = CVLD.tipo_valor
                    AND CCD.cod_entidade   = CVLD.cod_entidade
                    AND CVLD.tipo_valor    = 'D'
                    AND CVLD.cod_lote      = CLA.cod_lote
                    AND CVLD.tipo          = CLA.tipo
                    AND CVLD.cod_entidade  = CLA.cod_entidade
                    AND CVLD.exercicio     = CLA.exercicio
                    AND CVLD.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCD.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLD.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCD.cod_plano
                          ,CCD.exercicio
                  ORDER BY CCD.cod_plano
                          ,CCD.exercicio
      ) AS CCD ON( OCA.cod_plano = CCD.cod_plano
               AND OCA.exercicio = CCD.exercicio
      )

      LEFT JOIN ( SELECT CCC.cod_plano
                        ,CCC.exercicio
                        ,sum(vl_lancamento) as vl_lancamento
                  FROM contabilidade.conta_credito    AS CCC
                      ,contabilidade.valor_lancamento AS CVLC
                      ,contabilidade.lancamento       AS CLA
                      ,contabilidade.lote             AS CLO
                  WHERE CCC.cod_lote       = CVLC.cod_lote
                    AND CCC.tipo           = CVLC.tipo
                    AND CCC.sequencia      = CVLC.sequencia
                    AND CCC.exercicio      = CVLC.exercicio
                    AND CCC.tipo_valor     = CVLC.tipo_valor
                    AND CCC.cod_entidade   = CVLC.cod_entidade
                    AND CVLC.tipo_valor    = 'C'
                    AND CVLC.cod_lote      = CLA.cod_lote
                    AND CVLC.tipo          = CLA.tipo
                    AND CVLC.cod_entidade  = CLA.cod_entidade
                    AND CVLC.exercicio     = CLA.exercicio
                    AND CVLC.sequencia     = CLA.sequencia
                    AND CLA.cod_lote      = CLO.cod_lote
                    AND CLA.tipo          = CLO.tipo
                    AND CLA.cod_entidade  = CLO.cod_entidade
                    AND CLA.exercicio     = CLO.exercicio
                    AND CCC.exercicio      = '".$this->getDado('exercicio')."'
                    AND CVLC.cod_entidade  IN( ".$this->getDado('stEntidades')." )
                    AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                        AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                    AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCC.cod_plano
                          ,CCC.exercicio
                  ORDER BY CCC.cod_plano
                          ,CCC.exercicio
      ) AS CCC ON ( OCA.cod_plano = CCC.cod_plano
               AND  OCA.exercicio = CCC.exercicio
      )
      WHERE OPC.exercicio = '".$this->getDado('exercicio')."'
      AND ( OPC.cod_estrutural LIKE '6.1%'
      OR    OPC.cod_estrutural LIKE '6.2%'

      )
      GROUP BY OPC.cod_estrutural
              ,OPC.exercicio
      ORDER BY OPC.cod_estrutural
              ,OPC.exercicio
) AS tbl
,contabilidade.plano_conta AS OCR
WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )
AND   (length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9
OR OCR.cod_estrutural = '4.9.7.2.1.01.00.00.00.00' OR OCR.cod_estrutural ='4.9.7.2.2.01.00.00.00.00')
AND   tbl.exercicio      = OCR.exercicio
GROUP BY tbl.cod_estrutural
        ,OCR.nom_conta
ORDER BY tbl.cod_estrutural
        ,OCR.nom_conta
) AS tmp
GROUP BY
        tmp.cod_estrutural
) AS e6
) AS tbl
) AS saldo_patrimonial_exercicio
) AS atfinanceiro
) AS saldopatrimonial
;
";
return $stSql;
}

public function recuperaTodos2015(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
{
    $rsRecordSet = new RecordSet();
    $obConexao   = new Conexao();
    
    $stSQL = $this->montaRecuperaTodos2015();
    $this->setDebug($stSQL);
    
    return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos2015()
{
$stSql = "
SELECT *
     , 10 AS tipo_registro
     , '".$this->getDado('stEntidades')."' AS cod_orgao
     , '".$this->getDado('exercicio')."' AS exercicio
     , 0 AS numero_registro
  FROM
(
SELECT CASE WHEN SUM(despesas) > SUM(receitas)
            THEN '02'
            ELSE '01'
        END AS tipo_saldo_pat_ex
     , SUM(receitas) - SUM(despesas) AS saldo_pat_ex
  FROM (
SELECT CASE WHEN cod_estrutural ILIKE '5.%'
            THEN tabela1.vl_arrecadado
            ELSE 0.00
        END AS despesas
     , CASE WHEN cod_estrutural ILIKE '4.%'
            THEN tabela1.vl_arrecadado
            ELSE CASE WHEN cod_estrutural ILIKE '6.%'
                      THEN tabela1.vl_arrecadado
                      ELSE 0.00
                  END
             END AS receitas
     , cod_estrutural
FROM (
   SELECT tbl.cod_estrutural                                                                                
        , abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado        
        , OCR.nom_conta                                                                                     
        , CASE WHEN publico.fn_nivel( tbl.cod_estrutural ) > 5                                              
               THEN 5                                                                                           
               ELSE publico.fn_nivel( tbl.cod_estrutural )                                                      
           END AS nivel                                                                                       
     FROM ( SELECT substr( OPC.cod_estrutural, 1, 9 )  AS cod_estrutural                                       
                 , OPC.exercicio                                                                               
                 , sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito                 
                 , sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito                
              FROM contabilidade.plano_conta    AS OPC
              -- Join com plano analitica                                                                        
         LEFT JOIN contabilidade.plano_analitica AS OCA
                ON OPC.cod_conta = OCA.cod_conta
               AND OPC.exercicio = OCA.exercicio
         -- Join com contabilidade.valor_lancamento                                                         
         LEFT JOIN (SELECT CCD.cod_plano
                         , CCD.exercicio
                         , sum( vl_lancamento ) as vl_lancamento                                           
                      FROM contabilidade.conta_debito     AS CCD                                         
                         , contabilidade.valor_lancamento AS CVLD                                        
                         , contabilidade.lancamento       AS CLA                                        
                         , contabilidade.lote             AS CLO                                         
                     WHERE CCD.cod_lote      = CVLD.cod_lote                                               
                       AND CCD.tipo          = CVLD.tipo
                       AND CCD.sequencia     = CVLD.sequencia
                       AND CCD.exercicio     = CVLD.exercicio
                       AND CCD.tipo_valor    = CVLD.tipo_valor
                       AND CCD.cod_entidade  = CVLD.cod_entidade
                       AND CVLD.tipo_valor   = 'D'
                       AND CVLD.cod_lote     = CLA.cod_lote
                       AND CVLD.tipo         = CLA.tipo
                       AND CVLD.cod_entidade = CLA.cod_entidade
                       AND CVLD.exercicio    = CLA.exercicio
                       AND CVLD.sequencia    = CLA.sequencia
                       AND CLA.cod_lote      = CLO.cod_lote
                       AND CLA.tipo          = CLO.tipo
                       AND CLA.cod_entidade  = CLO.cod_entidade
                       AND CLA.exercicio     = CLO.exercicio
                       AND CCD.exercicio     = '".$this->getDado('exercicio')."'
                       AND CVLD.cod_entidade IN( ".$this->getDado('stEntidades')." )
                       AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                                           AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )
                       AND CLA.cod_historico not between 800 and 899
                  GROUP BY CCD.cod_plano
                         , CCD.exercicio
                  ORDER BY CCD.cod_plano
                         , CCD.exercicio
                   ) AS CCD
                ON OCA.cod_plano = CCD.cod_plano
               AND OCA.exercicio = CCD.exercicio
         -- Join com contabilidade.valor_lancamento
         LEFT JOIN (SELECT CCC.cod_plano
                         , CCC.exercicio
                         , sum(vl_lancamento) as vl_lancamento
                      FROM contabilidade.conta_credito    AS CCC
                         , contabilidade.valor_lancamento AS CVLC
                         , contabilidade.lancamento       AS CLA                                        
                         , contabilidade.lote             AS CLO                                         
                     WHERE CCC.cod_lote      = CVLC.cod_lote                                               
                       AND CCC.tipo          = CVLC.tipo                                                   
                       AND CCC.sequencia     = CVLC.sequencia                                              
                       AND CCC.exercicio     = CVLC.exercicio                                              
                       AND CCC.tipo_valor    = CVLC.tipo_valor                                             
                       AND CCC.cod_entidade  = CVLC.cod_entidade                                           
                       AND CVLC.tipo_valor   = 'C'                                                         
                       AND CVLC.cod_lote     = CLA.cod_lote                                                 
                       AND CVLC.tipo         = CLA.tipo                                                     
                       AND CVLC.cod_entidade = CLA.cod_entidade                                             
                       AND CVLC.exercicio    = CLA.exercicio                                                
                       AND CVLC.sequencia    = CLA.sequencia                                                
                       AND CLA.cod_lote      = CLO.cod_lote                                                
                       AND CLA.tipo          = CLO.tipo                                                    
                       AND CLA.cod_entidade  = CLO.cod_entidade                                            
                       AND CLA.exercicio     = CLO.exercicio                                               
                       AND CCC.exercicio     = '".$this->getDado('exercicio')."'                           
                       AND CVLC.cod_entidade IN( ".$this->getDado('stEntidades')." )                      
                       AND CLO.dt_lote BETWEEN TO_DATE( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )  
                                           AND TO_DATE( '31/12/".$this->getDado('exercicio')."', 'dd/mm/yyyy' )  
                       AND CLA.cod_historico not between 800 and 899                                          
                  GROUP BY CCC.cod_plano                                                                 
                         , CCC.exercicio                                                                 
                  ORDER BY CCC.cod_plano                                                                 
                         , CCC.exercicio                                                                 
                   ) AS CCC
                ON OCA.cod_plano = CCC.cod_plano                                                        
               AND  OCA.exercicio = CCC.exercicio                                                                                             
             WHERE OPC.exercicio = '".$this->getDado('exercicio')."'                                            
               AND (OPC.cod_estrutural LIKE '4.1%'   OR
                    OPC.cod_estrutural LIKE '4.2%'   OR
                    OPC.cod_estrutural LIKE '4.7%'   OR
                    OPC.cod_estrutural LIKE '4.8%'   OR
                    OPC.cod_estrutural LIKE '9.%'    OR
                    OPC.cod_estrutural LIKE '3.3%'   OR
                    OPC.cod_estrutural LIKE '3.4%'   OR
                    OPC.cod_estrutural LIKE '5.1.2%' OR
                    OPC.cod_estrutural LIKE '5.1.3%' OR
                    OPC.cod_estrutural LIKE '5.2%'   OR
                    OPC.cod_estrutural LIKE '6.1%'   OR
                    OPC.cod_estrutural LIKE '6.2%'
                   )
          GROUP BY OPC.cod_estrutural                                                                        
                 , OPC.exercicio                                                                             
          ORDER BY OPC.cod_estrutural                                                                        
                 , OPC.exercicio                                                                             
          ) AS tbl                                                                                                 
        , contabilidade.plano_conta AS OCR                                                                        
    WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )                                            
      AND length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9                               
      AND tbl.exercicio      = OCR.exercicio                                                                 
 GROUP BY tbl.cod_estrutural                                                                              
        , OCR.nom_conta                                                                                   
 ORDER BY tbl.cod_estrutural                                                                              
        , OCR.nom_conta                                                                                   
 ) AS tabela1
 ) AS tabela2
) AS resultado_patrimonial
,(
SELECT CASE WHEN abs(ativo.vl_ativo_real) - abs(passivo.vl_passivo_real) > 0.00
            THEN '01'
            ELSE '02'
        END AS tipo_saldo_ex_ant
     , abs(abs(ativo.vl_ativo_real) - abs(passivo.vl_passivo_real)) AS saldo_pat_ex_ant
  FROM (SELECT SUM(vl_saldo_atual) AS vl_ativo_real
          FROM contabilidade.fn_rl_balanco_patrimonial('".($this->getDado('exercicio')-1)."','cod_entidade IN  ( ".$this->getDado('stEntidades')." )  ','01/01/".($this->getDado('exercicio')-1)."','31/12/".($this->getDado('exercicio')-1)."','')
            AS retorno( cod_estrutural varchar
                      , nivel integer
                      , nom_conta varchar
                      , vl_saldo_anterior numeric
                      , vl_saldo_debitos  numeric
                      , vl_saldo_creditos numeric
                      , vl_saldo_atual    numeric
                      , nom_sistema varchar
                      )
         WHERE cod_estrutural ILIKE '1.%'
           AND (nom_sistema ILIKE '%Financeiro%' OR nom_sistema ILIKE '%Patrimonial%')
       )
    AS ativo
     , (SELECT SUM(vl_saldo_atual) AS vl_passivo_real
          FROM contabilidade.fn_rl_balanco_patrimonial('".($this->getDado('exercicio')-1)."','cod_entidade IN  ( ".$this->getDado('stEntidades')." )  ','01/01/".($this->getDado('exercicio')-1)."','31/12/".($this->getDado('exercicio')-1)."','')
            AS retorno( cod_estrutural varchar
                      , nivel integer
                      , nom_conta varchar
                      , vl_saldo_anterior numeric
                      , vl_saldo_debitos  numeric
                      , vl_saldo_creditos numeric
                      , vl_saldo_atual    numeric
                      , nom_sistema varchar
                      )
         WHERE (cod_estrutural  ILIKE '2.1%' OR cod_estrutural  ILIKE '2.2%')
           AND (nom_sistema ILIKE '%Financeiro%' OR nom_sistema ILIKE '%Patrimonial%')
       ) AS passivo
) AS exercicio_anterior
,(
SELECT CASE WHEN abs(ativo.vl_ativo_real) - abs(passivo.vl_passivo_real) > 0.00
            THEN '01'
            ELSE '02'
        END AS tipo_saldo_ex_atual
     , ABS(abs(ativo.vl_ativo_real) - abs(passivo.vl_passivo_real)) AS saldo_pat_ex_atual
  FROM (SELECT SUM(vl_saldo_atual) AS vl_ativo_real
          FROM contabilidade.fn_rl_balanco_patrimonial('".$this->getDado('exercicio')."','cod_entidade IN  ( ".$this->getDado('stEntidades')." )  ','01/01/".$this->getDado('exercicio')."','31/12/".$this->getDado('exercicio')."','')
            AS retorno( cod_estrutural varchar
                      , nivel integer
                      , nom_conta varchar
                      , vl_saldo_anterior numeric
                      , vl_saldo_debitos  numeric
                      , vl_saldo_creditos numeric
                      , vl_saldo_atual    numeric
                      , nom_sistema varchar
                      )
         WHERE cod_estrutural ILIKE '1.%'
           AND (nom_sistema ILIKE '%Financeiro%' OR nom_sistema ILIKE '%Patrimonial%')
       )
    AS ativo
     , (SELECT SUM(vl_saldo_atual) AS vl_passivo_real
          FROM contabilidade.fn_rl_balanco_patrimonial('".$this->getDado('exercicio')."','cod_entidade IN  ( ".$this->getDado('stEntidades')." )  ','01/01/".$this->getDado('exercicio')."','31/12/".$this->getDado('exercicio')."','')
            AS retorno( cod_estrutural varchar
                      , nivel integer
                      , nom_conta varchar
                      , vl_saldo_anterior numeric
                      , vl_saldo_debitos  numeric
                      , vl_saldo_creditos numeric
                      , vl_saldo_atual    numeric
                      , nom_sistema varchar
                      )
         WHERE (cod_estrutural  ILIKE '2.1%' OR cod_estrutural  ILIKE '2.2%')
           AND (nom_sistema ILIKE '%Financeiro%' OR nom_sistema ILIKE '%Patrimonial%')
       ) AS passivo
 ) AS exercicio_atual
";
return $stSql;
}

}
