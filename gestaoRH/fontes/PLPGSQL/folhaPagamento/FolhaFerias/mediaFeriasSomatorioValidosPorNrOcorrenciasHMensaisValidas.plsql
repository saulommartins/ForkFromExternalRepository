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
--INSERT INTO administracao.funcao VALUES(27,1,5,4,'mediaFeriasSomatorioValidosPorNrOcorrenciasHMensaisValidas'); 
--*/

CREATE OR REPLACE FUNCTION mediaFeriasSomatorioValidosPorNrOcorrenciasHMensaisValidas() RETURNS Numeric as '

DECLARE

inCodEvento                 INTEGER;

stLido_de                   VARCHAR := ''evento_calculado'';

inNrOcorrencias             INTEGER := 0 ;
stSql                       VARCHAR := '''';
crCursor                    REFCURSOR;
reRegistro                  RECORD;
nuResultante                NUMERIC := 0;


inCodContrato               INTEGER ;
stDataFinalCompetencia      VARCHAR ;

nuHorasMensais              NUMERIC := 200.00 ;

BEGIN



  inCodContrato := recuperarBufferInteiro( ''inCodContrato'');
  stDataFinalCompetencia := recuperarBufferTexto( ''stDataFinalCompetencia'');

  inCodEvento := recuperarBufferInteiro( ''inCodEvento'' );  


--                       15 | Q      |  0.00 |     110.00
--                       14 | Q      |  0.00 |     109.00
--                       16 | Q      |  0.00 |      50.00
--                       13 | Q      |  0.00 |     200.00
-- SELECT cod_periodo_movimentacao
--      ,fixado
--      ,round( sum(COALESCE(valor,0)),2)       as valor
--      ,round( sum(COALESCE(quantidade,0)),2)  as quantidade
--        FROM tmp_registro_evento_ferias
--        WHERE cod_evento = 1
--          AND substr(lido_de,1,16) ilike evento_calculado
--         GROUP BY 1,2;


  nuHorasMensais := pega0CampoSalarioHorasMensaisNaData( inCodContrato, stDataFinalCompetencia );

  stSql := ''  SELECT cod_periodo_movimentacao
                      ,fixado
                      ,round( sum(COALESCE(quantidade,0)),2)  as quantidade
                 FROM tmp_registro_evento_ferias
                WHERE cod_evento = ''||inCodEvento||''
                  AND (substr(lido_de,1,16) ilike  ''''''||stLido_de||'''''' OR SUBSTR(lido_de,1,29) ILIKE  ''''evento_complementar_calculado'''')
                GROUP BY 1,2
                ORDER BY 1,2
           '';



  FOR reRegistro IN  EXECUTE stSql LOOP

      -- este tipo so deve ser utilizado para eventos fixados em quantidade
      IF ( reRegistro.fixado = ''V''  )  THEN
          nuResultante := 0;
      ELSE
          nuResultante := reRegistro.quantidade ;
      END IF;



      WHILE nuResultante >= ( nuHorasMensais / 2 )
      LOOP 
         inNrOcorrencias :=  inNrOcorrencias + 1;
         nuResultante :=  nuResultante - nuHorasMensais ;
      END LOOP;

  END LOOP;
  
  IF inNrOcorrencias > 0 THEN
      nuResultante := arredondar( (nuHorasMensais / 12) * inNrOcorrencias  ,2);
  END IF;

  RETURN nuResultante; 
END;
'LANGUAGE 'plpgsql';
