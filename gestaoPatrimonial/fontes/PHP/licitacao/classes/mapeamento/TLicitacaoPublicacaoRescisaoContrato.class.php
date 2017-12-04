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
    * Classe de mapeamento da tabela licitacao.publicacao_contrato
    * Data de Criação: 01/10/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Lisiane Da Rosa Morais

    * $Id:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoPublicacaoRescisaoContrato extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("licitacao.publicacao_rescisao_contrato");

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo('cod_entidade'      ,'integer',true ,''  ,true ,'TLicitacaoRescisaoContrato');
        $this->AddCampo('exercicio_contrato','char'   ,true ,'4' ,true ,'TLicitacaoRescisaoContrato');
        $this->AddCampo('num_contrato'      ,'integer',true ,''  ,true ,'TLicitacaoContrato');
        $this->AddCampo('cgm_imprensa'      ,'integer',true ,''  ,true , 'TLicitacaoVeiculosPublicidade');
        $this->AddCampo('dt_publicacao'     ,'date'   ,true ,''  ,true ,false);
        $this->AddCampo('num_publicacao'    ,'integer',false ,''  ,false,false);
        $this->AddCampo('observacao'        ,'varchar',false ,'100'  ,false,false);

    }

	public function recuperaVeiculosPublicacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
	{
		$obErro      = new Erro;
		$obConexao   = new Conexao;
		$rsRecordSet = new RecordSet;
		$stSql = $this->montaRecuperaVeiculosPublicacao().$stFiltro.$stOrdem;
		$this->stDebug = $stSql;
		$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	
		return $obErro;
	}
	

    public function montaRecuperaVeiculosPublicacao()
    {
        $stSql = " SELECT publicacao_rescisao_contrato.num_contrato												
                    	, publicacao_rescisao_contrato.exercicio_contrato 												
                   		, publicacao_rescisao_contrato.cod_entidade												
                   		, to_char( publicacao_rescisao_contrato.dt_publicacao, 'dd/mm/yyyy' ) as dt_publicacao 	
                   		, publicacao_rescisao_contrato.cgm_imprensa as num_veiculo										
                   		, publicacao_rescisao_contrato.num_publicacao                         					
                   		, sw_cgm.nom_cgm as nom_veiculo 												
                   		, publicacao_rescisao_contrato.observacao 												
                     FROM licitacao.publicacao_rescisao_contrato  												
                   	INNER JOIN  sw_cgm 																	
                   	   ON sw_cgm.numcgm = publicacao_rescisao_contrato.cgm_imprensa 	  
                   	WHERE num_contrato       = ".$this->getDado('num_contrato')."	  
                      AND exercicio_contrato = '".$this->getDado('exercicio_contrato')."' 	  
                   	  AND cod_entidade       = ".$this->getDado('cod_entidade')."     
             ";
        return $stSql;
    }
	
	public function deletaPorContrato()
	{
		$obErro      = new Erro;
		$obConexao   = new Conexao;
		$rsRecordSet = new RecordSet;
		$stSql = $this->montaDeletaPorContrato().$stFiltro.$stOrdem;
		$this->stDebug = $stSql;
		$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	
		return $obErro;
	}

    public function montaDeletaPorContrato()
    {
        $stSql = " DELETE 												
                     FROM licitacao.publicacao_rescisao_contrato  												
                   	WHERE num_contrato       = ".$this->getDado('num_contrato')."	  
                      AND exercicio_contrato = '".$this->getDado('exercicio_contrato')."' 	  
                   	  AND cod_entidade       = ".$this->getDado('cod_entidade')."     
             ";
        return $stSql;
    }
}
