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

  $Id: TTCEMGORGAO2014.class.php 62310 2015-04-20 19:54:55Z franver $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTCEMGORGAO2014 extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGORGAO2014()
    {
        parent::Persistente();
    }

    public function recuperaOrgao(&$rsRecordSet,$stFiltro = "",$stOrder = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaOrgao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgao()
    {
        $stSql  = "SELECT 10 AS tipoRegistro,
                          (SELECT valor::INTEGER
                             FROM administracao.configuracao_entidade
                            WHERE exercicio = ACE.exercicio
                              AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                              AND cod_entidade = ACE.cod_entidade) AS codOrgao,
                          ACE.valor::INTEGER AS tipoOrgao,
                          CGM_PJ.cnpj::TEXT AS cnpjOrgao,
			  ACE.cod_entidade||''||ACE.exercicio AS chave
                    FROM administracao.configuracao_entidade AS ACE
              INNER JOIN orcamento.entidade AS OE
                      ON OE.cod_entidade = ACE.cod_entidade
                     AND OE.exercicio = ACE.exercicio
               LEFT JOIN sw_cgm_pessoa_juridica AS CGM_PJ
                      ON CGM_PJ.numcgm = OE.numcgm
                   WHERE ACE.exercicio = '".$this->getDado('exercicio')."'
                     AND ACE.cod_entidade IN (".$this->getDado('entidade').")
                     AND ACE.parametro = 'tcemg_tipo_orgao_entidade_sicom' ";
        return $stSql;
    }
    
    public function recuperaOrgaoResponsavel(&$rsRecordSet,$stFiltro = "",$stOrder = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaOrgaoResponsavel",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgaoResponsavel()
    {
        
	$stSql = "SELECT 11 AS tipoRegistro
			, configuracao_orgao.tipo_responsavel AS tipoResponsavel
			, cgm_pf.rg AS cartIdent
			, sw_uf.sigla_uf AS orgEmissorCi
			, cgm_pf.cpf
			, CASE WHEN configuracao_orgao.tipo_responsavel = 2
			       THEN configuracao_orgao.crc_contador
			       ELSE ''
			  END as crcContador
			, CASE WHEN configuracao_orgao.tipo_responsavel = 2
			       THEN configuracao_orgao.uf_crccontador 
			       ELSE ''
			  END AS ufCrcContador
			, CASE WHEN configuracao_orgao.tipo_responsavel = 4
			       THEN configuracao_orgao.cargo_ordenador_despesa 
			       ELSE ''
			  END AS cargoOrdDespDeleg
			, configuracao_entidade.exercicio
			, configuracao_entidade.cod_entidade
			, configuracao_orgao.num_cgm AS cgm
			, CASE WHEN TO_CHAR(TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY'), 'yyyymmdd') > TO_CHAR(dt_inicio, 'yyyymmdd')
                   THEN TO_CHAR(TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY'), 'ddmmyyyy')
                   ELSE to_char(configuracao_orgao.dt_inicio, 'ddmmyyyy')
               END AS dtInicio
            , CASE WHEN TO_CHAR(TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY'), 'yyyymmdd') < TO_CHAR(dt_fim, 'yyyymmdd')
                   THEN TO_CHAR(TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY'), 'ddmmyyyy')
                   ELSE to_char(configuracao_orgao.dt_inicio, 'ddmmyyyy')
               END AS dtfinal
            , configuracao_orgao.email AS email
			, configuracao_entidade.cod_entidade||''||configuracao_entidade.exercicio AS chave
		   
		   FROM administracao.configuracao_entidade
	   
	     INNER JOIN tcemg.configuracao_orgao
		     ON configuracao_orgao.cod_entidade = configuracao_entidade.cod_entidade
		    AND configuracao_orgao.exercicio    = configuracao_entidade.exercicio
	    
	      LEFT JOIN sw_cgm_pessoa_fisica AS cgm_pf
		     ON cgm_pf.numcgm = configuracao_orgao.num_cgm
	 
	      LEFT JOIN sw_cgm AS cgm
		     ON cgm.numcgm = configuracao_orgao.num_cgm
	 
	     INNER JOIN sw_uf 
		     ON sw_uf.cod_uf = cgm_pf.cod_uf_orgao_emissor
	 
		  WHERE configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
		    AND configuracao_entidade.cod_entidade IN (".$this->getDado('entidade').")
		    AND configuracao_entidade.parametro    = 'tcemg_cgm_responsavel'
		    AND (TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY') BETWEEN dt_inicio AND dt_fim
                OR
				 TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY') BETWEEN dt_inicio AND dt_fim
				)
		";
	
        return $stSql;
    }
    
    public function __destruct(){}

}
?>
