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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.fornecedor_socio
  * Data de Criação: 14/10/2014

  * @author Analista: Gelson
  * @author Desenvolvedor: Carlos Adriano

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasFornecedorSocio extends Persistente {
    /**
        * Método Construtor
        * @access Private
    */
    function TComprasFornecedorSocio() {
        parent::Persistente();
        $this->setTabela("compras.fornecedor_socio");
    
        $this->setCampoCod('id');
        $this->setComplementoChave('');

        $this->AddCampo('id'            , 'integer', true, '', true );
        $this->AddCampo('cod_tipo'      , 'integer', true, '', false);
        $this->AddCampo('cgm_fornecedor', 'integer', true, '', false);
        $this->AddCampo('cgm_socio'     , 'integer', true, '', false);
        $this->AddCampo('ativo'         , 'boolean', true, '', false);
    }
}