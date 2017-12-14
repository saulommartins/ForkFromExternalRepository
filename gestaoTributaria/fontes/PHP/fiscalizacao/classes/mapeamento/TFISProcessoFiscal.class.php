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
    * Classe de mapeamento para FISCALIZACAO.PROCESSO_FISCAL
    * Data de Criacao: 24/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Mapeamento

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once(CLA_PERSISTENTE);

class TFISProcessoFiscal extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('fiscalizacao.processo_fiscal');

        $this->setCampoCod('cod_processo');
        $this->setComplementoChave('');

        $this->AddCampo('cod_processo', 'integer', true, '', true, false);
        $this->AddCampo('cod_tipo', 'integer', true, '', false, true);
        $this->AddCampo('cod_processo_protocolo', 'integer', false, '', false, true);
        $this->AddCampo('ano_exercicio', 'varchar', true, '20', false, false);
        $this->AddCampo('numcgm', 'integer', true, '', false, true);
        $this->AddCampo('cod_natureza', 'integer', true, '', false, true);
        $this->AddCampo('periodo_inicio', 'date', true, '', false, false);
        $this->AddCampo('periodo_termino', 'date', true, '', false, false);
        $this->AddCampo('previsao_inicio', 'date', true, '', false, false);
        $this->AddCampo('previsao_termino', 'date', true, '', false, false);
        $this->AddCampo('observacao', 'text', true, '', false, false);
    }

    //Processo Fiscal com Empresa
    public function recuperaListaProcessoFiscalEconomica(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      	= new Erro;
        $obConexao   	= new Conexao;
        $rsRecordSet 	= new RecordSet;

        $stSql = $this->montaRecuperaListaProcessoFiscalEconomica($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaListaProcessoFiscalEconomica($condicao)
    {
        $stSql ="         SELECT DISTINCT ON (pf.cod_processo, tf.cod_tipo, pfe.inscricao_economica)    \n";
        $stSql.="		         pf.cod_processo	 						                            \n";
        $stSql.="              , tf.cod_tipo 								                            \n";
        $stSql.="       	   , tf.descricao 								                            \n";
        $stSql.="       	   , pfe.inscricao_economica AS inscricao 					                \n";
        $stSql.="       	   , fc.cod_fiscal 								                            \n";
        $stSql.="       	   , fc.numcgm 								                                \n";
        $stSql.="       	FROM fiscalizacao.processo_fiscal pf 					                    \n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 			                \n";
        $stSql.="       	  ON pf.cod_processo = pfe.cod_processo 					                \n";
        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 			                        \n";
        $stSql.="       	  ON pf.cod_tipo = tf.cod_tipo 						                        \n";
        $stSql.="LEFT OUTER JOIN (SELECT ffc.cod_fiscal 					                            \n";
        $stSql.="       	            , ffc.numcgm 								                    \n";
        $stSql.="       	            , ffpf.cod_processo 							                \n";
        $stSql.="       	         FROM fiscalizacao.fiscal as ffc 					                \n";
        $stSql.="       	   INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 			        \n";
        $stSql.="       	           ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 				        \n";
        $stSql.="       	  ON fc.cod_processo = pf.cod_processo 					                    \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif 		                        \n";
        $stSql.="       	  ON pf.cod_processo = fif.cod_processo 					                \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc		                    \n";
        $stSql.="       	  ON pf.cod_processo = pfc.cod_processo 	    				            \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao AS ftf	    	                    \n";
        $stSql.="        	  ON pf.cod_processo = ftf.cod_processo 					                \n";
        $stSql.= $condicao;

        return $stSql;

    }

//Processo Fiscal com Empresa
    public function recuperaListaProcessoFiscalEconomicaInicio(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      	= new Erro;
        $obConexao   	= new Conexao;
        $rsRecordSet 	= new RecordSet;

        $stSql = $this->montaRecuperaListaProcessoFiscalEconomica($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaListaProcessoFiscalEconomicaInicio($condicao)
    {
        $stSql ="SELECT distinct on(pf.cod_processo, tf.cod_tipo, pfe.inscricao_economica)  \n";
        $stSql.="		         pf.cod_processo                                            \n";
        $stSql.="       	   , tf.cod_tipo 								                \n";
        $stSql.="       	   , tf.descricao 								                \n";
        $stSql.="       	   , pfe.inscricao_economica AS inscricao 					    \n";
        $stSql.="       	   , fc.cod_fiscal 								                \n";
        $stSql.="       	   , fc.numcgm 								                    \n";
        $stSql.="       	FROM fiscalizacao.processo_fiscal pf 					        \n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 			    \n";
        $stSql.="       	  ON pf.cod_processo = pfe.cod_processo 					    \n";
        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 			            \n";
        $stSql.="       	  ON pf.cod_tipo = tf.cod_tipo 						            \n";
        $stSql.="     INNER JOIN (SELECT ffc.cod_fiscal 					                \n";
        $stSql.="       	           , ffc.numcgm 								        \n";
        $stSql.="       	           , ffpf.cod_processo 							        \n";
        $stSql.="       	        FROM fiscalizacao.fiscal as ffc 					    \n";
        $stSql.="       	  INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 		\n";
        $stSql.="       	          ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 			\n";
        $stSql.="       	  ON fc.cod_processo = pf.cod_processo 					        \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif 		            \n";
        $stSql.="             ON pf.cod_processo = fif.cod_processo 					    \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc		        \n";
        $stSql.="       	  ON pf.cod_processo = pfc.cod_processo 	    				\n";
        $stSql.= $condicao;

        return $stSql;
    }

    //Processo Fiscal com Imóvel
    public function recuperaListaProcessoFiscalObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaProcessoFiscalObra($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaListaProcessoFiscalObra($condicao)
    {
        $stSql ="SELECT distinct on(pf.cod_processo, tf.cod_tipo, pfo.inscricao_municipal)  \n";
        $stSql.="		         pf.cod_processo 							                \n";
        $stSql.="		       , tf.cod_tipo 								                \n";
        $stSql.="              , tf.descricao 							    	            \n";
        $stSql.="		       , pfo.inscricao_municipal AS inscricao					    \n";
        $stSql.="	           , fc.cod_fiscal 							                    \n";
        $stSql.="	           , fc.numcgm 								                    \n";
        $stSql.="		    FROM fiscalizacao.processo_fiscal pf 					        \n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo 				    \n";
        $stSql.="	          ON pf.cod_processo = pfo.cod_processo 				        \n";
        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf		 			    \n";
        $stSql.="	       	  ON pf.cod_tipo = tf.cod_tipo 					                \n";
        $stSql.="LEFT OUTER JOIN (SELECT ffc.cod_fiscal 						            \n";
        $stSql.="			           , ffc.numcgm 							            \n";
        $stSql.="			           , ffpf.cod_processo 						            \n";
        $stSql.="		            FROM fiscalizacao.fiscal as ffc 					    \n";
        $stSql.="		      INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 		\n";
        $stSql.="			          ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 			\n";
        $stSql.="	          ON fc.cod_processo = pf.cod_processo 				            \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif 		            \n";
        $stSql.="  	       	  ON pf.cod_processo = fif.cod_processo 				        \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc		        \n";
        $stSql.="             ON pf.cod_processo = pfc.cod_processo 					    \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao AS ftf		            \n";
        $stSql.="             ON pf.cod_processo = ftf.cod_processo 					    \n";
        $stSql.= $condicao;

        return $stSql;
    }

    //Processo Fiscal com Empresa e Imovel
    public function recuperaListaProcessoFiscalEconomicaObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaProcessoFiscalEconomicaObra($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaListaProcessoFiscalEconomicaObra($condicao)
    {
        $condicao = explode("#", $condicao);
        $stSql = $this->montaRecuperaListaProcessoFiscalEconomica($condicao[0]);
        $stSql.="\n UNION ALL \n";
        $stSql.=$this->montaRecuperaListaProcessoFiscalObra($condicao[1]);

        return $stSql;
    }

    //Monta a Início do Processo Fiscal com a Empresa
    public function recuperaInicioProcessoFiscalEconomica(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInicioProcessoFiscalEconomica($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaInicioProcessoFiscalEconomica($condicao)
    {
        $stSql =" 	      SELECT pf.cod_processo 				    	                \n";
        $stSql.="		       , pf.cod_processo_protocolo 				                \n";
        $stSql.="  		       , pf.ano_exercicio 					                    \n";
        $stSql.="		       , tf.cod_tipo 						                    \n";
        $stSql.="		       , tf.descricao 					                        \n";
        $stSql.="		       , pfe.inscricao_economica as inscricao 		            \n";
        $stSql.="		       , pfg.cod_grupo 					                        \n";
        $stSql.="		       , eace.cod_atividade 			                        \n";
        $stSql.="	        FROM fiscalizacao.processo_fiscal pf 			            \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_credito AS pfc 		    \n";
        $stSql.="		      ON pf.cod_processo = pfc.cod_processo 			        \n";
        $stSql.="	  INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 		    \n";
        $stSql.="		      ON pf.cod_processo = pfe.cod_processo 			        \n";
        $stSql.="	  INNER JOIN economico.atividade_cadastro_economico AS eace         \n";
        $stSql.="		      ON eace.inscricao_economica = pfe.inscricao_economica     \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_grupo AS pfg 		        \n";
        $stSql.="		      ON pf.cod_processo = pfg.cod_processo 			        \n";
        $stSql.=" 	  INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf  		            \n";
        $stSql.=" 		      ON pf.cod_tipo = tf.cod_tipo  				            \n";
        $stSql.="     INNER JOIN (SELECT ffc.cod_fiscal 			    	            \n";
        $stSql.="			           , ffc.numcgm 					                \n";
        $stSql.="			           , ffpf.cod_processo 				                \n";
        $stSql.="		            FROM fiscalizacao.fiscal as ffc 			        \n";
        $stSql.="		      INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 	\n";
        $stSql.="			          ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 	    \n";
        $stSql.="	          ON fc.cod_processo = pf.cod_processo 		                \n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaFiscaisProcesso(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFiscaisProcesso($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaFiscaisProcesso($condicao)
    {
        $stSql =" 	    SELECT distinct on (f.cod_fiscal)                   \n";
        $stSql.="              f.cod_fiscal as codigo  	                    \n";
        $stSql.="		     , sw.nom_cgm as descricao 				        \n";
        $stSql.="	      FROM fiscalizacao.processo_fiscal pf 			    \n";
        $stSql.=" 	INNER JOIN fiscalizacao.fiscal_processo_fiscal AS fpf 	\n";
        $stSql.="		    ON pf.cod_processo = fpf.cod_processo 			\n";
        $stSql.=" 	INNER JOIN fiscalizacao.fiscal AS f 		 		    \n";
        $stSql.="		    ON f.cod_fiscal = fpf.cod_fiscal 			    \n";
        $stSql.=" 	INNER JOIN sw_cgm AS sw 	 		 		            \n";
        $stSql.="		    ON sw.numcgm = f.numcgm		 			        \n";
        $stSql.= $condicao;

        return $stSql;
    }

    //Monta a Início do Processo Fiscal com a Obra
    public function recuperaInicioProcessoFiscalObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInicioProcessoFiscalObra($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaInicioProcessoFiscalObra($condicao)
    {
        $stSql =" 	    SELECT pf.cod_processo 					                        \n";
        $stSql.="		     , pf.cod_processo_protocolo 				                \n";
        $stSql.="		     , pf.ano_exercicio 					                    \n";
        $stSql.="		     , tf.cod_tipo 						                        \n";
        $stSql.="		     , tf.descricao 					                        \n";
        $stSql.="		     , pfo.inscricao_municipal as inscricao 		            \n";
        $stSql.="		     , pfo.cod_local 					                        \n";
        $stSql.="	      FROM fiscalizacao.processo_fiscal pf 			                \n";
        $stSql.="	INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo 		        \n";
        $stSql.="		    ON pf.cod_processo = pfo.cod_processo 		    	        \n";
        $stSql.=" 	INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf  		            \n";
        $stSql.=" 		    ON pf.cod_tipo = tf.cod_tipo  				                \n";
        $stSql.="   INNER JOIN (SELECT ffc.cod_fiscal 				                    \n";
        $stSql.="			         , ffc.numcgm 					                    \n";
        $stSql.="			         , ffpf.cod_processo 				                \n";
        $stSql.="		          FROM fiscalizacao.fiscal as ffc 			            \n";
        $stSql.="		    INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 	    \n";
        $stSql.="			        ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 	        \n";
        $stSql.="	        ON fc.cod_processo = pf.cod_processo 		                \n";
        $stSql.= $condicao;

        return $stSql;
    }

    //Monta a Início do Processo Fiscal com o Grupo
    public function recuperaInicioProcessoFiscalListaGrupo(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInicioProcessoFiscalGrupo($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaInicioProcessoFiscalGrupo($condicao)
    {
        $stSql ="     	SELECT pf.cod_processo 				            \n";
        $stSql.="		     , pfg.cod_grupo 				            \n";
        $stSql.="		     , pfg.ano_exercicio 				        \n";
        $stSql.="		     , agc.descricao 				            \n";
        $stSql.="	      FROM fiscalizacao.processo_fiscal_grupo pfg 	\n";
        $stSql.="	INNER JOIN fiscalizacao.processo_fiscal AS pf 		\n";
        $stSql.="		    ON pfg.cod_processo = pf.cod_processo 		\n";
        $stSql.="	INNER JOIN arrecadacao.grupo_credito AS agc 		\n";
        $stSql.="		    ON pfg.cod_grupo = agc.cod_grupo 		    \n";
        $stSql.="	       AND pfg.ano_exercicio = agc.ano_exercicio 	\n";

        $stGroupBy =" GROUP BY pf.cod_processo 				            \n";
        $stGroupBy.="		 , pfg.cod_grupo 				            \n";
        $stGroupBy.="		 , pfg.ano_exercicio 				        \n";
        $stGroupBy.="		 , agc.descricao 				            \n";

        $stSql.= $condicao . $stGroupBy;

        return $stSql;
    }

    //Monta a Início do Processo Fiscal com o Crédito
    public function recuperaInicioProcessoFiscalListaCredito(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInicioProcessoFiscalCredito($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaInicioProcessoFiscalCredito($condicao)
    {
        $stSql ="     	SELECT pf.cod_processo 				                \n";
        $stSql.="		     , pfc.cod_credito 				                \n";
        $stSql.="		     , pfc.cod_especie 				                \n";
        $stSql.="		     , pfc.cod_genero				                \n";
        $stSql.="		     , pfc.cod_natureza				                \n";
        $stSql.="		     , mc.descricao_credito				            \n";
        $stSql.="	      FROM fiscalizacao.processo_fiscal_credito pfc 	\n";
        $stSql.="	INNER JOIN fiscalizacao.processo_fiscal AS pf 		    \n";
        $stSql.="		    ON pfc.cod_processo = pf.cod_processo 		    \n";
        $stSql.="	INNER JOIN monetario.credito AS mc 	 		            \n";
        $stSql.="		    ON pfc.cod_credito = mc.cod_credito 		    \n";
        $stSql.="	       AND pfc.cod_especie = mc.cod_especie 	 	    \n";

        $stGroupBy =" GROUP BY pf.cod_processo 				                \n";
        $stGroupBy.="		 , pfc.cod_credito 				                \n";
        $stGroupBy.="		 , pfc.cod_especie 			    	            \n";
        $stGroupBy.="		 , pfc.cod_genero 				                \n";
        $stGroupBy.="		 , pfc.cod_natureza 			    	        \n";
        $stGroupBy.="		 , mc.descricao_credito				            \n";

        $stSql.= $condicao . $stGroupBy;

        return $stSql;
    }

    //Monta a Início do Processo Fiscal com o Crédito/Grupo
    public function recuperaInicioProcessoFiscalListaCreditoGrupo(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInicioProcessoFiscalCreditoGrupo($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaInicioProcessoFiscalCreditoGrupo($condicao)
    {
        $stSqlG ="     	SELECT pf.cod_processo 				                        \n";
        $stSqlG.="		     , agc.descricao as descricao	 		                \n";
        $stSqlG.="		     , (pfg.cod_grupo ||'/'|| pfg.ano_exercicio) as codigo  \n";
        $stSqlG.="	      FROM fiscalizacao.processo_fiscal_grupo pfg 	            \n";
        $stSqlG.="	INNER JOIN fiscalizacao.processo_fiscal AS pf 		            \n";
        $stSqlG.="		    ON pfg.cod_processo = pf.cod_processo 		            \n";
        $stSqlG.="	INNER JOIN arrecadacao.grupo_credito AS agc  		            \n";
        $stSqlG.="		    ON pfg.cod_grupo = agc.cod_grupo  		                \n";
        $stSqlG.="	       AND pfg.ano_exercicio = agc.ano_exercicio 	            \n";

        $stGroupByG ="GROUP BY pf.cod_processo 				                        \n";
        $stGroupByG.="		 , pfg.cod_grupo 				                        \n";
        $stGroupByG.="		 , pfg.ano_exercicio 				                    \n";
        $stGroupByG.="		 , agc.descricao				                        \n";

        $stSqlC ="     	SELECT pf.cod_processo 				                        \n";
        $stSqlC.="		     , mc.descricao_credito as descricao 		            \n";
        $stSqlC.="		     , (pfc.cod_credito ||'.'|| pfc.cod_especie ||'.'||     \n";
        $stSqlC.="		        pfc.cod_genero ||'.'|| pfc.cod_natureza) as codigo  \n";
        $stSqlC.="	      FROM fiscalizacao.processo_fiscal_credito pfc 	        \n";
        $stSqlC.="	INNER JOIN fiscalizacao.processo_fiscal AS pf 		            \n";
        $stSqlC.="		    ON pfc.cod_processo = pf.cod_processo 		            \n";
        $stSqlC.="	INNER JOIN monetario.credito AS mc 	 		                    \n";
        $stSqlC.="		    ON pfc.cod_credito = mc.cod_credito 		            \n";
        $stSqlC.="	       AND pfc.cod_especie = mc.cod_especie 	 	            \n";
        $stSqlC.="	       AND pfc.cod_genero = mc.cod_genero 	 	                \n";
        $stSqlC.="	       AND pfc.cod_natureza = mc.cod_natureza	 	            \n";

        $stGroupByC ="GROUP BY pf.cod_processo 				                        \n";
        $stGroupByC.="		 , pfc.cod_credito 			    	                    \n";
        $stGroupByC.="		 , pfc.cod_especie 				                        \n";
        $stGroupByC.="		 , pfc.cod_genero				                        \n";
        $stGroupByC.="		 , pfc.cod_natureza 				                    \n";
        $stGroupByC.="		 , mc.descricao_credito				                    \n";

        $stSqlG.= $condicao . $stGroupByG;
        $stSqlC.= $condicao . $stGroupByC;

        $stSql = $stSqlG;
        $stSql.= " UNION ALL ";
        $stSql.= $stSqlC;

        return $stSql;
    }

    public function recuperaUltimoCodProcessoFiscal(&$rsRecordSet, $stCondicao)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaUltimoCod().$stCondicao;

        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, false);

        return $obErro;

    }

    public function montaUltimoCod()
    {
        $stSql ="     	SELECT max(cod_processo) AS codigo 			\n";
        $stSql.=" 	      FROM fiscalizacao.processo_fiscal 		\n";

        return $stSql;
    }

    public function recuperaServidor(&$rsRecordSet, $stCondicao)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaServidor().$stCondicao;

        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, false);

        return $obErro;
    }

    public function montaServidor()
    {
        $stSql ="     	SELECT * 						                \n";
        $stSql.=" 	      FROM sw_cgm sw 					            \n";
        $stSql.="	INNER JOIN pessoal.servidor sr 				        \n";
        $stSql.="		    ON sw.numcgm = sr.numcgm 			        \n";
        $stSql.="	 LEFT JOIN pessoal.servidor_contrato_servidor scs 	\n";
        $stSql.="		    ON sr.cod_servidor = scs.cod_servidor 		\n";

        return $stSql;
    }

    public function recuperaFiscal(&$rsRecordSet, $stCondicao)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaFiscal().$stCondicao;

        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, false);

        return $obErro;
    }

    public function montaFiscal()
    {
        $stSql ="     	SELECT * 						                \n";
        $stSql.=" 	      FROM sw_cgm sw 					            \n";
        $stSql.="	 LEFT JOIN pessoal.servidor sr 				        \n";
        $stSql.="		    ON sw.numcgm = sr.numcgm 			        \n";
        $stSql.="	 LEFT JOIN pessoal.servidor_contrato_servidor scs 	\n";
        $stSql.="		    ON sr.cod_servidor = scs.cod_servidor 		\n";
        $stSql.="	INNER JOIN fiscalizacao.fiscal f 			        \n";
        $stSql.="		    ON sw.numcgm = f.numcgm 			        \n";

        return $stSql;
    }

    public function recuperaFundamentacaoLegal(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFundamentacaoLegal($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaFundamentacaoLegal($condicao)
    {
        $stSql ="         SELECT distinct pf.cod_processo           		            \n";
        $stSql.=" 			   , pf.cod_processo 	 					                \n";
        $stSql.="			   , pf.cod_processo_protocolo 					            \n";
        $stSql.="		       , pf.ano_exercicio 						                \n";
        $stSql.="	           , tf.cod_tipo 							                \n";
        $stSql.="	           , pfe.inscricao_economica AS inscricao 				    \n";
        $stSql.="			   , pf.cod_natureza							            \n";
        $stSql.="	        FROM fiscalizacao.processo_fiscal pf					    \n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 			\n";
        $stSql.="             ON pf.cod_processo = pfe.cod_processo 					\n";
        $stSql.="   NATURAL JOIN fiscalizacao.tipo_fiscalizacao as tf				    \n";
        $stSql.="   NATURAL JOIN administracao.usuario as au					        \n";
        $stSql.="     INNER JOIN fiscalizacao.natureza_fiscalizacao as fnf				\n";
        $stSql.="	          ON fnf.cod_natureza = pf.cod_natureza						\n";
        $stSql.="     INNER JOIN public.sw_processo AS psp						        \n";
        $stSql.="	          ON pf.ano_exercicio = psp.ano_exercicio   				\n";
        $stSql.="	         AND pf.cod_processo_protocolo = psp.cod_processo     		\n";
        $stSql.="     INNER JOIN (SELECT ffc.cod_fiscal 						        \n";
        $stSql.="			           , ffc.numcgm 							        \n";
        $stSql.="			           , ffpf.cod_processo 						        \n";
        $stSql.="		            FROM fiscalizacao.fiscal as ffc 					\n";
        $stSql.="	          INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf	\n";
        $stSql.="		              ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 		\n";
        $stSql.="	          ON fc.cod_processo = pf.cod_processo 				        \n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif			    \n";
        $stSql.="             ON pf.cod_processo = fif.cod_processo 					\n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc    		\n";
        $stSql.="             ON pf.cod_processo = pfc.cod_processo 					\n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaFundamentacaoLegalObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFundamentacaoLegalObra($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaFundamentacaoLegalObra($condicao)
    {
        $stSql =" 	SELECT distinct pf.cod_processo 			                    \n";
        $stSql.=" 	  	          , pf.cod_processo_protocolo 			            \n";
        $stSql.=" 		          , pf.ano_exercicio					            \n";
        $stSql.=" 		          , tf.cod_tipo 						            \n";
        $stSql.=" 		          , pfo.inscricao_municipal AS inscricao            \n";
        $stSql.="                 , tf.descricao                                    \n";
        $stSql.=" 		       FROM fiscalizacao.processo_fiscal pf 				\n";
        $stSql.=" 	     INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo 		\n";
        $stSql.=" 	             ON pf.cod_processo = pfo.cod_processo 			    \n";
        $stSql.=" 	     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf			\n";
        $stSql.=" 	             ON pf.cod_tipo = tf.cod_tipo 			         	\n";
        $stSql.=" 	   NATURAL JOIN administracao.usuario as au				        \n";
        $stSql.=" 	     INNER JOIN fiscalizacao.natureza_fiscalizacao as fnf		\n";
        $stSql.=" 	             ON fnf.cod_natureza = pf.cod_natureza		     	\n";
        $stSql.=" 	     INNER JOIN public.sw_processo AS psp				        \n";
        $stSql.="                ON pf.ano_exercicio = psp.ano_exercicio			\n";
        $stSql.=" 	      	    AND pf.cod_processo_protocolo = psp.cod_processo	\n";
        $stSql.=" 	     INNER JOIN (SELECT ffc.cod_fiscal 				            \n";
        $stSql.=" 	        		 , ffc.numcgm					                \n";
        $stSql.=" 	      			 , ffpf.cod_processo 				            \n";
        $stSql.=" 	      	   FROM fiscalizacao.fiscal as ffc			            \n";
        $stSql.=" 	     INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf	    \n";
        $stSql.=" 	             ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc	        \n";
        $stSql.=" 	      	     ON fc.cod_processo = pf.cod_processo			    \n";
        $stSql.=" 	LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif		    \n";
        $stSql.=" 	             ON pf.cod_processo = fif.cod_processo 		        \n";
        $stSql.=" 	LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc 	\n";
        $stSql.=" 	             ON pf.cod_processo = pfc.cod_processo   		    \n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaEnderecoProcesso(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaEnderecoProcesso($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

            return $obErro;
    }

    private function montaEnderecoProcesso($condicao)
    {
        $stSql ="          SELECT DISTINCT pf.cod_processo											\n";
        $stSql.="               , TO_CHAR(pf.periodo_inicio , 'DD/MM/YYYY') as periodo_inicio       \n";
        $stSql.="	       		, TO_CHAR(pf.periodo_termino , 'DD/MM/YYYY') as periodo_termino     \n";
        $stSql.="	       		, TO_CHAR(pf.previsao_inicio , 'DD/MM/YYYY') as previsao_inicio	\n";
        $stSql.="	       		, TO_CHAR(pf.previsao_termino , 'DD/MM/YYYY') as previsao_termino	\n";
        $stSql.="	       		, TO_CHAR(fif.prazo_entrega , 'DD/MM/YYYY') as prazo_entrega	\n";
        $stSql.="	       		, TO_CHAR(fif.dt_inicio , 'DD/MM/YYYY') as dt_inicio	\n";
        $stSql.="	       		, fif.cod_tipo_documento									\n";
        $stSql.="	       		, fif.cod_documento											\n";
        $stSql.="	       		, swtl.nom_tipo||' '||swnl.nom_logradouro as logradouro_f 	\n";
        $stSql.="	       		, swtl2.nom_tipo||' '||swnl2.nom_logradouro as logradouro_i \n";
        $stSql.="	       		, iim.numero AS numero_f 									\n";
        $stSql.="	       		, edi.numero AS numero_i									\n";
        $stSql.="	       		, iim.complemento AS complemento_f							\n";
        $stSql.="	       		, edi.complemento AS complemento_i							\n";
        $stSql.="	       		, swuf.sigla_uf 											\n";
        $stSql.="	       		, swmu.nom_municipio										\n";
        $stSql.="	       		, swb.nom_bairro											\n";
        $stSql.="	       		, swb.cod_bairro 											\n";
        $stSql.="	       		, edi.cod_logradouro										\n";
        $stSql.="	       		, edi.cep													\n";
        $stSql.="	       		, edi.caixa_postal											\n";
        $stSql.="	       		, edf.inscricao_municipal									\n";
        $stSql.="	       		, ece.inscricao_economica									\n";
        $stSql.="	       		, ea.nom_atividade											\n";
        $stSql.="	       		, ea.cod_atividade											\n";
        $stSql.="	       		, publico.mascara_cpf_cnpj(swcpj.cnpj, 'cnpj') as cnpj      \n";
        $stSql.="	       		, publico.mascara_cpf_cnpj(swcpf.cpf, 'cpf') as cpf  	    \n";
        $stSql.="	       		, swtl2.nom_tipo||' '||swnl2.nom_logradouro||' '||edi.numero||' '||edi.complemento||' '||swb.nom_bairro||' '||swmu.nom_municipio||' '||swuf.nom_uf as endereco \n";
        $stSql.="	       		, (SELECT eml.nom_modalidade 								\n";
        $stSql.="	             	  FROM economico.modalidade_lancamento AS eml			\n";
        $stSql.="	            	 WHERE eml.cod_modalidade = COALESCE(eceml.cod_modalidade, eam.cod_modalidade) \n";
        $stSql.="	         	 )AS nom_modalidade										    \n";
        $stSql.="	       		, COALESCE(eceml.cod_modalidade, eam.cod_modalidade) AS cod_modalidade \n";
        $stSql.="	       		, COALESCE(ecd.numcgm, ecf.numcgm, eca.numcgm) AS numcgm	\n";
        $stSql.="	       	    , (SELECT cgm.nom_cgm 										\n";
        $stSql.="	             	  FROM sw_cgm AS cgm 									\n";
        $stSql.="	           	 	 WHERE cgm.numcgm = COALESCE(ecd.numcgm, ecf.numcgm, eca.numcgm) \n";
        $stSql.="	         	 ) AS nom_cgm 												\n";
        $stSql.="	       		, swcpj.insc_estadual as inscricao_estadual  				\n";
          $stSql.="           FROM fiscalizacao.processo_fiscal as  pf    		            \n";
        $stSql.="      LEFT JOIN fiscalizacao.inicio_fiscalizacao AS fif					\n";
        $stSql.="	      	  ON pf.cod_processo = fif.cod_processo							\n";
        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 						\n";
        $stSql.="	      	  ON pf.cod_tipo = tf.cod_tipo									\n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc				\n";
        $stSql.="             ON pf.cod_processo = pfc.cod_processo 						\n";
        $stSql.="     INNER JOIN administracao.modelo_documento AS amd 						\n";
        $stSql.="	      	  ON fif.cod_documento = amd.cod_documento 						\n";
           $stSql.="	       	 AND fif.cod_tipo_documento = amd.cod_tipo_documento			\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 				\n";
        $stSql.="	      	  ON pfe.cod_processo = pf.cod_processo							\n";
        $stSql.="     INNER JOIN economico.cadastro_economico AS ece 						\n";
        $stSql.="	      	  ON ece.inscricao_economica = pfe.inscricao_economica			\n";
        $stSql.="      LEFT JOIN economico.cadastro_economico_empresa_direito AS ecd 		\n";
        $stSql.="	      	  ON ecd.inscricao_economica = ece.inscricao_economica			\n";
        $stSql.="      LEFT JOIN economico.cadastro_economico_empresa_fato AS ecf			\n";
        $stSql.="	      	  ON ecf.inscricao_economica = ece.inscricao_economica			\n";
        $stSql.="      LEFT JOIN economico.cadastro_economico_autonomo AS eca 				\n";
        $stSql.="	      	  ON eca.inscricao_economica = ece.inscricao_economica			\n";
        $stSql.="     INNER JOIN (SELECT ate.inscricao_economica							\n";
        $stSql.="   		        , max(ocorrencia_atividade) AS ocorrencia_atividade		\n";
        $stSql.="		     FROM economico.atividade_cadastro_economico AS ate				\n";
        $stSql.="	         GROUP BY inscricao_economica 									\n";
        $stSql.="	        )AS ate														    \n";
        $stSql.="	      	  ON ate.inscricao_economica = ece.inscricao_economica			\n";
        $stSql.="     INNER JOIN economico.atividade_cadastro_economico AS eac 				\n";
        $stSql.="	      	  ON eac.inscricao_economica = ate.inscricao_economica			\n";
           $stSql.="	     	 AND eac.ocorrencia_atividade = ate.ocorrencia_atividade 		\n";
        $stSql.="      LEFT JOIN economico.cadastro_economico_modalidade_lancamento AS eceml 	\n";
        $stSql.="	      	  ON eceml.inscricao_economica = ece.inscricao_economica 		\n";
           $stSql.="	     	 AND eceml.ocorrencia_atividade = eac.ocorrencia_atividade 		\n";
           $stSql.="	     	 AND eceml.cod_atividade = eac.cod_atividade					\n";
        $stSql.="     INNER JOIN economico.atividade AS ea									\n";
        $stSql.="	      	  ON ea.cod_atividade = eac.cod_atividade 						\n";
        $stSql.="      LEFT JOIN economico.atividade_modalidade_lancamento AS eam			\n";
        $stSql.="	      	  ON eam.cod_atividade = ea.cod_atividade 						\n";
        $stSql.="      LEFT JOIN economico.domicilio_informado edi							\n";
        $stSql.="	      	  ON edi.inscricao_economica = ece.inscricao_economica			\n";
        $stSql.="      LEFT JOIN economico.domicilio_fiscal edf								\n";
        $stSql.=" 	      	  ON edf.inscricao_economica = ece.inscricao_economica			\n";
        $stSql.="      LEFT JOIN economico.sociedade eso									\n";
        $stSql.="	      	  ON eso.inscricao_economica = ece.inscricao_economica			\n";
        $stSql.="      LEFT JOIN imobiliario.imovel iim										\n";
        $stSql.="	      	  ON iim.inscricao_municipal = edf.inscricao_municipal			\n";
        $stSql.="      LEFT JOIN imobiliario.imovel_confrontacao iic 						\n";
        $stSql.="	      	  ON iic.inscricao_municipal = iim.inscricao_municipal			\n";
        $stSql.="      LEFT JOIN imobiliario.confrontacao_trecho ict 						\n";
        $stSql.="	      	  ON ict.cod_confrontacao = iic.cod_confrontacao 				\n";
           $stSql.="	     	 AND ict.cod_lote = iic.cod_lote 								\n";
           $stSql.="	     	 AND ict.principal = true 										\n";
        $stSql.="      LEFT JOIN sw_uf swuf													\n";
        $stSql.="	      	  ON swuf.cod_uf = edi.cod_uf									\n";
        $stSql.="      LEFT JOIN sw_municipio swmu											\n";
        $stSql.="	      	  ON swmu.cod_municipio = edi.cod_municipio						\n";
           $stSql.="	     	 AND swmu.cod_uf = edi.cod_uf									\n";
        $stSql.="      LEFT JOIN sw_bairro swb 												\n";
        $stSql.="	      	  ON swb.cod_bairro = edi.cod_bairro							\n";
           $stSql.="	     	 AND swb.cod_uf = edi.cod_uf									\n";
           $stSql.="	     	 AND swb.cod_municipio = edi.cod_municipio 						\n";
        $stSql.="      LEFT JOIN sw_nome_logradouro swnl									\n";
        $stSql.="	      	  ON swnl.cod_logradouro = ict.cod_logradouro					\n";
        $stSql.="      LEFT JOIN sw_tipo_logradouro swtl									\n";
        $stSql.="	      	  ON swtl.cod_tipo = swnl.cod_tipo 								\n";
        $stSql.="      LEFT JOIN sw_nome_logradouro swnl2									\n";
        $stSql.="	      	  ON swnl2.cod_logradouro = edi.cod_logradouro					\n";
        $stSql.="      LEFT JOIN sw_tipo_logradouro swtl2									\n";
        $stSql.="	      	  ON swtl2.cod_tipo = swnl2.cod_tipo							\n";
        $stSql.="      LEFT JOIN sw_cgm_pessoa_juridica as swcpj							\n";
        $stSql.="	      	  ON swcpj.numcgm = COALESCE(ecd.numcgm, ecf.numcgm, eca.numcgm)\n";
        $stSql.="      LEFT JOIN sw_cgm_pessoa_fisica as swcpf                              \n";
        $stSql.="	      	  ON swcpf.numcgm = COALESCE(ecd.numcgm, ecf.numcgm, eca.numcgm)\n";
        $stSql.= $condicao;

        return $stSql;
    }

    //Processo Fiscal com Imóvel
    public function recuperaEnderecoProcessoObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaEnderecoProcessoObra($stCondicao).$stOrdem;

        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaEnderecoProcessoObra($condicao)
    {
        $stSql  ="   SELECT DISTINCT                                                                    \n";
        $stSql .="                  pf.cod_processo                                                     \n";
        $stSql .="                  , TO_CHAR(pf.periodo_inicio , 'DD/MM/YYYY') as periodo_inicio       \n";
        $stSql .="	       		    , TO_CHAR(pf.periodo_termino , 'DD/MM/YYYY') as periodo_termino     \n";
        $stSql .="	       		    , TO_CHAR(pf.previsao_termino , 'DD/MM/YYYY') as previsao_termino	\n";
        $stSql .="  	       		, publico.mascara_cpf_cnpj(swcpj.cnpj, 'cnpj') as cnpj              \n";
        $stSql .="	           		, publico.mascara_cpf_cnpj(swcpf.cpf, 'cpf') as cpf  	            \n";
        $stSql .="                  , pf.cod_processo_protocolo                                         \n";
        $stSql .="                  , pf.ano_exercicio                                                  \n";
        $stSql .="                  , tf.cod_tipo                                                       \n";
        $stSql .="                  , sw_cgm.nom_cgm                                                    \n";
        $stSql .="                  , tf.descricao                                                      \n";
        $stSql .="                  , pfo.inscricao_municipal                                           \n";
        $stSql .="                  , pfo.cod_local                                                     \n";
        $stSql .="                  , imobiliario.fn_busca_endereco_imovel( pfo.inscricao_municipal ) AS endereco               \n";
        $stSql .="	       		, swcpj.insc_estadual as inscricao_estadual  				            \n";
        $stSql .="          FROM fiscalizacao.processo_fiscal pf                                        \n";
        $stSql .="    INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo                              \n";
        $stSql .="            ON pf.cod_processo = pfo.cod_processo                                     \n";
        $stSql .="    INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf                                   \n";
        $stSql .="            ON pf.cod_tipo = tf.cod_tipo                                              \n";
        $stSql .="    INNER JOIN (SELECT ffc.cod_fiscal                                                 \n";
        $stSql .="                      ,ffc.numcgm                                                     \n";
        $stSql .="                      ,ffpf.cod_processo                                              \n";
        $stSql .="                  FROM fiscalizacao.fiscal as ffc                                     \n";
        $stSql .="            INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf                    \n";
        $stSql .="                    ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc                        \n";
        $stSql .="                    ON fc.cod_processo = pf.cod_processo                              \n";
        $stSql .="            INNER JOIN imobiliario.imovel AS i                                        \n";
        $stSql .="                    ON i.inscricao_municipal = pfo.inscricao_municipal                \n";

        $stSql .="            INNER JOIN
                                (
                                    SELECT
                                        tmp.*
                                    FROM
                                        imobiliario.proprietario AS tmp
                                    INNER JOIN
                                        (
                                            SELECT
                                                inscricao_municipal,
                                                max ( timestamp ) as timestamp
                                            FROM
                                                imobiliario.proprietario
                                            GROUP BY
                                                inscricao_municipal
                                        )AS tmp2
                                    ON
                                        tmp.inscricao_municipal = tmp2.inscricao_municipal
                                        AND tmp.timestamp = tmp2.timestamp
                                )AS prop
                               ON
                                    prop.inscricao_municipal = i.inscricao_municipal                    \n";

        $stSql .="             LEFT JOIN imobiliario.imovel_correspondencia as iic                      \n";
        $stSql .="                    ON iic.inscricao_municipal = i.inscricao_municipal                \n";
        $stSql .="             LEFT JOIN sw_cgm                                                         \n";
        $stSql .="                    ON sw_cgm.numcgm = prop.numcgm                                      \n";
        $stSql.="      LEFT JOIN sw_cgm_pessoa_juridica as swcpj            							\n";
        $stSql.="	      	  ON swcpj.numcgm = prop.numcgm                                               \n";
        $stSql.="      LEFT JOIN sw_cgm_pessoa_fisica as swcpf                                          \n";
        $stSql.="	      	  ON swcpf.numcgm = prop.numcgm                                               \n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaDadosFiscal(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaDadosFiscal($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaDadosFiscal($condicao)
    {
        $stSql ="     SELECT sw_cgm.numcgm		  						    			\n";
        $stSql.="          , sw_cgm.nom_cgm 					        	    		\n";
        $stSql.="          , contrato.* 					   	                   		\n";
        $stSql.="          , cargo.descricao				                   			\n";
        $stSql.="       FROM fiscalizacao.fiscal		                	    		\n";
        $stSql.=" INNER JOIN sw_cgm 				                        			\n";
        $stSql.="         ON sw_cgm.numcgm = fiscal.numcgm          		    		\n";
        $stSql.=" INNER JOIN pessoal.contrato	        			    	    		\n";
        $stSql.="         ON fiscal.cod_contrato = contrato.cod_contrato	    		\n";
        $stSql.=" INNER JOIN pessoal.contrato_servidor			        		    	\n";
        $stSql.="         ON contrato_servidor.cod_contrato = contrato.cod_contrato	    \n";
        $stSql.=" INNER JOIN pessoal.cargo						                        \n";
        $stSql.="         ON cargo.cod_cargo = contrato_servidor.cod_cargo				\n";
        $stSql.= $condicao;

        return $stSql ;
    }

    public function recuperaListaDocumentosParaConsultaProcessoFiscal(&$rsRecordSet, $inProcesso, $boTransacao = "")
    {
        $obErro         = new Erro;
        $obConexao      = new Conexao;
        $rsRecordSet    = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentosParaConsultaProcessoFiscal($inProcesso);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaListaDocumentosParaConsultaProcessoFiscal($inProcesso)
    {
        $stSql = "
            SELECT
                documento.cod_documento,
                documento.nom_documento,
                CASE WHEN documentos_entrega.situacao IS NULL THEN
                    'espera'
                ELSE
                    CASE WHEN documentos_entrega.situacao = 'R' THEN
                        'recebido'
                    ELSE
                        'devolvido'
                    END
                END AS situacao

            FROM
                fiscalizacao.documento

            INNER JOIN
                fiscalizacao.inicio_fiscalizacao_documentos
            ON
                inicio_fiscalizacao_documentos.cod_documento = documento.cod_documento

            LEFT JOIN
                (
                    SELECT
                        tmp.*
                    FROM
                        fiscalizacao.documentos_entrega AS tmp
                    INNER JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_documento,
                                cod_processo
                            FROM
                                fiscalizacao.documentos_entrega
                            GROUP BY
                                cod_documento,
                                cod_processo
                        )AS tmp2
                    ON
                        tmp2.cod_documento = tmp.cod_documento
                        AND tmp2.cod_processo = tmp.cod_processo
                        AND tmp2.timestamp = tmp.timestamp
                )AS documentos_entrega
            ON
                documentos_entrega.cod_documento = inicio_fiscalizacao_documentos.cod_documento
                AND documentos_entrega.cod_processo = inicio_fiscalizacao_documentos.cod_processo

            WHERE
                inicio_fiscalizacao_documentos.cod_processo = ".$inProcesso;

        return $stSql;
    }

    public function recuperaListaInfracoesParaConsultaProcessoFiscal(&$rsRecordSet, $inProcesso, $boTransacao = "")
    {
        $obErro         = new Erro;
        $obConexao      = new Conexao;
        $rsRecordSet    = new RecordSet;

        $stSql = $this->montaRecuperaListaInfracoesParaConsultaProcessoFiscal($inProcesso);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaListaInfracoesParaConsultaProcessoFiscal($inProcesso)
    {
        $stSql = "
            SELECT
                CASE WHEN notificacao_infracao.cod_processo IS NOT NULL THEN
                    'notificação'
                ELSE
                    'auto'
                END as tipo,
                COALESCE( notificacao_infracao.cod_penalidade, auto_infracao.cod_penalidade ) AS cod_penalidade,
                COALESCE( notificacao_infracao.cod_infracao, auto_infracao.cod_infracao ) AS cod_infracao,
                COALESCE( notificacao_infracao.nom_infracao, auto_infracao.nom_infracao ) AS nom_infracao,
                COALESCE( notificacao_infracao.nom_penalidade, auto_infracao.nom_penalidade ) AS nom_penalidade

            FROM
                fiscalizacao.processo_fiscal

            LEFT JOIN
                (
                    SELECT
                        auto_fiscalizacao.cod_processo,
                        auto_fiscalizacao.cod_auto_fiscalizacao,
                        auto_infracao.cod_penalidade,
                        auto_infracao.cod_infracao,
                        CASE WHEN auto_infracao_multa.cod_infracao IS NOT NULL THEN
                            'multa'
                        ELSE
                            'outros'
                        END AS tipo_penalidade,
                        infracao.nom_infracao,
                        penalidade.nom_penalidade

                    FROM
                        fiscalizacao.auto_fiscalizacao

                    INNER JOIN
                        fiscalizacao.auto_infracao
                    ON
                        auto_infracao.cod_processo = auto_fiscalizacao.cod_processo
                        AND auto_infracao.cod_auto_fiscalizacao = auto_fiscalizacao.cod_auto_fiscalizacao

                    LEFT JOIN
                        fiscalizacao.auto_infracao_outros
                    ON
                        auto_infracao_outros.cod_infracao = auto_infracao.cod_infracao
                        AND auto_infracao_outros.cod_auto_fiscalizacao = auto_infracao.cod_auto_fiscalizacao
                        AND auto_infracao_outros.cod_penalidade = auto_infracao.cod_penalidade
                        AND auto_infracao_outros.cod_processo = auto_infracao.cod_processo

                    LEFT JOIN
                        fiscalizacao.auto_infracao_multa
                    ON
                        auto_infracao_multa.cod_infracao = auto_infracao.cod_infracao
                        AND auto_infracao_multa.cod_auto_fiscalizacao = auto_infracao.cod_auto_fiscalizacao
                        AND auto_infracao_multa.cod_penalidade = auto_infracao.cod_penalidade
                        AND auto_infracao_multa.cod_processo = auto_infracao.cod_processo

                    INNER JOIN
                        fiscalizacao.infracao
                    ON
                        infracao.cod_infracao = auto_infracao.cod_infracao

                    INNER JOIN
                        fiscalizacao.penalidade
                    ON
                        penalidade.cod_penalidade = auto_infracao.cod_penalidade
                )AS auto_infracao
            ON
                auto_infracao.cod_processo = processo_fiscal.cod_processo

            LEFT JOIN
                (
                    SELECT
                        notificacao_fiscalizacao.cod_processo,
                        notificacao_infracao.cod_infracao,
                        notificacao_infracao.cod_penalidade,
                        infracao.nom_infracao,
                        penalidade.nom_penalidade

                    FROM
                        fiscalizacao.notificacao_fiscalizacao

                    INNER JOIN
                        fiscalizacao.notificacao_infracao
                    ON
                        notificacao_infracao.cod_processo = notificacao_fiscalizacao.cod_processo

                    INNER JOIN
                        fiscalizacao.infracao
                    ON
                        infracao.cod_infracao = notificacao_infracao.cod_infracao

                    INNER JOIN
                        fiscalizacao.penalidade
                    ON
                        penalidade.cod_penalidade = notificacao_infracao.cod_penalidade
                )AS notificacao_infracao
            ON
                notificacao_infracao.cod_processo = processo_fiscal.cod_processo

            WHERE
                COALESCE( notificacao_infracao.cod_infracao, auto_infracao.cod_infracao ) IS NOT NULL
                AND processo_fiscal.cod_processo = ".$inProcesso;

        return $stSql;
    }

    public function recuperaListaParaConsultaProcessoFiscal(&$rsRecordSet, $stCondicao, $boTransacao = "")
    {
        $obErro         = new Erro;
        $obConexao      = new Conexao;
        $rsRecordSet    = new RecordSet;

        $stSql = $this->montaRecuperaListaParaConsultaProcessoFiscal().$stCondicao;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaListaParaConsultaProcessoFiscal()
    {
        $stSql = "
            SELECT
                processo_fiscal.cod_tipo,
                tipo_fiscalizacao.descricao,
                processo_fiscal.cod_processo,
                processo_fiscal.cod_processo_protocolo,
                processo_fiscal.ano_exercicio,
                to_char ( processo_fiscal.periodo_inicio, 'dd/mm/YYYY' ) AS periodo_inicio,
                to_char ( processo_fiscal.periodo_termino, 'dd/mm/YYYY' ) AS periodo_termino,
                to_char ( processo_fiscal.previsao_inicio, 'dd/mm/YYYY' ) AS previsao_inicio,
                to_char ( processo_fiscal.previsao_termino, 'dd/mm/YYYY' ) AS previsao_termino,
                fiscal.cod_fiscal,
                (
                    SELECT
                        nom_cgm
                    FROM
                        sw_cgm
                    WHERE
                        numcgm = fiscal.numcgm
                )AS nom_fiscal,
                CASE WHEN (termino_fiscalizacao.cod_processo IS NOT NULL) THEN
                    'Processo Finalizado'
                ELSE
                    CASE WHEN ( inicio_fiscalizacao IS NOT NULL ) THEN
                        'Processo Iniciado'
                    ELSE
                        'Processo Cadastrado'
                    END
                END AS status,
                COALESCE( processo_fiscal_empresa.inscricao_economica, processo_fiscal_obras.inscricao_municipal ) AS inscricao

            FROM
                fiscalizacao.processo_fiscal

            LEFT JOIN
                fiscalizacao.processo_fiscal_empresa
            ON
                processo_fiscal_empresa.cod_processo = processo_fiscal.cod_processo

            LEFT JOIN
                fiscalizacao.processo_fiscal_obras
            ON
                processo_fiscal_obras.cod_processo = processo_fiscal.cod_processo

            INNER JOIN
                fiscalizacao.tipo_fiscalizacao
            ON
                tipo_fiscalizacao.cod_tipo = processo_fiscal.cod_tipo

            INNER JOIN
                fiscalizacao.fiscal_processo_fiscal
            ON
                fiscal_processo_fiscal.cod_processo = processo_fiscal.cod_processo

            INNER JOIN
                fiscalizacao.fiscal
            ON
                fiscal.cod_fiscal = fiscal_processo_fiscal.cod_fiscal

            LEFT JOIN
                fiscalizacao.inicio_fiscalizacao
            ON
                inicio_fiscalizacao.cod_processo = processo_fiscal.cod_processo

            LEFT JOIN
                fiscalizacao.termino_fiscalizacao
            ON
                termino_fiscalizacao.cod_processo = processo_fiscal.cod_processo
        ";

        return $stSql;
    }
}
?>
