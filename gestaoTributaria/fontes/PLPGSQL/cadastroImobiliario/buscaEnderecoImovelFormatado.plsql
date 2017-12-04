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
* $Id: buscaEnderecoImovelFormatado.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.19
*              uc-05.03.11
*/

/*
$Log$
Revision 1.2  2007/04/13 14:32:04  dibueno
Alteração na busca pelas informacoes do endereço

Revision 1.1  2007/04/04 15:02:22  dibueno
*** empty log message ***


*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_endereco_imovel_formatado( INTEGER )  RETURNS varchar AS '
DECLARE
    inImovel    ALIAS FOR $1;
    stRetorno   VARCHAR := null;
    stSql       VARCHAR;
    reRecord                record;
    
BEGIN


    SELECT                     

        coalesce (tl.nom_tipo, '''' )::varchar||'' ''|| coalesce(nl.nom_logradouro, '''')::varchar||'' ''||
        coalesce (i.numero, '''')::varchar||'' ''||coalesce (i.complemento, '''')::varchar||'' - ''||
        coalesce (bairro.nom_bairro, '''')::varchar

    INTO

        stRetorno

    FROM
        (   SELECT * FROM
            imobiliario.imovel
            WHERE inscricao_municipal = inImovel
        ) i
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
        i.inscricao_municipal   = inImovel
    ;


    RETURN stRetorno;
END;
' LANGUAGE 'plpgsql';
