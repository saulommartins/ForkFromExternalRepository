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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28665 $
* $Name$
* $Author: gris $
* $Date: 2008-03-20 10:51:31 -0300 (Qui, 20 Mar 2008) $
*
* Caso de uso: uc-06.04.00
*/

--
--
--
CREATE OR REPLACE FUNCTION public.manutencaokiller() RETURNS BOOLEAN AS $$
DECLARE
    reRegistro  RECORD;
    varFuncao   VARCHAR;
BEGIN
    FOR reRegistro IN SELECT 'DROP FUNCTION '         ||
                             pg_namespace.nspname     ||
                             '.'                      ||
                             pg_proc.proname          ||
                             '( '                     ||
                             Btrim(pg_catalog.oidvectortypes(pg_proc.proargtypes)) ||
                             ' ) '  as comando
                        FROM pg_catalog.pg_proc LEFT JOIN pg_catalog.pg_namespace  ON pg_namespace.oid = pg_proc.pronamespace
                        WHERE pg_proc.prorettype     <> 'pg_catalog.cstring'::pg_catalog.regtype
                          AND  NOT pg_proc.proisagg
                          AND pg_proc.proname      = 'numero_anulacao_liquidacao'
                          AND pg_namespace.nspname   = 'tcemg'
                     ORDER BY 1
    LOOP
    varFuncao := reRegistro.comando;
    EXECUTE varFuncao;
    END LOOP;
    RETURN true;
END;

$$ LANGUAGE 'plpgsql';

Select public.manutencaokiller(); Drop Function public.manutencaokiller();


--
-- Função que atribui sequencial para uma nota_liquidacao
--
CREATE OR REPLACE FUNCTION TCEMG.numero_anulacao_liquidacao( VARCHAR, VARCHAR, INTEGER, INTEGER, TIMESTAMP ) RETURNS INTEGER AS $$
DECLARE
    varExercicio        ALIAS FOR $1;
    varExercicioEmpenho ALIAS FOR $2;
    intCodEntidade      ALIAS FOR $3;
    intCodNota          ALIAS FOR $4;
    varTimeStamp        ALIAS FOR $5;
    recRegistro         RECORD;
    intRetorno          INTEGER;
    intCodPreEmpenho    INTEGER;
    
BEGIN
    ALTER SEQUENCE tcemg.seqLiquidacaoAnulacao RESTART WITH 1;
    
    SELECT nota_liquidacao_item.cod_pre_empenho
      INTO intCodPreEmpenho
      FROM empenho.nota_liquidacao_item
     WHERE nota_liquidacao_item.exercicio = varExercicioEmpenho
       AND nota_liquidacao_item.cod_entidade = intCodEntidade
       AND nota_liquidacao_item.cod_nota = intCodNota;
       
    FOR recRegistro IN SELECT timestamp
                         FROM empenho.nota_liquidacao_item_anulado
                        WHERE exercicio = varExercicio
                          AND cod_entidade = intCodEntidade
                          AND cod_pre_empenho = intCodPreEmpenho
                     GROUP BY timestamp
                     ORDER BY timestamp
    LOOP
        intRetorno := nextval('tcemg.seqLiquidacaoAnulacao');
        IF recRegistro.timestamp = varTimeStamp
            THEN EXIT;
        END IF;
    END LOOP;
    
    RETURN intRetorno;
    
END;
   
$$ LANGUAGE 'plpgsql'  SECURITY DEFINER;