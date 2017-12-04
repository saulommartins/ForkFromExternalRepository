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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * @author Analista: Carlos Adriano
  * @author Desenvolvedor: Carlos Adriano

*/
class TBeneficioLayoutFornecedor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioLayoutFornecedor()
{
    parent::Persistente();
    $this->setTabela("beneficio.layout_fornecedor");

    $this->setCampoCod('cgm_fornecedor');
    $this->setComplementoChave('');

    $this->AddCampo('cgm_fornecedor' , 'integer', true,  '', true , true);
    $this->AddCampo('cod_layout'     , 'integer', true,  '', false, true);
}

public function recuperaListaCompleta(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaListaCompleta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

public function montaRecuperaListaCompleta()
{
    $stSql = "
                SELECT layout_fornecedor.cgm_fornecedor,
                       sw_cgm.nom_cgm,
                       layout_plano_saude.cod_Layout,
                       layout_plano_saude.padrao
                       
                FROM beneficio.layout_fornecedor
                
                JOIN beneficio.layout_plano_saude
                  ON layout_plano_saude.cod_layout = layout_fornecedor.cod_layout
                  
                JOIN compras.fornecedor
                  ON fornecedor.cgm_fornecedor = layout_fornecedor.cgm_fornecedor
                  
                JOIN sw_cgm
                  ON sw_cgm.numcgm = fornecedor.cgm_fornecedor
    ";
    
    return $stSql;
}

}
?>
