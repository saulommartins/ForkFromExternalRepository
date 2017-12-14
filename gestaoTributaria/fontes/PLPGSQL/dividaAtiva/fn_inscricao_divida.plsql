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
* $Revision: 27473 $
* $Name$
* $Author: cercato $
* $Date: 2008-01-11 11:54:50 -0200 (Sex, 11 Jan 2008) $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log:
*/

CREATE OR REPLACE FUNCTION divida.fn_inscricao_divida( INTEGER[], INTEGER, INTEGER, INTEGER, INTEGER, DATE, INTEGER, INTEGER )
RETURNS BOOLEAN AS '

DECLARE

	arrLancamento		ALIAS FOR $1;
        inExercicio             ALIAS FOR $2; 
        inCodModalidade         ALIAS FOR $3;
        inCodAutoridade         ALIAS FOR $4;
        inCodCGMUsuario         ALIAS FOR $5;
        dtInscricao             ALIAS FOR $6;
        inCodProcesso           ALIAS FOR $7; 
        inExercicioProcesso     ALIAS FOR $8;
        inCodLancamento         INTEGER;
        inCodCredito            INTEGER:=0;
        inCodNatureza           INTEGER:=0;
        inCodEspecie            INTEGER:=0;
        inCodGenero             INTEGER:=0;
        inCodParcela            INTEGER:=0;
        inCodLancamentoAux      INTEGER:=0;
        inCodInscricao          INTEGER;
        inCodCadastro           INTEGER;
        inLivro                 INTEGER;
        inPagina                INTEGER;
        inCodParcelamento       INTEGER;
        inCodDocumento          INTEGER;
        inCodForma              INTEGER;
        inLancamentos           INTEGER;
        inCGMCadastro           INTEGER;
        inFolhasLivro           INTEGER;
        inInicialLivro          INTEGER;
        stInscricao             VARCHAR;
        stFolha                 VARCHAR;
        tmTimestampModalidade   TIMESTAMP;
        nmValoraCalcular        NUMERIC(14,2):=0.00;
	valor_acrescimo		NUMERIC;
        recConfiguracao         RECORD; 
        recCreditos             RECORD; 
        recCancelar             RECORD;
        recDocumentos           RECORD;
	recAcrescimos		RECORD;

