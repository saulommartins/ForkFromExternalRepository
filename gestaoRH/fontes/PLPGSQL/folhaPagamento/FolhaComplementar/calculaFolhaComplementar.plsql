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

--    * Função PLSQL
--    * Data de Criação: 00/00/0000
-- 
-- 
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
-- 
--    * @package URBEM
--    * @subpackage
-- 
--    $Revision: 27431 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-01-09 11:13:43 -0200 (Qua, 09 Jan 2008) $
-- 
--    * Casos de uso: uc-04.05.10
--*/
CREATE OR REPLACE FUNCTION CALCULAFOLHACOMPLEMENTAR(INTEGER,INTEGER,BOOLEAN,VARCHAR,VARCHAR) RETURNS BOOLEAN AS $$
DECLARE
inCodContratoParametro          ALIAS FOR $1;
INCODCOMPLEMENTARTEMP           ALIAS FOR $2;
BOERRO                          ALIAS FOR $3;
stEntidadeParametro             ALIAS FOR $4;
stExercicioParametro            ALIAS FOR $5;
stEntidade                      VARCHAR := '';
stExercicioSistema              VARCHAR := '';
BORETORNO                       BOOLEAN := TRUE;
boRetornoContratoPeriodo        BOOLEAN := FALSE;
boRetornoFerias                 BOOLEAN := FALSE;
boRetornoComplementar           BOOLEAN := FALSE;
boRetornoFeriasComplementar     BOOLEAN := FALSE;
INCODCONTRATO                   INTEGER;
INCODESPECIALIDADE              INTEGER;
INCODFUNCAO                     INTEGER;
INCODPERIODOMOVIMENTACAO        INTEGER;
INCODREGIME                     INTEGER;
INCODSUBDIVISAO                 INTEGER;
INCODCOMPLEMENTAR               INTEGER;
INCODPREVIDENCIAOFICIAL         INTEGER;
INCODSERVIDOR                   INTEGER;
INCONTROLE                      INTEGER := 1;
INNUMCGM                        INTEGER;
INCODCONTRATOGERADORBENEFICIO   INTEGER;
inPensionista                   INTEGER := 0;
inCodRegistro                   INTEGER;
STDATAFINALCOMPETENCIA          VARCHAR := '';
STTIPOFOLHA                     VARCHAR := 'C';
stAutomatico                    VARCHAR;
stSql                           VARCHAR;
arCompetencia                   VARCHAR[];
reRegistro                      RECORD;
stTimestamp                     TIMESTAMP:=now()::timestamp(3);

BEGIN
    boRetorno                   := removerTodosBuffers();
    stEntidade                  := criarBufferTexto('stEntidade',stEntidadeParametro);
    inCodContrato               := inCodContratoParametro;
    INCODPERIODOMOVIMENTACAO    := PEGA0CODIGOPERIODOMOVIMENTACAOABERTA(  );
    STDATAFINALCOMPETENCIA      := PEGA0DATAFINALCOMPETENCIADOPERIODOMOVIMENTO(  INCODPERIODOMOVIMENTACAO  );
    INCODCOMPLEMENTAR           := INCODCOMPLEMENTARTEMP;

stSql := 'SELECT CASE WHEN (COALESCE(count(*),0) > 0) THEN TRUE
                      ELSE FALSE
                 END
		 FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
		 INNER JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
		    ON ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
		   AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar.cod_evento
		   AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
		   AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
		 WHERE registro_evento_complementar.cod_contrato = '|| inCodContrato ||'
		   AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
		   AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'
		   AND registro_evento_complementar.cod_configuracao = 2';

    -- VALIDA A OCORRENCIA DE REGISTROS DE FERIAS LANÇADOS NA FOLHA COMPLEMENTAR
    -- RESULTANTES DE CALCULO ANTERIOR, CASO POSITIVO ESTES DEVEM SER DELETADOS PARA NOVA INCLUSAO

     boRetornoComplementar := SelectIntoBoolean(stSql);

 IF  boRetornoComplementar THEN
  BORETORNO := deletarInformacoesCalculo(INCODCONTRATO::varchar,'C',0,stEntidadeParametro);
 END IF;

 -- FIM VALIDA
 --INÍCIO DO CÓDIGO PARA CALCULAR FÉRIAS PARA PAGTO NA COMPLEMENTAR
 --
    --Código que verifica a existencia de concessão de férias para o contrato que está sendo calculado
    --na competência atual. Caso haja ferias a serem pagas dentro da folha complementar, será calculado
    --a folha férias, depois a folha complementar, e em seguida, os registros e eventos calculados de férias
    --serão transferidos para a folha complementar.

arCompetencia := string_to_array(STDATAFINALCOMPETENCIA,'-');

