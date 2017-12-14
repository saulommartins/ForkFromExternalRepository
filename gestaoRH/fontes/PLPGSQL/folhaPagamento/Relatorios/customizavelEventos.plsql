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
/* customizavelEventos
 *
 * Data de Criação : 15/04/2009


 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 */
CREATE OR REPLACE FUNCTION customizavelEventos(VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER[]) RETURNS SETOF colunasCustomizavelEventos AS $$
DECLARE
    stEntidade                  ALIAS FOR $1;
    stTipoFiltro                ALIAS FOR $2;
    stValoresFiltro             ALIAS FOR $3;
    stExercicio                 ALIAS FOR $4;
    stSituacao                  ALIAS FOR $5;
    inCodConfiguracao           ALIAS FOR $6;
    inCodComplementar           ALIAS FOR $7;
    inCodPeriodoMovimentacao    ALIAS FOR $8;
    inEventos                   ALIAS FOR $9;
    arEventos                   ALIAS FOR $10;

    rwCustomizavelEventos       colunasCustomizavelEventos%ROWTYPE;
    stSql                       VARCHAR;
    stConfiguracao              VARCHAR:='cgm,o,l,f,c,e,ef,pp,ca';
    reRegistro                  RECORD;
    reEvento                    RECORD;
    inIndex                     INTEGER;
    arValor                     NUMERIC[]:='{0,0,0,0,0,0}';
    arQuantidade                NUMERIC[]:='{0,0,0,0,0,0}';
    arQuantidadeParcela            INTEGER[]:='{0,0,0,0,0,0}';
    crCursor                    REFCURSOR;
    boRegistroEvento            BOOLEAN:=FALSE;
