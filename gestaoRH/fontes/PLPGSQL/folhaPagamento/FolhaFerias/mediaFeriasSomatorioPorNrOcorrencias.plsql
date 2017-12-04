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
-- Objetivo: avalia o tipo de media indicado a partir dos dados temporarios
-- da geracao do registro de ferias
--

--INSERT INTO administracao.funcao VALUES(27,1,3,4,'mediaFeriasSomatorioPorNrOcorrencias');
--*/

CREATE OR REPLACE FUNCTION mediaFeriasSomatorioPorNrOcorrencias() RETURNS Numeric as '

DECLARE

inCodEvento                 INTEGER := 0;
stSql                       VARCHAR := '''';
crCursor                    REFCURSOR;
nuValor                     NUMERIC := 0;
nuQuantidade                NUMERIC := 0;
stFixado                    VARCHAR := ''V'';
nuRetorno                   NUMERIC := 0;
stLido_de                   VARCHAR := ''evento_calculado'';
inNrOcorrencias             INTEGER := 0 ;


BEGIN


  inCodEvento := recuperarBufferInteiro( ''inCodEvento'' );  

  stSql := '' SELECT count( distinct(cod_periodo_movimentacao) ) as nr_ocorrencias
                FROM tmp_registro_evento_ferias 
                WHERE cod_evento = ''||inCodEvento||''
                  AND (substr(lido_de,1,16) ilike  ''''''||stLido_de||''''''  OR SUBSTR(lido_de,1,29) ILIKE  ''''evento_complementar_calculado'''')
           '';


  OPEN crCursor FOR EXECUTE stSql;
       FETCH crCursor INTO inNrOcorrencias ;
  CLOSE crCursor;




  IF inNROcorrencias > 0 THEN

     stSql := '' SELECT fixado
                 ,round( sum(COALESCE(valor,0)) /''||inNrOcorrencias||'' ,2 )       as valor
                 ,round( sum(COALESCE(quantidade,0 ))/''||inNrOcorrencias||'' ,2 )  as quantidade

                FROM tmp_registro_evento_ferias 
                WHERE cod_evento = ''||inCodEvento||''
                  AND substr(lido_de,1,16) ilike  ''''''||stLido_de||''''''
                group  by 1
              '';

     OPEN crCursor FOR EXECUTE stSql;
          FETCH crCursor INTO  stFixado, nuValor, nuQuantidade ;
     CLOSE crCursor;





     IF stFixado = ''V'' THEN
        nuRetorno := nuValor;
     ELSE
        nuRetorno := nuQuantidade;
     END IF;
  END IF;

  RETURN nuRetorno; 
END;
'LANGUAGE 'plpgsql';
