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


CREATE OR REPLACE FUNCTION mediaRescisaoSomatorioValidosPorNrOcorrenciasValidas() RETURNS Numeric as '

DECLARE

inCodEvento                 INTEGER := 0;
stLido_de                   VARCHAR := ''evento_calculado'';
inNrOcorrencias             INTEGER := 0 ;
stSql                       VARCHAR := '''';
crCursor                    REFCURSOR;
reRegistro                  RECORD;
nuResultante                NUMERIC := 0;

BEGIN


  inCodEvento := recuperarBufferInteiro( ''inCodEvento'' );
 
  stSql := ''  SELECT cod_periodo_movimentacao
                      ,fixado
                      ,unidade_quantitativa
                      ,round( sum(COALESCE(valor,0)),2)       as valor
                      ,round( sum(COALESCE(quantidade,0)),2)  as quantidade
                 FROM tmp_registro_evento_rescisao
                WHERE cod_evento = ''||inCodEvento||''
                  AND substr(lido_de,1,16) ilike  ''''''||stLido_de||''''''
                  AND unidade_quantitativa > 0
                GROUP BY 1,2,3
           '';

  FOR reRegistro IN  EXECUTE stSql LOOP

      IF reRegistro.fixado = ''V'' THEN
          nuResultante := reRegistro.valor ;
      ELSE
          nuResultante := reRegistro.quantidade ;
      END IF;
     

      IF reRegistro.unidade_quantitativa > 0 THEN
         WHILE nuResultante >= ( reRegistro.unidade_quantitativa / 2 )
         LOOP 
            inNrOcorrencias :=  inNrOcorrencias + 1;
            nuResultante := nuResultante - reRegistro.unidade_quantitativa ;
         END LOOP;
      END IF;

  END LOOP;
  
  IF inNrOcorrencias > 0 THEN
      nuResultante := arredondar( (reRegistro.unidade_quantitativa / 12) * inNrOcorrencias ,2);
  END IF;
  RETURN nuResultante; 
END;
'LANGUAGE 'plpgsql';
