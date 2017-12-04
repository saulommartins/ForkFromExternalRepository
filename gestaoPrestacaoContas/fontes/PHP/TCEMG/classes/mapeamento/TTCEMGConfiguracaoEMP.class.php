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
    * Classe de mapeamento da tabela TCEMG.CONFIGURACAO_LEIS_PPA
    * Data de Criação: 15/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCEMGConfiguracaoEMP.class.php 61800 2015-03-04 20:16:20Z arthur $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGConfiguracaoEMP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGConfiguracaoEMP()
    {
        parent::Persistente();
        $this->setTabela('tcemg.arquivo_emp');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_entidade, cod_empenho');

        $this->AddCampo('exercicio'                 , 'varchar', true,   4,  true, false);
        $this->AddCampo('cod_entidade'              , 'integer', false, '', false,  true);
        $this->AddCampo('cod_empenho'               , 'integer', false, '', false, false);
        $this->AddCampo('cod_licitacao'             , 'integer', false, '', false, false);
        $this->AddCampo('exercicio_licitacao'       , 'varchar', false,  4, false, false);
        $this->AddCampo('cod_modalidade'            , 'integer', false, '', false, false);

    }
    
    function recuperaComprasLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaComprasLicitacao().$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaComprasLicitacao(){
	
        $stSql  = "SELECT arquivo_emp.cod_empenho
                        , arquivo_emp.cod_licitacao
                        , arquivo_emp.exercicio_licitacao
                        
                     FROM tcemg.arquivo_emp
               
               INNER JOIN empenho.empenho
                       ON empenho.exercicio    = arquivo_emp.exercicio
                      AND empenho.cod_entidade = arquivo_emp.cod_entidade
                      AND empenho.cod_empenho  = arquivo_emp.cod_empenho 
               
               INNER JOIN empenho.pre_empenho
                       ON pre_empenho.exercicio       = empenho.exercicio
                      AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               
               INNER JOIN empenho.item_pre_empenho
                       ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                      AND item_pre_empenho.exercicio       = pre_empenho.exercicio       
                      
               INNER JOIN empenho.item_pre_empenho_julgamento
                       ON item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho 
                      AND item_pre_empenho_julgamento.exercicio       = item_pre_empenho.exercicio       
                      AND item_pre_empenho_julgamento.num_item        = item_pre_empenho.num_item        
               
               INNER JOIN compras.mapa_cotacao
                       ON mapa_cotacao.cod_cotacao       = item_pre_empenho_julgamento.cod_cotacao
                      AND mapa_cotacao.exercicio_cotacao = item_pre_empenho_julgamento.exercicio
               
               INNER JOIN licitacao.licitacao
                       ON licitacao.exercicio_mapa = mapa_cotacao.exercicio_mapa
                      AND licitacao.cod_mapa       = mapa_cotacao.cod_mapa
               
                    WHERE arquivo_emp.exercicio_licitacao = '".$this->getDado('exercicio_licitacao')."' 
                      AND arquivo_emp.cod_licitacao       = ".$this->getDado('cod_licitacao')." 
                      AND arquivo_emp.cod_modalidade      = ".$this->getDado('cod_modalidade')." 
                      AND arquivo_emp.cod_empenho         = ".$this->getDado('cod_empenho');
                      
        return $stSql;
    }

}
