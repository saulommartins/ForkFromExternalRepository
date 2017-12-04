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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GA_1912.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.91.2
*/


-----------------
--  Ticket #13591
-----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

    stUsuario       VARCHAR;
    stObjeto        TIMESTAMP;

    stSQL           VARCHAR;
    reRECORD        RECORD;
    inCountProc     INTEGER := 0;
    inCountElse     INTEGER := 0;
    inTeste         INTEGER;
    varAchouTr      VARCHAR;

    stRetorno       VARCHAR;

BEGIN

    SELECT tgname
      INTO varAchouTr
      FROM pg_trigger
     WHERE tgname = 'tr_atualiza_ultimo_andamento';

    IF FOUND THEN

        DROP TRIGGER tr_atualiza_ultimo_andamento ON sw_andamento;

    END IF;

    stSQL := '          SELECT DISTINCT ON (ano_exercicio, cod_processo) *
                          FROM sw_processo
                      ORDER BY ano_exercicio
                             , cod_processo
             ';

    FOR reRECORD IN EXECUTE stSQL LOOP

        stUsuario := 'numcgm = '||reRECORD.cod_usuario;

        SELECT timestamp
          INTO stObjeto
          FROM administracao.auditoria
         WHERE cod_acao = 23
           AND objeto ilike stUsuario;

        IF FOUND THEN

            SELECT cod_andamento
              INTO inTeste
              FROM sw_andamento
             WHERE cod_processo  = reRECORD.cod_processo
               AND ano_exercicio = reRECORD.ano_exercicio
               AND cod_andamento = 0;

            IF NOT FOUND THEN

                  INSERT
                    INTO sw_andamento
                  SELECT 0 AS cod_andamento
                       , cod_processo
                       , ano_exercicio
                       , cod_orgao
                       , cod_unidade
                       , cod_departamento
                       , cod_setor
                       , ano_exercicio_setor
                       , cod_usuario
                       , timestamp
                    FROM sw_andamento
                   WHERE cod_processo  = reRECORD.cod_processo
                     AND ano_exercicio = reRECORD.ano_exercicio
                ORDER BY cod_andamento DESC LIMIT 1;

            END IF;

            inCountProc := inCountProc + 1;

        ELSE

            SELECT cod_andamento
              INTO inTeste
              FROM sw_andamento
             WHERE cod_processo  = reRECORD.cod_processo
               AND ano_exercicio = reRECORD.ano_exercicio
               AND cod_andamento = 0;

            IF NOT FOUND THEN

                INSERT
                  INTO sw_andamento
                SELECT 0 AS cod_andamento
                     , proc.cod_processo
                     , proc.ano_exercicio
                     , usuario.cod_orgao
                     , usuario.cod_unidade
                     , usuario.cod_departamento
                     , usuario.cod_setor
                     , usuario.ano_exercicio
                     , usuario.numcgm
                     , proc.timestamp
                  FROM sw_processo                  AS proc
            INNER JOIN administracao.usuario        AS usuario
                    ON usuario.numcgm     = proc.cod_usuario
                 WHERE proc.cod_processo  = reRECORD.cod_processo
                   AND proc.ano_exercicio = reRECORD.ano_exercicio;

            END IF;

            inCountElse := inCountElse + 1;

        END IF;

    END LOOP;

    CREATE TRIGGER tr_atualiza_ultimo_andamento AFTER INSERT OR UPDATE ON sw_andamento FOR EACH ROW EXECUTE PROCEDURE fn_atualiza_ultimo_andamento();

END;

$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();
