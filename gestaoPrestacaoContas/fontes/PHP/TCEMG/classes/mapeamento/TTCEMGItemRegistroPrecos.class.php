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
 **/
?>
<?php
/**
 * Classe de mapeamento da tabela tcemg.item_registro_precos
 * Data de Criação: 11/03/2014
 * 
 * @author Analista      : Eduardo Schitz
 * @author Desenvolvedor : Franver Sarmento de Moraes
 * 
 * @package URBEM
 * @subpackage Mapeamento
 * 
 * Casos de uso: uc-02.09.04
 *
 * $Id: TTCEMGItemRegistroPrecos.class.php 61913 2015-03-13 18:55:57Z franver $
 * $Revision: 61913 $
 * $Author: franver $
 * $Date: 2015-03-13 15:55:57 -0300 (Fri, 13 Mar 2015) $
 * 
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGItemRegistroPrecos extends Persistente
{
    public function TTCEMGItemRegistroPrecos()
    {
        parent::Persistente();
        $this->setTabela('tcemg.item_registro_precos');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador, cod_lote, cod_item, cgm_fornecedor');
        
        $this->addCampo('cod_entidade'                  , 'integer' , true , ''    ,  true ,  true);
        $this->AddCampo('numero_registro_precos'        , 'integer' , true , ''    ,  true ,  true);
        $this->AddCampo('exercicio'                     , 'varchar' , true , '4'   ,  true ,  true);
        $this->AddCampo('cod_lote'                      , 'integer' , true , ''    ,  true ,  true);
        $this->AddCampo('cod_item'                      , 'integer' , true , ''    ,  true ,  true);
        $this->AddCampo('num_item'                      , 'integer' , true , ''    , false , false);
        $this->AddCampo('data_cotacao'                  , 'date'    , true , ''    , false , false);
        $this->AddCampo('vl_cotacao_preco_unitario'     , 'numeric' , true , '14.4', false , false);
        $this->AddCampo('quantidade_cotacao'            , 'numeric' , true , '14.4', false , false);
        $this->AddCampo('preco_unitario'                , 'numeric' , true , '14.4', false , false);
        $this->AddCampo('quantidade_licitada'           , 'numeric' , true , '14.4', false , false);
        $this->AddCampo('quantidade_aderida'            , 'numeric' , true , '14.4', false , false);
        $this->AddCampo('percentual_desconto'           , 'numeric' , true , '6.4' , false , false);
        $this->AddCampo('cgm_fornecedor'                , 'integer' , true , ''    , false ,  true);
        $this->AddCampo('interno'                       , 'boolean' , true , ''    ,  true ,  true);
        $this->AddCampo('ordem_classificacao_fornecedor', 'integer' , true , ''    , false ,  true);
        $this->AddCampo('numcgm_gerenciador'            , 'integer' , true , ''    ,  true ,  true);
    }
    
    public function recuperaListaItem(&$rsRecordSet)
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaItem($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }
    
    public function montaRecuperaListaItem()
    {
        $stSql = "
        SELECT irp.*
             , TO_CHAR(data_cotacao,'dd/mm/yyyy') AS data_cotacao
             , catalogo_item.descricao_resumida as descricao_resumida
             , sw_cgm.nom_cgm AS nomcgm_vencedor
             , sw_cgm.numcgm  AS numcgm_vencedor
             , unidade_medida.nom_unidade AS nom_unidade
             , lote_registro_precos.*
             , COALESCE(percentual_desconto, 0.00) AS percentual_desconto
             
          FROM tcemg.item_registro_precos irp

    INNER JOIN tcemg.lote_registro_precos                  
            ON lote_registro_precos.cod_entidade           = irp.cod_entidade
           AND lote_registro_precos.numero_registro_precos = irp.numero_registro_precos
           AND lote_registro_precos.exercicio              = irp.exercicio
           AND lote_registro_precos.interno                = irp.interno
           AND lote_registro_precos.numcgm_gerenciador     = irp.numcgm_gerenciador
           AND lote_registro_precos.cod_lote               = irp.cod_lote
          
    INNER JOIN almoxarifado.catalogo_item
            ON catalogo_item.cod_item = irp.cod_item

    INNER JOIN administracao.unidade_medida
            ON unidade_medida.cod_unidade  = catalogo_item.cod_unidade
           AND unidade_medida.cod_grandeza = catalogo_item.cod_grandeza

    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = irp.cgm_fornecedor

         WHERE irp.exercicio              = '".$this->getDado('exercicio')."'
           AND irp.numero_registro_precos = ".$this->getDado('numero_registro_precos')."
           AND irp.cod_entidade           = ".$this->getDado('cod_entidade')."
           AND irp.interno                = ".$this->getDado('interno')."
           AND irp.numcgm_gerenciador     = ".$this->getDado('numcgm_gerenciador')."
      ORDER BY irp.num_item";

        return $stSql;
    }
    
    public function __destruct(){}


}

?>