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
* PL para busca do primeiro vencimento base para calculo de acrescimos do lancamento
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_busca_livro_pagina.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.02
*/
/*
  As informações estao gravadas na tabela administracao.configuracao com o  parametro = 'livro_folha'
   sao 5 itens separados por ;(ponto e vírgula)
	numero_livro;
	exercicio ou sequencial referente ao livro;
	numero máximo de paginas por livro;
	numeracao sequencial ou por livro;
	exercicio do sistema
   exemplo : 1;sequencial;999;exercicio;2008
*/

CREATE OR REPLACE FUNCTION divida.fn_busca_livro_pagina() RETURNS VARCHAR AS $$
DECLARE    
    inLivro 		    integer;
    inFolha  		    integer;
    inQtdFolha          integer;
    valor 		        varchar;
    reRecord            RECORD;
    arConfig 		    VARCHAR[];
    stConfig		    varchar;  
    inInsere 		    integer;
    stSQL 		        varchar;
    crCursor		    REFCURSOR;
    inQtdUltimaFolha  	integer;
    stExercicio 		integer;
    inQtdFolhaLivro 	integer;
    inTMPFolha          integer;
BEGIN
    stSQL := '
        SELECT 
            valor
        FROM 
            administracao.configuracao
        WHERE 
            parametro = ''livro_folha''
            AND cod_modulo = 33
            AND exercicio = extract(year from now())::varchar;
    ';

    OPEN 
        crCursor
    FOR EXECUTE 
        stSql;

    FETCH 
        crCursor 
    INTO 
        stConfig;

    CLOSE 
        crCursor;

    arConfig = string_to_array(stConfig, ';' );
    SELECT
        extract(year from now())
    INTO
        stConfig;

    arConfig[5] := stConfig;
    IF arConfig[2] = 'sequencial' THEN
        SELECT 
            MAX(num_livro) 
        INTO 
            inLivro
        FROM
            divida.divida_ativa;

        arConfig[5] := '0000';
    ELSE
        SELECT 
            MAX(num_livro) 
        INTO 
            inLivro
        FROM
            divida.divida_ativa
        WHERE
            divida_ativa.exercicio_livro = arConfig[5];
    END IF;

    IF inLivro IS NULL THEN
        valor := 1||'-'||arConfig[1]||'-'||1||'-'||arConfig[5];
    ELSE
        SELECT 
            MAX(num_folha) 
        INTO
            inFolha
        FROM 
            divida.divida_ativa
        WHERE 
            num_livro = inLivro
            AND exercicio_livro = arConfig[5];

        SELECT 
            COUNT(*) 
        INTO 
            inQtdUltimaFolha
        FROM 
            divida.divida_ativa
        WHERE 
            num_livro = inLivro
            AND num_folha  = inFolha
            AND exercicio_livro = arConfig[5];

        inInsere := 0;
        IF inQtdUltimaFolha+1 > 54 THEN --vai estourar o limite por folha. precisa avancar a folha
            IF arConfig[4] = 'exercicio' THEN --esta por livro a contagem de folha
                IF inFolha+1 > arConfig[3]::INTEGER THEN --esta com nro de paginas maior q total de um livro. deve criar um livro novo
                    inInsere := 1;
                    inLivro := inLivro + 1;
                    inFolha := 1;
                ELSE --o livro tem paginas disponiveis. incrementa pagina
                    inFolha := inFolha + 1;
                END IF;
            ELSE -- a configuracao esta por sequencia geral.
                inTMPFolha := inFolha;
                WHILE ( inTMPFolha > arConfig[3]::INTEGER ) LOOP
                    inTMPFolha := inTMPFolha - arConfig[3];
                END LOOP;

                IF inTMPFolha = 0 THEN --nro de paginas maior q o total de um livro. deve criar um livro
                    inLivro := inLivro + 1;
                    inInsere := 1;
                END IF;

                inFolha := inFolha + 1;
            END IF;
        END IF;

        --se for usar um novo livro o insere eh 1 caso contrario eh 0
        valor := inInsere||'-'||inLivro||'-'||inFolha||'-'||arConfig[5];
    END IF;

    return valor;
END;
$$ LANGUAGE 'plpgsql'

