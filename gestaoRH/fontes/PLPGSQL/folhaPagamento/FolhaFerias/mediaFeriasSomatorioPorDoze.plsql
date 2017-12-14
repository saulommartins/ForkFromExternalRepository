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
--/**
--
-- script de funcao PLSQL
--
-- URBEM Solugues de Gestco Pzblica Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23133 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/07/01 10:50:00 $
--
-- Caso de uso: uc-04.05.53
--
-- Objetivo: avalia o resultado do tipo de media utilizando o arquivo 
-- temporario 
--
-- INSERT INTO administracao.funcao VALUES(27,1,2,4,'mediaFeriasSomatorioPorDoze');
--
--*/

CREATE OR REPLACE FUNCTION mediaFeriasSomatorioPorDoze() RETURNS Numeric as '

DECLARE



inCodEvento                 INTEGER := 0;
stSql                       VARCHAR := '''';
crCursor                    REFCURSOR;
nuValor                     NUMERIC := 0;
nuQuantidade                NUMERIC := 0;
stFixado                    VARCHAR := ''V'';
nuRetorno                   NUMERIC := 0;
stLido_de                   VARCHAR := ''evento_calculado'';

BEGIN

  inCodEvento := recuperarBufferInteiro( ''inCodEvento'' );  

  stSql := '' SELECT fixado
                     ,round( COALESCE(sum(valor),0) /12 ,2 )      as valor
                     ,round( COALESCE(sum(quantidade),0) /12 ,2 ) as quantidade
                FROM tmp_registro_evento_ferias 
                WHERE cod_evento = ''||inCodEvento||''
                  AND (substr(lido_de,1,16) ilike  ''''''||stLido_de||'''''' OR SUBSTR(lido_de,1,29) ILIKE  ''''evento_complementar_calculado'''')
                GROUP BY fixado
           '';




  OPEN crCursor FOR EXECUTE stSql;
       FETCH crCursor INTO stFixado, nuValor, nuQuantidade;
  CLOSE crCursor;

  IF stFixado = ''V'' THEN
     nuRetorno := nuValor;
  ELSE
     nuRetorno := nuQuantidade;
  END IF;

  IF nuRetorno is null THEN
     nuRetorno = 0 ;
  END IF;

  RETURN nuRetorno; 
END;
'LANGUAGE 'plpgsql';
