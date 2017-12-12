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
* $Id: consultaProcessoLicenca.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.2  2006/09/15 10:19:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION economico.fn_consulta_processo_licenca(INT,VARCHAR) RETURNS VARCHAR AS '
    DECLARE
        inCodLicenca            ALIAS FOR $1;
        stExercicio             ALIAS FOR $2;
        stProcesso              VARCHAR;
        stSituacao              VARCHAR;
        stSql                   VARCHAR;
	reRecord		RECORD;
    BEGIN
	stSituacao := economico.fn_consulta_situacao_licenca(inCodLicenca, stExercicio);
	
	If stSituacao = ''Ativa'' OR stSituacao = ''Expirada'' THEN
	stSql := ''SELECT cod_processo, exercicio_processo
		   FROM	economico.processo_licenca
		   WHERE   cod_licenca = ''||inCodLicenca||''
	 	   AND	exercicio   = ''''''||stExercicio||''''''	'';
	ELSE
	stSql := ''SELECT cod_processo,exercicio_processo
		 FROM	economico.processo_baixa_licenca
		 WHERE   cod_licenca = ''||inCodLicenca||''
	 	 AND	exercicio   = ''''''||stExercicio||''''''	'';	 
	END IF;		

	FOR reRecord IN EXECUTE stSql LOOP
		stProcesso := reRecord.cod_processo::varchar||''/''||reRecord.exercicio_processo::varchar ;
	END LOOP;
        RETURN stProcesso;
    END;

'language 'plpgsql';
