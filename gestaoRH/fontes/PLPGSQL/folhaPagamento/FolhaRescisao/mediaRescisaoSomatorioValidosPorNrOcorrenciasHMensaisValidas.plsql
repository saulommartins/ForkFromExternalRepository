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


CREATE OR REPLACE FUNCTION mediaRescisaoSomatorioValidosPorNrOcorrenciasHMensaisValidas() RETURNS Numeric as '

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

  inCodContrato            := recuperarBufferInteiro( ''inCodContrato'');
  stDataFinalCompetencia   := recuperarBufferTexto( ''stDataFinalCompetencia'');
  inCodEvento              := recuperarBufferInteiro( ''inCodEvento'' );  
  nuHorasMensais           := pega0CampoSalarioHorasMensaisNaData( inCodContrato, stDataFinalCompetencia );

  stSql := ''  SELECT cod_periodo_movimentacao
                      ,fixado
                      ,ROUND( SUM(COALESCE(quantidade,0)),2)  as quantidade
                 FROM tmp_registro_evento_rescisao
                WHERE cod_evento = ''||inCodEvento||''
                  AND SUBSTR(lido_de,1,16) ilike  ''''''||stLido_de||''''''
                GROUP BY 1,2
                ORDER BY 1,2
           '';

  FOR reRegistro IN  EXECUTE stSql LOOP
      --PARA ESTE TIPO DE MÉDIA SÃO UTILIZADOS APENAS EVENTOS FIXADOS POR QUANTIDADE 
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
