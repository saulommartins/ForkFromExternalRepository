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
* $Id: buscaEnderecoCorrespondencia.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.02.12
*              uc-05.03.11
*/

/*
$Log$
Revision 1.4  2007/06/27 14:02:02  dibueno
Melhoria e Identação do código

Revision 1.3  2007/01/23 11:01:01  fabio
correção da tag de caso de uso

Revision 1.2  2007/01/12 19:02:36  dibueno
Melhorias para emissão de carne para gráfica

Revision 1.1  2007/01/12 15:29:52  dibueno
*** empty log message ***

Revision 1.1  2006/10/20 17:39:36  dibueno
*** empty log message ***

Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_endereco_correspondencia ( INTEGER )  RETURNS varchar AS '
DECLARE

    inImovel        ALIAS FOR $1;
    stAux           VARCHAR := '''';
    stRetorno       VARCHAR := '''';
	reRecord        record;
	stSql 			VARCHAR := '''';
	inMunicipio	integer;
	inMunCGM	integer;
	inTipo		integer:=1;
	inCodigo	integer;
    
BEGIN

	SELECT
                valor
        INTO
                inMunicipio
        FROM
                administracao.configuracao
        WHERE
                parametro = ''cod_municipio'';

        IF inMunicipio = 251 THEN
                SELECT
			case when cod_municipio_corresp = 0 then 251 else cod_municipio_corresp end as 
                        cod_municipio_corresp
                INTO
                        inMunCGM
                FROM imobiliario.proprietario as ip, sw_cgm as cgm
                WHERE ip.inscricao_municipal = inImovel AND
                        ip.ordem = 1 AND
                        ip.numcgm = cgm.numcgm;

                IF inMunicipio <> inMunCGM THEN
                        inTipo = 2;
			SELECT
				cgm.numcgm
			INTO 
				inCodigo
			FROM imobiliario.proprietario as ip, sw_cgm as cgm
	                WHERE ip.inscricao_municipal = inImovel AND
                        ip.ordem = 1 AND
                        ip.numcgm = cgm.numcgm;
                END IF;
        END IF;

    IF inTipo = 1 THEN

    SELECT

        coalesce (
            TL.nom_tipo||''§''||IMC.cod_logradouro||''§''||LOGRN.nom_logradouro||''§''||     IMC.numero||''§''||IMC.complemento||''§''||IMC.cep||''§''||coalesce ( IMC.caixa_postal, '''')||''§''||BAIRRO.nom_bairro||''§''||MUN.cod_municipio||''§''||MUN.nom_municipio||''§''||UF.cod_uf||''§''||UF.sigla_uf||''§''||IMC.inscricao_municipal
            , ''''
        ) as endereco

    INTO
        stRetorno
    FROM
        imobiliario.imovel_correspondencia as IMC

        INNER JOIN sw_uf as UF
        ON UF.cod_uf = IMc.cod_UF

        INNER JOIN sw_municipio as MUN
        ON MUN.cod_uf = UF.cod_UF
        AND MUN.cod_municipio = IMC.cod_municipio

        INNER JOIN sw_bairro as BAIRRO
        ON BAIRRO.cod_uf = UF.cod_UF
        AND BAIRRO.cod_municipio = MUN.cod_municipio
        AND BAIRRO.cod_bairro = IMC.cod_bairro

        INNER JOIN sw_logradouro as LOGR
        ON LOGR.cod_logradouro = IMC.cod_logradouro
        AND LOGR.cod_municipio = IMC.cod_municipio
        AND LOGR.cod_uf = IMC.cod_uf
        INNER JOIN sw_nome_logradouro as LOGRN
        ON LOGRN.cod_logradouro = LOGR.cod_logradouro
        INNER JOIN sw_tipo_logradouro as TL
        ON TL.cod_tipo = LOGRN.cod_tipo

    WHERE                                                                  
        IMC.inscricao_municipal   = inImovel

    ORDER BY
        IMC.timestamp desc
    LIMIT 1
    ;
    ELSE
SELECT

        coalesce (
            cgm.tipo_logradouro_corresp||''§''||''0''||''§''||cgm.logradouro_corresp||''§''||cgm.numero_corresp||''§''||cgm.complemento_corresp||''§''||cgm.cep_corresp||''§''||''§''||cgm.bairro_corresp||''§''||MUN.cod_municipio||''§''||MUN.nom_municipio||''§''||UF.cod_uf||''§''||UF.sigla_uf||''§''||inImovel
            , ''''
        ) as endereco

    INTO
        stRetorno
    FROM
       sw_cgm as cgm

        INNER JOIN sw_uf as UF
        ON UF.cod_uf = cgm.cod_UF_corresp

        INNER JOIN sw_municipio as MUN
        ON MUN.cod_uf = UF.cod_UF
        AND MUN.cod_municipio = cgm.cod_municipio_corresp

    WHERE                                                                  
        cgm.numcgm = inCodigo
    ;

    END IF;

    RETURN stRetorno;

END;
' LANGUAGE 'plpgsql';
