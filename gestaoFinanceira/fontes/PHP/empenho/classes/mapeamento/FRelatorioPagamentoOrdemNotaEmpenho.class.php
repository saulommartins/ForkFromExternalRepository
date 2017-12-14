<?php
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
?>
<?php
/**
    * Classe de mapeamento da tabela FN_RELATORIO_PAGAMENTO_ORDEM_NOTA_EMPENHO
    * Data de Criação: 08/03/2015

    * @author Michel Teixeira

    $Id: FRelatorioPagamentoOrdemNotaEmpenho.class.php 64598 2016-03-17 18:06:12Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FRelatorioPagamentoOrdemNotaEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
	{
        parent::Persistente();

        $this->setTabela('empenho.fn_relatorio_pagamento_ordem_nota_empenho');

        $this->AddCampo('exercicio'         , 'varchar',  TRUE, '', FALSE, FALSE);
        $this->AddCampo('stEntidade'        , 'varchar', FALSE, '', FALSE, FALSE);
        $this->AddCampo('exercicio_empenho' , 'varchar', FALSE, '', FALSE, FALSE);
        $this->AddCampo('cod_empenho'       , 'integer', FALSE, '', FALSE, FALSE);
        $this->AddCampo('exercicio_nota'    , 'varchar', FALSE, '', FALSE, FALSE);
        $this->AddCampo('cod_nota'          , 'integer', FALSE, '', FALSE, FALSE);
        $this->AddCampo('exercicio_ordem'   , 'varchar', FALSE, '', FALSE, FALSE);
        $this->AddCampo('cod_ordem'         , 'integer', FALSE, '', FALSE, FALSE);
        $this->AddCampo('bo_estornado'      , 'boolean', FALSE, '', FALSE, FALSE);
        $this->AddCampo('bo_retencao'       , 'boolean', FALSE, '', FALSE, FALSE);
    }

    function montaRecuperaTodos()
    {
        $stSql  = "SELECT *
                     FROM " .$this->getTabela(). "
                        ( '".$this->getDado("exercicio")."'
                        , '".$this->getDado("stEntidade")."'
                        , '".$this->getDado("exercicio_empenho")."'
                        ,  ".$this->getDado("cod_empenho")."
                        , '".$this->getDado("exercicio_nota")."'
                        ,  ".$this->getDado("cod_nota")."
                        , '".$this->getDado("exercicio_ordem")."'
                        ,  ".$this->getDado("cod_ordem")."
                        ,  ".$this->getDado("bo_estornado")."
                        ,  ".$this->getDado("bo_retencao")."
                        ) AS pagamento
        ";

        return $stSql;
    }

    function recuperaRP(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRP().$stFiltro.$stGroup.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaRP()
    {
        $stSql  = "
			 SELECT *
			   FROM (
					SELECT pagamento.cod_entidade AS entidade
						 , pagamento.cod_empenho AS empenho
						 , pagamento.exercicio_empenho AS exercicio
						 , pre_empenho.cgm_beneficiario AS cgm
						 , sw_cgm.nom_cgm AS razao_social
						 , pagamento.desdobramento AS cod_estrutural
						 , pagamento.cod_nota
						 , to_char(pagamento.timestamp_pagamento,'dd/mm/yyyy') AS data
						 , pagamento.cod_plano_pagamento AS conta
						 , pagamento.nom_conta_plano_pagamento AS banco
						 , CASE WHEN situacao.cod_situacao = 1
								THEN pagamento.vl_pago + pagamento.vl_pago_retencao
								ELSE nota_liquidacao_paga_anulada.vl_anulado
						   END AS valor
						 , COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00) AS vl_anulado
						 , (pagamento.vl_pago + pagamento.vl_pago_retencao)- COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00) AS vl_saldo
						 , situacao.nom_situacao
						 , despesa.cod_recurso

					 FROM empenho.fn_relatorio_pagamento_ordem_nota_empenho( '".$this->getDado("exercicio")."'
																		   , '".$this->getDado("stEntidade")."'
																		   , ''
																		   ,  0
																		   , ''
																		   ,  0
																		   , ''
																		   ,  0
																		   ,  FALSE
																		   ,  FALSE
																		   ) AS pagamento

			   INNER JOIN empenho.pre_empenho
					   ON pre_empenho.cod_pre_empenho = pagamento.cod_pre_empenho
					  AND pre_empenho.exercicio = pagamento.exercicio_empenho

			   INNER JOIN sw_cgm
					   ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

			   INNER JOIN ( SELECT ped.exercicio
								 , ped.cod_pre_empenho
								 , d.num_orgao
								 , d.num_unidade
								 , d.cod_recurso
								 , REPLACE(cd.cod_estrutural,'.', '') AS cod_estrutural
								 , d.cod_funcao
								 , d.cod_subfuncao
							  FROM empenho.pre_empenho_despesa as ped
							  JOIN orcamento.despesa as d
								ON ped.cod_despesa = d.cod_despesa 
							   AND ped.exercicio = d.exercicio 
							  JOIN orcamento.recurso as r
								ON r.cod_recurso = d.cod_recurso
							   AND r.exercicio = d.exercicio
							  JOIN orcamento.conta_despesa as cd
								ON ped.cod_conta = cd.cod_conta 
							   AND ped.exercicio = cd.exercicio
							 UNION
							SELECT restos_pre_empenho.exercicio
								 , restos_pre_empenho.cod_pre_empenho
								 , restos_pre_empenho.num_orgao
								 , restos_pre_empenho.num_unidade
								 , restos_pre_empenho.recurso AS cod_recurso
								 , restos_pre_empenho.cod_estrutural
								 , restos_pre_empenho.cod_funcao
								 , restos_pre_empenho.cod_subfuncao
							  FROM empenho.restos_pre_empenho
						  ) AS despesa
					   ON despesa.exercicio       = pre_empenho.exercicio 
					  AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

				LEFT JOIN empenho.nota_liquidacao_paga_anulada
					   ON nota_liquidacao_paga_anulada.cod_entidade = pagamento.cod_entidade
					  AND nota_liquidacao_paga_anulada.cod_nota     = pagamento.cod_nota
					  AND nota_liquidacao_paga_anulada.exercicio    = pagamento.exercicio_nota
					  AND nota_liquidacao_paga_anulada.timestamp    = pagamento.timestamp_pagamento

			   INNER JOIN ( SELECT 1 AS cod_situacao, 'PAGAMENTO' AS nom_situacao
							UNION
							SELECT 2 AS cod_situacao, 'ANULAÇÃO'   AS nom_situacao
						  ) AS situacao
					   ON situacao.cod_situacao IN ( ".$this->getDado("inSituacao")." )

					WHERE 
						  --SITUAÇÃO 1, PAGAMENTO, DATA DO PAGAMENTO PRECISA ESTAR DENTRO DO FILTRO PERIODICIDADE
						  (   ( situacao.cod_situacao = 1
								AND pagamento.timestamp_pagamento::DATE
									 BETWEEN to_date('".$this->getDado("stDataInicial")."','dd/mm/yyyy')
										 AND to_date('".$this->getDado("stDataFinal")."','dd/mm/yyyy')
							  )
						  --SITUAÇÃO 2, ANULAÇÃO DE PAGAMENTO, DATA DA ANULAÇÃO PRECISA ESTAR DENTRO DO FILTRO PERIODICIDADE
						   OR ( situacao.cod_situacao = 2
								AND nota_liquidacao_paga_anulada.timestamp IS NOT NULL
								AND nota_liquidacao_paga_anulada.timestamp_anulada::DATE
									 BETWEEN to_date('".$this->getDado("stDataInicial")."','dd/mm/yyyy')
										 AND to_date('".$this->getDado("stDataFinal")."','dd/mm/yyyy')
							  )
						  )
        ";

        if( $this->getDado("exercicioEmpenho") != '' )
            $stSql .= " AND pagamento.exercicio_empenho = '".$this->getDado("exercicioEmpenho")."'";

        if( $this->getDado("inOrgao") != '' )
            $stSql .= " AND despesa.num_orgao = ".$this->getDado("inOrgao");

        if( $this->getDado("inUnidade") != '' )
            $stSql .= " AND despesa.num_unidade = ".$this->getDado("inUnidade");

        if( $this->getDado("inRecurso") != '' )
            $stSql .= " AND despesa.cod_recurso = ".$this->getDado("inRecurso");

        if( $this->getDado("stElementoDespesa") != '' )
            $stSql .= " AND despesa.cod_estrutural LIKE RTRIM('".str_replace(".","",$this->getDado("stElementoDespesa"))."','0')||'%'";

        if( $this->getDado("stCodFuncao") != '' )
            $stSql .= " AND despesa.cod_funcao = ".$this->getDado("stCodFuncao");

        if( $this->getDado("stCodSubFuncao") != '' )
            $stSql .= " AND despesa.cod_subfuncao = ".$this->getDado("stCodSubFuncao");

        if( $this->getDado("inCodFornecedor") != '' )
            $stSql .= " AND pre_empenho.cgm_beneficiario = ".$this->getDado("inCodFornecedor");

        $stSql .= "
				 GROUP BY pagamento.cod_entidade
						 , pagamento.cod_empenho
						 , pagamento.exercicio_empenho
						 , pre_empenho.cgm_beneficiario
						 , sw_cgm.nom_cgm
						 , pagamento.desdobramento
						 , pagamento.cod_nota
						 , pagamento.timestamp_pagamento
						 , pagamento.cod_plano_pagamento
						 , pagamento.nom_conta_plano_pagamento
						 , pagamento.vl_pago
						 , pagamento.vl_pago_retencao
						 , nota_liquidacao_paga_anulada.vl_anulado
						 , situacao.cod_situacao
						 , situacao.nom_situacao
						 , despesa.cod_recurso
	
				   ORDER BY to_date(to_char(pagamento.timestamp_pagamento,'dd/mm/yyyy'),'dd/mm/yyyy')
						 , pagamento.cod_entidade
						 , pagamento.cod_empenho
						 , pagamento.exercicio_empenho
						 , pagamento.cod_nota
						 , pagamento.cod_plano_pagamento
						 , pagamento.nom_conta_plano_pagamento
					) AS pagamento
			  WHERE pagamento.exercicio < '".$this->getDado("exercicio")."'
        ";

        if( $this->getDado("stDestinacaoRecurso") != '' ){
            $stSql .= " AND ( SELECT masc_recurso_red
                                FROM orcamento.recurso(pagamento.exercicio) AS REC
                               WHERE REC.exercicio = pagamento.exercicio
                                 AND REC.cod_recurso = pagamento.cod_recurso
                            ) = '".$this->getDado("stDestinacaoRecurso")."'
            ";
        }

        if( $this->getDado("inCodDetalhamento") != '' ){
            $stSql .= " AND ( SELECT cod_detalhamento
                                FROM orcamento.recurso(pagamento.exercicio) AS REC
                               WHERE REC.exercicio = pagamento.exercicio
                                 AND REC.cod_recurso = pagamento.cod_recurso
                            ) = '".$this->getDado("inCodDetalhamento")."'
            ";
        }

        return $stSql;
    }

    function recuperaRazaoTCEMG(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if($stOrdem == ''){
            $stOrdem = "
            ORDER BY despesa.num_orgao
                   , despesa.num_unidade
                   , pagamento.cod_entidade
                   , pagamento.timestamp_pagamento::DATE
                   , pagamento.exercicio_empenho
                   , pagamento.cod_empenho
            ";
        }

        $stGroupBy = "
            GROUP BY pagamento.timestamp_pagamento::DATE
                   , pagamento.cod_empenho
                   , pagamento.exercicio_empenho
                   , pagamento.cod_entidade
                   , pagamento.cod_nota
                   , pagamento.exercicio_nota
                   , pagamento.cod_ordem
                   , pagamento.exercicio_ordem
                   , conta_despesa.cod_estrutural
                   , conta_despesa.descricao
                   , sw_cgm.numcgm
                   , sw_cgm.nom_cgm
                   , pagamento_tipo_documento.num_documento
                   , ctb.conta_bancaria
                   , pagamento.cod_plano_pagamento
                   , pagamento.nom_conta_plano_pagamento
                   , despesa.cod_recurso
                   , despesa.nom_recurso
                   , pagamento.bo_pagamento_estornado
                   , despesa.cod_despesa
                   , despesa.num_orgao
                   , despesa.nom_orgao
                   , despesa.num_unidade
                   , despesa.nom_unidade
                   , despesa.dotacao
                   , tipo_documento.descricao
                   , ctb.cod_recurso
                   , ctb.nom_recurso
        ";

        $stSql = $this->montaRecuperaRazaoTCEMG().$stFiltro.$stGroupBy.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    function montaRecuperaRazaoTCEMG()
    {
        $stSql  = "
                     SELECT to_char(pagamento.timestamp_pagamento::DATE,'dd/mm/yyyy') AS dt_pagamento
                          , pagamento.cod_empenho
                          , pagamento.exercicio_empenho
                          , pagamento.cod_entidade
                          , pagamento.cod_nota
                          , pagamento.exercicio_nota
                          , pagamento.cod_ordem
                          , pagamento.exercicio_ordem
                          , conta_despesa.cod_estrutural AS despesa
                          , conta_despesa.descricao AS descricao_despesa
                          , sw_cgm.numcgm
                          , sw_cgm.nom_cgm AS credor
                          , (SUM(pagamento.vl_pago) + SUM(pagamento.vl_pago_retencao)) AS vl_pago
                          , (SUM(pagamento.vl_pago_retencao)) AS vl_retencao
                          , (SUM(pagamento.vl_pago) + SUM(pagamento.vl_pago_retencao)) - SUM(pagamento.vl_pago_retencao) AS vl_liquido
                          , (SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00))) AS vl_anulado
                          , pagamento_tipo_documento.num_documento AS documento
                          , tipo_documento.descricao AS tipo_documento
                          , ctb.conta_bancaria AS conta_banco
                          , pagamento.cod_plano_pagamento
                          , pagamento.nom_conta_plano_pagamento
                          , ctb.cod_recurso AS cod_recurso_pgto
                          , ctb.nom_recurso AS nom_recurso_pgto
                          , pagamento.bo_pagamento_estornado
                          , despesa.num_orgao
                          , despesa.nom_orgao
                          , despesa.num_unidade
                          , despesa.nom_unidade
                          , despesa.dotacao
                          , despesa.cod_recurso
                          , despesa.nom_recurso
                          , despesa.cod_despesa

                       FROM empenho.fn_relatorio_pagamento_ordem_nota_empenho( '".$this->getDado("exercicio")."'
                                                                             , '".$this->getDado("stEntidade")."'
                                                                             , '".$this->getDado("exercicio_empenho")."'
                                                                             , 0
                                                                             , ''
                                                                             , 0
                                                                             , ''
                                                                             , 0
                                                                             , FALSE
                                                                             , FALSE
                                                                             ) AS pagamento

                 INNER JOIN empenho.pre_empenho
                         ON pre_empenho.cod_pre_empenho = pagamento.cod_pre_empenho
                        AND pre_empenho.exercicio       = pagamento.exercicio_empenho

                 INNER JOIN sw_cgm
                         ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                 INNER JOIN ( SELECT ped.exercicio
                                   , ped.cod_pre_empenho
                                   , d.num_orgao
                                   , orgao_unidade.nom_orgao
                                   , d.num_unidade
                                   , orgao_unidade.nom_unidade
                                   , d.cod_recurso
                                   , rec.nom_recurso
                                   , d.cod_despesa
                                   , REPLACE(cd.cod_estrutural, '.', '') AS cod_estrutural
                                   , d.cod_funcao
                                   , d.cod_subfuncao
                                   , d.num_pao
                                   , LPAD(d.num_orgao::VARCHAR, 2, '0')
                                     ||'.'||LPAD(d.num_unidade::VARCHAR, 2, '0')
                                     ||'.'||d.cod_funcao
                                     ||'.'||d.cod_subfuncao
                                     ||'.'||ppa.programa.num_programa
                                     ||'.'||LPAD(acao.num_acao::VARCHAR, 4, '0')
                                    ||'.'||LPAD(REPLACE(cd.cod_estrutural, '.', ''), 6, '0')
                                     ||'.'||d.cod_recurso
                                     AS dotacao
                                   , FALSE AS restos
                                FROM empenho.pre_empenho_despesa AS ped
                          INNER JOIN orcamento.despesa AS d
                                  ON ped.cod_despesa    = d.cod_despesa
                                 AND ped.exercicio      = d.exercicio
                          INNER JOIN orcamento.recurso AS rec
                                  ON rec.cod_recurso = d.cod_recurso
                                 AND rec.exercicio   = d.exercicio
                          INNER JOIN orcamento.programa_ppa_programa
                                  ON programa_ppa_programa.cod_programa = d.cod_programa
                                 AND programa_ppa_programa.exercicio    = d.exercicio
                          INNER JOIN ppa.programa
                                  ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                          INNER JOIN orcamento.pao_ppa_acao
                                  ON pao_ppa_acao.num_pao   = d.num_pao
                                 AND pao_ppa_acao.exercicio = d.exercicio
                          INNER JOIN ppa.acao 
                                  ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                          INNER JOIN orcamento.conta_despesa AS cd
                                  ON ped.cod_conta = cd.cod_conta
                                 AND ped.exercicio = cd.exercicio
                          INNER JOIN (select unidade.exercicio
                                           , unidade.num_unidade
                                           , unidade.nom_unidade
                                           , orgao.num_orgao
                                           , orgao.nom_orgao
                                        from orcamento.unidade
                                  inner join orcamento.orgao
                                          on orgao.num_orgao = unidade.num_orgao
                                         and orgao.exercicio = unidade.exercicio
                                     ) orgao_unidade
                                  ON orgao_unidade.num_orgao   = d.num_orgao
                                 AND orgao_unidade.num_unidade = d.num_unidade
                                 AND orgao_unidade.exercicio   = d.exercicio
                               UNION
                              SELECT r.exercicio
                                   , r.cod_pre_empenho
                                   , r.num_orgao
                                   , orgao_unidade.nom_orgao
                                   , r.num_unidade
                                   , orgao_unidade.nom_unidade
                                   , r.recurso AS cod_recurso
                                   , rec.nom_recurso
                                   , NULL AS cod_despesa
                                   , r.cod_estrutural
                                   , r.cod_funcao
                                   , r.cod_subfuncao
                                   , r.num_pao
                                   , LPAD(r.num_orgao::VARCHAR, 2, '0')
                                     ||'.'||LPAD(r.num_unidade::VARCHAR, 2, '0')
                                     ||'.'||r.cod_funcao
                                     ||'.'||r.cod_subfuncao
                                     ||'.'||r.cod_programa
                                     ||'.'||LPAD(r.num_pao::VARCHAR, 4, '0')
                                     ||'.'||LPAD(r.cod_estrutural, 6, '0')
                                     ||'.'||r.recurso
                                     AS dotacao
                                   , TRUE AS restos
                                FROM empenho.restos_pre_empenho AS r
                          INNER JOIN orcamento.recurso AS rec
                                  ON rec.cod_recurso = r.recurso
                                 AND rec.exercicio   = '".$this->getDado("exercicio")."'
                           LEFT JOIN tcemg.uniorcam
                                  ON uniorcam.num_orgao   = r.num_orgao
                                 AND uniorcam.num_unidade = r.num_unidade
                                 AND uniorcam.exercicio   = r.exercicio
                           LEFT JOIN (select unidade.exercicio
                                           , unidade.num_unidade
                                           , unidade.nom_unidade
                                           , orgao.num_orgao
                                           , orgao.nom_orgao
                                        from orcamento.unidade
                                  inner join orcamento.orgao
                                          on orgao.num_orgao = unidade.num_orgao
                                         and orgao.exercicio = unidade.exercicio
                                     ) orgao_unidade
                                  ON orgao_unidade.num_orgao   = uniorcam.num_orgao_atual
                                 AND orgao_unidade.num_unidade = uniorcam.num_unidade_atual
                                 AND orgao_unidade.exercicio   = uniorcam.exercicio_atual
                            ) AS despesa
                         ON pagamento.exercicio_empenho = despesa.exercicio
                        AND pagamento.cod_pre_empenho   = despesa.cod_pre_empenho

                 INNER JOIN ( SELECT DISTINCT conta_corrente.num_conta_corrente
                                   , agencia.num_agencia
                                   , banco.num_banco
                                   , plano_banco.cod_plano
                                   , plano_banco.exercicio
                                   , num_banco||' / '||num_agencia||' / '||num_conta_corrente AS conta_bancaria
                                   , recurso.cod_recurso
                                   , recurso.nom_recurso
                                FROM contabilidade.plano_banco
                          INNER JOIN monetario.conta_corrente
                                  ON conta_corrente.cod_banco          = plano_banco.cod_banco
                                 AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                                 AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                          INNER JOIN monetario.agencia
                                  ON agencia.cod_banco   = conta_corrente.cod_banco
                                 AND agencia.cod_agencia = conta_corrente.cod_agencia
                          INNER JOIN monetario.banco
                                  ON banco.cod_banco = conta_corrente.cod_banco
                           LEFT JOIN contabilidade.plano_recurso
                                  ON plano_recurso.exercicio = plano_banco.exercicio
                                 AND plano_recurso.cod_plano = plano_banco.cod_plano
                           LEFT JOIN orcamento.recurso
                                  ON recurso.exercicio   = plano_recurso.exercicio
                                 AND recurso.cod_recurso = plano_recurso.cod_recurso
                            ) AS ctb
                         ON pagamento.exercicio_plano_pagamento = ctb.exercicio
                        AND pagamento.cod_plano_pagamento       = ctb.cod_plano

                  LEFT JOIN tcemg.pagamento_tipo_documento
                         ON pagamento_tipo_documento.exercicio    = pagamento.exercicio_nota
                        AND pagamento_tipo_documento.cod_nota     = pagamento.cod_nota
                        AND pagamento_tipo_documento.cod_entidade = pagamento.cod_entidade
                        AND pagamento_tipo_documento.timestamp    = pagamento.timestamp_pagamento

                  LEFT JOIN tcemg.tipo_documento
                         ON tipo_documento.cod_tipo = pagamento_tipo_documento.cod_tipo_documento

                 INNER JOIN orcamento.conta_despesa
                         ON conta_despesa.exercicio                        = pagamento.exercicio
                        AND REPLACE(conta_despesa.cod_estrutural, '.', '') = despesa.cod_estrutural

                  LEFT JOIN empenho.nota_liquidacao_paga_anulada
                         ON nota_liquidacao_paga_anulada.exercicio    = pagamento.exercicio
                        AND nota_liquidacao_paga_anulada.cod_nota     = pagamento.cod_nota
                        AND nota_liquidacao_paga_anulada.cod_entidade = pagamento.cod_entidade
                        AND nota_liquidacao_paga_anulada.timestamp    = pagamento.timestamp_pagamento

                      WHERE DATE_PART('YEAR', pagamento.timestamp_pagamento)::VARCHAR = '".$this->getDado("exercicio")."'
        ";

        return $stSql;
    }

}