BEGIN
    IF stSituacao = 'E' THEN
        stSql := 'SELECT cod_contrato
                       , registro
                       , nom_cgm
                       , cpf
                       , desc_orgao
                       , '''' as desc_local
                       , desc_funcao
                       , '''' as desc_cargo
                       , '''' as desc_especialidade_cargo
                       , desc_especialidade_funcao
                       , '''' as desc_padrao
                    FROM recuperarContratoPensionista('''||stConfiguracao||''','''||stEntidade||''','||inCodPeriodoMovimentacao||','''||stTipoFiltro||''','''||stValoresFiltro||''','''||stExercicio||''')';
    ELSE
        stSql := 'SELECT cod_contrato
                       , registro
                       , nom_cgm
                       , cpf
                       , desc_orgao
                       , desc_local
                       , desc_funcao
                       , desc_cargo
                       , desc_especialidade_cargo
                       , desc_especialidade_funcao
                       , desc_padrao
                    FROM recuperarContratoServidor('''||stConfiguracao||''','''||stEntidade||''','||inCodPeriodoMovimentacao||','''||stTipoFiltro||''','''||stValoresFiltro||''','''||stExercicio||''')
                  ';
        IF stSituacao <> 'T' THEN
           stSql := stSql||' WHERE recuperarSituacaoDoContrato(cod_contrato,'||inCodPeriodoMovimentacao||','''||stEntidade||''') = '''||stSituacao||'''';
        END IF;
    END IF;
    FOR reRegistro IN EXECUTE stSql LOOP
        boRegistroEvento := FALSE;
        arValor          :='{0,0,0,0,0,0}';
        arQuantidade     :='{0,0,0,0,0,0}';
        arQuantidadeParcela :='{0,0,0,0,0,0}';
        FOR inIndex IN 1..inEventos LOOP
            IF inCodConfiguracao = 0 THEN
                stSql := '   SELECT sum(evento_complementar_calculado.valor) as valor
                                  , sum(evento_complementar_calculado.quantidade) as quantidade
                                  , registro_evento_complementar_parcela.parcela
                               FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                         INNER JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado
                                 ON evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
                                AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                                AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                                AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_complementar_parcela
                                 ON registro_evento_complementar_parcela.cod_evento = evento_complementar_calculado.cod_evento
                                AND registro_evento_complementar_parcela.cod_registro = evento_complementar_calculado.cod_registro
                                AND registro_evento_complementar_parcela.timestamp = evento_complementar_calculado.timestamp_registro
                              WHERE registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                                AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                AND evento_complementar_calculado.cod_evento = '||arEventos[inIndex]||'
                                AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                           GROUP BY parcela';
            END IF;
            IF inCodConfiguracao = 1 THEN
                stSql := '   SELECT sum(evento_calculado.valor) as valor
                                  , sum(evento_calculado.quantidade) as quantidade
                                  , registro_evento_parcela.parcela
                               FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                         INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                                 ON evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                          LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_parcela
                                 ON registro_evento_parcela.cod_evento = evento_calculado.cod_evento
                                AND registro_evento_parcela.cod_registro = evento_calculado.cod_registro
                                AND registro_evento_parcela.timestamp = evento_calculado.timestamp_registro
                              WHERE registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                AND evento_calculado.cod_evento = '||arEventos[inIndex]||'
                                AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                           GROUP BY parcela';
            END IF;
            IF inCodConfiguracao = 2 THEN
                stSql := '   SELECT sum(evento_ferias_calculado.valor) as valor
                                  , sum(evento_ferias_calculado.quantidade) as quantidade
                                  , registro_evento_ferias_parcela.parcela
                               FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                         INNER JOIN folhapagamento'||stEntidade||'.evento_ferias_calculado
                                 ON evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
                                AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                                AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                                AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_ferias_parcela
                                 ON registro_evento_ferias_parcela.cod_evento = evento_ferias_calculado.cod_evento
                                AND registro_evento_ferias_parcela.cod_registro = evento_ferias_calculado.cod_registro
                                AND registro_evento_ferias_parcela.timestamp = evento_ferias_calculado.timestamp_registro
                              WHERE registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                AND evento_ferias_calculado.cod_evento = '||arEventos[inIndex]||'
                                AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                           GROUP BY parcela';
            END IF;
            IF inCodConfiguracao = 3 THEN
                stSql := '   SELECT sum(evento_decimo_calculado.valor) as valor
                                  , sum(evento_decimo_calculado.quantidade) as quantidade
                                  , registro_evento_decimo_parcela.parcela
                               FROM folhapagamento'||stEntidade||'.registro_evento_decimo
                         INNER JOIN folhapagamento'||stEntidade||'.evento_decimo_calculado
                                 ON evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro
                                AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                                AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                                AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_decimo_parcela
                                 ON registro_evento_decimo_parcela.cod_evento = evento_decimo_calculado.cod_evento
                                AND registro_evento_decimo_parcela.cod_registro = evento_decimo_calculado.cod_registro
                                AND registro_evento_decimo_parcela.timestamp = evento_decimo_calculado.timestamp_registro
                              WHERE registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                AND evento_decimo_calculado.cod_evento = '||arEventos[inIndex]||'
                                AND registro_evento_decimo.cod_contrato = '||reRegistro.cod_contrato||'
                           GROUP BY parcela';
            END IF;
            IF inCodConfiguracao = 4 THEN
                stSql := '   SELECT sum(evento_rescisao_calculado.valor) as valor
                                  , sum(evento_rescisao_calculado.quantidade) as quantidade
                                  , registro_evento_rescisao_parcela.parcela
                               FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                         INNER JOIN folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                 ON evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro
                                AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                                AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                                AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela
                                 ON registro_evento_rescisao_parcela.cod_evento = evento_rescisao_calculado.cod_evento
                                AND registro_evento_rescisao_parcela.cod_registro = evento_rescisao_calculado.cod_registro
                                AND registro_evento_rescisao_parcela.timestamp = evento_rescisao_calculado.timestamp_registro
                              WHERE registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                AND evento_rescisao_calculado.cod_evento = '||arEventos[inIndex]||'
                                AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                           GROUP BY parcela';
            END IF;

            OPEN crCursor FOR EXECUTE stSql;
                FETCH crCursor INTO reEvento;
            CLOSE crCursor;
            arValor[inIndex]      := reEvento.valor;
            arQuantidade[inIndex] := reEvento.quantidade;
            arQuantidadeParcela[inIndex] := reEvento.parcela;
            IF reEvento.valor IS NOT NULL OR reEvento.quantidade IS NOT NULL THEN
                boRegistroEvento := TRUE;
            END IF;
        END LOOP;

        IF boRegistroEvento IS TRUE THEN
            rwCustomizavelEventos.cod_contrato               := reRegistro.cod_contrato;
            rwCustomizavelEventos.registro                   := reRegistro.registro;
            rwCustomizavelEventos.nom_cgm                    := reRegistro.nom_cgm;
            rwCustomizavelEventos.cpf                        := reRegistro.cpf;
            rwCustomizavelEventos.desc_orgao                 := reRegistro.desc_orgao;
            rwCustomizavelEventos.desc_local                 := reRegistro.desc_local;
            rwCustomizavelEventos.desc_funcao                := reRegistro.desc_funcao;
            rwCustomizavelEventos.desc_cargo                 := reRegistro.desc_cargo;
            rwCustomizavelEventos.desc_especialidade_cargo   := reRegistro.desc_especialidade_cargo;
            rwCustomizavelEventos.desc_especialidade_funcao  := reRegistro.desc_especialidade_funcao;
            rwCustomizavelEventos.desc_padrao                := reRegistro.desc_padrao;
            rwCustomizavelEventos.valor1                     := arValor[1];
            rwCustomizavelEventos.quantidade1                := arQuantidade[1];
            rwCustomizavelEventos.quantidade1Parcela         := arQuantidadeParcela[1];
            rwCustomizavelEventos.valor2                     := arValor[2];
            rwCustomizavelEventos.quantidade2                := arQuantidade[2];
            rwCustomizavelEventos.quantidade2Parcela         := arQuantidadeParcela[2];
            rwCustomizavelEventos.valor3                     := arValor[3];
            rwCustomizavelEventos.quantidade3                := arQuantidade[3];
            rwCustomizavelEventos.quantidade3Parcela         := arQuantidadeParcela[3];
            rwCustomizavelEventos.valor4                     := arValor[4];
            rwCustomizavelEventos.quantidade4                := arQuantidade[4];
            rwCustomizavelEventos.quantidade4Parcela         := arQuantidadeParcela[4];
            rwCustomizavelEventos.valor5                     := arValor[5];
            rwCustomizavelEventos.quantidade5                := arQuantidade[5];
            rwCustomizavelEventos.quantidade5Parcela         := arQuantidadeParcela[5];
            rwCustomizavelEventos.valor6                     := arValor[6];
            rwCustomizavelEventos.quantidade6                := arQuantidade[6];
            rwCustomizavelEventos.quantidade6Parcela         := arQuantidadeParcela[6];
            RETURN NEXT rwCustomizavelEventos;
        END IF;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';


