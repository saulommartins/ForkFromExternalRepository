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
    * classe de mapeamento da tabela compras.nota_fiscal_fornecedor
    * Data de Criação: 12/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: TComprasNotaFiscalFornecedor.class.php 34096 2008-10-06 11:53:37Z diogo.zarpelon $
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasNotaFiscalFornecedorOrdem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TComprasNotaFiscalFornecedorOrdem()
    {
        parent::Persistente();
        $this->setTabela("compras.nota_fiscal_fornecedor_ordem");

        $this->setCampoCod('cod_nota');
        $this->setComplementoChave('cgm_fornecedor');

        $this->AddCampo('cgm_fornecedor'		,'integer'	,true  ,''   ,true,  true );
        $this->AddCampo('cod_nota'				,'integer'	,true  ,''   ,true,  false);
        $this->AddCampo('cod_ordem'				,'integer'	,true  ,''   ,false, true );
        $this->AddCampo('cod_entidade'			,'integer'	,true  ,''   ,false, true );
        $this->AddCampo('exercicio','char'		,true  ,'4'  ,false, true );
        $this->AddCampo('tipo'	                ,'char'	 	,true  ,'1'  ,false, true );
    }
}
?>
