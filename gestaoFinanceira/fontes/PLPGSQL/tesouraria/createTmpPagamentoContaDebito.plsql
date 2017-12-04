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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 27033 $
* $Name$
* $Author: cako $
* $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $
*
* Casos de uso: uc-02.04.14
*/

/*
$Log$
Revision 1.9  2006/10/09 09:22:49  cleisson
Ajustes

Revision 1.8  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_pagamento_conta_debito(VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,BIGINT,BIGINT,BIGINT,BIGINT,BIGINT) RETURNS BOOLEAN AS '

DECLARE

    stEntidade              ALIAS FOR $1;
    stExercicio             ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stTipoRelatorio         ALIAS FOR $5;
    inDespesaInicial        ALIAS FOR $6;
    inDespesaFinal          ALIAS FOR $7;
    inContaBancoInicial     ALIAS FOR $8;
    inContaBancoFinal       ALIAS FOR $9;
    inRecurso               ALIAS FOR $10;

    stSql                   VARCHAR   := '''';
    stAux                   VARCHAR   := '''';
    stContaBanco            VARCHAR   := '''';
    stDespesa               VARCHAR   := '''';
    stRecurso               VARCHAR   := '''';
   
    stCampos                VARCHAR   := '''';
    stCampos2               VARCHAR   := '''';
    stTabelas               VARCHAR   := '''';
    stFiltros               VARCHAR   := '''';

    stCampoNumCgm           VARCHAR   := '''';
    stTabelaNumCgm          VARCHAR   := '''';
    stFiltroNumCgm          VARCHAR   := '''';
    
    reRegistro              RECORD;

BEGIN

IF (stTipoRelatorio = ''B'') THEN

    stCampos  := '' ,cast(CPA.cod_plano || '''' - '''' || CPC.nom_conta AS varchar)   AS conta_banco '';
    stCampos2 := '' ,conta_banco '';
    stTabelas := '''';
    stFiltros := '''';

ELSIF (stTipoRelatorio = ''R'') THEN

    stCampos  := '' ,cast(ENL.cod_recurso || '''' - '''' || ENL.nom_recurso AS varchar) AS recurso '';
    stCampos2 := '' ,recurso '';

ELSIF (stTipoRelatorio = ''E'') THEN

    stCampos       := '' ,cast(sw_cgm.numcgm || '''' - '''' || sw_cgm.nom_cgm  AS varchar) AS entidade '';
    stCampos2      := '' ,entidade '';
    stTabelas      := '' ,sw_cgm                                        '';
    stFiltros      := '' AND sw_cgm.numcgm = ENLP.numcgm                '';

    stCampoNumCgm  := '' ,OE.numcgm                                     '';
    stTabelaNumCgm := '' ,orcamento.entidade                  AS OE     '';
    stFiltroNumCgm := ''
            AND TP.cod_entidade            = OE.cod_entidade
            AND TP.exercicio               = OE.exercicio
    '';
ELSE
    stCampos  := '' ,cast('''''''' AS varchar) AS tipo_despesa '';
    stCampos2 := '' ,tipo_despesa '';
END IF;

IF (stDtInicial = stDtFinal ) THEN
    stAux := '' AND TO_DATE(TO_CHAR(TP.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
ELSE
    stAux := '' AND TO_DATE(TO_CHAR(TP.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';

END IF;

IF ((inContaBancoInicial <> 0) OR (inContaBancoFinal <> 0)) THEN

    IF (inContaBancoInicial = inContaBancoFinal) THEN
        stContaBanco = '' AND  CPA.cod_plano = '' || inContaBancoInicial || '' '';
    ELSE
        stContaBanco = '' AND  CPA.cod_plano BETWEEN '' || inContaBancoInicial || '' AND '' || inContaBancoFinal || '' '';
    END IF;
END IF;

IF ((inDespesaInicial = 0) OR (inDespesaFinal = 0)) THEN
                                        
    IF (inDespesaInicial = inDespesaFinal) THEN
        stDespesa = '' AND  OD.cod_despesa = '' || inDespesaInicial || '' '';
    ELSE
        stDespesa = '' AND  OD.cod_despesa BETWEEN '' || inDespesaInicial || '' AND '' || inDespesaFinal || '' '';
    END IF;
END IF;


IF (inRecurso > 0) THEN
    stRecurso = '' AND  ORE.cod_recurso = '' || inRecurso || '' '';
END IF;

stSql := ''
    CREATE TEMPORARY TABLE tmp_nota_liquidacao_paga AS

        SELECT
             ENLP.cod_nota
            ,ENLP.cod_entidade
            ,ENLP.exercicio
            ,ENLP.timestamp
            ,CASE WHEN ENLP.vl_pago = TPE.vl_anulado
                THEN true
                ELSE false
            END AS estornado
            '' || stCampoNumCgm || ''

        FROM
            tesouraria.pagamento                           AS TP
            LEFT OUTER JOIN (
                SELECT
                    tpe.cod_nota,
                    tpe.cod_entidade,
                    tpe.exercicio,
                    tpe.timestamp,
                    coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado
                FROM
                    tesouraria.pagamento_estornado AS TPE,
                    empenho.nota_liquidacao_paga_anulada as nlpa
                WHERE
                    tpe.cod_nota    = nlpa.cod_nota     AND
                    tpe.exercicio   = nlpa.exercicio    AND
                    tpe.cod_entidade= nlpa.cod_entidade AND
                    tpe.timestamp_anulado   = nlpa.timestamp_anulada
                GROUP BY
                    tpe.cod_nota,
                    tpe.cod_entidade,
                    tpe.exercicio,
                    tpe.timestamp
            ) AS TPE
            on(
                    TP.cod_nota              =  TPE.cod_nota
                AND TP.cod_entidade          =  TPE.cod_entidade
                AND TP.exercicio             =  TPE.exercicio
                AND TP.timestamp             =  TPE.timestamp
            )
--            ,empenho.ordem_pagamento                       AS EOP
--            ,empenho.pagamento_liquidacao                  AS EPL
--            ,empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP
              ,empenho.nota_liquidacao_paga                  AS ENLP
            '' || stTabelaNumCgm || ''

        WHERE
--                TP.cod_ordem                = EOP.cod_ordem
--            AND TP.exercicio                = EOP.exercicio
--            AND TP.cod_entidade             = EOP.cod_entidade

                TP.cod_entidade             IN ('' || stEntidade || '')
            AND TP.exercicio                = ''''''|| stExercicio ||''''''

            '' || stAux || ''

--            AND EOP.cod_ordem               = EPL.cod_ordem
--            AND EOP.exercicio               = EPL.exercicio
--            AND EOP.cod_entidade            = EPL.cod_entidade
--
--            AND EPL.cod_ordem               = EPLNLP.cod_ordem
--            AND EPL.exercicio               = EPLNLP.exercicio
--            AND EPL.cod_entidade            = EPLNLP.cod_entidade
--            AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
--            AND EPL.cod_nota                = EPLNLP.cod_nota

            AND TP.exercicio            = ENLP.exercicio
            AND TP.cod_nota             = ENLP.cod_nota
            AND TP.cod_entidade         = ENLP.cod_entidade
            AND TP.timestamp            = ENLP.timestamp

            '' || stFiltroNumCgm  || ''
'';


    EXECUTE stSql;


stSql := ''
    CREATE TEMPORARY TABLE tmp_nota_liquidacao AS
 
        SELECT                       
             ENLP.exercicio
            ,ENLP.cod_entidade
            ,ENLP.cod_nota
            ,OCD.cod_conta
            ,OCD.descricao
            ,ORE.cod_recurso
            ,ORE.nom_recurso
        FROM 

             tmp_nota_liquidacao_paga             AS ENLP
             ,empenho.nota_liquidacao             AS ENL
             ,empenho.empenho                     AS EE
             ,empenho.pre_empenho                 AS EPE
             ,empenho.pre_empenho_despesa         AS EPED
             ,orcamento.conta_despesa             as OCD
             ,orcamento.despesa                   AS OD
             ,orcamento.recurso(''''''|| stExercicio ||'''''') AS ORE
  
        WHERE
             
                 ENLP.exercicio             = ENL.exercicio
             AND ENLP.cod_entidade          = ENL.cod_entidade
             AND ENLP.cod_nota              = ENL.cod_nota

             AND ENL.exercicio_empenho      = EE.exercicio
             AND ENL.cod_entidade           = EE.cod_entidade
             AND ENL.cod_empenho            = EE.cod_empenho

             AND EE.exercicio               = EPE.exercicio
             AND EE.cod_pre_empenho         = EPE.cod_pre_empenho
 
             AND EPE.exercicio              = EPED.exercicio
             AND EPE.cod_pre_empenho        = EPED.cod_pre_empenho

             AND EPED.exercicio             = OCD.exercicio
             AND EPED.cod_conta             = OCD.cod_conta

             AND EPED.exercicio             = OD.exercicio
             AND EPED.cod_despesa           = OD.cod_despesa

             AND OD.exercicio               = ORE.exercicio
             AND OD.cod_recurso             = ORE.cod_recurso

            '' || stDespesa || ''

            '' || stRecurso || ''


             GROUP BY
                ENLP.exercicio
                ,ENLP.cod_entidade
                ,ENLP.cod_nota
                ,OCD.cod_conta
                ,OCD.descricao
                ,ORE.cod_recurso
                ,ORE.nom_recurso
'';


    EXECUTE stSql;


stSql := '' CREATE TEMPORARY TABLE tmp_pagamento_conta_debito AS
    SELECT
            cast (ENL.cod_conta               AS integer) AS plano
            ,cast(ENL.descricao               AS varchar) AS conta
            ,cast(sum(abs(CVL.vl_lancamento)) AS numeric) AS pago
            ,CASE WHEN ENLP.estornado 
                THEN CVL.vl_lancamento*(-1)
                ELSE ''''0.00''''
            END AS estornado
            '' || stCampos || ''
    FROM
        tmp_nota_liquidacao_paga             AS ENLP
        ,tmp_nota_liquidacao                 AS ENL
        ,contabilidade.pagamento             AS CP
        ,contabilidade.lancamento_empenho    AS CLE
        ,contabilidade.lancamento            AS CL
        ,contabilidade.valor_lancamento      AS CVL
        ,contabilidade.conta_debito          AS CCD
        ,contabilidade.plano_analitica       AS CPA
        ,contabilidade.plano_banco           AS CPB
        ,contabilidade.plano_conta           AS CPC
        '' || stTabelas || ''

    WHERE

            ENLP.exercicio              = CP.exercicio_liquidacao
        AND ENLP.cod_entidade           = CP.cod_entidade
        AND ENLP.cod_nota               = CP.cod_nota
        AND ENLP.timestamp              = CP.timestamp

        AND ENLP.exercicio              = ENL.exercicio
        AND ENLP.cod_entidade           = ENL.cod_entidade
        AND ENLP.cod_nota               = ENL.cod_nota

        AND CP.exercicio                = CLE.exercicio
        AND CP.cod_lote                 = CLE.cod_lote
        AND CP.tipo                     = CLE.tipo
        AND CP.sequencia                = CLE.sequencia
        AND CP.cod_entidade             = CLE.cod_entidade

        AND CLE.exercicio               = CL.exercicio
        AND CLE.cod_lote                = CL.cod_lote
        AND CLE.tipo                    = CL.tipo
        AND CLE.sequencia               = CL.sequencia
        AND CLE.cod_entidade            = CL.cod_entidade
        AND CLE.tipo                    = ''''P''''

        AND CL.exercicio                = CVL.exercicio
        AND CL.cod_lote                 = CVL.cod_lote
        AND CL.tipo                     = CVL.tipo
        AND CL.sequencia                = CVL.sequencia
        AND CL.cod_entidade             = CVL.cod_entidade

        AND CVL.exercicio               = CCD.exercicio
        AND CVL.cod_lote                = CCD.cod_lote
        AND CVL.tipo                    = CCD.tipo
        AND CVL.sequencia               = CCD.sequencia
        AND CVL.cod_entidade            = CCD.cod_entidade
        AND CVL.tipo_valor              = CCD.tipo_valor
        AND CVL.tipo_valor              = ''''D''''

        AND CCD.exercicio               = CPA.exercicio
        AND CCD.cod_plano               = CPA.cod_plano

        AND CPA.exercicio               = CPC.exercicio
        AND CPA.cod_conta               = CPC.cod_conta

        AND CPA.exercicio               = CPB.exercicio
        AND CPA.cod_plano               = CPB.cod_plano

        '' || stContaBanco || ''

        '' || stFiltros ||'' 

        GROUP BY
             ENL.cod_conta
            ,ENL.descricao
            ,CVL.vl_lancamento
            ,ENLP.estornado
            '' || stCampos2  ||''

'';


    EXECUTE stSql;

    DROP TABLE tmp_nota_liquidacao_paga;
    
    DROP TABLE tmp_nota_liquidacao;

    RETURN TRUE;

END;
' LANGUAGE 'plpgsql';

