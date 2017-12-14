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
--    * Função PLSQL
--    * Data de Criação: 01/11/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23157 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-11 14:19:50 -0300 (Seg, 11 Jun 2007) $
--
--    * Casos de uso: uc-04.05.18
--*/


CREATE OR REPLACE FUNCTION mediaRescisaoSomatorioPorNrOcorrencias() RETURNS Numeric as $$ 

DECLARE

inCodEvento                 INTEGER := 0;
stSql                       VARCHAR := '';
crCursor                    REFCURSOR;
nuValor                     NUMERIC := 0;
nuQuantidade                NUMERIC := 0;
stFixado                    VARCHAR := 'V';
nuRetorno                   NUMERIC := 0;

-- utiliza apenas a comparacao com o inicio da expressao pois considera,
-- tambem, o evento_calculado_complementar.

stLido_de                   VARCHAR := 'evento_calculado';
inNrOcorrencias             INTEGER := 0 ;

BEGIN


  inCodEvento := recuperarBufferInteiro( 'inCodEvento' );  

  stSql := '  SELECT COUNT (*) 
                FROM (  SELECT  cod_periodo_movimentacao
                          FROM tmp_registro_evento_rescisao
                         WHERE cod_evento = '||inCodEvento||'
                           AND substr(lido_de,1,16) ILIKE  '''||stLido_de||'''
                      GROUP BY cod_periodo_movimentacao) as contador
           ';
 
 
  OPEN crCursor FOR EXECUTE stSql;
       FETCH crCursor INTO inNrOcorrencias ;
  CLOSE crCursor;

  IF inNrOcorrencias > 0 THEN
     stSql := '  SELECT fixado
                       ,ROUND( SUM(COALESCE(valor,0)) /'||inNrOcorrencias||' ,2 )       as valor
                       ,ROUND( SUM(COALESCE(quantidade,0 ))/'||inNrOcorrencias||' ,2 )  as quantidade
                  FROM tmp_registro_evento_rescisao
                 WHERE cod_evento = '||inCodEvento||'
                   AND SUBSTR(lido_de,1,16)  ILIKE  '''||stLido_de||'''
                 GROUP BY 1
              ';


     OPEN crCursor FOR EXECUTE stSql;
          FETCH crCursor INTO  stFixado, nuValor, nuQuantidade ;
     CLOSE crCursor;


     IF stFixado = 'V' THEN
        nuRetorno := nuValor;
     ELSE
        nuRetorno := nuQuantidade;
     END IF;
  END IF;

  RETURN nuRetorno; 
END;
$$LANGUAGE 'plpgsql';
