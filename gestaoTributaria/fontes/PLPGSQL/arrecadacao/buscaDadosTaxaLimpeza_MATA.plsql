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
* $Id: buscaDadosTaxaLimpeza_MATA.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.3  2007/07/10 20:21:22  dibueno
Melhoria referente à prevenção de retorno de valor ZERO para funcao de área

Revision 1.2  2007/02/09 15:29:44  dibueno
Alteração para buscar area e aliquota

Revision 1.1  2007/01/23 11:03:03  fabio
correção da tag de caso de uso
e movidos do cadastro econômico


*/

CREATE OR REPLACE FUNCTION economico.fn_busca_dados_taxa_limpeza( integer, integer )  RETURNS varchar AS '
DECLARE

    INIMOVEL ALIAS FOR $1;
    INEXERCICIO ALIAS FOR $2;


    INQUANTIDADE INTEGER;
    INZONA INTEGER;
    NUAREA NUMERIC;
    NUAREACALCULO NUMERIC;
    NUAREACONSTRUCAO NUMERIC;
    NUAREAIMOVEISLOTE NUMERIC;
    NUAREAIMOVEL NUMERIC;
    NUAREALOTE NUMERIC;
    NUISENTO NUMERIC;
    NUUSOSOLO NUMERIC;
    STESPECIFICACAOCOMERCIAL VARCHAR := '''';
    NUESPECIFICACAOCOMERCIAL NUMERIC;
    NUVALORZONA NUMERIC;
    NUZONA NUMERIC;
    STISENTO VARCHAR := '''';
    STUSOSOLO VARCHAR := '''';
    STZONA VARCHAR := '''';

    stRetorno VARCHAR := '''';
    stVazio VARCHAR := '''';
    stTrue VARCHAR := ''true'';
    stParametro_1 VARCHAR := ''Residencial'';
    stParametro_2 VARCHAR := ''Terreno'';
    stParametro_3 VARCHAR := ''Comercial/Serviços'';

BEGIN

    NUAREALOTE := IMOBILIARIO.FN_AREA_REAL(  INIMOVEL  );
    INQUANTIDADE := RECUPERAQUANTIDADEIMOVELPORLOTE(  INIMOVEL  );
    NUAREACONSTRUCAO := IMOBILIARIO.FN_CALCULA_AREA_IMOVEL(  INIMOVEL  );
    STUSOSOLO := RECUPERACADASTROIMOBILIARIOIMOVELUSODOSOLO(  INIMOVEL  );
    NUUSOSOLO := ARRECADACAO.FN_VC2NUM(  STUSOSOLO  );
    STZONA := RECUPERACADASTROIMOBILIARIOIMOVELZONA(  INIMOVEL  );
    NUZONA := ARRECADACAO.FN_VC2NUM(  STZONA  );

    STESPECIFICACAOCOMERCIAL := RECUPERACADASTROIMOBILIARIOIMOVELESPECIFICACAOCOMERCIAL(  INIMOVEL  );
    NUESPECIFICACAOCOMERCIAL := ARRECADACAO.FN_VC2NUM(  STESPECIFICACAOCOMERCIAL  );

    STISENTO := RECUPERACADASTROIMOBILIARIOIMOVELISENCAOTSU(  INIMOVEL  );
    NUISENTO := ARRECADACAO.FN_VC2NUM(  STISENTO  );



IF     NUISENTO  =   1 THEN
    NUZONA := 0.00;
END IF;
IF         NUZONA  >  0 THEN
    IF   NUUSOSOLO  =  1 THEN
            IF   INQUANTIDADE  >  1 THEN
                NUAREAIMOVEISLOTE := IMOBILIARIO.FN_CALCULA_AREA_IMOVEL_LOTE(  INIMOVEL  );
                NUAREAIMOVEL := IMOBILIARIO.FN_CALCULA_AREA_IMOVEL(  INIMOVEL  );

                IF ( NUAREAIMOVEISLOTE > 0 AND NUAREAIMOVEL > 0 ) THEN
                    NUAREACALCULO := (NUAREAIMOVEL *NUAREALOTE )/NUAREAIMOVEISLOTE ;
                ELSE
                    NUAREACALCULO := 0.00;
                END IF;
            ELSE
                NUAREACALCULO := NUAREALOTE;
            END IF;
            NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Terreno'' , STZONA , ''true'' , ''''  );
    ELSE
        NUAREACALCULO := NUAREACONSTRUCAO;
        IF   NUUSOSOLO  =  2 THEN
            NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Residencial'' , STZONA , ''true'' , ''''  );
        ELSE
            IF   NUUSOSOLO  =  4 THEN
                NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Industrial'' , STZONA , ''true'' , ''''  );
            ELSE
                IF   NUESPECIFICACAOCOMERCIAL  =  4  OR  NUESPECIFICACAOCOMERCIAL  =  6 THEN
                    NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Hospital e Escola'' , STZONA , ''true'' , ''''  );
                ELSE
                    IF     NUESPECIFICACAOCOMERCIAL  =   5 THEN
                        NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Hotel / Pousada'' , STZONA , ''true'' , '''' );
                    ELSE
                        IF   NUESPECIFICACAOCOMERCIAL  =  10 THEN
                            NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Banca de chapa'' , STZONA , '''' , ''true''  );
                        ELSE
                            IF   NUESPECIFICACAOCOMERCIAL  =  11 THEN
                                NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Banca de Feira'' , STZONA , '''' , ''true''  );
                            ELSE
                                IF   NUESPECIFICACAOCOMERCIAL  =  12 THEN
                                    NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Boxe de Mercado'' , STZONA , '''' , ''true''  );
                                ELSE
                                    NUVALORZONA := ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO(  160 , INEXERCICIO , ''Comercial/Serviços'' , STZONA , ''true'' , ''''  );
                                END IF;
                            END IF;
                        END IF;
                    END IF;
                END IF;
            END IF;
        END IF;
    END IF;
ELSE
    NUVALORZONA := 0;
    NUAREACALCULO := 0;
END IF;



    stRetorno := NUAREACALCULO||''§''||NUVALORZONA;

RETURN stRetorno;

END;
' LANGUAGE 'plpgsql';
