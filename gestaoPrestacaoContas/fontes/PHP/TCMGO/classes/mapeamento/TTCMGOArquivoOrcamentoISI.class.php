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
    * Extensão da Classe de mapeamento
    * Data de Criação: 28/01/2015

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOArquivoOrcamentoISI extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGOArquivoOrcamentoISI()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

public function recuperaISI(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaISI",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaISI()
{
    $stSql  = "
          SELECT 10::INTEGER AS tipo_registro
               , dados_proprietario.num_documento
               , dados_proprietario.tipo_pessoa
               , sw_cgm.nom_cgm 
               , sw_cgm.logradouro
               , sw_cgm.bairro AS setor
               , sw_municipio.nom_municipio
               , sw_uf.sigla_uf
               , sw_cgm.cep
               , sw_cgm.fone_residencial
               , sw_cgm.e_mail
               , resp_tecnico.nom_cgm AS nom_responsavel_tec
               , cpf_responsavel.cpf AS cpf_responsavel
               , resp_tecnico.e_mail AS email_responsavel_tec
               , (SELECT valor FROM administracao.configuracao WHERE parametro ilike 'versao_sistema' and exercicio = '".$this->getDado('exercicio')."') AS versao
               , 'URBEM' AS nom_sistema
	    ";
	    
    if ( Sessao::getExercicio() >= "2014" ) {
	$stSql .= " , 1 AS bo_portal_transparencia
		    , 'http://www.urbem.cnm.org.br/transparencia/' AS url_portal_transparencia
		    , 1 AS bo_sistema_integrado
		    , 1 AS bo_despesa
		    , 1 AS bo_receita
	";
    }

    $stSql  .= "
                , '' AS numero_sequencial
		
            FROM sw_cgm
      
      INNER JOIN (SELECT numcgm
		       , cpf AS num_documento
                       , 1 AS tipo_pessoa                       
                    FROM sw_cgm_pessoa_fisica
               UNION ALL
                  SELECT numcgm
                       , cnpj AS num_documento
                       , 2 AS tipo_pessoa                     
                    FROM sw_cgm_pessoa_juridica 
                ) AS dados_proprietario
              ON dados_proprietario.numcgm = sw_cgm.numcgm  
      
      INNER JOIN sw_municipio
              ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
             AND sw_municipio.cod_uf = sw_cgm.cod_uf
      
      INNER JOIN sw_uf
              ON sw_uf.cod_uf = sw_cgm.cod_uf
       
       LEFT JOIN tcmgo.responsavel_tecnico
              ON responsavel_tecnico.cod_entidade = '".$this->getDado('cod_entidade')."'
             AND responsavel_tecnico.exercicio    = '".$this->getDado('exercicio')."'
      
      INNER JOIN sw_cgm AS resp_tecnico
              ON resp_tecnico.numcgm = responsavel_tecnico.cgm_responsavel
      
      INNER JOIN sw_cgm_pessoa_fisica AS cpf_responsavel
              ON cpf_responsavel.numcgm = responsavel_tecnico.cgm_responsavel       
           
	   WHERE sw_cgm.numcgm::TEXT = (SELECT valor 
                                          FROM administracao.configuracao 
                                         WHERE configuracao.exercicio = '".$this->getDado('exercicio')."'
                                           AND configuracao.cod_modulo = 42
                                           AND parametro = 'provedor_sistema')
       ";

    return $stSql;
}

}