BEGIN
        SELECT cod_forma_inscricao, timestamp INTO inCodForma, tmTimestampModalidade 
           FROM divida.modalidade_vigencia 
           WHERE cod_modalidade = inCodModalidade 
           ORDER BY timestamp DESC 
           LIMIT 1;

        FOR recConfiguracao IN SELECT parametro, valor
                                  FROM administracao.configuracao
                                  WHERE cod_modulo = 33
                                     AND (parametro = ''numeracao_inscricao'' OR
                                          parametro = ''numero_folhas_livro'' OR
                                          parametro = ''numero_inicial_livro'' OR
                                          parametro = ''numeracao_folha'') 
                                     AND exercicio = inExercicio
	LOOP
		IF recConfiguracao.parametro = ''numeracao_inscricao'' THEN
                   stInscricao = recConfiguracao.valor;
                END IF;

		IF recConfiguracao.parametro = ''numero_folhas_livro'' THEN
                   inFolhasLivro = recConfiguracao.valor;
		END IF; 

		IF recConfiguracao.parametro = ''numero_inicial_livro'' THEN
		   inInicialLivro = recConfiguracao.valor;
		END IF; 

		IF recConfiguracao.parametro = ''numeracao_folha'' THEN
		   stFolha = recConfiguracao.valor;
		END IF; 	
	END LOOP;

	IF stInscricao = ''exercicio'' THEN
           SELECT MAX(cod_inscricao)+1 INTO inCodInscricao
              FROM divida.divida_ativa
              WHERE exercicio = inExercicio;
        ELSE
           SELECT MAX(cod_inscricao)+1 INTO inCodInscricao
              FROM divida.divida_ativa;
        END IF;

        IF stFolha = ''exercicio'' THEN
	   SELECT num_livro, num_folha INTO inLivro, inPagina 
              FROM divida.divida_ativa
              WHERE exercicio = (
                     SELECT MAX(exercicio)
                        FROM divida.divida_ativa
                     )
              GROUP BY num_livro,
                       num_folha
              ORDER BY
                 num_livro DESC
              LIMIT 1;
        ELSE
           SELECT num_livro, num_folha INTO inLivro, inPagina 
              FROM divida.divida_ativa
           GROUP BY
              num_livro,
              num_folha
           ORDER BY
              num_livro DESC
           LIMIT 1;
	END IF; 	

        FOR inLancamentos IN 1..array_upper(arrLancamento, 1) LOOP

		inCodLancamento = arrLancamento[inLancamentos];

		FOR recCancelar IN SELECT numeracao,
                                          cod_convenio
                                      FROM
                                         arrecadacao.carne
                                         INNER JOIN
                                         arrecadacao.parcela
                                            ON
                                             parcela.cod_parcela = carne.cod_parcela
                                      WHERE
                                         parcela.cod_lancamento = inCodLancamento 
                                         AND carne.numeracao NOT IN (
                                             SELECT
                                                pagamento.numeracao
                                             FROM
                                                arrecadacao.pagamento
                                             WHERE
                                                pagamento.numeracao = carne.numeracao
                                                AND pagamento.cod_convenio = carne.cod_convenio)
		LOOP
			INSERT INTO arrecadacao.carne_devolucao(numeracao,
                                                                cod_convenio,
                                                                dt_devolucao,
                                                                cod_motivo)
				VALUES(recCancelar.numeracao,
                                       recCancelar.cod_convenio,
                                       now()::date,
                                       11);
		END LOOP; 

		FOR recCreditos IN SELECT ap.cod_parcela
                                          , ap.vencimento
                                          , carne.exercicio::integer 
                                          , calc.cod_credito
                                          , mon.cod_natureza
                                          , mon.cod_genero
                                          , mon.cod_especie
                                          , (alc.valor * arrecadacao.calculaProporcaoParcela(ap.cod_parcela))::numeric(14,2) as valor
					  , case when inCodForma = 3 then ap.cod_parcela else calc.cod_credito end as ordenacao
                                      FROM arrecadacao.parcela as ap
					   INNER JOIN arrecadacao.carne
                                              ON carne.cod_parcela = ap.cod_parcela
                                           INNER JOIN arrecadacao.lancamento_calculo as alc
                                              ON alc.cod_lancamento = ap.cod_lancamento
                                           INNER JOIN arrecadacao.calculo as calc
                                              ON calc.cod_calculo = alc.cod_calculo
                                           INNER JOIN monetario.credito as mon
                                              ON mon.cod_credito = calc.cod_credito
                                              AND mon.cod_natureza = calc.cod_natureza
                                              AND mon.cod_especie = calc.cod_especie
                                              AND mon.cod_genero = calc.cod_genero
                                      WHERE
                                         ap.cod_lancamento = inCodLancamento
					 AND ap.nr_parcela > 0
				      ORDER BY ordenacao
                LOOP
			IF inCodForma = 4 THEN
                   		inCodCredito  = 0;
                   		inCodNatureza = 0;
                   		inCodGenero   = 0;
                   		inCodEspecie  = 0;
                	END IF;

                	IF inCodForma = 3 THEN
				inCodCredito  = recCreditos.cod_credito;
                                inCodNatureza = recCreditos.cod_natureza;
                                inCodGenero   = recCreditos.cod_genero;
                                inCodEspecie  = recCreditos.cod_especie;

                   		IF inCodParcela <> recCreditos.cod_parcela THEN
                      			inCodParcela  = recCreditos.cod_parcela;
                      			inCodCredito  = 0;
                      			inCodNatureza = 0;
                      			inCodGenero   = 0;
                      			inCodEspecie  = 0;
                   		END IF; 
                	END IF;

			IF inCodForma = 1 THEN
				inCodCredito  = recCreditos.cod_credito;
                                inCodNatureza = recCreditos.cod_natureza;
                                inCodGenero   = recCreditos.cod_genero;
                                inCodEspecie  = recCreditos.cod_especie;

			  	IF inCodLancamentoAux <> inCodLancamento THEN
					inCodLancamentoAux = inCodLancamento;
					inCodCredito  = 0;
                                        inCodNatureza = 0;
                                        inCodGenero   = 0;
                                        inCodEspecie  = 0;
				END IF; 	
			END IF;

       			IF inCodCredito  <> recCreditos.cod_credito  OR 
                   	   inCodNatureza <> recCreditos.cod_natureza OR 
                   	   inCodGenero   <> recCreditos.cod_genero   OR 
                   	   inCodEspecie  <> recCreditos.cod_especie THEN
				
				IF nmValoraCalcular > 0 THEN
                			FOR recAcrescimos IN select cod_tipo, cod_acrescimo
                                        			from divida.modalidade as a, divida.modalidade_acrescimo as b
                                        			where a.cod_modalidade = inCodModalidade and
                                                			a.cod_modalidade = b.cod_modalidade and
                                                			a.ultimo_timestamp = b.timestamp and
                                                			b.pagamento = false
                			LOOP
						select split_part(aplica_acrescimo_modalidade(0, inCodInscricao, 
                                                                                         inExercicio::integer, 
											 inCodModalidade,
											 recAcrescimos.cod_tipo, 
											 inCodInscricao, 
											 nmValoraCalcular,
											 recCreditos.vencimento, 
											 dtInscricao, 
											 ''false''::text),
											 '';'', 1) INTO valor_acrescimo;
 
						INSERT INTO divida.divida_acrescimo(exercicio,
 								    	    cod_inscricao,
    								            cod_acrescimo,
      								            cod_tipo,
								            valor)
							VALUES(inExercicio,
                                               	       		inCodInscricao,
                                               	       		recAcrescimos.cod_acrescimo,
 					               		recAcrescimos.cod_tipo,
					       	       		valor_acrescimo);
					END LOOP;
					nmValoraCalcular = 0.00;
				END IF; 

				IF inCodForma = 2 THEN 
					inCodCredito  = recCreditos.cod_credito;
					inCodNatureza = recCreditos.cod_natureza;
					inCodGenero   = recCreditos.cod_genero;
					inCodEspecie  = recCreditos.cod_especie;
				END IF; 

				inCodInscricao = inCodInscricao + 1;

				inPagina = inPagina + 1;
                                IF inPagina > inFolhasLivro THEN
					inLivro  = inLivro + 1;
                                        inPagina = 1;
				END IF;

				INSERT INTO divida.divida_ativa(exercicio, 
                                                                cod_inscricao, 
                                                                cod_autoridade, 
                                                                numcgm_usuario, 
                                                                dt_inscricao, 
                                                                num_livro, 
                                                                num_folha, 
                                                                dt_vencimento_origem, 
                                                                exercicio_original)
					VALUES(inExercicio,
                                               inCodInscricao,
                                               inCodAutoridade,
                                               inCodCGMUsuario,
                                               dtInscricao,
                                               inLivro,
                                               inPagina,
                                               recCreditos.vencimento,
                                               recCreditos.exercicio);

				SELECT DISTINCT numcgm INTO inCGMCadastro
                                   FROM arrecadacao.lancamento_calculo as lc, 
                                        arrecadacao.calculo_cgm as cc 
                                   WHERE lc.cod_lancamento = inCodLancamento
                                      AND lc.cod_calculo = cc.cod_calculo;

			        INSERT INTO divida.divida_cgm(exercicio,
                                                              cod_inscricao,
                                                              numcgm)
					VALUES(inExercicio,
                                               inCodInscricao,
                                               inCGMCadastro);	

				SELECT DISTINCT inscricao_municipal INTO inCodCadastro 
                                   FROM arrecadacao.lancamento_calculo AS lc,
                                        arrecadacao.imovel_calculo AS ic
                                   WHERE lc.cod_lancamento = inCodLancamento 
                                      AND lc.cod_calculo = ic.cod_calculo;

				IF FOUND THEN
					INSERT INTO divida.divida_imovel(exercicio,
                                                                         cod_inscricao,
                                                                         inscricao_municipal)
						VALUES(inExercicio,
                                                       inCodInscricao,
                                                       inCodCadastro);
				ELSE
					SELECT DISTINCT inscricao_economica INTO inCodCadastro
                                   	   FROM arrecadacao.lancamento_calculo AS lc,
                                        	arrecadacao.cadastro_economico_calculo AS ce
                                   	   WHERE lc.cod_lancamento = inCodLancamento
                                      	      AND lc.cod_calculo = ce.cod_calculo;

					IF FOUND THEN
						INSERT INTO divida.divida_empresa(exercicio,
                                                                                  cod_inscricao,
                                                                                  inscricao_economica)
							VALUES(inExercicio,
                                                               inCodInscricao,
                                                               inCodCadastro);
					END IF;
				END IF;
      
                                SELECT coalesce ( (max(ddp.num_parcelamento) +1), 0 ) as valor INTO inCodParcelamento 
                                   FROM divida.parcelamento as ddp;

				INSERT INTO divida.parcelamento(num_parcelamento,
                                                                numcgm_usuario,
                                                                cod_modalidade,
                                                                timestamp_modalidade,
                                                                numero_parcelamento,
                                                                exercicio)
					VALUES(inCodParcelamento,
                                               inCodCGMUsuario,
                                               inCodModalidade,
                                               tmTimestampModalidade,
                                               -1,
                                               -1);

				INSERT INTO divida.divida_parcelamento(exercicio,
                                                                       cod_inscricao,
                                                                       num_parcelamento)
					VALUES(inExercicio,
                                               inCodInscricao,
                                               inCodParcelamento);


				FOR recDocumentos IN SELECT cod_tipo_documento, cod_documento
                                                        FROM divida.modalidade_documento
                                                        WHERE cod_modalidade = inCodModalidade
                                                           AND timestamp = tmTimestampModalidade
                                LOOP 
					INSERT INTO divida.documento(num_parcelamento,
                                                                     cod_tipo_documento,
                                                                     cod_documento)
						VALUES(inCodParcelamento,
                                                       recDocumentos.cod_tipo_documento,
                                                       recDocumentos.cod_documento);
				END LOOP;

				IF inCodProcesso > 0 THEN
					INSERT INTO divida.divida_processo(exercicio,
                                                                           cod_inscricao,
                                                                           cod_processo,
                                                                           ano_exercicio)
						VALUES(inExercicio,
                                                       inCodInscricao,
                                                       inCodProcesso,
                                                       inExercicioProcesso);
				END IF;
			END IF;

		        INSERT INTO divida.parcela_origem(cod_parcela,
                                                          cod_especie,
                                                          cod_genero,
                                                          cod_natureza,
                                                          cod_credito,
                                                          num_parcelamento,
                                                          valor)
				VALUES(recCreditos.cod_parcela,
                                       recCreditos.cod_especie,
                                       recCreditos.cod_genero,
                                       recCreditos.cod_natureza,
                                       recCreditos.cod_credito,
                                       inCodParcelamento,
                                       recCreditos.valor);

			nmValoraCalcular = nmValoraCalcular + recCreditos.valor;

       		END LOOP;
	END LOOP;

       RETURN TRUE;
END;
' LANGUAGE 'plpgsql';

	

