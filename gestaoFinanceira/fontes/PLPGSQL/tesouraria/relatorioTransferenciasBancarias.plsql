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
* $Revision: 25500 $
* $Name$
* $Author: cako $
* $Date: 2007-09-17 10:39:09 -0300 (Seg, 17 Set 2007) $
*
* Casos de uso: uc-02.04.16
*/

CREATE OR REPLACE FUNCTION tesouraria.fn_relatorio_transferencias_bancarias(VARCHAR, VARCHAR, VARCHAR, VARCHAR, INTEGER, INTEGER, INTEGER, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio             ALIAS FOR $1;
    stEntidade              ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    inContaBancoInicial     ALIAS FOR $5;
    inContaBancoFinal       ALIAS FOR $6;
    inCodTipoTransferencia  ALIAS FOR $7;
    boUtilizaEstruturalTCE  ALIAS FOR $8;

    stSql                   VARCHAR   := '';
    stAux                   VARCHAR   := '';
    stContaBanco            VARCHAR   := '';
    reRegistro              RECORD;

BEGIN

    IF (stDtInicial = stDtFinal ) THEN
        stAux := ' TO_DATE(TO_CHAR(TT.timestamp_transferencia, ''dd/mm/yyyy''), ''dd/mm/yyyy'') = to_date('|| quote_literal(stDtInicial) ||', ''dd/mm/yyyy'') ';
    ELSE
        stAux := ' TO_DATE(TO_CHAR(TT.timestamp_transferencia, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN to_date('|| quote_literal(stDtInicial) ||', ''dd/mm/yyyy'') AND to_date('|| quote_literal(stDtFinal) ||', ''dd/mm/yyyy'') ';
    END IF;
    
    IF (inCodTipoTransferencia > 0) THEN
        stAux := stAux || ' AND TT.cod_tipo = '||inCodTipoTransferencia;
    END IF;
    
    IF ((inContaBancoInicial = 0) AND (inContaBancoFinal = 0)) THEN
        stContaBanco = '';
    ELSE
        IF (inContaBancoInicial = inContaBancoFinal) THEN
            stContaBanco = ' AND ( CPAD.cod_plano = '||inContaBancoInicial||' OR CPAC.cod_plano = '||inContaBancoInicial||'  ) ';
        ELSE
            stContaBanco = ' AND ( CPAD.cod_plano BETWEEN '||inContaBancoInicial||' AND '||inContaBancoFinal||' OR CPAC.cod_plano BETWEEN '||inContaBancoInicial||' AND '||inContaBancoFinal||' ) ';
        END IF;
    END IF;
    
    stSql := '
        SELECT CAST(TO_CHAR( CL.dt_lote, ''dd/mm/yyyy'' )           AS varchar) AS dt_lote
             , CAST(CL.cod_lote || ''/'' || CL.exercicio        AS varchar) AS lote  
             , CAST(CPAC.cod_plano || '' - '' || CPCC.nom_conta AS varchar) AS conta_credito
             , CAST(CPAD.cod_plano || '' - '' || CPCD.nom_conta AS varchar) AS conta_debito
             , CAST(ABS(CVL.vl_lancamento)                          AS numeric) AS vl_lancamento
             , TT.cod_tipo
          FROM tesouraria.transferencia       AS TT
             , contabilidade.valor_lancamento AS CVL
             , contabilidade.lote             AS CL
             , contabilidade.plano_analitica  AS CPAC
             , contabilidade.plano_conta      AS CPCC
             , contabilidade.plano_analitica  AS CPAD
             , contabilidade.plano_conta      AS CPCD
         WHERE 
            -- Join com valor_lancamento
               TT.exercicio    = CL.exercicio
           AND TT.cod_entidade = CL.cod_entidade
           AND TT.tipo         = CL.tipo
           AND TT.cod_lote     = CL.cod_lote
           
           AND TT.exercicio    = '|| quote_literal(stExercicio) ||'
           AND TT.cod_entidade IN ('|| stEntidade ||')
       
           AND CL.exercicio    = CVL.exercicio
           AND CL.cod_entidade = CVL.cod_entidade
           AND CL.tipo         = CVL.tipo
           AND CL.cod_lote     = CVL.cod_lote
           
           AND CVL.tipo_valor  = ''D''
    
           AND TT.cod_tipo <> 1
           AND TT.cod_tipo <> 2
       
           AND CPAC.exercicio = CVL.exercicio
           AND CPAC.cod_plano = ( SELECT contabilidade.fn_recupera_conta_lancamento( CVL.exercicio
                                                                                   , CVL.cod_entidade
                                                                                   , CVL.cod_lote
                                                                                   , CVL.tipo
                                                                                   , CVL.sequencia
                                                                                   , ''C''
                                                                                   ) )
            AND CPCC.exercicio = CPAC.exercicio
            AND CPCC.cod_conta = CPAC.cod_conta
        
            AND CPAD.exercicio = CVL.exercicio
            AND CPAD.cod_plano = ( SELECT contabilidade.fn_recupera_conta_lancamento( CVL.exercicio
                                                                                    , CVL.cod_entidade
                                                                                    , CVL.cod_lote
                                                                                    , CVL.tipo
                                                                                    , CVL.sequencia
                                                                                    , ''D'' ) )
            AND CPCD.exercicio = CPAD.exercicio
            AND CPCD.cod_conta = CPAD.cod_conta
                '||stContaBanco||'
            AND '||stAux||' ';

    IF boUtilizaEstruturalTCE = 'true' THEN
        stSql := stSql || ' AND CPCC.cod_estrutural LIKE ''1.1.1.%'' AND CPCD.cod_estrutural LIKE ''1.1.1.%'' ';
    END IF;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
