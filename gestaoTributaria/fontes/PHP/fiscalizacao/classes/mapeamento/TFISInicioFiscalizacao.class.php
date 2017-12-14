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
    * Data de Criacao: 01/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Mapeamento

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CLA_PERSISTENTE );

class TFISInicioFiscalizacao extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela( 'fiscalizacao.inicio_fiscalizacao' );

        $this->setCampoCod( 'cod_processo' );
        $this->setComplementoChave( '' );

        $this->AddCampo( 'cod_processo','integer',true,'',false,true );
        $this->AddCampo( 'cod_fiscal','integer',true,'',false,true );
        $this->AddCampo( 'cod_tipo_documento','integer',true,'',false,true );
        $this->AddCampo( 'cod_documento','integer',true,'',false,true );
        $this->AddCampo( 'dt_inicio','date',true,'',false,false );
        $this->AddCampo( 'local_entrega','varchar',true,'',false,false );
        $this->AddCampo( 'prazo_entrega','date',true,'',false,false );
        $this->AddCampo( 'observacao','text',true,'',false,false );
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

    private function montaRecuperarInicioFiscalizacaoEconomica($condicao)
    {
        $stSql ="       SELECT fif.cod_processo         			                        \n";
        $stSql.="            , fif.cod_fiscal           			                        \n";
        $stSql.="            , fif.cod_tipo_documento   			                        \n";
        $stSql.="            , fif.cod_documento        		       	                    \n";
        $stSql.="            , fif.dt_inicio               		       	                    \n";
        $stSql.="            , TO_CHAR(fif.prazo_entrega, 'dd/mm/yyyy') as prazo_entrega    \n";
        $stSql.="            , fif.local_entrega        		       	                    \n";
        $stSql.="            , fif.observacao                                  	            \n";
        $stSql.="            , pfe.inscricao_economica                        	            \n";
        $stSql.="            , pf.cod_tipo 	                        	                    \n";
        $stSql.="            , tf.descricao 	                        	                \n";
        $stSql.="            , TO_CHAR(pe.dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogada    \n";
        $stSql.="         FROM fiscalizacao.inicio_fiscalizacao AS fif         	            \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal AS pf              	            \n";
        $stSql.="     	    ON fif.cod_processo = pf.cod_processo              	            \n";
        $stSql.="    LEFT JOIN fiscalizacao.prorrogacao_entrega AS pe         	            \n";
        $stSql.="     	    ON fif.cod_processo = pe.cod_processo              	            \n";
        $stSql.="    LEFT JOIN fiscalizacao.termino_fiscalizacao AS ftf      	            \n";
        $stSql.="           ON pf.cod_processo = ftf.cod_processo       	                \n";
         $stSql.="   INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 		                \n";
        $stSql.="   	    ON pf.cod_tipo = tf.cod_tipo 			                        \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 	                \n";
        $stSql.="           ON pf.cod_processo = pfe.cod_processo 		                    \n";
        $stSql.="   INNER JOIN fiscalizacao.fiscal_processo_fiscal AS fpf      	            \n";
        $stSql.="     	    ON fif.cod_fiscal = fpf.cod_fiscal                	            \n";
        $stSql.="          AND fif.cod_processo = fpf.cod_processo             	            \n";
        $stSql.="   INNER JOIN administracao.modelo_documento AS amd           	            \n";
        $stSql.="           ON fif.cod_documento = amd.cod_documento           	            \n";
        $stSql.="          AND fif.cod_tipo_documento = amd.cod_tipo_documento 	            \n";

        $stOrderBy =" ORDER BY pe.dt_prorrogacao desc limit 1           	                \n";

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
        $stSql ="       SELECT fif.cod_processo         			                        \n";
        $stSql.="            , fif.cod_fiscal           			                        \n";
        $stSql.="            , fif.cod_tipo_documento   			                        \n";
        $stSql.="            , fif.cod_documento        		       	                    \n";
        $stSql.="            , fif.dt_inicio               		                        	\n";
        $stSql.="            , TO_CHAR(fif.prazo_entrega, 'dd/mm/yyyy') as prazo_entrega    \n";
        $stSql.="            , fif.local_entrega        		       	                    \n";
        $stSql.="            , fif.observacao                                  	            \n";
        $stSql.="            , pfo.inscricao_municipal                        	            \n";
        $stSql.="            , pf.cod_tipo 	                        	                    \n";
        $stSql.="            , tf.descricao 	                        	                \n";
        $stSql.="            , TO_CHAR(pe.dt_prorrogacao, 'dd/mm/yyyy') as dt_prorrogada    \n";
        $stSql.="         FROM fiscalizacao.inicio_fiscalizacao AS fif         	            \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal AS pf              	            \n";
        $stSql.="     	    ON fif.cod_processo = pf.cod_processo              	            \n";
        $stSql.="   LEFT JOIN fiscalizacao.prorrogacao_entrega AS pe           	            \n";
        $stSql.="     	    ON fif.cod_processo = pe.cod_processo              	            \n";
        $stSql.="   INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 		                \n";
        $stSql.="   	    ON pf.cod_tipo = tf.cod_tipo 			                        \n";
        $stSql.="   INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo 	                \n";
        $stSql.="           ON pf.cod_processo = pfo.cod_processo 		                    \n";
        $stSql.="   INNER JOIN fiscalizacao.fiscal_processo_fiscal AS fpf             	    \n";
        $stSql.="     	    ON fif.cod_fiscal = fpf.cod_fiscal                	            \n";
        $stSql.="          AND fif.cod_processo = fpf.cod_processo             	            \n";
        $stSql.="   INNER JOIN administracao.modelo_documento AS amd           	            \n";
        $stSql.="           ON fif.cod_documento = amd.cod_documento           	            \n";
        $stSql.="          AND fif.cod_tipo_documento = amd.cod_tipo_documento 	            \n";
        $stSql.="    LEFT JOIN fiscalizacao.termino_fiscalizacao AS ftf      	            \n";
        $stSql.="           ON pf.cod_processo = ftf.cod_processo       	                \n";

        $stOrderBy =" ORDER BY pe.dt_prorrogacao desc limit 1           	                \n";

        $stSql.= $condicao . $stOrderBy;

        return $stSql;
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
        $stSql =" 	        SELECT pf.cod_processo 					                    \n";
        $stSql.="	             , tf.cod_tipo 						                    \n";
        $stSql.="	             , tf.descricao 					                    \n";
        $stSql.="	             , pfe.inscricao_economica AS inscricao 		        \n";
        $stSql.="	             , fc.cod_fiscal 					                    \n";
        $stSql.="	             , pf.numcgm 						                    \n";
        $stSql.="	          FROM fiscalizacao.inicio_fiscalizacao AS fif 		        \n";
        $stSql.="       INNER JOIN fiscalizacao.processo_fiscal pf 	 		            \n";
        $stSql.="              	ON pf.cod_processo = fif.cod_processo 			        \n";
        $stSql.="       INNER JOIN fiscalizacao.processo_fiscal_empresa AS pfe 		    \n";
        $stSql.="              	ON pf.cod_processo = pfe.cod_processo 			        \n";
        $stSql.="       INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 		        \n";
        $stSql.="    	       	ON pf.cod_tipo = tf.cod_tipo 				            \n";
        $stSql.="       INNER JOIN ( SELECT ffc.cod_fiscal 				                \n";
        $stSql.="			              , ffc.numcgm 					                \n";
        $stSql.="			              , ffpf.cod_processo 				            \n";
        $stSql.="		               FROM fiscalizacao.fiscal as ffc 			        \n";
        $stSql.="		         INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf \n";
        $stSql.="			             ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 	\n";
        $stSql.="	            ON fc.cod_processo = pf.cod_processo 			        \n";
        $stSql.="   	INNER JOIN administracao.modelo_documento AS amd           	    \n";
        $stSql.="              	ON fif.cod_documento = amd.cod_documento           	    \n";
        $stSql.="              AND fif.cod_tipo_documento = amd.cod_tipo_documento 	    \n";
        $stSql.="  LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc	    \n";
        $stSql.="              	ON pf.cod_processo = pfc.cod_processo 			        \n";
        $stSql.="  LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao AS ftf             \n";
        $stSql.="        	    ON pf.cod_processo = ftf.cod_processo 					\n";
        $stSql.= $condicao;

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
        $stSql =" 	        SELECT pf.cod_processo 					                    \n";
        $stSql.="	             , tf.cod_tipo 						                    \n";
        $stSql.="	             , tf.descricao 					                    \n";
        $stSql.="	             , pfo.inscricao_municipal AS inscricao 		        \n";
        $stSql.="	             , fc.cod_fiscal 					                    \n";
        $stSql.="	             , pf.numcgm 						                    \n";
        $stSql.="	          FROM fiscalizacao.inicio_fiscalizacao AS fif 		        \n";
        $stSql.="       INNER JOIN fiscalizacao.processo_fiscal pf 	 		            \n";
        $stSql.="               ON pf.cod_processo = fif.cod_processo 			        \n";
        $stSql.="       INNER JOIN fiscalizacao.processo_fiscal_obras AS pfo 		    \n";
        $stSql.="               ON pf.cod_processo = pfo.cod_processo 			        \n";
        $stSql.="       INNER JOIN fiscalizacao.tipo_fiscalizacao AS tf 		        \n";
        $stSql.="    	        ON pf.cod_tipo = tf.cod_tipo 				            \n";
        $stSql.="       INNER JOIN ( SELECT ffc.cod_fiscal 				                \n";
        $stSql.="			             , ffc.numcgm 					                \n";
        $stSql.="			             , ffpf.cod_processo 				            \n";
        $stSql.="		              FROM fiscalizacao.fiscal as ffc 			        \n";
        $stSql.="		        INNER JOIN fiscalizacao.fiscal_processo_fiscal AS ffpf 	\n";
        $stSql.="			            ON ffc.cod_fiscal = ffpf.cod_fiscal) AS fc 	    \n";
        $stSql.="	            ON fc.cod_processo = pf.cod_processo 			        \n";
        $stSql.="       INNER JOIN administracao.modelo_documento AS amd           	    \n";
        $stSql.="           	ON fif.cod_documento = amd.cod_documento           	    \n";
        $stSql.="              AND fif.cod_tipo_documento = amd.cod_tipo_documento 	    \n";
        $stSql.="  LEFT OUTER JOIN fiscalizacao.processo_fiscal_cancelado AS pfc	    \n";
        $stSql.="              	ON pf.cod_processo = pfc.cod_processo 			        \n";
        $stSql.="  LEFT OUTER JOIN fiscalizacao.termino_fiscalizacao AS ftf             \n";
        $stSql.="        	    ON pf.cod_processo = ftf.cod_processo                   \n";
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

}

?>
