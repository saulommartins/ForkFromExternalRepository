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
    * Classe de mapeamento para relatorio Razão por credor
    * Data de Criação: 14/06/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31583 $
    $Name$
    $Autor:$
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.02.16
*/

/*
$Log$
Revision 1.9  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeRelatorioRazaoCredor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeRelatorioRazaoCredor()
{
    parent::Persistente();
    $this->setTabela('');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('cod_entidade'     ,'varchar',false,'',false,false);
    $this->AddCampo('dt_inicial'       ,'integer',false,'',false,false);
    $this->AddCampo('dt_final'         ,'varchar',false,'',false,false);
    $this->AddCampo('num_orgao'        ,'integer',false,'',false,false);
    $this->AddCampo('num_unidade'      ,'integer',false,'',false,false);
    $this->AddCampo('cod_despesa'      ,'integer',false,'',false,false);
    $this->AddCampo('cod_recurso'      ,'integer',false,'',false,false);
    $this->AddCampo('cgm_beneficiario' ,'varchar',false,'',false,false);
    $this->AddCampo('stFiltro'         ,'varchar',false,'',false,false);

}

function montaRecuperaTodos()
{
    $stSql .= "SELECT tbl.*                                                                             \n";
    $stSql .= "      ,conta_debito.cod_estrutural  AS cod_estrutural_debito                             \n";
    $stSql .= "      ,conta_credito.cod_estrutural AS cod_estrutural_credito                            \n";
    $stSql .= "      , publico.fn_mascara_dinamica( ( SELECT valor                                      \n";
    $stSql .= "                                       FROM administracao.configuracao                              \n";
    $stSql .= "                                       WHERE parametro = 'masc_despesa'                  \n";
    $stSql .= "                                         AND exercicio = '".$this->getDado('exercicio')."' ), dotacao ) as dotacao_formatada  \n";
    $stSql .= "FROM (                                                                                   \n";
    $stSql .= "       SELECT tbl.cgm_beneficiario                                                       \n";
    $stSql .= "             ,CGM.nom_cgm                                                                \n";
    $stSql .= "             ,tbl.exercicio                                                              \n";
    $stSql .= "             ,tbl.exercicio_empenho                                                      \n";
    $stSql .= "             ,tbl.cod_entidade                                                           \n";
    $stSql .= "             ,tbl.cod_empenho                                                            \n";
    $stSql .= "             ,CLE.estorno                                                                \n";
    $stSql .= "             ,CL.complemento                                                             \n";
    $stSql .= "             ,tbl.tipo                                                                   \n";
    $stSql .= "             ,CVL.cod_lote                                                               \n";
    $stSql .= "             ,CVL.sequencia                                                              \n";
    $stSql .= "             ,CL.cod_historico                                                           \n";
    $stSql .= "             ,CHC.nom_historico                                                          \n";
    $stSql .= "             ,TO_CHAR( CLO.dt_lote, 'dd/mm/yyyy' ) as dt_lote                            \n";
    $stSql .= "             ,contabilidade.fn_recupera_conta_lancamento( CVL.exercicio              \n";
    $stSql .= "                                                             ,CVL.cod_entidade           \n";
    $stSql .= "                                                             ,CVL.cod_lote               \n";
    $stSql .= "                                                             ,CVL.tipo                   \n";
    $stSql .= "                                                             ,CVL.sequencia              \n";
    $stSql .= "                                                             ,'D'                        \n";
    $stSql .= "             ) AS cod_plano_debito                                                       \n";
    $stSql .= "             ,contabilidade.fn_recupera_conta_lancamento( CVL.exercicio              \n";
    $stSql .= "                                                             ,CVL.cod_entidade           \n";
    $stSql .= "                                                             ,CVL.cod_lote               \n";
    $stSql .= "                                                             ,CVL.tipo                   \n";
    $stSql .= "                                                             ,CVL.sequencia              \n";
    $stSql .= "                                                             ,'C'                        \n";
    $stSql .= "             ) AS cod_plano_credito                                                      \n";
    $stSql .= "             ,abs( CVL.vl_lancamento ) AS vl_lancamento                                  \n";
    $stSql .= "             ,CASE WHEN tbl.implantado = false                                           \n";
    $stSql .= "                THEN OD.num_orgao                                                        \n";
    $stSql .= "              ||'.'||OD.num_unidade                                                      \n";
    $stSql .= "              ||'.'||OD.cod_funcao                                                       \n";
    $stSql .= "              ||'.'||OD.cod_subfuncao                                                    \n";
    $stSql .= "              ||'.'||OD.cod_programa                                                     \n";
    $stSql .= "              ||'.'||OD.num_pao                                                          \n";
    $stSql .= "              ||'.'||replace(OCD.cod_estrutural,'.','')                                  \n";
    $stSql .= "                ELSE ( SELECT ERPE.num_orgao                                             \n";
    $stSql .= "                       ||'.'||ERPE.num_unidade                                           \n";
    $stSql .= "                       ||'.'||ERPE.cod_funcao                                            \n";
    $stSql .= "                       ||'.'||ERPE.cod_subfuncao                                         \n";
    $stSql .= "                       ||'.'||ERPE.cod_programa                                          \n";
    $stSql .= "                       ||'.'||ERPE.num_pao                                               \n";
    $stSql .= "                       ||'.'||ERPE.cod_estrutural                                        \n";
    $stSql .= "                       FROM empenho.restos_pre_empenho AS ERPE                           \n";
    $stSql .= "                       WHERE ERPE.exercicio       = tbl.exercicio_empenho                \n";
    $stSql .= "                         AND ERPE.cod_pre_empenho = tbl.cod_pre_empenho )                \n";
    $stSql .= "              END AS dotacao                                                             \n";
    $stSql .= "             ,CASE WHEN tbl.implantado = false                                           \n";
    $stSql .= "                 THEN OCD.descricao                                                      \n";
    $stSql .= "                 ELSE ''                                                                 \n";
    $stSql .= "              END as descricao                                                           \n";
    $stSql .= "             ,EPED.cod_despesa                                                           \n";
    $stSql .= "       FROM (                                                                            \n";
    $stSql .= "              SELECT EPE.cgm_beneficiario                                                \n";
    $stSql .= "                    ,CE.exercicio                                                        \n";
    $stSql .= "                    ,EE.exercicio as exercicio_empenho                                   \n";
    $stSql .= "                    ,CE.cod_entidade                                                     \n";
    $stSql .= "                    ,CE.cod_empenho                                                      \n";
    $stSql .= "                    ,EPE.cod_pre_empenho                                                 \n";
    $stSql .= "                    ,CE.cod_lote                                                         \n";
    $stSql .= "                    ,CE.tipo                                                             \n";
    $stSql .= "                    ,CE.sequencia                                                        \n";
    $stSql .= "                    ,EPE.implantado                                                      \n";
    $stSql .= "              FROM empenho.empenho             AS EE                                 \n";
    $stSql .= "                  ,empenho.pre_empenho         AS EPE                                \n";
    $stSql .= "                  ,contabilidade.empenhamento  AS CE                                 \n";
    $stSql .= "                -- Join com pre_empenho                                                  \n";
    $stSql .= "              WHERE EE.cod_pre_empenho   = EPE.cod_pre_empenho                           \n";
    $stSql .= "                AND EE.exercicio         = EPE.exercicio                                 \n";
    $stSql .= "                -- Join com contabilidade_empenhamento                                   \n";
    $stSql .= "                AND EE.exercicio         = CE.exercicio_empenho                          \n";
    $stSql .= "                AND EE.cod_entidade      = CE.cod_entidade                               \n";
    $stSql .= "                AND EE.cod_empenho       = CE.cod_empenho                                \n";
    $stSql .= "                -- Filtro                                                                \n";
    $stSql .= "                ".$this->getDado( "stExercicio" )."                                      \n";
    $stSql .= "                AND EPE.cgm_beneficiario = ".$this->getDado( "cgm_beneficiario" )."      \n";
    $stSql .= "                AND EE.cod_entidade     IN ( ".$this->getDado( "cod_entidade" )." )      \n";
    if ($this->getDado( "stExercicio" ) ) {
        $stSql .= "                AND EE.dt_empenho between TO_DATE( '".$this->getDado("dt_inicial")."', 'dd/mm/yyyy' )   \n";
        $stSql .= "                                      AND TO_DATE( '".$this->getDado("dt_final")."'  , 'dd/mm/yyyy' )   \n";
    }
    $stSql .= "                                                                                         \n";
    $stSql .= "              UNION                                                                      \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "              SELECT EPE.cgm_beneficiario                                                \n";
    $stSql .= "                    ,CL.exercicio                                                        \n";
    $stSql .= "                    ,EE.exercicio as exercicio_empenho                                   \n";
    $stSql .= "                    ,CL.cod_entidade                                                     \n";
    $stSql .= "                    ,ENL.cod_empenho                                                     \n";
    $stSql .= "                    ,EPE.cod_pre_empenho                                                 \n";
    $stSql .= "                    ,CL.cod_lote                                                         \n";
    $stSql .= "                    ,CL.tipo                                                             \n";
    $stSql .= "                    ,CL.sequencia                                                        \n";
    $stSql .= "                    ,EPE.implantado                                                      \n";
    $stSql .= "              FROM empenho.pre_empenho        AS EPE                                 \n";
    $stSql .= "                  ,empenho.empenho            AS EE                                  \n";
    $stSql .= "                  ,empenho.nota_liquidacao    AS ENL                                 \n";
    $stSql .= "                  ,contabilidade.liquidacao   AS CL                                  \n";
    $stSql .= "              WHERE EPE.cod_pre_empenho = EE.cod_pre_empenho                             \n";
    $stSql .= "                AND EPE.exercicio       = EE.exercicio                                   \n";
    $stSql .= "                -- Join com liquidacao                                                   \n";
    $stSql .= "                AND EE.exercicio        = ENL.exercicio_empenho                          \n";
    $stSql .= "                AND EE.cod_entidade     = ENL.cod_entidade                               \n";
    $stSql .= "                AND EE.cod_empenho      = ENL.cod_empenho                                \n";
    $stSql .= "                -- Join com contabilidade_liquidacao                                     \n";
    $stSql .= "                AND ENL.exercicio       = CL.exercicio_liquidacao                        \n";
    $stSql .= "                AND ENL.cod_entidade    = CL.cod_entidade                                \n";
    $stSql .= "                AND ENL.cod_nota        = CL.cod_nota                                    \n";
    $stSql .= "                -- Filtro                                                                \n";
    $stSql .= "                ".$this->getDado( "stExercicio" )                         ."           \n";
    $stSql .= "                AND EPE.cgm_beneficiario = ".$this->getDado( "cgm_beneficiario" )."      \n";
    $stSql .= "                AND EE.cod_entidade     IN ( ".$this->getDado( "cod_entidade" )." )      \n";
    $stSql .= "                AND ENL.dt_liquidacao between TO_DATE( '".$this->getDado("dt_inicial")."', 'dd/mm/yyyy' )   \n";
    $stSql .= "                                          AND TO_DATE( '".$this->getDado("dt_final")."'  , 'dd/mm/yyyy' )   \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "              UNION                                                                      \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "              SELECT EPE.cgm_beneficiario                                                \n";
    $stSql .= "                    ,CP.exercicio                                                        \n";
    $stSql .= "                    ,EE.exercicio as exercicio_empenho                                   \n";
    $stSql .= "                    ,CP.cod_entidade                                                     \n";
    $stSql .= "                    ,ENL.cod_empenho                                                     \n";
    $stSql .= "                    ,EPE.cod_pre_empenho                                                 \n";
    $stSql .= "                    ,CP.cod_lote                                                         \n";
    $stSql .= "                    ,CP.tipo                                                             \n";
    $stSql .= "                    ,CP.sequencia                                                        \n";
    $stSql .= "                    ,EPE.implantado                                                      \n";
    $stSql .= "              FROM empenho.pre_empenho          AS EPE                               \n";
    $stSql .= "                  ,empenho.empenho              AS EE                                \n";
    $stSql .= "                  ,empenho.nota_liquidacao      AS ENL                               \n";
    $stSql .= "                  ,empenho.nota_liquidacao_paga AS ENLP                              \n";
    $stSql .= "                  ,contabilidade.pagamento      AS CP                                \n";
    $stSql .= "              WHERE EPE.cod_pre_empenho = EE.cod_pre_empenho                             \n";
    $stSql .= "                AND EPE.exercicio       = EE.exercicio                                   \n";
    $stSql .= "                -- Join com liquidacao                                                   \n";
    $stSql .= "                AND EE.exercicio      = ENL.exercicio_empenho                            \n";
    $stSql .= "                AND EE.cod_entidade   = ENL.cod_entidade                                 \n";
    $stSql .= "                AND EE.cod_empenho    = ENL.cod_empenho                                  \n";
    $stSql .= "                -- Join com nota_liquidacao_paga                                         \n";
    $stSql .= "                AND ENL.exercicio     = ENLP.exercicio                                   \n";
    $stSql .= "                AND ENL.cod_entidade  = ENLP.cod_entidade                                \n";
    $stSql .= "                AND ENL.cod_nota      = ENLP.cod_nota                                    \n";
    $stSql .= "                -- Join com contabilidade_pagamento                                      \n";
    $stSql .= "                AND ENLP.exercicio    = CP.exercicio_liquidacao                          \n";
    $stSql .= "                AND ENLP.cod_entidade = CP.cod_entidade                                  \n";
    $stSql .= "                AND ENLP.cod_nota     = CP.cod_nota                                      \n";
    $stSql .= "                AND ENLP.timestamp    = CP.timestamp                                     \n";
    $stSql .= "                -- Filtro                                                                \n";
    $stSql .= "                ".$this->getDado( "stExercicio" )                         ."           \n";
    $stSql .= "                AND EPE.cgm_beneficiario = ".$this->getDado( "cgm_beneficiario" )."      \n";
    $stSql .= "                AND EE.cod_entidade     IN ( ".$this->getDado( "cod_entidade" )." )      \n";
    $stSql .= "                AND TO_DATE(ENLP.timestamp, 'yyyy-mm-dd' )                               \n";
    $stSql .= "                          between TO_DATE( '".$this->getDado("dt_inicial")."', 'dd/mm/yyyy' )   \n";
    $stSql .= "                              AND TO_DATE( '".$this->getDado("dt_final")."'  , 'dd/mm/yyyy' )   \n";
    $stSql .= "                                                                                         \n";
    $stSql .= "              ORDER BY cgm_beneficiario                                                  \n";
    $stSql .= "                      ,cod_entidade                                                      \n";
    $stSql .= "                      ,exercicio                                                         \n";
    $stSql .= "                      ,cod_empenho                                                       \n";
    $stSql .= "                      ,cod_lote                                                          \n";
    $stSql .= "                      ,sequencia                                                         \n";
    $stSql .= "           )                                 AS tbl                                      \n";
    $stSql .= "           ,contabilidade.lancamento_empenho AS CLE                                  \n";
    $stSql .= "           ,contabilidade.lancamento         AS CL                                   \n";
    $stSql .= "           ,contabilidade.historico_contabil AS CHC                                  \n";
    $stSql .= "           ,contabilidade.valor_lancamento   AS CVL                                  \n";
    $stSql .= "           ,contabilidade.lote               AS CLO                                  \n";
    $stSql .= "           ,empenho.pre_empenho_despesa      AS EPED                                 \n";
    $stSql .= "           ,orcamento.conta_despesa          AS OCD                                  \n";
    $stSql .= "           ,orcamento.despesa                AS OD                                   \n";
    $stSql .= "            JOIN orcamento.recurso as REC                                    \n";
    $stSql .= "            ON ( od.cod_recurso = rec.cod_recurso                                    \n";
    $stSql .= "             AND od.exercicio = rec.exercicio )                                      \n";
    $stSql .= "           ,sw_cgm                           AS CGM                                  \n";
    $stSql .= "         -- Join com lancamento_empenho                                                  \n";
    $stSql .= "       WHERE tbl.exercicio    = CLE.exercicio                                            \n";
    $stSql .= "         AND tbl.cod_entidade = CLE.cod_entidade                                         \n";
    $stSql .= "         AND tbl.tipo         = CLE.tipo                                                 \n";
    $stSql .= "         AND tbl.cod_lote     = CLE.cod_lote                                             \n";
    $stSql .= "         AND tbl.sequencia    = CLE.sequencia                                            \n";
    $stSql .= "         -- Join com lancamento                                                          \n";
    $stSql .= "         AND CLE.exercicio    = CL.exercicio                                             \n";
    $stSql .= "         AND CLE.cod_entidade = CL.cod_entidade                                          \n";
    $stSql .= "         AND CLE.tipo         = CL.tipo                                                  \n";
    $stSql .= "         AND CLE.cod_lote     = CL.cod_lote                                              \n";
    $stSql .= "         -- Join com historico_contabil                                                  \n";
    $stSql .= "         AND CL.cod_historico = CHC.cod_historico                                        \n";
    $stSql .= "         AND CL.exercicio     = CHC.exercicio                                            \n";
    $stSql .= "         -- Join com valor_lancamento                                                    \n";
    $stSql .= "         AND CL.exercicio     = CVL.exercicio                                            \n";
    $stSql .= "         AND CL.cod_entidade  = CVL.cod_entidade                                         \n";
    $stSql .= "         AND CL.tipo          = CVL.tipo                                                 \n";
    $stSql .= "         AND CL.cod_lote      = CVL.cod_lote                                             \n";
    $stSql .= "         AND CL.sequencia     = CVL.sequencia                                            \n";
    $stSql .= "         -- Join com lote                                                                \n";
    $stSql .= "         AND CL.exercicio     = CLO.exercicio                                            \n";
    $stSql .= "         AND CL.cod_entidade  = CLO.cod_entidade                                         \n";
    $stSql .= "         AND CL.cod_lote      = CLO.cod_lote                                             \n";
    $stSql .= "         AND CL.tipo          = CLO.tipo                                                 \n";
    $stSql .= "         -- Join com empenho.pre_empenho_despesa                                         \n";
    $stSql .= "         AND tbl.exercicio       = EPED.exercicio                                        \n";
    $stSql .= "         AND tbl.cod_pre_empenho = EPED.cod_pre_empenho                                  \n";
    $stSql .= "         -- Join com orcamento.conta_despesa                                             \n";
    $stSql .= "         AND EPED.cod_conta      = OCD.cod_conta                                         \n";
    $stSql .= "         AND EPED.exercicio      = OCD.exercicio                                         \n";
    $stSql .= "         -- Join com orcamento_despesa                                                   \n";
    $stSql .= "         AND EPED.cod_despesa    = OD.cod_despesa                                        \n";
    $stSql .= "         AND EPED.exercicio      = OD.exercicio                                          \n";
    $stSql .= "         -- Join com CGM                                                                 \n";
    $stSql .= "         AND tbl.cgm_beneficiario = CGM.numcgm                                           \n";
    $stSql .= "         -- Filtro                                                                       \n";
    $stSql .= "         AND CVL.tipo_valor      = 'D'                                                   \n";
    $stSql .= "         AND CVL.cod_entidade   IN ( ".$this->getDado( "cod_entidade" )." )              \n";
    $stSql .= "         AND CLO.dt_lote between TO_DATE( '".$this->getDado("dt_inicial")."', 'dd/mm/yyyy' ) \n";
    $stSql .= "                             AND TO_DATE( '".$this->getDado("dt_final")."'  , 'dd/mm/yyyy' ) \n";
    $stSql .= "       ".$this->getDado('stFiltro');
    $stSql .= "       ORDER BY tbl.cgm_beneficiario                                                     \n";
    $stSql .= "               ,tbl.cod_entidade                                                         \n";
    $stSql .= "               ,tbl.exercicio                                                            \n";
    $stSql .= "               ,tbl.cod_empenho                                                          \n";
    $stSql .= "               ,tbl.cod_lote                                                             \n";
    $stSql .= "               ,CVL.sequencia                                                            \n";
    $stSql .= ") AS tbl                                                                                 \n";
    $stSql .= "INNER JOIN (  SELECT CPC.exercicio                                                       \n";
    $stSql .= "                    ,CPA.cod_plano                                                       \n";
    $stSql .= "                    ,CPC.cod_estrutural                                                  \n";
    $stSql .= "              FROM contabilidade.plano_conta     AS CPC                              \n";
    $stSql .= "                  ,contabilidade.plano_analitica AS CPA                              \n";
    $stSql .= "              WHERE CPA.cod_conta = CPC.cod_conta                                        \n";
    $stSql .= "                AND CPA.exercicio = CPC.exercicio                                        \n";
    $stSql .= ") AS conta_debito ON( tbl.cod_plano_debito = conta_debito.cod_plano                      \n";
    $stSql .= "                  AND tbl.exercicio        = conta_debito.exercicio )                    \n";
    $stSql .= "INNER JOIN (  SELECT CPC.exercicio                                                       \n";
    $stSql .= "                    ,CPA.cod_plano                                                       \n";
    $stSql .= "                    ,CPC.cod_estrutural                                                  \n";
    $stSql .= "              FROM contabilidade.plano_conta     AS CPC                              \n";
    $stSql .= "                  ,contabilidade.plano_analitica AS CPA                              \n";
    $stSql .= "              WHERE CPA.cod_conta = CPC.cod_conta                                        \n";
    $stSql .= "                AND CPA.exercicio = CPC.exercicio                                        \n";
    $stSql .= ") AS conta_credito ON( tbl.cod_plano_credito = conta_credito.cod_plano                   \n";
    $stSql .= "                  AND tbl.exercicio          = conta_credito.exercicio )                 \n";

return $stSql;
}

}
