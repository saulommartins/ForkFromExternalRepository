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
* $Id: buscaAtributosElementos.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.1  2006/11/17 12:39:22  domluc
Correção Bug #7437#

Revision 1.1  2006/10/20 17:39:36  dibueno
*** empty log message ***

Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION economico.fn_busca_atributos_elementos( int,int,int,int,int )  RETURNS varchar AS '
DECLARE
    inInscricaoEconomica    ALIAS FOR $1;
    inCodAtividade          ALIAS FOR $2;
    inCodElemento           ALIAS FOR $3;
    inOcorrenciaElemento    ALIAS FOR $4;
    inOcorrenciaAtividade   ALIAS FOR $5;
    stRetorno               VARCHAR;
	reRecord        		record;
	stSql 					VARCHAR;
    
BEGIN

stSql = ''
     SELECT   atrib.nom_atributo
            , case
                when atrib.cod_tipo = 3 then
                    administracao.valor_padrao_desc(  atributo_valor.cod_atributo
                                                    , atributo_valor.cod_modulo
                                                    , atributo_valor.cod_cadastro
                                                    , atributo_valor.valor::varchar)::varchar
                else
                    atributo_valor.valor::varchar
            end as valor
         FROM economico.elemento_ativ_cad_economico ace
              INNER JOIN economico.atributo_elem_cad_economico_valor atributo_valor
                      ON atributo_valor.inscricao_economica = ace.inscricao_economica
                     AND atributo_valor.cod_atividade = ace.cod_atividade
                     AND atributo_valor.cod_elemento = ace.cod_elemento
                     AND atributo_valor.ocorrencia_elemento = ace.ocorrencia_elemento
                     AND atributo_valor.ocorrencia_atividade = ace.ocorrencia_atividade
              INNER JOIN administracao.atributo_dinamico atrib
                      ON atrib.cod_modulo  = atributo_valor.cod_modulo
                     AND atrib.cod_cadastro = atributo_valor.cod_cadastro
                     AND atrib.cod_atributo = atributo_valor.cod_atributo

     WHERE   ace.inscricao_economica = ''|| inInscricaoEconomica  || '' 
       and   ace.cod_atividade       = ''|| inCodAtividade        || '' 
       and   ace.cod_elemento        = ''|| inCodElemento         || '' 
       and   ace.ocorrencia_elemento = ''|| inOcorrenciaElemento  || '' 
       and   ace.ocorrencia_atividade= ''|| inOcorrenciaAtividade || '' 
       and   ace.ativo = true
    '';
    stRetorno := '''';
	FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := reRecord.valor;
       -- stRetorno := stRetorno|| reRecord.nom_atributo;
       -- stRetorno := stRetorno||'' = ''||reRecord.valor;
       -- stRetorno := stRetorno||''<br>'';
    END LOOP;

    RETURN stRetorno; 
END;
' LANGUAGE 'plpgsql';
