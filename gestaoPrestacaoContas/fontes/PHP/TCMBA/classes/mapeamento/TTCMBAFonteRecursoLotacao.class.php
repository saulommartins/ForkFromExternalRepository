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
  * Mapeamento tcmba.fonte_recurso_lotacao
  * Data de Criação: 03/11/2015
  * 
  * @author Analista:      Dagiane Vieira
  * @author Desenvolvedor: Arthur Cruz
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/
require_once CLA_PERSISTENTE;

class TTCMBAFonteRecursoLotacao extends Persistente {

    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.fonte_recurso_lotacao');
        $this->setComplementoChave('cod_tipo_fonte, exercicio, cod_entidade, cod_orgao');
        
        $this->AddCampo('cod_tipo_fonte'  , 'integer',  true,  '', true, true);
        $this->AddCampo('exercicio'       , 'varchar',  true, '4', true, true);
        $this->AddCampo('cod_entidade'    , 'integer',  true,  '', true, true);
        $this->AddCampo('cod_orgao'       , 'integer',  true,  '', true, true);
    }
    
    public function recuperaFonteRecursoLotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFonteRecursoLotacao().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFonteRecursoLotacao()
    {
        $stSql = " SELECT recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') AS descricao
                        , vw_orgao_nivel.orgao AS cod_estrutural
                        , orgao.cod_orgao 

                     FROM organograma.orgao
            
               INNER JOIN organograma.vw_orgao_nivel
                       ON vw_orgao_nivel.cod_orgao = orgao.cod_orgao 
            
               INNER JOIN organograma.organograma
                       ON organograma.cod_organograma = vw_orgao_nivel.cod_organograma
                      AND organograma.ativo = TRUE ";

        return $stSql;
    }
    
    public function recuperaFonteRecursoLotacaoSelecionado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaFonteRecursoLotacaoSelecionado().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFonteRecursoLotacaoSelecionado()
    {
        $stSql = " SELECT recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') AS descricao
                        , vw_orgao_nivel.orgao AS cod_estrutural
                        , fonte_recurso_lotacao.cod_orgao 

                     FROM tcmba.fonte_recurso_lotacao

               INNER JOIN organograma.orgao
                       ON orgao.cod_orgao = fonte_recurso_lotacao.cod_orgao
            
               INNER JOIN organograma.vw_orgao_nivel
                       ON vw_orgao_nivel.cod_orgao = orgao.cod_orgao 
            
               INNER JOIN organograma.organograma
                       ON organograma.cod_organograma = vw_orgao_nivel.cod_organograma
                      AND organograma.ativo = TRUE  

                    WHERE fonte_recurso_lotacao.cod_tipo_fonte = ".$this->getDado('cod_tipo_fonte')."
                      AND fonte_recurso_lotacao.cod_entidade   = ".$this->getDado('cod_entidade')."
                      AND fonte_recurso_lotacao.exercicio      = '".$this->getDado('exercicio')."' ";

        return $stSql;
    }
    
    public function __destruct(){}

}

?>