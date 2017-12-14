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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_calculoEconomico.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION fn_calculo_economico(varchar, varchar) returns boolean as '
DECLARE
     stGrupo                   ALIAS FOR $1;  
     stExercicio               ALIAS FOR $2;
     stSql                     varchar;
     stSqlGrupo                varchar;
     stTabela                  varchar;
     stSqlCalculoTributario    varchar;

     reRecord                  record;
     reRecord2                 record;
     reRecordGrupo             record;

     iCount                    integer := 0;    
     inCodGrupo                integer;
     inCodModulo               integer;
     inRegistro                integer;
     inExercicio               integer;

     boRetorno                 boolean;
 
BEGIN
    inCodModulo := criarbufferinteiro( ''inCodModulo'', 14                );
    inExercicio := criarbufferinteiro( ''inExercicio'', stExercicio::int  );
    inCodGrupo  := criarbufferinteiro( ''inCodGrupo'' , stGrupo::int      );


    stSqlGrupo := ''
                    SELECT acgr.cod_calculo
                      FROM arrecadacao.calculo_grupo_credito    AS acgr
                    
                      JOIN arrecadacao.calculo                  AS ac
                        ON ac.cod_calculo     = acgr.cod_calculo 
                       AND ac.ativo           = TRUE
      
                 LEFT JOIN arrecadacao.lancamento_calculo       AS alc
                        ON alc.cod_calculo    = acgr.cod_calculo
      
                     WHERE cod_grupo          = ''||stGrupo||''  
                       AND acgr.ano_exercicio = ''||stExercicio||'' 
                       AND alc.cod_calculo IS NULL;
  
                  '';

    FOR reRecordGrupo IN EXECUTE stSqlGrupo LOOP
            
           UPDATE  arrecadacao.calculo SET ativo = FALSE WHERE cod_calculo = reRecordGrupo.cod_calculo;

    END LOOP;



    stSql := ''
        SELECT
            ece.inscricao_economica
        FROM
            economico.cadastro_economico AS ece

        LEFT JOIN (
            SELECT
                BCE.*
            FROM
                economico.baixa_cadastro_economico AS BCE,
                (
                SELECT
                    MAX (TIMESTAMP) AS TIMESTAMP,
                    inscricao_economica
                FROM
                    economico.baixa_cadastro_economico
                GROUP BY
                    inscricao_economica
                ) AS BT
            WHERE
                BCE.inscricao_economica = BT.inscricao_economica AND
                BCE.timestamp = BT.timestamp
        ) be
        ON
            ece.inscricao_economica = be.inscricao_economica
        
        WHERE
            ( 
                (be.dt_inicio IS NULL) OR 
                (be.dt_inicio IS NOT NULL AND be.dt_termino IS NOT NULL)
                AND be.inscricao_economica = ece.inscricao_economica
            )
    '';


    -- cria tabelas temporarias para os calculos e erros
    create temp table calculos_mensagem ( cod_calculo int , mensagem varchar );
    create temp table calculos_correntes ( cod_calculo int , valor numeric);
    create temp table calculos_erro ( registro int , credito varchar, funcao varchar , erro boolean, valor numeric);

  
     --BEGIN  
     -- bloquear tabelas por causa dos inserts
     LOCK TABLE arrecadacao.calculo                      IN EXCLUSIVE MODE;
     LOCK TABLE arrecadacao.calculo_cgm                  IN EXCLUSIVE MODE;
     LOCK TABLE arrecadacao.imovel_calculo               IN EXCLUSIVE MODE;
     LOCK TABLE arrecadacao.cadastro_economico_calculo   IN EXCLUSIVE MODE;
     LOCK TABLE arrecadacao.log_calculo                  IN EXCLUSIVE MODE;
     LOCK TABLE arrecadacao.calculo_grupo_credito        IN EXCLUSIVE MODE;
   
    FOR reRecord IN EXECUTE stSql LOOP
           inRegistro  := criarbufferinteiro( ''inRegistro'', reRecord.inscricao_economica );

           boRetorno :=   CalculoEconomico ( );

           FOR reRecord2 IN EXECUTE '' SELECT * FROM calculos_correntes'' LOOP

                INSERT INTO arrecadacao.calculo_grupo_credito VALUES ( reRecord2.cod_calculo , inCodGrupo , stExercicio );

           END LOOP;

           DELETE FROM calculos_correntes;

          iCount := iCount + 1;


     END LOOP;

RETURN boRetorno; 
END;  
'language 'plpgsql';
