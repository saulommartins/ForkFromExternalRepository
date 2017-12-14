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

/*
 * Classe de mapeamento da tabela tcepe.cgm_agente_politico
 * Data de Criação: 01/10/2014
 * @author Desenvolvedor Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 * @package URBEM
 * @subpackage
 $Id:$
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEPECGMAgentePolitico extends Persistente
{
    /**
     * Método Construtor da classe de mapeamento
     *
     * @return void
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela  ('tcepe.cgm_agente_politico');

        $this->setCampoCod('cod_agente_politico');

        $this->AddCampo('numcgm'              , 'integer', true, '' , true  , true);
        $this->AddCampo('exercicio'           , 'varchar', true, '4', false , true);
        $this->AddCampo('cod_entidade'        , 'integer', true, '' , false , true);
        $this->AddCampo('cod_agente_politico' , 'integer', true, '' , false , true);
    }

    public function recuperaVinculoAgentePolitico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaVinculoAgentePolitico().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaVinculoAgentePolitico() 
    {
        $stSql = "
            SELECT cgm_agente_politico.*
                 , sw_cgm.nom_cgm AS nom_cgm
                 , sw_entidade.nom_cgm AS nom_entidade
                 , agente_politico.descricao AS nom_agente_politico

              FROM tcepe.cgm_agente_politico

        INNER JOIN sw_cgm
                ON cgm_agente_politico.numcgm = sw_cgm.numcgm

        INNER JOIN orcamento.entidade
                ON entidade.cod_entidade = cgm_agente_politico.cod_entidade 
               AND entidade.exercicio    = cgm_agente_politico.exercicio

        INNER JOIN sw_cgm as sw_entidade
                ON entidade.numcgm = sw_entidade.numcgm

        INNER JOIN tcepe.agente_politico
                ON agente_politico.cod_agente_politico = cgm_agente_politico.cod_agente_politico 

             WHERE cgm_agente_politico.exercicio = '".Sessao::getExercicio()."' ";

        return $stSql;
    }
    
     public function recuperaAgentePolitico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAgentePolitico().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaAgentePolitico() 
    {
        $stSql = "
            SELECT cgm_agente_politico.numcgm 
                 , sw_cgm.nom_cgm AS nome_agente_politico
                 , sw_cgm_pessoa_fisica.cpf
                 , sw_cgm_pessoa_fisica.rg
                 , sw_cgm_pessoa_fisica.orgao_emissor||sw_uf_orgaoemissor.sigla_uf AS orgao_expeditor
                 , sw_municipio.nom_municipio AS municipio
                 , sw_uf.sigla_uf 
                 , sw_cgm.e_mail AS email
                 , cgm_agente_politico.cod_agente_politico  AS tipo_agente_politico
             FROM tcepe.cgm_agente_politico 
             JOIN sw_cgm
               ON sw_cgm.numcgm = cgm_agente_politico.numcgm 
             JOIN sw_cgm_pessoa_fisica
               ON sw_cgm_pessoa_fisica.numcgm = cgm_agente_politico.numcgm 
             JOIN sw_municipio
               ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
              AND sw_municipio.cod_uf = sw_cgm.cod_uf
             JOIN sw_uf
               ON sw_uf.cod_uf = sw_cgm.cod_uf
             JOIN sw_uf AS sw_uf_orgaoemissor
               ON sw_uf_orgaoemissor.cod_uf = sw_cgm_pessoa_fisica.cod_uf_orgao_emissor
             
             WHERE cgm_agente_politico.cod_entidade IN ( '".$this->getDado('cod_entidade')."')
                 AND cgm_agente_politico.exercicio = '".$this->getDado('exercicio')."'
            ";
            return $stSql;
    }
}