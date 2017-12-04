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
* $Id: buscaEnderecoImovel.plsql 66548 2016-09-21 13:05:07Z evandro $
*
* Caso de uso: uc-05.03.19
*              uc-05.03.11
*/

/*
$Log$
Revision 1.6  2007/06/27 14:01:31  dibueno
Limpeza e Identação do código

Revision 1.5  2007/01/23 11:01:01  fabio
correção da tag de caso de uso

Revision 1.4  2007/01/16 16:36:12  dibueno
*** empty log message ***

Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_endereco_imovel( INTEGER )  RETURNS varchar AS $$
DECLARE
    inImovel    ALIAS FOR $1;
    stRetorno   VARCHAR;
    stSql       VARCHAR;
    reRecord                record;
    
BEGIN

    stSql := '

        SELECT

            COALESCE( tl.nom_tipo::varchar, '' '' ) as nom_tipo_logradouro
            , COALESCE( t.cod_logradouro::varchar, '' '' ) as cod_logradouro
            , COALESCE( nl.nom_logradouro::varchar, '' '' ) as logradouro
            , COALESCE( i.numero::varchar, '' '' ) as numero
            , COALESCE( i.complemento::varchar, '' '' ) as complemento
            , COALESCE( bairro.nom_bairro::varchar, '' '' ) as bairro
            , COALESCE( i.cep::varchar, '' '' ) as cep
            , COALESCE( uf.cod_uf::varchar, '' '' ) as cod_uf
            , COALESCE( uf.sigla_uf::varchar, '' '' ) as sigla_uf
            , COALESCE( mun.cod_municipio::varchar, '' '' ) as cod_municipio
            , COALESCE( mun.nom_municipio::varchar, '' '' ) as municipio

        FROM

            imobiliario.imovel as i

            INNER JOIN imobiliario.imovel_confrontacao ic
            ON ic.inscricao_municipal  = i.inscricao_municipal

            INNER JOIN imobiliario.confrontacao_trecho ct
            ON ct.cod_confrontacao  = ic.cod_confrontacao AND
            ct.cod_lote             = ic.cod_lote

            INNER JOIN imobiliario.trecho t
            ON t.cod_trecho     = ct.cod_trecho     AND
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
            i.inscricao_municipal   = '|| inImovel
    ;

    FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := reRecord.nom_tipo_logradouro;
        stRetorno := stRetorno||'§'||reRecord.cod_logradouro;
        stRetorno := stRetorno||'§'||reRecord.logradouro;
        stRetorno := stRetorno||'§'||reRecord.numero;
        stRetorno := stRetorno||'§'||reRecord.complemento;
        stRetorno := stRetorno||'§'||reRecord.bairro;
        stRetorno := stRetorno||'§'||reRecord.cep;
        stRetorno := stRetorno||'§'||reRecord.cod_municipio;
        stRetorno := stRetorno||'§'||reRecord.municipio;
        stRetorno := stRetorno||'§'||reRecord.cod_uf;
        stRetorno := stRetorno||'§'||reRecord.sigla_uf;
    END LOOP;

    RETURN stRetorno;

END;
$$ LANGUAGE 'plpgsql';
