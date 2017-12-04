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
* Script de DDL e DML
*
* Versao 2.05.3
*
* Fabio Bertoldi - 20160622
*
*/

----------------
-- Ticket #23884
----------------

SELECT atualizarbanco('UPDATE pessoal.causa_rescisao SET cod_sefip_saida = 2 WHERE num_causa = 11;');

----------------
-- Ticket #23882
----------------

select atualizarBanco('
                        CREATE OR REPLACE FUNCTION ponto.fn_atualiza_ultimo_timestamp_escala( )
                           RETURNS TRIGGER AS $$
                        DECLARE
                           rEscalaAtual      RECORD;
                           iCodEscala        INTEGER;
                           tNewTimestamp     TIMESTAMP;
                           cAux              VARCHAR;

                        BEGIN

                           IF TG_OP=''INSERT'' THEN
                              --
                              -- Define a escala a ser inserida
                              --
                              iCodEscala      := NEW.cod_escala;
                              tNewTimestamp   := NEW.timestamp;

                              --
                              -- Verifica a existencia da ultima escala
                              --
                              SELECT escala.*
                                INTO rEscalaAtual
                                FROM ponto.escala
                               WHERE escala.cod_escala = iCodEscala
                              ;
                              IF FOUND THEN
                                 tNewTimestamp := (''now''::TEXT)::TIMESTAMP(3) WITH TIME ZONE;
                                 IF COALESCE(rEscalaAtual.ultimo_timestamp, ''1800-01-01'') <= tNewTimestamp  THEN
                                    UPDATE ponto.escala
                                       SET ultimo_timestamp =  tNewTimestamp
                                     WHERE cod_escala       = iCodEscala
                                    ;
                                 ELSE
                                    cAux := TO_CHAR(iCodEscala,''9999'');
                                    RAISE EXCEPTION ''Tabela ponto.escala inconsistente, contate suporte. Escala:%'', cAux;
                                 END IF;
                              ELSE
                                 RAISE EXCEPTION ''Falha de integridade referencial, tabela ponto.escala.: %'', TG_OP;
                                 RAISE EXCEPTION ''Código escala: %'', iCodEscala;
                              END IF;
                           ELSE
                              RAISE EXCEPTION ''Operação não permitida para tabela ponto.escala.: %'', TG_OP;
                           END IF;

                           RETURN NEW;
                        END;
                        $$ LANGUAGE plpgsql;
');

select atualizarBanco('
                        CREATE OR REPLACE FUNCTION ponto.fn_atualiza_ultimo_timestamp_conf_ponto( )
                                          RETURNS TRIGGER AS $$
                        DECLARE
                           rPontoAtual       RECORD;
                           iCodConfiguracao  INTEGER;
                           tNewTimestamp     TIMESTAMP;
                           cAux              VARCHAR;

                        BEGIN
                           IF TG_OP=''INSERT'' THEN
                              --
                              -- Define a configuracao a ser inserida
                              --
                              iCodConfiguracao  := NEW.cod_configuracao;
                              tNewTimestamp     := NEW.timestamp;

                              --
                              -- Verifica a existencia da ultima configuracao
                              --
                              SELECT configuracao_relogio_ponto.*
                                INTO rPontoAtual
                                FROM ponto.configuracao_relogio_ponto
                               WHERE configuracao_relogio_ponto.cod_configuracao = iCodConfiguracao;

                              IF FOUND THEN
                                 tNewTimestamp := (''now''::text)::TIMESTAMP(3) WITH TIME ZONE ;
                                 If COALESCE(rPontoAtual.ultimo_timestamp, ''1800-01-01'') <= tNewTimestamp  THEN
                                    UPDATE ponto.configuracao_relogio_ponto
                                       SET ultimo_timestamp = tNewTimestamp
                                     WHERE cod_configuracao = iCodConfiguracao;
                                 ELSE
                                    cAux := TO_CHAR(iCodConfiguracao,''9999'');
                                    RAISE EXCEPTION ''Tabela ponto.configuracao_relogio_ponto inconsistente, contate suporte. Configuração:%'', cAux;
                                 END IF;
                              ELSE
                                 RAISE EXCEPTION ''Falha de integridade referencial, tabela ponto.configuracao_relogio_ponto.: %'', TG_OP;
                                 RAISE EXCEPTION ''Código configuração: %'', iCodConfiguracao;
                              END IF;
                           ELSE
                              RAISE EXCEPTION ''Operação não permitida para tabela ponto.configuracao_relogio_ponto.: %'', TG_OP;
                           END IF;

                           RETURN NEW;

                        END;
                        $$ LANGUAGE plpgsql;
');


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodENtPref    INTEGER;
    stCriaTRG       VARCHAR;
    stSQL           VARCHAR;
    reRecord        RECORD;
BEGIN
    SELECT valor
      INTO inCodENtPref
      FROM administracao.configuracao
     WHERE exercicio  = '2016'
       AND cod_modulo = 8
       AND parametro  = 'cod_entidade_prefeitura'
         ;

    stSQL := '
                 SELECT '''' as entidade
               UNION
                 SELECT ''_''||cod_entidade AS entidade
                   FROM administracao.entidade_rh
                  WHERE exercicio     = ''2016''
                    AND cod_entidade != '|| inCodENtPref ||'
               GROUP BY cod_entidade
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
            stCriaTRG := 'DROP TRIGGER IF EXISTS tr_contrato_servidor_conta_salario_historico'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.contrato_servidor_conta_salario;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS tr_contrato_servidor_conta_salario_historico ON pessoal'|| reRecord.entidade ||'.contrato_servidor_conta_salario;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER tr_contrato_servidor_conta_salario_historico'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON pessoal'|| reRecord.entidade ||'.contrato_servidor_conta_salario FOR EACH ROW EXECUTE PROCEDURE pessoal'|| reRecord.entidade ||'.fn_contrato_servidor_conta_salario_historico();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS tr_configuracao_banco_horas'|| reRecord.entidade ||' ON ponto'|| reRecord.entidade ||'.configuracao_banco_horas;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER tr_configuracao_banco_horas'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON ponto'|| reRecord.entidade ||'.configuracao_banco_horas FOR EACH ROW EXECUTE PROCEDURE ponto'|| reRecord.entidade ||'.fn_atualiza_ultimo_timestamp_conf_ponto();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS tr_configuracao_horas_extras_2'|| reRecord.entidade ||' ON ponto'|| reRecord.entidade ||'.configuracao_horas_extras_2;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER tr_configuracao_horas_extras_2'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON ponto'|| reRecord.entidade ||'.configuracao_horas_extras_2 FOR EACH ROW EXECUTE PROCEDURE ponto'|| reRecord.entidade ||'.fn_atualiza_ultimo_timestamp_conf_ponto();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS tr_configuracao_parametros_gerais'|| reRecord.entidade ||' ON ponto'|| reRecord.entidade ||'.configuracao_parametros_gerais;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER tr_configuracao_parametros_gerais'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON ponto'|| reRecord.entidade ||'.configuracao_parametros_gerais FOR EACH ROW EXECUTE PROCEDURE ponto'|| reRecord.entidade ||'.fn_atualiza_ultimo_timestamp_conf_ponto();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS tr_atualiza_ultimo_timestamp_escala'|| reRecord.entidade ||' ON ponto'|| reRecord.entidade ||'.escala_turno;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER tr_atualiza_ultimo_timestamp_escala'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON ponto'|| reRecord.entidade ||'.escala_turno FOR EACH ROW EXECUTE PROCEDURE ponto'|| reRecord.entidade ||'.fn_atualiza_ultimo_timestamp_escala();';
            EXECUTE stCriaTRG;


            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_pensionista ON pessoal'|| reRecord.entidade ||'.contrato_pensionista';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_pensionista'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.contrato_pensionista';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_pensionista'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON pessoal'|| reRecord.entidade ||'.contrato_pensionista FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_pensionista();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor ON pessoal'|| reRecord.entidade ||'.contrato_servidor;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.contrato_servidor;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_servidor'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON pessoal'|| reRecord.entidade ||'.contrato_servidor FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria ON pessoal'|| reRecord.entidade ||'.aposentadoria;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.aposentadoria;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_aposentadoria'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON pessoal'|| reRecord.entidade ||'.aposentadoria FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria_excluida ON pessoal'|| reRecord.entidade ||'.aposentadoria_excluida;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria_excluida'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.aposentadoria_excluida;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_aposentadoria_excluida'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON pessoal'|| reRecord.entidade ||'.aposentadoria_excluida FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria_excluida();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor_caso_causa ON pessoal'|| reRecord.entidade ||'.contrato_servidor_caso_causa;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor_caso_causa'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.contrato_servidor_caso_causa;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_servidor_caso_causa'|| reRecord.entidade ||' BEFORE INSERT OR UPDATE ON pessoal'|| reRecord.entidade ||'.contrato_servidor_caso_causa FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor_caso_causa();';
            EXECUTE stCriaTRG;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();
