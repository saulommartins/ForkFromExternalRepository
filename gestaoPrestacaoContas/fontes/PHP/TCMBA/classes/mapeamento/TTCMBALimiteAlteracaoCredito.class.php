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
    * Extensão da Classe de mapeamento TCM-BA tcmba.limite_alteracao_credito
    * Data de Criação: 14/09/2015
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBALimiteAlteracaoCredito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.limite_alteracao_credito');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_tipo_alteracao, exercicio, cod_entidade, cod_norma');
                                                    
        $this->AddCampo('exercicio'          ,'char'    ,true ,'4'  ,true  ,'','');
        $this->AddCampo('cod_entidade'       ,'integer' ,true ,''   ,true  ,'','');
        $this->AddCampo('cod_norma'          ,'integer' ,true ,''   ,true  ,'','');
        $this->AddCampo('cod_tipo_alteracao' ,'integer' ,true ,''   ,true  ,'','');
        $this->AddCampo('valor_alteracao'    ,'numeric' ,true ,'14' ,false ,'','');
        
    }
    
    function recuperaLimiteAlteracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaLimiteAlteracao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaLimiteAlteracao()
    {
        $stSql = "  SELECT 
                             limite_alteracao_credito.cod_entidade::varchar||' - '||sw_cgm.nom_cgm as descricao_entidade
                           , limite_alteracao_credito.cod_tipo_alteracao::varchar||' - '||tipo_alteracao_orcamentaria.descricao as descricao_alteracao
                           , limite_alteracao_credito.cod_norma::varchar||' - '||tipo_norma.nom_tipo_norma||' - '||norma.nom_norma as descricao_lei
                           , limite_alteracao_credito.exercicio
                           , limite_alteracao_credito.cod_entidade
                           , limite_alteracao_credito.cod_norma
                           , limite_alteracao_credito.cod_tipo_alteracao
                           , limite_alteracao_credito.valor_alteracao
                           
                      FROM tcmba.limite_alteracao_credito
                
                INNER JOIN tcmba.tipo_alteracao_orcamentaria 
                        ON tipo_alteracao_orcamentaria.cod_tipo = limite_alteracao_credito.cod_tipo_alteracao
                
                INNER JOIN normas.norma
                        ON norma.cod_norma = limite_alteracao_credito.cod_norma
                
                INNER JOIN normas.tipo_norma
                        ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma
                
                INNER JOIN orcamento.entidade
                        ON entidade.exercicio     = limite_alteracao_credito.exercicio
                        AND entidade.cod_entidade = limite_alteracao_credito.cod_entidade
                
                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = entidade.numcgm

                ";
        return $stSql;
    }

}//End of Class

?>