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
    * @author Analista:      Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    $Id: FTCEMGFolhaPagamentoPessoa.plsql 65246 2016-05-04 18:16:04Z michel $
*/
CREATE OR REPLACE FUNCTION tcemg.folha_pagamento_pessoa(VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE 
    stExercicio   ALIAS FOR $1;
    stMes         ALIAS FOR $2;
    stSql         VARCHAR;
    stSqlAux      VARCHAR;
    stChave       VARCHAR;
    stChaveAux    VARCHAR;
    reRegistro    RECORD;
    reRegistroAux RECORD;

BEGIN
    stSql := '
    SELECT *
      FROM (
      SELECT 10 AS tipo_registro
           , CASE WHEN sw_cgm.cod_pais BETWEEN 0 AND 1
                  THEN 1
                  ELSE 3
              END AS tipo_documento
           , sw_cgm_pessoa_fisica.cpf AS nro_documento
           , sem_acentos(sw_cgm.nom_cgm) AS nome
           , UPPER(COALESCE(sw_cgm_pessoa_fisica.sexo,''M'')) as sexo
           , TO_CHAR(sw_cgm_pessoa_fisica.dt_nascimento,''ddmmyyyy'') as dt_nascimento
           , ''''::VARCHAR as tipo_cadastro
           , ''''::VARCHAR AS justificativa_alteracao
           , sw_cgm.numcgm
        FROM SW_CGM
  INNER JOIN sw_cgm_pessoa_fisica
          ON SW_CGM.numcgm = sw_cgm_pessoa_fisica.numcgm
  INNER JOIN (
              SELECT servidor.numcgm
                FROM pessoal.servidor
               UNION
              SELECT pensionista.numcgm
                FROM pessoal.pensionista
             ) as pessoal
          ON pessoal.numcgm = SW_CGM.numcgm
   LEFT JOIN tcemg.arquivo_folha_pessoa
          ON SW_CGM.numcgm = arquivo_folha_pessoa.numcgm
       WHERE SW_CGM.numcgm > 0

       UNION

      SELECT 10 AS tipo_registro
           , 2 AS tipo_documento
           , sw_cgm_pessoa_juridica.cnpj AS nro_documento
           , sem_acentos(sw_cgm.nom_cgm) AS nome
           , '''' as sexo
           , '''' as dt_nascimento
           , ''''::VARCHAR as tipo_cadastro
           , ''''::VARCHAR AS justificativa_alteracao
           , sw_cgm.numcgm
        FROM tcemg.teto_remuneratorio
  INNER JOIN orcamento.entidade
          ON entidade.exercicio    = teto_remuneratorio.exercicio
         AND entidade.cod_entidade = teto_remuneratorio.cod_entidade
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm
  INNER JOIN sw_cgm_pessoa_juridica
          ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
       WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE(''01/'||stMes||'/'||stExercicio||''',''dd/mm/yyyy''))
         AND teto_remuneratorio.vigencia = ( SELECT MAX(TR.vigencia)
                                                   FROM tcemg.teto_remuneratorio AS TR
                                                  WHERE TR.vigencia <= last_day(TO_DATE(''01/'||stMes||'/'||stExercicio||''',''dd/mm/yyyy''))
                                                    AND TR.cod_entidade = teto_remuneratorio.cod_entidade
                                               )
           ) AS sw_cgm
    ORDER BY sw_cgm.numcgm
    ';

    stSqlAux := ' SELECT * FROM tcemg.arquivo_folha_pessoa ORDER BY numcgm';

    FOR reRegistro IN EXECUTE stSql LOOP

        IF EXISTS (SELECT 1 FROM tcemg.arquivo_folha_pessoa) THEN

            FOR reRegistroAux IN EXECUTE stSqlAux LOOP

                stChave    := reRegistro.numcgm::varchar||reRegistro.nome::varchar;
                stChaveAux := reRegistroAux.numcgm::varchar||reRegistroAux.nome::varchar;                
                --Verificando se o registro sofreu alteracao
                IF stChave != stChaveAux THEN
                    --Verifica se o registro é novo ou sofreu alteracao em algum campo
                    IF reRegistro.numcgm = reRegistroAux.numcgm THEN
                        --Update na tabela de registro do arquivo
                        UPDATE tcemg.arquivo_folha_pessoa
                            SET   numcgm        = reRegistro.numcgm
                                , ano           = stExercicio
                                , mes           = stMes
                                , nome          = reRegistro.nome
                                , alterado      = true
                        WHERE numcgm = reRegistro.numcgm;
                        --Alterando tipo de registro 'Alteracao'
                        reRegistro.tipo_cadastro := '2';
                        reRegistro.justificativa_alteracao := 'Alteração de Cadastro';
                        RETURN NEXT reRegistro;
                    ELSE
                        IF NOT EXISTS (SELECT 1 FROM tcemg.arquivo_folha_pessoa WHERE numcgm = reRegistro.numcgm ) THEN
                            --Registro Novo
                            INSERT INTO tcemg.arquivo_folha_pessoa 
                                VALUES( reRegistro.numcgm
                                        ,stExercicio
                                        ,stMes
                                        ,reRegistro.nome
                                        ,false );
                            --Alterando tipo de registro 'Novo'
                            reRegistro.tipo_cadastro := '1';
                            RETURN NEXT reRegistro;
                        END IF;
                    END IF;
                --Caso as chaves forem iguais não envia nada para o arquivo nem no resultado da consulta
                ELSE
                    IF EXISTS( SELECT 1 FROM tcemg.arquivo_folha_pessoa WHERE numcgm = reRegistroAux.numcgm AND ano = stExercicio AND mes = stMes ) THEN
                        IF reRegistroAux.alterado = true THEN
                            reRegistro.tipo_cadastro := '2';
                            reRegistro.justificativa_alteracao := 'Alteração de Cadastro';
                        ELSE
                            reRegistro.tipo_cadastro := '1';
                        END IF;
                        RETURN NEXT reRegistro;
                    ELSE
                        CONTINUE;
                    END IF;
                END IF;
            END LOOP;
        --Se não houver dados na tabela de arquivo_folha_pessoa
        ELSE
            --Registro Novo
            INSERT INTO tcemg.arquivo_folha_pessoa 
                VALUES( reRegistro.numcgm
                        ,stExercicio
                        ,stMes
                        ,reRegistro.nome
                        ,false );
            --Alterando tipo de registro 'Novo'
            reRegistro.tipo_cadastro := '1';
            RETURN NEXT reRegistro;
        END IF;
    END LOOP;

RETURN;
END;
$$ LANGUAGE 'plpgsql';
