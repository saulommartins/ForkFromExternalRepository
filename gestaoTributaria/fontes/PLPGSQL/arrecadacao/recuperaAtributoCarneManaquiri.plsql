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
* $Id:
*
* Caso de uso: uc-05.03.00
* Recupera os atributos do Carnê de Manaquiri
*/
--DROP FUNCTION recuperaAtributoCarneManaquiri(integer);
CREATE OR REPLACE FUNCTION recuperaAtributoCarneManaquiri(integer) RETURNS SETOF RECORD as $$

    DECLARE
        inInscricaoMunicipal    ALIAS FOR $1;
        reRegistro              RECORD;
        inCodTipo               INTEGER;
        inCodConstrucao         INTEGER;
        stSql                   VARCHAR;
        
    BEGIN
    
            SELECT
                  cod_tipo
                , cod_construcao
            INTO
                  inCodTipo
                , inCodConstrucao
            FROM
                imobiliario.unidade_autonoma
            WHERE
                inscricao_municipal = inInscricaoMunicipal
            ;

        IF inCodConstrucao IS NOT NULL THEN
            stSql := '
                SELECT
                    --consulta do uso do imóvel
                    (SELECT atributo_valor_padrao.valor_padrao
                      FROM administracao.atributo_valor_padrao
                     WHERE atributo_valor_padrao.cod_modulo = 12
                       AND atributo_valor_padrao.cod_cadastro = 4
                       AND atributo_valor_padrao.cod_atributo = 5014
                       AND atributo_valor_padrao.cod_valor = (SELECT recuperaCadastroImobiliarioImovelUso('||inInscricaoMunicipal||'))) AS uso,
                    
                    --Conservação do Tipo de Edificação
                    (SELECT atributo_valor_padrao.valor_padrao
                      FROM administracao.atributo_valor_padrao
                     WHERE atributo_valor_padrao.cod_modulo = 12
                       AND atributo_valor_padrao.cod_cadastro = 5
                       AND atributo_valor_padrao.cod_atributo = 5023
                       AND atributo_valor_padrao.cod_valor = (SELECT recuperaCadastroImobiliarioTipoDeEdificacaoConservacao('||inCodTipo||','||inCodConstrucao||'))) AS conservacao,
                    
                    --padrao de construcao do tipo de edificação
                    (SELECT atributo_valor_padrao.valor_padrao
                      FROM administracao.atributo_valor_padrao
                     WHERE atributo_valor_padrao.cod_modulo = 12
                       AND atributo_valor_padrao.cod_cadastro = 5
                       AND atributo_valor_padrao.cod_atributo = 5022
                       AND atributo_valor_padrao.cod_valor = (SELECT recuperaCadastroImobiliarioTipoDeEdificacaoPadraoDeConstrucao('||inCodTipo||','||inCodConstrucao||'))) AS padrao,
                    
                    --tipo de construcao do tipo de edificação
                    (SELECT atributo_valor_padrao.valor_padrao
                      FROM administracao.atributo_valor_padrao
                     WHERE atributo_valor_padrao.cod_modulo = 12
                       AND atributo_valor_padrao.cod_cadastro = 5
                       AND atributo_valor_padrao.cod_atributo = 5017
                       AND atributo_valor_padrao.cod_valor = (SELECT recuperaCadastroImobiliarioTipoDeEdificacaoTipoDeConstrucao('||inCodTipo||','||inCodConstrucao||'))) AS tipo
            ';
        ELSE
            stSql := '
                SELECT
                    --consulta do uso do imóvel
                    (SELECT atributo_valor_padrao.valor_padrao
                      FROM administracao.atributo_valor_padrao
                     WHERE atributo_valor_padrao.cod_modulo = 12
                       AND atributo_valor_padrao.cod_cadastro = 4
                       AND atributo_valor_padrao.cod_atributo = 5014
                       AND atributo_valor_padrao.cod_valor = (SELECT recuperaCadastroImobiliarioImovelUso('||inInscricaoMunicipal||'))) AS uso,
                    
                    --Conservação do Tipo de Edificação
                    ''''::varchar AS conservacao,
                    
                    --padrao de construcao do tipo de edificação
                    ''''::varchar AS padrao,
                    
                    --tipo de construcao do tipo de edificação
                    ''''::varchar AS tipo
            ';
        END IF;

        FOR reRegistro IN EXECUTE stSql LOOP
            return next reRegistro;
        END LOOP;
        
    END;
$$ language 'plpgsql';
