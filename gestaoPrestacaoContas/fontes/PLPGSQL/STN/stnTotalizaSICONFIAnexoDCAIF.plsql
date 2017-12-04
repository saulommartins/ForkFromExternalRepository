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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*/

CREATE OR REPLACE FUNCTION stn.totaliza_siconfi_anexo_dca_if(VARCHAR) RETURNS numeric[] AS $$
DECLARE
    stMascaraReduzida   ALIAS FOR $1;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    nuValorT1           NUMERIC   := 0;
    nuValorT2           NUMERIC   := 0;
    nuValorT3           NUMERIC   := 0;
    nuValorT4           NUMERIC   := 0;
    nuValorT5           NUMERIC   := 0;
    nuValorT6           NUMERIC   := 0;
    nuValorT7           NUMERIC   := 0;
    nuValorT8           NUMERIC   := 0;
    arRetorno           NUMERIC[] := array[0];

BEGIN
    
    SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT1
      FROM tmp_processados_exercicios_anteriores
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;
    
    SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT2
      FROM tmp_processados_exercicio_anterior
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;

     SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT3
      FROM tmp_processados_cancelado
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;

     SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT4
      FROM tmp_processados_pago
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;

     SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT5
      FROM tmp_nao_processados_exercicios_anteriores
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;

     SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT6
      FROM tmp_nao_processados_exercicio_anterior
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;

     SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT7
      FROM tmp_nao_processados_cancelado
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;

     SELECT SUM( COALESCE(vl_total,0.00) ) as soma
      INTO nuValorT8
      FROM tmp_nao_processados_pago
     WHERE cod_estrutural LIKE ''||stMascaraReduzida||'%' ;

    --Preenche array de retorno
    arRetorno[1] := nuValorT1;
    arRetorno[2] := nuValorT2;
    arRetorno[3] := nuValorT3;
    arRetorno[4] := nuValorT4;
    arRetorno[5] := nuValorT5;
    arRetorno[6] := nuValorT6;
    arRetorno[7] := nuValorT7;
    arRetorno[8] := nuValorT8;

    RETURN arRetorno;
END;
$$ language 'plpgsql';
