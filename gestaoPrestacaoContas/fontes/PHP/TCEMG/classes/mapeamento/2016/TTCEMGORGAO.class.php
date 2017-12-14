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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGORGAO.class.php 62310 2015-04-20 19:54:55Z franver $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTCEMGORGAO extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGORGAO()
    {
        parent::Persistente();
    }

    function recuperaExportacaoOrgaoPlanejamento(&$rsRecordSet, $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaExportacaoOrgaoPlanejamento();
	$this->setDebug( $stSql);
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
	return $obErro;
    }
    
    function montaRecuperaExportacaoOrgaoPlanejamento()
    {
	$stSql = "
	    SELECT codigo_unidade_gestora.cod_orgao
		 , tipo_unidade_gestora.tipo_orgao
		 , responsavel_unidade_gestora.cpf
		 
	      FROM ( SELECT valor AS cod_orgao
			  , cod_entidade
		       FROM administracao.configuracao_entidade
		      WHERE parametro = 'tcemg_codigo_orgao_entidade_sicom'
			AND exercicio = '".Sessao::getExercicio()."'
		 ) AS codigo_unidade_gestora
	
	 LEFT JOIN ( SELECT valor AS tipo_orgao
			  , cod_entidade
		       FROM administracao.configuracao_entidade
		      WHERE parametro = 'tcemg_tipo_orgao_entidade_sicom'
			AND exercicio = '".Sessao::getExercicio()."'
		 ) AS tipo_unidade_gestora
		ON tipo_unidade_gestora.cod_entidade = codigo_unidade_gestora.cod_entidade
	
	 LEFT JOIN ( SELECT CGM_PF.cpf
			  , configuracao_orgao.cod_entidade  
		     FROM tcemg.configuracao_orgao
	       INNER JOIN sw_cgm_pessoa_fisica as CGM_PF
		       ON CGM_PF.numcgm = configuracao_orgao.num_cgm 
		      AND configuracao_orgao.tipo_responsavel = 1
		      AND configuracao_orgao.exercicio = '".Sessao::getExercicio()."'
		      
		    ) as responsavel_unidade_gestora
		   ON responsavel_unidade_gestora.cod_entidade = codigo_unidade_gestora.cod_entidade
	
		WHERE codigo_unidade_gestora.cod_entidade IN (".$this->getDado('entidade').") ";
    
	return $stSql;
    }
	
    public function __destruct(){}

}
?>
