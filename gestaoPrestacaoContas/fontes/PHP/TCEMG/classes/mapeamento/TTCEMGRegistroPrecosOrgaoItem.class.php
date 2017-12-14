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
  * Página de Mapeamento da tabela: tcemg.registro_precos_orgao_item
  * Data de Criação: 27/02/2015

  * @author Analista:      Gelson
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGRegistroPrecosOrgaoItem.class.php 61913 2015-03-13 18:55:57Z franver $
  $Date: 2015-03-13 15:55:57 -0300 (Fri, 13 Mar 2015) $
  $Author: franver $
  $Rev: 61913 $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGRegistroPrecosOrgaoItem extends Persistente {
    
    /**
    * Método Construtor
    * @access Private
    */
    public function TTCEMGRegistroPrecosOrgaoItem()
    {
        parent::Persistente();
        $this->setTabela('tcemg.registro_precos_orgao_item');
        $this->setComplementoChave('cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, numcgm_gerenciador, exercicio_unidade, num_unidade, num_orgao, cod_lote, cod_item, cgm_fornecedor');
        
	    $this->AddCampo('cod_entidade'              , 'integer', true,    '',  true,  true);
        $this->AddCampo('numero_registro_precos'    , 'integer', true,    '',  true,  true);
	    $this->AddCampo('exercicio_registro_precos' , 'varchar', true,   '4',  true,  true);
        $this->AddCampo('numcgm_gerenciador'        , 'integer', true,    '',  true,  true);
        $this->AddCampo('interno'                   , 'boolean', true,    '',  true,  true);
        $this->AddCampo('exercicio_unidade'         , 'varchar', true,   '4',  true,  true);
        $this->AddCampo('num_unidade'               , 'integer', true,    '',  true,  true);
        $this->AddCampo('num_orgao'                 , 'integer', true,    '',  true,  true);
        $this->AddCampo('cod_lote'                  , 'integer', true,    '', false,  true);
        $this->AddCampo('cod_item'                  , 'integer', true,    '', false,  true);
        $this->AddCampo('cgm_fornecedor'            , 'integer', true,    '', false,  true);
        $this->AddCampo('quantidade'                , 'numeric', true,'14,4', false,  true);
    }
    
    public function montaRecuperaPorChave()
    {
        $stSql = "
         SELECT registro_precos_orgao_item.cod_entidade
              , registro_precos_orgao_item.numero_registro_precos
              , registro_precos_orgao_item.exercicio_registro_precos
              , registro_precos_orgao_item.numcgm_gerenciador
              , registro_precos_orgao_item.interno
              , registro_precos_orgao_item.exercicio_unidade
              , registro_precos_orgao_item.num_unidade
              , registro_precos_orgao_item.num_orgao
              , registro_precos_orgao_item.cod_lote
              , registro_precos_orgao_item.cod_item
              , registro_precos_orgao_item.cgm_fornecedor
              , registro_precos_orgao_item.quantidade
              , item_registro_precos.num_item
           FROM tcemg.registro_precos_orgao_item
           JOIN tcemg.item_registro_precos
             ON item_registro_precos.cod_entidade              = registro_precos_orgao_item.cod_entidade
            AND item_registro_precos.numero_registro_precos    = registro_precos_orgao_item.numero_registro_precos
            AND item_registro_precos.exercicio                 = registro_precos_orgao_item.exercicio_registro_precos
            AND item_registro_precos.interno                   = registro_precos_orgao_item.interno
            AND item_registro_precos.numcgm_gerenciador        = registro_precos_orgao_item.numcgm_gerenciador
            AND item_registro_precos.cod_lote                  = registro_precos_orgao_item.cod_lote
            AND item_registro_precos.cod_item                  = registro_precos_orgao_item.cod_item
            AND item_registro_precos.cgm_fornecedor            = registro_precos_orgao_item.cgm_fornecedor
          WHERE registro_precos_orgao_item.cod_entidade              = ".$this->getDado('cod_entidade')."
            AND registro_precos_orgao_item.numero_registro_precos    = ".$this->getDado('numero_registro_precos')."
            AND registro_precos_orgao_item.exercicio_registro_precos = '".$this->getDado('exercicio_registro_precos')."'
            AND registro_precos_orgao_item.interno                   = ".$this->getDado('interno')."
            AND registro_precos_orgao_item.numcgm_gerenciador        = ".$this->getDado('numcgm_gerenciador')." 
        ";
        return $stSql;
    }

    public function __destruct(){}

}

?>