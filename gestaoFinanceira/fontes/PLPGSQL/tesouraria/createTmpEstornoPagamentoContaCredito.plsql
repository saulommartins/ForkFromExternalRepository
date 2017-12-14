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
Revision 1.7  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_estorno_pagamento_conta_credito(VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,BIGINT,BIGINT,BIGINT,BIGINT,BIGINT) RETURNS BOOLEAN AS '

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

    stCampos  := '' ,cast(AB.cod_banco || '''' - '''' || AB.nom_banco AS varchar)   AS banco '';
    stCampos2 := '' ,banco '';
    stTabelas := '' ,administracao.agencia AS AA
                    ,administracao.banco   AS AB
    '';
    stFiltros := '' AND CPB.cod_banco    = AA.cod_banco
                    AND AA.cod_banco     = AB.cod_banco
    '';

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
            AND EOP.cod_entidade            = OE.cod_entidade
            AND EOP.exercicio               = OE.exercicio
    '';
ELSE
    stCampos  := '' ,cast('''''''' AS varchar) AS tipo_despesa '';
    stCampos2 := '' ,tipo_despesa '';
END IF;

IF (stDtInicial = stDtFinal ) THEN
    stAux := '' AND TO_DATE(TO_CHAR(TP.timestamp_pagamento,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
ELSE
    stAux := '' AND TO_DATE(TO_CHAR(TP.timestamp_pagamento,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';

END IF;

IF ((inContaBancoInicial <> 0) OR (inContaBancoFinal <> 0)) THEN

    IF (inContaBancoInicial = inContaBancoFinal) THEN
        stContaBanco = '' AND  CPA.cod_plano = '' || inContaBancoInicial || '' '';
    ELSE
        stContaBanco = '' AND  CPA.cod_plano BETWEEN '' || inContaBancoInicial || '' AND '' || inContaBancoFinal || '' '';
    END IF;
END IF;

IF ((inDespesaInicial <> 0) OR (inDespesaFinal <> 0)) THEN

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
            '' || stCampoNumCgm || ''

        FROM
            tesouraria.pagamento                               AS TP
            ,tesouraria.pagamento_estornado                    AS TPE 
            ,empenho.ordem_pagamento                           AS EOP
            ,empenho.pagamento_liquidacao                      AS EPL
            ,empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP
            ,empenho.nota_liquidacao_paga                      AS ENLP
            '' || stTabelaNumCgm || ''

        WHERE

                TP.cod_ordem                =  TPE.cod_ordem
            AND TP.cod_entidade             =  TPE.cod_entidade
            AND TP.exercicio                =  TPE.exercicio
            AND TP.timestamp_pagamento      =  TPE.timestamp_pagamento

            AND TP.cod_ordem                = EOP.cod_ordem
            AND TP.exercicio                = EOP.exercicio
            AND TP.cod_entidade             = EOP.cod_entidade

            AND TP.cod_entidade             IN ('' || stEntidade || '')
            AND TP.exercicio                = ''''''|| stExercicio ||''''''

            '' || stAux || ''

            AND EOP.cod_ordem               = EPL.cod_ordem
            AND EOP.exercicio               = EPL.exercicio
            AND EOP.cod_entidade            = EPL.cod_entidade

            AND EPL.cod_ordem               = EPLNLP.cod_ordem
            AND EPL.exercicio               = EPLNLP.exercicio
            AND EPL.cod_entidade            = EPLNLP.cod_entidade
            AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
            AND EPL.cod_nota                = EPLNLP.cod_nota

            AND EPLNLP.exercicio_liquidacao = ENLP.exercicio
            AND EPLNLP.cod_nota             = ENLP.cod_nota
            AND EPLNLP.cod_entidade         = ENLP.cod_entidade
            AND EPLNLP.timestamp            = ENLP.timestamp

            '' || stFiltroNumCgm  || ''
'';


    EXECUTE stSql;



stSql := ''
    CREATE TEMPORARY TABLE tmp_nota_liquidacao AS
 
        SELECT                       
             ENLP.exercicio
            ,ENLP.cod_entidade
            ,ENLP.cod_nota
            ,ORE.cod_recurso
            ,ORE.nom_recurso
        FROM 

             tmp_nota_liquidacao_paga             AS ENLP
             ,empenho.nota_liquidacao             AS ENL
             ,empenho.empenho                     AS EE
             ,empenho.pre_empenho                 AS EPE
             ,empenho.pre_empenho_despesa         AS EPED
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
                ,ORE.cod_recurso
                ,ORE.nom_recurso
'';


    EXECUTE stSql;


stSql := '' CREATE TEMPORARY TABLE tmp_estorno_pagamento_conta_credito AS
    SELECT

            cast (CPA.cod_plano               AS integer) AS plano
            ,cast(CPC.nom_conta               AS varchar) AS conta
            ,cast(''''0.00''''                AS numeric) AS pago
            ,cast(sum(abs(CVL.vl_lancamento)) AS numeric) AS estornado
            '' || stCampos || ''
    FROM
        tmp_nota_liquidacao_paga             AS ENLP
        ,tmp_nota_liquidacao                 AS ENL
        ,contabilidade.pagamento             AS CP
        ,contabilidade.lancamento_empenho    AS CLE
        ,contabilidade.lancamento            AS CL
        ,contabilidade.valor_lancamento      AS CVL
        ,contabilidade.conta_credito         AS CCC
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

        AND CVL.exercicio               = CCC.exercicio
        AND CVL.cod_lote                = CCC.cod_lote
        AND CVL.tipo                    = CCC.tipo
        AND CVL.sequencia               = CCC.sequencia
        AND CVL.cod_entidade            = CCC.cod_entidade
        AND CVL.tipo_valor              = CCC.tipo_valor
        AND CVL.tipo_valor              = ''''C''''

        AND CCC.exercicio               = CPA.exercicio
        AND CCC.cod_plano               = CPA.cod_plano

        AND CPA.exercicio               = CPC.exercicio
        AND CPA.cod_conta               = CPC.cod_conta

        AND CPA.exercicio               = CPB.exercicio
        AND CPA.cod_plano               = CPB.cod_plano

        '' || stContaBanco || ''

        '' || stFiltros ||'' 

        GROUP BY
            CPA.cod_plano
            ,CPC.nom_conta
            ,CVL.vl_lancamento
            '' || stCampos2  ||''

'';


    EXECUTE stSql;

    DROP TABLE tmp_nota_liquidacao_paga;
    
    DROP TABLE tmp_nota_liquidacao;

    RETURN TRUE;

END;
' LANGUAGE 'plpgsql';

