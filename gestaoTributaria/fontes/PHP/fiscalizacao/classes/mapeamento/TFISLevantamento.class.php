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
    * Classe de mapeamento para FISCALIZACAO.LEVANTAMENTO
    * Data de Criacao: 15/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vaconcellos de Magalhães
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Mapeamento

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CLA_PERSISTENTE );

class TFISLevantamento extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function __construct()
    {
            parent::Persistente();
            $this->setTabela( 'fiscalizacao.levantamento' );

            $this->setCampoCod( 'cod_processo' );
            $this->setComplementoChave( 'competencia' );

        $this->AddCampo( 'cod_processo','integer',true,'',true,true,true );
        $this->AddCampo( 'competencia','varchar',true,'7',true,true,true );
        $this->AddCampo( 'receita_declarada','numeric',true,'14,2',false,false );
        $this->AddCampo( 'receita_efetiva','numeric',true,'14,2',false,false );
        $this->AddCampo( 'iss_pago','numeric',true,'14,2',false,false );
        $this->AddCampo( 'iss_devido','numeric',true,'14,2',false,false );
        $this->AddCampo( 'iss_devolver','numeric',true,'14,2',false,false );
        $this->AddCampo( 'iss_pagar','numeric',true,'14,2',false,false );
        $this->AddCampo( 'total_devolver','numeric',true,'14,2',false,false );
        $this->AddCampo( 'total_pagar','numeric',true,'14,2',false,false );
    }

    //Processo Fiscal com Empresa
    public function recuperaListaProcessoFiscalEconomica(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaListaProcessoFiscalEconomica($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }
    //busca
    private function montaRecuperaListaProcessoFiscalEconomica($condicao)
    {
            $stSql =" SELECT DISTINCT pf.cod_processo       		  								\n";
            $stSql.="	        	, tf.cod_tipo                             							\n";
            $stSql.="	        	, tf.descricao 											\n";
            $stSql.="	        	, pfe.inscricao_economica AS inscricao          						\n";
            $stSql.="	        	, fc.numcgm                                                     				\n";
            $stSql.="	        	, fc.cod_fiscal 										\n";
            $stSql.="	        	, ea.cod_atividade										\n";
            $stSql.="	        	, ea.cod_estrutural										\n";
            $stSql.="	        	, ea.nom_atividade				       						\n";
            $stSql.="				, eaml.cod_modalidade				        				\n";
            $stSql.="				, pf.periodo_inicio				        				\n";
            $stSql.="				, pf.periodo_termino				        				\n";
            $stSql.="				, ml.nom_modalidade				        				\n";
            $stSql.="				, fifde.status					        				\n";
            $stSql.="	     	 FROM fiscalizacao.processo_fiscal pf 		       							\n";
            $stSql.="      INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe    							\n";
            $stSql.="              ON pf.cod_processo = pfe.cod_processo 		        					\n";
            $stSql.="      INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 		       		 				\n";
            $stSql.="    	       ON pf.cod_tipo = tf.cod_tipo 			        					\n";
            $stSql.="      INNER JOIN ( SELECT ffc.cod_fiscal 										\n";
            $stSql.="			 			 , ffc.numcgm 								\n";
            $stSql.="			 			 , ffpf.cod_processo 							\n";
            $stSql.="		      		  FROM fiscalizacao.fiscal as ffc 							\n";
            $stSql.="				INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf  				\n";
        $stSql.="						ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 				\n";
            $stSql.="	       	   ON fc.cod_processo = pf.cod_processo                                         			\n";
            $stSql.="      INNER JOIN economico.atividade_cadastro_economico ace	        					\n";
            $stSql.="	      	   ON pfe.inscricao_economica = ace.inscricao_economica							\n";
            $stSql.="      INNER JOIN economico.atividade ea										\n";
            $stSql.="	       	   ON ace.cod_atividade = ea.cod_atividade								\n";
            $stSql.="  INNER JOIN ( select ace.inscricao_economica                                                                      \n";
            $stSql.="  , max( ace.ocorrencia_atividade) as ocorrencia_atividade                                                         \n";
            $stSql.="  FROM economico.atividade_cadastro_economico as ace                                                               \n";
            $stSql.="  GROUP BY ace.inscricao_economica                                                                                 \n";
            $stSql.="  ) AS max                                                                                                         \n";
            $stSql.="  ON max.ocorrencia_atividade = ace.ocorrencia_atividade                                                           \n";
            $stSql.="  AND max.inscricao_economica = ace.inscricao_economica                                                            \n";
            $stSql.="      LEFT JOIN economico.atividade_modalidade_lancamento eaml 							\n";
            $stSql.="	       	   ON eaml.cod_atividade = ea.cod_atividade								\n";
            $stSql.="      LEFT JOIN economico.modalidade_lancamento ml								\n";
            $stSql.="	       	   ON eaml.cod_modalidade = ml.cod_modalidade								\n";
            $stSql.=" LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif 								\n";
            $stSql.="              ON pf.cod_processo = fif.cod_processo 								\n";
            $stSql.=" LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao  AS ftf								\n";
            $stSql.="              ON ftf.cod_processo = pf.cod_processo								\n";
            $stSql.=" LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado  AS pfc							\n";
            $stSql.="              ON pfc.cod_processo = pf.cod_processo								\n";
            $stSql.=" LEFT OUTER JOIN ( SELECT (count(fifd.cod_documento) - count(fde.cod_documento)) as status                         \n";
            $stSql.="			 			 , fifd.cod_processo                                            	\n";
            $stSql.="	              	  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd 					\n";
            $stSql.="	   	   LEFT OUTER JOIN fiscalizacao.documentos_entrega AS fde 						\n";
            $stSql.="						ON fde.cod_documento = fifd.cod_documento                               \n";
            $stSql.="		     		 WHERE fde.cod_documento is null 							\n";
            $stSql.="		  		  GROUP BY fifd.cod_processo) AS fifde 							\n";
            $stSql.="	       	   ON fifde.cod_processo = fif.cod_processo  								\n";
            $stSql.="	    WHERE													\n";
            $stSql.= $condicao. "AND ace.principal = 't'";

        return $stSql;
    }

    public function recuperaListaProcessoFiscalEconomicaDocumentos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaListaProcessoFiscalEconomicaDocumentos($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaListaProcessoFiscalEconomicaDocumentos($condicao)
    {
        $stSql =" 	   	   SELECT pf.cod_processo       		  						\n";
        $stSql.="	        	, tf.cod_tipo 											\n";
        $stSql.="	        	, tf.descricao 											\n";
        $stSql.="	        	, pfe.inscricao_economica AS inscricao 					\n";
        $stSql.="	        	, fc.numcgm 											\n";
        $stSql.="	        	, fc.cod_fiscal 										\n";
        $stSql.="	        	, fifde.cod_documento									\n";
        $stSql.="	        	, fifde.cod_documento_entrega							\n";
        $stSql.="	        	, fifde.cod_processo				        			\n";
        $stSql.="	        	, fifde.nom_documento				        			\n";
        $stSql.="	     	 FROM fiscalizacao.processo_fiscal pf 		        		\n";
        $stSql.="      INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe  			\n";
        $stSql.="              ON pf.cod_processo = pfe.cod_processo 		   		 	\n";
        $stSql.="      INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 		   		 	\n";
        $stSql.="    	       ON pf.cod_tipo = tf.cod_tipo 			        		\n";
        $stSql.="      INNER JOIN ( SELECT ffc.cod_fiscal 								\n";
        $stSql.="			 			 , ffc.numcgm 									\n";
        $stSql.="			 			 , ffpf.cod_processo 							\n";
        $stSql.="		      		  FROM fiscalizacao.fiscal as ffc 					\n";
        $stSql.="		      	INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf  \n";
        $stSql.="				   		ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 		\n";
        $stSql.="	       	   ON fc.cod_processo = pf.cod_processo 					\n";
        $stSql.=" 	   LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif 			\n";
        $stSql.="              ON pf.cod_processo = fif.cod_processo 					\n";
        $stSql.=" LEFT OUTER JOIN ( SELECT fifd.cod_documento 							\n";
        $stSql.="		         		 , fde.cod_documento as cod_documento_entrega  	\n";
        $stSql.="			 			 , fifd.cod_processo 							\n";
        $stSql.="			 			 , fd.cod_documento ||' - '|| fd.nom_documento as nom_documento 	\n";
        $stSql.="	              	  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd 				\n";
        $stSql.="	   			INNER JOIN fiscalizacao.documento AS fd 				\n";
        $stSql.="						ON fd.cod_documento = fifd.cod_documento 		\n";
        $stSql.="	   	   LEFT OUTER JOIN fiscalizacao.documentos_entrega AS fde 		\n";
        $stSql.="						ON fde.cod_documento = fifd.cod_documento 		\n";
        $stSql.="		       		   AND fde.cod_processo = fifd.cod_processo 		\n";
        $stSql.="		  		  GROUP BY fifd.cod_documento 							\n";
        $stSql.="                        , fde.cod_documento 							\n";
        $stSql.="                        , fifd.cod_processo 							\n";
        $stSql.="                        , fd.cod_documento 							\n";
        $stSql.="                        , fd.nom_documento 							\n";
        $stSql.="		  ORDER BY fifd.cod_documento) AS fifde 						\n";
        $stSql.="	       ON fifde.cod_processo = fif.cod_processo  					\n";
        $stSql.="	    WHERE															\n";

        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaEnderecoProcessoFiscalLevantamentos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaEnderecoProcessoFiscalLevantamentos($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaEnderecoProcessoFiscalLevantamentos($condicao)
    {
        $stSql ="         SELECT fif.cod_processo											\n";
        $stSql.="	       		, fc.cod_fiscal 											\n";
        $stSql.="	       		, fif.cod_tipo_documento									\n";
        $stSql.="	       		, fif.cod_documento											\n";
        $stSql.="	       		, swtl.nom_tipo||' '||swnl.nom_logradouro as logradouro_f 	\n";
        $stSql.="	       		, swtl2.nom_tipo||' '||swnl2.nom_logradouro as logradouro_i \n";
        $stSql.="	       		, iim.numero AS numero_f 									\n";
        $stSql.="	       		, edi.numero AS numero_i									\n";
        $stSql.="	       		, iim.complemento AS complemento_f							\n";
        $stSql.="	       		, edi.complemento AS complemento_i							\n";
        $stSql.="	       		, swuf.nom_uf 												\n";
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
        $stSql.="	       		, swtl2.nom_tipo||' '||swnl2.nom_logradouro||' '||edi.numero||' '||edi.complemento||' '||swb.nom_bairro||' '||swmu.nom_municipio||' '||swuf.nom_uf as endereco \n";
        $stSql.="	       		, ( SELECT eml.nom_modalidade 								\n";
        $stSql.="	             	  FROM economico.modalidade_lancamento AS eml			\n";
        $stSql.="	            	 WHERE eml.cod_modalidade = COALESCE( eceml.cod_modalidade,eam.cod_modalidade) \n";
        $stSql.="	         	  )AS nom_modalidade										\n";
        $stSql.="	       		, COALESCE( eceml.cod_modalidade, eam.cod_modalidade) AS cod_modalidade \n";
        $stSql.="	       		, COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) AS numcgm	\n";
        $stSql.="	       		, ( SELECT cgm.nom_cgm 										\n";
        $stSql.="	             	  FROM sw_cgm AS cgm 									\n";
        $stSql.="	           	 	 WHERE cgm.numcgm = COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) \n";
        $stSql.="	         	  ) AS nom_cgm 												\n";
          $stSql.="           FROM fiscalizacao.inicio_fiscalizacao as fif					\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal AS pf							\n";
        $stSql.="	      	  ON fif.cod_processo = pf.cod_processo							\n";
        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 						\n";
        $stSql.="	      	  ON pf.cod_tipo = tf.cod_tipo									\n";
        $stSql.="     INNER JOIN ( SELECT ffc.cod_fiscal 									\n";
        $stSql.="     		        	, ffc.numcgm 										\n";
        $stSql.="		        		, ffpf.cod_processo									\n";
        $stSql.="		     		 FROM fiscalizacao.fiscal as ffc 						\n";
        $stSql.="              INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 		\n";
        $stSql.="	 	       		   ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc			\n";
        $stSql.="	      	  ON fc.cod_processo = pf.cod_processo							\n";
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
        $stSql.="     INNER JOIN ( SELECT ate.inscricao_economica							\n";
        $stSql.="   		        , max(ocorrencia_atividade) AS ocorrencia_atividade		\n";
        $stSql.="		     FROM economico.atividade_cadastro_economico AS ate				\n";
        $stSql.="	         GROUP BY inscricao_economica 									\n";
        $stSql.="	         )AS ate														\n";
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
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaProcessoFiscalTodosLevantamentos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaProcessoFiscalTodosLevantamentos($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaProcessoFiscalTodosLevantamentos($condicao)
    {
        $stSql =" 	  SELECT trim(round( sum( ssr.valor_lancado ),2 )) as receita_efetivo  		\n";
        $stSql.="	       , trim(round( sum( ssr.valor_lancado * ssr.aliquota / 100), 2 )) as issqn_devido \n";
        $stSql.="	       , trim(ssr.competencia) as competencia						\n";
        $stSql.="	       , to_date( ssr.competencia , 'MM-YYYY') as data_competencia 	\n";
        $stSql.="	       , pfe.inscricao_economica 	 								\n";
        $stSql.="	    FROM fiscalizacao.servico_sem_retencao as ssr 	        		\n";
        $stSql.="     INNER JOIN fiscalizacao.faturamento_servico as fs 	    		\n";
        $stSql.="             ON fs.cod_processo = ssr.cod_processo		        		\n";
        $stSql.="      	     AND fs.competencia = ssr.competencia 		        		\n";
        $stSql.="      	     AND fs.cod_servico = ssr.cod_servico 		       		 	\n";
        $stSql.="      	     AND fs.cod_atividade = ssr.cod_atividade 		        	\n";
        $stSql.="            AND fs.ocorrencia = ssr.ocorrencia		        			\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_levantamento as pl 	        	\n";
        $stSql.="             ON pl.cod_processo = ssr.cod_processo 	        		\n";
        $stSql.="            AND pl.competencia = ssr.competencia 	        			\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_empresa as pfe 	        \n";
        $stSql.="             ON pfe.cod_processo = ssr.cod_processo 	        		\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal AS pf						\n";
        $stSql.="	      	  ON pfe.cod_processo = pf.cod_processo						\n";
        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 					\n";
        $stSql.="	      	  ON pf.cod_tipo = tf.cod_tipo								\n";
        $stSql.="     INNER JOIN ( SELECT ffc.cod_fiscal 								\n";
        $stSql.="     		        	, ffc.numcgm 									\n";
        $stSql.="		        		, ffpf.cod_processo								\n";
        $stSql.="		     		 FROM fiscalizacao.fiscal as ffc 					\n";
        $stSql.="              INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 	\n";
        $stSql.="	 	       		   ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc		\n";
        $stSql.="	      	  ON fc.cod_processo = pf.cod_processo						\n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc			\n";
        $stSql.="             ON pf.cod_processo = pfc.cod_processo 					\n";

        $stGroupBy =" 	GROUP BY pfe.inscricao_economica 								\n";
        $stGroupBy.=" 	       , ssr.competencia 										\n";

        $stOrderBy =" 	ORDER BY data_competencia 	 									\n";

        $stSql.= $condicao . $stGroupBy . $stOrderBy;

        return $stSql;
    }

    public function recuperaProcessoFiscalTotalTodosLevantamentos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaProcessoFiscalTotalTodosLevantamentos($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaProcessoFiscalTotalTodosLevantamentos($condicao)
    {
        $stSql =" 	  	  SELECT trim(round(sum(total_geral.receita_efetivo),2 )) as total_receita_efetivo  		\n";
        $stSql.="	       	   , trim(round(sum( total_geral.issqn_devido ),2 )) as total_issqn_devido 		 \n";
        $stSql.="	   	    FROM (SELECT sum( ssr.valor_lancado )  as receita_efetivo  					\n";
        $stSql.="		       		  , sum( ssr.valor_lancado * ssr.aliquota / 100) as issqn_devido 	\n";
        $stSql.="		       		  , pfe.inscricao_economica   										\n";
        $stSql.="		       		  , ssr.competencia as competencia									\n";
        $stSql.=" 		       	      , pfe.cod_processo  												\n";
        $stSql.="  		    	   FROM fiscalizacao.servico_sem_retencao as ssr 						\n";
        $stSql.=" 	      	 INNER JOIN fiscalizacao.faturamento_servico as fs   						\n";
        $stSql.="    		      	 ON fs.cod_processo = ssr.cod_processo   							\n";
        $stSql.="  		     		AND fs.competencia = ssr.competencia   								\n";
        $stSql.=" 		     		AND fs.cod_servico = ssr.cod_servico  								\n";
        $stSql.=" 		     	    AND fs.cod_atividade = ssr.cod_atividade  							\n";
        $stSql.="  		     		AND fs.ocorrencia = ssr.ocorrencia  								\n";
        $stSql.="    	     INNER JOIN fiscalizacao.processo_levantamento as pl   						\n";
        $stSql.="   		      	 ON pl.cod_processo = ssr.cod_processo   							\n";
        $stSql.="           	    AND pl.competencia = ssr.competencia   								\n";
        $stSql.="    	     INNER JOIN fiscalizacao.processo_fiscal_empresa as pfe   					\n";
        $stSql.="	    	      	 ON pfe.cod_processo = ssr.cod_processo   							\n";
        $stSql.="      		   GROUP BY pfe.inscricao_economica, ssr.competencia ,pfe.cod_processo 		\n";
        $stSql.="      	       	 ) AS total_geral   													\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal as pf   									\n";
        $stSql.="	      	  ON pf.cod_processo = total_geral.cod_processo  							\n";

        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaProcessoFiscalTodosArrecadacao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaProcessoFiscalTodosArrecadacao($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaProcessoFiscalTodosArrecadacao($condicao)
    {
        $stSql =" 	  	  SELECT trim(round(sum( assr.valor_declarado ),2 )) as receita_declarado  \n";
        $stSql.="	       	   , trim(round(apc.valor, 2 )) as issqn_pago \n";
        $stSql.="	       	   , trim(acef.competencia) as competencia									\n";
        $stSql.="	       	   , to_date( acef.competencia , 'MM-YYYY') as data_competencia 			\n";
        $stSql.="	       	   , pfe.inscricao_economica 	 											\n";
        $stSql.="	    	FROM arrecadacao.cadastro_economico_faturamento as acef     				\n";
        $stSql.="     INNER JOIN arrecadacao.faturamento_servico as afs 	    						\n";
        $stSql.="             ON afs.inscricao_economica = acef.inscricao_economica     				\n";
        $stSql.="      	     AND afs.timestamp = acef.timestamp 		        						\n";
        $stSql.="     INNER JOIN arrecadacao.servico_sem_retencao as assr 	        					\n";
        $stSql.="             ON assr.inscricao_economica = afs.inscricao_economica     				\n";
        $stSql.="      	     AND assr.timestamp = afs.timestamp 		        						\n";
        $stSql.="      	     AND assr.cod_atividade = afs.cod_atividade 	        					\n";
        $stSql.="            AND assr.cod_servico = afs.cod_servico 	        						\n";
        $stSql.="            AND assr.ocorrencia = afs.ocorrencia 	        							\n";
        $stSql.="     INNER JOIN arrecadacao.cadastro_economico_calculo as acec         				\n";
        $stSql.="             ON acec.inscricao_economica = acef.inscricao_economica   					\n";
        $stSql.="            AND acec.timestamp = acef.timestamp	        							\n";
        $stSql.="     INNER JOIN arrecadacao.calculo as ac 			        							\n";
        $stSql.="             ON ac.cod_calculo = acec.cod_calculo 	        							\n";
        $stSql.="      	     AND ac.timestamp = acec.timestamp 			        						\n";
        $stSql.="      	     AND ac.cod_genero = 1 			        									\n";
        $stSql.="      	     AND ac.cod_natureza = 1			        								\n";
        $stSql.="      LEFT JOIN arrecadacao.pagamento_calculo as apc 									\n";
        $stSql.="	      	  ON apc.cod_calculo = acec.cod_calculo 									\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_empresa as pfe 							\n";
        $stSql.="	      	  ON pfe.inscricao_economica = acef.inscricao_economica 					\n";

        $stSql.="     INNER JOIN
                            fiscalizacao.processo_fiscal as pf
                      ON
                            pf.cod_processo = pfe.cod_processo
                            AND afs.dt_emissao BETWEEN pf.periodo_inicio AND pf.periodo_termino
        \n";

        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 									\n";
        $stSql.="	      	  ON pf.cod_tipo = tf.cod_tipo												\n";
        $stSql.="     INNER JOIN ( SELECT ffc.cod_fiscal 												\n";
        $stSql.="     		        	, ffc.numcgm 													\n";
        $stSql.="		        		, ffpf.cod_processo												\n";
        $stSql.="		     	     FROM fiscalizacao.fiscal as ffc 									\n";
        $stSql.="              INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 					\n";
        $stSql.="	 	       		   ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc						\n";
        $stSql.="	      	  ON fc.cod_processo = pf.cod_processo										\n";
        $stSql.="LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc							\n";
        $stSql.="             ON pf.cod_processo = pfc.cod_processo 									\n";

        $stGroupBy =" 	GROUP BY pfe.inscricao_economica 												\n";
        $stGroupBy.=" 	       , apc.valor																\n";
        $stGroupBy.=" 	       , acef.competencia 														\n";

        $stOrderBy =" 	ORDER BY data_competencia 	 													\n";

        $stSql.= $condicao . $stGroupBy .$stOrderBy;

        return $stSql;
    }

    public function recuperaProcessoFiscalTotalTodosArrecadacao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaProcessoFiscalTotalTodosArrecadacao($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaProcessoFiscalTotalTodosArrecadacao($condicao)
    {
        $arCondicao = explode('---', $condicao);

        $stSql =" 	  	   SELECT trim(round(sum(total_geral.receita_declarado),2))as total_receita_declarado  		\n";
        $stSql.="	            , trim(round(sum(total_geral.issqn_pago),2)) as total_issqn_pago  		 \n";
        $stSql.="	       		, total_geral.inscricao_economica 		 						\n";
        $stSql.="	    	 FROM (SELECT sum( assr.valor_declarado ) as receita_declarado      \n";
        $stSql.="		       			, apc.valor as issqn_pago 				 				\n";
        $stSql.="		       			, acef.competencia as competencia						\n";
        $stSql.="		       			, acef.inscricao_economica   							\n";
        $stSql.="  		    		 FROM arrecadacao.cadastro_economico_faturamento as acef 	\n";
        $stSql.=" 	      	   INNER JOIN arrecadacao.faturamento_servico as afs 		  		\n";
        $stSql.="    		      	   ON afs.inscricao_economica = acef.inscricao_economica   	\n";
        $stSql.="  		     		  AND afs.timestamp = acef.timestamp 		 				\n";
        $stSql.="    	       INNER JOIN arrecadacao.servico_sem_retencao as assr 		   		\n";
        $stSql.="   		      	   ON assr.inscricao_economica = afs.inscricao_economica   	\n";
        $stSql.="           	      AND assr.timestamp = afs.timestamp 		  				\n";
        $stSql.="           	      AND assr.cod_atividade = afs.cod_atividade 		  		\n";
        $stSql.="           	      AND assr.cod_servico = afs.cod_servico 			  		\n";
        $stSql.="           	      AND assr.ocorrencia = afs.ocorrencia  			  		\n";
        $stSql.="    	       INNER JOIN arrecadacao.cadastro_economico_calculo as acec 	   	\n";
        $stSql.="	    	      	   ON acec.inscricao_economica = acef.inscricao_economica   \n";
        $stSql.="           	      AND acec.timestamp = acef.timestamp  				  		\n";
        $stSql.="    	       INNER JOIN arrecadacao.calculo as ac 	   						\n";
        $stSql.="	    	      	   ON ac.cod_calculo = acec.cod_calculo   					\n";
        $stSql.="           	      AND ac.timestamp = acec.timestamp  				  		\n";
        $stSql.="           	      AND ac.cod_genero = 1  			  						\n";
        $stSql.="           	      AND ac.cod_natureza = 1  			  						\n";
        $stSql.="    	        LEFT JOIN arrecadacao.pagamento_calculo as apc 					\n";
        $stSql.="	    	      	   ON apc.cod_calculo = acec.cod_calculo   					\n";

        if ($arCondicao[1]) {
            $stSql.="               WHERE ".$arCondicao[1]."                    			  	\n";
        }

        $stSql.="      			 GROUP BY acef.inscricao_economica 								\n";
        $stSql.="			   			, acef.competencia 										\n";
        $stSql.="					   	, apc.valor 											\n";
        $stSql.="      	       ) AS total_geral   												\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal_empresa as pfe    				\n";
        $stSql.="	      	  ON pfe.inscricao_economica = total_geral.inscricao_economica  	\n";
        $stSql.="     INNER JOIN fiscalizacao.processo_fiscal as pf   							\n";
        $stSql.="	      	  ON pf.cod_processo = pfe.cod_processo  							\n";
        $stSql.="     INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf    						\n";
        $stSql.="	      	  ON pf.cod_tipo = tf.cod_tipo 	  									\n";

        $stGroupBy =" 	GROUP BY total_geral.inscricao_economica 	 							\n";

        if (is_array($arCondicao[0])) {
            $stSql.= $arCondicao[0] . $stGroupBy;
        } else {
            $stSql.= $condicao . $stGroupBy;
        }

        return $stSql;
    }

    public function recuperaJuroMultaGrupoISS(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaJuroMultaGrupoISS().$stCondicao.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperaJuroMultaGrupoISS()
    {
        $stSql = "
            SELECT
                af.cod_modulo
                , af.nom_funcao
                , af.cod_biblioteca
                , af.cod_funcao
                , af.cod_tipo_retorno
                , mca.cod_tipo
                , mca.cod_acrescimo

            FROM
                administracao.funcao as af

            INNER JOIN (

                SELECT
                    tmp.*
                FROM
                    monetario.formula_acrescimo AS tmp

                INNER JOIN
                    (
                        SELECT
                            max(timestamp) as timestamp,
                            cod_acrescimo,
                            cod_modulo,
                            cod_biblioteca,
                            cod_tipo

                        FROM
                            monetario.formula_acrescimo

                        GROUP BY
                            cod_acrescimo,
                            cod_modulo,
                            cod_biblioteca,
                            cod_tipo
                    )AS tmp2
                ON
                    tmp.cod_acrescimo = tmp2.cod_acrescimo
                    AND tmp.cod_tipo = tmp2.cod_tipo
                    AND tmp.cod_modulo = tmp2.cod_modulo
                    AND tmp.cod_biblioteca = tmp2.cod_biblioteca
                    AND tmp.timestamp = tmp2.timestamp
            ) as mfa
            ON
                af.cod_modulo = mfa.cod_modulo
                AND af.cod_biblioteca = mfa.cod_biblioteca
                AND af.cod_funcao = mfa.cod_funcao

            INNER JOIN
                monetario.credito_acrescimo as mca
            ON
                mca.cod_acrescimo = mfa.cod_acrescimo
                AND mca.cod_tipo = mfa.cod_tipo

            INNER JOIN
                monetario.credito as mc
            ON
                mc.cod_credito = mca.cod_credito
                AND mc.cod_natureza = mca.cod_natureza
                AND mc.cod_genero = mca.cod_genero
                AND mc.cod_especie = mca.cod_especie

            INNER JOIN
                arrecadacao.credito_grupo as acg
            ON
                acg.cod_credito = mc.cod_credito
                AND acg.cod_natureza = mc.cod_natureza
                AND acg.cod_genero = mc.cod_genero
                AND acg.cod_especie = mc.cod_especie
        \n";

        return $stSql;
    }

    public function recuperaFuncao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaFuncao($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaFuncao($condicao)
    {
        $stSql =" 	  	  SELECT af.cod_modulo 							\n";
        $stSql.="	       	   , af.nom_funcao  						\n";
        $stSql.="	       	   , af.cod_biblioteca 						\n";
        $stSql.="	       	   , af.cod_funcao 							\n";
        $stSql.="	       	   , af.cod_tipo_retorno					\n";
        $stSql.="	       	   , max (mfa.timestamp)					\n";
        $stSql.="	       	   , mca.cod_tipo							\n";
        $stSql.="	       	   , mca.cod_acrescimo						\n";
        $stSql.="	    	FROM administracao.funcao as af 			\n";
        $stSql.="     INNER JOIN monetario.formula_acrescimo as mfa    	\n";
        $stSql.="             ON af.cod_modulo = mfa.cod_modulo 		\n";
        $stSql.="      	     AND af.cod_biblioteca = mfa.cod_biblioteca \n";
        $stSql.="      	     AND af.cod_funcao = mfa.cod_funcao 	    \n";
        $stSql.="     INNER JOIN monetario.credito_acrescimo as mca 	\n";
        $stSql.="             ON mca.cod_acrescimo = mfa.cod_acrescimo 	\n";
        $stSql.="      	     AND mca.cod_tipo = mfa.cod_tipo 			\n";
        $stSql.="     INNER JOIN monetario.credito as mc 			    \n";
        $stSql.="             ON mc.cod_credito = mca.cod_credito 		\n";
        $stSql.="            AND mc.cod_natureza = mca.cod_natureza 	\n";
        $stSql.="            AND mc.cod_genero = mca.cod_genero 	    \n";
        $stSql.="            AND mc.cod_especie = mca.cod_especie 	    \n";
        $stSql.="     INNER JOIN arrecadacao.calculo as ac 			    \n";
        $stSql.="             ON ac.cod_credito = mc.cod_credito 	    \n";
        $stSql.="      	     AND ac.cod_natureza = mc.cod_natureza 		\n";
        $stSql.="      	     AND ac.cod_genero = mc.cod_genero 		    \n";
        $stSql.="      	     AND ac.cod_especie = mc.cod_especie 		\n";
        $stSql.="      	     AND ac.cod_genero = 1 			        	\n";
        $stSql.="      	     AND ac.cod_natureza = 1			        \n";

        $stGroupBy =" 	GROUP BY af.cod_modulo 				 			\n";
        $stGroupBy.="	       , af.nom_funcao  						\n";
        $stGroupBy.="	       , af.cod_biblioteca 						\n";
        $stGroupBy.="	       , af.cod_funcao 							\n";
        $stGroupBy.="	       , af.cod_tipo_retorno					\n";
        $stGroupBy.="	       , mca.cod_tipo							\n";
        $stGroupBy.="	       , mca.cod_acrescimo						\n";

        $stOrderBy =" 	ORDER BY mca.cod_acrescimo asc 	 				\n";

        $stSql.= $condicao . $stGroupBy .$stOrderBy;

        return $stSql;
    }

    public function recuperaSelectFuncao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaSelectFuncao($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaSelectFuncao($stCondicao)
    {
        # $condicao (date, date, double precision or integer , integer, integer ) #
        $stNew = explode( "---", $stCondicao );
        $condicao = $stNew[0];
        $stFuncao = $stNew[1];
        $stSql =" 	  SELECT ".$stFuncao."({$condicao}) as funcao \n";

        return $stSql;
    }

    public function recuperaConfiguracao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaConfiguracao($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaConfiguracao($condicao)
    {
        $stSql =" 	  SELECT ac.exercicio 						\n";
        $stSql.="	       , ac.cod_modulo  					\n";
        $stSql.="	       , ac.parametro 	 					\n";
        $stSql.="	       , ac.valor							\n";
        $stSql.="	    FROM administracao.configuracao as ac 	\n";

        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaVencimentosParcela(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaVencimentosParcela($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaVencimentosParcela($condicao)
    {
        $stSql =" 	  SELECT avp.cod_grupo 								\n";
        $stSql.="	       , avp.cod_vencimento  						\n";
        $stSql.="	       , avp.cod_parcela  							\n";
        $stSql.="	       , avp.data_vencimento  						\n";
        $stSql.="	       , avp.valor									\n";
        $stSql.="	       , avp.percentual								\n";
        $stSql.="	       , avp.data_vencimento_desconto				\n";
        $stSql.="	       , avp.ano_exercicio							\n";
        $stSql.="	    FROM arrecadacao.vencimento_parcela as avp		\n";

        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaValorIndicador(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaValorIndicador($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaValorIndicador($condicao)
    {
        $stSql =" 	  	  SELECT mvi.cod_indicador 					\n";
        $stSql.="	       	   , mvi.inicio_vigencia 	 			\n";
        $stSql.="	       	   , mvi.valor							\n";
        $stSql.="	    	FROM monetario.valor_indicador as mvi 	\n";

        $stGroupBy =" 	GROUP BY mvi.cod_indicador 					\n";
        $stGroupBy.=" 	       , mvi.inicio_vigencia 				\n";
        $stGroupBy.=" 	       , mvi.valor 							\n";

        $stOrderBy =" 	ORDER BY mvi.inicio_vigencia desc limit 1 	\n";

        $stSql.= $condicao . $stGroupBy .$stOrderBy;

        return $stSql;
    }

    public function recuperaIndice(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaIndice($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaIndice($condicao)
    {
        $stSql =" 	  SELECT mvi.cod_indicador 					\n";
        $stSql.="	       , mvi.inicio_vigencia 	 			\n";
        $stSql.="	       , mvi.valor							\n";
        $stSql.="	    FROM monetario.valor_indicador as mvi 	\n";

        $stSql.= $condicao . $stGroupBy .$stOrderBy;

        return $stSql;
    }

    public function recuperaCodProcessoLevantamentos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaCodProcessoLevantamentos($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaRecuperaCodProcessoLevantamentos($condicao)
    {
        $stSql =" 	     SELECT fpl.cod_processo 														\n";
        $stSql.="	       	  , fpfe.inscricao_economica  												\n";
        $stSql.="	       	  , fpl.cod_processo ||'-'|| fpfe.inscricao_economica as cod_inscricao    \n";
        $stSql.="    		  , fpl.cod_processo ||' - '|| 												\n";
        $stSql.="				( SELECT cgm.nom_cgm 			 										\n";
        $stSql.="		        	FROM sw_cgm AS cgm 													\n";
        $stSql.="		     	   WHERE cgm.numcgm = COALESCE( ecd.numcgm, ecf.numcgm, eca.numcgm ) 	\n";
        $stSql.="    		    ) AS nom_cgm 			 												\n";
        $stSql.="	       FROM fiscalizacao.processo_levantamento as fpl 								\n";
        $stSql.="    INNER JOIN fiscalizacao.servico_sem_retencao as fssr 								\n";
        $stSql.="	         ON fssr.cod_processo = fpl.cod_processo 									\n";
        $stSql.="    INNER JOIN fiscalizacao.faturamento_servico as fss 								\n";
        $stSql.="	         ON fss.cod_processo = fpl.cod_processo 									\n";
        $stSql.="    INNER JOIN fiscalizacao.inicio_fiscalizacao as fif 								\n";
        $stSql.="	         ON fif.cod_processo = fpl.cod_processo 									\n";
        $stSql.="     LEFT JOIN fiscalizacao.processo_fiscal_cancelado as fpfc 							\n";
        $stSql.="	         ON fpfc.cod_processo = fpl.cod_processo 									\n";
        $stSql.="    INNER JOIN fiscalizacao.processo_fiscal_empresa as fpfe 							\n";
        $stSql.="	         ON fpfe.cod_processo = fpl.cod_processo 									\n";
        $stSql.="    INNER JOIN economico.cadastro_economico AS ece 		 							\n";
        $stSql.="	         ON ece.inscricao_economica = fpfe.inscricao_economica 						\n";
        $stSql.="     LEFT JOIN economico.cadastro_economico_empresa_direito AS ecd						\n";
        $stSql.="	         ON ecd.inscricao_economica = ece.inscricao_economica 						\n";
        $stSql.="     LEFT JOIN economico.cadastro_economico_empresa_fato AS ecf 						\n";
        $stSql.="	         ON ecf.inscricao_economica = ece.inscricao_economica 						\n";
        $stSql.="     LEFT JOIN economico.cadastro_economico_autonomo AS eca 							\n";
        $stSql.="	         ON eca.inscricao_economica = ece.inscricao_economica 						\n";
        $stSql.="    INNER JOIN ( SELECT ate.inscricao_economica  										\n";
        $stSql.="     		        	, max(ocorrencia_atividade) AS ocorrencia_atividade 			\n";
        $stSql.="		        	 FROM economico.atividade_cadastro_economico AS ate					\n";
        $stSql.="		     	 GROUP BY inscricao_economica ) AS ate 									\n";
        $stSql.="	 	     ON ate.inscricao_economica = ece.inscricao_economica 						\n";
        $stSql.="    INNER JOIN economico.atividade_cadastro_economico AS eac  							\n";
        $stSql.="	         ON eac.inscricao_economica = ate.inscricao_economica 						\n";
        $stSql.="	        AND eac.ocorrencia_atividade = ate.ocorrencia_atividade 					\n";
        $stSql.="     LEFT JOIN economico.cadastro_economico_modalidade_lancamento AS eceml 			\n";
        $stSql.="	         ON eceml.inscricao_economica = ece.inscricao_economica 					\n";
        $stSql.="	        AND eceml.ocorrencia_atividade = eac.ocorrencia_atividade 					\n";
        $stSql.="	        AND eceml.cod_atividade = eac.cod_atividade 			 					\n";
        $stSql.="    INNER JOIN economico.atividade AS ea 												\n";
        $stSql.="	         ON ea.cod_atividade = eac.cod_atividade 									\n";
        $stSql.="  LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao AS ftf	    	                    \n";
        $stSql.="        	    ON fpl.cod_processo = ftf.cod_processo 					                \n";

        $stGroupBy ="  GROUP BY fpl.cod_processo 														\n";
        $stGroupBy.="  		  , fpfe.inscricao_economica 												\n";
        $stGroupBy.="  		  , nom_cgm 																\n";

        $stOrderBy ="  ORDER BY fpl.cod_processo 			 											\n";

        $stSql.= $condicao . $stGroupBy .$stOrderBy;

        return $stSql;
    }

    public function recuperaPendenciasProcessoFiscal(&$rsRecordSet, $stCondicao, $stOrdem = null, $boTransacao = null)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaPendenciasProcessoFiscal($stCondicao).$stOrdem;

        return $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    }

    public function montaPendenciasProcessoFiscal($condicao)
    {
        $stSql = "  SELECT                                                                             \n";
        $stSql.= "      SUM(receita_efetiva - receita_declarada) as declarado_menor,                    \n";
        $stSql.= "      SUM(total_pagar) as devido                                                      \n";
        $stSql.= "  FROM                                                                                \n";
        $stSql.= "      fiscalizacao.levantamento                                                       \n";
        $stSql.= $condicao . $stGroupBy .$stOrderBy;

        return $stSql;
    }

    public function recuperaIndicadorEconomico(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaIndicadorEconomico($stCondicao).$stOrdem;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
    }

    private function montaIndicadorEconomico($condicao)
    {
        $stSql =" 	  SELECT mie.cod_indicador 					            \n";
        $stSql.="	       , mie.abreviatura     	 			            \n";
        $stSql.="	       , mie.descricao						            \n";
        $stSql.="	       , mfi.inicio_vigencia				            \n";
        $stSql.="	       , mfi.cod_funcao						            \n";
        $stSql.="	       , mfi.cod_modulo						            \n";
        $stSql.="	       , mfi.cod_biblioteca					            \n";
        $stSql.="	       , af.cod_tipo_retorno				            \n";
        $stSql.="	       , af.nom_funcao  					            \n";
        $stSql.="	    FROM monetario.indicador_economico as mie 	        \n";
        $stSql.=" INNER JOIN monetario.formula_indicador as mfi             \n";
        $stSql.="	      ON mie.cod_indicador = mfi.cod_indicador 		    \n";
        $stSql.=" INNER JOIN administracao.funcao as af  		            \n";
        $stSql.="	      ON af.cod_funcao = mfi.cod_funcao      		    \n";
        $stSql.="	     AND af.cod_modulo = mfi.cod_modulo      		    \n";
        $stSql.="	     AND af.cod_biblioteca = mfi.cod_biblioteca		    \n";

        $stOrderBy =" ORDER BY mfi.inicio_vigencia DESC limit 1			    \n";

        $stSql.= $condicao . $stOrderBy;

        return $stSql;
    }
}// fecha classe de mapeamento
?>
