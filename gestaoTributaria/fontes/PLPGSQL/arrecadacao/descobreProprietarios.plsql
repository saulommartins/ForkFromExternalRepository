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
CREATE OR REPLACE FUNCTION descobreProprietarios( inCodLancamento INTEGER ) RETURNS VARCHAR AS $$
DECLARE

stSql VARCHAR;
stTemp VARCHAR;
crCursor REFCURSOR;
reRecord RECORD;
stProprietarios VARCHAR = '';

BEGIN
stSql := 'SELECT sw_cgm.numcgm, sw_cgm.nom_cgm
	  FROM sw_cgm
	  INNER JOIN imobiliario.proprietario ON imobiliario.proprietario.numcgm = sw_cgm.numcgm
	  INNER JOIN arrecadacao.imovel_calculo ON arrecadacao.imovel_calculo.inscricao_municipal = imobiliario.proprietario.inscricao_municipal
	  AND imovel_calculo.cod_calculo = (SELECT max(cod_calculo) 
							FROM arrecadacao.lancamento_calculo 
							WHERE cod_lancamento = '||inCodLancamento||')
	 ORDER BY sw_cgm.nom_cgm';

OPEN crCursor FOR EXECUTE stSql;
	LOOP
		FETCH crCursor INTO reRecord;			
		EXIT WHEN NOT FOUND;		
		stTemp := reRecord.numcgm||' - '||reRecord.nom_cgm||' / ';
		stProprietarios := stProprietarios||stTemp;
	END LOOP;
CLOSE crCursor;

IF stProprietarios = '' THEN

stSql := 'SELECT sw_cgm.numcgm, sw_cgm.nom_cgm
	  FROM sw_cgm
	  INNER JOIN arrecadacao.calculo_cgm ON calculo_cgm.numcgm = sw_cgm.numcgm
	  AND calculo_cgm.cod_calculo = (SELECT max(cod_calculo) 
					    FROM arrecadacao.lancamento_calculo 
					    WHERE cod_lancamento = '||inCodLancamento||')
          INNER JOIN arrecadacao.calculo
                  ON calculo.cod_calculo = calculo_cgm.cod_calculo
	 ORDER BY sw_cgm.nom_cgm';

OPEN crCursor FOR EXECUTE stSql;
	LOOP
		FETCH crCursor INTO reRecord;			
		EXIT WHEN NOT FOUND;		
		stTemp := reRecord.numcgm||' - '||reRecord.nom_cgm||' / ';
		stProprietarios := stProprietarios||stTemp;
	END LOOP;
CLOSE crCursor;
END IF;

stProprietarios := SUBSTR(stProprietarios, 1, (LENGTH(stProprietarios)-2));

RETURN stProprietarios;

END;

$$ LANGUAGE 'plpgsql';
