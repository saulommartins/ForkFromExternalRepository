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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaDomicilioInformado.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.02.12
*              uc-05.03.11
*/

/*
$Log$
Revision 1.5  2007/01/23 11:01:32  fabio
correção da tag de caso de uso

Revision 1.4  2007/01/18 12:36:24  dibueno
Melhorias para emissão de carne para gráfica

Revision 1.3  2007/01/12 13:03:11  dibueno
Alteração do caracter separador para '§'

Revision 1.2  2006/10/20 17:39:36  dibueno
*** empty log message ***

Revision 1.1  2006/10/17 16:39:41  dibueno
*** empty log message ***

*/

CREATE OR REPLACE FUNCTION economico.fn_busca_domicilio_informado( INTEGER )  RETURNS varchar AS '
DECLARE
    inInscricaoEconomica    ALIAS FOR $1;
	stRetorno   			VARCHAR;
	reRecord        		record;
	stSql 					VARCHAR;
    
BEGIN

stSql = ''
SELECT 
    tl.nom_tipo::varchar as nom_tipo_logradouro,
    logr.cod_logradouro::varchar as cod_logradouro,
    logr.nom_logradouro::varchar as logradouro,
    edi.numero::varchar as numero,
    edi.complemento::varchar as complemento,
    bairro.nom_bairro::varchar as bairro,
    ( edi.cep )::varchar as cep,
    uf.cod_uf::varchar as cod_uf,
    uf.sigla_uf::varchar as sigla_uf,
    mun.cod_municipio::varchar as cod_municipio,
    mun.nom_municipio::varchar as municipio
FROM
	economico.domicilio_informado as edi

	INNER JOIN sw_uf as uf
	ON uf.cod_uf = edi.cod_uf

	INNER JOIN sw_municipio as mun
	ON mun.cod_municipio = edi.cod_municipio
	AND mun.cod_uf = edi.cod_uf

	INNER JOIN sw_bairro as bairro
	ON bairro.cod_bairro = edi.cod_bairro
	AND bairro.cod_municipio = edi.cod_municipio
	AND bairro.cod_uf = edi.cod_uf

	INNER JOIN sw_nome_logradouro as logr
	ON logr.cod_logradouro = edi.cod_logradouro

	INNER JOIN sw_tipo_logradouro as tl
	ON tl.cod_tipo = logr.cod_tipo

	where edi.inscricao_economica = ''||inInscricaoEconomica||''

    order by edi.timestamp desc limit 1
    
	'';

    FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := reRecord.nom_tipo_logradouro;
        stRetorno := stRetorno||''§''||reRecord.cod_logradouro;
        stRetorno := stRetorno||''§''||reRecord.logradouro;
        stRetorno := stRetorno||''§''||reRecord.numero;
        stRetorno := stRetorno||''§''||reRecord.complemento;
        stRetorno := stRetorno||''§''||reRecord.bairro;
        stRetorno := stRetorno||''§''||reRecord.cep;
        stRetorno := stRetorno||''§''||reRecord.cod_municipio;
        stRetorno := stRetorno||''§''||reRecord.municipio;
        stRetorno := stRetorno||''§''||reRecord.cod_uf;
        stRetorno := stRetorno||''§''||reRecord.sigla_uf;
    END LOOP;

    RETURN stRetorno; 
END;
' LANGUAGE 'plpgsql';