stSql := 'SELECT CASE WHEN (COALESCE(count(*),0) > 0) THEN TRUE
                      ELSE FALSE
                 END
       FROM pessoal'|| stEntidadeParametro ||'.lancamento_ferias
          , pessoal'|| stEntidadeParametro ||'.ferias
      WHERE lancamento_ferias.cod_ferias = ferias.cod_ferias
        AND cod_tipo = 3
        AND cod_contrato = '|| INCODCONTRATO ||'
        AND mes_competencia = '|| quote_literal(arCompetencia[2]) ||'
        AND ano_competencia = '|| quote_literal(arCompetencia[1]) ||' ';

    boRetornoFerias := SelectIntoBoolean(stSql);
   
    IF boRetornoFerias IS TRUE THEN  
         BORETORNO := deletarInformacoesCalculo(INCODCONTRATO::varchar,'F',0,stEntidadeParametro);
         BORETORNO := calculaFolhaFerias(INCODCONTRATO,BOERRO,stEntidadeParametro,stExercicioParametro);
    END IF;
    --FIM DO CÓDIGO PARA CALCULAR FÉRIAS

    stEntidade                  := criarBufferEntidade(stEntidadeParametro);
    INCODPERIODOMOVIMENTACAO    := CRIARBUFFERINTEIRO('inCodPeriodoMovimentacao' , INCODPERIODOMOVIMENTACAO  );
    inCodContrato               := CriarBufferInteiro('inCodContrato',inCodContrato);
    INCODCOMPLEMENTAR           := CRIARBUFFERINTEIRO('inCodComplementar',INCODCOMPLEMENTARTEMP);
    
	-- INICIO DO CODIGO PARA CALCULO DA FOLHA COMPLEMENTAR
    stSql:= 'SELECT CASE WHEN (COALESCE(count(*),0) > 0) THEN TRUE
                         ELSE FALSE
                       END
             FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
             WHERE cod_complementar = '|| inCodComplementar ||'
            AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
            AND cod_contrato = '|| inCodContrato ||'
            GROUP BY cod_contrato';
    boRetornoContratoPeriodo := SelectIntoBoolean(stSql);
    IF boRetornoContratoPeriodo THEN
        INCODCONTRATOGERADORBENEFICIO := pega0ContratoDoGeradorBeneficio(inCodContratoParametro);
        IF INCODCONTRATOGERADORBENEFICIO IS NULL THEN
            inCodContrato := inCodContratoParametro;
            inPensionista := criarBufferInteiro('inPensionista',0);
        ELSE
            inCodContrato := INCODCONTRATOGERADORBENEFICIO;
            inPensionista := criarBufferInteiro('inPensionista',1);
        END IF;
        stExercicioSistema          := criarBufferTexto('stExercicioSistema',stExercicioParametro);
        STTIPOFOLHA                 := CRIARBUFFERTEXTO('stTipoFolha',STTIPOFOLHA);
        STDATAFINALCOMPETENCIA      := CRIARBUFFERTEXTO(  'stDataFinalCompetencia' , STDATAFINALCOMPETENCIA  );
        INCODREGIME                 := PEGA0REGIMEDOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  );
        INCODREGIME                 := CRIARBUFFERINTEIRO(  'inCodRegime' , INCODREGIME  );
        INCODSUBDIVISAO             := PEGA0SUBDIVISAODOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  );
        INCODSUBDIVISAO             := CRIARBUFFERINTEIRO(  'inCodSubDivisao' , INCODSUBDIVISAO  );
        INCODFUNCAO                 := PEGA0FUNCAODOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  );
        INCODFUNCAO                 := CRIARBUFFERINTEIRO(  'inCodFuncao' , INCODFUNCAO  );
        INCODESPECIALIDADE          := PEGA0ESPECIALIDADEDOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  );
        INCODESPECIALIDADE          := CRIARBUFFERINTEIRO(  'inCodEspecialidade' , INCODESPECIALIDADE  );
        INCODPREVIDENCIAOFICIAL     := PEGA1PREVIDENCIAOFICIALDOCONTRATO();
        INCODPREVIDENCIAOFICIAL     := CRIARBUFFERINTEIRO('inCodPrevidenciaOficial',INCODPREVIDENCIAOFICIAL);
        INCODSERVIDOR               := PEGA0SERVIDORDOCONTRATO(INCODCONTRATO);
        INCODSERVIDOR               := CRIARBUFFERINTEIRO('inCodServidor',INCODSERVIDOR);
        INNUMCGM                    := PEGA0NUMCGMSERVIDOR(INCODSERVIDOR);
        INNUMCGM                    := CRIARBUFFERINTEIRO('inNumCgm',INNUMCGM);
        IF INCODCONTRATOGERADORBENEFICIO IS NOT NULL THEN
            inCodContrato := criarBufferInteiro('inCodContrato',inCodContratoParametro);
        END IF;
        --Variável utilizada no controle para a função pegaValorCalculadoFixo
        --Esta variável controla se o valor será gravado em banco ou apenas em memória
        INCONTROLE                  := CRIARBUFFERINTEIRO(  'inControle', INCONTROLE  );
        BORETORNO := PROCESSAREVENTOSAUTOMATICOSCOMPLEMENTAR();
        BORETORNO := CRIARTEMPORARIAREGISTROSFIXOS();
        IF BOERRO = 'f' THEN
            BORETORNO                   := CALCULAEVENTOCOMPLEMENTARPORCONTRATO(  INCODCONTRATO  );
        ELSIF BOERRO = 't' THEN
            BORETORNO                   := CALCULAEVENTOCOMPLEMENTARPORCONTRATOERRO(  INCODCONTRATO  );
        END IF;
    END IF;
    --INÍCIO DO CÓDIGO PARA INCORPORAR OS REGISTROS E EVENTOS CALCULADOS DE FÉRIAS EM COMPLEMENTAR
    IF boRetornoFerias IS TRUE THEN
       boRetornoComplementar:= FALSE;
        stSql := 'SELECT CASE WHEN (COALESCE(count(*),0) > 0) THEN TRUE 
                         ELSE FALSE 
                       END
                  FROM folhapagamento'|| stEntidade ||'.contrato_servidor_complementar
                  WHERE cod_complementar = '|| inCodComplementar ||'
                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                    AND cod_contrato = '|| inCodContrato;
        boRetornoComplementar:= SelectIntoBoolean(stSql);
        IF boRetornoComplementar IS FALSE THEN
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.contrato_servidor_complementar
                      (cod_complementar,cod_periodo_movimentacao,cod_contrato)
                      VALUES
                      ('|| inCodComplementar ||','|| inCodPeriodoMovimentacao ||','|| inCodContrato ||')';
            EXECUTE stSql;
        END IF;
        stSql := 'SELECT registro_evento_ferias.cod_evento
                       , registro_evento_ferias.desdobramento
                       , registro_evento_ferias.valor
                       , registro_evento_ferias.quantidade
                       , registro_evento_ferias.automatico
                       , evento_ferias_calculado.cod_registro
                       , evento_ferias_calculado.timestamp_registro
                       , evento_ferias_calculado.valor as valor_calculado
                       , evento_ferias_calculado.quantidade as quantidade_calculado
                    FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                       , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                   WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                     AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                     AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                     AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                     AND registro_evento_ferias.cod_contrato = '|| INCODCONTRATO ||'
                     AND registro_evento_ferias.cod_periodo_movimentacao = '|| INCODPERIODOMOVIMENTACAO;

        inCodRegistro := selectIntoInteger('SELECT max(cod_registro)+1
                                              FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar');
        IF inCodRegistro IS NULL THEN
            inCodRegistro := 1;
        END IF;
        FOR reRegistro IN EXECUTE stSql LOOP
            IF reRegistro.automatico IS TRUE THEN
                stAutomatico := 'TRUE';
            ELSE
                stAutomatico := 'FALSE';
            END IF;
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_complementar (cod_registro,cod_evento,timestamp,valor,quantidade,cod_complementar,cod_configuracao,cod_contrato,cod_periodo_movimentacao)
                     VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||','|| reRegistro.valor ||','|| reRegistro.quantidade ||','|| inCodComplementar ||',2,'|| inCodContrato ||','|| inCodPeriodoMovimentacao ||')';
            EXECUTE stSql;
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar (cod_registro,cod_evento,timestamp,cod_configuracao)
                     VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||',2)';
            EXECUTE stSql;
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.evento_complementar_calculado (cod_registro,cod_evento,timestamp_registro,valor,quantidade,desdobramento,cod_configuracao)
                     VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||','|| reRegistro.valor_calculado ||','|| reRegistro.quantidade_calculado ||','|| quote_literal(reRegistro.desdobramento) ||',2)';
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                     WHERE cod_registro = '|| reRegistro.cod_registro ||'
                       AND cod_evento = '|| reRegistro.cod_evento ||'
                       AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                       AND timestamp_registro = '|| quote_literal(reRegistro.timestamp_registro) ||' ';
            EXECUTE stSql;
            inCodRegistro := inCodRegistro + 1;
            stTimestamp   := stTimestamp + (time '00:00:01');
        END LOOP;
    END IF;
    --FIM DO CÓDIGO PARA INCORPORAR OS REGISTROS E EVENTOS CALCULADOS DE FÉRIAS EM COMPLEMENTAR
RETURN BORETORNO;
END;
$$ LANGUAGE 'plpgsql';

