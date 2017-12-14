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

    * Atualiza a data de vencimento, caso caia em um sábado ou domingo.
    * Passa-se o vencimento para o proximo dia útil
    *
    * URBEM Soluções de Gestão Pública Ltda
    * www.urbem.cnm.org.br
    *
    * $Id: AtualizaDataVencimento.plsql 59612 2014-09-02 12:00:51Z gelson $
    *
    * Caso de uso: uc-05.03.19
    *
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_atualiza_data_vencimento( date ) returns date as $$
DECLARE

    dataVencimento	ALIAS FOR $1;
	ano 			integer := substring ( $1::varchar, 0, 5 );
	mes 			integer := substring ( $1::varchar, 6, 2 );
	dia 			integer := substring ( $1::varchar, 9, 2 );

	anoV			varchar;
	mesV			char(2);
	diaV			varchar;
	dataVarchar		varchar;

	diaSemana		integer;
	diaFev			integer;
	novoVencimento	date;

	stSql			varchar;

    stRetorno       date;
    reRecord        RECORD;

begin

stSql := '
	SELECT EXTRACT( DOW FROM DATE '''||dataVencimento||''' ) as valor
	';

	FOR reRecord IN EXECUTE stSql LOOP
    	diaSemana := reRecord.valor;
    END LOOP;

	--verifica ano bissexto
	IF ( ( ano - 1980 ) % 4 = 0 ) THEN
		diaFev := 29;
	ELSE
		diaFev := 28;
	END IF;


	IF diaSemana = 6 THEN -- se for SÁBADO
		IF (dia = 31) OR ( mes = 2 AND dia = diaFev ) OR  ( dia = 30 AND mes in ( 04, 06, 09, 11 ) )  THEN
			dia := 2;
			IF mes = 12 THEN
				mes := 1;
				ano := ano + 1;
			ELSE
				mes := mes + 1;
			END IF;
		ELSIF ( (dia = (diaFev-1)) AND mes = 2) OR (dia = 29 AND mes in (04, 06, 09, 11)) OR (dia = 30 AND mes in (01, 03, 05, 07, 08, 10, 12) ) THEN
			dia := 1;
			IF mes = 12 THEN
				mes := 1;
				ano := ano + 1;
			ELSE
				mes := mes + 1;
			END IF;
		ELSE
			dia := dia + 2;
		END IF;

	ELSIF diaSemana = 0 THEN --se for DOMINGO
		IF (dia = 31) OR ( mes = 2 AND dia = diaFev ) OR ( dia = 30 AND mes in ( 04, 06, 09, 11 ) ) THEN
			dia := 1;
			IF mes = 12 THEN
				mes := 1;
				ano := ano + 1;
			ELSE
				mes := mes + 1;
			END IF;
		ELSE
			dia := dia + 1;
		END IF;
	END IF;

	anoV := to_char(ano,'0000');
	mesV := substring ( to_char(mes,'00'), 2, 2 );
	diaV := substring ( to_char(dia,'00'), 2, 2 );

	dataVarchar := anoV||'-'||mesV||'-'||diaV;

	--novoVencimento := to_date ( dataVarchar, ''YYYY-MM-DD'');

   	return dataVarchar;
end;
$$ LANGUAGE 'plpgsql';