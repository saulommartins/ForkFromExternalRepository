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
--
--*
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/12/28 10:50:00 $
--
-- Caso de uso: uc-04.05.45
-- Caso de uso: uc-04.05.48
--
-- Objetivo: verificar todos os vales transporte indicados ao contrato,
--  seja individualmente ou por grupo, avaliando seu valor unitario e 
-- fazendo o somatorio total de forma a gerar o custo total de vales para o contrato
-- Este valor sera tratado pelo percentual definido para descontos e a base do vale 
-- transporte para que seja estipulado p real valor de desconto de vale tranporte 
-- para o contrato. 
-- Como ainda nao ha relacao entre period de leitura e periodo de desconto do vale 
-- sera tratado ate la como data final da competencia limitando o uso do vale ao mes 
-- de competencia tanto para leitura dos dias como para o lançamento do desconto. 
--/
--


CREATE OR REPLACE FUNCTION pega0valortotaldevalestransporteporcontratonadata(integer, character varying)
  RETURNS numeric AS
$BODY$

DECLARE
    inCodContrato             ALIAS FOR $1;
    stTimestamp               ALIAS FOR $2;

    dtTimestamp               Date ;

    inQtdTotalVales           INTEGER := 0;
    inQtdValesAvulsos         INTEGER := 0;
    inQtdValesGrupo           INTEGER := 0;
    inMes                     INTEGER := 1;
    
    nuValorTotalVale          NUMERIC := 0.00;
    nuValorAvulsos            NUMERIC := 0.00;
    nuValorGrupo              NUMERIC := 0.00;

    stSql                     VARCHAR;
    stSql1                    VARCHAR;
    stSql2                    VARCHAR;
    stSql3                    VARCHAR;
    stAno                     VARCHAR;
    
    reRegistro                RECORD;
    reRegistro1               RECORD;
    reRegistro2               RECORD;
    reRegistro3               RECORD;

stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
 BEGIN


    dtTimestamp := to_date(substr( stTimestamp ,1,10),'yyyy-mm-dd');

    inMes := substr(stTimestamp,6,2)::INTEGER;
    stAno := substr(stTimestamp,1,4)::VARCHAR;
   
/*
    -- leitura registro especifico de vale transporte
*/
    stSql := ' SELECT 
        beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.cod_contrato
       ,beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.cod_mes
       ,beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.cod_concessao
       ,beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.exercicio  
       ,beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.vigencia

       ,beneficio'||stEntidade||'.concessao_vale_transporte.cod_vale_transporte as cod_vale_transporte
       ,beneficio'||stEntidade||'.concessao_vale_transporte.cod_tipo
       ,beneficio'||stEntidade||'.concessao_vale_transporte.quantidade as quantidade

       FROM beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte 

       LEFT OUTER JOIN beneficio'||stEntidade||'.concessao_vale_transporte 
         ON beneficio'||stEntidade||'.concessao_vale_transporte.cod_concessao 
            = beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.cod_concessao
        AND beneficio'||stEntidade||'.concessao_vale_transporte.cod_mes = '||inMes||'
        AND beneficio'||stEntidade||'.concessao_vale_transporte.exercicio = '''||stAno||'''

       LEFT OUTER JOIN beneficio'||stEntidade||'.vale_transporte
         ON beneficio'||stEntidade||'.vale_transporte.cod_vale_transporte 
            = beneficio'||stEntidade||'.concessao_vale_transporte.cod_vale_transporte

      WHERE beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.cod_contrato = '||inCodContrato||'
        AND beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.cod_mes = '||inMes||'
        AND beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.exercicio = '''||stAno||'''
        AND beneficio'||stEntidade||'.contrato_servidor_concessao_vale_transporte.vigencia <= '''||dtTimestamp||''' 
      ';

-- verificar um distinct para o codigo do vale - cod_vale_transporte

    FOR reRegistro IN  EXECUTE stSql LOOP

       stSql1 := '
           SELECT 
           beneficio'||stEntidade||'.custo.inicio_vigencia
           ,beneficio'||stEntidade||'.custo.vale_transporte_cod_vale_transporte as cod_vale_transporte
           ,beneficio'||stEntidade||'.custo.valor as valor
           FROM beneficio'||stEntidade||'.custo
           WHERE beneficio'||stEntidade||'.custo.vale_transporte_cod_vale_transporte = '||reRegistro.cod_vale_transporte||'
             AND beneficio'||stEntidade||'.custo.inicio_vigencia <= '''||dtTimestamp||'''
             ORDER BY beneficio'||stEntidade||'.custo.inicio_vigencia desc
             LIMIT 1

             ';     

        FOR reRegistro1 IN  EXECUTE stSql1 LOOP

           IF reRegistro1.cod_vale_transporte is not null THEN
              inQtdValesAvulsos := inQtdValesAvulsos + reRegistro.quantidade;
              nuValorAvulsos := nuValorAvulsos + ((reRegistro.quantidade) * (reRegistro1.valor)) ;
           END IF;

        END LOOP;

    END LOOP;

