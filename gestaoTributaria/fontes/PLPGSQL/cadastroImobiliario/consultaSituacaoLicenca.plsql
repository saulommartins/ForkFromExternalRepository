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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: consultaSituacaoLicenca.plsql 60011 2014-09-25 15:12:19Z michel $
*
* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.2  2006/09/15 10:19:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_consulta_situacao_licenca(INT,VARCHAR) RETURNS VARCHAR AS '
    DECLARE
        reRecord                RECORD;
        stValor                 VARCHAR := ''Temp'' ;
        stSql                   VARCHAR;
        inCodLicenca            ALIAS FOR $1;
        stExercicio             ALIAS FOR $2;
	inTstLicenca		INTEGER;
        inCount                 INTEGER;	
    BEGIN
	SELECT  a.cod_licenca 
	INTO	inTstLicenca	
	FROM 	imobiliario.licenca a 
	WHERE 	
		a.cod_licenca =inCodLicenca AND
		a.exercicio   =stExercicio::varchar  AND
		a.dt_inicio <= now()::date and 
		Case 
			When a.dt_termino is not null then a.dt_termino >= now()::date 
			Else true::boolean 
		End;
	
	IF inTstLicenca IS NOT NULL THEN
		-- é ativa, mas pode estar baixada
		inTstLicenca := null;
		SELECT a.cod_tipo   
		INTO   inTstLicenca
		FROM   imobiliario.licenca_baixa a
		WHERE 	
			a.cod_licenca =inCodLicenca AND
			a.exercicio   =stExercicio::varchar  AND
			a.dt_inicio <= now()::date and 
			Case 
				When a.dt_termino is not null then a.dt_termino >= now()::date 
				Else true::boolean 
			End;
		IF inTstLicenca IS NOT NULL THEN
			SELECT 	nom_baixa
			INTO	stValor
			FROM    imobiliario.tipo_baixa
			WHERE cod_tipo = inTstLicenca;

			IF stValor = ''Suspensão'' THEN
				stValor := ''Suspensa'';
			ELSIF stValor = ''Cassação'' THEN
				stValor := ''Cassada'';
			ELSE
				stValor := ''Baixada'';
			END IF;				
				
		ELSE
			stValor := ''Ativa'';
		END IF;
	
	ELSE	
		stValor := '''';	
	END IF;		

        RETURN stValor;
    END;

'language 'plpgsql';
