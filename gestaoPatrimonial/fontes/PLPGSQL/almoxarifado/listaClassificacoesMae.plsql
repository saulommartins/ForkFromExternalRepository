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
 * Função PLPGSQL
 * Data de Criação   : 20/07/2006


 * @author Analista      -------
 * @author Desenvolvedor Diego
 
 * @package URBEM
 * @subpackage 

 $Id: listaClassificacoesMae.plsql 59612 2014-09-02 12:00:51Z gelson $
 */

CREATE OR REPLACE FUNCTION almoxarifado.fn_lista_classificacoes_mae(INTEGER, VARCHAR) RETURNS SETOF colunasListaClassificacoesMae AS $$
DECLARE
    inCodCatalogo       ALIAS FOR $1;
    stCodEstrutural     ALIAS FOR $2;
    stEstruturalPadrao  VARCHAR := '';
    stEstruturalTemp    VARCHAR := '';
    stEstruturalMaes    VARCHAR := '';
    stSql               VARCHAR := '';
    arEstrutural        VARCHAR[];
    inIndex             INTEGER := 1;
    inIndexInterno      INTEGER := 1;
    reRegistro          RECORD;
    rwRegistro          colunasListaClassificacoesMae%ROWTYPE;
BEGIN
    arEstrutural := string_to_array(stCodEstrutural,'.'); 

    -- Busca o inicio da string que deve ser completado com zeros
    WHILE arEstrutural[inIndex] IS NOT NULL LOOP
        stEstruturalTemp := '';
        inIndexInterno     := 1;
        stEstruturalPadrao := stEstruturalPadrao||'.'||arEstrutural[inIndex];
        
        -- completa o resto da string com zeros
        WHILE arEstrutural[inIndexInterno] IS NOT NULL LOOP 
            IF inIndexInterno >= (inIndex+1) THEN
                stEstruturalTemp := stEstruturalTemp||repeat('0', length(arEstrutural[inIndexInterno]))||'.';
            END IF;

            inIndexInterno := inIndexInterno + 1;
        END LOOP;    
    
        -- Guarda em stEstrutural a mãe
        stEstruturalTemp := stEstruturalPadrao||'.'||stEstruturalTemp;                              -- Concatena o padrão com os zeros
        stEstruturalTemp := substr(stEstruturalTemp,2,length(stEstruturalTemp));                    -- remove primeiro ponto
        stEstruturalTemp := quote_literal((substr(stEstruturalTemp,1,length(stEstruturalTemp)-1)));   -- remove ultimo ponto
        stEstruturalMaes := stEstruturalMaes||stEstruturalTemp||',';                                -- String que guardas as mães

        inIndex := inIndex + 1;
    END LOOP;

    IF trim(stEstruturalMaes) != '' THEN
        -- Remove o ponto e virgula do final da string
        stEstruturalMaes := substr(stEstruturalMaes,1,length(stEstruturalMaes)-1);
    
        stSql := ' SELECT catalogo_classificacao.cod_estrutural
                        , catalogo_classificacao.descricao
                        , publico.fn_mascara_dinamica(catalogo_niveis.mascara, classificacao_nivel.cod_nivel::varchar)  as cod_nivel
                        , almoxarifado.catalogo_niveis.descricao as descricao_nivel
                        , catalogo_niveis.mascara
                        , classificacao_nivel.nivel
                     FROM almoxarifado.catalogo_classificacao
               INNER JOIN almoxarifado.classificacao_nivel
                       ON catalogo_classificacao.cod_catalogo = classificacao_nivel.cod_catalogo
                      AND catalogo_classificacao.cod_classificacao = classificacao_nivel.cod_classificacao
                      AND publico.fn_nivel(catalogo_classificacao.cod_estrutural) = classificacao_nivel.nivel
               INNER JOIN almoxarifado.catalogo_niveis
                       ON classificacao_nivel.nivel = catalogo_niveis.nivel
                      AND classificacao_nivel.cod_catalogo = catalogo_niveis.cod_catalogo
                    WHERE catalogo_classificacao.cod_catalogo = '|| inCodCatalogo ||'
                      AND catalogo_classificacao.cod_estrutural IN ( '|| stEstruturalMaes ||' )
                 ORDER BY cod_estrutural';
    
        FOR reRegistro IN EXECUTE stSql LOOP 
            rwRegistro.cod_estrutural   := reRegistro.cod_estrutural;
            rwRegistro.descricao        := reRegistro.descricao;
            rwRegistro.cod_nivel        := reRegistro.cod_nivel;
            rwRegistro.descricao_nivel  := reRegistro.descricao_nivel;
            rwRegistro.mascara          := reRegistro.mascara;
            rwRegistro.nivel            := reRegistro.nivel;

            RETURN NEXT rwRegistro;
        END LOOP;
    END IF;
END;
$$ LANGUAGE 'plpgsql';