/*
    -- leitura de grupo
*/

    stSql2 := ' SELECT 
      beneficio'||stEntidade||'.contrato_servidor_grupo_concessao_vale_transporte.cod_contrato
     ,beneficio'||stEntidade||'.contrato_servidor_grupo_concessao_vale_transporte.cod_grupo

     ,beneficio'||stEntidade||'.grupo_concessao_vale_transporte.cod_concessao

     ,beneficio'||stEntidade||'.concessao_vale_transporte.cod_vale_transporte
     ,beneficio'||stEntidade||'.concessao_vale_transporte.cod_tipo
     ,beneficio'||stEntidade||'.concessao_vale_transporte.quantidade

     FROM beneficio'||stEntidade||'.contrato_servidor_grupo_concessao_vale_transporte

      LEFT OUTER JOIN beneficio'||stEntidade||'.grupo_concessao_vale_transporte 
        ON beneficio'||stEntidade||'.grupo_concessao_vale_transporte.cod_grupo 
           = beneficio'||stEntidade||'.contrato_servidor_grupo_concessao_vale_transporte.cod_grupo 
       AND beneficio'||stEntidade||'.grupo_concessao_vale_transporte.cod_mes = '||inMes||'
       AND beneficio'||stEntidade||'.grupo_concessao_vale_transporte.exercicio = '''||stAno||'''
       AND beneficio'||stEntidade||'.grupo_concessao_vale_transporte.vigencia <= '''||dtTimestamp||'''


      LEFT OUTER JOIN beneficio'||stEntidade||'.concessao_vale_transporte 
        ON beneficio'||stEntidade||'.concessao_vale_transporte.cod_concessao 
           = beneficio'||stEntidade||'.grupo_concessao_vale_transporte.cod_concessao 
       AND beneficio'||stEntidade||'.concessao_vale_transporte.cod_mes = '||inMes||'
       AND beneficio'||stEntidade||'.concessao_vale_transporte.exercicio = '''||stAno||'''

      LEFT OUTER JOIN beneficio'||stEntidade||'.vale_transporte
        ON beneficio'||stEntidade||'.vale_transporte.cod_vale_transporte 
           = beneficio'||stEntidade||'.concessao_vale_transporte.cod_vale_transporte

     WHERE beneficio'||stEntidade||'.contrato_servidor_grupo_concessao_vale_transporte.cod_contrato = '||inCodContrato||'
       AND beneficio'||stEntidade||'.grupo_concessao_vale_transporte.cod_grupo is not null

     ' ;

    FOR reRegistro2 IN  EXECUTE stSql2 LOOP

       stSql3 := '
           SELECT 
           beneficio'||stEntidade||'.custo.inicio_vigencia
           ,beneficio'||stEntidade||'.custo.vale_transporte_cod_vale_transporte as cod_vale_transporte
           ,beneficio'||stEntidade||'.custo.valor as valor
           FROM beneficio'||stEntidade||'.custo
           WHERE beneficio'||stEntidade||'.custo.vale_transporte_cod_vale_transporte = '||reRegistro2.cod_vale_transporte||'
             AND beneficio'||stEntidade||'.custo.inicio_vigencia <= '''||dtTimestamp||'''

             ';     

        FOR reRegistro3 IN  EXECUTE stSql3 LOOP

           IF reRegistro3.cod_vale_transporte is not null THEN
              inQtdValesGrupo := inQtdValesGrupo + reRegistro2.quantidade;
              nuValorGrupo := nuValorGrupo + ((reRegistro2.quantidade) * (reRegistro3.valor)) ;
           END IF;

        END LOOP;

    END LOOP;

    nuValorTotalVale := nuValorAvulsos + nuValorGrupo;

    RETURN nuValorTotalVale;

END;

$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION pega0valortotaldevalestransporteporcontratonadata(integer, character varying)
  OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pega0valortotaldevalestransporteporcontratonadata(integer, character varying) TO postgres;
GRANT EXECUTE ON FUNCTION pega0valortotaldevalestransporteporcontratonadata(integer, character varying) TO public;
GRANT EXECUTE ON FUNCTION pega0valortotaldevalestransporteporcontratonadata(integer, character varying) TO urbem;
