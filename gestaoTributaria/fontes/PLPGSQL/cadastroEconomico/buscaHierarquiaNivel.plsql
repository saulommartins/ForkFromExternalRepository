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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaHierarquiaNivel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.02.03
*               uc-05.02.07
*/

/*
$Log$
Revision 1.4  2006/09/15 10:19:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

--DROP FUNCTION sw_fn_busca_hierarquia_nivel(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER);
-- Funcao que retorna a hierarquia conforme a tabela informada.
-- Parametros de Entrada: tabela(atividade ou servico), cod_vigencia, cod_nivel, cod_pai
CREATE OR REPLACE FUNCTION sw_fn_busca_hierarquia_nivel(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER)
   RETURNS VARCHAR AS '

   DECLARE

        v_codigo            VARCHAR;
        v_campoCodValor     INTEGER;
        v_codVigenciaPai    INTEGER;
        v_codNivelPai       INTEGER;
        v_codPai            INTEGER;
        v_codAvo            INTEGER;
        v_mascara           INTEGER;

        p_tabela            ALIAS FOR $1;
        p_campoCod          ALIAS FOR $2;
        p_codVigencia       ALIAS FOR $3;
        p_codNivel          ALIAS FOR $4;
        p_codPai            ALIAS FOR $5;


   BEGIN

        --IF TRIM($1) <> ''0'' AND $2 <> 0 THEN

        /* BUSCA A MASCARA PARA O PRIMEIRO CODIGO */
        IF p_tabela = ''atividade'' THEN
            SELECT INTO v_campoCodValor, v_codVigenciaPai, v_codNivelPai,v_codPai, v_codAvo, v_mascara
                        t.cod_atividade,t.cod_vigencia_pai,t.cod_nivel_pai,t.cod_pai,t.cod_avo, LENGTH(TRIM(a.mascara))
            FROM    sw_atividade_cem t, sw_nivel_atividade a
            WHERE   t.cod_vigencia  = a.cod_vigencia
            AND     t.cod_nivel     = a.cod_nivel
            AND     t.cod_atividade = p_campoCod
            AND     t.cod_vigencia  = p_codVigencia
            AND     t.cod_nivel     = p_codNivel
            AND     t.cod_pai       = p_codPai ;
        ELSIF p_tabela = ''servico'' THEN
            SELECT INTO v_campoCodValor, v_codVigenciaPai, v_codNivelPai,v_codPai, v_codAvo, v_mascara
                        t.cod_servico,t.cod_vigencia_pai,t.cod_nivel_pai,t.cod_pai,t.cod_avo, LENGTH(TRIM(a.mascara))
            FROM    sw_servico_cem t, sw_nivel_servico a
            WHERE   t.cod_vigencia  = a.cod_vigencia
            AND     t.cod_nivel     = a.cod_nivel
            AND     t.cod_servico   = p_campoCod
            AND     t.cod_vigencia  = p_codVigencia
            AND     t.cod_nivel     = p_codNivel
            AND     t.cod_pai       = p_codPai ;
        END IF;

         IF FOUND THEN
            v_codigo := LPAD(p_campoCod,v_mascara,''0'');
            IF v_campoCodValor = ''0'' THEN
               RETURN v_codigo;
            END IF;

            /* BUSCA AS DEMAIS ATE O CAMPO COD_SUPERIOR FOR ZERO */
            LOOP
                IF p_tabela = ''atividade'' THEN
                    SELECT INTO v_campoCodValor, v_codVigenciaPai,v_codNivelPai,v_codPai, v_codAvo, v_mascara
                                t.cod_atividade,t.cod_vigencia_pai,t.cod_nivel_pai,t.cod_pai,t.cod_avo,LENGTH(TRIM(a.mascara))
                    FROM    sw_atividade_cem t, sw_nivel_atividade a
                    WHERE   t.cod_vigencia  = a.cod_vigencia
                    AND     t.cod_nivel     = a.cod_nivel
                    AND     t.cod_atividade = v_codPai
                    AND     t.cod_vigencia  = v_codVigenciaPai
                    AND     t.cod_nivel     = v_codNivelPai
                    AND     t.cod_pai       = v_codAvo ;
                ELSIF p_tabela = ''servico'' THEN
                    SELECT INTO v_campoCodValor, v_codVigenciaPai,v_codNivelPai,v_codPai, v_codAvo, v_mascara
                                t.cod_servico,t.cod_vigencia_pai,t.cod_nivel_pai,t.cod_pai,t.cod_avo,LENGTH(TRIM(a.mascara))
                    FROM    sw_servico_cem t, sw_nivel_servico a
                    WHERE   t.cod_vigencia  = a.cod_vigencia
                    AND     t.cod_nivel     = a.cod_nivel
                    AND     t.cod_servico   = v_codPai
                    AND     t.cod_vigencia  = v_codVigenciaPai
                    AND     t.cod_nivel     = v_codNivelPai
                    AND     t.cod_pai       = v_codAvo ;
                END IF;

               IF FOUND THEN
                  IF v_campoCodValor = ''0'' THEN
                     EXIT;
                  END IF;
                  v_codigo := LPAD(v_campoCodValor,v_mascara,''0'')||''.''||v_codigo;
               ELSE
                  EXIT ;
               END IF;

            END LOOP;
         END IF;
      --END IF;

      RETURN v_codigo;

   END;

'language 'plpgsql';

