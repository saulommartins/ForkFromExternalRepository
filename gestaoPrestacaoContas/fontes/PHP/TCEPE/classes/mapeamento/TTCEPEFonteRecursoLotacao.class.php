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
    * Classe de mapeamento da tabela tcepe.fonte_recurso_lotacao
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    *
    $Id: TTCEPEFonteRecursoLotacao.class.php 60373 2014-10-16 14:35:21Z diogo.zarpelon $
    *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEFonteRecursoLotacao extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEFonteRecursoLotacao()
    {
        parent::Persistente();
        $this->setTabela('tcepe.fonte_recurso_lotacao');

        $this->setCampoCod('cod_fonte');
        $this->setComplementoChave('exercicio, cod_entidade, cod_orgao');

        $this->AddCampo('cod_fonte'      , 'integer', true  , ''  , true, true);
        $this->AddCampo('exercicio'      , 'varchar', true  , '4' , true, true);
        $this->AddCampo('cod_entidade'   , 'integer', true  , ''  , true, true);
        $this->AddCampo('cod_orgao'      , 'integer', true  , ''  , true, true);
        
    }

    public function recuperaLotacoesSelecionados(&$rsRecordset, $stFiltro = "", $stOrdem = "")
    {
        $obErro = $this->executaRecupera("montaRecuperaLotacoesSelecionados", $rsRecordset, $stFiltro, $stOrdem);
        return $obErro;
    }

    public function montaRecuperaLotacoesSelecionados()
    {
        $stSql = "
            SELECT recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') AS descricao
                 , vw_orgao_nivel.orgao AS cod_estrutural
                 , orgao.cod_orgao 

             FROM tcepe.fonte_recurso_lotacao

       INNER JOIN organograma.orgao
               ON orgao.cod_orgao = fonte_recurso_lotacao.cod_orgao

       INNER JOIN organograma.vw_orgao_nivel
               ON vw_orgao_nivel.cod_orgao = orgao.cod_orgao 

       INNER JOIN organograma.organograma
               ON organograma.cod_organograma = vw_orgao_nivel.cod_organograma
              AND organograma.ativo = true
       
            WHERE 1=1 ";
    
            if ($this->getDado('cod_fonte')) {
                $stSql .= " AND fonte_recurso_lotacao.cod_fonte = ".$this->getDado('cod_fonte');
            }
        
            if ($this->getDado('exercicio')) {
                $stSql .= " AND fonte_recurso_lotacao.exercicio = '".$this->getDado('exercicio')."'";
            }

            if ($this->getDado('cod_entidade')) {
                $stSql .= " AND fonte_recurso_lotacao.cod_entidade = ".$this->getDado('cod_entidade');
            }

        return $stSql;
    }

    public function recuperaLotacoesDisponiveis(&$rsRecordset, $stFiltro = "", $stOrdem = "")
    {
        $obErro = $this->executaRecupera("montaRecuperaLotacoesDisponiveis", $rsRecordset, $stFiltro, $stOrdem);
        return $obErro;
    }

    public function montaRecuperaLotacoesDisponiveis()
    {
        $stSql = "
            SELECT recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao 
                 , orgao.cod_orgao 
                 , vw_orgao_nivel.orgao  AS cod_estrutural

              FROM organograma.orgao 

        INNER JOIN organograma.vw_orgao_nivel
                ON vw_orgao_nivel.cod_orgao = orgao.cod_orgao

        INNER JOIN organograma.organograma
                ON organograma.cod_organograma = vw_orgao_nivel.cod_organograma
               AND organograma.ativo = true

             WHERE NOT EXISTS (
                SELECT 1
                  
                  FROM tcepe.fonte_recurso_lotacao
                 
                 WHERE fonte_recurso_lotacao.cod_orgao = orgao.cod_orgao ";

            if ($this->getDado('cod_fonte')) {
                $stSql .= " AND fonte_recurso_lotacao.cod_fonte = ".$this->getDado('cod_fonte');
            }
        
            if ($this->getDado('exercicio')) {
                $stSql .= " AND fonte_recurso_lotacao.exercicio = '".$this->getDado('exercicio')."'";
            }

            if ($this->getDado('cod_entidade')) {
                $stSql .= " AND fonte_recurso_lotacao.cod_entidade = ".$this->getDado('cod_entidade');
            }
        
        $stSql .= " ) ";

        return $stSql;
    }

}