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

  $Id: TTCEMGConfiguracaoOrgao.class.php 64654 2016-03-18 14:44:48Z jean $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTCEMGConfiguracaoOrgao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGConfiguracaoOrgao()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configuracao_orgao');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,tipo_responsavel,num_cgm');
        
        $this->AddCampo('exercicio'              , 'varchar',   true,   '4',   true, false);
        $this->AddCampo('cod_entidade'           , 'integer',   true,    '',   true, false);
        $this->AddCampo('tipo_responsavel'       , 'integer',   true,    '',   true, false);
        $this->AddCampo('num_cgm'                , 'integer',   true,   '6',  false, false);
        $this->AddCampo('crc_contador'           , 'varchar',  false,  '11',  false, false);
        $this->AddCampo('uf_crcContador'         , 'varchar',  false,   '2',  false, false);
        $this->AddCampo('cargo_ordenador_despesa', 'varchar',  false,  '50',  false, false);
        $this->AddCampo('dt_inicio'              , 'date'   ,   true,    '',  false, false);
        $this->AddCampo('dt_fim'                 , 'date'   ,   true,    '',  false, false);
        $this->AddCampo('email'                  , 'varchar',  false,  '35',  false, false);
    }

    public function recuperaResponsaveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaResponsaveis().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaResponsaveis()
    {
	$stSql = "
	SELECT configuracao_orgao.num_cgm
	     , cgm.nom_cgm
	     , CASE configuracao_orgao.tipo_responsavel WHEN 1 THEN 'Gestor'
							WHEN 2 THEN 'Contador'
							WHEN 3 THEN 'Controle Interno'
							WHEN 4 THEN 'Ordenador de Despesa por Delegação'
              WHEN 5 THEN 'Informações - Folha de Pagamento'
		END AS nom_tipo_responsavel
	     , TO_CHAR(configuracao_orgao.dt_inicio,'dd/mm/yyyy') AS dt_inicio
	     , TO_CHAR(configuracao_orgao.dt_fim,'dd/mm/yyyy') AS dt_fim
			     , configuracao_orgao.crc_contador
			     , configuracao_orgao.uf_crccontador
			     , configuracao_orgao.cargo_ordenador_despesa
			     , configuracao_orgao.email
			     , configuracao_orgao.tipo_responsavel
			     , configuracao_orgao.cod_entidade
	  FROM tcemg.configuracao_orgao
	  JOIN sw_cgm AS cgm
	    ON cgm.numcgm = configuracao_orgao.num_cgm
	 WHERE configuracao_orgao.exercicio = '".$this->getDado('exercicio')."'
	   AND configuracao_orgao.cod_entidade = ".$this->getDado('cod_entidade')."
	    ";
	    return $stSql;
    }
	
    public function recuperaCodigos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCodigos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaCodigos()
    {
        $stSql = "  SELECT  ent.cod_entidade            
                           ,cgm.nom_cgm                 
                           ,ce.valor                    
                           ,( SELECT valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                 AND configuracao_entidade.parametro    = 'tcemg_codigo_tipo_balancete'
				 AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                           ) AS tipo_balancete         
                          ,( SELECT CASE WHEN LENGTH(valor) >= 11 
                                         THEN SUBSTR(valor, 1, LENGTH(valor)-11)
                                         ELSE valor
                                          END AS valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                 AND configuracao_entidade.parametro    = 'tcemg_tipo_orgao_entidade_sicom'
				 AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                            ) AS orgao_unidade          
                           ,( SELECT CASE WHEN LENGTH(valor) >= 11 
                                          THEN SUBSTR(valor, 1, LENGTH(valor)-11)
                                          ELSE valor
                                           END AS valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                 AND configuracao_entidade.parametro    = 'tcemg_cgm_responsavel'
				 AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                            ) AS num_cgm          
                           ,(SELECT nom_cgm 
                               FROM sw_cgm 
                              WHERE numcgm = ( SELECT CASE WHEN LENGTH(valor) >= 11 
                                                           THEN SUBSTR(valor, 1, LENGTH(valor)-11) 
                                                           ELSE valor 
                                                           END AS valor
                                                 FROM administracao.configuracao_entidade
                                                WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                                  AND configuracao_entidade.parametro    = 'tcemg_cgm_responsavel'
						  AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                                             )::integer
                            ) AS nom_cgm_responsavel
                      FROM sw_cgm AS cgm
		      
                INNER JOIN orcamento.entidade AS ent     
                        ON cgm.numcgm = ent.numcgm
			
                 LEFT JOIN administracao.configuracao_entidade AS ce      
                        ON ent.exercicio    = ce.exercicio           
                       AND ent.cod_entidade = ce.cod_entidade        
                       AND ce.parametro     = 'tcemg_codigo_orgao_entidade_sicom'
		       
                     WHERE ent.exercicio    = '".$this->getDado('exercicio')."'                  
                       AND ce.exercicio     = '".$this->getDado('exercicio')."'                              
                       AND ce.cod_modulo    = ".$this->getDado('cod_modulo')." \n";
		       
        return $stSql;
    }
	
    public function __destruct(){}

}
?>
