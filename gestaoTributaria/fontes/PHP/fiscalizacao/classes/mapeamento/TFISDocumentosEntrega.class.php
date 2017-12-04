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
    * Classe de mapeamento para FISCALIZACAO.INICIO_FISCALIZACAO
    * Data de Criacao: 13/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Zainer Cruz dos Santos Silva

    * @package URBEM
    * @subpackage Mapeamento

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CLA_PERSISTENTE );

class TFISDocumentosEntrega extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela( 'fiscalizacao.documentos_entrega' );

        $this->setCampoCod( 'cod_documento' );
        $this->setComplementoChave( 'cod_processo' );
        $this->setComplementoChave( 'situacao' );

        $this->AddCampo( 'situacao','varchar',true,'1',true,false );
        $this->AddCampo( 'cod_processo','integer',true,'',true,true );
        $this->AddCampo( 'cod_documento','integer',true,'',true,true );
        $this->AddCampo( 'cod_fiscal','integer',true,'',false,true );
        $this->AddCampo( 'observacao','text',true,'',false,false );
    }

    public function recuperaListaInicioFiscalizacaoEconomica(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarListaInicioFiscalizacaoEconomica($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarListaInicioFiscalizacaoEconomica($condicao)
    {
        $stSql =" 	       SELECT DISTINCT pf.cod_processo as cod_processo		                \n";
        $stSql.="	            , tf.cod_tipo 						                            \n";
        $stSql.="	            , tf.descricao 						                            \n";
        $stSql.="	            , pfe.inscricao_economica AS inscricao 			                \n";
        $stSql.="	            , pf.numcgm 						                            \n";
        $stSql.="	            , fifde.cod_processo as	cod_processo_entrega 		            \n";
        $stSql.="	            , fifde.situacao  					                            \n";
        $stSql.="	            , fifded.situacao as situacao2 				                    \n";
        $stSql.="	         FROM
                                fiscalizacao.processo_fiscal AS pf 	 		                    \n";

        $stSql.="            INNER JOIN (
                                SELECT
                                    ffc.cod_fiscal
                                    , ffc.numcgm
                                    , ffpf.cod_processo
                                FROM
                                    fiscalizacao.fiscal as ffc

                                INNER JOIN
                                    fiscalizacao.fiscal_processo_fiscal AS ffpf
                                ON
                                    ffc.cod_fiscal = ffpf.cod_fiscal
                             ) AS fc
                             ON
                                fc.cod_processo = pf.cod_processo                               \n";

        $stSql.="      INNER JOIN fiscalizacao.inicio_fiscalizacao AS fif 	                	\n";
        $stSql.="              ON fif.cod_processo = pf.cod_processo 			                \n";
        $stSql.="      INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 		            \n";
        $stSql.="              ON pf.cod_processo = pfe.cod_processo 			                \n";
        $stSql.="      INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 			                \n";
        $stSql.="    	       ON pf.cod_tipo = tf.cod_tipo 				                    \n";
        $stSql.="      INNER JOIN administracao.modelo_documento AS amd         	            \n";
        $stSql.="              ON fif.cod_documento = amd.cod_documento         	            \n";
        $stSql.="             AND fif.cod_tipo_documento = amd.cod_tipo_documento	            \n";
        $stSql.=" LEFT OUTER JOIN ( SELECT fifd.cod_documento 				                    \n";
        $stSql.="		                 , fde.cod_documento as cod_documento_entrega  	        \n";
        $stSql.="			             , fifd.cod_processo 				                    \n";
        $stSql.="			             , fde.situacao				 	                        \n";
        $stSql.="	                  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd  \n";
        $stSql.="	   	        INNER JOIN fiscalizacao.documento AS fd 		                \n";
        $stSql.="			            ON fd.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="	            INNER JOIN fiscalizacao.documentos_entrega AS fde 	            \n";
        $stSql.="			            ON fde.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="			           AND fde.cod_processo = fifd.cod_processo 	            \n";
        $stSql.="			           AND fde.situacao = 'R' 	                                \n";
        $stSql.="		          GROUP BY fifd.cod_documento 				                    \n";
        $stSql.="                        , fde.cod_documento 				                    \n";
        $stSql.="                        , fifd.cod_processo 				                    \n";
        $stSql.="                        , fde.situacao 				                        \n";
        $stSql.="		          ORDER BY fifd.cod_documento) AS fifde 		                \n";
        $stSql.="	           ON fifde.cod_processo = fif.cod_processo  		                \n";
        $stSql.=" LEFT OUTER JOIN ( SELECT fifd.cod_documento 				                    \n";
        $stSql.="		                 , fde.cod_documento as cod_documento_entrega  	        \n";
        $stSql.="			             , fifd.cod_processo 				                    \n";
        $stSql.="			             , fde.situacao				 	                        \n";
        $stSql.="	                  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd  \n";
        $stSql.="	   	        INNER JOIN fiscalizacao.documento AS fd 		                \n";
        $stSql.="			            ON fd.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="	            INNER JOIN fiscalizacao.documentos_entrega AS fde 	            \n";
        $stSql.="			            ON fde.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="			           AND fde.cod_processo = fifd.cod_processo 	            \n";
        $stSql.="			           AND fde.situacao = 'D' 	                                \n";
        $stSql.="		          GROUP BY fifd.cod_documento 				                    \n";
        $stSql.="                        , fde.cod_documento 				                    \n";
        $stSql.="                        , fifd.cod_processo 				                    \n";
        $stSql.="                        , fde.situacao 				                        \n";
        $stSql.="		          ORDER BY fifd.cod_documento) AS fifded 		                \n";
        $stSql.="	           ON fifded.cod_processo = fif.cod_processo  		                \n";
        $stSql.="             AND fifded.cod_documento_entrega = fifde.cod_documento_entrega    \n";
        $stSql.="             AND fifded.cod_documento = fifde.cod_documento     	            \n";
        $stSql.="  LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao AS ftf	    	            \n";
        $stSql.="        	    ON pf.cod_processo = ftf.cod_processo 					        \n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperarInicioFiscalizacaoEconomica(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarInicioFiscalizacaoEconomica($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    //Monta a Início do Processo Fiscal com o Crédito/Grupo
    public function recuperaInicioProcessoFiscalListaCreditoGrupo(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInicioProcessoFiscalCreditoGrupo($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarInicioFiscalizacaoEconomica($condicao)
    {
        $stSql ="       SELECT fif.cod_processo         					                    \n";
        $stSql.="            , fif.cod_fiscal           					                    \n";
        $stSql.="            , fif.cod_tipo_documento   					                    \n";
        $stSql.="            , fif.cod_documento        	       				                \n";
        $stSql.="            , TO_CHAR(fif.dt_inicio, 'dd/mm/yyyy') as dt_inicio 		        \n";
        $stSql.="            , TO_CHAR(fif.prazo_entrega, 'dd/mm/yyyy') as prazo_entrega 	    \n";
        $stSql.="            , fif.local_entrega        		       			                \n";
        $stSql.="            , fif.observacao                                  			        \n";
        $stSql.="            , pfe.inscricao_economica                        			        \n";
        $stSql.="            , pf.cod_tipo 	                        			                \n";
        $stSql.="            , tf.descricao 	                        			            \n";
        $stSql.="            , TO_CHAR(pe.dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogada  	    \n";
        $stSql.="            , TO_CHAR(pf.periodo_inicio, 'dd/mm/yyyy') as periodo_inicio 	    \n";
        $stSql.="            , TO_CHAR(pf.periodo_termino, 'dd/mm/yyyy') as periodo_termino 	\n";
        $stSql.="            , TO_CHAR(pf.previsao_termino, 'dd/mm/yyyy') as previsao_termino 	\n";
        $stSql.="         FROM fiscalizacao.inicio_fiscalizacao AS fif         		    	    \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal AS pf              			        \n";
        $stSql.="     	    ON fif.cod_processo = pf.cod_processo              		    	    \n";
        $stSql.="    LEFT JOIN fiscalizacao.prorrogacao_entrega AS pe           			    \n";
        $stSql.="     	    ON fif.cod_processo = pe.cod_processo              			        \n";
        $stSql.="   INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 				            \n";
        $stSql.="   	    ON pf.cod_tipo = tf.cod_tipo 					                    \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 			            \n";
        $stSql.="           ON pf.cod_processo = pfe.cod_processo 				                \n";
        $stSql.="   INNER JOIN fiscalizacao.fiscal_processo_fiscal AS fpf      			        \n";
        $stSql.="     	    ON fif.cod_fiscal = fpf.cod_fiscal                			        \n";
        $stSql.="          AND fif.cod_processo = fpf.cod_processo             			        \n";
        $stSql.="   INNER JOIN administracao.modelo_documento AS amd           			        \n";
        $stSql.="           ON fif.cod_documento = amd.cod_documento           			        \n";
        $stSql.="          AND fif.cod_tipo_documento = amd.cod_tipo_documento 			        \n";

        $stOrderBy =" ORDER BY pe.dt_prorrogacao desc limit 1           			            \n";

        $stSql.= $condicao . $stOrderBy;

        return $stSql;
    }

    public function recuperarInicioFiscalizacaoObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarInicioFiscalizacaoObra($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarInicioFiscalizacaoObra($condicao)
    {
        $stSql ="       SELECT fif.cod_processo		        				                    \n";
        $stSql.="            , fif.cod_fiscal           					                    \n";
        $stSql.="            , fif.cod_tipo_documento   					                    \n";
        $stSql.="            , fif.cod_documento        		       			                \n";
        $stSql.="            , TO_CHAR(fif.dt_inicio, 'dd/mm/yyyy') as dt_inicio 		        \n";
        $stSql.="            , TO_CHAR(fif.prazo_entrega, 'dd/mm/yyyy') as prazo_entrega 	    \n";
        $stSql.="            , fif.local_entrega        		       			                \n";
        $stSql.="            , fif.observacao                                  			        \n";
        $stSql.="            , pfo.inscricao_municipal                        			        \n";
        $stSql.="            , pf.cod_tipo 	                        			                \n";
        $stSql.="            , tf.descricao 	                        			            \n";
        $stSql.="            , TO_CHAR(pe.dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogada  	    \n";
        $stSql.="            , TO_CHAR(pf.periodo_inicio, 'dd/mm/yyyy') as periodo_inicio 	    \n";
        $stSql.="            , TO_CHAR(pf.periodo_termino, 'dd/mm/yyyy') as periodo_termino 	\n";
        $stSql.="            , TO_CHAR(pf.previsao_termino, 'dd/mm/yyyy') as previsao_termino 	\n";
        $stSql.="         FROM fiscalizacao.inicio_fiscalizacao AS fif         			        \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal AS pf              			        \n";
        $stSql.="     	    ON fif.cod_processo = pf.cod_processo              			        \n";
        $stSql.="    LEFT JOIN fiscalizacao.prorrogacao_entrega AS pe           			    \n";
        $stSql.="     	    ON fif.cod_processo = pe.cod_processo              			        \n";
        $stSql.="   INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 				            \n";
        $stSql.="   	    ON pf.cod_tipo = tf.cod_tipo 				    	                \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo 			            \n";
        $stSql.="           ON pf.cod_processo = pfo.cod_processo 				                \n";
        $stSql.="   INNER JOIN fiscalizacao.fiscal_processo_fiscal AS fpf      			        \n";
        $stSql.="     	    ON fif.cod_fiscal = fpf.cod_fiscal                			        \n";
        $stSql.="          AND fif.cod_processo = fpf.cod_processo             			        \n";
        $stSql.="   INNER JOIN administracao.modelo_documento AS amd           			        \n";
        $stSql.="           ON fif.cod_documento = amd.cod_documento           			        \n";
        $stSql.="          AND fif.cod_tipo_documento = amd.cod_tipo_documento 			        \n";

        $stOrderBy =" ORDER BY pe.dt_prorrogacao desc limit 1           			            \n";

        $stSql.= $condicao . $stOrderBy;

        return $stSql;
    }

    public function recuperaListaInicioFiscalizacaoObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarListaInicioFiscalizacaoObra($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    private function montaRecuperarListaInicioFiscalizacaoObra($condicao)
    {
        $stSql =" 	       SELECT DISTINCT pf.cod_processo as cod_processo		                \n";
        $stSql.="	            , tf.cod_tipo 						                            \n";
        $stSql.="	            , tf.descricao 						                            \n";
        $stSql.="	            , pfo.inscricao_municipal AS inscricao 			                \n";
        $stSql.="	            , pf.numcgm 						                            \n";
        $stSql.="	            , fifde.cod_processo as	cod_processo_entrega 		            \n";
        $stSql.="	            , fifde.situacao  					                            \n";
        $stSql.="	            , fifded.situacao as situacao2 				                    \n";
        $stSql.="	         FROM fiscalizacao.processo_fiscal AS pf 	 		                \n";

        $stSql.="      INNER JOIN fiscalizacao.inicio_fiscalizacao AS fif 		                \n";
        $stSql.="              ON fif.cod_processo = pf.cod_processo 		    	            \n";
        $stSql.="      INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo 		            \n";
        $stSql.="              ON pf.cod_processo = pfo.cod_processo 			                \n";
        $stSql.="      INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 			                \n";
        $stSql.="    	       ON pf.cod_tipo = tf.cod_tipo 				                    \n";
        $stSql.="      INNER JOIN fiscalizacao.fiscal_processo_fiscal AS fpf 		            \n";
        $stSql.="	           ON pf.cod_processo = fpf.cod_processo 			                \n";
        $stSql.="      INNER JOIN fiscalizacao.fiscal fc				                        \n";
        $stSql.="	           ON fc.cod_fiscal = fpf.cod_fiscal 			                    \n";
        $stSql.="      INNER JOIN administracao.modelo_documento AS amd           	            \n";
        $stSql.="              ON fif.cod_documento = amd.cod_documento           	            \n";
        $stSql.="             AND fif.cod_tipo_documento = amd.cod_tipo_documento 	            \n";
        $stSql.=" LEFT OUTER JOIN ( SELECT fifd.cod_documento 				                    \n";
        $stSql.="		                 , fde.cod_documento as cod_documento_entrega  	        \n";
        $stSql.="			             , fifd.cod_processo 				                    \n";
        $stSql.="			             , fde.situacao				 	                        \n";
        $stSql.="	                  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd  \n";
        $stSql.="	   	        INNER JOIN fiscalizacao.documento AS fd 		                \n";
        $stSql.="			            ON fd.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="	            INNER JOIN fiscalizacao.documentos_entrega AS fde 	            \n";
        $stSql.="			            ON fde.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="			           AND fde.cod_processo = fifd.cod_processo 	            \n";
        $stSql.="			           AND fde.situacao = 'R' 	                                \n";
        $stSql.="		          GROUP BY fifd.cod_documento 				                    \n";
        $stSql.="                        , fde.cod_documento 				                    \n";
        $stSql.="                        , fifd.cod_processo 				                    \n";
        $stSql.="                        , fde.situacao 				                        \n";
        $stSql.="		          ORDER BY fifd.cod_documento) AS fifde 		                \n";
        $stSql.="	           ON fifde.cod_processo = fif.cod_processo  		                \n";
        $stSql.=" LEFT OUTER JOIN ( SELECT fifd.cod_documento 				                    \n";
        $stSql.="		                 , fde.cod_documento as cod_documento_entrega  	        \n";
        $stSql.="			             , fifd.cod_processo 				                    \n";
        $stSql.="			             , fde.situacao				 	                        \n";
        $stSql.="	                  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd  \n";
        $stSql.="	   	        INNER JOIN fiscalizacao.documento AS fd 		                \n";
        $stSql.="			            ON fd.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="	            INNER JOIN fiscalizacao.documentos_entrega AS fde 	            \n";
        $stSql.="			            ON fde.cod_documento = fifd.cod_documento 	            \n";
        $stSql.="			           AND fde.cod_processo = fifd.cod_processo 	            \n";
        $stSql.="			           AND fde.situacao = 'D' 	                                \n";
        $stSql.="		          GROUP BY fifd.cod_documento 				                    \n";
        $stSql.="                        , fde.cod_documento 				                    \n";
        $stSql.="                        , fifd.cod_processo 				                    \n";
        $stSql.="                        , fde.situacao 				                        \n";
        $stSql.="		          ORDER BY fifd.cod_documento) AS fifded 		                \n";
        $stSql.="	           ON fifded.cod_processo = fif.cod_processo  		                \n";
        $stSql.="             AND fifded.cod_documento_entrega = fifde.cod_documento_entrega    \n";
        $stSql.="             AND fifded.cod_documento = fifde.cod_documento     	            \n";
        $stSql.=" LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao AS ftf	    	            \n";
        $stSql.="        	   ON pf.cod_processo = ftf.cod_processo 		                    \n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaListaInicioFiscalizacaoEconomicaObra(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarListaInicioFiscalizacaoEconomicaObra($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarListaInicioFiscalizacaoEconomicaObra($condicao)
    {
        $stSql = $this->montaRecuperarListaInicioFiscalizacaoEconomica( $condicao );
        $stSql.= " UNION ALL " ;
        $stSql.= $this->montaRecuperarListaInicioFiscalizacaoObra( $condicao );

        return $stSql;
    }

    public function recuperaListaDocumentosDevolvidos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarListaDocumentosDevolvidos( $stCondicao ).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarListaDocumentosDevolvidos($condicao)
    {
        $stSql =" 	       SELECT pf.cod_processo       		  			                            \n";
        $stSql.="	            , tf.cod_tipo 							                                \n";
        $stSql.="	            , tf.descricao 							                                \n";
        $stSql.="	            , fde.cod_documento						                                \n";
        $stSql.="	            , fde.cod_documento_entrega					                            \n";
        $stSql.="	            , fde.cod_processo					                                    \n";
        $stSql.="	            , fde.nom_documento					                                    \n";
        $stSql.="	         FROM fiscalizacao.processo_fiscal pf 			                            \n";
        $stSql.="      INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 			                        \n";
        $stSql.="    	       ON pf.cod_tipo = tf.cod_tipo 				                            \n";
        $stSql.=" LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif 			                    \n";
        $stSql.="              ON pf.cod_processo = fif.cod_processo 				                    \n";
        $stSql.=" LEFT OUTER JOIN ( SELECT fifd.cod_documento 					                        \n";
        $stSql.="		                 , de.cod_documento as cod_documento_entrega  		            \n";
        $stSql.="			             , fifd.cod_processo 					                        \n";
        $stSql.="			             , fd.cod_documento ||' - '|| fd.nom_documento as nom_documento \n";
        $stSql.="	                  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd 	        \n";
        $stSql.="	   	        INNER JOIN fiscalizacao.documento AS fd 			                    \n";
        $stSql.="			            ON fd.cod_documento = fifd.cod_documento 		                \n";
        $stSql.="	       LEFT OUTER JOIN fiscalizacao.documentos_entrega AS de 		                \n";
        $stSql.="			            ON de.cod_documento = fifd.cod_documento 		                \n";
        $stSql.="			           AND de.cod_processo = fifd.cod_processo 		                    \n";
        $stSql.="		          GROUP BY fifd.cod_documento 				    	                    \n";
        $stSql.="                        , de.cod_documento 					                        \n";
        $stSql.="                        , fifd.cod_processo 					                        \n";
        $stSql.="                        , fd.cod_documento 					                        \n";
        $stSql.="                        , fd.nom_documento 					                        \n";
        $stSql.="		          ORDER BY fifd.cod_documento) AS fde	 			                    \n";
        $stSql.="	       ON fde.cod_processo = fif.cod_processo 	 			                        \n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaListaDocumentosEntrega(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarListaDocumentosEntrega( $stCondicao ).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarListaDocumentosEntrega($condicao)
    {
        $stSql =" 	       SELECT DISTINCT pf.cod_processo       		  			                    \n";
        $stSql.="	            , tf.cod_tipo 							                                \n";
        $stSql.="	            , tf.descricao 							                                \n";
        $stSql.="	            , fde.cod_documento						                                \n";
        $stSql.="	            , fde.cod_documento_entrega					                            \n";
        $stSql.="	            , fde.cod_processo					                                    \n";
        $stSql.="	            , fde.nom_documento					                                    \n";
        $stSql.="	            , fde.situacao					                                        \n";
        $stSql.="	            , fde.observacao					                                    \n";
        $stSql.="               , TO_CHAR(pe.dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogada  	        \n";
        $stSql.="	         FROM fiscalizacao.processo_fiscal pf 		                                \n";
        $stSql.="      INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 			                        \n";
        $stSql.="    	       ON pf.cod_tipo = tf.cod_tipo 				                            \n";
        $stSql.="	   INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 			                \n";
        $stSql.="		       ON pf.cod_processo = ffpf.cod_processo 		                            \n";
        $stSql.="       LEFT JOIN fiscalizacao.prorrogacao_entrega AS pe           			            \n";
        $stSql.="     	       ON ffpf.cod_processo = pe.cod_processo              			            \n";
        $stSql.=" LEFT OUTER JOIN fiscalizacao.inicio_fiscalizacao AS fif 			                    \n";
        $stSql.="              ON pf.cod_processo = fif.cod_processo 				                    \n";
        $stSql.=" LEFT OUTER JOIN ( SELECT fifd.cod_documento 					                        \n";
        $stSql.="		                 , de.cod_documento as cod_documento_entrega  		            \n";
        $stSql.="			             , fifd.cod_processo 					                        \n";
        $stSql.="			             , de.situacao  					                            \n";
        $stSql.="			             , de.observacao  					                            \n";
        $stSql.="			             , de.timestamp  					                            \n";
        $stSql.="			             , fd.cod_documento ||' - '|| fd.nom_documento as nom_documento \n";
        $stSql.="	                  FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd 	        \n";
        $stSql.="	   	        INNER JOIN fiscalizacao.documento AS fd 			                    \n";
        $stSql.="			            ON fd.cod_documento = fifd.cod_documento 		                \n";
        $stSql.="	       LEFT OUTER JOIN fiscalizacao.documentos_entrega AS de 		                \n";
        $stSql.="			            ON de.cod_documento = fifd.cod_documento 		                \n";
        $stSql.="		               AND de.cod_processo = fifd.cod_processo 			                \n";
        $stSql.="		          GROUP BY fifd.cod_documento 					                        \n";
        $stSql.="                        , de.cod_documento 					                        \n";
        $stSql.="                        , fifd.cod_processo 					                        \n";
        $stSql.="                        , fd.cod_documento 					                        \n";
        $stSql.="                        , fd.nom_documento 					                        \n";
        $stSql.="                        , de.situacao 					                                \n";
        $stSql.="                        , de.observacao 					                            \n";
        $stSql.="                        , de.timestamp 					                            \n";
        $stSql.="		          ORDER BY fifd.cod_documento, de.timestamp DESC) AS fde	            \n";
        $stSql.="	       ON fde.cod_processo = fif.cod_processo 	 		                            \n";
        $stSql.= $condicao;

        return $stSql;
    }

    public function recuperaListaDocumentosD(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperarListaDocumentosD( $stCondicao ).$stOrdem;
        //echo "<pre>",$stSql,"</pre>";
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperarListaDocumentosD($condicao)
    {
        $stSql.="           SELECT fifd.cod_documento 					                        \n";
        $stSql.="		         , de.cod_documento as cod_documento_entrega  		            \n";
        $stSql.="			     , fifd.cod_processo 					                        \n";
        $stSql.="			     , de.situacao  					                            \n";
        $stSql.="			     , de.observacao  					                            \n";
        $stSql.="			     , de.timestamp  					                            \n";
        $stSql.="			     , fd.cod_documento ||' - '|| fd.nom_documento as nom_documento \n";
        $stSql.="	          FROM fiscalizacao.inicio_fiscalizacao_documentos AS fifd    	    \n";
        $stSql.="	   	INNER JOIN fiscalizacao.documento AS fd 			                    \n";
        $stSql.="			    ON fd.cod_documento = fifd.cod_documento 		                \n";
        $stSql.="  LEFT OUTER JOIN fiscalizacao.documentos_entrega AS de 		                \n";
        $stSql.="			    ON de.cod_documento = fifd.cod_documento 		                \n";
        $stSql.="		       AND de.cod_processo = fifd.cod_processo 			                \n";

        $stGroupby ="     GROUP BY fifd.cod_documento 					                        \n";
        $stGroupby.="            , de.cod_documento 					                        \n";
        $stGroupby.="            , fifd.cod_processo 					                        \n";
        $stGroupby.="            , fd.cod_documento 					                        \n";
        $stGroupby.="            , fd.nom_documento 					                        \n";
        $stGroupby.="            , de.situacao 					                                \n";
        $stGroupby.="            , de.observacao 					                            \n";
        $stGroupby.="            , de.timestamp 					                            \n";

        $stOrderby.="	  ORDER BY fifd.cod_documento                                           \n";
        $stOrderby.="		     , de.timestamp DESC                                            \n";

        $stSql.= $condicao . $stGroupby . $stOrderby;

        return $stSql;
    }
}
?>
