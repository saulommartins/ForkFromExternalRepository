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
* $Id: buscaDomicilioFiscal.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.02.12
*              uc-05.03.11
*/

/*
$Log$
Revision 1.5  2007/01/23 11:01:32  fabio
correção da tag de caso de uso

Revision 1.4  2007/01/18 12:36:13  dibueno
Melhorias para emissão de carne para gráfica

Revision 1.3  2007/01/12 19:02:14  dibueno
Melhorias para emissão de carne para gráfica

Revision 1.2  2007/01/12 13:03:35  dibueno
Alteração do caracter separador para '§'

Revision 1.1  2006/10/20 17:39:36  dibueno
*** empty log message ***

Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION economico.fn_busca_domicilio_fiscal( INTEGER )  RETURNS varchar AS '
DECLARE
    inInscricaoEconomica    ALIAS FOR $1;
    stRetorno   VARCHAR;
	reRecord        		record;
	stSql 					VARCHAR;
    
BEGIN

stSql = ''
    SELECT                     
		tl.nom_tipo::varchar as nom_tipo_logradouro,
        t.cod_logradouro::varchar as cod_logradouro,
        nl.nom_logradouro::varchar as logradouro,
		i.numero::varchar as numero,
		i.complemento::varchar as complemento,
		bairro.nom_bairro::varchar as bairro,
		( i.cep )::varchar as cep,
		uf.cod_uf::varchar as cod_uf,
        uf.sigla_uf::varchar as sigla_uf,
		mun.cod_municipio::varchar as cod_municipio,
        mun.nom_municipio::varchar as municipio
    FROM
        economico.domicilio_fiscal as edf
        
        INNER JOIN imobiliario.imovel as i
        ON i.inscricao_municipal = edf.inscricao_municipal

		INNER JOIN imobiliario.imovel_confrontacao ic
		ON ic.inscricao_municipal  = i.inscricao_municipal

		INNER JOIN imobiliario.confrontacao_trecho ct
		ON ct.cod_confrontacao	= ic.cod_confrontacao AND
        ct.cod_lote             = ic.cod_lote

        INNER JOIN imobiliario.trecho t
		ON t.cod_trecho		= ct.cod_trecho   	AND
        t.cod_logradouro    = ct.cod_logradouro

        INNER JOIN sw_logradouro l
		ON l.cod_logradouro = t.cod_logradouro

        INNER JOIN sw_nome_logradouro nl
		ON nl.cod_logradouro = l.cod_logradouro
                                               
        INNER JOIN sw_tipo_logradouro tl
		ON tl.cod_tipo       = nl.cod_tipo

		INNER JOIN imobiliario.lote_bairro as ilb
		ON ilb.cod_lote = ic.cod_lote
		AND ilb.cod_municipio = l.cod_municipio
		AND ilb.cod_uf = l.cod_uf

		INNER JOIN sw_bairro as bairro
		ON bairro.cod_bairro = ilb.cod_bairro
		AND bairro.cod_municipio = l.cod_municipio
		AND bairro.cod_uf = l.cod_uf

		INNER JOIN sw_municipio as mun
		ON mun.cod_municipio = l.cod_municipio
		AND mun.cod_uf = l.cod_uf

		INNER JOIN sw_uf as uf
		ON uf.cod_uf = mun.cod_uf

    WHERE                                                                  
        edf.inscricao_economica   = ''|| inInscricaoEconomica ||''

    order by edf.timestamp desc limit 1

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
